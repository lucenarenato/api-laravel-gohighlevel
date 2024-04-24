<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Vendor\AchievementResource;
use App\Models\Achievement;
use App\Models\Reward;
use App\Models\AchievementActions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AchievementController extends BaseController
{
    public function index(){
        try{
            $achievements = Achievement::with(['actions', 'members']);

            if(isset(request()->type)) {
                if(request()->type == 0) {
                    $achievements = $achievements->whereNotNull('deleted_at')->withTrashed();
                }
            }

            $achievements = $achievements->paginate(25);

            $data = [
                'achievements' => AchievementResource::collection($achievements),
                'pagination' => [
                    'current_page' => $achievements->currentPage(),
                    'per_page' => $achievements->perPage(),
                    'total' => $achievements->total(),
                    'prev_page_url' => $achievements->previousPageUrl(),
                    'next_page_url' => $achievements->nextPageUrl(),
                    'first_page_url' => $achievements->url(1),
                    'last_page_url' => $achievements->url($achievements->lastPage()),
                ],
            ];

            return $this->sendResponse($data, 'Achievements fetched successfully');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function create(Request $request){
        try{
            DB::beginTransaction();
            $achievement = Achievement::create($request->all());
            AchievementActions::create(['action_id' => $request->action_id, 'count' => $request->action_count, 'achievement_id' => $achievement->id]);
            DB::commit();

            $achievement = Achievement::with(['actions'])->find($achievement->id);

            return $this->sendResponse(new AchievementResource($achievement), 'Achievement created successfully');
        }catch(Exception $error){
            DB::rollBack();
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getAchievement($id){
        try{
            $achievement = Achievement::with(['actions', 'members'])->find($id);

            if(!$achievement){
                return $this->sendError('Achievement not found', [], 400);
            }

            return $this->sendResponse(new AchievementResource($achievement), 'Achievement fetched successfully');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function update(Request $request, Achievement $achievement){
        try{
            $uploadedFileName = null;
            if ($request->hasFile('image')) {
                $img = $request->file('image');
                $imgPath = 'reward-images/';
                $uploadedFileName = app('uploadImage')(0, $img, $imgPath);
            }

            DB::beginTransaction();
            $achievement->update([
                'name' => $request->name,
                'badge' => $request->badge ?? $achievement->badge,
                'reward_points' => $request->rewardPoints ?? $achievement->reward_points,
                'image' => ($uploadedFileName) ? $uploadedFileName : $achievement->image,
                'action_count' => $request->actionCount ?? $achievement->action_count
            ]);

            DB::commit();
            return $this->sendResponse(new AchievementResource($achievement), 'Achievement updated successfully');
        }catch(Exception $error){
            DB::rollBack();
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function delete($id){
        try{
            DB::beginTransaction();
            $achievement = Achievement::find($id);

            if(!$achievement){
                return $this->sendError('Achievement not found', [], 400);
            }

            $achievement->delete();

            Reward::where('achievement_id', $achievement->id)->delete();
            DB::commit();
            return $this->sendResponse('Success', 'Achievement deleted successfully');
        }catch(Exception $error){
            DB::rollBack();
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

}
