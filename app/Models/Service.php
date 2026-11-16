<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Route;

class Service extends Model
{
    use HasFactory;

    public const ICON_OPTIONS = ['code' => 'Code', 'search' => 'Search', 'cloud' => 'Cloud', 'gear' => 'Automation', 'medical' => 'Medical'];

    public function publicUrl(): ?string
    {
        return array_key_exists($this->route_name, config('seo.pages')) && Route::has($this->route_name)
            ? route($this->route_name) : null;
    }

    protected $fillable = [
        'title',
        'slug',
        'route_name',
        'icon',
        'short_description',
        'features',
        'featured',
        'sort_order',
        'catalogue_key',
        'is_published',
    ];

    protected $casts = [
        'features' => 'array',
        'featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * Each service is presented on its own static page; that page's SEO row.
     */
    public function pageSeo(): HasOne
    {
        return $this->hasOne(PageSeo::class, 'route_name', 'route_name');
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(ServiceOffering::class)->orderBy('sort_order')->orderBy('id');
    }
}
