<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchievementActions extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_id',
        'count',
        'achievement_id'
    ];

    public function achievement(){
        return $this->belongsTo(Achievement::class);
    }

    public function action(){
        return $this->belongsTo(Action::class);
    }
    
}
