@extends('layouts.app')

@section('title', $portfolioItem->seoDefaultTitle())
@section('description', \App\Support\Seo::excerpt($portfolioItem->seoDefaultDescription()))
@section('breadcrumbs', json_encode([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Our Works', 'url' => route('our-works')],
    ['name' => $portfolioItem->title, 'url' => route('our-works.show', $portfolioItem)],
]))
@section('page_schema', json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'CreativeWork',
    '@id' => \App\Support\Seo::url($portfolioItem->seoPath()).'#project',
    'name' => $portfolioItem->title,
    'description' => \App\Support\Seo::plain($portfolioItem->seo?->meta_description) ?: $portfolioItem->seoDefaultDescription(),
    'image' => \App\Support\Seo::imageUrl($portfolioItem->seo?->og_image ?: $portfolioItem->thumbnail_path),
    'keywords' => implode(', ', $portfolioItem->tags ?? []) ?: null,
    'dateModified' => $portfolioItem->updated_at?->toAtomString(),
    'creator' => $portfolioItem->project_type === 'technical_demonstration'
        ? ($portfolioItem->attribution ? ['@type' => 'Person', 'name' => $portfolioItem->attribution] : null)
        : ['@id' => \App\Support\SeoHead::organizationId()],
    'mainEntityOfPage' => \App\Support\Seo::url($portfolioItem->seoPath()),
])))

