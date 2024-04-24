<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $action = [
            'id' => $this->id,
            'name' => $this->name,
            'achievements' => $this->whenLoaded('achievements', function () {
                return AchievementResource::collection($this->achievements);
            }),
        ];

        return $action;
    }
}
