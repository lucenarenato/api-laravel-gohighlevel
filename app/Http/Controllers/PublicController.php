<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\TimezoneService;
use App\Models\Location;
use App\Models\Member;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PublicController extends BaseController
{
    protected $ghl_controller;

    public function __construct(GHLController $ghl_controller){
        $this->ghl_controller = $ghl_controller;
    }

    public function redirectToGHL()
    {
        $url = 'https://marketplace.gohighlevel.com/oauth/chooselocation?response_type=code&redirect_uri='
                .env('GO_HIGH_LEVEL_REDIRECT').
                '&client_id='.env('GO_HIGH_LEVEL_CLIENT_ID').
                '&scope=businesses.readonly businesses.write contacts.readonly contacts.write locations.write locations.readonly '.
                'locations/customValues.write locations/customValues.readonly locations/customFields.readonly locations/customFields.write '.
                'locations/tags.readonly locations/tags.write opportunities.readonly '.
                'opportunities.write oauth.readonly users.readonly users.write';
        return redirect()->away($url);
    }

    public function storeGHL(Request $request){
        $sourceId = session('sourceId');

        $code = $request->code;

        $response = Http::asForm()->post('https://services.leadconnectorhq.com/oauth/token', [
            'client_id' => env('GO_HIGH_LEVEL_CLIENT_ID'),
            'client_secret' => env('GO_HIGH_LEVEL_SECRET'),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => env('GO_HIGH_LEVEL_REDIRECT'),
            'user_type' => 'Company'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Setting::updateOrCreate([
                'name' => 'ghl_integration'
            ],
            [
                'name' => 'ghl_integration',
                'value' => $data['access_token'],
                'meta_data' => $data
            ]);
            dd('Successfully Added');
           
        } else {
            Log::info("==== Error in getting the Go High Level response=====");
            Log::info($response->json());
        }
    }

    public function imageUpdate(ImageUploadRequest $request, $userId){
        try{
            $img = $request->file('image');
            $imgPath = 'user-images/';
            $user = User::find($userId);

            if(!$user){
                return $this->sendError('User not exist.', [], 400);
            }

            $uploadedFileName = app('uploadImage')($userId, $img, $imgPath);

            if($user->hasRole(\App\Models\User::VENDOR)) {
                $user->vendor->logo = $uploadedFileName;
                $user->vendor->save();
            }else{
                $user->member->avatar = $uploadedFileName;
                $user->member->save();
            }
            
            return $this->sendResponse('Success', 'Image updated successfully.');
        }catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function memberImageUpdate(ImageUploadRequest $request, $memberId){
        try{
            $img = $request->file('image');
            $imgPath = 'user-images/';
            $member = Member::find($memberId);

            $uploadedFileName = app('uploadImage')($memberId, $img, $imgPath);
            $member->avatar = $uploadedFileName;
            $member->save();
            
            return $this->sendResponse('Success', 'Image updated successfully.');
        }catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function createVendorWebhook(Request $request){
        try{
            $ghl_user_response = $this->ghl_controller->getUserWithTypeAndRole($request->email,'account','admin');
            
            if(count($ghl_user_response['users']) == 0 || !isset($ghl_user_response['users'])) {
                return $this->sendError('Error getting users from ghl. Please email support help@mymonstro.com', [], 400);
            }

            $ghl_user = $ghl_user_response['users'][0];

            if ($ghl_user && isset($ghl_user['email'])) {
                $ghl_location_id = $ghl_user['roles']['locationIds'][0];
                if ($ghl_location_id) {
                    $ghl_location_data = $this->ghl_controller->getLocation($ghl_location_id);
                    $ghl_location_data = $ghl_location_data['location'];

                    // $password = str_replace(' ', '', $ghl_user['firstName']).'@'.Carbon::now()->year.'!';
                    $password = 'Monstro2024Welcome';

                    DB::beginTransaction();

                    $user = User::where('email', $ghl_user['email'])->first();

                    if(!$user) {
                        $user = User::create([
                            'name' => $ghl_user['name'],
                            'email' => $ghl_user['email'],
                            'password' => bcrypt($password),
                            'email_verified_at' => now()
                        ]);
    
                        $user->assignRole(\App\Models\User::VENDOR);
    
                        $data =  [];
                        $data['name'] = $ghl_user['name'];
                        $data['email'] = $ghl_user['email'];
                        $data['password'] = $password;

                        $vendor = Vendor::where('user_id', $user->id)->first();

                        if(!$vendor){
                            $timezone = TimezoneService::findTimezone($ghl_location_data['timezone']);
                            $vendor = Vendor::create([
                                'first_name' => $ghl_location_data['firstName'],
                                'last_name' => isset($ghl_location_data['lastName']) ? $ghl_location_data['lastName'] : null,
                                'company_name' => $ghl_location_data['name'],
                                'company_email' => $ghl_location_data['email'],
                                'company_website' => isset($ghl_location_data['website']) ? $ghl_location_data['website'] : null,
                                'company_address' => isset($ghl_location_data['address']) ? $ghl_location_data['address'] : null,
                                'go_high_level_user_id' => $ghl_user['id'],
                                'user_id' => $user->id,
                                'phone_number' => isset($ghl_location_data['phone']) ? $ghl_location_data['phone'] : null,
                                'stripe_customer_id' => isset($request->stripe_customer_id) ? $request->stripe_customer_id : null
                            ]);

                            Location::updateOrCreate([
                                'go_high_level_location_id' => $ghl_location_id
                            ],
                            [
                                'go_high_level_location_id' => $ghl_location_data['id'],
                                'name' => $ghl_location_data['name'],
                                'address' => isset($ghl_location_data['address']) ? $ghl_location_data['address'] : null,
                                'city' => isset($ghl_location_data['city']) ? $ghl_location_data['city'] : $ghl_location_data['city'],
                                'state' => isset($ghl_location_data['state']) ? $ghl_location_data['state'] : null,
                                'logo_url' => isset($ghl_location_data['logoUrl']) ? $ghl_location_data['logoUrl'] : null,
                                'country' => isset($ghl_location_data['country']) ? $ghl_location_data['country'] : null,
                                'postal_code' => isset($ghl_location_data['postalCode']) ? $ghl_location_data['postalCode'] : null,
                                'website' => isset($ghl_location_data['website']) ? $ghl_location_data['website'] : null,
                                'email' => $ghl_location_data['email'],
                                'phone' => isset($ghl_location_data['phone']) ? $ghl_location_data['phone'] : null,
                                'timezone' => $timezone,
                                'vendor_id' => $vendor->id,
                                'meta_data' => $ghl_location_data
                            ]);
                            
                            DB::commit();
                            // Notification::route('mail', $ghl_user['email'])->notify(new NewVendorNotification($data));
                        }
                    }else{
                        return $this->sendError('A vendor already registered with this email.', [], 400);
                    }
                }

                return $this->sendResponse('Success', 'Vendor created successfully');
            }else{
                return $this->sendError('Error registering vendor. Please email support help@mymonstro.com', [], 400);
            }
        }catch (Exception $error) {
            DB::rollBack();
            Log::info('===== PublicController - createVendorWebhook() - error =====');
            Log::info($error->getMessage());
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

}
