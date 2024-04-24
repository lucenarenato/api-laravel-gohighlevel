<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
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
            'pointsClaimed' => $this->points_claimed,
            'dateClaimed' => $this->date_claimed,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
            'member' => $this->whenLoaded('member', function () {
                return MemberResource::collection($this->member);
            }),
        ];
        return $reward;
    }
}
