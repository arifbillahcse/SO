/* ============================================
   SOFTORIO — Interactions & Animations
============================================ */

document.addEventListener('DOMContentLoaded', () => {

  // Nav is handled by nav.js

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

  // ========== TESTIMONIAL CAROUSEL (scroll-snap based) ==========
  const viewport  = document.getElementById('testiViewport');
  const track     = document.getElementById('testiTrack');
  const prevBtn   = document.getElementById('testiPrev');
  const nextBtn   = document.getElementById('testiNext');
  const dotsWrap  = document.getElementById('testiDots');
  const cards     = track ? Array.from(track.querySelectorAll('.testi-card')) : [];

  if (viewport && track && cards.length) {
    let perView   = getPerView();
    let pageCount = Math.ceil(cards.length / perView);
    let current   = 0;
    let autoTimer;
    let isSyncing = false;

    function getPerView() {
      if (window.innerWidth <= 768)  return 1;
      if (window.innerWidth <= 1024) return 2;
      return 3;
    }

    function buildDots() {
      if (!dotsWrap) return;
      dotsWrap.innerHTML = '';
      for (let i = 0; i < pageCount; i++) {
        const dot = document.createElement('button');
        dot.className = 'testi-dot' + (i === current ? ' active' : '');
        dot.setAttribute('aria-label', `Go to review page ${i + 1}`);
        dot.addEventListener('click', () => goTo(i));
        dotsWrap.appendChild(dot);
      }
    }

    function updateDots() {
      if (!dotsWrap) return;
      dotsWrap.querySelectorAll('.testi-dot').forEach((d, i) => d.classList.toggle('active', i === current));
    }

    function updateButtons() {
      if (prevBtn) prevBtn.disabled = current === 0;
      if (nextBtn) nextBtn.disabled = current === pageCount - 1;
    }

    function cardOffset(card) {
      return card.getBoundingClientRect().left - track.getBoundingClientRect().left + viewport.scrollLeft;
    }

    function goTo(index) {
      current = Math.max(0, Math.min(index, pageCount - 1));
      isSyncing = true;
      const targetCard = cards[current * perView];
      if (targetCard) {
        viewport.scrollTo({ left: cardOffset(targetCard), behavior: 'smooth' });
      }
      updateDots();
      updateButtons();
      resetAuto();
      setTimeout(() => { isSyncing = false; }, 500);
    }

    function resetAuto() {
      clearInterval(autoTimer);
      autoTimer = setInterval(() => goTo((current + 1) % pageCount), 5000);
    }

    // Detect manual scroll/swipe and sync dots/buttons to nearest page
    let scrollTimer;
    viewport.addEventListener('scroll', () => {
      if (isSyncing) return;
      clearTimeout(scrollTimer);
      scrollTimer = setTimeout(() => {
        const cardW = cards[1] ? cardOffset(cards[1]) - cardOffset(cards[0]) : viewport.clientWidth;
        const scrolledIndex = Math.round(viewport.scrollLeft / cardW);
        current = Math.max(0, Math.min(Math.round(scrolledIndex / perView), pageCount - 1));
        updateDots();
        updateButtons();
      }, 120);
    }, { passive: true });

    if (prevBtn) prevBtn.addEventListener('click', () => goTo(current - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goTo(current + 1));

    viewport.addEventListener('mouseenter', () => clearInterval(autoTimer));
    viewport.addEventListener('mouseleave', resetAuto);

    window.addEventListener('resize', () => {
      perView   = getPerView();
      pageCount = Math.ceil(cards.length / perView);
      current   = Math.min(current, pageCount - 1);
      buildDots();
      updateButtons();
    });

    buildDots();
    updateButtons();
    resetAuto();
  }

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

  // ========== SMOOTH SCROLL (index.html in-page anchors) ==========
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
