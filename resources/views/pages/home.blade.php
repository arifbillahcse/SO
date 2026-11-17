@extends('layouts.app')

@section('title', 'Softorio — Website Development, Cloud & SEO Agency')
@section('description', 'Softorio builds fast, scalable websites and manages cloud infrastructure, SEO, and automation — so you can focus on growing your business.')

@push('head')
@include('partials.hero-service-forge-styles')
@include('partials.about-styles')
@include('partials.portfolio-card-styles')
@endpush

@section('content')
<!-- ========== HERO ========== -->
  <section class="hero" id="home">
    <div class="hero-bg">
      <div class="hero-orb orb-1"></div>
      <div class="hero-orb orb-2"></div>
      <div class="hero-grid"></div>
    </div>
    <div class="container hero-inner">
      <div class="hero-content" data-aos="fade-up">
        <div class="hero-badge">
          <span class="badge-dot"></span>
          Website, Cloud &amp; SEO Services
        </div>
        <h1 class="hero-title">
          Your Vision,<br />
          <span class="text-gradient">Our Innovation</span>
        </h1>
        <p class="hero-subtitle">
          Softorio drives digital transformation. We build fast, scalable, and stunning websites — so you can focus on growing your business.
        </p>
        <div class="hero-cta">
          <a href="#services" class="btn btn-primary btn-lg">Explore Services</a>
          <a href="#works" class="btn btn-outline btn-lg">View Our Work</a>
        </div>
        <div class="hero-stats">
          <div class="stat">
            <span class="stat-num" data-count="200">0</span><span class="stat-plus">+</span>
            <span class="stat-label">Projects Done</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat">
            <span class="stat-num" data-count="10">0</span><span class="stat-plus">+</span>
            <span class="stat-label">Years Experience</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat">
            <span class="stat-num" data-count="98">0</span><span class="stat-plus">%</span>
            <span class="stat-label">Client Satisfaction</span>
          </div>
        </div>
      </div>
      <div class="hero-visual" data-aos="fade-left">
        @include('partials.hero-service-forge')
      </div>
    </div>
    <div class="hero-scroll-hint">
      <span>Scroll down</span>
      <div class="scroll-line"></div>
    </div>
  </section>

  @if($clientBrands->isNotEmpty())
  <section class="clients-section" aria-labelledby="client-brands-label">
    <div class="container">
      <p class="clients-label" id="client-brands-label">Trusted by leading brands worldwide</p>
    </div>
    <div class="marquee-wrapper">
      <div class="marquee-track">
        @foreach([false, true] as $duplicate)
        <div class="marquee-group" @if($duplicate) aria-hidden="true" @endif>
          @foreach($clientBrands as $brand)
          @php($brandLogoUrl = $brand->logoUrl())
          <span class="client-logo" data-client-brand="{{ $brand->id }}">
            @if($brandLogoUrl)
            <img class="client-brand-image" src="{{ $brandLogoUrl }}" alt="{{ $brand->name }}" width="144" height="60" decoding="async">
            @endif
            <span @if($brandLogoUrl) hidden @endif>{{ $brand->name }}</span>
          </span>
          @endforeach
        </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <!-- ========== SERVICES ========== -->
  <section class="services-section section" id="services">
    <div class="container">
      <div class="section-header" data-aos="fade-up">
        <span class="section-tag">What We Do</span>
        <h2 class="section-title">Services Built for <span class="text-gradient">Growth</span></h2>
        <p class="section-sub">From concept to launch and beyond — we handle every layer of your digital presence.</p>
      </div>
      <div class="services-grid">
        @foreach($services as $i => $service)
        <div class="service-card{{ $service->featured ? ' featured' : '' }}" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
          <div class="service-icon-wrap{{ $service->featured ? ' orange' : '' }}">
            @switch($service->icon)
              @case('code')
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                @break
              @case('search')
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><path d="M11 8v6M8 11h6"/></svg>
                @break
              @case('cloud')
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
                @break
              @case('gear')
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a10 10 0 1 0 10 10"/><path d="M12 8v4l3 3"/><circle cx="19" cy="5" r="3"/></svg>
                @break
              @case('medical')
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="3" width="16" height="18" rx="3"/><path d="M12 7v6m-3-3h6M8 17h8"/></svg>
                @break
            @endswitch
          </div>
          <h3 class="service-name">{{ $service->title }}</h3>
          <p class="service-desc">{{ $service->short_description }}</p>
          <ul class="service-list">
            @foreach($service->features ?? [] as $feature)
            <li>{{ $feature }}</li>
            @endforeach
          </ul>
          <a href="{{ $service->publicUrl() ?: '#contact' }}" class="service-link">Learn More &rarr;</a>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  @include('partials.work-approach')

  <!-- ========== OUR WORKS ========== -->
  <section class="works-section section" id="works" aria-labelledby="home-works-title">
    <div class="container">
      <div class="section-header" data-aos="fade-up">
        <span class="section-tag">Portfolio</span>
        <h2 class="section-title" id="home-works-title">Our <span class="text-gradient">Selected Work</span></h2>
        <p class="section-sub">Website projects, design demonstrations and technical work from our portfolio.</p>
      </div>
      @if($homePortfolioItems->pluck('category')->unique()->count() > 1)
      <div class="works-filter" data-home-portfolio-filters role="group" aria-label="Filter featured portfolio projects">
        <button type="button" class="filter-btn active" data-filter="all" aria-pressed="true">All projects</button>
        @foreach(\App\Models\PortfolioItem::CATEGORY_OPTIONS as $category => $label)
          @if($homePortfolioItems->contains('category', $category))
          <button type="button" class="filter-btn" data-filter="{{ $category }}" aria-pressed="false">{{ $label }}</button>
          @endif
        @endforeach
      </div>
      @endif
      @if($homePortfolioItems->isNotEmpty())
      <div class="works-grid">
        @foreach($homePortfolioItems as $item)
        <x-portfolio-card :project="$item" :delay="($loop->index % 3) * 60" heading="h3" class="work-card" />
        @endforeach
      </div>
      @else
      <p class="portfolio-note" style="text-align:center;color:var(--text-secondary);margin-bottom:28px">New work will appear here as our portfolio grows.</p>
      @endif
      <div class="works-cta" data-aos="fade-up">
        <a href="{{ route('our-works') }}" class="btn btn-primary btn-lg">View all projects <span aria-hidden="true">&nearr;</span></a>
      </div>
    </div>
  </section>

  @include('partials.client-map')

  <!-- ========== TESTIMONIALS ========== -->
  @if($testimonials->isNotEmpty())
  <section class="testimonials-section section" id="testimonials" aria-labelledby="testimonials-title">
    <div class="container">
      <div class="testi-header">
        <div class="testi-header-text">
          <span class="section-tag">Testimonials</span>
          <h2 class="section-title" id="testimonials-title">Let Our Clients <span class="text-gradient">Speak</span></h2>
          <p class="section-sub" style="margin:0">Feedback shared by our customers.</p>
        </div>
      </div>

      <div class="testi-carousel">
        <div class="testi-viewport" id="testiViewport">
          <div class="testi-track" id="testiTrack">
            @foreach($testimonials as $testimonial)
            <div class="testi-card">
              <svg class="testi-quote" width="34" height="26" viewBox="0 0 34 26" fill="none"><path d="M0 26V15.6C0 10.6 1.1 6.9 3.4 4.5C5.7 2.1 8.9 0.6 13 0V5.4C10.9 5.9 9.3 6.8 8.2 8.1C7.1 9.4 6.5 11.1 6.4 13.2H13V26H0ZM21 26V15.6C21 10.6 22.1 6.9 24.4 4.5C26.7 2.1 29.9 0.6 34 0V5.4C31.9 5.9 30.3 6.8 29.2 8.1C28.1 9.4 27.5 11.1 27.4 13.2H34V26H21Z" fill="currentColor"/></svg>
              <div class="testi-stars" aria-label="{{ max(1, min(5, $testimonial->stars)) }} out of 5 stars">{!! str_repeat('&#9733;', max(1, min(5, $testimonial->stars))) !!}</div>
              <p class="testi-text">{{ $testimonial->text }}</p>
              <div class="testi-author">
                <div class="testi-avatar">{{ $testimonial->avatar_initial }}</div>
                <div>
                  <div class="testi-name">{{ $testimonial->name }}</div>
                  <div class="testi-role">{{ $testimonial->role }}</div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <button class="testi-btn testi-btn-prev" id="testiPrev" type="button" aria-label="Previous reviews" hidden>&#8592;</button>
        <button class="testi-btn testi-btn-next" id="testiNext" type="button" aria-label="Next reviews" hidden>&#8594;</button>
      </div>
      <div class="testi-dots" id="testiDots"></div>
    </div>
  </section>
  @endif

  <!-- ========== CTA BANNER ========== -->
  <section class="cta-section" id="contact">
    <div class="cta-bg">
      <div class="cta-orb"></div>
    </div>
    <div class="container cta-inner" data-aos="fade-up">
      <div class="cta-content">
        <h2 class="cta-title">Ready to Build Something <span>Amazing?</span></h2>
        <p class="cta-sub">Tell us about your project. We'll review your requirements and discuss a practical next step.</p>
        <div class="cta-info">
          <div class="info-block">
            <div class="info-icon">&#128222;</div>
            <div>
              <div class="info-label">Phone</div>
              <div class="info-val">@if($siteContact['telephone_url'])<a href="{{ $siteContact['telephone_url'] }}">{{ $siteContact['phone'] }}</a>@else<a href="{{ route('contact') }}">Contact us</a>@endif</div>
            </div>
          </div>
          <div class="info-block">
            <div class="info-icon">&#9993;</div>
            <div>
              <div class="info-label">Email</div>
              <div class="info-val">@if($siteContact['email'])<a href="mailto:{{ $siteContact['email'] }}">{{ $siteContact['email'] }}</a>@else<a href="{{ route('contact') }}">Contact us</a>@endif</div>
            </div>
          </div>
          @if(count($siteContact['social_links']))
          <div class="cta-socials">@foreach($siteContact['social_links'] as $label => $url)<a href="{{ $url }}" class="social-btn" aria-label="{{ $label }}" target="_blank" rel="noopener noreferrer">{{ ['Facebook' => 'f', 'X / Twitter' => 'x', 'LinkedIn' => 'in', 'YouTube' => 'yt'][$label] }}</a>@endforeach</div>
          @endif
        </div>
      </div>
      <div class="cta-form" data-endpoint="{{ route('contact.store') }}">
        <input type="text" name="name" maxlength="255" aria-label="Your name" placeholder="Your Name" class="cta-input" />
        <input type="email" name="email" maxlength="255" aria-label="Email address" placeholder="Your Email" class="cta-input" />
        <input type="tel" name="phone" maxlength="50" aria-label="Phone number (optional)" placeholder="Phone Number" class="cta-input" />
        <select name="service_interested" aria-label="Service you are interested in" class="cta-input"><option value="" selected>Choose a service (optional)</option>@foreach($contactServiceOptions as $contactServiceOption)<option value="{{ $contactServiceOption->title }}" @selected(request()->routeIs($contactServiceOption->route_name))>{{ $contactServiceOption->title }}</option>@endforeach</select>
        <textarea name="message" maxlength="5000" aria-label="Your message" placeholder="Tell us about your project..." class="cta-input cta-textarea"></textarea>
        <input type="text" name="company_website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true" />
        <button class="btn btn-primary btn-lg cta-submit">Send Message</button>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  @vite(['resources/js/home.js'])
@endpush
