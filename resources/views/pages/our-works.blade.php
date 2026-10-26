@extends('layouts.app')

@section('title', 'Our Works — Softorio Portfolio')
@section('description', '200+ projects delivered across WordPress, eCommerce, cloud infrastructure, and automation — for clients across 3 continents.')

@push('head')
<style>
.works-page-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-bottom:60px; }
    .wp-card { background:var(--dark-2); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; transition:var(--transition); cursor:pointer; }
    .wp-card:hover { transform:translateY(-6px); box-shadow:var(--shadow-card); border-color:rgba(255,121,0,0.3); }
    .wp-card.hidden { display:none; }
    .wp-img { height:200px; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; }
    .wp-overlay { position:absolute; inset:0; background:rgba(0,0,0,0.65); display:flex; align-items:center; justify-content:center; opacity:0; transition:var(--transition); }
    .wp-card:hover .wp-overlay { opacity:1; }
    .wp-view { padding:10px 22px; background:var(--primary); color:#fff; font-size:0.88rem; font-weight:700; border-radius:var(--radius-sm); }
    .wp-info { padding:20px 22px; }
    .wp-tags { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px; }
    .wp-tag { font-size:0.7rem; font-weight:700; color:var(--primary); background:var(--primary-glow); padding:2px 10px; border-radius:100px; letter-spacing:0.05em; }
    .wp-title { font-size:1rem; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .wp-client { font-size:0.8rem; color:var(--text-muted); }
    .wp-mock { width:82%; }
    .wm-bar { height:8px; background:rgba(255,255,255,0.1); border-radius:4px; margin-bottom:10px; }
    .wm-hero { border-radius:6px; margin-bottom:10px; }
    .wm-cols { display:flex; gap:8px; }
    .wm-cols div { flex:1; border-radius:6px; }
    @media(max-width:1024px){ .works-page-grid{grid-template-columns:repeat(2,1fr);} }
    @media(max-width:600px){ .works-page-grid{grid-template-columns:1fr;} }
</style>
@endpush

@section('content')
<section class="page-hero" style="padding-bottom:0">
    <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
    <div class="container" style="padding:100px 24px 60px; text-align:center; position:relative">
      <div data-aos="fade-up">
        <div class="breadcrumb" style="justify-content:center"><a href="{{ route('home') }}">Home</a><span>/</span><span class="bc-current">Our Works</span></div>
        <div class="hero-badge" style="margin:16px auto"><span class="badge-dot"></span>Portfolio</div>
        <h1 class="page-hero-title">Projects We're <span class="text-gradient">Proud Of</span></h1>
        <p class="page-hero-sub" style="margin:0 auto 36px;max-width:540px">200+ projects delivered across WordPress, eCommerce, cloud infrastructure, and automation — for clients across 3 continents.</p>
      </div>
    </div>
    <div class="trust-bar"><div class="container trust-inner">
      <div class="trust-item"><span class="trust-num">200+</span><span class="trust-label">Projects Delivered</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">50+</span><span class="trust-label">Enterprise Clients</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">3</span><span class="trust-label">Continents</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">10+</span><span class="trust-label">Years Active</span></div>
    </div></div>
  </section>

  <section class="section" id="portfolio">
    <div class="container">
      <div class="works-filter" data-aos="fade-up">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="wordpress">WordPress</button>
        <button class="filter-btn" data-filter="ecommerce">eCommerce</button>
        <button class="filter-btn" data-filter="cloud">Cloud</button>
        <button class="filter-btn" data-filter="automation">Automation</button>
        <button class="filter-btn" data-filter="seo">SEO</button>
      </div>
      @php
        $wpGradients = [
          'linear-gradient(135deg,#0a0a1a,#1a1a3e)',
          'linear-gradient(135deg,#0f2027,#203a43)',
          'linear-gradient(135deg,#0d0d0d,#1a1a2e)',
          'linear-gradient(135deg,#200122,#6f0000)',
          'linear-gradient(135deg,#0d1a0d,#1a3a1a)',
          'linear-gradient(135deg,#0f0c29,#302b63)',
          'linear-gradient(135deg,#1a0a0a,#2e1a1a)',
          'linear-gradient(135deg,#0a1a0a,#0f3a0f)',
          'linear-gradient(135deg,#1a1a0a,#3a3a0f)',
        ];
      @endphp
      <div class="works-page-grid" id="portfolioGrid">
        @foreach($portfolioItems as $item)
        <div class="wp-card" data-category="{{ $item->category }}" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 60 }}">
          <div class="wp-img" style="background:{{ $wpGradients[$loop->index % count($wpGradients)] }}">
            <div class="wp-mock">
              <div class="wm-bar"></div>
              <div class="wm-hero" style="height:50px;background:rgba(255,121,0,0.12)"></div>
            </div>
            <div class="wp-overlay"><span class="wp-view">View Details</span></div>
          </div>
          <div class="wp-info">
            <div class="wp-tags">
              @foreach($item->tags as $tag)
              <span class="wp-tag">{{ $tag }}</span>
              @endforeach
            </div>
            <p class="wp-title">{{ $item->title }}</p>
            <p class="wp-client">{{ $item->client }}</p>
          </div>
        </div>
        @endforeach
      </div>

      <div style="text-align:center" data-aos="fade-up">
        <a href="{{ route('home') }}#contact" class="btn btn-primary btn-lg">Start Your Project</a>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
