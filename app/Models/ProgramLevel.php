<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'capacity',
        'min_age',
        'max_age',
        'program_id',
        'parent_id',
    ];

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function parent(){
        return $this->belongsTo(ProgramLevel::class, 'parent_id');
    }

    public function sessions(){
        return $this->hasMany(Session::class);
    }

    public function currentActiveSession()
    {
        return $this->sessions()
            ->where('status', Session::ACTIVE)->latest()->first();
    }

    public function getFormattedSessions()
    {
        $formattedSessions = [];
        $activeSession = $this->currentActiveSession();
    
        if (!$activeSession) {
            return $formattedSessions; // No active session found
        }
    
        $startDate = \Carbon\Carbon::parse($activeSession->start_date);
        $endDate = \Carbon\Carbon::parse($activeSession->end_date);
      
        // Iterate over the date range within the start and end dates
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dayName = strtolower($currentDate->format('l'));

            // Check if the session is active on this day
            if ($activeSession->$dayName) {
                $startTime = \Carbon\Carbon::parse($activeSession->$dayName); // Convert to Carbon instance
                $endTime = $startTime->copy()->addMinutes($activeSession->duration_time);

                $formattedSessions[] = [
                    'title' => '',
                    'start' => $currentDate->copy()->setTimeFrom($startTime)->format('Y-m-d\TH:i:s'),
                    'end' => $currentDate->copy()->setTimeFrom($endTime)->format('Y-m-d\TH:i:s'),
                ];
            }

            // Move to the next day
            $currentDate->addDay();
        }
        
    
        return $formattedSessions;
    }

}