<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class CustomReservationResource extends JsonResource
{
    public function toArray($request)
    {
        $dayName = strtolower(Carbon::now()->format('l'));
        $date = Carbon::now()->toDateString();

        return [
            'programName' => $this->session->programLevel->program->name ?? null,
            'programDescription' => $this->session->programLevel->program->description ?? null,
            'locationName' => $this->session->programLevel->program->location->name ?? null,
            'day' => $dayName,
            'time' => $this->session->{$dayName},
            'date' => $date
        ];
    }

}

