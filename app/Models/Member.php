<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    public const ACTIVE = 1; 
    public const INACTIVE = 2;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'referral_code',
        'avatar',
        'current_points'
    ];

    public function locations(){
        return $this->belongsToMany(Location::class, 'member_locations', 'member_id', 'location_id');
    }    

    public function achievements(){
        return $this->belongsToMany(Achievement::class, 'member_achievements', 'member_id', 'achievement_id');
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function programs(){
        return $this->belongsToMany(Program::class, 'member_programs', 'member_id', 'program_id');
    }
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool{
        $activeReservationsCount = $this->reservations()
            ->where('status', \App\Models\Reservation::ACTIVE)
            ->count();

        return $activeReservationsCount > 0 ? true : false;
    }

    public function reedemPoints(){
        return $this->rewards()->sum('points_claimed');
    }

    public function rewards(){
        return $this->hasMany(MemberRewardClaim::class);
    }

}
