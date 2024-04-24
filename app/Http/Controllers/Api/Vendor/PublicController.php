<?php

namespace App\Http\Controllers\Api\Vendor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Vendor;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Vendor\MemberController;
use Illuminate\Support\Facades\Log;

class PublicController extends BaseController
{
    
    public function redirectToGHL()
    {
        $url = 'https://marketplace.gohighlevel.com/oauth/chooselocation?response_type=code&redirect_uri='
                .env('GO_HIGH_LEVEL_REDIRECT').
                '&client_id='.env('GO_HIGH_LEVEL_CLIENT_ID').
                '&scope=businesses.readonly businesses.write contacts.readonly contacts.write locations.write locations.readonly '.
                'locations/customValues.write locations/customValues.readonly locations/customFields.readonly locations/customFields.write '.
                'locations/tags.readonly locations/tags.write opportunities.readonly '.
                'opportunities.write oauth.readonly';
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
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Config::set('GO_HIGH_LEVEL_AGENCY_KEY', $data['access_token']);
            return redirect()->route('customer.sources.index')->with(['success' => 'Source added successfully']);

        } else {
            Log::info("==== Error in getting the Go High Level response=====");
            Log::info($response->json());
        }
    }
}
