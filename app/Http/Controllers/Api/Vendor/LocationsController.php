<?php

namespace App\Http\Controllers\Api\Vendor;

use Illuminate\Http\Request;
use App\Services\TimezoneService;
use App\Models\Vendor;
use App\Models\Location;
use App\Models\User;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Vendor\LocationResource;
use Exception;

class LocationsController extends BaseController
{
    public function vendorLocations($vendor_id){
        $locations = Location::where('vendor_id', $vendor_id)->paginate(25);
        $data = [
            'locations' => LocationResource::collection($locations),
            'pagination' => [
                'current_page' => $locations->currentPage(),
                'per_page' => $locations->perPage(),
                'total' => $locations->total(),
                'prev_page_url' => $locations->previousPageUrl(),
                'next_page_url' => $locations->nextPageUrl(),
                'first_page_url' => $locations->url(1),
                'last_page_url' => $locations->url($locations->lastPage()),
            ],
        ];

        return $this->sendResponse($data, 'Vendor locations.');
    }

    public function checkLocationStatus(){
        try{
            $location = request()->location;
            $location = Location::find($location->id);

            if(!$location){
                return $this->sendError("Location doesnot exist", [], 400);
            }

            return $this->sendResponse(new LocationResource($location), 'Location fetched successfully');

        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getLocatonById($locationId) {
        $location = request()->location;
        $vendor = $location->vendor;
        $location = Location::where('go_high_level_location_id', $locationId)->where('vendor_id', $vendor->id)->latest()->first();
        return $this->sendResponse(new LocationResource($location), 200);
    }

    public function updateLocation($locationId, Request $request) {
        $location = request()->location;
        $timezone = TimezoneService::findTimezone($request->timezone);
        Location::where('id', $location->id)->update([
            'timezone' => $timezone
        ]);
        $location->timezone = $timezone;
        return $this->sendResponse(new LocationResource($location), 200);
    }
}