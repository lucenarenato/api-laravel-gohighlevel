<?php

namespace App\Http\Resources\Member;

use App\Http\Resources\Vendor\AchievementResource;
use App\Http\Resources\Vendor\RewardResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $member = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'referralCode' => $this->referral_code,
            'avatar' => $this->avatar,
            'currentPoints' => $this->current_points,
            'achievements' => $this->whenLoaded('achievements', function () {
                return AchievementResource::collection($this->achievements);
            }),
            'reedemPoints' => $this->reedemPoints(),
            'rewards' => $this->whenLoaded('rewards', function () {
                return ClaimedRewardResource::collection($this->rewards);
            }),
        ];
        return $member;
    }
}
