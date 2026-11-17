@extends('layouts.app')

@section('title', 'Website & Cloud Portfolio | Softorio')
@section('description', 'Explore Softorio website projects and Arif Billah’s cloud and Linux technical portfolio, with project details, technologies and video demonstrations.')
@section('page_schema', json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    '@id' => \App\Support\Seo::url('/our-works').'#collection',
    'url' => \App\Support\Seo::url('/our-works'),
    'name' => 'Website & Cloud Portfolio',
    'description' => 'Website projects, design demonstrations, and cloud and Linux technical topics from the Softorio portfolio.',
    'mainEntity' => [
        '@type' => 'ItemList',
        'numberOfItems' => $portfolioItems->count(),
        'itemListElement' => $portfolioItems->values()->map(fn ($item, $index) => [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $item->title,
            'url' => \App\Support\Seo::url($item->seoPath()),
        ])->all(),
    ],
]))

@push('head')
@include('partials.portfolio-card-styles')
<style>
.works-page-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-bottom:60px; }
    .portfolio-toolbar { display:flex; align-items:center; justify-content:space-between; gap:24px; flex-wrap:wrap; margin-bottom:30px; }
    .portfolio-toolbar .works-filter { margin:0; justify-content:flex-start; }
    .portfolio-count { color:var(--text-secondary); font-size:0.85rem; }
    html:not([data-theme="light"]) .portfolio-toolbar .filter-btn.active { color:#141414; }
    .portfolio-mobile-category { display:none; }
    .portfolio-collections { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:26px; }
    .portfolio-collection-btn { padding:12px 18px; display:flex; align-items:center; gap:12px; background:var(--dark-2); color:var(--text-secondary); border:1px solid var(--border); border-radius:var(--radius-md); font:inherit; font-size:0.85rem; font-weight:600; cursor:pointer; }
    .portfolio-collection-btn span { font-size:0.72rem; background:var(--dark-4); padding:3px 8px; border-radius:100px; }
    .portfolio-collection-btn[aria-pressed="true"] { color:var(--primary); border-color:var(--primary); background:var(--primary-glow); }
    .portfolio-collection-btn:focus-visible { outline:2px solid var(--primary); outline-offset:3px; }
    .portfolio-empty { padding:45px 24px; text-align:center; border:1px dashed var(--border); border-radius:var(--radius-lg); color:var(--text-secondary); margin-bottom:36px; }
    .portfolio-empty p { margin-bottom:20px; }
    .works-page-grid[hidden] { display:none; }
    .portfolio-note { max-width:620px; margin:20px auto 0; color:var(--text-muted); font-size:0.85rem; line-height:1.7; text-align:center; }
    .portfolio-guide { display:flex; align-items:center; justify-content:space-between; gap:28px; padding:26px 30px; margin-bottom:30px; border:1px solid var(--border); background:var(--dark-2); border-radius:var(--radius-lg); }
    .portfolio-guide h2 { color:var(--text-primary); font-size:1.08rem; margin-bottom:9px; }
    .portfolio-guide p { color:var(--text-secondary); font-size:0.85rem; line-height:1.8; max-width:690px; }
    .portfolio-guide a { color:var(--primary); text-decoration:underline; text-underline-offset:3px; }
    .portfolio-guide .btn { flex-shrink:0; text-decoration:none; }
    .portfolio-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:24px; }
    .portfolio-stats .trust-sep { display:none; }
    #portfolio, .portfolio-guide { scroll-margin-top:96px; }
    .wp-mock { width:82%; }
    .wm-bar { height:8px; background:rgba(255,255,255,0.1); border-radius:4px; margin-bottom:10px; }
    .wm-hero { border-radius:6px; margin-bottom:10px; }
    .wm-cols { display:flex; gap:8px; }
    .wm-cols div { flex:1; border-radius:6px; }
    @media(max-width:1024px){ .works-page-grid{grid-template-columns:repeat(2,1fr);} }
    @media(max-width:600px){
      .works-page-grid{grid-template-columns:1fr;}
      .portfolio-stats{grid-template-columns:repeat(2,minmax(0,1fr));gap:24px 16px;}
      .portfolio-guide{flex-direction:column;align-items:flex-start;padding:22px;gap:20px;}
      .portfolio-collections{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}
      .portfolio-collection-btn{font-size:0.77rem;padding:12px;gap:8px;justify-content:space-between;min-height:62px;text-align:left;}
      .portfolio-toolbar{gap:18px;}
      .portfolio-toolbar [data-portfolio-filters]{display:none;}
      .portfolio-mobile-category{display:block;width:100%;font-size:0.76rem;color:var(--text-secondary);}
      .portfolio-mobile-category select{display:block;width:100%;margin-top:9px;padding:12px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--dark-2);color:var(--text-primary);font:inherit;font-size:0.9rem;}
    }
    @media(prefers-reduced-motion:reduce){ .wp-card,.wp-overlay{transition:none;} .wp-card:hover{transform:none;} }
</style>
@endpush

@section('content')
@php
    $demoCount = $portfolioItems->filter(fn ($item) => $item->project_type !== 'technical_demonstration' && in_array('Demo', $item->tags ?? [], true))->count();
    $technicalCount = $portfolioItems->where('project_type', 'technical_demonstration')->count();
    $categoryLabels = \App\Models\PortfolioItem::CATEGORY_OPTIONS;
