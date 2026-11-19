// DiagnoCare landing page. Everything on the page is readable without this
// file; it adds the menu, the scroll animations, the screen slider, the feature filter, the module
// demo and the demo form.
(function () {
  'use strict';

  // Change these two lines to your real contact details.
  var WHATSAPP_NUMBER = '8801XXXXXXXXX'; // country code and number, digits only
  var EMAIL = 'hello@example.com.bd';

  var root = document.documentElement;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Header shadow once the page scrolls
  var header = document.querySelector('.top');
  var onScroll = function () { header.classList.toggle('scrolled', window.scrollY > 8); };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Mobile menu
  var burger = document.querySelector('.burger');
  var nav = document.getElementById('nav');
  burger.addEventListener('click', function () {
    var open = nav.classList.toggle('open');
    burger.setAttribute('aria-expanded', String(open));
  });
  nav.addEventListener('click', function (e) {
    if (e.target.tagName === 'A') { nav.classList.remove('open'); burger.setAttribute('aria-expanded', 'false'); }
  });

  // Reveal sections as they scroll into view
  var revealables = document.querySelectorAll('.rv');
  if (!('IntersectionObserver' in window) || reduceMotion) {
    root.classList.add('no-anim');
    revealables.forEach(function (el) { el.classList.add('seen'); });
  } else {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { entry.target.classList.add('seen'); io.unobserve(entry.target); }
      });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.12 });
    revealables.forEach(function (el) { io.observe(el); });
  }

  // Software views slider: scroll-snap does the swiping; this adds arrows, dots and autoplay
  var slider = document.querySelector('[data-slider]');
  if (slider) {
    var track = slider.querySelector('.slides');
    var slides = Array.prototype.slice.call(track.children);
    var dotsBox = slider.querySelector('.dots');
    var at = 0, auto = null;
    var dots = slides.map(function (s, i) {
      var d = document.createElement('button');
      d.type = 'button'; d.setAttribute('role', 'tab');
      d.setAttribute('aria-label', 'Screen ' + (i + 1) + ': ' + s.querySelector('b').textContent);
      d.addEventListener('click', function () { stopAuto(); go(i); });
      dotsBox.appendChild(d); return d;
    });
    var mark = function (i) {
      at = i;
      dots.forEach(function (d, j) { d.setAttribute('aria-selected', String(j === i)); });
    };
    var go = function (i) {
      i = (i + slides.length) % slides.length;
      track.scrollTo({ left: slides[i].offsetLeft - track.offsetLeft, behavior: reduceMotion ? 'auto' : 'smooth' });
      mark(i);
    };
    var stopAuto = function () { clearInterval(auto); auto = null; };
    slider.querySelector('.prev').addEventListener('click', function () { stopAuto(); go(at - 1); });
    slider.querySelector('.next').addEventListener('click', function () { stopAuto(); go(at + 1); });
    slider.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { stopAuto(); go(at + 1); }
      if (e.key === 'ArrowLeft') { stopAuto(); go(at - 1); }
    });
    // Keep the dots right when someone swipes
    var t = null;
    track.addEventListener('scroll', function () {
      clearTimeout(t);
      t = setTimeout(function () { mark(Math.round(track.scrollLeft / track.clientWidth)); }, 80);
    }, { passive: true });
    track.addEventListener('pointerdown', stopAuto);
    track.addEventListener('touchstart', stopAuto, { passive: true });
    slider.addEventListener('mouseenter', function () { if (auto) { stopAuto(); slider.dataset.paused = '1'; } });
    slider.addEventListener('mouseleave', function () { if (slider.dataset.paused) { delete slider.dataset.paused; startAuto(); } });
    var startAuto = function () { if (!reduceMotion && !auto) auto = setInterval(function () { go(at + 1); }, 5000); };
    mark(0);
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        entries[0].isIntersecting ? startAuto() : stopAuto();
      }, { threshold: 0.4 }).observe(slider);
    }
  }

  // Feature filter
  var chips = document.querySelectorAll('.filters .chip');
  chips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      var want = chip.dataset.filter;
      chips.forEach(function (c) { c.classList.toggle('on', c === chip); c.setAttribute('aria-pressed', String(c === chip)); });
      document.querySelectorAll('.fg').forEach(function (g) {
        g.hidden = want !== 'all' && g.dataset.group !== want;
        if (!g.hidden) g.classList.add('seen');
      });
    });
  });

  // Lightbox for feature screenshots
  var lb = document.getElementById('lightbox');
  if (lb && typeof lb.showModal === 'function') {
    var lbImg = lb.querySelector('img'), lbText = lb.querySelector('p');
    document.querySelectorAll('.shot').forEach(function (b) {
      b.addEventListener('click', function () {
        var img = b.querySelector('img');
        lbImg.src = img.src; lbImg.alt = img.alt; lbText.textContent = img.alt;
        lb.showModal();
      });
    });
    lb.querySelector('.lb-close').addEventListener('click', function () { lb.close(); });
    lb.addEventListener('click', function (e) { if (e.target === lb) lb.close(); });
  }

  // Module demo: switching one off switches off what depends on it
  var box = document.querySelector('[data-modules]');
  if (box) {
    var switches = Array.prototype.slice.call(box.querySelectorAll('.sw'));
    var isOn = function (key) {
      var s = switches.find(function (b) { return b.dataset.m === key; });
      return s && s.getAttribute('aria-pressed') === 'true';
    };
    var sync = function () {
      // Repeat until nothing changes, so chains (Laboratory → Reports → Portal) settle
      for (var pass = 0; pass < switches.length; pass++) {
        switches.forEach(function (b) {
          var needs = b.dataset.needs;
          var ok = !needs || isOn(needs);
          if (!ok) b.setAttribute('aria-pressed', 'false');
          b.disabled = !ok;
        });
      }
      var count = 0;
      switches.forEach(function (b) {
        var on = b.getAttribute('aria-pressed') === 'true';
        if (on) count++;
        var item = document.querySelector('[data-s="' + b.dataset.m + '"]');
        if (item) item.classList.toggle('off', !on);
      });
      document.getElementById('mod-count').textContent = count + ' of ' + switches.length + ' modules on';
    };
    switches.forEach(function (b) {
      b.addEventListener('click', function () {
        b.setAttribute('aria-pressed', String(b.getAttribute('aria-pressed') !== 'true'));
        sync();
      });
    });
  }

  // "Ask about" buttons pick the plan in the form
  document.querySelectorAll('[data-plan]').forEach(function (link) {
    link.addEventListener('click', function () {
      var select = document.getElementById('f-plan');
      if (select) select.value = link.getAttribute('data-plan');
    });
  });

  // Demo form: opens WhatsApp or the mail app with the details filled in
  var form = document.getElementById('demo-form');
  if (form) {
    var via = 'whatsapp';
    var err = document.getElementById('form-err');
    form.querySelectorAll('button[type="submit"]').forEach(function (b) {
      b.addEventListener('click', function () { via = b.dataset.via; });
    });
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var first = null;
      form.querySelectorAll('input[required]').forEach(function (input) {
        var bad = !input.checkValidity();
        input.classList.toggle('bad', bad);
        if (bad && !first) first = input;
      });
      if (first) {
        err.textContent = 'Please add your name, your clinic and a Bangladeshi mobile number.';
        err.hidden = false;
        first.focus();
        return;
      }
      err.hidden = true;
      var d = new FormData(form);
      var text = 'Demo request for DiagnoCare\nName: ' + d.get('name') + '\nClinic: ' + d.get('clinic') +
        '\nMobile: ' + d.get('phone') + '\nPlan: ' + d.get('plan');
      if (via === 'email') {
        window.location.href = 'mailto:' + EMAIL + '?subject=' + encodeURIComponent('Demo request: ' + d.get('clinic')) + '&body=' + encodeURIComponent(text);
      } else {
        window.open('https://wa.me/' + WHATSAPP_NUMBER + '?text=' + encodeURIComponent(text), '_blank', 'noopener');
      }
    });
  }

  var year = document.getElementById('year');
  if (year) year.textContent = String(new Date().getFullYear());
})();
