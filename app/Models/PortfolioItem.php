<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioItem extends Model
{
    protected $fillable = [
        'title',
        'client',
        'category',
        'tags',
        'thumbnail_path',
        'sort_order',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
}
