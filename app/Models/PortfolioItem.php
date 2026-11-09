<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioItem extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'client',
        'category',
        'tags',
        'thumbnail_path',
        'description',
        'body',
        'sort_order',
        'meta_title',
        'meta_description',
        'focus_keywords',
        'robots_index',
        'robots_follow',
    ];

    protected $casts = [
        'tags' => 'array',
        'focus_keywords' => 'array',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
