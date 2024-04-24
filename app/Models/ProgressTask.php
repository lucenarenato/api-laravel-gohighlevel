<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgressTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'progress_step_id',
        'name',
        'next_task',
        'prev_task',
        'orders',
        'content',
        'video_id',
        'video_platform',
        'cta_btn',
        'cta_url'
    ];

    public function step(){
        return $this->belongsTo(ProgressStep::class);
    }
    
}
