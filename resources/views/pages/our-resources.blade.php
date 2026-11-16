@extends('layouts.app')

@section('title', 'WordPress, Cloud & Automation Guides | Softorio')
@section('description', 'Practical Softorio articles about website launches, cloud hosting, backups and business automation. Browse guides by category.')

@push('head')
@include('partials.case-study-styles')
<style>
.resource-filter{display:flex;gap:10px;flex-wrap:wrap;margin:0 0 36px}
.resource-filter a{padding:9px 18px;border:1px solid var(--border);border-radius:100px;color:var(--text-secondary);font-size:.84rem;background:var(--dark-3)}
.resource-filter a[aria-current="page"],.resource-filter a:hover{background:var(--primary-glow);border-color:var(--case-accent-border);color:var(--primary)}
.resource-feature{grid-template-columns:minmax(0,1.6fr) minmax(0,1fr)}
.resource-feature-aside{align-self:center;border-left:1px solid var(--border);padding-left:32px}
.resource-feature-aside p{color:var(--text-secondary);font-size:.9rem;line-height:1.8;margin-top:16px}
.resource-card-image{display:block;width:100%;aspect-ratio:16/9;object-fit:cover;border:1px solid var(--border);border-radius:12px;margin-bottom:24px}
.resource-feature-image{align-self:center}
.resource-feature-image img{display:block;width:100%;aspect-ratio:16/9;object-fit:cover;border:1px solid var(--border);border-radius:16px}
.resource-feature-image p{font-size:.78rem;color:var(--text-muted);margin-top:14px;line-height:1.7}
.resource-meta{font-size:.76rem;color:var(--text-muted);margin:18px 0}
@media(max-width:768px){.resource-feature{grid-template-columns:1fr}.resource-feature-aside{padding:20px 0 0;border-left:0;border-top:1px solid var(--border)}}
</style>
@endpush

@section('content')
<main class="case-library">
  <section class="case-hero">
    <div class="page-hero-bg" aria-hidden="true"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
    <div class="container">
      <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span class="bc-current">Our Resources</span></div>
      <span class="section-tag">The Softorio journal</span>
      <h1>Practical knowledge.<br><span class="text-gradient">Confident next steps.</span></h1>
      <p class="case-lead">Guides for the decisions behind your website, cloud infrastructure and business workflows. Explore a topic, understand the checks and plan what comes next.</p>
      <div class="case-hero-bottom"><span>{{ $articleCount }} {{ $articleCount === 1 ? 'article' : 'articles' }}{{ $activeCategory ? ' in this category' : ' to explore' }}</span><a href="#article-collection">Browse the articles <span aria-hidden="true">&darr;</span></a></div>
    </div>
  </section>

  <section class="case-collection" id="article-collection">
    <div class="container">
      <nav class="resource-filter" aria-label="Article categories">
        <a href="{{ route('our-resources') }}#article-collection" @if(!$activeCategory) aria-current="page" @endif>All articles</a>
        @foreach($categories as $category)
        <a href="{{ route('our-resources', ['category' => $category->slug]) }}#article-collection" @if($activeCategory === $category->slug) aria-current="page" @endif>{{ $category->name }}</a>
        @endforeach
      </nav>

      @if($featuredArticle)
      <article class="case-featured resource-feature">
        <div class="case-featured-copy">
          <span class="case-eyebrow">Featured guide</span>
          <div class="case-tags"><span>{{ $featuredArticle->category_label }}</span></div>
          <h2>{{ $featuredArticle->title }}</h2>
          <p>{{ $featuredArticle->excerpt }}</p>
          <a href="{{ route('our-resources.show', $featuredArticle) }}" class="btn btn-primary">Read the guide <span aria-hidden="true">&nearr;</span></a>
        </div>
        @if($featuredArticle->featuredImageUrl())
        <div class="resource-feature-image"><img src="{{ $featuredArticle->featuredImageUrl() }}" alt="{{ $featuredArticle->featuredImageAltText() }}" loading="lazy" decoding="async"><p>{{ $featuredArticle->read_minutes }} min read &middot; {{ $featuredArticle->published_at->format('M j, Y') }}</p></div>
        @else
        <div class="resource-feature-aside"><span class="case-eyebrow">Make time for the details</span><p>{{ $featuredArticle->read_minutes }} min read<br>Published {{ $featuredArticle->published_at->format('F j, Y') }}</p><p>Practical guidance with related services and references to help you explore the subject.</p></div>
        @endif
      </article>
      @endif

      <div class="case-grid">
        @foreach($articles as $article)
        <a href="{{ route('our-resources.show', $article) }}" class="case-card">
          @if($article->featuredImageUrl())<img class="resource-card-image" src="{{ $article->featuredImageUrl() }}" alt="{{ $article->featuredImageAltText() }}" loading="lazy" decoding="async">@endif
          <span class="case-eyebrow">{{ $article->category_label }}</span>
          <p class="resource-meta">{{ $article->read_minutes }} min read &middot; {{ $article->published_at->format('M j, Y') }}</p>
          <h3>{{ $article->title }}</h3>
          <p class="case-card-description">{{ $article->excerpt }}</p>
          <div class="case-card-bottom"><span class="case-read">Read the article <span aria-hidden="true">&nearr;</span></span></div>
        </a>
        @endforeach
      </div>
      @if($articleCount === 0)<p class="case-empty">No published articles in this category yet. <a href="{{ route('our-resources') }}" class="case-text-link">View all articles</a>.</p>@endif
    </div>
  </section>
</main>
@endsection

@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
