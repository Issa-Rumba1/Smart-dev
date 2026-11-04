/**
 * SMART DEV - Main JavaScript
 */
'use strict';

// ─── DOM Ready ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initThemeToggle();
    initBackToTop();
    initContactForm();
    initGeolocation();
    initPortfolioFilter();
    autoHideFlash();
    initAdminSidebar();
    initCounters();
});

// ─── Navbar ─────────────────────────────────────────────────────
function initNavbar() {
    const navbar    = document.getElementById('navbar');
    const hamburger = document.getElementById('hamburger');
    const navLinks  = document.getElementById('navLinks');
    const overlay   = document.getElementById('navOverlay');

    if (!navbar) return;

    // Scroll effect
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 50);
        document.getElementById('backToTop')?.classList.toggle('visible', window.scrollY > 300);
    }, { passive: true });

    // Mobile menu
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            const isOpen = navLinks.classList.toggle('open');
            hamburger.classList.toggle('active', isOpen);
            hamburger.setAttribute('aria-expanded', isOpen);
            if (overlay) overlay.style.display = isOpen ? 'block' : 'none';
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });

        if (overlay) {
            overlay.addEventListener('click', closeMobileMenu);
        }

        // Close on nav link click
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });
    }

    function closeMobileMenu() {
        navLinks?.classList.remove('open');
        hamburger?.classList.remove('active');
        hamburger?.setAttribute('aria-expanded', 'false');
        if (overlay) overlay.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// ─── Dark / Light Theme Toggle ──────────────────────────────────
function initThemeToggle() {
    const toggle   = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const html     = document.documentElement;

    if (!toggle) return;

    // Load saved preference
    const saved = localStorage.getItem('sd_theme') || 'light';
    applyTheme(saved);

    toggle.addEventListener('click', () => {
        const current = html.getAttribute('data-theme');
        const next    = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        localStorage.setItem('sd_theme', next);
    });

    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);
        if (themeIcon) {
            themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }
}

// ─── Back to Top ────────────────────────────────────────────────
function initBackToTop() {
    const btn = document.getElementById('backToTop');
    if (!btn) return;
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

// ─── Auto-hide Flash Messages ────────────────────────────────────
function autoHideFlash() {
    const flash = document.getElementById('flashMessage');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.5s';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }, 5000);
    }
}

// ─── Contact Form Validation ─────────────────────────────────────
function initContactForm() {
    const form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        let valid = true;

        const fields = [
            { id: 'full_name',    msg: 'Please enter your name.' },
            { id: 'email',        msg: 'Please enter a valid email address.', validate: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) },
            { id: 'subject',      msg: 'Please enter a subject.' },
            { id: 'message',      msg: 'Please enter a message.' },
        ];

        fields.forEach(f => {
            const input = document.getElementById(f.id);
            const errEl = document.getElementById(f.id + '_error');
            if (!input) return;

            const val = input.value.trim();
            let ok = val.length > 0;
            if (ok && f.validate) ok = f.validate(val);

            input.classList.toggle('error', !ok);
            if (errEl) {
                errEl.textContent = ok ? '' : f.msg;
                errEl.classList.toggle('show', !ok);
            }
            if (!ok) valid = false;
        });

        if (!valid) {
            e.preventDefault();
        }
    });

    // Clear errors on input
    form.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('error');
            const err = document.getElementById(input.id + '_error');
            if (err) { err.textContent = ''; err.classList.remove('show'); }
        });
    });
}

// ─── Geolocation ─────────────────────────────────────────────────
function initGeolocation() {
    const btn = document.getElementById('detectLocationBtn');
    if (!btn) return;

    btn.addEventListener('click', () => {
        if (!navigator.geolocation) {
            showLocationResult('Geolocation is not supported by your browser.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting…';

        navigator.geolocation.getCurrentPosition(
            pos => {
                const { latitude, longitude } = pos.coords;
                // Reverse geocode using a free public API
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`)
                    .then(r => r.json())
                    .then(data => {
                        const addr = data.address || {};
                        const location = addr.city || addr.town || addr.village || addr.county || addr.state || 'Location found';
                        const country  = addr.country || '';
                        const display  = location + (country ? ', ' + country : '');
                        showLocationResult(display);
                        const hiddenInput = document.getElementById('locationInput');
                        if (hiddenInput) hiddenInput.value = display;
                    })
                    .catch(() => {
                        showLocationResult(`Approx. ${latitude.toFixed(2)}°, ${longitude.toFixed(2)}° (Geocoding unavailable)`);
                    })
                    .finally(resetBtn);
            },
            err => {
                const msgs = {
                    1: 'Location permission denied.',
                    2: 'Location unavailable. Please enter manually.',
                    3: 'Location request timed out.',
                };
                showLocationResult(msgs[err.code] || 'Unable to retrieve location.');
                resetBtn();
            },
            { timeout: 10000, maximumAge: 300000, enableHighAccuracy: false }
        );
    });

    function showLocationResult(text) {
        const el = document.getElementById('locationText');
        if (el) el.textContent = text;
    }
    function resetBtn() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-crosshairs"></i> Detect My Location';
    }
}

// ─── Portfolio Filter ─────────────────────────────────────────────
function initPortfolioFilter() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const cards      = document.querySelectorAll('.project-card');
    if (!filterBtns.length) return;

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.dataset.filter;
            cards.forEach(card => {
                const show = filter === 'all' || card.dataset.tech?.toLowerCase().includes(filter.toLowerCase());
                card.style.display = show ? '' : 'none';
            });
        });
    });
}

// ─── Admin Sidebar Toggle (Mobile) ────────────────────────────────
function initAdminSidebar() {
    const sidebar   = document.querySelector('.admin-sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    if (!sidebar || !toggleBtn) return;

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
}

// ─── Animated Counters ─────────────────────────────────────────────
function initCounters() {
    const counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el  = entry.target;
            const end = parseInt(el.dataset.count, 10);
            const dur = 1500;
            const step = end / (dur / 16);
            let current = 0;
            const timer = setInterval(() => {
                current = Math.min(current + step, end);
                el.textContent = Math.floor(current) + (el.dataset.suffix || '');
                if (current >= end) clearInterval(timer);
            }, 16);
            observer.unobserve(el);
        });
    }, { threshold: 0.5 });

    counters.forEach(el => observer.observe(el));
}

// ─── Confirm delete dialogs ──────────────────────────────────────
document.addEventListener('click', (e) => {
    if (e.target.closest('[data-confirm]')) {
        const msg = e.target.closest('[data-confirm]').dataset.confirm || 'Are you sure?';
        if (!confirm(msg)) e.preventDefault();
    }
});
