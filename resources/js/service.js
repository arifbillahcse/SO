import { initializeLeadForms } from './lead-forms.js';

/* ============================================
   SERVICE PAGE — web-development.html JS
============================================ */

document.addEventListener('DOMContentLoaded', () => {

  const portfolioGrid = document.getElementById('portfolioGrid');
  if (portfolioGrid) {
    const cards = [...portfolioGrid.querySelectorAll('.wp-card')];
    const filters = document.querySelectorAll('[data-portfolio-filters] .filter-btn');
    const collections = document.querySelectorAll('[data-collection]');
    const count = document.getElementById('portfolioCount');
    const categorySelect = document.getElementById('portfolioCategorySelect');
    const empty = document.getElementById('portfolioEmpty');
    const query = new URLSearchParams(window.location.search);
    let collection = [...collections].some(button => button.dataset.collection === query.get('collection')) ? query.get('collection') : 'all';
    let category = [...filters].some(button => button.dataset.filter === query.get('category')) ? query.get('category') : 'all';

    const applyFilters = (animate = true) => {
      let visible = 0;
      cards.forEach(card => {
        const technical = card.dataset.projectType === 'technical_demonstration';
        const collectionMatches = collection === 'all'
          || (collection === 'websites' && !technical)
          || (collection === 'technical' && technical)
          || (collection === 'video' && technical && card.dataset.video === 'true');
        const categoryMatches = category === 'all' || (category === 'demo' ? card.dataset.demo === 'true' : card.dataset.category === category);
        const matches = collectionMatches && categoryMatches;
        card.classList.toggle('hidden', !matches);
        if (matches) {
          visible++;
          if (animate) card.classList.add('aos-animate');
        }
      });
      filters.forEach(button => {
        const active = button.dataset.filter === category;
        button.classList.toggle('active', active);
        button.setAttribute('aria-pressed', String(active));
      });
      collections.forEach(button => button.setAttribute('aria-pressed', String(button.dataset.collection === collection)));
      if (categorySelect) categorySelect.value = category;
      count.textContent = `Showing ${visible} of ${cards.length} projects`;
      portfolioGrid.hidden = visible === 0;
      if (empty) empty.hidden = visible !== 0;
      const url = new URL(window.location.href);
      for (const [key, value] of [['collection', collection], ['category', category]]) {
        if (value === 'all') url.searchParams.delete(key);
        else url.searchParams.set(key, value);
      }
      window.history.replaceState(null, '', url);
    };

    filters.forEach(button => button.addEventListener('click', () => {
      category = button.dataset.filter;
      applyFilters();
    }));
    categorySelect?.addEventListener('change', () => {
      category = categorySelect.value;
      applyFilters();
    });
    collections.forEach(button => button.addEventListener('click', () => {
      collection = button.dataset.collection;
      category = 'all';
      applyFilters();
    }));
    document.querySelector('[data-portfolio-reset]')?.addEventListener('click', () => {
      collection = category = 'all';
      applyFilters();
      document.querySelector('[data-collection="all"]')?.focus();
    });
    applyFilters(false);
  }

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

  initializeLeadForms('.sp-form-card');

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
  }, { threshold: 0.01, rootMargin: '0px 0px -50px 0px' });
  aosEls.forEach(el => obs.observe(el));

  // ========== CURSOR GLOW ==========
  if (window.innerWidth > 768 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
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
