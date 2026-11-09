@extends('layouts.app')

@section('title', $portfolioItem->meta_title ?: $portfolioItem->title . ' — Softorio Portfolio')
@section('description', $portfolioItem->meta_description ?: \Illuminate\Support\Str::limit($portfolioItem->description ?? $portfolioItem->title, 155))
@section('breadcrumbs', json_encode([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Our Works', 'url' => route('our-works')],
    ['name' => $portfolioItem->title, 'url' => route('our-works.show', $portfolioItem)],
]))
@if(! ($portfolioItem->robots_index ?? true) || ! ($portfolioItem->robots_follow ?? true))
@section('meta')
  <meta name="robots" content="{{ ($portfolioItem->robots_index ?? true) ? 'index' : 'noindex' }}, {{ ($portfolioItem->robots_follow ?? true) ? 'follow' : 'nofollow' }}" />
@endsection
@endif

@push('head')
<style>
.wp-tags { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px; justify-content:center; }
.wp-tag { font-size:0.7rem; font-weight:700; color:var(--primary); background:var(--primary-glow); padding:2px 10px; border-radius:100px; letter-spacing:0.05em; }
.article-body { font-size:1rem; color:var(--text-secondary); line-height:1.8; max-width:720px; margin:0 auto; }
.article-body h2 { font-size:1.5rem; font-weight:700; color:var(--text-primary); margin:36px 0 14px; }
.article-body h3 { font-size:1.2rem; font-weight:700; color:var(--text-primary); margin:28px 0 12px; }
.article-body p { margin-bottom:18px; }
.article-body ul, .article-body ol { margin:0 0 18px 20px; }
.article-body a { color:var(--primary); text-decoration:underline; }
.related-works-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-top:16px; }
@media(max-width:768px){ .related-works-grid{grid-template-columns:1fr;} }
</style>
@php
  $portfolioItemSchema = json_encode(array_filter([
      '@context' => 'https://schema.org',
      '@type' => 'CreativeWork',
      'name' => $portfolioItem->title,
      'description' => $portfolioItem->meta_description ?: $portfolioItem->description,
      'image' => $portfolioItem->thumbnail_path ? asset('storage/' . $portfolioItem->thumbnail_path) : null,
      'dateModified' => $portfolioItem->updated_at->toAtomString(),
      'creator' => ['@type' => 'Organization', 'name' => 'Softorio'],
      'mainEntityOfPage' => route('our-works.show', $portfolioItem),
  ]));
@endphp
<script type="application/ld+json">{!! $portfolioItemSchema !!}</script>
@endpush

@section('content')
<section class="page-hero" style="padding-bottom:0">
  <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
  <div class="container" style="padding:100px 24px 60px;text-align:center;position:relative" data-aos="fade-up">
    <div class="breadcrumb" style="justify-content:center">
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
    @if($portfolioItem->client)
    <p class="page-hero-sub" style="margin:0 auto;max-width:540px">Client: {{ $portfolioItem->client }}</p>
    @endif
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="article-body" data-aos="fade-up">
      @if($portfolioItem->body)
        {!! $portfolioItem->body !!}
      @elseif($portfolioItem->description)
        <p>{{ $portfolioItem->description }}</p>
      @endif
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
      <a href="{{ route('our-works.show', $related) }}" style="display:block;background:var(--dark-3);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;color:inherit" data-aos="fade-up">
        <div style="font-size:0.75rem;color:var(--primary);font-weight:600;margin-bottom:8px">{{ $related->category }}</div>
        <div style="font-size:1rem;font-weight:700;color:var(--text-primary);line-height:1.4">{{ $related->title }}</div>
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
