<?php

namespace App\Http\Resources\Member;

use App\Http\Resources\Vendor\AchievementResource;
use App\Models\MemberRewardClaim;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementRewardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $reward = Reward::where("achievement_id", $this->achievement->id)->first();
        if($reward) {
            $reward->remainingClaims = $reward->limit_per_member - MemberRewardClaim::where("reward_id", $reward->id)->where("member_id", $this->member_id)->count();
        }
        
        $rewardClaim = [
            'id' => $this->id,
            'isClaimed' => $this->is_claimed,
            'note' => $this->note,
            'dateAchieved' => $this->date_achieved,
            'dateExpired' => $this->date_expire,
            'isExpired' => $this->is_expired,
            'reward' => $reward,
            'achievement' => [
                'id' => $this->achievement->id,
                'name' => $this->achievement->name,
                'badge' => $this->achievement->badge,
                'rewardPoints' => $this->achievement->reward_points,
                'image' => $this->image,
            ],

            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];

        return $rewardClaim;
    }
}
