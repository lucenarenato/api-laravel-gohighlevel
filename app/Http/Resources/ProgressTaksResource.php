<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressTaksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tasks = [
            'id' => $this->id,
            'progressStepId' => $this->progress_step_id,
            'name' => $this->name,
            'nextStep' => $this->next_step,
            'prevStep' => $this->prev_step,
            'orders' => $this->orders,
            'content' => $this->content,
            'videoId' => $this->video_id,
            'videoPlatform' => $this->video_platform,
            'ctaBtn' => $this->cta_btn,
            'ctaUrl' => $this->cta_url,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];

        return $tasks;
    }
}
