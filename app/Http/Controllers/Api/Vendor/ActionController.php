<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Vendor\ActionResource;
use App\Models\Action;
use Exception;

class ActionController extends BaseController
{
    public function index(){
        try{
            $actions = Action::with(['achievements'])->get();

            if ($actions->isEmpty()) {
                return $this->sendError('No actions found', [], 400);
            }

            return $this->sendResponse(ActionResource::collection($actions), 'Actions fetched successfully');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }
}
