<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\GetPlansRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Exception;
use Illuminate\Http\Request;

class PlansController extends BaseController
{
    public function getPlans(GetPlansRequest $request){
        try {
            $cycle = $request->input('cycle');
            $plans = Plan::where('cycle', $cycle)->orderBy('order')->get();
            
            if($plans->isEmpty()) {
                return $this->sendError("No plans found", [], 400);
            }

            return $this->sendResponse(PlanResource::collection($plans), 'Plans fetched successfully');
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getPlansByName($name){
        try {
            $plans = Plan::where('name', $name)->get();

            if($plans->isEmpty()) {
                return $this->sendError("No plans found", [], 400);
            }
            
            return $this->sendResponse(PlanResource::collection($plans), 'Plans fetched successfully');
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

}