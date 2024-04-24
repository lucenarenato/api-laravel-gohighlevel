<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Vendor\VendorController;
use App\Http\Controllers\BaseController;
use App\Http\Requests\DepositRequest;
use App\Http\Resources\Vendor\VendorResource;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProgress;
use App\Services\GHLService;
use App\Services\StripeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends BaseController
{
    protected $stripeService;
    protected $ghlService;
    protected $vendor_controller;

    public function __construct(StripeService $stripeService, GHLService $ghlService, VendorController $vendor_controller){
        $this->stripeService = $stripeService;
        $this->ghlService = $ghlService;
        $this->vendor_controller = $vendor_controller;
    }

    public function deposit(DepositRequest $request){
        try {
            $vendor = $request->input('owner');
            $token = $request->input('token');

            $customer = $this->stripeService->createCustomer($vendor, $token['id']);
            $this->stripeService->setupIntents($customer, $token);

            return $this->sendResponse('Success', 'Stripe customer created successfully.');
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function subscribe(Request $request){
        try {
            $vendorInput = $request->input('owner');
            $coupon = $request->input('coupon');
            $plan = $request->input('plan');
            $token = $request->input('token');

            $setupFee = $plan['setup'] * 100;
            $planName = strtolower($plan['name']);

            if ($coupon && in_array($planName, $coupon['plans']) && $coupon['planCycle'] === $plan['cycle']) {
                $setupFee *= ($coupon['discount'] / 100);
            }

            $setupFee = ($setupFee === 0) ? 100 : $setupFee;
            
            $stripeCustomerId = null;
            $vendor = null;
            $user = User::with('vendor')->where('email', $vendorInput['email'])->first();

            DB::beginTransaction();
            if($user){
                $stripeCustomerId = $user->vendor->stripe_customer_id;
                $vendor = $user->vendor;
            }else{
                $stripeCustomer = $this->stripeService->createCustomer($vendorInput, $token['id']);
                $stripeCustomerId = $stripeCustomer['id'];
                $password = str_replace(' ', '', $vendorInput['firstName']).'@'.Carbon::now()->year.'!';
                
                $user = User::create([
                    'name' => $vendorInput['firstName'] .' '. $vendorInput['lastName'],
                    'email' => $vendorInput['email'],
                    'password' => bcrypt($password),
                    'email_verified_at' => now()
                ]);
                $user->assignRole(\App\Models\User::VENDOR);
                
                $vendor = Vendor::create([
                    'first_name' => $vendorInput['firstName'],
                    'last_name' => isset($vendorInput['lastName'])? $vendorInput['lastName'] : null,
                    'go_high_level_user_id' => null,
                    'user_id' => $user->id,
                    'company_name' => $vendorInput['firstName'].' '.isset($vendorInput['lastName'])? $vendorInput['lastName'] : null,
                    'company_email' => $vendorInput['email'],
                    'plan_id' => $plan['id'],
                    'phone_number' => $vendorInput['phone'],
                    'stripe_customer_id' => $stripeCustomerId
                ]);    
            }

            $paymentMethod = $this->stripeService->getPaymentMethods($stripeCustomerId);
            $this->stripeService->attachPaymentMethod($stripeCustomerId, $paymentMethod->data[0]->id);
            $clientSecret = $this->stripeService->createPaymentIntent($setupFee, $stripeCustomerId, $paymentMethod->data[0]->id);
            $subscriptionStatus = $this->stripeService->createSubscription($plan['name'], $plan['cycle'], $stripeCustomerId);

            if ($clientSecret && $subscriptionStatus) {
                $steps = [];
                for ($i = 1; $i <= 5; $i++) {
                    $steps[] = [
                        'vendor_id' => $vendor->id,
                        'progress_step_id' => $i,
                        'active' => ($i === 1),
                    ];
                }
                
                VendorProgress::insert($steps);

                $tags = ["new lead", "booked appointment", "customer"];
                $contactData = [
                    'locationId' => 'kxsCgZcTUell5zwFkTUc', //Main Location To Manage All Users
                    'email' => $vendor->company_email,
                    'firstName' => $vendor->first_name,
                    'lastName' => $vendor->last_name,
                    'tags' => $tags,
                    'customFields' => [[
                        'key' => 'plan_type',
                        'field_value' => $planName
                    ],[
                        'key' => 'onboarding',
                        'field_value' => "https://join.mymonstro.com/onboarding/{$vendor->id}"
                    ]],
                ];
                $ghlContact = $this->ghlService->createContact($contactData);
                DB::commit();
                return $this->sendResponse($vendor->id, 'Subscription successfull.');
            } else {
                return $this->sendError('Payment declined.', [], 500);
            }
        } catch (Exception $error) {
            DB::rollBack();
            Log::info('===== PaymentController - subscribe() - error =====');
            Log::info($error->getMessage());
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

}
