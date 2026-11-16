<?php

namespace App\Models;

use Database\Factories\ServiceOfferingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Route;

class ServiceOffering extends Model
{
    /** @use HasFactory<ServiceOfferingFactory> */
    use HasFactory;

    protected $fillable = [
        'service_id', 'catalogue_key', 'slug', 'title', 'description', 'scope', 'icon',
        'action_label', 'action_route', 'action_fragment', 'action_url', 'availability',
        'is_published', 'featured', 'sort_order',
    ];

    protected $casts = [
        'scope' => 'array',
        'is_published' => 'boolean',
        'featured' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function portfolioItems(): BelongsToMany
    {
        return $this->belongsToMany(PortfolioItem::class)
            ->withPivot('sort_order')->orderByPivot('sort_order')->orderBy('portfolio_items.id');
    }

    public function actionUrl(): ?string
    {
        if (filled($this->action_url)) {
            return filter_var($this->action_url, FILTER_VALIDATE_URL)
                && in_array(strtolower((string) parse_url($this->action_url, PHP_URL_SCHEME)), ['http', 'https'], true)
                    ? $this->action_url : null;
        }

        if (! array_key_exists((string) $this->action_route, config('seo.pages')) || ! Route::has($this->action_route)) {
            return null;
        }

        $fragment = filled($this->action_fragment) && preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $this->action_fragment)
            ? '#'.$this->action_fragment : '';

        return route($this->action_route).$fragment;
    }
}
