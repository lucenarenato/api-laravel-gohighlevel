<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory;

    public const NO_OF_CLASSES = "No of classes";
    public const LEVEL_ACHIEVED = "Level achieved";
    public const No_OF_REFERRALS = "No of Referrals";

    protected $fillable = [
        'name'
    ];

    public function achievements(){
        return $this->belongsToMany(Achievement::class, 'achievement_actions', 'action_id', 'achievement_id');
    }    

}
