<header class="navbar" id="navbar">
  <div class="container nav-inner">
    <a href="{{ route('home') }}" class="logo">
      <span class="logo-icon"><img src="{{ asset('images/logo-icon.png') }}" alt="" class="logo-icon-img"></span>
      <span class="logo-text">Softorio</span>
    </a>
    <nav class="nav-links" id="navLinks">

      <a href="{{ route('home') }}" class="nav-item{{ request()->routeIs('home') ? ' active' : '' }}">Home</a>

      <div class="nav-dropdown services-dropdown">
        <div class="services-nav-heading">
        <a href="{{ route('services') }}" class="nav-item{{ request()->routeIs('services', 'web-development', 'seo', 'manage-cloud', 'automation', 'diagnostic-management-system') ? ' active' : '' }}">Services</a>
        <button type="button" class="services-menu-toggle" aria-label="Show services submenu" aria-expanded="false" aria-controls="servicesMenu">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        </div>
        <div class="dropdown-menu" id="servicesMenu">
          <a href="{{ route('web-development') }}" class="dropdown-item{{ request()->routeIs('web-development') ? ' active' : '' }}"><span class="d-icon">&#9001;</span> Website Development</a>
          <a href="{{ route('seo') }}" class="dropdown-item{{ request()->routeIs('seo') ? ' active' : '' }}"><span class="d-icon">&#128269;</span> SEO Optimization</a>
          <a href="{{ route('manage-cloud') }}" class="dropdown-item{{ request()->routeIs('manage-cloud') ? ' active' : '' }}"><span class="d-icon">&#9729;</span> Manage Cloud</a>
          <a href="{{ route('automation') }}" class="dropdown-item{{ request()->routeIs('automation') ? ' active' : '' }}"><span class="d-icon">&#9881;</span> Automation</a>
          <a href="{{ route('diagnostic-management-system') }}" class="dropdown-item{{ request()->routeIs('diagnostic-management-system') ? ' active' : '' }}"><span class="d-icon">&#10010;</span> Diagnostic Management</a>
        </div>
      </div>

      <div class="nav-dropdown">
        <button type="button" class="nav-item has-dropdown{{ request()->routeIs('ecommerce-solution', 'cloud-solution', 'automation-solution') ? ' active' : '' }}">
          Solutions
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="dropdown-menu">
          <a href="{{ route('ecommerce-solution') }}" class="dropdown-item{{ request()->routeIs('ecommerce-solution') ? ' active' : '' }}"><span class="d-icon">&#128722;</span> eCommerce Solution</a>
          <a href="{{ route('cloud-solution') }}" class="dropdown-item{{ request()->routeIs('cloud-solution') ? ' active' : '' }}"><span class="d-icon">&#9729;</span> Cloud Solution</a>
          <a href="{{ route('automation-solution') }}" class="dropdown-item{{ request()->routeIs('automation-solution') ? ' active' : '' }}"><span class="d-icon">&#9881;</span> Automation Solution</a>
        </div>
      </div>

      <a href="{{ route('our-works') }}" class="nav-item{{ request()->routeIs('our-works') ? ' active' : '' }}">Our Works</a>

      <div class="nav-dropdown">
        <button type="button" class="nav-item has-dropdown{{ request()->routeIs('about-us', 'case-study', 'our-resources', 'affiliate-program') ? ' active' : '' }}">
          Resources
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="dropdown-menu">
          <a href="{{ route('about-us') }}" class="dropdown-item{{ request()->routeIs('about-us') ? ' active' : '' }}"><span class="d-icon">&#128101;</span> About Us</a>
          <a href="{{ route('case-study') }}" class="dropdown-item{{ request()->routeIs('case-study') ? ' active' : '' }}"><span class="d-icon">&#128196;</span> Case Study</a>
          <a href="{{ route('our-resources') }}" class="dropdown-item{{ request()->routeIs('our-resources') ? ' active' : '' }}"><span class="d-icon">&#128218;</span> Our Resources</a>
          <a href="{{ route('affiliate-program') }}" class="dropdown-item{{ request()->routeIs('affiliate-program') ? ' active' : '' }}"><span class="d-icon">&#129309;</span> Affiliate Program</a>
        </div>
      </div>

    </nav>
    <div class="nav-actions">
      <button type="button" class="theme-toggle" id="themeToggle" aria-label="Switch to light mode" hidden>
        <svg class="theme-icon-sun" aria-hidden="true" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
        <svg class="theme-icon-moon" aria-hidden="true" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 14.5A8.5 8.5 0 0 1 9.5 3.5a8.5 8.5 0 1 0 11 11Z"/></svg>
        <span class="theme-toggle-label">Light mode</span>
      </button>
      <a href="{{ route('contact') }}" class="btn btn-primary">Get in Touch</a>
      <button type="button" class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>
