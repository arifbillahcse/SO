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
@include('partials.portfolio-cover-styles')
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

.project-detail-hero>.container{max-width:1008px;text-align:left!important}
.project-detail-hero .breadcrumb{justify-content:flex-start!important}
.project-detail-hero .wp-tags{display:none}
.project-category{font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;color:var(--primary);font-weight:700;margin:30px 0 18px}
.project-detail-hero .page-hero-title{max-width:900px!important;margin-left:0!important;letter-spacing:-.045em;font-size:clamp(2.1rem,4.4vw,4rem);line-height:1.12}
.project-detail-hero .page-hero-sub{margin:18px 0 0!important;font-size:.85rem}
.project-detail-hero .portfolio-intro{margin:24px 0 0;max-width:800px;font-size:1.08rem;line-height:1.85}
.project-story-grid{display:grid;grid-template-columns:minmax(0,1fr) 260px;gap:52px;max-width:960px;margin:auto}
.project-write-up{max-width:none;width:100%;min-width:0}
.project-write-up>:first-child,.project-write-up>div>:first-child{margin-top:0}
.project-write-up img{max-width:100%;height:auto;border-radius:12px}
.project-write-up a{overflow-wrap:anywhere}
.project-facts{align-self:start;background:var(--dark-2);border:1px solid var(--border);border-radius:16px;padding:24px;min-width:0}
.project-facts h2{font-size:.95rem;margin-bottom:22px;color:var(--text-primary)}
.project-facts dl>div+div{border-top:1px solid var(--border);padding-top:16px;margin-top:16px}
.project-facts dt{font-size:.7rem;color:var(--text-muted);margin-bottom:8px}
.project-facts dd{font-size:.85rem;color:var(--text-primary);line-height:1.7;overflow-wrap:anywhere}
.project-back-link{display:inline-flex;gap:8px;margin-top:24px;font-size:.74rem;color:var(--primary);font-weight:600}
.project-technologies{border-top:1px solid var(--border);margin-top:28px;padding-top:6px}
.project-technologies ul{display:flex;gap:8px;flex-wrap:wrap;margin:0;list-style:none}
.project-technologies li{padding:6px 12px;font-size:.76rem;background:var(--primary-glow);color:var(--primary);border-radius:6px;margin:0}
.project-visual-section{max-width:960px;margin:48px auto 0}
.project-visual-section>h2{font-size:1.35rem;letter-spacing:-.03em;margin-bottom:22px}
.related-project-brief{font-size:.82rem;color:var(--text-secondary);line-height:1.8;margin-top:12px;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden}
.related-project-link{display:block;margin-top:20px;color:var(--primary);font-size:.76rem;font-weight:600}
@media(max-width:768px){.project-story-grid{grid-template-columns:1fr;gap:32px}.project-facts{width:100%}.project-detail-hero .portfolio-intro{font-size:.96rem}.project-detail-hero>.container{padding-bottom:40px!important}}
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
<section class="page-hero project-detail-hero" style="padding-bottom:0">
  <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
  <div class="container" style="padding:100px 24px 60px;text-align:center;position:relative" data-aos="fade-up">
    <div class="breadcrumb portfolio-breadcrumb" style="justify-content:center">
      <a href="{{ route('home') }}">Home</a><span>/</span>
      <a href="{{ route('our-works') }}">Our Works</a><span>/</span>
      <span class="bc-current">{{ $portfolioItem->title }}</span>
    </div>
    <p class="project-category">{{ $portfolioItem->categoryLabel() }}</p>
    <h1 class="page-hero-title" style="max-width:760px;margin-left:auto;margin-right:auto">{{ $portfolioItem->title }}</h1>
    @if($portfolioItem->project_type === 'technical_demonstration')
    <p class="page-hero-sub" style="margin:0 auto;max-width:540px">Technical portfolio by {{ $portfolioItem->attribution }}</p>
    @elseif($portfolioItem->client)
    <p class="page-hero-sub" style="margin:0 auto;max-width:540px">{{ in_array('Demo', $portfolioItem->tags ?? [], true) ? 'Design demonstration' : $portfolioItem->client }}</p>
    @endif
    <p class="portfolio-intro">{{ $portfolioItem->publicBrief() }}</p>
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
    <div class="project-story-grid">
      <article class="article-body project-write-up">
        @if(\App\Support\Seo::wordCount($portfolioItem->body))
          {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($portfolioItem->body)->fileAttachmentsDisk('public')->fileAttachmentsVisibility('public') }}
        @else
          <h2>Overview</h2>
          <p>{{ $portfolioItem->publicBrief() }}</p>
        @endif
        @if(count($portfolioItem->technologyTags()))
        <section class="project-technologies" aria-labelledby="project-technologies-title">
          <h2 id="project-technologies-title">Technologies &amp; focus</h2>
          <ul>@foreach($portfolioItem->technologyTags() as $tag)<li>{{ $tag }}</li>@endforeach</ul>
        </section>
        @endif
      </article>
      <aside class="project-facts" aria-label="Project information">
        <h2>Project information</h2>
        <dl>
          <div><dt>Service</dt><dd>{{ $portfolioItem->categoryLabel() }}</dd></div>
          <div><dt>Format</dt><dd>{{ $portfolioItem->project_type === 'technical_demonstration' ? $portfolioItem->publicEvidenceLabel() : (in_array('Demo', $portfolioItem->tags ?? [], true) ? 'Design demonstration' : 'Website project') }}</dd></div>
          @if($portfolioItem->project_type === 'technical_demonstration' && $portfolioItem->attribution)
          <div><dt>Contributor</dt><dd>{{ $portfolioItem->attribution }}</dd></div>
          @elseif($portfolioItem->client && !in_array('Demo', $portfolioItem->tags ?? [], true))
          <div><dt>Client</dt><dd>{{ $portfolioItem->client }}</dd></div>
          @endif
        </dl>
        <a href="{{ route('our-works') }}" class="project-back-link">Browse all projects <span aria-hidden="true">&nearr;</span></a>
      </aside>
    </div>
    <section class="project-visual-section" aria-labelledby="project-visual-title">
    <h2 id="project-visual-title">Project preview</h2>
    <figure class="portfolio-figure">
      <x-portfolio-cover :project="$portfolioItem" variant="detail" />
      @if(str_starts_with($portfolioItem->thumbnail_path ?? '', 'portfolio/cloud-covers/'))
      <figcaption>Illustrative technical cover · This image is not a screenshot of a client system.</figcaption>
      @elseif(str_starts_with($portfolioItem->thumbnail_path ?? '', 'portfolio/cloud-videos/'))
      <figcaption>Video thumbnail from the linked demonstration by {{ $portfolioItem->attribution }}.</figcaption>
      @elseif(str_starts_with($portfolioItem->thumbnail_path ?? '', 'portfolio/covers/'))
      <figcaption>Portfolio cover · A website screenshot is currently unavailable.</figcaption>
      @elseif(str_starts_with($portfolioItem->thumbnail_path ?? '', 'portfolio/screenshots/'))
      <figcaption>Website captured in November 2026 · The live website may have changed since this capture.</figcaption>
      @endif
    </figure>
    </section>
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
        <x-portfolio-cover :project="$related" />
        <div class="related-project-info">
        <div style="font-size:0.75rem;color:var(--primary);font-weight:600;margin-bottom:8px">{{ \App\Models\PortfolioItem::CATEGORY_OPTIONS[$related->category] ?? ucfirst($related->category) }}</div>
        <div style="font-size:1rem;font-weight:700;color:var(--text-primary);line-height:1.4">{{ $related->title }}</div>
        <p class="related-project-brief">{{ $related->publicBrief() }}</p>
        <span class="related-project-link">View project &nearr;</span>
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
      <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Start Your Project</a>
      <a href="{{ route('our-works') }}" class="btn btn-outline btn-lg">See More Work</a>
    </div>
  </div>
</section>
@endsection

@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
