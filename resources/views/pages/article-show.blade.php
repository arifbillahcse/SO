@extends('layouts.app')

@section('title', $article->meta_title ?: $article->title . ' — Softorio')
@section('description', $article->meta_description ?: \Illuminate\Support\Str::limit($article->excerpt, 155))
@if($article->og_image)
@section('image', asset('storage/' . $article->og_image))
@endif
@if(! ($article->robots_index ?? true) || ! ($article->robots_follow ?? true))
@section('meta')
  <meta name="robots" content="{{ ($article->robots_index ?? true) ? 'index' : 'noindex' }}, {{ ($article->robots_follow ?? true) ? 'follow' : 'nofollow' }}" />
@endsection
@endif

@push('head')
<style>
.article-body { font-size:1rem; color:var(--text-secondary); line-height:1.8; max-width:720px; margin:0 auto; }
.article-body h2 { font-size:1.5rem; font-weight:700; color:var(--text-primary); margin:36px 0 14px; }
.article-body h3 { font-size:1.2rem; font-weight:700; color:var(--text-primary); margin:28px 0 12px; }
.article-body p { margin-bottom:18px; }
.article-body ul, .article-body ol { margin:0 0 18px 20px; }
.article-body li { margin-bottom:8px; }
.article-body a { color:var(--primary); text-decoration:underline; }
.article-body img { max-width:100%; border-radius:var(--radius-md); margin:20px 0; }
.related-articles-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-top:16px; }
@media(max-width:768px){ .related-articles-grid{grid-template-columns:1fr;} }
</style>
@endpush

@section('content')
<section class="page-hero" style="padding-bottom:0">
  <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
  <div class="container" style="padding:100px 24px 60px;text-align:center;position:relative" data-aos="fade-up">
    <div class="breadcrumb" style="justify-content:center">
      <a href="{{ route('home') }}">Home</a><span>/</span>
      <a href="{{ route('our-resources') }}">Our Resources</a><span>/</span>
      <span class="bc-current">{{ $article->title }}</span>
    </div>
    <span class="hero-badge" style="margin:16px auto"><span class="badge-dot"></span>{{ $article->category }}</span>
    <h1 class="page-hero-title" style="max-width:760px;margin-left:auto;margin-right:auto">{{ $article->title }}</h1>
    <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;margin-top:16px">
      <span style="font-size:0.82rem;color:var(--text-muted);font-weight:500">&#128336; {{ $article->read_minutes }} min read</span>
      @if($article->published_at)
      <span style="font-size:0.82rem;color:var(--text-muted);font-weight:500">&#128197; {{ $article->published_at->format('F j, Y') }}</span>
      @endif
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="article-body" data-aos="fade-up">
      @if($article->body)
        {!! $article->body !!}
      @else
        <p>{{ $article->excerpt }}</p>
      @endif
    </div>
  </div>
</section>

@if($relatedArticles->isNotEmpty())
<section class="section" style="background:var(--dark-2);padding:70px 0">
  <div class="container">
    <div class="section-header" data-aos="fade-up" style="text-align:center">
      <span class="section-tag">Keep Reading</span>
      <h2 class="section-title">More <span class="text-gradient">Guides</span></h2>
    </div>
    <div class="related-articles-grid">
      @foreach($relatedArticles as $related)
      <a href="{{ route('our-resources.show', $related) }}" style="display:block;background:var(--dark-3);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;color:inherit" data-aos="fade-up">
        <div style="font-size:0.75rem;color:var(--primary);font-weight:600;margin-bottom:8px">{{ $related->category }}</div>
        <div style="font-size:1rem;font-weight:700;color:var(--text-primary);line-height:1.4">{{ $related->title }}</div>
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif
@endsection

@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
