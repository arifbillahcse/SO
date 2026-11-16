<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory;
    use HasSeo {
        seoNoindexReason as seoThinContentReason;
    }

    protected $fillable = [
        'title',
        'slug',
        'category',
        'article_category_id',
        'source_key',
        'excerpt',
        'body',
        'featured_image_path',
        'featured_image_alt',
        'featured_image_caption',
        'read_minutes',
        'featured',
        'published_at',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $with = ['articleCategory'];

    protected static function booted(): void
    {
        static::saving(function (Article $article): void {
            if ($article->article_category_id && ($article->isDirty('article_category_id') || blank($article->getRawOriginal('category')))) {
                $article->category = ArticleCategory::findOrFail($article->article_category_id)->name;
                $article->unsetRelation('articleCategory');
            } elseif (filled($article->category) && ($article->isDirty('category') || ! $article->article_category_id)) {
                $category = ArticleCategory::firstOrCreate(['name' => $article->category]);
                $article->article_category_id = $category->id;
                $article->unsetRelation('articleCategory');
            }
        });
    }

    public function articleCategory(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->articleCategory?->name ?? $this->category ?? 'Uncategorized';
    }

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

    public function featuredImageUrl(): ?string
    {
        return $this->featured_image_path ? Storage::disk('public')->url($this->featured_image_path) : null;
    }

    public function featuredImageAltText(): string
    {
        return filled($this->featured_image_alt) ? $this->featured_image_alt : $this->title;
    }

    public function seoDefaultImage(): ?string
    {
        return $this->featured_image_path;
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
