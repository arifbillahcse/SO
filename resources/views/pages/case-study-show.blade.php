@extends('layouts.app')

@section('title', $caseStudy->meta_title ?: $caseStudy->title . ' — Softorio Case Study')
@section('description', $caseStudy->meta_description ?: \Illuminate\Support\Str::limit($caseStudy->description, 155))
@if($caseStudy->og_image)
@section('image', asset('storage/' . $caseStudy->og_image))
@endif
@section('breadcrumbs', json_encode([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Case Studies', 'url' => route('case-study')],
    ['name' => $caseStudy->title, 'url' => route('case-study.show', $caseStudy)],
]))
@if(! ($caseStudy->robots_index ?? true) || ! ($caseStudy->robots_follow ?? true))
@section('meta')
  <meta name="robots" content="{{ ($caseStudy->robots_index ?? true) ? 'index' : 'noindex' }}, {{ ($caseStudy->robots_follow ?? true) ? 'follow' : 'nofollow' }}" />
@endsection
@endif

@push('head')
<style>
.cs-tag { font-size:0.72rem; font-weight:700; color:var(--primary); background:var(--primary-glow); border:1px solid rgba(255,121,0,0.2); padding:3px 12px; border-radius:100px; }
.cs-results { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin:28px 0; max-width:720px; }
.cs-result { background:var(--dark-3); border:1px solid var(--border); border-radius:var(--radius-md); padding:16px; text-align:center; }
.cs-result-num { font-size:1.6rem; font-weight:800; color:var(--primary); display:block; letter-spacing:-0.03em; }
.cs-result-label { font-size:0.75rem; color:var(--text-muted); margin-top:4px; font-weight:500; }
.article-body { font-size:1rem; color:var(--text-secondary); line-height:1.8; max-width:720px; margin:0 auto; }
.article-body h2 { font-size:1.5rem; font-weight:700; color:var(--text-primary); margin:36px 0 14px; }
.article-body h3 { font-size:1.2rem; font-weight:700; color:var(--text-primary); margin:28px 0 12px; }
.article-body p { margin-bottom:18px; }
.article-body ul, .article-body ol { margin:0 0 18px 20px; }
.article-body a { color:var(--primary); text-decoration:underline; }
@media(max-width:600px){ .cs-results{grid-template-columns:1fr 1fr;} }
</style>
@php
  $caseStudySchema = json_encode(array_filter([
      '@context' => 'https://schema.org',
      '@type' => 'CreativeWork',
      'name' => $caseStudy->title,
      'description' => $caseStudy->meta_description ?: $caseStudy->description,
      'image' => $caseStudy->og_image ? asset('storage/' . $caseStudy->og_image) : null,
      'dateModified' => $caseStudy->updated_at->toAtomString(),
      'creator' => ['@type' => 'Organization', 'name' => 'Softorio'],
      'mainEntityOfPage' => route('case-study.show', $caseStudy),
  ]));
@endphp
<script type="application/ld+json">{!! $caseStudySchema !!}</script>
@endpush

@section('content')
<section class="page-hero" style="padding-bottom:0">
  <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
  <div class="container" style="padding:100px 24px 60px;text-align:center;position:relative" data-aos="fade-up">
    <div class="breadcrumb" style="justify-content:center">
      <a href="{{ route('home') }}">Home</a><span>/</span>
      <a href="{{ route('case-study') }}">Case Studies</a><span>/</span>
      <span class="bc-current">{{ $caseStudy->title }}</span>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin:16px 0">
      @foreach($caseStudy->tags as $tag)
      <span class="cs-tag">{{ $tag }}</span>
      @endforeach
    </div>
    <h1 class="page-hero-title" style="max-width:760px;margin-left:auto;margin-right:auto">{{ $caseStudy->title }}</h1>
    @if($caseStudy->client)
    <p class="page-hero-sub" style="margin:0 auto;max-width:540px">Client: {{ $caseStudy->client }}</p>
    @endif
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="article-body" data-aos="fade-up">
      <p>{{ $caseStudy->description }}</p>

      @if(! empty($caseStudy->metrics))
      <div class="cs-results">
        @foreach($caseStudy->metrics as $metric)
        <div class="cs-result"><span class="cs-result-num">{{ $metric['value'] }}</span><div class="cs-result-label">{{ $metric['label'] }}</div></div>
        @endforeach
      </div>
      @endif

      @if($caseStudy->body)
        {!! $caseStudy->body !!}
      @endif
    </div>
  </div>
</section>

<section class="section sp-contact" style="padding:70px 0" id="contact-form">
  <div class="sp-contact-bg"><div class="sp-contact-orb"></div></div>
  <div class="container" style="text-align:center;position:relative" data-aos="fade-up">
    <span class="section-tag">Your Project Next</span>
    <h2 class="section-title" style="margin-top:14px">Ready to Become a <span class="text-gradient">Success Story?</span></h2>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;margin-top:24px">
      <a href="{{ route('home') }}#contact" class="btn btn-primary btn-lg">Start Your Project</a>
      <a href="{{ route('case-study') }}" class="btn btn-outline btn-lg">See More Case Studies</a>
    </div>
  </div>
</section>
@endsection

@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
