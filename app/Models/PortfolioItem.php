<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioItem extends Model
{
    use HasSeo;

    public const CATEGORY_OPTIONS = [
        'wordpress' => 'WordPress',
        'ecommerce' => 'eCommerce',
        'cloud' => 'Cloud',
        'automation' => 'Automation',
        'seo' => 'SEO',
        'web-development' => 'Web Development',
    ];

    public const PROJECT_TYPE_OPTIONS = [
        'website_project' => 'Website / design project',
        'technical_demonstration' => 'Technical demonstration',
    ];

    public const EVIDENCE_STATUS_OPTIONS = [
        'not_reviewed' => 'Not reviewed',
        'available_video_title_matches_topic' => 'Video available; title matches topic',
        'available_video_partially_matches_topic' => 'Video available; partial topic match',
        'linked_video_unavailable' => 'Linked video unavailable',
        'specific_repository_needed' => 'Project repository needed',
        'repository_available' => 'Project repository reviewed',
    ];

    protected $fillable = [
        'title',
        'slug',
        'client',
        'category',
        'tags',
        'thumbnail_path',
        'website_url',
        'project_type',
        'attribution',
        'video_url',
        'repository_url',
        'evidence_status',
        'evidence_notes',
        'evidence_checked_at',
        'source_key',
        'description',
        'body',
        'sort_order',
    ];

    protected $casts = [
        'tags' => 'array',
        'evidence_checked_at' => 'datetime',
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

    public function publicWebsiteUrl(): ?string
    {
        return $this->publicHttpUrl($this->website_url);
    }

    public function publicVideoUrl(): ?string
    {
        return $this->evidence_status === 'available_video_title_matches_topic'
            ? $this->publicHttpUrl($this->video_url)
            : null;
    }

    public function publicRepositoryUrl(): ?string
    {
        return $this->evidence_status === 'repository_available'
            ? $this->publicHttpUrl($this->repository_url)
            : null;
    }

    public function publicEvidenceLabel(): string
    {
        if ($this->publicVideoUrl()) {
            return 'Video demonstration';
        }

        return $this->publicRepositoryUrl() ? 'Code reference' : 'Technical overview';
    }

    private function publicHttpUrl(?string $url): ?string
    {

        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return in_array(strtolower(parse_url($url, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true)
            ? $url
            : null;
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail_path ? Storage::disk('public')->url($this->thumbnail_path) : null;
    }

    public function thumbnailAltText(): string
    {
        $path = $this->thumbnail_path ?? '';

        return match (true) {
            str_starts_with($path, 'portfolio/cloud-videos/') => 'Video thumbnail: '.$this->title,
            str_starts_with($path, 'portfolio/cloud-covers/') => 'Illustrated technical cover: '.$this->title,
            str_starts_with($path, 'portfolio/screenshots/') => $this->title.' website screenshot',
            str_starts_with($path, 'portfolio/covers/') => 'Portfolio archive cover: '.$this->title,
            default => $this->title.' portfolio image',
        };
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
        return $this->title.' | '.config('seo.site_name');
    }

    public function seoDefaultDescription(): ?string
    {
        if ($this->description) {
            if ($this->project_type !== 'technical_demonstration' && in_array('Demo', $this->tags ?? [], true)) {
                return $this->title.'. '.$this->description;
            }

            return $this->description;
        }

        if ($this->project_type === 'technical_demonstration') {
            $attribution = $this->attribution ? " by {$this->attribution}" : '';

            return "{$this->title} — a technical demonstration{$attribution}.";
        }

        return $this->client
            ? "{$this->title} — a project delivered by Softorio for {$this->client}."
            : "{$this->title} — a Softorio portfolio project.";
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
