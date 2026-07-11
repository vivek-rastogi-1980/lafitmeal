import Alpine from 'alpinejs';
import './site.js';

window.Alpine = Alpine;

Alpine.start();

// Hero: orbiting glass dishes around the person (code-split, only on home)
const heroStage = document.querySelector('[data-hero-stage]');
if (heroStage) {
    import('./hero-carousel.js').then(({ initHeroCarousel }) => initHeroCarousel(heroStage));
}
