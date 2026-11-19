@extends('layouts.app')
@section('title', 'Business Solutions for Websites, Cloud & Diagnostic Centers | Softorio')
@section('description', 'Explore Softorio solutions for eCommerce, cloud infrastructure, business automation and DiagnoSolution diagnostic center software.')
@section('breadcrumbs', json_encode([['name' => 'Home', 'url' => route('home')], ['name' => 'Solutions', 'url' => route('solutions')]]))
@push('head')
@include('partials.about-styles')
@include('partials.diagnostic-solution-styles')
@endpush
@section('content')
<main class="dc-page">
<section class="dc-hero" style="padding-bottom:42px"><div class="ab-wrap"><nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span class="bc-current">Solutions</span></nav><span class="ab-eyebrow">Solutions</span><h1>Practical solutions.<br><em>Built around your business.</em></h1><p class="dc-lead">Explore the approach that fits your next project, then agree on the scope and priorities with our team.</p></div></section>
<section class="dc-section" style="padding-top:0"><div class="ab-wrap dc-solution-grid"><a href="{{ route('ecommerce-solution') }}" class="dc-solution-card"><span class="dc-solution-icon"><x-service-icon group="code" width="24" height="24" /></span><h2>eCommerce solutions</h2><p>Storefronts, checkout and integrations built around the way you sell.</p><span>Explore solution &nearr;</span></a><a href="{{ route('cloud-solution') }}" class="dc-solution-card"><span class="dc-solution-icon"><x-service-icon group="cloud" width="24" height="24" /></span><h2>Cloud solutions</h2><p>Server setup, hosting infrastructure, security and the systems behind your website.</p><span>Explore solution &nearr;</span></a><a href="{{ route('automation-solution') }}" class="dc-solution-card"><span class="dc-solution-icon"><x-service-icon group="gear" width="24" height="24" /></span><h2>Automation solutions</h2><p>Connect tools and streamline repetitive business workflows.</p><span>Explore solution &nearr;</span></a><a href="{{ route('diagnostic-management-system') }}" class="dc-solution-card"><span class="dc-solution-icon"><x-service-icon group="medical" width="24" height="24" /></span><h2>Diagnostic centers</h2><p>DiagnoSolution brings billing, laboratory workflows, reports and owner oversight into one connected system.</p><span class="dc-status" style="margin-top:18px">DiagnoSolution by Softorio</span><span>Explore solution &nearr;</span></a></div></section>
<section class="dc-section dc-alt"><div class="ab-wrap dc-cta"><div><h2 class="ab-title">Start with a conversation.</h2><p>Tell us what you want to build, improve or simplify. We will help you identify a practical next step.</p></div><a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Discuss your project &nearr;</a></div></section>
</main>
@endsection
