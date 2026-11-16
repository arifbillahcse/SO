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
    'genre' => $caseStudy->isIllustrative() ? 'Illustrative scenario' : null,
    'description' => \App\Support\Seo::plain($caseStudy->seo?->meta_description) ?: $caseStudy->description,
    'image' => \App\Support\Seo::imageUrl($caseStudy->seo?->og_image),
    'keywords' => implode(', ', $caseStudy->tags ?? []) ?: null,
    'dateModified' => $caseStudy->updated_at?->toAtomString(),
    'creator' => ['@id' => \App\Support\SeoHead::organizationId()],
    'mainEntityOfPage' => \App\Support\Seo::url($caseStudy->seoPath()),
])))

@push('head')
@include('partials.case-study-styles')
@endpush

@section('content')
<main class="case-detail">
  <section class="case-hero">
    <div class="page-hero-bg" aria-hidden="true"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
    <div class="container">
      <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><a href="{{ route('case-study') }}">Case Studies</a><span>/</span><span class="bc-current">The study</span></div>
      <div class="case-tags"><span class="case-type">{{ $caseStudy->isIllustrative() ? 'Illustrative scenario' : 'Case study' }}</span>@foreach($caseStudy->tags ?? [] as $tag)<span>{{ $tag }}</span>@endforeach</div>
      <h1>{{ $caseStudy->title }}</h1>
      <p class="case-lead">{{ $caseStudy->description }}</p>
      @if($caseStudy->client)<div class="case-hero-bottom"><span>Client: {{ $caseStudy->client }}</span></div>@endif
    </div>
  </section>

  <section class="case-detail-content">
    <div class="container">
      <div class="case-reading">
        @if($caseStudy->isIllustrative())
        <aside class="case-disclosure"><p><strong>Illustrative scenario.</strong> Figures are stated assumptions or modelled calculations, not measured client results. No customer engagement is claimed.</p></aside>
        @endif

        @if(!empty($caseStudy->metrics))
        <div class="case-detail-metrics">
          @if($caseStudy->isIllustrative())<p><strong>Illustrative figures:</strong> read the assumptions and calculation below before interpreting these numbers.</p>@else<p><strong>Project figures</strong></p>@endif
          <div class="case-stat-grid">
            @foreach($caseStudy->metrics as $metric)
            <div class="case-stat"><strong>{{ $metric['value'] }}</strong><span>{{ $metric['label'] }}</span></div>
            @endforeach
          </div>
        </div>
        @endif

        <article class="article-body">
          @if($caseStudy->body){!! $caseStudy->body !!}@endif
        </article>
      </div>
    </div>
  </section>

  @if($relatedCaseStudies->isNotEmpty())
  <section class="case-related">
    <div class="container">
      <div class="case-collection-heading"><div><span class="case-eyebrow">Keep exploring</span><h2>Another perspective.<br><span class="text-gradient">A new possibility.</span></h2></div><a href="{{ route('case-study') }}" class="case-text-link">All case studies <span aria-hidden="true">&rarr;</span></a></div>
      <div class="case-grid">
        @foreach($relatedCaseStudies as $related)
        <a href="{{ route('case-study.show', $related) }}" class="case-card">
          <span class="case-eyebrow">{{ $related->isIllustrative() ? 'Illustrative scenario' : 'Case study' }}</span>
          <div class="case-tags">@foreach($related->tags ?? [] as $tag)<span>{{ $tag }}</span>@endforeach</div>
          <h3>{{ $related->title }}</h3>
          <span class="case-read">Read the study <span aria-hidden="true">&nearr;</span></span>
        </a>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <section class="case-contact"><div class="container"><div class="case-contact-inner"><div><span class="case-eyebrow">Your next step</span><h2>Let's plan <span class="text-gradient">your project.</span></h2><p>Discuss your requirements and agree an approach, scope and measurement plan for your project.</p></div><div class="case-contact-actions"><a href="{{ route('home') }}#contact" class="btn btn-primary">Discuss Your Project <span aria-hidden="true">&nearr;</span></a><a href="{{ route('case-study') }}" class="case-text-link">Back to the collection <span aria-hidden="true">&rarr;</span></a></div></div></div></section>
</main>
@endsection

@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
