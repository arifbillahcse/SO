<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PortfolioItem extends Model
{
    use HasSeo;

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
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    protected static function booted(): void
    {
        // Every item needs a slug: the listing links to its detail page.
        static::saving(function (PortfolioItem $item) {
            if (blank($item->slug)) {
                $base = Str::slug($item->title) ?: 'project';
                $slug = $base;
                for ($i = 2; static::where('slug', $slug)->whereKeyNot($item->getKey())->exists(); $i++) {
                    $slug = "{$base}-{$i}";
                }
                $item->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function seoRouteName(): string
    {
        return 'our-works.show';
    }

    public function seoPageType(): string
    {
        return 'portfolio';
    }

    public function seoDefaultTitle(): string
    {
        return $this->title.' — Project | '.config('seo.site_name');
    }

    public function seoDefaultDescription(): ?string
    {
        return $this->description ?: "{$this->title} — a project delivered by Softorio for {$this->client}.";
    }

    public function seoDefaultImage(): ?string
    {
        return $this->thumbnail_path;
    }

    public function seoBodyText(): string
    {
        return $this->description.' '.$this->body;
    }
}
