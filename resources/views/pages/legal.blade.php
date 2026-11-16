@extends('layouts.app')

@section('title', $legalPage['title'].' | Softorio')
@section('description', $legalPage['intro'])
@section('breadcrumbs', json_encode([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => $legalPage['title'], 'url' => route($legalRoute)],
]))

@push('head')
<style>
.legal-page{padding:128px 0 80px}
.legal-shell{width:100%;max-width:940px;padding:0 24px;margin:0 auto}
.legal-page .breadcrumb{display:flex;align-items:center;justify-content:flex-start;flex-wrap:wrap;gap:10px;margin:0 0 22px;font-size:.78rem;line-height:1.6;color:var(--text-muted)}
.legal-page .breadcrumb a{color:var(--text-secondary)}
.legal-page .breadcrumb a:hover{color:var(--primary)}
.legal-page .bc-current{color:var(--text-secondary)}
.legal-paper{position:relative;overflow:hidden;background:var(--dark-3);border:1px solid var(--border);border-radius:24px;box-shadow:0 16px 60px rgba(0,0,0,.035)}
.legal-paper::before{content:"";position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#ff7900,rgba(255,121,0,.1))}
.legal-heading{padding:42px 52px 30px;border-bottom:1px solid var(--border)}
.legal-kicker{display:flex;align-items:center;gap:10px;color:var(--primary);font-size:.72rem;font-weight:600;letter-spacing:.09em;text-transform:uppercase}
.legal-kicker svg{width:28px;height:28px;padding:5px;background:var(--primary-glow);border-radius:8px}
.legal-heading h1{color:var(--text-primary);font-size:clamp(2rem,3.7vw,2.8rem);font-weight:750;line-height:1.18;letter-spacing:-.04em;margin:20px 0 14px;overflow-wrap:anywhere}
.legal-intro{max-width:700px;font-size:1rem;line-height:1.8;color:var(--text-secondary);margin:0}
.legal-date{display:flex;align-items:center;flex-wrap:wrap;gap:7px;color:var(--text-muted);font-size:.78rem;line-height:1.6;margin-top:22px}
.legal-date svg{width:15px;height:15px;flex-shrink:0}
.legal-date time{color:var(--text-secondary)}
.legal-body{padding:32px 52px 40px;min-width:0;color:var(--text-secondary);font-size:.96rem;line-height:1.9;overflow-wrap:anywhere}
.legal-body h2{color:var(--text-primary);font-size:1.18rem;font-weight:650;line-height:1.45;letter-spacing:-.015em;margin:30px 0 12px;padding-top:26px;border-top:1px solid var(--border)}
.legal-body h2:first-child{margin-top:0;padding-top:0;border-top:0}
.legal-body h3{color:var(--text-primary);font-size:1.05rem;line-height:1.5;margin:24px 0 12px}
.legal-body p,.legal-body ul,.legal-body ol{margin:0 0 16px}
.legal-body li{margin-bottom:8px}
.legal-body ul,.legal-body ol{padding-left:24px}
.legal-body a,.legal-contact a{color:var(--primary);text-decoration:underline;text-underline-offset:4px}
.legal-body blockquote{border-left:3px solid var(--primary);padding:12px 20px;margin:24px 0;background:var(--dark-2);border-radius:0 12px 12px 0}
.legal-body>:last-child{margin-bottom:0}
.legal-contact{display:flex;align-items:flex-start;gap:12px;padding:22px 52px;background:var(--dark-2);border-top:1px solid var(--border);color:var(--text-secondary);font-size:.86rem;line-height:1.8}
.legal-contact>svg{color:var(--primary);width:20px;height:20px;flex-shrink:0;margin-top:3px}
.legal-contact p{margin:0}
.legal-contact strong{display:block;color:var(--text-primary);font-weight:600;margin-bottom:2px}
.legal-page a:focus-visible{outline:2px solid var(--primary);outline-offset:4px}
@media(max-width:600px){
.legal-page{padding:102px 0 48px}
.legal-shell{padding:0 16px}
.legal-page .breadcrumb{font-size:.73rem;margin-bottom:16px}
.legal-paper{border-radius:18px}
.legal-heading{padding:30px 24px 24px}
.legal-heading h1{font-size:2rem;margin-top:18px}
.legal-intro{font-size:.92rem}
.legal-date{margin-top:18px;font-size:.73rem;gap:6px}
.legal-body{padding:26px 24px 30px;font-size:.92rem;line-height:1.85}
.legal-body h2{font-size:1.1rem;margin-top:26px;padding-top:22px}
.legal-contact{padding:20px 24px;font-size:.82rem}
}
@media(prefers-reduced-motion:reduce){.legal-page *{scroll-behavior:auto}}
</style>
@endpush

@section('content')
<main class="legal-page">
  <div class="legal-shell">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span class="bc-current" aria-current="page">{{ $legalPage['title'] }}</span>
    </nav>
    <div class="legal-paper">
      <header class="legal-heading">
        <div class="legal-kicker">
          <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 8 3v6c0 4-4 7-8 9-4-2-8-5-8-9V6l8-3Z"/><path d="m8.5 12 2.5 2.5 4.5-5"/></svg>
          Softorio legal
        </div>
        <h1>{{ $legalPage['title'] }}</h1>
        <p class="legal-intro">{{ $legalPage['intro'] }}</p>
        <p class="legal-date">
          <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4m10-4v4M3 11h18"/></svg>
          <span>Effective date:</span> <time datetime="{{ $legalPage['effective_date'] }}">{{ \Illuminate\Support\Carbon::parse($legalPage['effective_date'])->format('F j, Y') }}</time>
        </p>
      </header>
      <article class="legal-body">
        {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($legalPage['body']) }}
      </article>
      <div class="legal-contact">
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5H4l-2 2v-10a9.5 9.5 0 1 1 19 0Z"/><path d="M7 9h9M7 13h6"/></svg>
        <p><strong>Need clarification?</strong>Have a question about this policy? <a href="{{ route('contact') }}">Contact Softorio</a>.</p>
      </div>
    </div>
  </div>
</main>
@endsection
