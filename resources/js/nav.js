/* ============================================
   SOFTORIO — Nav & Footer Interactivity
   (Markup is now server-rendered via Blade partials;
   this file only handles behavior.)
============================================ */
document.addEventListener('DOMContentLoaded', () => {
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
});
