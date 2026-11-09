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
        'featured',
        'published_at',
        'meta_title',
        'meta_description',
        'focus_keywords',
        'og_image',
        'robots_index',
        'robots_follow',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'focus_keywords' => 'array',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
