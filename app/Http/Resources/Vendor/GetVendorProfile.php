<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetVendorProfile extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $vendor = [];

        if ($this->resource) {
            $vendor = [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'email' => $this->resource->email,
                'firstName' => optional($this->resource->vendor)->first_name,
                'lastName' => optional($this->resource->vendor)->last_name,
                'phoneNumber' => optional($this->resource->vendor)->phone_number,
                'companyName' => optional($this->resource->vendor)->company_name,
                'companyEmail' => optional($this->resource->vendor)->company_email,
                'companyWebsite' => optional($this->resource->vendor)->company_website,
                'companyAddress' => optional($this->resource->vendor)->company_address,
                'logo' => optional($this->resource->vendor)->logo,
                'planName' => optional(optional($this->resource->vendor)->plan)->name,
                'pin' => optional($this->resource->vendor)->pin,
                'vendorId' => optional($this->resource->vendor)->id,
            ];
        }

        return $vendor;
    }
}
