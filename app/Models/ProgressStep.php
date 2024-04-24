<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgressStep extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'orders',
        'next_step',
        'prev_step',
        'plan',
        'description'
    ];

    public function tasks(){
        return $this->hasMany(ProgressTask::class);
    }

}
