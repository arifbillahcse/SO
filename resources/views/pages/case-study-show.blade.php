@extends('layouts.app')

@section('title', $caseStudy->seoDefaultTitle())
@section('description', \App\Support\Seo::excerpt($caseStudy->description))
@section('breadcrumbs', json_encode([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Case Studies', 'url' => route('case-study')],
    ['name' => $caseStudy->title, 'url' => route('case-study.show', $caseStudy)],
]))
@section('page_schema', json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'CreativeWork',
    '@id' => \App\Support\Seo::url($caseStudy->seoPath()).'#casestudy',
    'name' => $caseStudy->title,
    'headline' => $caseStudy->title,
    'description' => \App\Support\Seo::plain($caseStudy->seo?->meta_description) ?: $caseStudy->description,
    'image' => \App\Support\Seo::imageUrl($caseStudy->seo?->og_image),
    'keywords' => implode(', ', $caseStudy->tags ?? []) ?: null,
    'dateModified' => $caseStudy->updated_at?->toAtomString(),
    'creator' => ['@id' => \App\Support\SeoHead::organizationId()],
    'mainEntityOfPage' => \App\Support\Seo::url($caseStudy->seoPath()),
])))

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

@if($relatedCaseStudies->isNotEmpty())
<section class="section" style="background:var(--dark-2);padding:70px 0">
  <div class="container">
    <div class="section-header" data-aos="fade-up" style="text-align:center">
      <span class="section-tag">More Results</span>
      <h2 class="section-title">Related <span class="text-gradient">Case Studies</span></h2>
    </div>
    <div class="related-articles-grid">
      @foreach($relatedCaseStudies as $related)
      <a href="{{ route('case-study.show', $related) }}" style="display:block;background:var(--dark-3);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;color:inherit" data-aos="fade-up">
        <div style="font-size:0.75rem;color:var(--primary);font-weight:600;margin-bottom:8px">{{ implode(' · ', $related->tags ?? []) }}</div>
        <h3 style="font-size:1rem;font-weight:700;color:var(--text-primary);line-height:1.4">{{ $related->title }}</h3>
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
