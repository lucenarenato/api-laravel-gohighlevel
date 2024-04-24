<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class MemberAchievement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'achievement_id',
        'member_id',
        'status',
        'note',
        'date_achieved',
        'date_expire',
        'is_claimed',
        'is_expired'
    ];

    public function achievement(){
        return $this->belongsTo(Achievement::class);
    }

    public function member(){
        return $this->belongsTo(Member::class);
    }

        // Define boot method to handle model events
        protected static function boot()
        {
            parent::boot();
    
            // Listen for 'creating' event to automatically set date_expire
            static::creating(function ($memberAchievement) {
                // Set date_expire to 1 year from date_achieved
                $memberAchievement->date_expire = Carbon::parse($memberAchievement->date_achieved)->addYear();
            });
        }
    

}