import Alpine from 'alpinejs';
import './site.js';

window.Alpine = Alpine;

Alpine.start();

// Load the Three.js hero only where the canvas exists (code-split chunk)
const heroCanvas = document.getElementById('hero-canvas');
if (heroCanvas) {
    import('./hero3d.js').then(({ initHero3D }) => initHero3D(heroCanvas));
}
