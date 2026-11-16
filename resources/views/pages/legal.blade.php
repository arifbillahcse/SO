@extends('layouts.app')

@section('title', $legalPage['title'].' | Softorio')
@section('description', $legalPage['intro'])
@section('breadcrumbs', json_encode([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => $legalPage['title'], 'url' => route($legalRoute)],
]))

@push('head')
@include('partials.case-study-styles')
<style>
.legal-hero{padding-bottom:46px}
.legal-layout{display:grid;grid-template-columns:210px minmax(0,1fr);gap:56px;align-items:start;max-width:1040px;margin:auto}
.legal-nav{position:sticky;top:110px;display:grid;gap:8px}
.legal-nav a{padding:14px 16px;border:1px solid var(--border);border-radius:12px;color:var(--text-secondary);font-size:.9rem;transition:background .2s,color .2s}
.legal-nav a[aria-current="page"]{background:var(--primary-glow);color:var(--primary);border-color:var(--primary)}
.legal-nav a:hover{color:var(--primary)}
.legal-body{min-width:0;color:var(--text-secondary);font-size:1rem;line-height:1.9;overflow-wrap:anywhere}
.legal-body h2{color:var(--text-primary);font-size:1.4rem;line-height:1.4;margin:38px 0 14px}
.legal-body h2:first-child{margin-top:0}
.legal-body h3{color:var(--text-primary);font-size:1.15rem;margin:26px 0 12px}
.legal-body p,.legal-body ul,.legal-body ol{margin-bottom:18px}
.legal-body ul,.legal-body ol{padding-left:24px}
.legal-body a{color:var(--primary);text-decoration:underline}
.legal-body blockquote{border-left:3px solid var(--primary);padding:12px 20px;margin:24px 0;background:var(--dark-2);border-radius:0 12px 12px 0}
.legal-contact{border-top:1px solid var(--border);margin-top:36px;padding-top:24px}
.legal-date{color:var(--text-muted);font-size:.85rem;margin-top:22px}
@media(max-width:768px){.legal-layout{grid-template-columns:1fr;gap:32px}.legal-nav{position:static;grid-template-columns:1fr 1fr}.legal-nav a{text-align:center;font-size:.8rem;padding:12px 8px}.legal-hero{padding-bottom:30px}}
</style>
@endpush

@section('content')
<main class="case-detail">
  <section class="case-hero legal-hero">
    <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
    <div class="container" style="text-align:center;position:relative">
      <div class="breadcrumb" style="justify-content:center">
        <a href="{{ route('home') }}">Home</a><span>/</span><span class="bc-current">{{ $legalPage['title'] }}</span>
      </div>
      <span class="hero-badge" style="margin:16px auto"><span class="badge-dot"></span>Softorio policies</span>
      <h1 class="page-hero-title">{{ $legalPage['title'] }}</h1>
      <p class="case-lead" style="margin:20px auto 0">{{ $legalPage['intro'] }}</p>
      <p class="legal-date">Effective date: <time datetime="{{ $legalPage['effective_date'] }}">{{ \Illuminate\Support\Carbon::parse($legalPage['effective_date'])->format('F j, Y') }}</time></p>
    </div>
  </section>
  <section class="section">
    <div class="container legal-layout">
      <nav class="legal-nav" aria-label="Legal pages">
        <a href="{{ route('tos') }}" @if($legalRoute === 'tos') aria-current="page" @endif>Terms &amp; Conditions</a>
        <a href="{{ route('privacy') }}" @if($legalRoute === 'privacy') aria-current="page" @endif>Privacy Policy</a>
      </nav>
      <article class="legal-body">
        {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($legalPage['body']) }}
        <div class="legal-contact"><p>Have a question about this policy? <a href="{{ route('contact') }}">Contact Softorio</a>.</p></div>
      </article>
    </div>
  </section>
</main>
@endsection
