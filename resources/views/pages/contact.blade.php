@extends('layouts.app')
@php($errors = $errors ?? new \Illuminate\Support\ViewErrorBag)

@section('title', 'Contact Softorio | Discuss Your Next Project')
@section('description', \App\Support\Seo::excerpt($contactSettings->hero_description))
@section('breadcrumbs', json_encode([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Contact Us', 'url' => route('contact')],
]))

@push('head')
@include('partials.case-study-styles')
<style>
.contact-page .case-hero h1{max-width:880px}
.contact-layout{display:grid;grid-template-columns:minmax(0,.85fr) minmax(0,1.35fr);gap:64px;padding:58px 0 80px;align-items:start}
.contact-overline{font-size:.73rem;text-transform:uppercase;letter-spacing:.12em;color:var(--primary);font-weight:700}
.contact-details h2{font-size:clamp(1.65rem,2.7vw,2.2rem);line-height:1.25;letter-spacing:-.035em;margin:14px 0 28px}
.contact-info-card{padding:23px 0;border-top:1px solid var(--border)}
.contact-info-card h3{font-size:.75rem;letter-spacing:.04em;color:var(--text-muted);font-weight:600;margin-bottom:9px}
.contact-info-card a{font-weight:600;color:var(--primary);line-height:1.8;overflow-wrap:anywhere}
.contact-info-card p{font-size:.9rem;color:var(--text-secondary);line-height:1.8;white-space:pre-line}
.contact-socials{display:flex;flex-wrap:wrap;gap:16px;margin-top:24px}
.contact-socials a{font-size:.8rem;color:var(--text-secondary);text-decoration:underline;text-underline-offset:4px}
.contact-message-card{border:1px solid var(--border);background:var(--dark-3);border-radius:24px;padding:36px;box-shadow:var(--shadow-card);scroll-margin-top:110px}
.contact-message-card h2{font-size:1.65rem;line-height:1.3;letter-spacing:-.03em}
.contact-message-card>p{font-size:.9rem;line-height:1.8;color:var(--text-secondary);margin:14px 0 26px}
.contact-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
.contact-field{display:flex;flex-direction:column;gap:8px;min-width:0}
.contact-field-wide{grid-column:1/-1}
.contact-field label{font-size:.8rem;font-weight:600;color:var(--text-primary)}
.contact-field label span{color:var(--primary)}
.contact-field input,.contact-field select,.contact-field textarea{width:100%;padding:13px 15px;background:var(--dark-2);color:var(--text-primary);border:1px solid var(--border);border-radius:10px;font:inherit;font-size:.88rem;line-height:1.6;min-width:0;transition:border-color .2s}
.contact-field input::placeholder,.contact-field textarea::placeholder{color:var(--text-muted)}
.contact-field input:focus,.contact-field select:focus,.contact-field textarea:focus{outline:2px solid var(--primary-glow);outline-offset:2px;border-color:var(--primary)}
.contact-field [aria-invalid="true"]{border-color:#dc2626}
.contact-field textarea{resize:vertical;min-height:150px}
.contact-error{color:#c2410c;font-size:.76rem;line-height:1.6}
[data-theme="light"] .contact-error{color:#b91c1c}
.contact-form-actions{display:flex;gap:18px;align-items:center;flex-wrap:wrap;margin-top:26px}
.contact-form-actions p{font-size:.73rem;line-height:1.6;color:var(--text-muted)}
.contact-form-actions button:disabled{opacity:.7;cursor:wait}
.contact-status{font-size:.86rem;line-height:1.8;padding:16px;border:1px solid var(--border);border-radius:10px;background:var(--primary-glow);color:var(--text-primary);margin-top:22px}
.contact-status[hidden]{display:none}
.contact-honeypot{position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden}
.contact-next{padding:70px 0;background:var(--dark-2);border-block:1px solid var(--border)}
.contact-section-title{font-size:clamp(1.75rem,3vw,2.5rem);line-height:1.2;letter-spacing:-.035em;margin:14px 0 32px}
.contact-steps{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:28px}
.contact-step{border-top:1px solid var(--border);padding-top:24px}
.contact-step>span{font-size:.8rem;color:var(--primary);font-weight:700}
.contact-step h3{font-size:1.08rem;margin:16px 0 12px}
.contact-step p{color:var(--text-secondary);font-size:.88rem;line-height:1.85}
.contact-faq{padding:76px 0}
.contact-faq-inner{max-width:860px;margin:0 auto}
.contact-faq details{border-top:1px solid var(--border);padding:22px 0}
.contact-faq details:last-of-type{border-bottom:1px solid var(--border)}
.contact-faq summary{cursor:pointer;font-size:1rem;font-weight:600;line-height:1.7}
.contact-faq details p{color:var(--text-secondary);font-size:.9rem;line-height:1.9;margin:18px 0 4px}
.contact-page{overflow-wrap:anywhere}
.contact-page a:focus-visible,.contact-page summary:focus-visible,.contact-page button:focus-visible{outline:2px solid var(--primary);outline-offset:5px}
@media(max-width:900px){.contact-layout{grid-template-columns:1fr;gap:36px}.contact-details{max-width:650px}.contact-steps{grid-template-columns:1fr}}
@media(max-width:600px){.contact-layout{padding:34px 0 50px}.contact-message-card{padding:24px;border-radius:18px}.contact-fields{grid-template-columns:1fr}.contact-message-card h2{font-size:1.45rem}.contact-next,.contact-faq{padding:48px 0}.contact-form-actions button{width:100%}}
@media(prefers-reduced-motion:reduce){.contact-field input,.contact-field select,.contact-field textarea{transition:none}}
</style>
@endpush

@section('content')
<main class="contact-page case-library">
  <section class="case-hero">
    <div class="page-hero-bg" aria-hidden="true"><div class="ph-orb ph-orb-1"></div><div class="ph-grid"></div></div>
    <div class="container">
      <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span class="bc-current">Contact Us</span></div>
      <span class="section-tag">{{ $contactSettings->hero_tag }}</span>
      <h1>{{ $contactSettings->hero_title }}</h1>
      <p class="case-lead">{{ $contactSettings->hero_description }}</p>
      <div class="case-hero-bottom"><a href="#message-form">Start a conversation <span aria-hidden="true">&darr;</span></a></div>
    </div>
  </section>

  <div class="container">
    <div class="contact-layout">
      <aside class="contact-details" aria-label="Contact details">
        <span class="contact-overline">Get in touch</span>
        <h2>A conversation is<br><span class="text-gradient">a good place to start.</span></h2>
        @if($contactEmail)<div class="contact-info-card"><h3>Email</h3><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></div>@endif
        @if($contactPhone)<div class="contact-info-card"><h3>Phone</h3><a href="tel:{{ $phoneLink }}">{{ $contactPhone }}</a></div>@endif
        @if($contactSettings->whatsappUrl())<div class="contact-info-card"><h3>WhatsApp</h3><a href="{{ $contactSettings->whatsappUrl() }}" target="_blank" rel="noopener noreferrer">Chat with us <span aria-hidden="true">&nearr;</span></a></div>@endif
        @if($contactSettings->address || $contactSettings->mapUrl())<div class="contact-info-card"><h3>Location</h3>@if($contactSettings->address)<p>{{ $contactSettings->address }}</p>@endif @if($contactSettings->mapUrl())<a href="{{ $contactSettings->mapUrl() }}" target="_blank" rel="noopener noreferrer">View on map <span aria-hidden="true">&nearr;</span></a>@endif</div>@endif
        @if($contactSettings->business_hours)<div class="contact-info-card"><h3>Business hours</h3><p>{{ $contactSettings->business_hours }}</p></div>@endif
        @if(count($socialLinks))<nav class="contact-socials" aria-label="Softorio social profiles">@foreach($socialLinks as $label => $url)<a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>@endforeach</nav>@endif
      </aside>

      <section class="contact-message-card" id="message-form" aria-labelledby="contact-form-title">
        <h2 id="contact-form-title">{{ $contactSettings->form_title }}</h2>
        <p>{{ $contactSettings->form_description }}</p>
        <form id="contactPageForm" method="POST" action="{{ route('contact.store') }}">
          @csrf
          <div class="contact-fields">
            <div class="contact-field"><label for="contact-name">Your name <span aria-hidden="true">*</span></label><input id="contact-name" name="name" autocomplete="name" required maxlength="255" value="{{ old('name') }}" placeholder="Your full name" aria-describedby="contact-name-error"><span class="contact-error" id="contact-name-error" data-error-for="name">@error('name'){{ $message }}@enderror</span></div>
            <div class="contact-field"><label for="contact-email">Email address <span aria-hidden="true">*</span></label><input id="contact-email" name="email" type="email" autocomplete="email" required maxlength="255" value="{{ old('email') }}" placeholder="you@company.com" aria-describedby="contact-email-error"><span class="contact-error" id="contact-email-error" data-error-for="email">@error('email'){{ $message }}@enderror</span></div>
            <div class="contact-field"><label for="contact-phone">Phone <span style="color:var(--text-muted);font-weight:400">(optional)</span></label><input id="contact-phone" name="phone" type="tel" autocomplete="tel" maxlength="50" value="{{ old('phone') }}" placeholder="Include your country code" aria-describedby="contact-phone-error"><span class="contact-error" id="contact-phone-error" data-error-for="phone">@error('phone'){{ $message }}@enderror</span></div>
            <div class="contact-field"><label for="contact-service">What can we help with?</label><select id="contact-service" name="service_interested" aria-describedby="contact-service-error"><option value="">Choose a service (optional)</option>@foreach($services as $service)<option value="{{ $service->title }}" @selected(old('service_interested') === $service->title)>{{ $service->title }}</option>@endforeach<option value="Other / not sure yet" @selected(old('service_interested') === 'Other / not sure yet')>Other / not sure yet</option></select><span class="contact-error" id="contact-service-error" data-error-for="service_interested">@error('service_interested'){{ $message }}@enderror</span></div>
            <div class="contact-field contact-field-wide"><label for="contact-message">Your message <span aria-hidden="true">*</span></label><textarea id="contact-message" name="message" required maxlength="5000" rows="6" placeholder="Tell us about your goals, current setup and what you need help with." aria-describedby="contact-message-error">{{ old('message') }}</textarea><span class="contact-error" id="contact-message-error" data-error-for="message">@error('message'){{ $message }}@enderror</span></div>
          </div>
          <div class="contact-honeypot" aria-hidden="true"><label for="contact-website">Leave this empty</label><input id="contact-website" name="company_website" tabindex="-1" autocomplete="off"></div>
          <div class="contact-form-actions"><button type="submit" class="btn btn-primary">{{ $contactSettings->submit_label }} <span aria-hidden="true">&nearr;</span></button><p><span aria-hidden="true">*</span> Required fields</p></div>
          <p class="contact-status" data-contact-status role="status" tabindex="-1" @if(!session('contact_success')) hidden @endif>{{ session('contact_success') }}</p>
          @error('company_website')<p class="contact-error">{{ $message }}</p>@enderror
        </form>
      </section>
    </div>
  </div>

  @if(count($contactSettings->next_steps ?? []))
  <section class="contact-next"><div class="container"><span class="contact-overline">A clear next step</span><h2 class="contact-section-title">What happens <span class="text-gradient">next?</span></h2><div class="contact-steps">@foreach($contactSettings->next_steps as $step)<article class="contact-step"><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p></article>@endforeach</div></div></section>
  @endif

  @if(count($contactSettings->faqs ?? []))
  <section class="contact-faq"><div class="container"><div class="contact-faq-inner"><span class="contact-overline">Before you send</span><h2 class="contact-section-title">A few useful <span class="text-gradient">answers.</span></h2>@foreach($contactSettings->faqs as $faq)<details><summary>{{ $faq['question'] }}</summary><p>{{ $faq['answer'] }}</p></details>@endforeach<p style="margin-top:28px;color:var(--text-secondary);font-size:.88rem;line-height:1.8">Exploring your options? <a href="{{ route('services') }}" class="case-text-link">Browse our services <span aria-hidden="true">&rarr;</span></a>.</p></div></div></section>
  @endif
</main>
@endsection

@push('scripts')
  @vite(['resources/js/contact.js'])
@endpush
