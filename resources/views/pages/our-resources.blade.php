@extends('layouts.app')

@section('title', 'Our Resources — Softorio')

@push('head')
<style>
.res-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
    .res-card { background:var(--dark-2); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; transition:var(--transition); }
    .res-card:hover { transform:translateY(-6px); box-shadow:var(--shadow-card); border-color:rgba(255,121,0,0.3); }
    .res-thumb { height:180px; display:flex; align-items:center; justify-content:center; position:relative; }
    .res-cat { position:absolute; top:14px; left:14px; font-size:0.7rem; font-weight:700; color:#fff; background:var(--primary); padding:3px 12px; border-radius:100px; letter-spacing:0.05em; }
    .res-body { padding:22px; }
    .res-meta { font-size:0.75rem; color:var(--text-muted); font-weight:500; margin-bottom:10px; }
    .res-title { font-size:1rem; font-weight:700; color:var(--text-primary); margin-bottom:8px; line-height:1.4; }
    .res-desc { font-size:0.87rem; color:var(--text-secondary); line-height:1.6; margin-bottom:16px; }
    .res-read { font-size:0.85rem; font-weight:700; color:var(--primary); transition:var(--transition); }
    .res-read:hover { letter-spacing:0.03em; }
    .res-featured { background:linear-gradient(135deg,rgba(255,121,0,0.08),var(--dark-2)); border:1px solid rgba(255,121,0,0.25); border-radius:var(--radius-xl); padding:40px; display:grid; grid-template-columns:1.2fr 1fr; gap:40px; align-items:center; margin-bottom:56px; }
    .cat-filter { display:flex; gap:10px; flex-wrap:wrap; justify-content:center; margin-bottom:48px; }
    .cat-btn { padding:7px 20px; font-size:0.85rem; font-weight:600; font-family:var(--font); border:1.5px solid var(--border); background:transparent; color:var(--text-secondary); border-radius:100px; cursor:pointer; transition:var(--transition); }
    .cat-btn.active, .cat-btn:hover { background:var(--primary); border-color:var(--primary); color:#fff; }
    @media(max-width:1024px){ .res-featured{grid-template-columns:1fr;} .res-grid{grid-template-columns:repeat(2,1fr);} }
    @media(max-width:600px){ .res-grid{grid-template-columns:1fr;} }
</style>
@endpush

@section('content')
<section class="page-hero" style="padding-bottom:0">
    <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
    <div class="container" style="padding:100px 24px 60px;text-align:center;position:relative" data-aos="fade-up">
      <div class="breadcrumb" style="justify-content:center"><a href="{{ route('home') }}">Home</a><span>/</span><a href="#">Resources</a><span>/</span><span class="bc-current">Our Resources</span></div>
      <div class="hero-badge" style="margin:16px auto"><span class="badge-dot"></span>Knowledge Base</div>
      <h1 class="page-hero-title">Guides, Tips & <span class="text-gradient">Industry Insights</span></h1>
      <p class="page-hero-sub" style="margin:0 auto;max-width:540px">Free resources from the Softorio team — WordPress guides, SEO tips, cloud infrastructure tutorials, and business automation insights.</p>
    </div>
    <div class="trust-bar"><div class="container trust-inner">
      <div class="trust-item"><span class="trust-num">20+</span><span class="trust-label">Free Guides</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">WordPress</span><span class="trust-label">Tutorials</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">SEO</span><span class="trust-label">Checklists</span></div><div class="trust-sep"></div>
      <div class="trust-item"><span class="trust-num">Cloud</span><span class="trust-label">Setup Guides</span></div>
    </div></div>
  </section>

  <section class="section">
    <div class="container">

      @if($featuredArticle)
      <!-- Featured Article -->
      <div class="res-featured" data-aos="fade-up">
        <div>
          <span class="section-tag">Featured Guide</span>
          <h2 class="section-title" style="text-align:left;margin-top:12px;font-size:clamp(1.5rem,3vw,2rem)">{{ $featuredArticle->title }}</h2>
          <p style="font-size:0.95rem;color:var(--text-secondary);line-height:1.7;margin:14px 0 24px">{{ $featuredArticle->excerpt }}</p>
          <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:24px">
            <span style="font-size:0.78rem;color:var(--text-muted);font-weight:500">&#128336; {{ $featuredArticle->read_minutes }} min read</span>
            <span style="font-size:0.78rem;color:var(--text-muted);font-weight:500">&#128197; {{ $featuredArticle->published_at?->format('F Y') }}</span>
            <span style="font-size:0.78rem;color:var(--primary);font-weight:600;background:var(--primary-glow);padding:2px 10px;border-radius:100px">{{ $featuredArticle->category }}</span>
          </div>
          <a href="#" class="btn btn-primary">Read Full Guide</a>
        </div>
        <div style="height:240px;background:linear-gradient(135deg,#0a1a2e,#1a2e3e);border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;border:1px solid var(--border)">
          <div style="width:80%">
            <div style="height:8px;background:rgba(255,255,255,0.1);border-radius:4px;margin-bottom:10px"></div>
            <div style="height:50px;background:rgba(255,121,0,0.1);border-radius:8px;margin-bottom:10px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,121,0,0.2)">
              <span style="font-size:0.7rem;color:var(--primary);font-weight:700">PageSpeed Score: 99</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px"><div style="height:24px;background:rgba(34,197,94,0.2);border-radius:4px"></div><div style="height:24px;background:rgba(255,255,255,0.05);border-radius:4px"></div><div style="height:24px;background:rgba(255,255,255,0.05);border-radius:4px"></div></div>
          </div>
        </div>
      </div>
      @endif

      <!-- Category Filter -->
      <div class="cat-filter" data-aos="fade-up">
        <button class="cat-btn active">All</button>
        <button class="cat-btn">WordPress</button>
        <button class="cat-btn">SEO</button>
        <button class="cat-btn">Hosting & Cloud</button>
        <button class="cat-btn">Automation</button>
        <button class="cat-btn">eCommerce</button>
      </div>

      @php
        $resGradients = [
          'linear-gradient(135deg,#0a1a0a,#1a2e1a)',
          'linear-gradient(135deg,#1a0a0a,#2e1a0a)',
          'linear-gradient(135deg,#0a0a1a,#1a1a3e)',
          'linear-gradient(135deg,#0f0c29,#302b63)',
          'linear-gradient(135deg,#0f2027,#203a43)',
          'linear-gradient(135deg,#1a1a0a,#2e2e0a)',
        ];
      @endphp
      <!-- Articles Grid -->
      <div class="res-grid">
        @foreach($articles as $article)
        <div class="res-card" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
          <div class="res-thumb" style="background:{{ $resGradients[$loop->index % count($resGradients)] }}">
            <span class="res-cat">{{ $article->category }}</span>
            <div style="width:70%;display:flex;flex-direction:column;gap:8px"><div style="height:8px;background:rgba(255,255,255,0.1);border-radius:4px"></div><div style="height:6px;background:rgba(255,255,255,0.06);border-radius:3px;width:80%"></div><div style="height:6px;background:rgba(255,255,255,0.05);border-radius:3px;width:65%"></div></div>
          </div>
          <div class="res-body">
            <div class="res-meta">&#128336; {{ $article->read_minutes }} min read &nbsp;|&nbsp; {{ $article->category }}</div>
            <p class="res-title">{{ $article->title }}</p>
            <p class="res-desc">{{ $article->excerpt }}</p>
            <a href="#" class="res-read">Read Article &rarr;</a>
          </div>
        </div>
        @endforeach
      </div>

      <div style="text-align:center;margin-top:48px" data-aos="fade-up">
        <a href="#" class="btn btn-outline btn-lg">Load More Articles</a>
      </div>
    </div>
  </section>

  <!-- NEWSLETTER -->
  <section class="section" style="background:var(--dark-2);padding:80px 0">
    <div class="container" style="max-width:600px;text-align:center" data-aos="fade-up">
      <span class="section-tag">Newsletter</span>
      <h2 class="section-title" style="margin-top:14px">Get Tips Straight to <span class="text-gradient">Your Inbox</span></h2>
      <p class="section-sub" style="margin:0 auto 32px">Weekly insights on WordPress, SEO, and digital growth. No spam. Unsubscribe anytime.</p>
      <div style="display:flex;gap:12px;max-width:420px;margin:0 auto;flex-wrap:wrap">
        <input type="email" class="cta-input" placeholder="Your email address" style="flex:1;min-width:200px" />
        <button class="btn btn-primary" style="white-space:nowrap">Subscribe Free</button>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  @vite(['resources/js/service.js'])
@endpush
