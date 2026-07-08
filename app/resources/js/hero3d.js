/**
 * Hero 3D scene: a luminous particle "energy bowl" — thousands of points
 * orbiting a torus-bowl form, breathing and reacting to the cursor.
 */
import * as THREE from 'three';

export function initHero3D(canvas) {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(devicePixelRatio, 2));

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(48, 1, 0.1, 100);
    camera.position.set(0, 1.1, 7.5);
    camera.lookAt(0, 0, 0);

    /* --- particle bowl: points distributed over a half-torus + inner swirl --- */
    const COUNT = 5200;
    const positions = new Float32Array(COUNT * 3);
    const scales = new Float32Array(COUNT);
    const colorMix = new Float32Array(COUNT);

    for (let i = 0; i < COUNT; i++) {
        const t = Math.random();
        let x, y, z;

        if (t < 0.62) {
            // torus rim (the bowl's edge)
            const u = Math.random() * Math.PI * 2;
            const v = Math.random() * Math.PI * 2;
            const R = 2.5, r = 0.55 + Math.random() * 0.22;
            x = (R + r * Math.cos(v)) * Math.cos(u);
            z = (R + r * Math.cos(v)) * Math.sin(u);
            y = r * Math.sin(v) * 0.55;
        } else if (t < 0.88) {
            // inner disc swirl (the "food")
            const a = Math.random() * Math.PI * 2;
            const rad = Math.pow(Math.random(), 0.5) * 2.2;
            x = Math.cos(a) * rad;
            z = Math.sin(a) * rad;
            y = 0.25 + Math.sin(rad * 3 + a * 2) * 0.16 + Math.random() * 0.12;
        } else {
            // rising steam column
            const a = Math.random() * Math.PI * 2;
            const rad = Math.random() * 0.9;
            x = Math.cos(a) * rad;
            z = Math.sin(a) * rad;
            y = 0.6 + Math.random() * 2.6;
        }

        positions.set([x, y, z], i * 3);
        scales[i] = Math.random();
        colorMix[i] = Math.random();
    }

    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    geo.setAttribute('aScale', new THREE.BufferAttribute(scales, 1));
    geo.setAttribute('aMix', new THREE.BufferAttribute(colorMix, 1));

    const mat = new THREE.ShaderMaterial({
        transparent: true,
        depthWrite: false,
        blending: THREE.AdditiveBlending,
        uniforms: {
            uTime: { value: 0 },
            uSize: { value: 34 * renderer.getPixelRatio() },
        },
        vertexShader: /* glsl */`
            uniform float uTime;
            uniform float uSize;
            attribute float aScale;
            attribute float aMix;
            varying float vMix;
            varying float vFade;

            void main() {
                vec3 p = position;

                // slow orbital rotation
                float ang = uTime * 0.12 + p.y * 0.35;
                float c = cos(ang), s = sin(ang);
                p.xz = mat2(c, -s, s, c) * p.xz;

                // breathing + per-particle wobble
                p.y += sin(uTime * 0.9 + aScale * 6.2831) * 0.09;
                p.xz *= 1.0 + sin(uTime * 0.5) * 0.02;

                vec4 mv = modelViewMatrix * vec4(p, 1.0);
                gl_Position = projectionMatrix * mv;
                gl_PointSize = uSize * aScale * (1.0 / -mv.z);

                vMix = aMix;
                vFade = smoothstep(3.4, 0.4, p.y); // steam fades with height
            }
        `,
        fragmentShader: /* glsl */`
            varying float vMix;
            varying float vFade;

            void main() {
                float d = distance(gl_PointCoord, vec2(0.5));
                float alpha = smoothstep(0.5, 0.05, d) * 0.9 * vFade;

                vec3 limeNeon = vec3(0.776, 0.949, 0.306);
                vec3 mintCol  = vec3(0.290, 0.871, 0.690);
                vec3 warmCol  = vec3(1.000, 0.478, 0.349);
                vec3 col = mix(limeNeon, mintCol, vMix);
                col = mix(col, warmCol, smoothstep(0.82, 1.0, vMix) * 0.8);

                gl_FragColor = vec4(col, alpha);
            }
        `,
    });

    const points = new THREE.Points(geo, mat);
    points.rotation.x = 0.28;
    scene.add(points);

    /* --- glowing core --- */
    const core = new THREE.Mesh(
        new THREE.SphereGeometry(0.5, 32, 32),
        new THREE.MeshBasicMaterial({ color: 0xc6f24e, transparent: true, opacity: 0.16 })
    );
    core.position.y = 0.3;
    scene.add(core);

    /* --- resize --- */
    function resize() {
        const { clientWidth: w, clientHeight: h } = canvas.parentElement;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
    }
    resize();
    addEventListener('resize', resize);

    /* --- pointer parallax --- */
    let targetX = 0, targetY = 0;
    if (matchMedia('(pointer:fine)').matches) {
        addEventListener('pointermove', (e) => {
            targetX = (e.clientX / innerWidth - 0.5) * 0.6;
            targetY = (e.clientY / innerHeight - 0.5) * 0.35;
        });
    }

    /* --- loop --- */
    const clock = new THREE.Clock();
    let raf;
    function tick() {
        const t = clock.getElapsedTime();
        mat.uniforms.uTime.value = reduceMotion ? 0 : t;
        points.rotation.y += (targetX - points.rotation.y) * 0.03;
        points.rotation.x += (0.28 + targetY - points.rotation.x) * 0.03;
        core.scale.setScalar(1 + Math.sin(t * 1.4) * 0.12);
        renderer.render(scene, camera);
        raf = requestAnimationFrame(tick);
    }
    tick();

    /* pause when off-screen */
    new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) { if (!raf) tick(); }
        else { cancelAnimationFrame(raf); raf = null; }
    }).observe(canvas);
}
