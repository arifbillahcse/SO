/* ============================================
   SOFTORIO — Interactions & Animations
============================================ */

document.addEventListener('DOMContentLoaded', () => {

  // ========== NAVBAR SCROLL ==========
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
  });

  // ========== HAMBURGER MENU ==========
  const hamburger = document.getElementById('hamburger');
  const navLinks  = document.getElementById('navLinks');
  hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    const spans = hamburger.querySelectorAll('span');
    hamburger.classList.toggle('active');
    if (hamburger.classList.contains('active')) {
      spans[0].style.transform = 'translateY(7px) rotate(45deg)';
      spans[1].style.opacity   = '0';
      spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
    } else {
      spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
    }
  });

  // Mobile dropdown toggles
  document.querySelectorAll('.nav-dropdown .nav-item').forEach(item => {
    item.addEventListener('click', (e) => {
      if (window.innerWidth <= 768) {
        e.preventDefault();
        item.closest('.nav-dropdown').classList.toggle('open');
      }
    });
  });

  // Close nav when link clicked
  document.querySelectorAll('.dropdown-item, .nav-links > .nav-item').forEach(link => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('open');
      hamburger.classList.remove('active');
      hamburger.querySelectorAll('span').forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
    });
  });

  // ========== AOS — SCROLL ANIMATIONS ==========
  const aosEls = document.querySelectorAll('[data-aos]');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const delay = parseInt(entry.target.dataset.aosDelay || 0);
        setTimeout(() => entry.target.classList.add('aos-animate'), delay);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
  aosEls.forEach(el => observer.observe(el));

  // ========== COUNTER ANIMATION ==========
  const counters = document.querySelectorAll('.stat-num[data-count]');
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        counterObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(el => counterObserver.observe(el));

  function animateCounter(el) {
    const target   = parseInt(el.dataset.count);
    const duration = 1800;
    const start    = performance.now();
    const update   = (now) => {
      const elapsed  = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const ease     = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(ease * target);
      if (progress < 1) requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
  }

  // ========== WORKS FILTER ==========
  const filterBtns = document.querySelectorAll('.filter-btn');
  const workCards  = document.querySelectorAll('.work-card');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filter;
      workCards.forEach(card => {
        const match = filter === 'all' || card.dataset.category === filter;
        card.style.opacity    = '0';
        card.style.transform  = 'scale(0.9)';
        setTimeout(() => {
          card.classList.toggle('hidden', !match);
          if (match) {
            card.style.display = '';
            requestAnimationFrame(() => {
              card.style.opacity   = '1';
              card.style.transform = 'scale(1)';
              card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            });
          }
        }, 200);
      });
    });
  });

  // ========== TESTIMONIAL SLIDER ==========
  const track     = document.getElementById('testiTrack');
  const prevBtn   = document.getElementById('testiPrev');
  const nextBtn   = document.getElementById('testiNext');
  const dotsWrap  = document.getElementById('testiDots');
  const cards     = track ? track.querySelectorAll('.testi-card') : [];
  let current     = 0;
  let perView     = getPerView();
  let total       = Math.ceil(cards.length / perView);
  let autoTimer;

  function getPerView() {
    if (window.innerWidth <= 768) return 1;
    if (window.innerWidth <= 1024) return 2;
    return 3;
  }

  function buildDots() {
    if (!dotsWrap) return;
    dotsWrap.innerHTML = '';
    for (let i = 0; i < total; i++) {
      const dot = document.createElement('button');
      dot.classList.add('testi-dot');
      if (i === current) dot.classList.add('active');
      dot.addEventListener('click', () => goTo(i));
      dotsWrap.appendChild(dot);
    }
  }

  function goTo(index) {
    current = Math.max(0, Math.min(index, total - 1));
    const offset = current * (100 / perView) * perView;
    // calculate card width including gap
    const cardW  = cards[0] ? cards[0].offsetWidth + 24 : 0;
    track.style.transform = `translateX(-${current * perView * cardW}px)`;
    document.querySelectorAll('.testi-dot').forEach((d, i) => d.classList.toggle('active', i === current));
    resetAuto();
  }

  function resetAuto() {
    clearInterval(autoTimer);
    autoTimer = setInterval(() => goTo((current + 1) % total), 5000);
  }

  if (prevBtn) prevBtn.addEventListener('click', () => goTo(current - 1));
  if (nextBtn) nextBtn.addEventListener('click', () => goTo((current + 1) % total));

  window.addEventListener('resize', () => {
    perView = getPerView();
    total   = Math.ceil(cards.length / perView);
    current = 0;
    buildDots();
    goTo(0);
  });

  buildDots();
  resetAuto();

  // ========== CONTACT FORM ==========
  const form       = document.querySelector('.cta-form');
  const submitBtn  = document.querySelector('.cta-submit');
  if (form && submitBtn) {
    submitBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const inputs = form.querySelectorAll('.cta-input');
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
      submitBtn.disabled    = true;
      setTimeout(() => {
        submitBtn.textContent = 'Message Sent!';
        submitBtn.style.background = '#22c55e';
        inputs.forEach(inp => inp.value = '');
        setTimeout(() => {
          submitBtn.textContent      = 'Send Message';
          submitBtn.style.background = '';
          submitBtn.disabled         = false;
        }, 3000);
      }, 1500);
    });
  }

  // ========== NAVBAR ACTIVE LINK ==========
  const sections = document.querySelectorAll('section[id]');
  const navItems = document.querySelectorAll('.nav-item');
  window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(sec => {
      if (window.scrollY >= sec.offsetTop - 120) current = sec.getAttribute('id');
    });
    navItems.forEach(item => {
      item.classList.remove('active');
      if (item.getAttribute('href') === `#${current}`) item.classList.add('active');
    });
  }, { passive: true });

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

  // ========== CURSOR GLOW (desktop) ==========
  if (window.innerWidth > 768) {
    const glow = document.createElement('div');
    glow.style.cssText = `
      position:fixed; pointer-events:none; z-index:9999;
      width:400px; height:400px; border-radius:50%;
      background:radial-gradient(circle, rgba(255,121,0,0.04), transparent 70%);
      transform:translate(-50%,-50%); transition:opacity 0.3s;
      top:0; left:0;
    `;
    document.body.appendChild(glow);
    window.addEventListener('mousemove', e => {
      glow.style.top  = e.clientY + 'px';
      glow.style.left = e.clientX + 'px';
    });
  }

});
