<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasSeo {
        seoNoindexReason as seoThinContentReason;
    }

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'read_minutes',
        'featured',
        'published_at',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function seoRouteName(): string
    {
        return 'our-resources.show';
    }

    public function seoPageType(): string
    {
        return 'article';
    }

    public function seoDefaultDescription(): ?string
    {
        return $this->excerpt;
    }

    public function seoBodyText(): string
    {
        return (string) $this->body;
    }

    public function seoNoindexReason(): ?string
    {
        if (! $this->published_at || $this->published_at->isFuture()) {
            return 'Not published yet (no publish date, or it is in the future).';
        }

        if (blank($this->body)) {
            return 'No article body yet — only the excerpt is shown. Write the full article to make this page indexable.';
        }

        return $this->seoThinContentReason();
    }
}
