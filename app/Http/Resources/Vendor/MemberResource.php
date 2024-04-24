<?php

namespace App\Http\Resources\Vendor;

use App\Http\Resources\Member\ClaimedRewardResource;
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
            'activityStatus' => $this->isActive() ? 'Active' : 'Not Active',
            'reservations' => $this->whenLoaded('reservations', function () {
                return ReservationResource::collection($this->reservations);
            }),
            'goHighLevelContactId' => $this->when($this->go_high_level_contact_id !== null, $this->go_high_level_contact_id),
            'lastSeen' => $this->when($this->last_seen !== null, $this->last_seen),
            'achievements' => $this->whenLoaded('achievements', function () {
                return AchievementResource::collection($this->achievements);
            }),
            'reedemPoints' => $this->reedemPoints(),
            'currentLevel' => $this->current_level,
            'rewards' => $this->whenLoaded('rewards', function () {
                return ClaimedRewardResource::collection($this->rewards);
            }),
        ];
        return $member;
    }
}
