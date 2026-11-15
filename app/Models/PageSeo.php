<?php

namespace App\Models;

use App\Support\Seo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'route_name',
    'page_type',
    'page_label',
    'page_path',
    'title',
    'meta_description',
    'focus_keyword',
    'secondary_keywords',
    'content_notes',
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
    public const SCORE_GOOD = 80;

    public const SCORE_WARNING = 50;

    protected function casts(): array
    {
        return [
            'secondary_keywords' => 'array',
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'seo_score' => 'integer',
            'seo_checks' => 'array',
            'seo_scanned_at' => 'datetime',
        ];
    }

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The row for a static route, created from config/seo.php on first use so
     * a page's defaults (e.g. noindex for our-founders) never depend on a seeder.
     */
    public static function forRoute(string $routeName): ?self
    {
        $page = config("seo.pages.{$routeName}");

        if (! $page) {
            return static::where('route_name', $routeName)->first();
        }

        return static::firstOrCreate(['route_name' => $routeName], [
            'page_type' => $page['type'],
            'page_label' => $page['label'],
            'page_path' => Seo::routePath($routeName),
            'schema_type' => $page['schema'] ?? 'WebPage',
            'robots_index' => ! ($page['noindex'] ?? false),
            'robots_follow' => ! ($page['noindex'] ?? false),
        ]);
    }

    public static function syncStaticPages(): void
    {
        foreach (array_keys(config('seo.pages')) as $routeName) {
            static::forRoute($routeName)->update([
                'page_label' => config("seo.pages.{$routeName}.label"),
                'page_type' => config("seo.pages.{$routeName}.type"),
                'page_path' => Seo::routePath($routeName),
            ]);
        }
    }

    public function isStatic(): bool
    {
        return $this->route_name !== null;
    }

    public function path(): string
    {
        if ($this->isStatic()) {
            return Seo::routePath($this->route_name);
        }

        return $this->seoable?->seoPath() ?? ($this->page_path ?? '/');
    }

    public function selfUrl(): string
    {
        return Seo::url($this->path());
    }

    public function canonicalUrl(): string
    {
        return $this->canonical_url ?: $this->selfUrl();
    }

    public function canonicalisesElsewhere(): bool
    {
        return filled($this->canonical_url) && rtrim($this->canonical_url, '/') !== rtrim($this->selfUrl(), '/');
    }

    /**
     * Why search engines are told not to index this URL, or null when indexable.
     */
    public function noindexReason(): ?string
    {
        if (! $this->robots_index) {
            return 'Set to noindex in its SEO settings.';
        }

        if ($this->seoable && ($reason = $this->seoable->seoNoindexReason())) {
            return $reason;
        }

        return null;
    }

    public function isIndexable(): bool
    {
        return $this->noindexReason() === null;
    }

    public function belongsInSitemap(): bool
    {
        return $this->isIndexable() && ! $this->canonicalisesElsewhere();
    }

    public function scoreStatus(): ?string
    {
        if ($this->seo_score === null) {
            return null;
        }

        return match (true) {
            $this->seo_score >= self::SCORE_GOOD => 'good',
            $this->seo_score >= self::SCORE_WARNING => 'warning',
            default => 'danger',
        };
    }

    /**
     * @return list<string>
     */
    public function missingFields(): array
    {
        return array_keys(array_filter([
            'SEO title' => blank($this->title),
            'Meta description' => blank($this->meta_description),
            'Focus keyword' => blank($this->focus_keyword),
        ]));
    }

    public function typeLabel(): string
    {
        return config("seo.page_types.{$this->page_type}", ucfirst($this->page_type));
    }
}
