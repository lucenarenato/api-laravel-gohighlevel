<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProgressStepResource;
use App\Models\ProgressStep;
use Exception;
use Illuminate\Http\Request;

class StepsController extends BaseController
{
    public function getSteps(Request $request){
        try {
            $plan = $request->input('plan');
            $plan = ($plan) ? $plan : 'scale';
            $progress_steps = ProgressStep::where('plan', $plan)->with('tasks')->orderBy('orders')->get();

            return $this->sendResponse(ProgressStepResource::collection($progress_steps), 'Progress steps fetched successfully');
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getSingleStep($id){
        try {
            $step = ProgressStep::where('id', $id)->with('tasks')->first();
            return $step ? 
                $this->sendResponse(new ProgressStepResource($step), 'Progress step fetched successfully') : 
                $this->sendError('No Step Found', [], 404);
                
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

}