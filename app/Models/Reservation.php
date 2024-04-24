<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    public const ACTIVE = 1; 
    public const INACTIVE = 2; 
    public const COMPLETED = 3;

    protected $fillable = [
        'session_id',
        'member_id',
        'status',
        'start_date',
        'end_date',
    ];

    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function checkIns(){
        return $this->hasMany(CheckIn::class);
    }

    public function session(){
        return $this->belongsTo(Session::class);
    }

    public function getIsMarkedAttendenceTodayAttribute()
    {
        $now = \Carbon\Carbon::now();
        return $this->checkIns()->whereDate('check_in_time', $now)->count() ? true : false;
    }
}