<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'read_minutes',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
