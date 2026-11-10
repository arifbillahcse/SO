<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    use HasSeo;

    protected $fillable = [
        'title',
        'slug',
        'client',
        'tags',
        'description',
        'body',
        'metrics',
        'featured',
        'sort_order',
    ];

    protected $casts = [
        'tags' => 'array',
        'metrics' => 'array',
        'featured' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function seoRouteName(): string
    {
        return 'case-study.show';
    }

    public function seoPageType(): string
    {
        return 'case_study';
    }

    public function seoDefaultTitle(): string
    {
        return $this->title.' — Case Study | '.config('seo.site_name');
    }

    public function seoDefaultDescription(): ?string
    {
        return $this->description;
    }

    public function seoBodyText(): string
    {
        return $this->description.' '.$this->body;
    }
}
