<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Session extends Model
{
    use HasFactory, SoftDeletes;

    public const ACTIVE = 1; 
    public const INACTIVE = 2; 
    public const COMPLETED = 3;    

    protected $fillable = [
        'program_level_id',
        'program_id',
        'duration_time',
        'start_date',
        'end_date',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'status'
    ];

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function programLevel(){
        return $this->belongsTo(ProgramLevel::class);
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function getCurrentStatusAttribute($timezone)
    {

        $location = $this->program->location;
        $locationTimezone = $location->timezone ? $location->timezone : 'UTC';
        // Convert start_date and end_date to the user's timezone
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        // Get the current time in the user's timezone
        $currentTime = Carbon::now()->tz($locationTimezone);
        if ($currentTime->lt($startDate)) {
            return "Session Not Started";
        } elseif ($currentTime->gt($endDate)) {
            return "Session Ended";
        } else {
            $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            // Get the current day of the week
            $currentDayOfWeek = strtolower($currentTime->format('l'));
            $nextClassDayIndex = array_search($currentDayOfWeek, $daysOfWeek);
            $daysToAdd = 0;
            for ($i = 0; $i <= 7; $i++) {
                $nextDayIndex = ($nextClassDayIndex + $i) % 7; // ite1 = 2
                $nextDay = $daysOfWeek[$nextDayIndex]; // ite1 = wed
                if($this->{$nextDay}) {
                    $daysToAdd = $i;
                    break;
                }
            }
            
            if($daysToAdd) {
                $nextSessionDate = $currentTime->copy()->addDays($daysToAdd); 
            } else {
                $nextSessionDate = $currentTime->copy();
            }

            $nextSessionDay = strtolower($nextSessionDate->format('l'));

            // Get the start time and end time for the current day
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $nextSessionDate->format('Y-m-d') .' '.$this->{$nextSessionDay}, $locationTimezone);

            // $startTime = Carbon::parse($this->{$today}, $timezone)->setTimezone('UTC');
            $endTime = $startTime->copy()->addMinutes($this->duration_time);

            $startTime = $startTime->copy()->subMinutes($this->duration_time);

            if($currentTime->between($startTime, $endTime)) {
                return 'In Progress';
            }
            // Calculate time until next session
            $timeUntilNextSession = $currentTime->diff($startTime);

            if ($timeUntilNextSession->invert) {
                for ($i = 1; $i <= 7; $i++) {
                    $nextDayIndex = ($nextClassDayIndex + $i) % 7; // ite1 = 2
                    $nextDay = $daysOfWeek[$nextDayIndex]; // ite1 = wed
                    if($this->{$nextDay}) {
                        $daysToAdd = $i;
                        break;
                    }
                }
                $nextSessionDate = $currentTime->copy()->addDays($daysToAdd);
                $nextSessionDay = strtolower($nextSessionDate->format('l'));
                $nextSessionDateTimeReadableFormat = Carbon::createFromFormat('H:i:s', $this->{$nextSessionDay}, $locationTimezone);
                $convertedTime = $nextSessionDateTimeReadableFormat->copy()->setTimezone($timezone)->format('h:i A');
                // Get the start time and end time for the current day
                $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $nextSessionDate->format('Y-m-d') .' '.$this->{$nextSessionDay}, $locationTimezone);
                
                // $startTime = Carbon::parse($this->{$today}, $timezone)->setTimezone('UTC');
                $endTime = $startTime->copy()->addMinutes($this->duration_time);
        
                // Calculate time until next session
                $timeUntilNextSession = $currentTime->diff($startTime);

                $days = $timeUntilNextSession->d;

                $hours = $timeUntilNextSession->h + ($timeUntilNextSession->d * 24);

                if (($hours < 24 && $hours >= 0) && $days == 0) {
                    if($hours == 0) {
                        return "Next session starts in {$timeUntilNextSession->i} minutues.";
                    } else {
                        return "Next session starts in {$hours} hours and {$timeUntilNextSession->i} minutes.";
                    }
                }elseif ($days < 2) {
                    $days = $timeUntilNextSession->days;
                    return "Next session starts tommorrow at {$convertedTime}";
                } elseif ($days >= 2) {
                    $days = $timeUntilNextSession->days;
                    return "Next session starts {$nextSessionDay} at {$convertedTime}";
                } else {
                    return "The next session has already started.";
                }

            } else {
                $nextSessionDateTimeReadableFormat = Carbon::createFromFormat('H:i:s', $this->{$nextSessionDay}, $locationTimezone);
                $convertedTime = $nextSessionDateTimeReadableFormat->copy()->setTimezone($timezone)->format('h:i A');
                $days = $timeUntilNextSession->d;
                $hours = $timeUntilNextSession->h + ($timeUntilNextSession->d * 24);
                if (($hours < 24 && $hours >= 0) && $days == 0) {
                    if($hours == 0) {
                        return "Next session starts in {$timeUntilNextSession->i} minutues.";
                    } else {
                        return "Next session starts in {$hours} hours and {$timeUntilNextSession->i} minutes.";
                    }
                }elseif ($days < 2) {
                    $days = $timeUntilNextSession->days;
                    return "Next session starts tommorrow at {$convertedTime}";
                } elseif ($days >= 2) {
                    $days = $timeUntilNextSession->days;
                    return "Next session starts {$nextSessionDay} at {$convertedTime}";
                } else {
                    return "The next session has already started.";
                }
            }
        }
    }
}