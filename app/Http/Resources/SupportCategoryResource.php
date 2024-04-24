<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $support_category = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'docMetas' => DocMetasResource::collection($this->docMetas)
        ];

        return $support_category;
    }
}
