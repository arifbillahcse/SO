<footer class="footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <a href="{{ route('home') }}" class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="Softorio" class="logo-img">
      </a>
      <p class="footer-tagline">Your Vision, Our Innovation. Driving digital transformation for businesses worldwide.</p>
      <div class="footer-socials">
        <a href="#" class="social-btn sm" aria-label="Facebook">f</a>
        <a href="#" class="social-btn sm" aria-label="Twitter">t</a>
        <a href="#" class="social-btn sm" aria-label="LinkedIn">in</a>
        <a href="#" class="social-btn sm" aria-label="YouTube">yt</a>
      </div>
    </div>
    <div class="footer-col">
      <h4 class="footer-heading">Services</h4>
      <ul class="footer-links">
        <li><a href="{{ route('web-development') }}">Website Development</a></li>
        <li><a href="{{ route('seo') }}">SEO Optimization</a></li>
        <li><a href="{{ route('manage-cloud') }}">Manage Cloud</a></li>
        <li><a href="{{ route('automation') }}">Automation</a></li>
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
        <li><a href="{{ route('home') }}#contact">Contact Us</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container footer-bottom-inner">
      <p>&copy; {{ date('Y') }} Softorio. All Rights Reserved.</p>
      <div class="footer-legal">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>
