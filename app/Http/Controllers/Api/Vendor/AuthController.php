<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{
    public function vendorAuthenticate(Request $request){
        if($request->user()->email !== $request->email){
            return $this->sendResponse('Unauthorized', 401);
        }

        $vendor = User::where('email', $request->email)->whereHas("roles", function ($q){
            $q->where('name', \App\Models\User::VENDOR);
        })->first();
        
        if (!$vendor || !Hash::check($request->password, $vendor->password)) {
            return $this->sendResponse(false, 401);
        }

        return $this->sendResponse(true, 200);
    }

    public function logout(){
        try{
            Auth::user()->currentAccessToken()->delete();
            return $this->sendResponse(true, 200);
        }catch(Exception $e){
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

}