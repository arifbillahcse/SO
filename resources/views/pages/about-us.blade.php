@extends('layouts.app')

@section('title', 'About Softorio | Website, SEO & Cloud Services Team')
@section('description', 'Meet Softorio: a hands-on team connecting website development, cloud infrastructure, SEO and automation, from your first idea to launch and support.')
@section('breadcrumbs', json_encode([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'About Us', 'url' => route('about-us')],
]))

@push('head')
@include('partials.about-styles')
@endpush

@section('content')
<main class="about-page">
  <section class="ab-hero" aria-labelledby="about-title">
    <div class="ab-wrap">
      <nav class="ab-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">About Us</span></nav>
      <div class="ab-hero-grid">
        <div>
          <span class="ab-eyebrow">Meet Softorio</span>
          <h1 id="about-title">Your digital ideas.<br>A team to bring<br>them <em>together.</em></h1>
          <p class="ab-lead">Websites, cloud and smarter workflows. We connect the pieces so your next idea has a practical path from first sketch to everyday use.</p>
          <div class="ab-actions">
            <a href="{{ route('our-works') }}" class="btn btn-primary btn-lg">Explore our work <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
            <a href="#our-story" class="ab-text-link">The people behind it <span aria-hidden="true">&darr;</span></a>
          </div>
          <p class="ab-signoff">Your Vision, Our Innovation.</p>
        </div>
        <div class="ab-visual">@include('partials.about-workspace')</div>
      </div>
    </div>
  </section>

  <section class="ab-section ab-story" id="our-story" aria-labelledby="about-story-title">
    <div class="ab-wrap ab-story-grid">
      <div>
        <span class="ab-eyebrow">Who we are</span>
        <h2 class="ab-title" id="about-story-title">People who care<br>about the whole picture.</h2>
        <p class="ab-copy">Softorio brings web development and Linux systems experience into one hands-on team. We look at how your website, infrastructure and everyday tools work together.</p>
        <p class="ab-copy">Founded by {{ $aboutFounder?->name ?? 'Md. Arif Billah' }}, with a background in Computer Science &amp; Engineering, our approach is simple: understand the business, make thoughtful choices and stay close to the work.</p>
      </div>
      <div class="ab-founder">
        <div class="ab-founder-photo"><img src="{{ $aboutFounderPhoto }}" alt="{{ $aboutFounder?->name ?? 'Md. Arif Billah' }}, Softorio founder" width="300" height="300" loading="lazy" decoding="async"></div>
        <div class="ab-founder-info">
          <span class="ab-founder-label">The person behind Softorio</span>
          <h3>{{ $aboutFounder?->name ?? 'Md. Arif Billah' }}</h3>
          <p class="ab-founder-role">{{ $aboutFounder?->short_role ?: 'Founder & Web / Cloud Engineer' }}</p>
          <p class="ab-founder-note">A practical mindset.<br>A personal commitment.</p>
        </div>
      </div>
    </div>
  </section>

  @include('partials.client-map')

  <section class="ab-section ab-services" aria-labelledby="about-services-title">
    <div class="ab-wrap">
      <div class="ab-section-head">
        <div><span class="ab-eyebrow">What we do</span><h2 class="ab-title" id="about-services-title">Five services.<br>One connected team.</h2></div>
        <a href="{{ route('services') }}" class="ab-text-link">Explore all services <span aria-hidden="true">&nearr;</span></a>
      </div>
      <div class="ab-services-grid">
        <a href="{{ route('web-development') }}" class="ab-service">
          <span class="ab-service-icon"><x-service-icon group="code" width="24" height="24" /></span>
          <h3>Website development</h3><p>WordPress, WooCommerce and custom Laravel / PHP applications shaped around your business.</p>
          <span class="ab-service-bottom">Build your presence <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></span>
        </a>
        <a href="{{ route('seo') }}" class="ab-service">
          <span class="ab-service-icon"><x-service-icon group="search" width="24" height="24" /></span>
          <h3>SEO optimization</h3><p>Technical audits, on-page improvements and content planning for a stronger search foundation.</p>
          <span class="ab-service-bottom">Make your work discoverable <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></span>
        </a>
        <a href="{{ route('manage-cloud') }}" class="ab-service">
          <span class="ab-service-icon"><x-service-icon group="cloud" width="24" height="24" /></span>
          <h3>Managed cloud</h3><p>Linux servers, deployment, DNS, SSL and maintenance for the systems behind your website.</p>
          <span class="ab-service-bottom">Support your foundation <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></span>
        </a>
        <a href="{{ route('automation') }}" class="ab-service">
          <span class="ab-service-icon"><x-service-icon group="gear" width="24" height="24" /></span>
          <h3>Business automation</h3><p>API integrations, connected workflows and AI chatbots that reduce repetitive work.</p>
          <span class="ab-service-bottom">Connect your tools <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></span>
        </a>
        <a href="{{ route('diagnostic-management-system') }}" class="ab-service">
          <span class="ab-service-icon"><x-service-icon group="medical" width="24" height="24" /></span>
          <h3>Diagnostic Management System</h3><p>Software being developed for patient registration, billing, samples, results and reporting.</p>
          <span class="ab-service-bottom"><span class="ab-development">In development</span><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></span>
        </a>
      </div>
    </div>
  </section>

  <section class="ab-section" aria-labelledby="about-values-title">
    <div class="ab-wrap">
      <span class="ab-eyebrow">Why work with us</span>
      <h2 class="ab-title" id="about-values-title">Good work. Clear conversations.<br>No unnecessary complexity.</h2>
      <div class="ab-values-grid">
        <div class="ab-value"><span class="ab-value-number">01 / CONNECTED THINKING</span><h3>One partner, the full picture.</h3><p>Development, cloud, SEO and automation planned together, with fewer gaps between the people doing the work.</p></div>
        <div class="ab-value"><span class="ab-value-number">02 / DIRECT COMMUNICATION</span><h3>Talk to the people building it.</h3><p>Share questions, see progress and discuss decisions directly with the team responsible for your project.</p></div>
        <div class="ab-value"><span class="ab-value-number">03 / PRACTICAL CHOICES</span><h3>A scope you can understand.</h3><p>Agree on priorities, responsibilities and costs before building. Focus on what helps your business move forward.</p></div>
      </div>
    </div>
  </section>

  <section class="ab-section ab-process" aria-labelledby="about-process-title">
    <div class="ab-wrap">
      <div class="ab-process-panel">
        <div class="ab-process-heading"><div><span class="ab-eyebrow">How we work</span><h2 class="ab-title" id="about-process-title">From a conversation<br>to something useful.</h2></div><p>A clear path, with room to ask questions and make informed decisions along the way.</p></div>
        <div class="ab-steps">
          <div class="ab-step"><span class="ab-step-number">01</span><h3>Discover</h3><p>Understand your goals, audience and constraints.</p></div>
          <div class="ab-step"><span class="ab-step-number">02</span><h3>Plan</h3><p>Agree on the scope, approach and practical timeline.</p></div>
          <div class="ab-step"><span class="ab-step-number">03</span><h3>Build</h3><p>Develop in clear stages and share regular updates.</p></div>
          <div class="ab-step"><span class="ab-step-number">04</span><h3>Launch &amp; support</h3><p>Deploy, review and plan the support you need next.</p></div>
        </div>
      </div>
    </div>
  </section>

  <section class="ab-cta" aria-labelledby="about-contact-title">
    <div class="ab-wrap">
      <div class="ab-cta-panel">
        <div><h2 class="ab-title" id="about-contact-title">Your next idea<br>starts with a conversation.</h2><p>Tell us what you want to build, improve or simplify. We will help you find a practical next step.</p></div>
        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Get in touch <span aria-hidden="true">&nearr;</span></a>
      </div>
    </div>
  </section>
</main>
@endsection
