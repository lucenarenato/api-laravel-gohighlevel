<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $vendor = [
            'id' => $this->id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'goHighLevelUserId' => $this->go_high_level_user_id,
            'planId' => $this->plan_id,
            'userId' => $this->user_id,
            'companyName' => $this->company_name,
            'companyEmail' => $this->company_email,
            'companyWebsite' => $this->company_website,
            'companyAddress' => $this->company_address,
            'logo' => $this->logo,
            'phoneNumber' => $this->phone_number
        ];
        return $vendor;
    }
}
