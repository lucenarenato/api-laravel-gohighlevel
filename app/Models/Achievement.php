<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'name',
        'badge',
        'reward_points',
        'image'
    ];

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function actions(){
        return $this->belongsToMany(Action::class, 'achievement_actions', 'achievement_id', 'action_id')->withPivot('count');
    }    

    public function members(){
        return $this->belongsToMany(Member::class, 'member_achievements', 'achievement_id', 'member_id');
    }

    public function reward(){
        return $this->belongsTo(Reward::class);
    }

}