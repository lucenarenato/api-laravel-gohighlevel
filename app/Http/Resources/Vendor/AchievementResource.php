<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $program = $this->program()->withTrashed()->first();

        $achievement = [
            'id' => $this->id,
            'programName' => $program->name,
            'name' => $this->name,
            'badge' => $this->badge,
            'rewardPoints' => $this->reward_points,            
            'action' => $this->actions,
            'image' => $this->image,
            'members' => $this->whenLoaded('members', function () {
                return MemberResource::collection($this->members);
            }),
        ];

        return $achievement;
    }
}
