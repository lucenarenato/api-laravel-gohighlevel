<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocMetasResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $doc_metas = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'published' => $this->published,
            'tags' => $this->tags,
        ];

        return $doc_metas;
    }
}
