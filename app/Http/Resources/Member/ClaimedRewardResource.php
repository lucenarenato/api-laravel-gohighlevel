<?php

namespace App\Http\Resources\Member;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClaimedRewardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $rewardClaim = [
            'members' => $this->whenLoaded('members', function () {
                return MemberResource::collection($this->members);
            }),
            'pointsClaimed' => $this->points_claimed,
            'currentPoints' => $this->current_points,
            'dateClaimed' => $this->date_claimed,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];

        return $rewardClaim;
    }
}
