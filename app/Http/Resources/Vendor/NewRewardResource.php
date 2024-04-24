<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewRewardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $reward = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'type' => $this->type,
            'limitPerMember' => $this->limit_per_member,
            'rewardPoints' => $this->reward_points,
            'achievement' => $this->whenLoaded('achievement', function () {
                return new AchievementResource($this->achievement);
            }),
        ];
        return $reward;
    }
}
