<header class="navbar" id="navbar">
  <div class="container nav-inner">
    <a href="{{ route('home') }}" class="logo">
      <img src="{{ asset('images/logo.png') }}" alt="Softorio" class="logo-img">
    </a>
    <nav class="nav-links" id="navLinks">

      <a href="{{ route('home') }}" class="nav-item{{ request()->routeIs('home') ? ' active' : '' }}">Home</a>

      <div class="nav-dropdown">
        <a href="#" class="nav-item has-dropdown{{ request()->routeIs('web-development', 'seo', 'manage-cloud', 'automation') ? ' active' : '' }}">
          Services
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <div class="dropdown-menu">
          <a href="{{ route('web-development') }}" class="dropdown-item{{ request()->routeIs('web-development') ? ' active' : '' }}"><span class="d-icon">&#9001;</span> Website Development</a>
          <a href="{{ route('seo') }}" class="dropdown-item{{ request()->routeIs('seo') ? ' active' : '' }}"><span class="d-icon">&#128269;</span> SEO Optimization</a>
          <a href="{{ route('manage-cloud') }}" class="dropdown-item{{ request()->routeIs('manage-cloud') ? ' active' : '' }}"><span class="d-icon">&#9729;</span> Manage Cloud</a>
          <a href="{{ route('automation') }}" class="dropdown-item{{ request()->routeIs('automation') ? ' active' : '' }}"><span class="d-icon">&#9881;</span> Automation</a>
        </div>
      </div>

      <div class="nav-dropdown">
        <a href="#" class="nav-item has-dropdown{{ request()->routeIs('ecommerce-solution', 'cloud-solution', 'automation-solution') ? ' active' : '' }}">
          Solutions
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <div class="dropdown-menu">
          <a href="{{ route('ecommerce-solution') }}" class="dropdown-item{{ request()->routeIs('ecommerce-solution') ? ' active' : '' }}"><span class="d-icon">&#128722;</span> eCommerce Solution</a>
          <a href="{{ route('cloud-solution') }}" class="dropdown-item{{ request()->routeIs('cloud-solution') ? ' active' : '' }}"><span class="d-icon">&#9729;</span> Cloud Solution</a>
          <a href="{{ route('automation-solution') }}" class="dropdown-item{{ request()->routeIs('automation-solution') ? ' active' : '' }}"><span class="d-icon">&#9881;</span> Automation Solution</a>
        </div>
      </div>

      <a href="{{ route('our-works') }}" class="nav-item{{ request()->routeIs('our-works') ? ' active' : '' }}">Our Works</a>

      <div class="nav-dropdown">
        <a href="#" class="nav-item has-dropdown{{ request()->routeIs('about-us', 'case-study', 'our-resources', 'affiliate-program') ? ' active' : '' }}">
          Resources
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <div class="dropdown-menu">
          <a href="{{ route('about-us') }}" class="dropdown-item{{ request()->routeIs('about-us') ? ' active' : '' }}"><span class="d-icon">&#128101;</span> About Us</a>
          <a href="{{ route('case-study') }}" class="dropdown-item{{ request()->routeIs('case-study') ? ' active' : '' }}"><span class="d-icon">&#128196;</span> Case Study</a>
          <a href="{{ route('our-resources') }}" class="dropdown-item{{ request()->routeIs('our-resources') ? ' active' : '' }}"><span class="d-icon">&#128218;</span> Our Resources</a>
          <a href="{{ route('affiliate-program') }}" class="dropdown-item{{ request()->routeIs('affiliate-program') ? ' active' : '' }}"><span class="d-icon">&#129309;</span> Affiliate Program</a>
        </div>
      </div>

    </nav>
    <div class="nav-actions">
      <a href="{{ route('home') }}#contact" class="btn btn-primary">Get in Touch</a>
      <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>
