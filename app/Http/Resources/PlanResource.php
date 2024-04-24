<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $plans = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'order' => $this->order,
            'cycle' => $this->cycle,
            'price' => $this->price,
            'setup' => $this->setup,
            'trial' => $this->trial,
            'features' => $this->features,
            'setup' => $this->setup,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];

        return $plans;
    }
}
