<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'client',
        'tags',
        'description',
        'body',
        'metrics',
        'featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'focus_keywords',
        'og_image',
        'robots_index',
        'robots_follow',
    ];

    protected $casts = [
        'tags' => 'array',
        'metrics' => 'array',
        'featured' => 'boolean',
        'focus_keywords' => 'array',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
