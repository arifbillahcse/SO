document.addEventListener('DOMContentLoaded', () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reducedMotion && 'IntersectionObserver' in window) document.documentElement.classList.add('motion-ready');
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        const update = () => {
            const isLight = document.documentElement.dataset.theme === 'light';
            themeToggle.setAttribute('aria-label', isLight ? 'Switch to dark mode' : 'Switch to light mode');
            themeToggle.querySelector('.theme-toggle-label').textContent = isLight ? 'Dark mode' : 'Light mode';
        };
        update(); themeToggle.hidden = false;
        themeToggle.addEventListener('click', () => {
            const theme = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
            document.documentElement.dataset.theme = theme;
            try { localStorage.setItem('softorio-theme', theme); } catch {}
            update();
        });
    }
    const mobileNavigation = window.matchMedia('(max-width: 1024px)');
    const navbar = document.getElementById('navbar');
    const navLinks = document.getElementById('navLinks');
    const hamburger = document.getElementById('hamburger');
    if (navbar) {
        const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 20);
        window.addEventListener('scroll', onScroll, { passive: true }); onScroll();
    }
    const dropdowns = [];
    const closeDropdowns = () => dropdowns.forEach(({menu, toggle}) => {
        menu.classList.remove('show'); toggle?.setAttribute('aria-expanded', 'false');
    });
    const setMobileMenu = (open, focusFirst = false) => {
        navLinks?.classList.toggle('open', open);
        hamburger?.setAttribute('aria-expanded', String(open));
        hamburger?.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        document.documentElement.classList.toggle('mobile-menu-open', open);
        if (open && focusFirst) navLinks?.querySelector('a, button')?.focus({ preventScroll: true });
        const spans = hamburger?.querySelectorAll('span');
        if (spans?.length === 3) {
            spans[0].style.transform = open ? 'translateY(7px) rotate(45deg)' : '';
            spans[1].style.opacity = open ? '0' : '';
            spans[2].style.transform = open ? 'translateY(-7px) rotate(-45deg)' : '';
        }
        if (!open) closeDropdowns();
    };
    if (hamburger && navLinks) {
        hamburger.setAttribute('aria-controls', 'navLinks'); hamburger.setAttribute('aria-expanded', 'false');
        hamburger.addEventListener('click', event => setMobileMenu(!navLinks.classList.contains('open'), event.detail === 0));
    }
    document.querySelectorAll('.nav-dropdown').forEach((dropdown, index) => {
        const menu = dropdown.querySelector('.dropdown-menu');
        const toggle = dropdown.querySelector('.services-menu-toggle, .has-dropdown');
        if (!menu || !toggle) return;
        menu.id ||= 'navigation-submenu-' + index;
        toggle.setAttribute('aria-controls', menu.id); toggle.setAttribute('aria-expanded', 'false'); toggle.setAttribute('aria-haspopup', 'true');
        dropdowns.push({ menu, toggle });
        let timer;
        const setOpen = open => { menu.classList.toggle('show', open); toggle.setAttribute('aria-expanded', String(open)); };
        dropdown.addEventListener('mouseenter', () => {
            if (mobileNavigation.matches) return;
            clearTimeout(timer); setOpen(true);
        });
        dropdown.addEventListener('mouseleave', () => {
            if (mobileNavigation.matches) return;
            timer = setTimeout(() => setOpen(false), 150);
        });
        toggle.addEventListener('click', event => {
            event.preventDefault();
            const open = !mobileNavigation.matches || !menu.classList.contains('show');
            closeDropdowns(); setOpen(open);
        });
        dropdown.addEventListener('keydown', event => {
            if (event.key === 'Escape' && menu.classList.contains('show')) {
                event.preventDefault(); event.stopPropagation(); setOpen(false); toggle.focus();
            } else if (event.key === 'ArrowDown' && event.target === toggle) {
                event.preventDefault(); setOpen(true); menu.querySelector('a')?.focus();
            }
        });
        dropdown.addEventListener('focusout', () => { setTimeout(() => { if (!dropdown.contains(document.activeElement)) setOpen(false); }, 0); });
    });
    navLinks?.querySelectorAll('a[href]').forEach(link => link.addEventListener('click', () => setMobileMenu(false)));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && navLinks?.classList.contains('open')) { setMobileMenu(false); hamburger?.focus(); }
    });
    document.addEventListener('click', event => { if (navbar && !navbar.contains(event.target)) setMobileMenu(false); });
    window.addEventListener('resize', () => { if (!mobileNavigation.matches) setMobileMenu(false); });
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', event => {
            const fragment = link.getAttribute('href').slice(1);
            if (!fragment) { event.preventDefault(); return; }
            let id; try { id = decodeURIComponent(fragment); } catch { return; }
            const target = document.getElementById(id);
            if (target) { event.preventDefault(); target.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' }); if (link.classList.contains('skip-link')) target.focus({ preventScroll: true }); }
        });
    });
});
