<?php
/**
 * components/hero-background.php — Komponen Latar Hero Beranimasi
 * Porting terintegrasi dari HeroBackground.tsx untuk stack PHP Native
 * 
 * 1. Gambar zoom pelan (Ken Burns) foto Rumah Gadang
 * 2. Overlay maroon (#3a0d12/70%) + radial vignette
 * 3. Cahaya hangat berdenyut halus (hero-glow)
 * 4. Partikel emas naik perlahan (canvas embers) dengan prefers-reduced-motion check
 */
$hero_bg_image = $hero_bg_image ?? 'assets/img/rumah-gadang.jpg';
?>
<!-- HeroBackground Component -->
<div aria-hidden="true" class="hero-bg-wrapper pointer-events-none absolute inset-0 -z-10 overflow-hidden">
    <style>
        @keyframes hero-kenburns {
            from { transform: scale(1.05) translate3d(0, 0, 0); }
            to   { transform: scale(1.16) translate3d(-1.5%, -1%, 0); }
        }
        @keyframes hero-glow {
            0%, 100% { opacity: .35; transform: translate(-50%, -50%) scale(1); }
            50%      { opacity: .6;  transform: translate(-50%, -50%) scale(1.08); }
        }
        .hero-kenburns {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            will-change: transform;
            animation: hero-kenburns 30s ease-in-out infinite alternate;
        }
        .hero-overlay-maroon {
            position: absolute;
            inset: 0;
            background-color: rgba(58, 13, 18, 0.70);
        }
        .hero-overlay-vignette {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at center, transparent 35%, rgba(20, 4, 6, 0.75) 100%);
        }
        .hero-glow {
            position: absolute;
            left: 50%;
            top: 38%;
            height: 420px;
            width: 720px;
            max-width: 90vw;
            border-radius: 9999px;
            filter: blur(48px);
            background: radial-gradient(circle, rgba(232, 178, 92, 0.35), transparent 70%);
            animation: hero-glow 7s ease-in-out infinite;
            pointer-events: none;
        }
        #hero-embers-canvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        @media (prefers-reduced-motion: reduce) {
            .hero-kenburns, .hero-glow { animation: none !important; }
        }
    </style>

    <!-- 1. Gambar siluet Rumah Gadang dengan zoom pelan (Ken Burns) -->
    <div class="hero-kenburns" style="background-image: url('<?= htmlspecialchars($hero_bg_image); ?>');"></div>

    <!-- 2. Overlay maroon + vignette -->
    <div class="hero-overlay-maroon"></div>
    <div class="hero-overlay-vignette"></div>

    <!-- 3. Cahaya hangat di belakang judul -->
    <div class="hero-glow"></div>

    <!-- 4. Partikel emas naik perlahan (Embers) -->
    <canvas id="hero-embers-canvas"></canvas>
</div>

<script>
(function() {
    const canvas = document.getElementById('hero-embers-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Hormati pengguna yang mematikan animasi di sistemnya
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    let w = 0;
    let h = 0;
    let raf = 0;
    let embers = [];

    const spawn = (initial = false) => {
        const maxLife = 360 + Math.random() * 360;
        return {
            x: Math.random() * w,
            y: initial ? Math.random() * h : h + 10,
            r: 0.6 + Math.random() * 1.8,
            vy: 0.15 + Math.random() * 0.45,
            sway: 8 + Math.random() * 22,
            phase: Math.random() * Math.PI * 2,
            life: initial ? Math.random() * maxLife : 0,
            maxLife: maxLife
        };
    };

    const resize = () => {
        const parent = canvas.parentElement;
        if (!parent) return;
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        w = parent.clientWidth;
        h = parent.clientHeight;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        canvas.style.width = w + 'px';
        canvas.style.height = h + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        const count = Math.min(70, Math.round(w / 22));
        embers = Array.from({ length: count }, () => spawn(true));
    };

    const draw = () => {
        ctx.clearRect(0, 0, w, h);

        for (let i = 0; i < embers.length; i++) {
            const e = embers[i];
            e.life += 1;
            e.y -= e.vy;
            const x = e.x + Math.sin(e.life / 60 + e.phase) * e.sway * 0.05 * 10;

            // fade in di awal, fade out di akhir
            const t = e.life / e.maxLife;
            const alpha = Math.sin(Math.min(t, 1) * Math.PI) * 0.75;

            const g = ctx.createRadialGradient(x, e.y, 0, x, e.y, e.r * 4);
            g.addColorStop(0, `rgba(240, 196, 90, ${alpha})`);
            g.addColorStop(1, 'rgba(240, 196, 90, 0)');
            ctx.fillStyle = g;
            ctx.beginPath();
            ctx.arc(x, e.y, e.r * 4, 0, Math.PI * 2);
            ctx.fill();

            if (e.life >= e.maxLife || e.y < -10) embers[i] = spawn();
        }

        raf = requestAnimationFrame(draw);
    };

    const onVisibility = () => {
        cancelAnimationFrame(raf);
        if (!document.hidden) raf = requestAnimationFrame(draw);
    };

    resize();
    raf = requestAnimationFrame(draw);
    window.addEventListener('resize', resize);
    document.addEventListener('visibilitychange', onVisibility);
})();
</script>
