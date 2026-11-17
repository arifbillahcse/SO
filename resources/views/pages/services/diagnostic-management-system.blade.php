@extends('layouts.app')

@section('title', 'Diagnostic Management System for Diagnostic Centers | Softorio')
@section('description', 'Explore diagnostic center software for patient records, billing, sample tracking and reports, with offline and cloud setup options.')

@push('head')
<style>
  .diagnostic-page { --diag-surface: var(--dark-2); }
  .diag-section { padding: 90px 0; }
  .diagnostic-page [id] { scroll-margin-top: 88px; }
  .diag-section.alt { background: var(--dark-2); }
  .diag-heading { max-width: 720px; margin-bottom: 38px; }
  .diag-heading .section-sub { text-align: left; margin: 14px 0 0; }
  .diag-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; }
  .diag-card { background: var(--diag-surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 30px; transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease; }
  .diag-card:hover { border-color: rgba(255,121,0,.4); box-shadow: var(--shadow-card); transform: translateY(-3px); }
  .diag-section.alt .diag-card { background: var(--dark-3); }
  .diag-card h3 { font-size: 1.15rem; margin: 14px 0 10px; }
  .diag-card p, .diag-copy { color: var(--text-secondary); line-height: 1.7; }
  .diag-number { color: var(--primary); font-weight: 800; font-size: .78rem; letter-spacing: .1em; }
  .diag-icon { display: grid; place-items: center; width: 48px; height: 48px; border: 1px solid rgba(255,121,0,.18); border-radius: 14px; background: var(--primary-glow); color: var(--primary); font-size: .85rem; font-weight: 800; }
  .diag-split { display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; }
  .diag-journey { position: relative; width: min(100%, 480px); aspect-ratio: 1.15; display: grid; place-items: center; isolation: isolate; }
  .diag-journey::before { content: ''; position: absolute; inset: 11%; border-radius: 50%; background: radial-gradient(circle, rgba(255,121,0,.16), transparent 70%); filter: blur(12px); }
  .diag-orbit { position: absolute; width: 67%; aspect-ratio: 1; border: 1px dashed rgba(255,121,0,.35); border-radius: 50%; animation: diag-orbit 32s linear infinite; }
  .diag-orbit::after { content: ''; position: absolute; top: 12%; left: 15%; width: 10px; height: 10px; border-radius: 50%; background: #ff7900; box-shadow: 0 0 0 7px rgba(255,121,0,.12); }
  .diag-orbit.inner { width: 48%; border-style: solid; border-color: var(--border); animation-direction: reverse; animation-duration: 24s; }
  .diag-orbit.inner::after { width: 6px; height: 6px; box-shadow: 0 0 0 5px rgba(255,121,0,.1); }
  .diag-hub { position: relative; display: grid; place-items: center; width: 126px; aspect-ratio: 1; border-radius: 38px; background: linear-gradient(145deg, #ff9a3c, #ff7900); color: #fff; box-shadow: 0 20px 50px rgba(255,121,0,.28); animation: diag-breathe 5s ease-in-out infinite; }
  .diag-hub svg { width: 58px; height: 58px; }
  .diag-journey-card { position: absolute; display: flex; align-items: center; gap: 12px; min-width: 158px; padding: 12px 15px; border: 1px solid var(--border); border-radius: 14px; background: var(--dark-3); box-shadow: var(--shadow-card); animation: diag-float 5s ease-in-out infinite; }
  .diag-journey-card svg { flex: none; width: 38px; height: 38px; padding: 9px; color: var(--primary); background: var(--primary-glow); border-radius: 10px; }
  .diag-journey-card strong, .diag-journey-card small { display: block; text-align: left; }
  .diag-journey-card strong { color: var(--text-primary); font-size: .85rem; }
  .diag-journey-card small { color: var(--text-secondary); font-size: .7rem; }
  .diag-journey-card.patient { top: 7%; left: 0; }
  .diag-journey-card.sample { top: 19%; right: 0; animation-delay: -1.3s; }
  .diag-journey-card.billing { bottom: 12%; left: 1%; animation-delay: -2.5s; }
  .diag-journey-card.report { bottom: 3%; right: 1%; animation-delay: -3.7s; }
  @keyframes diag-orbit { to { transform: rotate(360deg); } }
  @keyframes diag-float { 50% { transform: translateY(-8px); } }
  @keyframes diag-breathe { 50% { transform: scale(1.045); } }
  .diag-preview { background: var(--dark-3); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 24px; box-shadow: var(--shadow-card); }
  .diag-preview-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid var(--border); }
  .diag-preview-head strong { color: var(--text-primary); }
  .diag-preview small { color: var(--text-muted); }
  .diag-preview-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 22px 0; }
  .diag-preview-stat { background: var(--dark-4); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 15px; }
  .diag-preview-stat strong { display: block; color: var(--text-primary); margin-top: 4px; }
  .diag-preview-row { display: flex; justify-content: space-between; gap: 12px; padding: 12px 0; border-top: 1px solid var(--border); color: var(--text-secondary); font-size: .85rem; }
  .diag-preview-row span:last-child { color: var(--primary); }
  .diag-note { color: var(--text-muted); font-size: .82rem; margin-top: 16px; }
  .diag-price { color: var(--text-primary); font-size: 2.1rem; font-weight: 800; margin: 20px 0 4px; }
  .diag-price small { font-size: .9rem; font-weight: 600; }
  .diag-card.featured { border-color: var(--primary); background: linear-gradient(145deg, var(--primary-glow), var(--dark-3)); }
  .diag-card .btn { margin-top: 22px; }
  .diag-pricing .diag-card { display: flex; flex-direction: column; align-items: flex-start; }
  .diag-pricing .diag-card > .btn { margin-top: auto; }
  .diag-pricing .diag-price { padding-top: 10px; }
  .diag-pricing .diag-card > p:last-of-type { margin-bottom: 26px; }
  .diag-pricing .featured { box-shadow: var(--shadow-card); }
  .diag-steps { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; }
  .diag-step { border-top: 2px solid var(--primary); padding-top: 18px; }
  .diag-step h3 { margin: 12px 0 8px; font-size: 1.05rem; }
  .diag-step p { color: var(--text-secondary); font-size: .92rem; }
  .diag-list { display: grid; gap: 12px; margin-top: 22px; color: var(--text-secondary); }
  .diag-list li::before { content: '✓'; color: var(--primary); font-weight: 800; margin-right: 10px; }
  .diag-faq { display: grid; gap: 12px; }
  .diag-faq details { background: var(--dark-3); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 18px 20px; }
  .diag-faq summary { display: flex; align-items: center; justify-content: space-between; gap: 18px; font-weight: 700; cursor: pointer; list-style: none; }
  .diag-faq summary::-webkit-details-marker { display: none; }
  .diag-faq summary::after { content: '+'; display: grid; place-items: center; flex: 0 0 32px; height: 32px; border-radius: 9px; background: var(--primary-glow); color: var(--primary); font-size: 1.25rem; }
  .diag-faq details[open] summary::after { content: '−'; }
  .diag-faq summary:focus-visible { outline: 2px solid var(--primary); outline-offset: 4px; border-radius: 3px; }
  .diag-faq details p { margin-top: 12px; color: var(--text-secondary); }
  .diag-contact { position: relative; overflow: hidden; border: 1px solid rgba(255,121,0,.25); border-radius: var(--radius-xl); background: linear-gradient(135deg, var(--primary-glow), var(--dark-3) 70%); padding: clamp(28px, 5vw, 54px); }
  .diag-contact .diag-copy { max-width: 540px; }
  .diag-contact-action { justify-self: end; }
  @media (max-width: 900px) { .diag-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .diag-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); } .diag-split { gap: 32px; } }
  @media (max-width: 768px) { .diag-section { padding: 65px 0; } .diag-grid, .diag-split { grid-template-columns: 1fr; } .diag-heading { margin-bottom: 28px; } .diag-contact-action { justify-self: start; } }
  @media (max-width: 1024px) { .diagnostic-page .page-hero-visual { order: 0; } }
  @media (max-width: 480px) { .diag-steps { grid-template-columns: 1fr; } .diag-card { padding: 22px; } .diag-journey { min-height: 310px; } .diag-journey-card { min-width: 138px; gap: 8px; padding: 9px; } .diag-journey-card svg { width: 33px; height: 33px; padding: 7px; } .diag-hub { width: 92px; border-radius: 29px; } .diag-hub svg { width: 46px; height: 46px; } }
  @media (prefers-reduced-motion: reduce) { .diag-orbit, .diag-hub, .diag-journey-card { animation: none; } .diag-card { transition: none; } }
