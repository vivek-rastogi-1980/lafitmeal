import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';

gsap.registerPlugin(ScrollTrigger);

document.documentElement.classList.add('js');

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ---------- Smooth scroll (Lenis) ---------- */
if (!reduceMotion) {
    const lenis = new Lenis({ lerp: 0.11, wheelMultiplier: 0.95 });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((t) => lenis.raf(t * 1000));
    gsap.ticker.lagSmoothing(0);
}

/* ---------- Scroll reveals ---------- */
document.querySelectorAll('[data-reveal]').forEach((el) => {
    const mode = el.dataset.reveal || 'up';
    const from = {
        up: { y: 56, opacity: 0 },
        left: { x: -64, opacity: 0 },
        right: { x: 64, opacity: 0 },
        zoom: { scale: 0.88, opacity: 0 },
        blur: { y: 30, opacity: 0, filter: 'blur(12px)' },
    }[mode] ?? { y: 56, opacity: 0 };

    gsap.fromTo(el, from, {
        y: 0, x: 0, scale: 1, opacity: 1, filter: 'blur(0px)',
        duration: reduceMotion ? 0 : 1.1,
        ease: 'power3.out',
        delay: parseFloat(el.dataset.delay || 0),
        scrollTrigger: { trigger: el, start: 'top 88%' },
    });
});

/* ---------- Staggered groups ---------- */
document.querySelectorAll('[data-stagger]').forEach((group) => {
    gsap.fromTo(group.children, { y: 48, opacity: 0 }, {
        y: 0, opacity: 1,
        duration: reduceMotion ? 0 : 0.9,
        ease: 'power3.out',
        stagger: 0.09,
        scrollTrigger: { trigger: group, start: 'top 85%' },
    });
});

/* ---------- Number counters ---------- */
document.querySelectorAll('[data-count]').forEach((el) => {
    const target = parseFloat(el.dataset.count);
    const decimals = el.dataset.decimals ? parseInt(el.dataset.decimals) : 0;
    const obj = { v: 0 };
    gsap.to(obj, {
        v: target,
        duration: reduceMotion ? 0 : 1.6,
        ease: 'power2.out',
        scrollTrigger: { trigger: el, start: 'top 90%' },
        onUpdate: () => { el.textContent = obj.v.toFixed(decimals); },
    });
});

/* ---------- Macro rings ---------- */
document.querySelectorAll('.ring-fill').forEach((ring) => {
    const len = ring.getTotalLength ? ring.getTotalLength() : 2 * Math.PI * 26;
    const pct = parseFloat(ring.dataset.pct || 0);
    ring.style.strokeDasharray = len;
    ring.style.strokeDashoffset = len;
    ScrollTrigger.create({
        trigger: ring,
        start: 'top 90%',
        onEnter: () => { ring.style.strokeDashoffset = len * (1 - Math.min(pct, 1)); },
    });
});

/* ---------- Header meal search ----------
   Type-ahead over /search. The surrounding <form> still GETs /menu?q=, so the
   box keeps working if this never boots. */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('mealSearch', () => ({
        q: new URLSearchParams(location.search).get('q') ?? '',
        results: [],
        showResults: false,
        loading: false,
        active: -1,
        controller: null,

        async lookup() {
            const term = this.q.trim();

            if (term.length < 2) {
                this.results = [];
                this.close();
                return;
            }

            // Drop the previous in-flight request so slow replies can't
            // overwrite results for a newer keystroke.
            this.controller?.abort();
            this.controller = new AbortController();
            this.loading = true;
            this.showResults = true;

            try {
                const res = await fetch(`/search?q=${encodeURIComponent(term)}`, {
                    signal: this.controller.signal,
                    headers: { Accept: 'application/json' },
                });
                this.results = await res.json();
                this.active = -1;
            } catch (err) {
                if (err.name !== 'AbortError') this.results = [];
            } finally {
                this.loading = false;
            }
        },

        move(step) {
            if (!this.showResults || !this.results.length) return;
            this.active = (this.active + step + this.results.length) % this.results.length;
        },

        /* Enter opens the highlighted result; otherwise the form submits to /menu. */
        go(event) {
            if (this.active >= 0 && this.results[this.active]) {
                event.preventDefault();
                location.href = this.results[this.active].url;
            }
        },

        close() {
            this.showResults = false;
            this.active = -1;
        },
    }));
});

