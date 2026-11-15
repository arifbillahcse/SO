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
  const formCard   = document.querySelector('.sp-form-card');
  if (submitBtn && formCard) {
    const originalText  = submitBtn.textContent.trim();
    const requiredNames = ['name', 'email', 'message'];

    submitBtn.addEventListener('click', async e => {
      e.preventDefault();

      let valid = true;
      requiredNames.forEach(fieldName => {
        const inp = formCard.querySelector(`[name="${fieldName}"]`);
        if (inp && !inp.value.trim()) {
          inp.style.borderColor = '#ef4444';
          valid = false;
          setTimeout(() => inp.style.borderColor = '', 2000);
        }
      });
      if (!valid) return;

      const payload = {};
      formCard.querySelectorAll('[name]').forEach(inp => { payload[inp.name] = inp.value; });

      submitBtn.textContent = 'Sending...';
      submitBtn.disabled = true;
      submitBtn.style.background = '';

      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const res = await fetch(formCard.dataset.endpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token || '',
          },
          body: JSON.stringify(payload),
        });

        if (res.ok) {
          submitBtn.textContent = 'Sent! We will respond shortly.';
          submitBtn.style.background = '#22c55e';
          formCard.querySelectorAll('[name]').forEach(inp => {
            if (inp.name !== 'company_website') inp.value = '';
          });
        } else if (res.status === 422) {
          const body = await res.json();
          const firstError = Object.values(body.errors || {})[0]?.[0];
          submitBtn.textContent = firstError || 'Please check your info and try again';
          submitBtn.style.background = '#ef4444';
        } else {
          throw new Error('Request failed with status ' + res.status);
        }
      } catch (err) {
        submitBtn.textContent = 'Something went wrong — please try again';
        submitBtn.style.background = '#ef4444';
      }

      setTimeout(() => {
        submitBtn.textContent      = originalText;
        submitBtn.style.background = '';
        submitBtn.disabled         = false;
      }, 3500);
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
