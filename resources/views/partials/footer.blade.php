<footer class="footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <a href="{{ route('home') }}" class="logo">
        <span class="logo-icon"><img src="{{ asset('images/logo-icon.png') }}" alt="" class="logo-icon-img"></span>
        <span class="logo-text">Softorio</span>
      </a>
      <p class="footer-tagline">Your Vision, Our Innovation. Driving digital transformation for businesses worldwide.</p>
      @if($footerContact['telephone_url'] || $footerContact['email'])
      <div class="footer-contact-details">
        @if($footerContact['telephone_url'])
        <a href="{{ $footerContact['telephone_url'] }}">
          <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m7 3 3 5-2.5 2.5a14 14 0 0 0 6 6L16 14l5 3v3a2 2 0 0 1-2.2 2C9.7 21.1 2.9 14.3 2 5.2A2 2 0 0 1 4 3h3Z"/></svg>
          <span>{{ $footerContact['phone'] }}</span>
        </a>
        @endif
        @if($footerContact['email'])
        <a href="mailto:{{ $footerContact['email'] }}">
          <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 7 9 6 9-6"/></svg>
          <span>{{ $footerContact['email'] }}</span>
        </a>
        @endif
      </div>
      @endif
      @if(count($orgSettings->publicSocialLinks()))
      <div class="footer-socials">
        @foreach($orgSettings->publicSocialLinks() as $label => $url)
        <a href="{{ $url }}" class="social-btn sm" aria-label="{{ $label }}" target="_blank" rel="noopener noreferrer">{{ ['Facebook' => 'f', 'X / Twitter' => 'x', 'LinkedIn' => 'in', 'YouTube' => 'yt'][$label] }}</a>
        @endforeach
      </div>
      @endif
    </div>
    <div class="footer-col">
      <h4 class="footer-heading">Services</h4>
      <ul class="footer-links">
        <li><a href="{{ route('web-development') }}">Website Development</a></li>
        <li><a href="{{ route('seo') }}">SEO Optimization</a></li>
        <li><a href="{{ route('manage-cloud') }}">Manage Cloud</a></li>
        <li><a href="{{ route('automation') }}">Automation</a></li>
        <li><a href="{{ route('diagnostic-management-system') }}">Diagnostic Management System</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4 class="footer-heading">Solutions</h4>
      <ul class="footer-links">
        <li><a href="{{ route('ecommerce-solution') }}">eCommerce Solution</a></li>
        <li><a href="{{ route('cloud-solution') }}">Cloud Solution</a></li>
        <li><a href="{{ route('automation-solution') }}">Automation Solution</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4 class="footer-heading">Resources</h4>
      <ul class="footer-links">
        <li><a href="{{ route('about-us') }}">About Us</a></li>
        <li><a href="{{ route('case-study') }}">Case Studies</a></li>
        <li><a href="{{ route('our-resources') }}">Our Resources</a></li>
        <li><a href="{{ route('affiliate-program') }}">Affiliate Program</a></li>
        <li><a href="{{ route('our-works') }}">Our Works</a></li>
        <li><a href="{{ route('contact') }}">Contact Us</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container footer-bottom-inner">
      <p>&copy; {{ date('Y') }} Softorio. All Rights Reserved.</p>
      @if(count($orgSettings->publicLegalLinks()))
      <div class="footer-legal">
        @foreach($orgSettings->publicLegalLinks() as $label => $url)<a href="{{ $url }}">{{ $label }}</a>@endforeach
      </div>
      @endif
    </div>
  </div>
</footer>
