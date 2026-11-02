<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'route_name',
    'title',
    'meta_description',
    'focus_keywords',
    'canonical_url',
    'robots_index',
    'robots_follow',
    'robots_advanced',
    'schema_type',
    'og_title',
    'og_description',
    'og_image',
    'twitter_card_type',
    'twitter_title',
    'twitter_description',
    'twitter_image',
])]
class PageSeo extends Model
{
    protected function casts(): array
    {
        return [
            'focus_keywords' => 'array',
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
        ];
    }
}
