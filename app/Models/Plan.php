<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'order',
        'cycle',
        'price',
        'setup',
        'trial',
        'features',
    ];

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class);
    }

}
