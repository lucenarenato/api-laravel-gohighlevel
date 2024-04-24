<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressStepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $steps = [
            'id' => $this->id,
            'name' => $this->name,
            'order' => $this->order,
            'nextStep' => $this->next_step,
            'prevStep' => $this->prev_step,
            'plan' => $this->plan,
            'description' => $this->description,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'tasks' => ProgressTaksResource::collection($this->tasks)
        ];

        return $steps;
    }
}
