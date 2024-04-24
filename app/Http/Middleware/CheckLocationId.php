<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Location;

class CheckLocationId
{
    public function handle($request, Closure $next)
    {
        $requestLocationId = $request->header('Locationid');

        if (!$requestLocationId) {
            $response = [
                'success' => false,
                'message' => 'Location Id not provided in header',
            ];
            return response()->json($response, 400);
        }

        $location = Location::where('go_high_level_location_id', $requestLocationId)->first();

        if (!$location) {
            $response = [
                'success' => false,
                'message' => 'Location Id not found',
            ];
            return response()->json($response, 400);
        }

        // Pass the $locationId variable to the request
        $request->merge(['location' => $location]);
    
        return $next($request);
    }
}