</style>
@endpush

@section('content')
<main class="diagnostic-page">
  <section class="page-hero">
    <div class="page-hero-bg"><div class="ph-orb ph-orb-1"></div><div class="ph-orb ph-orb-2"></div><div class="ph-grid"></div></div>
    <div class="container page-hero-inner">
      <div class="page-hero-content">
        <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span>Services</span><span>/</span><span class="bc-current">Diagnostic Management</span></div>
        <div class="hero-badge"><span class="badge-dot"></span> Built for diagnostic centers</div>
        <h1 class="page-hero-title">From reception <span class="text-gradient">to report.</span></h1>
        <p class="page-hero-sub">Bring patient registration, billing, sample tracking and pathology reports together in one connected workflow for your diagnostic center.</p>
        <div class="page-hero-cta"><a class="btn btn-primary btn-lg" href="#contact">Request a Demo</a><a class="btn btn-outline btn-lg" href="#features">Explore Features</a></div>
        <p class="diag-note">Software under development. Contact us to confirm current feature availability.</p>
      </div>
      <div class="page-hero-visual">
        <div class="diag-journey" role="img" aria-label="Illustration of a connected diagnostic workflow: patients, billing, samples and reports">
          <span class="diag-orbit" aria-hidden="true"></span><span class="diag-orbit inner" aria-hidden="true"></span>
          <div class="diag-hub" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="5" width="38" height="38" rx="12"/><path d="M24 14v20M14 24h20"/></svg></div>
          <div class="diag-journey-card patient"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="3"/><path d="M5 20c0-4 3-6 7-6s7 2 7 6"/></svg><span><strong>Patients</strong><small>Register &amp; order</small></span></div>
          <div class="diag-journey-card sample"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6M10 3v7l-4 7a3 3 0 0 0 2.6 4.5h6.8A3 3 0 0 0 18 17l-4-7V3M8 16h8"/></svg><span><strong>Samples</strong><small>Collect &amp; track</small></span></div>
          <div class="diag-journey-card billing"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="6" width="16" height="13" rx="2"/><path d="M4 10h16M7 15h4"/></svg><span><strong>Billing</strong><small>Invoices &amp; dues</small></span></div>
          <div class="diag-journey-card report"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h8l4 4v14H6zM14 3v5h4M9 13h6M9 17h6"/></svg><span><strong>Reports</strong><small>Review &amp; release</small></span></div>
        </div>
      </div>
    </div>
  </section>

  <section class="diag-section" id="features"><div class="container">
    <div class="diag-heading"><span class="section-tag">Planned core features</span><h2 class="section-title">The essentials for your <span class="text-gradient">center.</span></h2><p class="section-sub">A shared workflow designed to support your front desk, laboratory and accounts team.</p></div>
    <div class="diag-grid">
      <article class="diag-card"><div class="diag-icon">01</div><h3>Patients &amp; Referrals</h3><p>Keep patient and referring doctor records together as visits and test orders are created.</p></article>
      <article class="diag-card"><div class="diag-icon">02</div><h3>Test Orders &amp; Billing</h3><p>Prepare invoices, record full or partial payments and keep track of outstanding balances.</p></article>
      <article class="diag-card"><div class="diag-icon">03</div><h3>Sample Tracking</h3><p>Label applicable samples and follow their progress from collection through the laboratory.</p></article>
      <article class="diag-card"><div class="diag-icon">04</div><h3>Results &amp; Reports</h3><p>Enter pathology results, arrange independent review and prepare branded PDF reports.</p></article>
      <article class="diag-card"><div class="diag-icon">05</div><h3>Daily Closing</h3><p>Bring billing and collections into a clearer daily closing process for the accounts team.</p></article>
      <article class="diag-card"><div class="diag-icon">06</div><h3>Staff Permissions</h3><p>Organize access around staff roles and responsibilities within the center.</p></article>
    </div><p class="diag-note">These capabilities are planned for the core; availability should be confirmed with our team.</p>
  </div></section>

  <section class="diag-section alt" id="workflow"><div class="container">
    <div class="diag-heading"><span class="section-tag">How it works</span><h2 class="section-title">One visit. <span class="text-gradient">A clear path.</span></h2><p class="section-sub">A planned workflow that helps each team see what comes next.</p></div>
    <div class="diag-steps"><div class="diag-step"><span class="diag-number">01 / REGISTER</span><h3>Register &amp; order</h3><p>Find or register a patient, select tests and add a referring doctor.</p></div><div class="diag-step"><span class="diag-number">02 / BILL</span><h3>Bill &amp; collect</h3><p>Create an invoice, record payment and keep track of the balance.</p></div><div class="diag-step"><span class="diag-number">03 / COLLECT</span><h3>Collect &amp; test</h3><p>Label samples and follow each test through the lab.</p></div><div class="diag-step"><span class="diag-number">04 / REPORT</span><h3>Review &amp; release</h3><p>Enter results, arrange independent review and print the released report.</p></div></div>
  </div></section>

  <section class="diag-section" id="preview"><div class="container diag-split">
    <div><span class="section-tag">One connected workspace</span><h2 class="section-title">The details your team needs, <span class="text-gradient">together.</span></h2><p class="diag-copy" style="margin-top:18px">Connect the front desk, laboratory and accounts around a shared process. Talk to us about the system, current progress and how it could fit your center.</p><a href="#contact" class="btn btn-primary" style="margin-top:24px">Discuss Your Center</a></div>
    <div class="diag-preview"><div class="diag-preview-head"><strong>Center overview</strong><small>ILLUSTRATIVE PREVIEW</small></div><div class="diag-preview-stats"><div class="diag-preview-stat"><small>ORDERS TODAY</small><strong>28</strong></div><div class="diag-preview-stat"><small>COLLECTIONS</small><strong>৳18,450</strong></div></div><div class="diag-preview-row"><span>Complete Blood Count</span><span>In review</span></div><div class="diag-preview-row"><span>Blood Glucose</span><span>Collected</span></div><p class="diag-note">Sample data shown for illustration.</p></div>
  </div></section>

  <section class="diag-section alt" id="free-core"><div class="container diag-split">
    <div><span class="section-tag">The free core</span><h2 class="section-title">Start with the <span class="text-gradient">essentials.</span></h2><p class="diag-copy" style="margin-top:18px">The core software is offered free for one diagnostic center with one active branch. The software is under development; contact us to confirm what is currently available.</p><ul class="diag-list"><li>Patient records and test orders</li><li>Billing, samples and reports</li><li>Daily closing and staff permissions</li></ul></div>
    <div class="diag-card featured"><span class="diag-number">CORE SOFTWARE</span><div class="diag-price">৳0</div><p>One diagnostic center · One active branch</p><p class="diag-note">Hosting, installation, training and support arrangements are discussed separately. Optional extensions are separate.</p><a href="#contact" class="btn btn-outline">Ask About the Core</a></div>
  </div></section>

  <section class="diag-section diag-pricing" id="pricing"><div class="container">
    <div class="diag-heading"><span class="section-tag">Setup &amp; support</span><h2 class="section-title">Choose what fits <span class="text-gradient">your center.</span></h2><p class="section-sub">The core software is free. The following options cover setup or services; contact us to confirm details and availability.</p></div>
    <div class="diag-grid"><article class="diag-card"><span class="diag-number">01 / OFFLINE</span><h3>Offline setup</h3><p>Setup assistance and documentation for an offline installation.</p><div class="diag-price">৳4,999</div><p>One-time setup fee</p><a href="#contact" class="btn btn-outline">Ask About Offline Setup</a></article><article class="diag-card featured"><span class="diag-number">02 / CLOUD</span><h3>Cloud-based setup</h3><p>Online setup for your center, with a monthly server charge.</p><div class="diag-price">৳0 <small>setup</small></div><p>৳299 / month server charge</p><a href="#contact" class="btn btn-primary">Ask About Cloud Setup</a></article><article class="diag-card"><span class="diag-number">03 / CUSTOM</span><h3>Custom solution</h3><p>Need a solution tailored to your center's workflow? Tell us about your requirements.</p><div class="diag-price" style="font-size:1.6rem">Custom quote</div><a href="#contact" class="btn btn-outline">Discuss Your Needs</a></article></div>
  </div></section>

  <section class="diag-section alt" id="getting-started"><div class="container">
    <div class="diag-heading"><span class="section-tag">Getting started</span><h2 class="section-title">A thoughtful start, <span class="text-gradient">together.</span></h2><p class="section-sub">We’ll discuss what your center needs and plan the right setup.</p></div>
    <div class="diag-grid"><article class="diag-card"><span class="diag-number">01</span><h3>Tell us about your center</h3><p>Discuss your workflow, setup and support requirements.</p></article><article class="diag-card"><span class="diag-number">02</span><h3>Gather the essentials</h3><p>Prepare your tests, prices, staff roles and report formats.</p></article><article class="diag-card"><span class="diag-number">03</span><h3>Plan your setup</h3><p>Review the configuration, current availability and next steps with our team.</p></article></div>
    <div class="diag-card" style="margin-top:24px"><span class="diag-number">OPTIONAL EXTENSIONS</span><h3>More possibilities, planned separately.</h3><p>Pharmacy, appointments, inpatient care, home collection, PACS and DICOM are outside the first-version core. Optional add-on availability and pricing are not yet confirmed.</p></div>
  </div></section>

  <section class="diag-section" id="faq"><div class="container diag-split">
    <div><span class="section-tag">Frequently asked questions</span><h2 class="section-title">Questions? <span class="text-gradient">Let's talk.</span></h2><p class="diag-copy" style="margin-top:18px">Our team can help you understand the software and setup options.</p></div>
    <div class="diag-faq"><details><summary>Is the core software free?</summary><p>Yes. The core is offered free for one diagnostic center with one active branch. Hosting, installation, training and support are discussed separately.</p></details><details><summary>Is every feature available now?</summary><p>The software is under development. Contact us to confirm the current status of individual features.</p></details><details><summary>Can I use it offline or online?</summary><p>Both offline setup assistance and cloud-based setup are offered. Contact us to discuss requirements and current availability.</p></details><details><summary>Are add-ons included?</summary><p>Optional extensions are separate from the planned core, and their availability and pricing are not yet confirmed.</p></details></div>
  </div></section>

  <section class="diag-section" id="contact"><div class="container"><div class="diag-contact diag-split"><div><span class="section-tag">Ready to talk?</span><h2 class="section-title">Let's talk about <span class="text-gradient">your center.</span></h2><p class="diag-copy" style="margin-top:18px">Request a demo or discuss the setup, features and support your team needs.</p></div><div class="diag-contact-action"><a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Contact Softorio</a></div></div></div></section>
</main>
<section class="diag-section"><div class="container"><div class="diag-card"><span class="section-tag">Plan the rollout</span><h2 class="section-title" style="margin-top:18px">Explore the diagnostic center solution.</h2><p class="diag-copy">Review the design-first development roadmap, clinic decisions, pilot and phased expansion.</p><a href="{{ route('diagnostic-centers-solution') }}" class="btn btn-primary" style="margin-top:24px">View the solution roadmap &nearr;</a></div></div></section>
@endsection