@endphp
<section class="page-hero" style="padding-bottom:0">
    <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
    <div class="container" style="padding:100px 24px 60px; text-align:center; position:relative">
      <div data-aos="fade-up">
        <div class="breadcrumb" style="justify-content:center"><a href="{{ route('home') }}">Home</a><span>/</span><span class="bc-current">Our Works</span></div>
        <div class="hero-badge" style="margin:16px auto"><span class="badge-dot"></span>Portfolio</div>
        <h1 class="page-hero-title">Website & Cloud <span class="text-gradient">Portfolio</span></h1>
        <p class="page-hero-sub" style="margin:0 auto 36px;max-width:600px">Business websites, design ideas, and the systems behind them. Explore our website projects and Arif Billah's technical portfolio.</p>
      </div>
    </div>
    <div class="trust-bar"><div class="container trust-inner portfolio-stats">
      <div class="trust-item"><span class="trust-num">{{ $portfolioItems->count() }}</span><span class="trust-label">Portfolio Entries</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">{{ $portfolioItems->count() - $demoCount - $technicalCount }}</span><span class="trust-label">Website Projects</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">{{ $demoCount }}</span><span class="trust-label">Design Demos</span></div>
      @if($technicalCount)
      <div class="trust-sep"></div><div class="trust-item"><span class="trust-num">{{ $technicalCount }}</span><span class="trust-label">Technical Topics</span></div>
      @endif
    </div></div>
  </section>

  <section class="section" id="portfolio">
    <div class="container">
      <div class="portfolio-guide">
        <div><h2>Explore the work behind your next project</h2><p>Browse website and design work alongside an attributed technical collection covering <a href="{{ route('manage-cloud') }}">cloud and Linux hosting</a>, <a href="{{ route('web-development') }}">WordPress and web development</a>, and <a href="{{ route('automation') }}">automation</a>. Each technical entry identifies its scope and the supporting evidence available.</p></div>
        <a href="{{ route('services') }}" class="btn btn-outline">Explore our services</a>
      </div>
      @if($technicalCount)
      <div class="portfolio-collections" role="group" aria-label="Choose a portfolio collection">
        <button type="button" class="portfolio-collection-btn" data-collection="all" aria-pressed="true">All work <span>{{ $portfolioItems->count() }}</span></button>
        <button type="button" class="portfolio-collection-btn" data-collection="websites" aria-pressed="false">Websites & design <span>{{ $portfolioItems->count() - $technicalCount }}</span></button>
        <button type="button" class="portfolio-collection-btn" data-collection="technical" aria-pressed="false">Technical topics <span>{{ $technicalCount }}</span></button>
        <button type="button" class="portfolio-collection-btn" data-collection="video" aria-pressed="false">Video demonstrations <span>{{ $portfolioItems->filter(fn ($item) => $item->project_type === 'technical_demonstration' && $item->publicVideoUrl())->count() }}</span></button>
      </div>
      @endif
      <div class="portfolio-toolbar">
        <div class="works-filter" data-portfolio-filters role="group" aria-label="Filter portfolio projects">
          <button type="button" class="filter-btn active" data-filter="all" aria-pressed="true">All projects</button>
          @foreach($categoryLabels as $category => $label)
            @if($portfolioItems->contains('category', $category))
            <button type="button" class="filter-btn" data-filter="{{ $category }}" aria-pressed="false">{{ $label }}</button>
            @endif
          @endforeach
          @if($demoCount)
          <button type="button" class="filter-btn" data-filter="demo" aria-pressed="false">Design demos</button>
          @endif
        </div>
        <label class="portfolio-mobile-category" for="portfolioCategorySelect">Project category
          <select id="portfolioCategorySelect">
            <option value="all">All categories</option>
            @foreach($categoryLabels as $category => $label)
              @if($portfolioItems->contains('category', $category))
              <option value="{{ $category }}">{{ $label }}</option>
              @endif
            @endforeach
            @if($demoCount)
            <option value="demo">Design demos</option>
            @endif
          </select>
        </label>
        <p class="portfolio-count" id="portfolioCount" aria-live="polite" aria-atomic="true">Showing {{ $portfolioItems->count() }} of {{ $portfolioItems->count() }} projects</p>
      </div>
      <div class="works-page-grid" id="portfolioGrid">
        @foreach($portfolioItems as $item)
        <x-portfolio-card :project="$item" :delay="($loop->index % 3) * 60" />
        @endforeach
      </div>
      <div class="portfolio-empty" id="portfolioEmpty" hidden><p>No projects match this combination. Try another category or browse all work.</p><button type="button" class="btn btn-outline" data-portfolio-reset>Reset filters</button></div>
      <p class="portfolio-note">Technical overviews, video demonstrations, and design demos are labeled separately. Illustrated covers represent topics rather than screenshots of client systems. Linked content may change over time.</p>

      <div style="text-align:center;display:flex;gap:16px;justify-content:center;flex-wrap:wrap" data-aos="fade-up">
        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Start Your Project</a>
        <a href="{{ route('case-study') }}" class="btn btn-outline btn-lg">See Case Studies</a>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
