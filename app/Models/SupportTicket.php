<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'issue',
        'video',
        'account_id',
        'description',
        'status',
        'location_id',
    ];

    public function location(){
        return $this->belongsTo(Location::class);
    }
    
}
