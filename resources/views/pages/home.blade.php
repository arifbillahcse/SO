@extends('layouts.app')

@section('title', 'Softorio — Your Vision, Our Innovation')

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
          WordPress VIP Gold Partner
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
        <div class="hero-card-stack">
          <div class="float-card card-perf">
            <div class="card-icon-sm">&#9889;</div>
            <div>
              <div class="card-title-sm">Page Speed</div>
              <div class="card-val">99 / 100</div>
            </div>
          </div>
          <div class="hero-browser">
            <div class="browser-bar">
              <span class="dot r"></span>
              <span class="dot y"></span>
              <span class="dot g"></span>
              <div class="browser-url">softorio.com</div>
            </div>
            <div class="browser-body">
              <div class="mock-hero-strip"></div>
              <div class="mock-cards">
                <div class="mock-card"></div>
                <div class="mock-card"></div>
                <div class="mock-card"></div>
              </div>
              <div class="mock-line"></div>
              <div class="mock-line short"></div>
            </div>
          </div>
          <div class="float-card card-seo">
            <div class="card-icon-sm">&#128200;</div>
            <div>
              <div class="card-title-sm">SEO Score</div>
              <div class="card-val green">Excellent</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="hero-scroll-hint">
      <span>Scroll down</span>
      <div class="scroll-line"></div>
    </div>
  </section>

  <!-- ========== CLIENTS MARQUEE ========== -->
  <section class="clients-section">
    <div class="container">
      <p class="clients-label">Trusted by leading brands worldwide</p>
    </div>
    <div class="marquee-wrapper">
      <div class="marquee-track">
        <span class="client-logo">Ask Media</span>
        <span class="client-logo">Tropicana</span>
        <span class="client-logo">Storyful</span>
        <span class="client-logo">PepsiCo</span>
        <span class="client-logo">NewsCorp</span>
        <span class="client-logo">HKP Group</span>
        <span class="client-logo">Scan Media</span>
        <span class="client-logo">DC Innovation</span>
        <span class="client-logo">MarketHive</span>
        <span class="client-logo">STEM Academy</span>
        <!-- duplicate for seamless loop -->
        <span class="client-logo">Ask Media</span>
        <span class="client-logo">Tropicana</span>
        <span class="client-logo">Storyful</span>
        <span class="client-logo">PepsiCo</span>
        <span class="client-logo">NewsCorp</span>
        <span class="client-logo">HKP Group</span>
        <span class="client-logo">Scan Media</span>
        <span class="client-logo">DC Innovation</span>
        <span class="client-logo">MarketHive</span>
        <span class="client-logo">STEM Academy</span>
      </div>
    </div>
  </section>

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
            @endswitch
          </div>
          <h3 class="service-name">{{ $service->title }}</h3>
          <p class="service-desc">{{ $service->short_description }}</p>
          <ul class="service-list">
            @foreach($service->features ?? [] as $feature)
            <li>{{ $feature }}</li>
            @endforeach
          </ul>
          <a href="#contact" class="service-link">Learn More &rarr;</a>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- ========== WHY CHOOSE US ========== -->
  <section class="why-section section">
    <div class="container why-inner">
      <div class="why-left" data-aos="fade-right">
        <span class="section-tag">Why Softorio</span>
        <h2 class="section-title">One Partner for Your<br/><span class="text-gradient">Entire Digital Stack</span></h2>
        <p class="section-sub">We don't just build websites. We build digital infrastructure that supports your business goals — from day one to scale.</p>
        <div class="why-features">
          <div class="why-feat">
            <div class="why-feat-icon">&#10003;</div>
            <div>
              <strong>10+ Years of Expertise</strong>
              <p>Deep experience in WordPress, eCommerce, and cloud infrastructure.</p>
            </div>
          </div>
          <div class="why-feat">
            <div class="why-feat-icon">&#10003;</div>
            <div>
              <strong>WordPress VIP Gold Partner</strong>
              <p>Recognized by Automattic for elite WordPress development standards.</p>
            </div>
          </div>
          <div class="why-feat">
            <div class="why-feat-icon">&#10003;</div>
            <div>
              <strong>End-to-End Services</strong>
              <p>Design, development, hosting, SEO, and automation — all under one roof.</p>
            </div>
          </div>
          <div class="why-feat">
            <div class="why-feat-icon">&#10003;</div>
            <div>
              <strong>Global Clients, Local Care</strong>
              <p>Serving businesses across North America, Europe, and Asia with personal attention.</p>
            </div>
          </div>
        </div>
        <a href="#contact" class="btn btn-primary">Start a Project</a>
      </div>
      <div class="why-right" data-aos="fade-left">
        <div class="why-metrics">
          <div class="metric-card">
            <div class="metric-num">13,768</div>
            <div class="metric-label">Hours Contributed to WordPress Core</div>
          </div>
          <div class="metric-card accent">
            <div class="metric-num">200+</div>
            <div class="metric-label">Projects Successfully Delivered</div>
          </div>
          <div class="metric-card">
            <div class="metric-num">3</div>
            <div class="metric-label">Continents Served</div>
          </div>
          <div class="metric-card accent">
            <div class="metric-num">99.9%</div>
            <div class="metric-label">Uptime Guarantee</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== SOLUTIONS ========== -->
  <section class="solutions-section section" id="solutions">
    <div class="container">
      <div class="section-header" data-aos="fade-up">
        <span class="section-tag">Solutions</span>
        <h2 class="section-title">Complete Solutions for <span class="text-gradient">Every Business</span></h2>
        <p class="section-sub">Packaged offerings that combine our services into focused, results-driven digital solutions.</p>
      </div>
      <div class="solutions-grid">

        <div class="solution-card" data-aos="fade-up" data-aos-delay="0">
          <div class="solution-num">01</div>
          <div class="solution-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          </div>
          <h3 class="solution-name">eCommerce Solution</h3>
          <p class="solution-desc">Launch and scale your online store with WooCommerce, secure payments, inventory management, and conversion-focused design.</p>
          <ul class="solution-features">
            <li>WooCommerce Setup</li>
            <li>Payment Gateway Integration</li>
            <li>Inventory Management</li>
            <li>Analytics & Reporting</li>
            <li>SEO & Content Marketing</li>
          </ul>
          <a href="#contact" class="btn btn-outline-sm">Get Started &rarr;</a>
        </div>

        <div class="solution-card highlight" data-aos="fade-up" data-aos-delay="150">
          <div class="solution-badge">Most Popular</div>
          <div class="solution-num">02</div>
          <div class="solution-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"/></svg>
          </div>
          <h3 class="solution-name">Cloud Solution</h3>
          <p class="solution-desc">Enterprise-grade cloud infrastructure — VPS deployment, firewalls, CDN, mail setup, and server management for total reliability.</p>
          <ul class="solution-features">
            <li>VPS/Dedicated Server</li>
            <li>Server Setup & Firewall</li>
            <li>Mail Server Configuration</li>
            <li>DNS Management</li>
            <li>Cron & Email Marketing</li>
          </ul>
          <a href="#contact" class="btn btn-primary-sm">Get Started &rarr;</a>
        </div>

        <div class="solution-card" data-aos="fade-up" data-aos-delay="300">
          <div class="solution-num">03</div>
          <div class="solution-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          </div>
          <h3 class="solution-name">Automation Solution</h3>
          <p class="solution-desc">Automate your business workflows, marketing pipelines, and repetitive tasks to save time and scale operations efficiently.</p>
          <ul class="solution-features">
            <li>Workflow Automation</li>
            <li>API & Tool Integration</li>
            <li>Email Sequence Automation</li>
            <li>CRM Pipeline Setup</li>
            <li>Reporting Automation</li>
          </ul>
          <a href="#contact" class="btn btn-outline-sm">Get Started &rarr;</a>
        </div>

      </div>
    </div>
  </section>

  <!-- ========== OUR WORKS ========== -->
  <section class="works-section section" id="works">
    <div class="container">
      <div class="section-header" data-aos="fade-up">
        <span class="section-tag">Portfolio</span>
        <h2 class="section-title">Our <span class="text-gradient">Recent Works</span></h2>
        <p class="section-sub">A snapshot of projects we've built — from eCommerce stores to enterprise platforms.</p>
      </div>
      <div class="works-filter" data-aos="fade-up">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="wordpress">WordPress</button>
        <button class="filter-btn" data-filter="ecommerce">eCommerce</button>
        <button class="filter-btn" data-filter="cloud">Cloud</button>
      </div>
      <div class="works-grid">
        <div class="work-card" data-category="wordpress" data-aos="fade-up" data-aos-delay="0">
          <div class="work-img" style="background:linear-gradient(135deg,#1a1a2e,#16213e)">
            <div class="work-mock">
              <div class="wm-bar"></div>
              <div class="wm-hero"></div>
              <div class="wm-cols"><div></div><div></div></div>
            </div>
          </div>
          <div class="work-info">
            <span class="work-tag">WordPress</span>
            <h4 class="work-title">Business Corporate Site</h4>
            <p class="work-client">Ask Media Group</p>
          </div>
        </div>
        <div class="work-card" data-category="ecommerce" data-aos="fade-up" data-aos-delay="100">
          <div class="work-img" style="background:linear-gradient(135deg,#0f2027,#203a43)">
            <div class="work-mock">
              <div class="wm-bar"></div>
              <div class="wm-products"><div></div><div></div><div></div></div>
            </div>
          </div>
          <div class="work-info">
            <span class="work-tag">eCommerce</span>
            <h4 class="work-title">Online Store — WooCommerce</h4>
            <p class="work-client">Tropicana (PepsiCo)</p>
          </div>
        </div>
        <div class="work-card" data-category="cloud" data-aos="fade-up" data-aos-delay="200">
          <div class="work-img" style="background:linear-gradient(135deg,#1a1a1a,#2d2d2d)">
            <div class="work-mock">
              <div class="wm-bar"></div>
              <div class="wm-dashboard">
                <div class="wm-metric"></div>
                <div class="wm-chart"></div>
              </div>
            </div>
          </div>
          <div class="work-info">
            <span class="work-tag">Cloud / VPS</span>
            <h4 class="work-title">Cloud Infrastructure Setup</h4>
            <p class="work-client">Storyful (NewsCorp)</p>
          </div>
        </div>
        <div class="work-card" data-category="wordpress" data-aos="fade-up" data-aos-delay="300">
          <div class="work-img" style="background:linear-gradient(135deg,#200122,#6f0000)">
            <div class="work-mock">
              <div class="wm-bar"></div>
              <div class="wm-hero"></div>
              <div class="wm-cols"><div></div><div></div></div>
            </div>
          </div>
          <div class="work-info">
            <span class="work-tag">WordPress</span>
            <h4 class="work-title">Publisher Platform</h4>
            <p class="work-client">Association of American Publishers</p>
          </div>
        </div>
        <div class="work-card" data-category="ecommerce" data-aos="fade-up" data-aos-delay="400">
          <div class="work-img" style="background:linear-gradient(135deg,#0d0d0d,#1a3a1a)">
            <div class="work-mock">
              <div class="wm-bar"></div>
              <div class="wm-products"><div></div><div></div><div></div></div>
            </div>
          </div>
          <div class="work-info">
            <span class="work-tag">eCommerce</span>
            <h4 class="work-title">Multi-Vendor Marketplace</h4>
            <p class="work-client">DC Innovation</p>
          </div>
        </div>
        <div class="work-card" data-category="cloud" data-aos="fade-up" data-aos-delay="500">
          <div class="work-img" style="background:linear-gradient(135deg,#0f0c29,#302b63)">
            <div class="work-mock">
              <div class="wm-bar"></div>
              <div class="wm-dashboard">
                <div class="wm-metric"></div>
                <div class="wm-chart"></div>
              </div>
            </div>
          </div>
          <div class="work-info">
            <span class="work-tag">Automation</span>
            <h4 class="work-title">Workflow Automation System</h4>
            <p class="work-client">MarketHive</p>
          </div>
        </div>
      </div>
      <div class="works-cta" data-aos="fade-up">
        <a href="#contact" class="btn btn-primary btn-lg">Start Your Project</a>
      </div>
    </div>
  </section>

  <!-- ========== TESTIMONIALS ========== -->
  <section class="testimonials-section section">
    <div class="container">
      <div class="testi-header" data-aos="fade-up">
        <div class="testi-header-text">
          <span class="section-tag">Testimonials</span>
          <h2 class="section-title">Let Our Clients <span class="text-gradient">Speak</span></h2>
          <p class="section-sub" style="margin:0">Real feedback from businesses we've helped build, host, and grow online.</p>
        </div>
        <div class="testi-rating">
          <div class="testi-rating-score">4.9<span>/5</span></div>
          <div class="testi-rating-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <div class="testi-rating-label">From 120+ client reviews</div>
        </div>
      </div>

      <div class="testi-carousel" data-aos="fade-up">
        <div class="testi-viewport" id="testiViewport">
          <div class="testi-track" id="testiTrack">
            @foreach($testimonials as $testimonial)
            <div class="testi-card">
              <svg class="testi-quote" width="34" height="26" viewBox="0 0 34 26" fill="none"><path d="M0 26V15.6C0 10.6 1.1 6.9 3.4 4.5C5.7 2.1 8.9 0.6 13 0V5.4C10.9 5.9 9.3 6.8 8.2 8.1C7.1 9.4 6.5 11.1 6.4 13.2H13V26H0ZM21 26V15.6C21 10.6 22.1 6.9 24.4 4.5C26.7 2.1 29.9 0.6 34 0V5.4C31.9 5.9 30.3 6.8 29.2 8.1C28.1 9.4 27.5 11.1 27.4 13.2H34V26H21Z" fill="currentColor"/></svg>
              <div class="testi-stars">{!! str_repeat('&#9733;', $testimonial->stars) !!}</div>
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
        <button class="testi-btn testi-btn-prev" id="testiPrev" aria-label="Previous reviews">&#8592;</button>
        <button class="testi-btn testi-btn-next" id="testiNext" aria-label="Next reviews">&#8594;</button>
      </div>
      <div class="testi-dots" id="testiDots"></div>
    </div>
  </section>

  <!-- ========== CTA BANNER ========== -->
  <section class="cta-section" id="contact">
    <div class="cta-bg">
      <div class="cta-orb"></div>
    </div>
    <div class="container cta-inner" data-aos="fade-up">
      <div class="cta-content">
        <h2 class="cta-title">Ready to Build Something <span>Amazing?</span></h2>
        <p class="cta-sub">Tell us about your project. We'll respond within 24 hours and get you started on the path to digital success.</p>
        <div class="cta-info">
          <div class="info-block">
            <div class="info-icon">&#128222;</div>
            <div>
              <div class="info-label">Phone</div>
              <div class="info-val">+880 1776060247</div>
            </div>
          </div>
          <div class="info-block">
            <div class="info-icon">&#9993;</div>
            <div>
              <div class="info-label">Email</div>
              <div class="info-val">support@softorio.com</div>
            </div>
          </div>
          <div class="cta-socials">
            <a href="#" class="social-btn" aria-label="Facebook">f</a>
            <a href="#" class="social-btn" aria-label="Twitter">t</a>
            <a href="#" class="social-btn" aria-label="LinkedIn">in</a>
            <a href="#" class="social-btn" aria-label="YouTube">yt</a>
          </div>
        </div>
      </div>
      <div class="cta-form" data-endpoint="{{ route('contact.store') }}">
        <input type="text" name="name" placeholder="Your Name" class="cta-input" />
        <input type="email" name="email" placeholder="Your Email" class="cta-input" />
        <input type="tel" name="phone" placeholder="Phone Number" class="cta-input" />
        <select name="service_interested" class="cta-input">
          <option value="" disabled selected>Select Service</option>
          <option>Website Development</option>
          <option>SEO Optimization</option>
          <option>Manage Cloud</option>
          <option>Automation</option>
          <option>eCommerce Solution</option>
          <option>Cloud Solution</option>
        </select>
        <textarea name="message" placeholder="Tell us about your project..." class="cta-input cta-textarea"></textarea>
        <input type="text" name="company_website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true" />
        <button class="btn btn-primary btn-lg cta-submit">Send Message</button>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  @vite(['resources/js/home.js'])
@endpush
