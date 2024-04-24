<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Vendor\MemberResource;
use App\Models\Member;
use App\Models\MemberRewardClaim;
use App\Models\Reward;
use Exception;

class RewardController extends BaseController
{
    public function index(){
        try{
            $members = Member::with(['rewards' => function ($query) {
                if (request()->filled('type') && request()->type == 0) {
                    $query->withTrashed()->whereNotNull('deleted_at');
                }
            }])->whereHas('rewards', function ($query) {
                if (request()->filled('type') && request()->type == 0) {
                    $query->withTrashed()->whereNotNull('deleted_at');
                }
            })->paginate(25);

            if ($members->isEmpty()) {
                return $this->sendError('No member rewards found', [], 400);
            }

            $data = [
                'members' => MemberResource::collection($members),
                'pagination' => [
                    'current_page' => $members->currentPage(),
                    'per_page' => $members->perPage(),
                    'total' => $members->total(),
                    'prev_page_url' => $members->previousPageUrl(),
                    'next_page_url' => $members->nextPageUrl(),
                    'first_page_url' => $members->url(1),
                    'last_page_url' => $members->url($members->lastPage()),
                ],
            ];

            return $this->sendResponse($data, 'Members with rewards fetched successfully');

        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getMemberRewards($memberId){
        try{
            $member = Member::with(['rewards'])->where('id', $memberId)->first();
    
            if (!$member) {
                return $this->sendError('No member with rewards found', [], 400);
            }
    
            return $this->sendResponse(new MemberResource($member), 'Member with rewards fetched successfully');
        } catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }    

    public function delete($id){
        try{
            $reward = MemberRewardClaim::find($id);

            if(!$reward){
                return $this->sendError('Reward does not exist', [], 400);
            }

            $reward->delete();
            return $this->sendResponse('Success', 'Reward deleted successfully');
        } catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function restore($id){
        try{
            MemberRewardClaim::withTrashed()->where('id', $id)->update(['deleted_at' => null]);
            return $this->sendResponse('Success', 'Reward restore successfully');
        } catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

}
