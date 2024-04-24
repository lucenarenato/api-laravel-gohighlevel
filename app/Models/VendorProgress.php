<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'progress_step_id',
        'vendor_id',
        'active',
        'completed',
        'tasks_completed',
    ];

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

    public function progressStep(){
        return $this->belongsTo(ProgressStep::class);
    }

}