/* ---------- Food cinemagraphs ----------
   Native `autoplay muted` already lazy-plays only while on-screen and
   pauses when scrolled away (browser offscreen-video optimisation). We only
   step in to honour reduced-motion: strip autoplay so the poster stays still. */
if (reduceMotion) {
    document.querySelectorAll('video[data-food-video]').forEach((v) => {
        v.removeAttribute('autoplay');
        v.pause();
    });
} else {
    /* Video plays once on load (no loop). Replay it from the start when the
       user hovers its card. */
    document.querySelectorAll('video[data-food-video]').forEach((v) => {
        const card = v.closest('a') || v.parentElement;
        card.addEventListener('pointerenter', () => {
            v.currentTime = 0;
            v.play().catch(() => {});
        });
    });
}

/* ---------- 3D tilt cards ---------- */
if (!reduceMotion && matchMedia('(pointer:fine)').matches) {
    document.querySelectorAll('.tilt').forEach((card) => {
        card.addEventListener('pointermove', (e) => {
            const r = card.getBoundingClientRect();
            const px = (e.clientX - r.left) / r.width - 0.5;
            const py = (e.clientY - r.top) / r.height - 0.5;
            card.style.setProperty('--ry', `${px * 14}deg`);
            card.style.setProperty('--rx', `${-py * 12}deg`);
        });
        card.addEventListener('pointerleave', () => {
            card.style.setProperty('--rx', '0deg');
            card.style.setProperty('--ry', '0deg');
        });
    });
}

/* ---------- Magnetic buttons ---------- */
if (!reduceMotion && matchMedia('(pointer:fine)').matches) {
    document.querySelectorAll('[data-magnet]').forEach((btn) => {
        btn.addEventListener('pointermove', (e) => {
            const r = btn.getBoundingClientRect();
            gsap.to(btn, {
                x: (e.clientX - r.left - r.width / 2) * 0.28,
                y: (e.clientY - r.top - r.height / 2) * 0.28,
                duration: 0.4,
            });
        });
        btn.addEventListener('pointerleave', () => gsap.to(btn, { x: 0, y: 0, duration: 0.5, ease: 'elastic.out(1, .4)' }));
    });
}

/* ---------- Hero intro timeline ---------- */
const hero = document.querySelector('[data-hero]');
if (hero) {
    const tl = gsap.timeline({ defaults: { ease: 'power4.out' } });
    tl.fromTo('[data-hero-line]', { yPercent: 120, opacity: 0 }, { yPercent: 0, opacity: 1, duration: 1.2, stagger: 0.14 }, 0.15)
      .fromTo('[data-hero-fade]', { opacity: 0, y: 26 }, { opacity: 1, y: 0, duration: 1, stagger: 0.1 }, 0.9);

    // parallax the floating ingredients with the mouse
    if (!reduceMotion && matchMedia('(pointer:fine)').matches) {
        hero.addEventListener('pointermove', (e) => {
            const cx = e.clientX / innerWidth - 0.5;
            const cy = e.clientY / innerHeight - 0.5;
            document.querySelectorAll('[data-depth]').forEach((el) => {
                const d = parseFloat(el.dataset.depth);
                gsap.to(el, { x: cx * d * 60, y: cy * d * 40, duration: 0.9, ease: 'power2.out' });
            });
        });
    }
}

/* ---------- Nav background on scroll ---------- */
const nav = document.querySelector('[data-nav]');
if (nav) {
    ScrollTrigger.create({
        start: 80,
        onUpdate: (self) => nav.classList.toggle('nav-scrolled', self.scroll() > 80),
        onToggle: (self) => nav.classList.toggle('nav-scrolled', self.isActive),
    });
}

export { gsap, ScrollTrigger };
