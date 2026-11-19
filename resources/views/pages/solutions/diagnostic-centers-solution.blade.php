@extends('layouts.app')
@section('title', 'Custom Diagnostic Center Software Rollout | Softorio')
@section('description', 'Plan a modular diagnostic center solution for billing, laboratory reports and owner operations, with phased development, clinic review and a real-world pilot.')
@section('breadcrumbs', json_encode([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Solutions', 'url' => route('solutions')],
    ['name' => 'Diagnostic Centers', 'url' => route('diagnostic-centers-solution')],
]))
@push('head')
@include('partials.about-styles')
@include('partials.diagnostic-solution-styles')
@endpush
@section('content')
<main class="dc-page">
<section class="dc-hero">
  <div class="ab-wrap">
    <nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><a href="{{ route('solutions') }}">Solutions</a><span>/</span><span class="bc-current">Diagnostic Centers</span></nav>
    <div class="dc-hero-grid">
      <div>
        <span class="dc-status">Custom implementation &middot; Phased development</span>
        <h1>One connected workflow<br>for your <em>diagnostic center.</em></h1>
        <p class="dc-lead">Bring patient registration, billing, laboratory work and owner oversight into a modular system shaped around your clinic.</p>
        <p class="dc-hero-note">This roadmap describes custom development and extensions for your center. For the DiagnoSolution product, explore the software screens and request a demonstration.</p>
        <p class="dc-hero-note"><a href="{{ route('diagnostic-management-system') }}">Explore DiagnoSolution software &nearr;</a></p>
        <div class="dc-actions"><a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Discuss your center <span aria-hidden="true">&nearr;</span></a><a href="#diagnostic-roadmap" class="ab-text-link">Explore the rollout <span aria-hidden="true">&darr;</span></a></div>
      </div>
      <div class="dc-workflow" aria-label="Planned diagnostic center workflow">
        <div class="dc-workflow-top"><span>THE CLINIC WORKFLOW</span><span>Concept</span></div>
        <div class="dc-flow-row"><span class="dc-flow-number">01</span><div><strong>Register &amp; bill</strong><p>Patients, orders, payments and dues</p></div></div>
        <div class="dc-flow-row"><span class="dc-flow-number">02</span><div><strong>Collect &amp; track</strong><p>Barcoded samples and laboratory work</p></div></div>
        <div class="dc-flow-row"><span class="dc-flow-number">03</span><div><strong>Verify &amp; report</strong><p>Results, review and doctor sign-off</p></div></div>
        <div class="dc-flow-row"><span class="dc-flow-number">04</span><div><strong>Review &amp; manage</strong><p>Cash closing, expenses and owner reports</p></div></div>
        <p class="dc-workflow-foot">Connected modules. Clear responsibilities. Clinic review at every stage.</p>
      </div>
    </div>
  </div>
</section>
<section class="dc-section dc-alt"><div class="ab-wrap">
  <span class="ab-eyebrow">The core scope</span><h2 class="ab-title">Start with the work<br>your center does every day.</h2>
  <div class="dc-three-grid">
    <article class="dc-card"><span class="dc-card-number">01 / FRONT DESK</span><h3>Billing that reflects your practice.</h3><p>Patient records, referral doctors, orders, invoices, payments, partial payments, dues, refunds, discounts and receipts, with daily cash closing.</p></article>
    <article class="dc-card"><span class="dc-card-number">02 / LABORATORY</span><h3>From sample to signed report.</h3><p>Test catalogs, reference ranges, sample barcodes, result entry, abnormal flags, verification, amendments and locked results, with PDF and printed reports.</p></article>
    <article class="dc-card"><span class="dc-card-number">03 / OWNER</span><h3>A clearer view of operations.</h3><p>A mobile-friendly dashboard, expenses, cash and bank accounts, bKash, referral commissions, profit reports and data exports. Accounting depth is agreed during design.</p></article>
  </div>
</div></section>
<section class="dc-section"><div class="ab-wrap dc-split">
  <div><span class="ab-eyebrow">Built in modules</span><h2 class="ab-title">A foundation that can<br>grow with your clinics.</h2><p class="dc-copy">The proposed architecture uses Laravel, Filament and MySQL, with a control database plus a separate database for each clinic. The roadmap targets PHP 8.3; the supported version is confirmed during design.</p></div>
  <div class="dc-foundation"><div><h3>Module controls</h3><p>Defined module descriptions, loading rules, enable / disable switches, dependencies and per-clinic entitlements.</p></div><div><h3>Access and traceability</h3><p>Users, roles, permissions, audit logs and settings designed around responsibility and clinic workflows.</p></div><div><h3>Data rules first</h3><p>Schema, keys, indexes, number counters and state machines reviewed before the application is built.</p></div></div>
