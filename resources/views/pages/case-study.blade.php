@extends('layouts.app')

@section('title', 'Case Studies — Softorio')

@push('head')
<style>
.cs-featured { background:linear-gradient(135deg,rgba(255,121,0,0.1),var(--dark-2)); border:1px solid rgba(255,121,0,0.25); border-radius:var(--radius-xl); padding:48px; display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center; margin-bottom:60px; }
    .cs-featured-img { height:280px; background:linear-gradient(135deg,#0a0a1a,#1a1a3e); border-radius:var(--radius-lg); display:flex; align-items:center; justify-content:center; border:1px solid var(--border); }
    .cs-meta { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:20px; }
    .cs-tag { font-size:0.72rem; font-weight:700; color:var(--primary); background:var(--primary-glow); border:1px solid rgba(255,121,0,0.2); padding:3px 12px; border-radius:100px; }
    .cs-results { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin:24px 0; }
    .cs-result { background:var(--dark-3); border:1px solid var(--border); border-radius:var(--radius-md); padding:16px; text-align:center; }
    .cs-result-num { font-size:1.6rem; font-weight:800; color:var(--primary); display:block; letter-spacing:-0.03em; }
    .cs-result-label { font-size:0.75rem; color:var(--text-muted); margin-top:4px; font-weight:500; }
    .cs-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
    .cs-card { background:var(--dark-2); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; transition:var(--transition); }
    .cs-card:hover { transform:translateY(-6px); box-shadow:var(--shadow-card); border-color:rgba(255,121,0,0.3); }
    .cs-card-img { height:160px; display:flex; align-items:center; justify-content:center; }
    .cs-card-body { padding:24px; }
    .cs-card-meta { display:flex; gap:6px; margin-bottom:12px; }
    .cs-card-title { font-size:1rem; font-weight:700; color:var(--text-primary); margin-bottom:8px; }
    .cs-card-desc { font-size:0.87rem; color:var(--text-secondary); line-height:1.6; margin-bottom:16px; }
    .cs-card-results { display:flex; gap:16px; }
    .cs-mini-result { text-align:center; }
    .cs-mini-num { font-size:1.1rem; font-weight:800; color:var(--primary); display:block; }
    .cs-mini-label { font-size:0.68rem; color:var(--text-muted); }
    @media(max-width:1024px){ .cs-featured{grid-template-columns:1fr;} .cs-grid{grid-template-columns:repeat(2,1fr);} }
    @media(max-width:600px){ .cs-grid{grid-template-columns:1fr;} .cs-results{grid-template-columns:1fr 1fr;} }
</style>
@endpush

@section('content')
<section class="page-hero" style="padding-bottom:0">
    <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
    <div class="container" style="padding:100px 24px 60px;text-align:center;position:relative" data-aos="fade-up">
      <div class="breadcrumb" style="justify-content:center"><a href="{{ route('home') }}">Home</a><span>/</span><a href="#">Resources</a><span>/</span><span class="bc-current">Case Studies</span></div>
      <div class="hero-badge" style="margin:16px auto"><span class="badge-dot"></span>Real Results</div>
      <h1 class="page-hero-title">Real Clients. <span class="text-gradient">Real Results.</span></h1>
      <p class="page-hero-sub" style="margin:0 auto;max-width:540px">Behind every number is a business that trusted us to solve a real problem. Here's proof of what we deliver.</p>
    </div>
    <div class="trust-bar"><div class="container trust-inner">
      <div class="trust-item"><span class="trust-num">340%</span><span class="trust-label">Avg Traffic Increase (SEO)</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">220%</span><span class="trust-label">Avg Conversion Lift</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">99.97%</span><span class="trust-label">Avg Server Uptime</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">80%</span><span class="trust-label">Reduction in Manual Work</span></div>
    </div></div>
  </section>

  <section class="section">
    <div class="container">

      @if($featuredCaseStudy)
      <!-- Featured Case Study -->
      <div class="cs-featured" data-aos="fade-up">
        <div class="cs-featured-img">
          <div style="width:80%">
            <div style="height:8px;background:rgba(255,255,255,0.1);border-radius:4px;margin-bottom:12px"></div>
            <div style="height:60px;background:rgba(255,121,0,0.1);border-radius:8px;margin-bottom:12px"></div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px"><div style="height:35px;background:rgba(255,255,255,0.06);border-radius:6px"></div><div style="height:35px;background:rgba(255,255,255,0.06);border-radius:6px"></div><div style="height:35px;background:rgba(255,255,255,0.06);border-radius:6px"></div></div>
          </div>
        </div>
        <div>
          <div class="cs-meta">
            @foreach($featuredCaseStudy->tags as $tag)
            <span class="cs-tag">{{ $tag }}</span>
            @endforeach
          </div>
          <h2 class="section-title" style="text-align:left;font-size:clamp(1.5rem,3vw,2rem)">{{ $featuredCaseStudy->title }}</h2>
          <p style="font-size:0.95rem;color:var(--text-secondary);line-height:1.7;margin-bottom:24px">{{ $featuredCaseStudy->description }}</p>
          <div class="cs-results">
            @foreach($featuredCaseStudy->metrics as $metric)
            <div class="cs-result"><span class="cs-result-num">{{ $metric['value'] }}</span><div class="cs-result-label">{{ $metric['label'] }}</div></div>
            @endforeach
          </div>
          <a href="{{ route('home') }}#contact" class="btn btn-primary">Start a Similar Project</a>
        </div>
      </div>
      @endif

      @php
        $csGradients = [
          'linear-gradient(135deg,#0f2027,#203a43)',
          'linear-gradient(135deg,#1a0a2e,#2d1a4e)',
          'linear-gradient(135deg,#0d0d0d,#1a1a2e)',
          'linear-gradient(135deg,#0f0c29,#302b63)',
          'linear-gradient(135deg,#1a0a0a,#2e1a1a)',
          'linear-gradient(135deg,#0d1a0d,#1a3a1a)',
        ];
      @endphp
      <!-- Case Study Grid -->
      <div class="cs-grid">
        @foreach($caseStudies as $caseStudy)
        <div class="cs-card" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
          <div class="cs-card-img" style="background:{{ $csGradients[$loop->index % count($csGradients)] }}">
            <div style="width:80%;display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px"><div style="height:38px;background:rgba(255,255,255,0.08);border-radius:5px"></div><div style="height:38px;background:rgba(255,255,255,0.08);border-radius:5px"></div><div style="height:38px;background:rgba(255,255,255,0.08);border-radius:5px"></div></div>
          </div>
          <div class="cs-card-body">
            <div class="cs-card-meta">
              @foreach($caseStudy->tags as $tag)
              <span class="cs-tag">{{ $tag }}</span>
              @endforeach
            </div>
            <p class="cs-card-title">{{ $caseStudy->title }}</p>
            <p class="cs-card-desc">{{ $caseStudy->description }}</p>
            <div class="cs-card-results">
              @foreach($caseStudy->metrics as $metric)
              <div class="cs-mini-result"><span class="cs-mini-num">{{ $metric['value'] }}</span><div class="cs-mini-label">{{ $metric['label'] }}</div></div>
              @endforeach
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="section sp-contact" style="padding:80px 0" id="contact-form">
    <div class="sp-contact-bg"><div class="sp-contact-orb"></div></div>
    <div class="container" style="text-align:center;position:relative" data-aos="fade-up">
      <span class="section-tag">Your Project Next</span>
      <h2 class="section-title" style="margin-top:14px">Ready to Become a <span class="text-gradient">Success Story?</span></h2>
      <p class="section-sub" style="margin:0 auto 36px;max-width:480px">Let's discuss your project and show you how we can deliver results like these for your business.</p>
      <a href="{{ route('home') }}#contact" class="btn btn-primary btn-lg">Start Your Project</a>
    </div>
  </section>
@endsection

@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
