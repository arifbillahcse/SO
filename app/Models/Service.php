<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'route_name',
        'icon',
        'short_description',
        'features',
        'featured',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'featured' => 'boolean',
    ];

    /**
     * Each service is presented on its own static page; that page's SEO row.
     */
    public function pageSeo(): HasOne
    {
        return $this->hasOne(PageSeo::class, 'route_name', 'route_name');
    }
}
