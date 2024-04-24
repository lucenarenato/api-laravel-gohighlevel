<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckIn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'time_to_check_in',
        'check_in_time',
        'check_out_time'
    ];

    public function reservation(){
        return $this->belongsTo(Reservation::class);
    }

}