<?php

namespace App\Models\Concerns;

use App\Models\PageSeo;
use App\Support\Seo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * A content record with its own public detail page and a page_seos row.
 */
trait HasSeo
{
    abstract public function seoRouteName(): string;

    abstract public function seoPageType(): string;

    abstract public function seoDefaultDescription(): ?string;

    /**
     * Unique body text of the detail page, used for the thin-content guard.
     */
    abstract public function seoBodyText(): string;

    public static function bootHasSeo(): void
    {
        static::created(fn ($model) => $model->ensureSeo());

        static::updated(function ($model) {
            if ($model->wasChanged(['title', 'slug'])) {
                $model->seo()->update([
                    'page_label' => $model->title,
                    'page_path' => $model->seoPath(),
                ]);
            }
        });

        static::deleted(fn ($model) => $model->seo()->delete());
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(PageSeo::class, 'seoable');
    }

    public function ensureSeo(): PageSeo
    {
        if ($this->relationLoaded('seo') && $this->seo) {
            return $this->seo;
        }

        $seo = $this->seo()->firstOrCreate([], [
            'page_type' => $this->seoPageType(),
            'page_label' => $this->title,
            'page_path' => $this->seoPath(),
            'schema_type' => $this->seoPageType() === 'article' ? 'Article' : 'CreativeWork',
        ]);
        $this->setRelation('seo', $seo);

        return $seo;
    }

    public function seoPath(): string
    {
        return Seo::routePath($this->seoRouteName(), $this);
    }

    public function seoDefaultTitle(): string
    {
        return $this->title.' | '.config('seo.site_name');
    }

    public function seoDefaultImage(): ?string
    {
        return null;
    }

    public function seoWordCount(): int
    {
        return Seo::wordCount($this->seoBodyText());
    }

    public function seoNoindexReason(): ?string
    {
        $min = config('seo.min_indexable_words');
        $words = $this->seoWordCount();

        if ($words < $min) {
            return "Thin content: {$words} words of unique text (needs {$min}+). Add a full write-up to make this page indexable.";
        }

        return null;
    }
}
