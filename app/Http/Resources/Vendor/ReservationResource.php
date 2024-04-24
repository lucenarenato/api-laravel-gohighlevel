<?php

namespace App\Http\Resources\Vendor;

use App\Http\Resources\Vendor\CheckInResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Vendor\SessionResource;
use App\Http\Resources\Vendor\MemberResource;


class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $reservation = [
            'id' => $this->id,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'status' => $this->status,
            'isMarkedAttendence' => $this->getIsMarkedAttendenceTodayAttribute(),
            'session' => $this->whenLoaded('session', function () {
                return new SessionResource($this->session);
            }),
            'member' => $this->whenLoaded('member', function () {
                return new MemberResource($this->member);
            }),
            'checkIns' => $this->whenLoaded('checkIns', function () {
                return CheckInResource::collection($this->checkIns);
            }),
        ];

        return $reservation;
    }
}
