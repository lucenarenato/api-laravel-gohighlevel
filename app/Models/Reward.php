<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    use HasFactory, SoftDeletes;
    
    const ACHIEVEMENT = 1;
    const POINTS = 2;

    protected $fillable = [
        'name',
        'description',
        'image',
        'type',
        'limit_per_member',
        'achievement_id',
        'reward_points',
        'locaiton_id'
    ];

    public function achievement(){
        return $this->belongsTo(Achievement::class);
    }

    public function members(){
        return $this->belongsToMany(Member::class, 'member_reward_claim', 'achievement_id', 'member_id');
    }

}
