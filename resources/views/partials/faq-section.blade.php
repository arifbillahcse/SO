@php
    $faqTag ??= 'FAQ';
    $faqCtaText ??= 'Ask Us Anything';
    $faqCtaHref ??= '#contact-form';
@endphp

<!-- ========== FAQ ========== -->
<section class="section sp-faq" id="faq">
  <div class="container sp-faq-inner">
    <div class="sp-faq-left" data-aos="fade-right">
      <span class="section-tag">{{ $faqTag }}</span>
      <h2 class="section-title">{!! $faqTitle !!}</h2>
      <p class="section-sub" style="text-align:left;">{{ $faqSubtitle }}</p>
      <a href="{{ $faqCtaHref }}" class="btn btn-primary" style="margin-top:24px;">{{ $faqCtaText }}</a>
    </div>
    <div class="sp-faq-right" data-aos="fade-left">
      <div class="faq-list">
        @foreach($faqs as $faq)
        <div class="faq-item">
          <button class="faq-q">
            {{ $faq['q'] }}
            <svg class="faq-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="faq-a">
            <p>{{ $faq['a'] }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faqs)->map(fn (array $faq) => [
        '@type' => 'Question',
        'name' => $faq['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $faq['a'],
        ],
    ])->all(),
]) !!}</script>
