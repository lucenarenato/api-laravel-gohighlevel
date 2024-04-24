<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportDocMeta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'file',
        'description',
        'published',
        'tags',
        'support_category_id'
    ];

    public function supportCategory(){
        return $this->belongsTo(SupportCategory::class);
    }
    
}
