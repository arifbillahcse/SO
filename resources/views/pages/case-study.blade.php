@extends('layouts.app')
@section('title', 'Case Studies & Illustrative Scenarios | Softorio')
@section('description', 'Explore project approaches and clearly labelled illustrative scenarios for websites, SEO, cloud infrastructure, automation and diagnostic center workflows.')

@push('head')
@include('partials.case-study-styles')
@endpush

@section('content')
<main class="case-library">
  <section class="case-hero">
    <div class="page-hero-bg" aria-hidden="true"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
    <div class="container">
      <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span class="bc-current">Case Studies</span></div>
      <span class="section-tag">Ideas into practice</span>
      <h1>Explore the approach.<br><span class="text-gradient">Understand the details.</span></h1>
      <p class="case-lead">Case Studies &amp; Illustrative Scenarios covering websites, cloud infrastructure and connected workflows. Follow the thinking, scope and calculations behind each approach.</p>
      <div class="case-hero-bottom"><span>{{ $caseStudies->count() + ($featuredCaseStudy ? 1 : 0) }} studies to explore</span><span>Illustrative figures are labelled throughout.</span><a href="#study-collection">Browse the collection <span aria-hidden="true">&darr;</span></a></div>
    </div>
  </section>

  <section class="case-collection" id="study-collection">
    <div class="container">
      @if($featuredCaseStudy)
      <article class="case-featured">
        <div class="case-featured-copy">
          <span class="case-eyebrow">Featured {{ $featuredCaseStudy->isIllustrative() ? 'scenario' : 'case study' }}</span>
          <div class="case-tags">@foreach($featuredCaseStudy->tags ?? [] as $tag)<span>{{ $tag }}</span>@endforeach</div>
          <h2>{{ $featuredCaseStudy->title }}</h2>
          <p>{{ $featuredCaseStudy->description }}</p>
          <a class="btn btn-primary" href="{{ route('case-study.show', $featuredCaseStudy) }}">Explore the study <span aria-hidden="true">&nearr;</span></a>
        </div>
        <div class="case-featured-summary">
          <span class="case-eyebrow">Inside this {{ $featuredCaseStudy->isIllustrative() ? 'scenario' : 'study' }}</span>
          @if($featuredCaseStudy->isIllustrative())<p class="case-summary-note">Illustrative scenario: assumptions, not measured client results.</p>@endif
          @foreach($featuredCaseStudy->metrics ?? [] as $metric)
          <div class="case-featured-metric"><strong>{{ $metric['value'] }}</strong><span>{{ $metric['label'] }}</span></div>
          @endforeach
        </div>
      </article>
      @endif

      <div class="case-collection-heading"><div><span class="case-eyebrow">The collection</span><h2>Find a topic for your <span class="text-gradient">next project.</span></h2></div><p>Read the context, the proposed approach and what would need to be measured.</p></div>
      <div class="case-grid">
        @foreach($caseStudies as $caseStudy)
        <a href="{{ route('case-study.show', $caseStudy) }}" class="case-card">
          <div class="case-card-top"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span class="case-type">{{ $caseStudy->isIllustrative() ? 'Illustrative scenario' : 'Case study' }}</span></div>
          <div class="case-tags">@foreach($caseStudy->tags ?? [] as $tag)<span>{{ $tag }}</span>@endforeach</div>
          <h3>{{ $caseStudy->title }}</h3>
          <p class="case-card-description">{{ $caseStudy->description }}</p>
          <div class="case-card-bottom">
            @if($caseStudy->isIllustrative())<p class="case-card-note">Assumptions, not measured client results.</p>@endif
            @if(!empty($caseStudy->metrics))
            <div class="case-card-metrics">
              @foreach($caseStudy->metrics as $metric)
              <div><strong>{{ $metric['value'] }}</strong><span>{{ $metric['label'] }}</span></div>
              @endforeach
            </div>
            @endif
            <span class="case-read">Read the study <span aria-hidden="true">&nearr;</span></span>
          </div>
        </a>
        @endforeach
      </div>
      @if(!$featuredCaseStudy && $caseStudies->isEmpty())<p class="case-empty">New studies will appear here as they are published. Tell us about the project you have in mind.</p>@endif
    </div>
  </section>
  <section class="case-contact"><div class="container"><div class="case-contact-inner"><div><span class="case-eyebrow">Your next step</span><h2>Let's plan <span class="text-gradient">your project.</span></h2><p>Discuss your requirements and agree an approach, scope and measurement plan for your project.</p></div><div class="case-contact-actions"><a href="{{ route('home') }}#contact" class="btn btn-primary">Discuss Your Project <span aria-hidden="true">&nearr;</span></a><a href="{{ route('our-works') }}" class="case-text-link">See the portfolio <span aria-hidden="true">&rarr;</span></a></div></div></div></section>
</main>
@endsection
@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
