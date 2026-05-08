/**
 * hero3d.js — Grand Hotel | Cinematic 3D Hero Engine
 */
(function () {
  'use strict';

  const canvas = document.getElementById('heroCanvas');
  if (!canvas || typeof THREE === 'undefined') return;

  const isMobile = window.innerWidth < 768;

  /* ──────────────── SCENE SETUP ──────────────── */
  const scene  = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(60, innerWidth / innerHeight, 0.1, 500);
  camera.position.set(0, 1, 9);

  const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: !isMobile });
  renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
  renderer.setSize(innerWidth, innerHeight);
  renderer.setClearColor(0x000000, 0);

  scene.fog = new THREE.FogExp2(0x060200, 0.018);

  /* ──────────────── LIGHTS ──────────────── */
  scene.add(new THREE.AmbientLight(0xffecd0, 0.6));

  const pL1 = new THREE.PointLight(0xC9974A, 5, 35);
  pL1.position.set(3, 5, 4);   scene.add(pL1);

  const pL2 = new THREE.PointLight(0xe8c070, 3, 25);
  pL2.position.set(-5, -3, 3); scene.add(pL2);

  const pL3 = new THREE.PointLight(0xff9944, 2, 20);
  pL3.position.set(0, -8, -5); scene.add(pL3);

  /* ──────────────── PARTICLES ──────────────── */
  const N = isMobile ? 600 : 1400;
  const pos = new Float32Array(N * 3);
  const col = new Float32Array(N * 3);

  // gold palette (r,g,b)
  const pal = [[.788,.592,.290],[.910,.725,.416],[.961,.816,.541],[1,.973,.933],[.627,.455,.165]];

  for (let i = 0; i < N; i++) {
    // spherical shell distribution — looks organic, like stardust
    const radius = 6 + Math.random() * 14;
    const phi    = Math.acos(2 * Math.random() - 1);
    const theta  = Math.random() * Math.PI * 2;
    pos[i*3]   = radius * Math.sin(phi) * Math.cos(theta);
    pos[i*3+1] = radius * Math.sin(phi) * Math.sin(theta) * 0.55;
    pos[i*3+2] = radius * Math.cos(phi) * 0.45 - 4;
    const c = pal[i % pal.length];
    col[i*3] = c[0]; col[i*3+1] = c[1]; col[i*3+2] = c[2];
  }

  const pGeo = new THREE.BufferGeometry();
  pGeo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
  pGeo.setAttribute('color',    new THREE.BufferAttribute(col, 3));
  const pMesh = new THREE.Points(pGeo, new THREE.PointsMaterial({
    size: isMobile ? 0.10 : 0.08,
    vertexColors: true, transparent: true, opacity: 0.92,
    blending: THREE.AdditiveBlending, depthWrite: false
  }));
  scene.add(pMesh);

  /* ──────────────── CRYSTALS ──────────────── */
  const crystals = [];
  const octBase  = new THREE.OctahedronGeometry(1, 0);
  const numC     = isMobile ? 7 : 16;

  for (let i = 0; i < numC; i++) {
    const wire = i % 3 !== 0;
    const mesh = new THREE.Mesh(octBase, new THREE.MeshPhongMaterial({
      color: 0xC9974A, emissive: wire ? 0x3a1800 : 0x1a0800,
      specular: 0xffd580, shininess: 200,
      transparent: true, opacity: wire ? 0.55 : 0.22, wireframe: wire
    }));
    const s = Math.random() * 0.28 + 0.07;
    mesh.scale.set(s, s * (.7 + Math.random() * .7), s);
    mesh.position.set(
      (Math.random()-.5)*20, (Math.random()-.5)*12, (Math.random()-.5)*10 - 3
    );
    mesh.rotation.set(Math.random()*Math.PI, Math.random()*Math.PI, Math.random()*Math.PI);
    mesh.userData = {
      ry: (Math.random()-.5)*.016, rx: (Math.random()-.5)*.010,
      phase: Math.random()*Math.PI*2, speed: Math.random()*.008+.003,
      baseY: mesh.position.y
    };
    scene.add(mesh); crystals.push(mesh);
  }

  /* ──────────────── RINGS ──────────────── */
  const mkRing = (r, tube, color, op, rx, ry) => {
    const m = new THREE.Mesh(
      new THREE.TorusGeometry(r, tube, 8, 120),
      new THREE.MeshBasicMaterial({ color, transparent: true, opacity: op })
    );
    m.rotation.set(rx, ry, 0); scene.add(m); return m;
  };
  const R1 = mkRing(4.5, 0.016, 0xC9974A, 0.20, Math.PI/2.5,  0.3);
  const R2 = mkRing(6.5, 0.010, 0xe8b96a, 0.12, -Math.PI/3.5, Math.PI/5);
  const R3 = mkRing(3.0, 0.022, 0xffd580, 0.16, Math.PI/6,   -Math.PI/8);
  const R4 = mkRing(8.5, 0.006, 0xC9974A, 0.07, Math.PI/4,    Math.PI/4);

  /* ──────────────── MOUSE ──────────────── */
  let mX = 0, mY = 0, sX = 0, sY = 0;
  addEventListener('mousemove', e => {
    mX = (e.clientX / innerWidth  - .5) * 2;
    mY = (e.clientY / innerHeight - .5) * 2;
  });

  /* ──────────────── TICK ──────────────── */
  const clock = new THREE.Clock();
  (function tick() {
    requestAnimationFrame(tick);
    const t = clock.getElapsedTime();
    sX += (mX - sX) * .035; sY += (mY - sY) * .035;

    camera.position.x  = sX * 1.0;
    camera.position.y  = -sY * 0.6 + Math.sin(t * .15) * .12;
    camera.position.z  = 9  + Math.sin(t * .12) * .4;
    camera.lookAt(0, 0, 0);

    pMesh.rotation.y = t * .016;
    pMesh.rotation.x = Math.sin(t * .08) * .04;

    crystals.forEach(c => {
      c.userData.phase += c.userData.speed;
      c.position.y = c.userData.baseY + Math.sin(c.userData.phase) * .22;
      c.rotation.y += c.userData.ry; c.rotation.x += c.userData.rx;
    });

    R1.rotation.z =  t * .040;
    R2.rotation.y =  t * .022; R2.rotation.z = t * .010;
    R3.rotation.z = -t * .060; R3.rotation.y = t * .018;
    R4.rotation.z =  t * .015;

    pL1.intensity = 4.5 + Math.sin(t * 1.4) * 1.0;
    pL2.intensity = 2.5 + Math.cos(t * 1.0) * 0.7;

    renderer.render(scene, camera);
  })();

  /* ──────────────── RESIZE ──────────────── */
  addEventListener('resize', () => {
    camera.aspect = innerWidth / innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(innerWidth, innerHeight);
  });

  /* ──────────────── GSAP ENTRANCE ──────────────── */
  if (typeof gsap === 'undefined') return;

  // Kill any transforms that might linger
  gsap.set(['.h-pretag','.h-word-top','.h-word-mid','.h-word-bot',
            '.h-ruled','.h-sub','.h-btns','.h-stats','.h-scroll-btn',
            '.h-side'], { clearProps: 'all' });

  // Set initial hidden state
  gsap.set('.h-pretag',  { opacity:0, y:-18 });
  gsap.set('.h-word-top',{ opacity:0, y:55 });
  gsap.set('.h-word-mid',{ opacity:0, y:35, scaleY:.7 });
  gsap.set('.h-word-bot',{ opacity:0, y:55 });
  gsap.set(['.h-ruled','.h-sub','.h-btns','.h-stats','.h-scroll-btn','.h-side'], { opacity:0 });
  gsap.set(['.h-sub','.h-btns','.h-stats'], { y:28 });

  const tl = gsap.timeline({ delay: 0.3 });
  tl.to('.h-pretag',    { opacity:1, y:0, duration:.7, ease:'power3.out' })
    .to('.h-word-top',  { opacity:1, y:0, duration:.9, ease:'power3.out' }, '-=.2')
    .to('.h-word-mid',  { opacity:1, y:0, scaleY:1, duration:1.0, ease:'expo.out' }, '-=.65')
    .to('.h-word-bot',  { opacity:1, y:0, duration:.9, ease:'power3.out' }, '-=.7')
    .to('.h-ruled',     { opacity:1, duration:.7 }, '-=.4')
    .to('.h-sub',       { opacity:1, y:0, duration:.8, ease:'power2.out' }, '-=.4')
    .to('.h-btns',      { opacity:1, y:0, duration:.7, ease:'power2.out' }, '-=.35')
    .to('.h-stats',     { opacity:1, y:0, duration:.6, ease:'power2.out' }, '-=.25')
    .to('.h-side',      { opacity:1, duration:.8 }, '-=.4')
    .to('.h-scroll-btn',{ opacity:1, duration:.5 }, '-=.3');

  /* ──────────────── SCROLL PARALLAX ──────────────── */
  const mc = document.querySelector('.h-content');
  addEventListener('scroll', () => {
    if (!mc) return;
    const p = Math.min(scrollY / (innerHeight * .65), 1);
    gsap.set(mc, { y: p * 90, opacity: 1 - p });
  }, { passive: true });

})();
