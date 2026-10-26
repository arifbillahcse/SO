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
        'metrics',
        'featured',
        'sort_order',
    ];

    protected $casts = [
        'tags' => 'array',
        'metrics' => 'array',
        'featured' => 'boolean',
    ];
}
