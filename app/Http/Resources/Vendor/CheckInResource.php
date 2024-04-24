<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckInResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $check_ins = [
            'id' => $this->id,
            'reservationId' => $this->reservation_id,
            'timeToCheckIn' => $this->time_to_check_in,
            'checkInTime' => $this->check_in_time,
            'checkOutTime' => $this->check_out_time,
            'reservation' => $this->whenLoaded('reservation', function () {
                return new ReservationResource($this->reservation);
            }),
        ];

        return $check_ins;
    }
}
