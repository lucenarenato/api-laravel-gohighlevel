<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GHLController;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\Vendor\VendorProfileUpdate;
use App\Http\Resources\Vendor\GetVendorProfile;
use App\Models\Location;
use App\Models\User;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VendorController extends BaseController
{
    protected $ghl_controller;

    public function __construct(GHLController $ghl_controller)
    {
        $this->ghl_controller = $ghl_controller;
    }

    public function updatePassword(Request $request, $id)
    {
        try {
            $vendor = Vendor::find($id);
            $user = $vendor->user;
            if (!$user) {
                return response()->json(['message' => 'Vendor not found'], 404);
            }

            $new_password = $request->input('password');
            $hashed_password = Hash::make($new_password);

            $ghl_user_response = $this->ghl_controller->getUserWithTypeAndRole($user->email,'account','admin');
            
            if(count($ghl_user_response['users']) == 0 || !isset($ghl_user_response['users'])) {
                return $this->sendError('Error getting users from ghl. Please email support help@mymonstro.com', [], 400);
            }

            $ghl_user = $ghl_user_response['users'][0];

            if ($ghl_user && isset($ghl_user['email'])) {
                $ghl_location_id = $ghl_user['roles']['locationIds'][0];
                if ($ghl_location_id) {
                    $ghl_location_data = $this->ghl_controller->getLocation($ghl_location_id);
                    $ghl_location_data = $ghl_location_data['location'];
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
                        'vendor_id' => $vendor->id,
                        'meta_data' => $ghl_location_data
                    ]);
                }

                $vendor->company_name = $ghl_location_data['name'];
                $vendor->company_email = $ghl_location_data['email'];
                $vendor->company_website = isset($ghl_location_data['website']) ? $ghl_location_data['website'] : null;
                $vendor->company_address = isset($ghl_location_data['address']) ? $ghl_location_data['address'] : null;
                $vendor->go_high_level_user_id = $ghl_user['id'];
                $vendor->save();

                $this->ghl_controller->updateUser($ghl_user['id'], [
                    'email' => $vendor->email,
                    'password' => $new_password
                ]);
                
                $updateContact = [
                    'locationId' => 'kxsCgZcTUell5zwFkTUc', //Main Location To Manage All Users
                    'email' => $user->email,
                    'customFields' => [
                        [
                            'key' => 'password',
                            'field_value' => $new_password
                        ],[
                            'key' => 'go_high_level_user_id',
                            'field_value' => $vendor->go_high_level_user_id
                        ],[
                            'key' => 'paymentgateway_customer_id',
                            'field_value' => $vendor->stripe_customer_id
                        ]
                    ],
                ];
                $this->ghl_controller->upsertContact($updateContact);
                return $this->sendResponse('Success', 'Password set successfully');
            }else{
                return $this->sendError('Error setting contact up your password. Please email support help@mymonstro.com', [], 400);
            }
        } catch (\Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function vendorUpdatePassword(PasswordUpdateRequest $request){
        try {
            $location = request()->location;
            $location = Location::find($location->id);
            $user = User::find($location->vendor->user->id);
            if(!$user){
                return $this->sendError("No user found for this location", [], 400);
            }
            if (!Hash::check($request->currentPassword, $user->password)) {
                return $this->sendError('The current password is incorrect.', [], 400);
            }
            $user->password = bcrypt($request->password);
            $user->save();
            return $this->sendResponse('Success', 'Password updated successfully');
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function passwordReset(Request $request){
        try{
            $location = request()->location;
            $location = Location::find($location->id);
            $user = User::find($location->vendor->user->id);

            if(!$location->is_new){
                return $this->sendError("Password already set", [], 400);
            }
            $user->password = bcrypt($request->password);
            $user->save();
            $location->is_new = false;
            $location->save();
            return $this->sendResponse('Success', 'Password set successfully');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }


    public function getProfile(){
        try {
            $user = User::find(request()->user()->id);

            if (!$user) {
                return $this->sendError('User not found.', [], 404);
            }

            return $this->sendResponse(new GetVendorProfile($user), 200);
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function updateProfile(VendorProfileUpdate $request){
        try {
            $user = User::find(request()->user()->id);

            if (!$user) {
                return $this->sendError('User not found.', [], 404);
            }

            $user->vendor->first_name = $request->firstName;
            $user->vendor->last_name = $request->lastName;
            $user->vendor->phone_number = $request->phoneNumber;
            $user->vendor->company_name = $request->companyName;
            // $user->vendor->company_email = $request->companyEmail;
            $user->vendor->company_website = $request->companyWebsite;
            $user->vendor->company_address = $request->companyAddress;

            $user->vendor->save();
            return $this->sendResponse(new GetVendorProfile($user), 200);
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }
}