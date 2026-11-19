import { initializeLeadForms } from './lead-forms.js';

const page = document.querySelector('.diagnocare-page');
if (page) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    initializeLeadForms('.diagnocare-page [data-endpoint]');

    const slider = page.querySelector('[data-slider]');
    const track = slider.querySelector('.dg-slides');
    const slides = [...track.children];
    let currentSlide = 0;
    const dots = slides.map((slide, index) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.setAttribute('aria-label', `Screen ${index + 1}: ${slide.querySelector('b').textContent}`);
        button.addEventListener('click', () => goToSlide(index));
        slider.querySelector('.dg-dots').append(button);
        return button;
    });
    const markSlide = index => {
        currentSlide = Math.max(0, Math.min(slides.length - 1, index));
        dots.forEach((dot, i) => dot.setAttribute('aria-current', String(i === currentSlide)));
    };
    const goToSlide = index => {
        const next = (index + slides.length) % slides.length;
        track.scrollTo({ left: next * track.clientWidth, behavior: reducedMotion.matches ? 'instant' : 'smooth' });
        markSlide(next);
    };
    slider.querySelector('.dg-prev').addEventListener('click', () => goToSlide(currentSlide - 1));
    slider.querySelector('.dg-next').addEventListener('click', () => goToSlide(currentSlide + 1));
    slider.addEventListener('keydown', event => {
        if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
        event.preventDefault();
        goToSlide(currentSlide + (event.key === 'ArrowRight' ? 1 : -1));
    });
    let scrollTimer;
    track.addEventListener('scroll', () => {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(() => markSlide(Math.round(track.scrollLeft / track.clientWidth)), 100);
    }, { passive: true });
    window.addEventListener('resize', () => {
        track.scrollTo({ left: currentSlide * track.clientWidth, behavior: 'instant' });
    });
    markSlide(0);

    const chips = [...page.querySelectorAll('[data-filter]')];
    chips.forEach(chip => {
        chip.setAttribute('aria-pressed', String(chip.dataset.filter === 'all'));
        chip.addEventListener('click', () => {
            chips.forEach(other => {
                other.classList.toggle('dg-on', other === chip);
                other.setAttribute('aria-pressed', String(other === chip));
            });
            page.querySelectorAll('[data-group]').forEach(group => {
                group.hidden = chip.dataset.filter !== 'all' && group.dataset.group !== chip.dataset.filter;
            });
        });
    });

    const lightbox = page.querySelector('#lightbox');
    let screenshotTrigger;
    page.querySelectorAll('.dg-shot').forEach(button => {
        button.addEventListener('click', () => {
            const image = button.querySelector('img');
            lightbox.querySelector('img').src = image.src;
            lightbox.querySelector('img').alt = image.alt;
            lightbox.querySelector('p').textContent = image.alt;
            screenshotTrigger = button;
            lightbox.showModal();
            document.documentElement.classList.add('dg-lightbox-open');
        });
    });
    lightbox.querySelector('.dg-lb-close').addEventListener('click', () => lightbox.close());
    lightbox.addEventListener('click', event => {
        const bounds = lightbox.getBoundingClientRect();
        if (event.target === lightbox && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) lightbox.close();
    });
    lightbox.addEventListener('close', () => {
        document.documentElement.classList.remove('dg-lightbox-open');
        screenshotTrigger?.focus({ preventScroll: true });
    });

    const switches = [...page.querySelectorAll('[data-modules] .dg-sw')];
    const isOn = key => switches.find(button => button.dataset.m === key)?.getAttribute('aria-pressed') === 'true';
    const syncModules = () => {
        for (let pass = 0; pass < switches.length; pass++) {
            switches.forEach(button => {
                const available = !button.dataset.needs || isOn(button.dataset.needs);
                if (!available) button.setAttribute('aria-pressed', 'false');
                button.disabled = !available;
            });
        }
        switches.forEach(button => page.querySelector(`[data-s="${button.dataset.m}"]`).classList.toggle('dg-off', !isOn(button.dataset.m)));
        page.querySelector('#mod-count').textContent = `${switches.filter(button => isOn(button.dataset.m)).length} of ${switches.length} modules on`;
    };
    switches.forEach(button => button.addEventListener('click', () => {
        button.setAttribute('aria-pressed', String(!isOn(button.dataset.m)));
        syncModules();
    }));
    syncModules();

    page.querySelectorAll('[data-plan]').forEach(link => link.addEventListener('click', () => {
        page.querySelector('#f-plan').value = link.dataset.plan;
    }));
}
