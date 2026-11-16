<footer class="footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <a href="{{ route('home') }}" class="logo">
        <span class="logo-icon"><img src="{{ asset('images/logo-icon.png') }}" alt="" class="logo-icon-img"></span>
        <span class="logo-text">Softorio</span>
      </a>
      <p class="footer-tagline">Your Vision, Our Innovation. Driving digital transformation for businesses worldwide.</p>
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