@push('head')
<style>
.wp-tags { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px; justify-content:center; }
.wp-tag { font-size:0.7rem; font-weight:700; color:var(--primary); background:var(--primary-glow); padding:2px 10px; border-radius:100px; letter-spacing:0.05em; }
.article-body { font-size:1rem; color:var(--text-secondary); line-height:1.8; max-width:720px; margin:0 auto; }
.article-body h2 { font-size:1.5rem; font-weight:700; color:var(--text-primary); margin:36px 0 14px; }
.article-body h3 { font-size:1.2rem; font-weight:700; color:var(--text-primary); margin:28px 0 12px; }
.article-body p { margin-bottom:18px; }
.article-body ul, .article-body ol { margin:0 0 18px 20px; }
.article-body ul { list-style:disc; }
.article-body ol { list-style:decimal; }
.article-body li { margin-bottom:10px; padding-left:3px; }
.article-body a { color:var(--primary); text-decoration:underline; }
.portfolio-intro { max-width:700px; margin:20px auto 0; color:var(--text-secondary); font-size:0.95rem; line-height:1.8; }
.portfolio-service { display:flex; justify-content:space-between; align-items:center; gap:24px; max-width:960px; margin:44px auto 0; padding:26px; border:1px solid var(--border); border-radius:var(--radius-lg); background:var(--dark-2); }
.portfolio-service h2 { color:var(--text-primary); font-size:1.1rem; margin-bottom:8px; }
.portfolio-service p { font-size:0.85rem; line-height:1.8; color:var(--text-secondary); }
.portfolio-service a { flex-shrink:0; }
.related-works-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-top:16px; }
.portfolio-figure { max-width:960px; margin:0 auto 48px; }
.portfolio-preview { display:block; width:100%; height:auto; border:1px solid var(--border); border-radius:var(--radius-lg); box-shadow:var(--shadow-card); }
.portfolio-figure figcaption { margin-top:16px; color:var(--text-muted); font-size:0.8rem; line-height:1.6; text-align:center; }
.portfolio-preview--video { max-width:720px; margin:0 auto; background:#09090d; }
.technical-meta { display:grid; grid-template-columns:repeat(3,1fr); max-width:960px; margin:0 auto 24px; gap:20px; padding:24px 28px; background:var(--dark-2); border:1px solid var(--border); border-radius:var(--radius-lg); }
.technical-meta span { display:block; color:var(--text-secondary); font-size:0.73rem; margin-bottom:8px; }
.technical-meta strong { font-size:0.88rem; color:var(--text-primary); }
.technical-evidence { max-width:960px; margin:0 auto 32px; border-left:3px solid var(--primary); padding:18px 24px; background:var(--primary-glow); border-radius:0 var(--radius-md) var(--radius-md) 0; }
.technical-evidence strong { color:var(--text-primary); font-size:0.9rem; }
.technical-evidence p { color:var(--text-secondary); font-size:0.85rem; line-height:1.7; margin-top:8px; }
.portfolio-breadcrumb { flex-wrap:wrap; line-height:1.7; }
.portfolio-breadcrumb .bc-current { overflow-wrap:anywhere; }
.related-project { display:block; background:var(--dark-3); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; color:inherit; }
.related-project:focus-visible { outline:3px solid var(--primary); outline-offset:4px; }
.related-project img { width:100%; aspect-ratio:1.6; object-fit:cover; object-position:top; border-bottom:1px solid var(--border); }
.related-project img.video-thumbnail { object-fit:contain; background:#09090d; }
.related-project-info { padding:22px; }
@media(max-width:768px){ .related-works-grid{grid-template-columns:1fr;} }
@media(max-width:600px){ .technical-meta { grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; padding:22px; } .technical-meta > div:last-child { grid-column:1/-1; } .technical-evidence { padding:18px; } .portfolio-service { flex-direction:column; align-items:flex-start; padding:22px; } }
</style>
@endpush

@section('content')
@php
    $serviceLink = match ($portfolioItem->category) {
        'cloud' => ['route' => 'manage-cloud', 'label' => 'managed cloud services'],
        'wordpress', 'web-development' => ['route' => 'web-development', 'label' => 'website development'],
        'ecommerce' => ['route' => 'ecommerce-solution', 'label' => 'eCommerce solutions'],
        'automation' => ['route' => 'automation', 'label' => 'business automation'],
        'seo' => ['route' => 'seo', 'label' => 'SEO services'],
        default => ['route' => 'services', 'label' => 'our services'],
    };
@endphp
<section class="page-hero" style="padding-bottom:0">
  <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
  <div class="container" style="padding:100px 24px 60px;text-align:center;position:relative" data-aos="fade-up">
    <div class="breadcrumb portfolio-breadcrumb" style="justify-content:center">
      <a href="{{ route('home') }}">Home</a><span>/</span>
      <a href="{{ route('our-works') }}">Our Works</a><span>/</span>
      <span class="bc-current">{{ $portfolioItem->title }}</span>
    </div>
    <div class="wp-tags">
      @foreach($portfolioItem->tags as $tag)
      <span class="wp-tag">{{ $tag }}</span>
      @endforeach
    </div>
    <h1 class="page-hero-title" style="max-width:760px;margin-left:auto;margin-right:auto">{{ $portfolioItem->title }}</h1>
    @if($portfolioItem->project_type === 'technical_demonstration')
    <p class="page-hero-sub" style="margin:0 auto;max-width:540px">Technical portfolio by {{ $portfolioItem->attribution }}</p>
    @elseif($portfolioItem->client)
    <p class="page-hero-sub" style="margin:0 auto;max-width:540px">{{ in_array('Demo', $portfolioItem->tags ?? [], true) ? 'Design demonstration' : $portfolioItem->client }}</p>
    @endif
    @if($portfolioItem->description)
    <p class="portfolio-intro">{{ $portfolioItem->description }}</p>
    @endif
    @if($portfolioItem->publicVideoUrl())
    <a class="btn btn-primary" style="margin-top:24px" href="{{ $portfolioItem->publicVideoUrl() }}" target="_blank" rel="noopener noreferrer" aria-label="Watch demonstration (opens in a new tab)">Watch demonstration <span aria-hidden="true">↗</span></a>
    @endif
    @if($portfolioItem->publicRepositoryUrl())
    <a class="btn btn-outline" style="margin-top:24px" href="{{ $portfolioItem->publicRepositoryUrl() }}" target="_blank" rel="noopener noreferrer" aria-label="View repository (opens in a new tab)">View repository <span aria-hidden="true">↗</span></a>
    @endif
    @if($portfolioItem->publicWebsiteUrl())
    <a class="btn btn-primary" style="margin-top:24px" href="{{ $portfolioItem->publicWebsiteUrl() }}" target="_blank" rel="noopener noreferrer" aria-label="Visit Website (opens in a new tab)">Visit Website <span aria-hidden="true">↗</span></a>
    @endif
  </div>
</section>

<section class="section">
  <div class="container">
    @if($portfolioItem->project_type === 'technical_demonstration')
    <div class="technical-meta">
      <div><span>Category</span><strong>{{ \App\Models\PortfolioItem::CATEGORY_OPTIONS[$portfolioItem->category] ?? ucfirst($portfolioItem->category) }}</strong></div>
      <div><span>Format</span><strong>{{ $portfolioItem->publicEvidenceLabel() }}</strong></div>
      <div><span>Evidence last reviewed</span><strong>{{ $portfolioItem->evidence_checked_at?->format('j M Y') ?? 'Not yet reviewed' }}</strong></div>
    </div>
    <div class="technical-evidence" role="note">
      <strong>{{ $portfolioItem->publicEvidenceLabel() }}</strong>
      <p>{{ match ($portfolioItem->evidence_status) {
          'available_video_title_matches_topic' => 'The linked video title matches this topic. It is presented as a technical demonstration, without a claim of a separate client engagement.',
          'available_video_partially_matches_topic' => 'The supplied video covers part of this topic and is shared with another entry. This page is presented as a technical overview.',
          'linked_video_unavailable' => 'The source video was unavailable at the last review. This topic is presented as a technical overview, without a video demonstration link.',
          'specific_repository_needed' => 'A project-specific code repository is still needed. This topic is presented as a technical overview.',
          'repository_available' => 'A project repository is linked for reference. Review its contents for the scope of the implementation.',
          default => 'The supporting evidence has not yet been reviewed. This topic is presented as a technical overview.',
      } }}</p>
    </div>
    @endif
    @if($portfolioItem->thumbnail_path)
    <figure class="portfolio-figure">
      <img class="portfolio-preview{{ str_starts_with($portfolioItem->thumbnail_path, 'portfolio/cloud-videos/') ? ' portfolio-preview--video' : '' }}" src="{{ $portfolioItem->thumbnailUrl() }}" alt="{{ $portfolioItem->thumbnailAltText() }}" decoding="async" width="{{ str_starts_with($portfolioItem->thumbnail_path, 'portfolio/cloud-videos/') ? 480 : 1280 }}" height="{{ str_starts_with($portfolioItem->thumbnail_path, 'portfolio/cloud-videos/') ? 360 : 800 }}">
      @if(str_starts_with($portfolioItem->thumbnail_path, 'portfolio/cloud-covers/'))
      <figcaption>Illustrative technical cover · This image is not a screenshot of a client system.</figcaption>
      @elseif(str_starts_with($portfolioItem->thumbnail_path, 'portfolio/cloud-videos/'))
      <figcaption>Video thumbnail from the linked demonstration by {{ $portfolioItem->attribution }}.</figcaption>
      @elseif(str_starts_with($portfolioItem->thumbnail_path, 'portfolio/covers/'))
      <figcaption>Portfolio cover · A website screenshot is currently unavailable.</figcaption>
      @elseif(str_starts_with($portfolioItem->thumbnail_path, 'portfolio/screenshots/'))
      <figcaption>Website captured in November 2026 · The live website may have changed since this capture.</figcaption>
      @endif
    </figure>
    @endif
    <article class="article-body" data-aos="fade-up">
      @if($portfolioItem->body)
        {!! $portfolioItem->body !!}
      @elseif($portfolioItem->description)
        <p>{{ $portfolioItem->description }}</p>
      @endif
    </article>
    <div class="portfolio-service">
      <div><h2>Planning a similar project?</h2><p>Explore Softorio's {{ $serviceLink['label'] }} and discuss the scope of your next project with our team.</p></div>
      <a href="{{ route($serviceLink['route']) }}" class="btn btn-outline">Explore {{ $serviceLink['label'] }}</a>
    </div>
  </div>
</section>

@if($relatedItems->isNotEmpty())
<section class="section" style="background:var(--dark-2);padding:70px 0">
  <div class="container">
    <div class="section-header" data-aos="fade-up" style="text-align:center">
      <span class="section-tag">More Work</span>
      <h2 class="section-title">Related <span class="text-gradient">Projects</span></h2>
    </div>
    <div class="related-works-grid">
      @foreach($relatedItems as $related)
      <a href="{{ route('our-works.show', $related) }}" class="related-project" data-aos="fade-up">
        @if($related->thumbnail_path)
        <img class="{{ str_starts_with($related->thumbnail_path, 'portfolio/cloud-videos/') ? 'video-thumbnail' : '' }}" src="{{ $related->thumbnailUrl() }}" alt="{{ $related->thumbnailAltText() }}" loading="lazy" decoding="async" width="640" height="400">
        @endif
        <div class="related-project-info">
        <div style="font-size:0.75rem;color:var(--primary);font-weight:600;margin-bottom:8px">{{ \App\Models\PortfolioItem::CATEGORY_OPTIONS[$related->category] ?? ucfirst($related->category) }}</div>
        <div style="font-size:1rem;font-weight:700;color:var(--text-primary);line-height:1.4">{{ $related->title }}</div>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="section sp-contact" style="padding:70px 0" id="contact-form">
  <div class="sp-contact-bg"><div class="sp-contact-orb"></div></div>
  <div class="container" style="text-align:center;position:relative" data-aos="fade-up">
    <span class="section-tag">Your Project Next</span>
    <h2 class="section-title" style="margin-top:14px">Ready to Start Your <span class="text-gradient">Own Project?</span></h2>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;margin-top:24px">
      <a href="{{ route('home') }}#contact" class="btn btn-primary btn-lg">Start Your Project</a>
      <a href="{{ route('our-works') }}" class="btn btn-outline btn-lg">See More Work</a>
    </div>
  </div>
</section>
@endsection

@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
