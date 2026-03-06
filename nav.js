/* ============================================
   SOFTORIO — Shared Nav & Footer Component
   Injected into every page via #nav-placeholder
   and #footer-placeholder
============================================ */
(function () {
  'use strict';

  const page = window.location.pathname.split('/').pop() || 'index.html';

  function ac(href) { return page === href ? ' active' : ''; }
  function pac(hrefs) { return hrefs.includes(page) ? ' active' : ''; }

  const ch = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>`;

  const NAV = `
<header class="navbar" id="navbar">
  <div class="container nav-inner">
    <a href="index.html" class="logo">
      <span class="logo-icon">S</span>
      <span class="logo-text">Softorio</span>
    </a>
    <nav class="nav-links" id="navLinks">

      <a href="index.html" class="nav-item${ac('index.html')}">Home</a>

      <div class="nav-dropdown">
        <a href="#" class="nav-item has-dropdown${pac(['web-development.html','seo.html','web-hosting.html','automation.html'])}">
          Services ${ch}
        </a>
        <div class="dropdown-menu">
          <a href="web-development.html" class="dropdown-item${ac('web-development.html')}"><span class="d-icon">&#9001;</span> Website Development</a>
          <a href="seo.html"             class="dropdown-item${ac('seo.html')}"><span class="d-icon">&#128269;</span> SEO Optimization</a>
          <a href="web-hosting.html"     class="dropdown-item${ac('web-hosting.html')}"><span class="d-icon">&#9729;</span> Web Hosting</a>
          <a href="automation.html"      class="dropdown-item${ac('automation.html')}"><span class="d-icon">&#9881;</span> Automation</a>
        </div>
      </div>

      <div class="nav-dropdown">
        <a href="#" class="nav-item has-dropdown${pac(['ecommerce-solution.html','cloud-solution.html','automation-solution.html'])}">
          Solutions ${ch}
        </a>
        <div class="dropdown-menu">
          <a href="ecommerce-solution.html"  class="dropdown-item${ac('ecommerce-solution.html')}"><span class="d-icon">&#128722;</span> eCommerce Solution</a>
          <a href="cloud-solution.html"      class="dropdown-item${ac('cloud-solution.html')}"><span class="d-icon">&#9729;</span> Cloud Solution</a>
          <a href="automation-solution.html" class="dropdown-item${ac('automation-solution.html')}"><span class="d-icon">&#9881;</span> Automation Solution</a>
        </div>
      </div>

      <a href="our-works.html" class="nav-item${ac('our-works.html')}">Our Works</a>

      <div class="nav-dropdown">
        <a href="#" class="nav-item has-dropdown${pac(['about-us.html','case-study.html','our-resources.html'])}">
          Resources ${ch}
        </a>
        <div class="dropdown-menu">
          <a href="about-us.html"      class="dropdown-item${ac('about-us.html')}"><span class="d-icon">&#128101;</span> About Us</a>
          <a href="case-study.html"    class="dropdown-item${ac('case-study.html')}"><span class="d-icon">&#128196;</span> Case Study</a>
          <a href="our-resources.html" class="dropdown-item${ac('our-resources.html')}"><span class="d-icon">&#128218;</span> Our Resources</a>
        </div>
      </div>

    </nav>
    <div class="nav-actions">
      <a href="index.html#contact" class="btn btn-primary">Get in Touch</a>
      <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>`;

  const FOOTER = `
<footer class="footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <a href="index.html" class="logo">
        <span class="logo-icon">S</span>
        <span class="logo-text">Softorio</span>
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
        <li><a href="web-development.html">Website Development</a></li>
        <li><a href="seo.html">SEO Optimization</a></li>
        <li><a href="web-hosting.html">Web Hosting</a></li>
        <li><a href="automation.html">Automation</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4 class="footer-heading">Solutions</h4>
      <ul class="footer-links">
        <li><a href="ecommerce-solution.html">eCommerce Solution</a></li>
        <li><a href="cloud-solution.html">Cloud Solution</a></li>
        <li><a href="automation-solution.html">Automation Solution</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4 class="footer-heading">Resources</h4>
      <ul class="footer-links">
        <li><a href="about-us.html">About Us</a></li>
        <li><a href="case-study.html">Case Studies</a></li>
        <li><a href="our-resources.html">Our Resources</a></li>
        <li><a href="our-works.html">Our Works</a></li>
        <li><a href="index.html#contact">Contact Us</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container footer-bottom-inner">
      <p>&copy; 2025 Softorio. All Rights Reserved.</p>
      <div class="footer-legal">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>`;

  function inject() {
    const n = document.getElementById('nav-placeholder');
    const f = document.getElementById('footer-placeholder');
    if (n) n.outerHTML = NAV;
    if (f) f.outerHTML = FOOTER;
    initNav();
  }

  function initNav() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

    // Scroll effect
    const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 20);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Hamburger
    const hamburger = document.getElementById('hamburger');
    const navLinks  = document.getElementById('navLinks');
    if (hamburger && navLinks) {
      hamburger.addEventListener('click', () => {
        const open = navLinks.classList.toggle('open');
        const s = hamburger.querySelectorAll('span');
        s[0].style.transform = open ? 'translateY(7px) rotate(45deg)' : '';
        s[1].style.opacity   = open ? '0' : '';
        s[2].style.transform = open ? 'translateY(-7px) rotate(-45deg)' : '';
      });
    }

    // Dropdowns — hover (desktop) with gap-tolerance, click (mobile)
    document.querySelectorAll('.nav-dropdown').forEach(dd => {
      const menu = dd.querySelector('.dropdown-menu');
      if (!menu) return;
      let timer;

      // Desktop hover
      dd.addEventListener('mouseenter', () => {
        if (window.innerWidth <= 768) return;
        clearTimeout(timer);
        menu.classList.add('show');
      });
      dd.addEventListener('mouseleave', () => {
        if (window.innerWidth <= 768) return;
        timer = setTimeout(() => menu.classList.remove('show'), 150);
      });

      // Mobile click
      const trigger = dd.querySelector('.nav-item');
      if (trigger) {
        trigger.addEventListener('click', e => {
          if (window.innerWidth > 768) return;
          e.preventDefault();
          menu.classList.toggle('show');
        });
      }
    });

    // Close nav on mobile when a dropdown link is clicked
    document.querySelectorAll('.dropdown-item').forEach(link => {
      link.addEventListener('click', () => {
        const nl = document.getElementById('navLinks');
        if (nl) nl.classList.remove('open');
        document.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
        if (hamburger) {
          hamburger.querySelectorAll('span').forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
        }
      });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inject);
  } else {
    inject();
  }
})();
