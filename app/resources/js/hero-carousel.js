/**
 * Hero orbit: glass dish cards revolve around the standing person.
 * Front-most card is marked "selected" and mirrored into the HUD, as if
 * the person is choosing it. Cards face the camera (billboard style) so the
 * dish photos stay readable; depth is faked with scale / opacity / blur /
 * z-index so front cards pass in front of the person and back cards behind.
 */
export function initHeroCarousel(stage) {
    const ring = stage.querySelector('[data-hero-ring]');
    const cards = [...stage.querySelectorAll('[data-dish]')];
    if (!cards.length) return;

    const hudName = stage.querySelector('[data-hud-name]');
    const hudMacros = stage.querySelector('[data-hud-macros]');
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const n = cards.length;
    const TAU = Math.PI * 2;
    const PERSON_Z = 100; // cards with depth>0 sit above (in front of) the person

    let angle = -0.35; // start slightly turned so the ring reads as 3D, not flat
    let velocity = reduce ? 0 : 0.0042; // idle spin speed (radians/frame)
    let paused = false;
    let selected = -1;

    // pointer-drag to spin
    let dragging = false;
    let lastX = 0;

    function layout() {
        const w = stage.clientWidth;
        // orbit width: keep the outer cards inside the stage on small screens
        const radiusX = Math.min(w * 0.32, 210);
        const liftY = 6; // subtle vertical bob between front/back

        let frontIdx = 0;
        let frontDepth = -2;

        cards.forEach((card, i) => {
            const a = angle + (i / n) * TAU;
            const depth = Math.cos(a); // 1 = front, -1 = back
            const x = Math.sin(a) * radiusX;
            const y = -depth * liftY;
            const scale = 0.62 + ((depth + 1) / 2) * 0.46; // 0.62 → 1.08
            const z = Math.round(PERSON_Z + depth * 90); // 10 → 190

            // centre the card on the orbit point regardless of its own size
            card.style.transform =
                `translate(calc(-50% + ${x.toFixed(1)}px), calc(-50% + ${y}px)) scale(${scale.toFixed(3)})`;
            card.style.opacity = (0.4 + ((depth + 1) / 2) * 0.6).toFixed(3);
            card.style.filter = depth < 0 ? `blur(${(-depth * 2.4).toFixed(1)}px)` : 'none';
            card.style.zIndex = String(z);
            // back-facing cards shouldn't intercept clicks meant for the person
            card.style.pointerEvents = depth > -0.2 ? 'auto' : 'none';

            if (depth > frontDepth) {
                frontDepth = depth;
                frontIdx = i;
            }
        });

        if (frontIdx !== selected) {
            selected = frontIdx;
            cards.forEach((c, i) => c.classList.toggle('is-selected', i === frontIdx));
            const c = cards[frontIdx];
            if (hudName) hudName.textContent = c.dataset.name;
            if (hudMacros) hudMacros.textContent =
                `${c.dataset.cat} · ${c.dataset.kcal} kcal · ${c.dataset.protein}g protein`;
        }
    }

    const IDLE = reduce ? 0 : 0.0042;

    function frame() {
        if (!paused && !dragging) angle += velocity;
        // ease idle velocity back to default after a manual nudge
        velocity += (IDLE - velocity) * 0.03;
        layout();
        requestAnimationFrame(frame);
    }

    // pause the idle spin while hovering so users can read a card
    stage.addEventListener('pointerenter', () => (paused = true));
    stage.addEventListener('pointerleave', () => {
        paused = false;
        dragging = false;
    });

    // drag to spin
    ring.addEventListener('pointerdown', (e) => {
        dragging = true;
        lastX = e.clientX;
        ring.setPointerCapture?.(e.pointerId);
    });
    ring.addEventListener('pointermove', (e) => {
        if (!dragging) return;
        angle += (e.clientX - lastX) * 0.006;
        lastX = e.clientX;
    });
    ring.addEventListener('pointerup', () => (dragging = false));

    // let a genuine click (no drag) still open the dish page
    cards.forEach((card) => {
        let downX = 0;
        card.addEventListener('pointerdown', (e) => (downX = e.clientX));
        card.addEventListener('click', (e) => {
            if (Math.abs(e.clientX - downX) > 6) e.preventDefault(); // was a drag
        });
    });

    // arrows: step to the next / previous dish
    const step = TAU / n;
    stage.querySelector('[data-hero-next]')?.addEventListener('click', () => (angle -= step));
    stage.querySelector('[data-hero-prev]')?.addEventListener('click', () => (angle += step));

    // Correct static layout must never depend on rAF (some environments
    // throttle it). Relayout directly on resize / load / observer, and after
    // the fonts+images settle the grid width.
    new ResizeObserver(layout).observe(stage);
    window.addEventListener('resize', layout);
    window.addEventListener('load', layout);
    layout();
    requestAnimationFrame(() => layout());
    setTimeout(layout, 250);

    // rAF loop drives the idle spin (frozen for reduced-motion users).
    requestAnimationFrame(frame);
}