</div></section>
<section class="dc-section dc-alt" id="diagnostic-roadmap"><div class="ab-wrap">
  <div class="dc-section-heading"><div><span class="ab-eyebrow">The rollout plan</span><h2 class="ab-title">Design first. Pilot next.<br>Expand with evidence.</h2></div><p>Phases 0-6 establish the core and a clinic pilot. Later phases add capabilities as the clinic's needs become clear. Each phase requires review before the next begins.</p></div>
  <div class="dc-roadmap"><details class="dc-phase" open><summary><span class="dc-phase-number">00</span><span>Design documents</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>Architecture and conventions, module definitions and dependencies, schema design, keys and indexes, number counters, state machines, permissions and a competitor feature checklist.</p></div><div><h3>Your review / decision</h3><p>Approve the schema and module rules. Decide the offline approach and accounting depth before application code begins.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">01</span><span>Skeleton</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>Laravel with a PHP 8.3 target, Filament admin and MySQL; a control database and separate clinic databases; login, users, roles, permissions, audit log, settings, module switches and dependency checks. Includes a demo module, tests and setup README.</p></div><div><h3>Your review / decision</h3><p>Confirm the module manager works the way you need.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">02</span><span>Front desk and billing</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>Patients, referral doctors, test orders, invoices, full and partial payments, dues, refunds, discounts, receipts and daily cash closing. Money calculations receive regression tests.</p></div><div><h3>Your review / decision</h3><p>Review billing rules against a real clinic&#x27;s practice.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">03</span><span>Laboratory and reports</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>Test catalog, reference ranges and packages; barcoded samples; result entry, abnormal flags, verification, amendments and locked results; PDF and printed reports, templates and authorized doctor sign-off.</p></div><div><h3>Your review / decision</h3><p>Review the reports against your actual report formats.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">04</span><span>Owner basics</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>A mobile-friendly owner dashboard, basic reports, expenses, cash, bank and bKash accounts, referral commissions, profit reports and data export.</p></div><div><h3>Your review / decision</h3><p>Decide whether this accounting scope is sufficient for your center.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">05</span><span>Performance and safety pass</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>Simulate two to three years of data at a target of about 500 patients and 1,000 tests per day. Measure queries, fix bottlenecks, test backup and restore, and review security. These are test targets, not verified production capacity.</p></div><div><h3>Your review / decision</h3><p>Provide a MySQL environment for realistic testing.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">06</span><span>Pilot</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>Install at one real clinic, collect feedback and correct issues before expanding the rollout.</p></div><div><h3>Your review / decision</h3><p>Use it for real work and report problems before wider adoption.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">07</span><span>Communication and payments</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>SMS, email and WhatsApp report delivery, an online payment gateway, and a message log with duplicate-send protection.</p></div><div><h3>Your review / decision</h3><p>Choose providers and compare their terms and integration requirements.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">08</span><span>Subscription layer</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>Plans, per-clinic module entitlements, license and trial dates, and billing for your software customers.</p></div><div><h3>Your review / decision</h3><p>Set editions and pricing after obtaining competitor quotes.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">09</span><span>Inventory and people</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>Reagents, consumables, purchases, suppliers and stock alerts, followed by staff, attendance and salary basics.</p></div><div><h3>Your review / decision</h3><p>Ask pilot clinics which inventory and staff processes they actually use.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">10</span><span>Patient portal and imaging reports</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>Online report access, imaging report templates and printing. Imaging image storage is outside this scope.</p></div><div><h3>Your review / decision</h3><p>Decide the portal and imaging workflow using pilot feedback.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">11</span><span>Machine integration and multi-branch</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>A local bridge agent, analyzer connectors for the exact machine models your centers use, branches and branch-wise reports.</p></div><div><h3>Your review / decision</h3><p>This is the largest integration effort. Move it earlier if the clinic relies on analyzers.</p></div></div></details>
<details class="dc-phase"><summary><span class="dc-phase-number">12</span><span>Automatic signup and provisioning</span><span class="dc-expand" aria-hidden="true">+</span></summary><div class="dc-phase-content"><div><h3>What we deliver</h3><p>Automatic clinic setup on Hostorio, after the core application and subscription workflow have been validated.</p></div><div><h3>Your review / decision</h3><p>Schedule this after you have paying customers.</p></div></div></details></div>
</div></section>
<section class="dc-section"><div class="ab-wrap">
  <span class="ab-eyebrow">Decisions we make together</span><h2 class="ab-title">Agree on the important details<br>before committing to the build.</h2>
  <div class="dc-three-grid">
    <article class="dc-card"><h3>Offline approach</h3><p>Agree how the clinic should work during internet outages and what synchronisation is required. Offline support is a design decision, not an assumed feature.</p></article>
    <article class="dc-card"><h3>Accounting depth</h3><p>Review whether expenses, balances, commissions and profit reports are enough, or whether deeper accounting is needed.</p></article>
    <article class="dc-card"><h3>Integrations and providers</h3><p>Choose messaging and payment providers, confirm analyzer models, and use pilot feedback to decide which integrations should come first.</p></article>
  </div>
</div></section>
<section class="dc-section dc-alt"><div class="ab-wrap dc-split">
  <div><span class="ab-eyebrow">Every phase ends with</span><h2 class="ab-title">A reviewable result.<br>A clear decision to continue.</h2><ul class="dc-checklist"><li>All phase tests pass.</li><li>A short summary of what was built, tested and not verified.</li><li>A commit on the development branch; a pull request only when requested.</li><li>Your review before the next phase starts.</li></ul></div>
  <div class="dc-card dc-scope"><h3>Outside the current scope</h3><p>Keeping the first product focused helps the clinic validate the essentials.</p><ul><li>PACS image viewing and imaging image storage</li><li>Hospital beds, OT and inpatient features</li><li>Prescriptions, OPD and pharmacy</li><li>Native mobile applications</li><li>Free-tier logic, ads and watermarks</li></ul></div>
</div></section>
<section class="dc-section"><div class="ab-wrap"><div class="dc-cta">
  <div><span class="ab-eyebrow">The next step</span><h2 class="ab-title">Start with your clinic.<br>Then agree on the design.</h2><p>Discuss your billing rules, report formats, offline needs and priorities. Phase 0 produces design documents only; application development begins after you approve them.</p></div>
  <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Plan your diagnostic solution <span aria-hidden="true">&nearr;</span></a>
</div></div></section>
</main>
@endsection
