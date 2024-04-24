<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $support_ticket = [
            'id' => $this->id,
            'subject' => $this->subject,
            'issue' => $this->issue,
            'video' => $this->video,
            'accountId' => $this->account_id,
            'description' => $this->description,
            'status' => $this->status,
            'locationId' => $this->location_id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];

        return $support_ticket;
    }
}
