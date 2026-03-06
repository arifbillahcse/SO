/* ============================================
   SERVICE PAGE — web-development.html JS
============================================ */

document.addEventListener('DOMContentLoaded', () => {

  // ========== FAQ ACCORDION ==========
  document.querySelectorAll('.faq-item').forEach(item => {
    const btn = item.querySelector('.faq-q');
    const ans = item.querySelector('.faq-a');
    if (!btn || !ans) return;

    btn.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      // close all
      document.querySelectorAll('.faq-item.open').forEach(other => {
        other.classList.remove('open');
        other.querySelector('.faq-a').style.maxHeight = null;
      });
      // open clicked
      if (!isOpen) {
        item.classList.add('open');
        ans.style.maxHeight = ans.scrollHeight + 'px';
      }
    });
  });

  // ========== CONTACT FORM ==========
  const submitBtn = document.querySelector('.sp-submit');
  if (submitBtn) {
    submitBtn.addEventListener('click', e => {
      e.preventDefault();
      const inputs = document.querySelectorAll('.sp-form-card .cta-input');
      let valid = true;
      inputs.forEach(inp => {
        if (!inp.value.trim()) {
          inp.style.borderColor = '#ef4444';
          valid = false;
          setTimeout(() => inp.style.borderColor = '', 2000);
        }
      });
      if (!valid) return;
      submitBtn.textContent = 'Sending...';
      submitBtn.disabled = true;
      setTimeout(() => {
        submitBtn.textContent   = 'Brief Sent! We will respond shortly.';
        submitBtn.style.background = '#22c55e';
        inputs.forEach(inp => inp.value = '');
        setTimeout(() => {
          submitBtn.textContent      = 'Send Project Brief';
          submitBtn.style.background = '';
          submitBtn.disabled         = false;
        }, 3500);
      }, 1500);
    });
  }

  // ========== STICKY NAV SCROLL HIGHLIGHT ==========
  const navbar = document.getElementById('navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 20);
    }, { passive: true });
  }

  // ========== HAMBURGER ==========
  const hamburger = document.getElementById('hamburger');
  const navLinks  = document.getElementById('navLinks');
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
      navLinks.classList.toggle('open');
      hamburger.classList.toggle('active');
      const spans = hamburger.querySelectorAll('span');
      if (hamburger.classList.contains('active')) {
        spans[0].style.transform = 'translateY(7px) rotate(45deg)';
        spans[1].style.opacity   = '0';
        spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
      } else {
        spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
      }
    });
  }

  // ========== SCROLL ANIMATIONS (AOS) ==========
  const aosEls = document.querySelectorAll('[data-aos]');
  const obs = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const delay = parseInt(entry.target.dataset.aosDelay || 0);
        setTimeout(() => entry.target.classList.add('aos-animate'), delay);
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
  aosEls.forEach(el => obs.observe(el));

  // ========== SMOOTH SCROLL ==========
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ========== CURSOR GLOW ==========
  if (window.innerWidth > 768) {
    const glow = document.createElement('div');
    glow.style.cssText = `
      position:fixed; pointer-events:none; z-index:9999;
      width:400px; height:400px; border-radius:50%;
      background:radial-gradient(circle, rgba(255,121,0,0.04), transparent 70%);
      transform:translate(-50%,-50%); top:0; left:0; transition:opacity 0.3s;
    `;
    document.body.appendChild(glow);
    window.addEventListener('mousemove', e => {
      glow.style.top  = e.clientY + 'px';
      glow.style.left = e.clientX + 'px';
    });
  }

});
