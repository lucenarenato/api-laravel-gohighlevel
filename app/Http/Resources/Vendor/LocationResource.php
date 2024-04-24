<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locations = [
            'id' => $this->id,
            'goHighLevelUserId' => $this->go_high_level_user_id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'logoUrl' => $this->logo_url,
            'country' => $this->country,
            'postalCode' => $this->postal_code,
            'website' => $this->website,
            'email' => $this->email,
            'phone' => $this->phone,
            'timezone' => $this->timezone,
            'metaData' => $this->meta_data,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'isNew' => ($this->is_new == 1)? true: false, 
        ];

        return $locations;
    }
}
