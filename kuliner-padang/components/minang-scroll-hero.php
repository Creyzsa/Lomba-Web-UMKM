<?php
/**
 * components/minang-scroll-hero.php
 * Porting terintegrasi dari MinangScrollHero (1).tsx untuk PHP Native
 * Efek Scroll-Scrubbed: Makanan keluar dari pintu Rumah Gadang saat scroll down & mundur saat scroll up.
 */
$hero_bg = 'assets/img/rumah-gadang.jpg';

// 8 Makanan Autentik dari database & koleksi lokal kita
$food_items = [
    ["id" => "rendang", "label" => "Rendang Daging", "src" => "assets/img/menu_rendang.jpg", "size" => 18, "to" => ["x" => -33, "y" => -16, "r" => -8, "s" => 1], "win" => [0.02, 0.26], "depth" => 1.2],
    ["id" => "sate", "label" => "Sate Padang", "src" => "assets/img/menu_sate_daging.jpg", "size" => 16, "to" => ["x" => 32, "y" => -20, "r" => 6, "s" => 1], "win" => [0.06, 0.30], "depth" => 0.8],
    ["id" => "ayam-pop", "label" => "Ayam Pop", "src" => "assets/img/khas_ayam_pop.jpg", "size" => 15, "to" => ["x" => -36, "y" => 20, "r" => 5, "s" => 0.92], "win" => [0.10, 0.34], "depth" => 1.5],
    ["id" => "dendeng", "label" => "Dendeng Balado", "src" => "assets/img/khas_dendeng_balado.jpg", "size" => 16, "to" => ["x" => 35, "y" => 16, "r" => -6, "s" => 1.04], "win" => [0.14, 0.38], "depth" => 1.0],
    ["id" => "gulai", "label" => "Gulai Tunjang", "src" => "assets/img/khas_gulai_tunjang.jpg", "size" => 14, "to" => ["x" => -18, "y" => 33, "r" => -4, "s" => 0.88], "win" => [0.16, 0.40], "depth" => 0.6],
    ["id" => "kawa", "label" => "Kawa Daun", "src" => "assets/img/menu_kawa_daun.jpg", "size" => 13, "to" => ["x" => 18, "y" => 34, "r" => 7, "s" => 0.88], "win" => [0.18, 0.42], "depth" => 1.3],
    ["id" => "sanjai", "label" => "Keripik Sanjai", "src" => "assets/img/menu_sanjai.jpg", "size" => 13, "to" => ["x" => -22, "y" => -34, "r" => 10, "s" => 0.82], "win" => [0.20, 0.44], "depth" => 0.7],
    ["id" => "sala", "label" => "Sala Lauak", "src" => "assets/img/menu_sala_lauak.jpg", "size" => 12, "to" => ["x" => 15, "y" => -35, "r" => -10, "s" => 0.78], "win" => [0.22, 0.46], "depth" => 1.6]
];
?>

<section id="scroll-hero-track" class="scroll-hero-container" style="height: 360vh; position: relative;">
    <style>
        .scroll-hero-container {
            position: relative;
            background: #140406;
        }
        .scroll-hero-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sh-house {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            will-change: transform;
            transform-origin: 50% 65%;
            transform: scale(1.05);
        }
        .sh-maroon-overlay {
            position: absolute;
            inset: 0;
            background: rgba(58, 13, 18, 0.68);
        }
        .sh-vignette {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at center, transparent 32%, rgba(20, 4, 6, 0.82) 100%);
        }
        .sh-dim {
            position: absolute;
            inset: 0;
            background: #140406;
            opacity: 0.1;
            transition: opacity 0.1s linear;
        }
        .sh-glow {
            position: absolute;
            left: 50%;
            top: 40%;
            height: 480px;
            width: 780px;
            max-width: 92vw;
            border-radius: 9999px;
            filter: blur(52px);
            background: radial-gradient(circle, rgba(232, 178, 92, 0.38), transparent 70%);
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        /* Piring Makanan Melayang */
        .sh-plate-item {
            position: absolute;
            left: 50%;
            top: 50%;
            will-change: transform, opacity;
            pointer-events: none;
            opacity: 0;
            z-index: 15;
        }
        @keyframes sh-plate-float {
            from { transform: translateY(-7px) rotate(-1.5deg); }
            to   { transform: translateY(7px) rotate(1.5deg); }
        }
        .sh-plate-inner {
            width: 100%;
            height: 100%;
            animation: sh-plate-float 6s ease-in-out infinite alternate;
        }
        .plate-gold-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            overflow: hidden;
            background: #2a0a0e;
            box-shadow: 0 24px 50px rgba(0,0,0,0.65), 0 0 0 3px #E8B25C, 0 0 0 7px rgba(58,13,18,0.9), 0 0 0 9px rgba(232,178,92,0.55);
        }
        .plate-gold-ring img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            user-select: none;
            pointer-events: none;
        }
        .plate-shine {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 25%, rgba(255,255,255,0.22), transparent 48%);
            pointer-events: none;
        }
        .plate-tag {
            position: absolute;
            left: 50%;
            top: calc(100% + 12px);
            transform: translateX(-50%);
            white-space: nowrap;
            border-radius: 999px;
            background: rgba(20, 4, 6, 0.85);
            backdrop-filter: blur(8px);
            padding: 4px 14px;
            font-size: 0.76rem;
            font-weight: 700;
            color: var(--emas-terang);
            border: 1px solid rgba(247, 208, 112, 0.35);
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        /* Konten Hero Awal */
        .sh-initial-hero {
            position: absolute;
            inset: 0;
            z-index: 25;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 100px 24px 80px;
            text-align: center;
            will-change: transform, opacity;
        }

        /* Bab Cerita Bergantian */
        .sh-chapter {
            position: absolute;
            inset: 0;
            z-index: 20;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 28px;
            text-align: center;
            opacity: 0;
            pointer-events: none;
            visibility: hidden;
            will-change: transform, opacity;
        }
        .sh-chapter-kicker {
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--emas-utama);
            margin-bottom: 12px;
        }
        .sh-chapter-title {
            font-family: var(--font-heading);
            font-size: clamp(2rem, 4.2vw, 3.4rem);
            font-weight: 800;
            color: var(--emas-terang);
            max-width: 920px;
            line-height: 1.2;
            text-shadow: 0 4px 20px rgba(0,0,0,0.6);
        }
        .sh-chapter-text {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 680px;
            margin: 16px auto 0;
            line-height: 1.7;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }

        /* Indikator Scroll Vertikal */
        .sh-scroll-hint {
            position: absolute;
            bottom: 24px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.72rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.75);
            z-index: 30;
            pointer-events: none;
            animation: bounceScroll 2s infinite ease-in-out;
        }
        @keyframes bounceScroll {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(6px); }
        }
        .sh-rail {
            position: absolute;
            right: 22px;
            top: 100px;
            bottom: 60px;
            width: 2px;
            background: rgba(255, 255, 255, 0.15);
            z-index: 30;
            pointer-events: none;
            border-radius: 999px;
            overflow: hidden;
        }
        .sh-rail-fill {
            width: 100%;
            height: 100%;
            background: var(--emas-utama);
            transform-origin: top;
            transform: scaleY(0);
        }
        @media (max-width: 768px) {
            .sh-rail { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            .sh-plate-inner { animation: none !important; }
            .sh-scroll-hint { animation: none !important; }
        }
    </style>

    <div class="scroll-hero-sticky">
        <!-- 1. Background Rumah Gadang (Zoom ke arah Pintu) -->
        <div id="sh-house" class="sh-house" style="background-image: url('<?= $hero_bg; ?>');"></div>

        <!-- 2. Overlay Maroon Gelap + Vignette -->
        <div class="sh-maroon-overlay"></div>
        <div class="sh-vignette"></div>
        <div id="sh-dim" class="sh-dim"></div>

        <!-- 3. Glow Cahaya Hangat -->
        <div id="sh-glow" class="sh-glow"></div>

        <!-- 4. Piring-piring Makanan Keluar dari Pintu Rumah Gadang -->
        <?php foreach ($food_items as $idx => $it): ?>
            <div class="sh-plate-item" id="plate-item-<?= $idx; ?>" data-size="<?= $it['size']; ?>" data-win-a="<?= $it['win'][0]; ?>" data-win-b="<?= $it['win'][1]; ?>" data-to-x="<?= $it['to']['x']; ?>" data-to-y="<?= $it['to']['y']; ?>" data-to-r="<?= $it['to']['r']; ?>" data-to-s="<?= $it['to']['s']; ?>" data-depth="<?= $it['depth']; ?>">
                <div class="sh-plate-inner" style="animation-delay: <?= $idx * 0.4; ?>s;">
                    <div style="position: relative; width: 100%; height: 100%;">
                        <div class="plate-gold-ring">
                            <img src="<?= $it['src']; ?>" alt="<?= $it['label']; ?>" loading="eager" onerror="this.src='assets/img/default.png'">
                            <div class="plate-shine"></div>
                        </div>
                        <span class="plate-tag"><?= $it['label']; ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- 5. Konten Hero Utama (Memudar & naik saat scroll down) -->
        <div id="sh-initial-hero" class="sh-initial-hero">
            <div class="hero-badge"><i class="fa-solid fa-utensils"></i> Digitalisasi UMKM Kuliner Minangkabau</div>
            <h1 class="hero-title"><span class="hero-title-gradient">Peta Rasa Padang</span></h1>
            <p class="hero-tagline">Jelajahi Cita Rasa Autentik Ranah Minang Langsung dari Sentra Kuliner Kota Padang</p>
            <p class="hero-subtext">Temukan rumah makan legendaris, sate kuah rempah khas Pariaman, seduhan kawa daun hangat, hingga oleh-oleh khas Padang dalam etalase digital interaktif modern.</p>

            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number" id="stat-toko"><?= $total_toko; ?></span>
                    <span class="stat-label">Titik UMKM Kuliner</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number" id="stat-menu"><?= $total_menu; ?></span>
                    <span class="stat-label">Pilihan Menu Minang</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number">5</span>
                    <span class="stat-label">Kategori Kuliner</span>
                </div>
            </div>

            <div class="hero-actions">
                <a href="peta.php" class="btn btn-hero-primary"><i class="fa-solid fa-location-dot"></i> Buka Peta Interaktif</a>
                <a href="toko.php" class="btn btn-hero-secondary"><i class="fa-solid fa-utensils"></i> Jelajahi Daftar Toko</a>
            </div>
        </div>

        <!-- 6. Tiga Bab Cerita Interaktif saat Scroll Berlanjut -->
        <div class="sh-chapter" id="sh-chapter-0">
            <span class="sh-chapter-kicker"><i class="fa-solid fa-fire"></i> Warisan Leluhur Ranah Minang</span>
            <h2 class="sh-chapter-title">Rasa Otentik yang Lahir dari Kesabaran &amp; Rempah</h2>
            <p class="sh-chapter-text">Dari kawah rendang yang diaduk berjam-jam hingga kuah sate kuning pekat berempah jahe kunyit, setiap suapan mengisahkan tradisi ratusan tahun kebanggaan Minangkabau.</p>
        </div>

        <div class="sh-chapter" id="sh-chapter-1">
            <span class="sh-chapter-kicker"><i class="fa-solid fa-store"></i> Pemberdayaan Ekonomi Rakyat</span>
            <h2 class="sh-chapter-title">Mendukung 35+ Pelaku UMKM Lokal Mandiri</h2>
            <p class="sh-chapter-text">Menghubungkan penikmat kuliner langsung ke warung ampera, gerobak sate trotoar, sentra keripik sanjai, dan kedai kawa daun tanpa perantara.</p>
        </div>

        <div class="sh-chapter" id="sh-chapter-2">
            <span class="sh-chapter-kicker"><i class="fa-solid fa-compass"></i> Siap Menjelajah Kota Padang?</span>
            <h2 class="sh-chapter-title">Mulai Petualangan Kuliner Anda</h2>
            <p class="sh-chapter-text" style="margin-bottom: 24px;">Gunakan peta pintar kami untuk menemukan tempat makan terdekat, susun rute perjalanan, atau pesan langsung via WhatsApp.</p>
            <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                <a href="peta.php" class="btn btn-hero-primary" style="padding: 12px 28px; font-size: 0.95rem;">
                    <i class="fa-solid fa-map-location-dot"></i> Buka Peta Interaktif
                </a>
                <a href="smart-route.php" class="btn btn-hero-secondary" style="padding: 12px 28px; font-size: 0.95rem;">
                    <i class="fa-solid fa-route"></i> Susun Rute Pintar
                </a>
            </div>
        </div>

        <!-- 7. Indikator Scroll & Progress Rail -->
        <div id="sh-hint" class="sh-scroll-hint">
            <span>↓ Scroll ke Bawah untuk Membuka Rasa</span>
        </div>
        <div class="sh-rail">
            <div id="sh-rail-fill" class="sh-rail-fill"></div>
        </div>

        <!-- Ornamen Pembatas Songket di bagian paling bawah hero -->
        <div class="songket-border-bottom"></div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const track = document.getElementById('scroll-hero-track');
    const house = document.getElementById('sh-house');
    const dim = document.getElementById('sh-dim');
    const glow = document.getElementById('sh-glow');
    const initHero = document.getElementById('sh-initial-hero');
    const railFill = document.getElementById('sh-rail-fill');
    const hint = document.getElementById('sh-hint');
    const plates = Array.from(document.querySelectorAll('.sh-plate-item'));
    const chapters = [
        document.getElementById('sh-chapter-0'),
        document.getElementById('sh-chapter-1'),
        document.getElementById('sh-chapter-2')
    ];

    if (!track) return;

    // Cek preferensi aksesibilitas
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Titik awal pintu Rumah Gadang (ambang pintu tengah bawah)
    const DOOR = { x: 0, y: 14 };

    const clamp = (v, a = 0, b = 1) => Math.min(Math.max(v, a), b);
    const lerp = (a, b, t) => a + (b - a) * t;
    const easeInOut = (t) => t * t * (3 - 2 * t);
    const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);
    const easeOutBack = (t) => {
        const c1 = 1.4;
        const c3 = c1 + 1;
        return 1 + c3 * Math.pow(t - 1, 3) + c1 * Math.pow(t - 1, 2);
    };

    let vw = window.innerWidth;
    let vh = window.innerHeight;
    let target = 0;
    let cur = 0;
    let raf = 0;

    const measure = () => {
        vw = window.innerWidth;
        vh = window.innerHeight;
        plates.forEach(el => {
            const sizeVw = parseFloat(el.dataset.size) || 15;
            const px = clamp((sizeVw * vw) / 100, 110, 270);
            el.style.width = px + 'px';
            el.style.height = px + 'px';
            el.style.marginLeft = (-px / 2) + 'px';
            el.style.marginTop = (-px / 2) + 'px';
        });
    };

    const apply = (p) => {
        // 1. Rumah Gadang: Zoom bertahap ke arah pintu
        if (house) {
            const s = 1.05 + easeInOut(p) * 0.65;
            house.style.transform = `translate3d(0, ${-p * vh * 0.08}px, 0) scale(${s})`;
        }
        if (dim) {
            dim.style.opacity = (0.1 + easeInOut(clamp(p / 0.8)) * 0.55);
        }
        if (glow) {
            glow.style.opacity = (0.9 - easeInOut(clamp(p / 0.5)) * 0.9);
        }

        // 2. Konten hero awal memudar dan naik lembut
        if (initHero) {
            const c = easeInOut(clamp(p / 0.24));
            initHero.style.opacity = (1 - c);
            initHero.style.transform = `translate3d(0, ${-c * 70}px, 0) scale(${1 - c * 0.04})`;
            initHero.style.pointerEvents = (c > 0.4) ? 'none' : 'auto';
        }

        // 3. Piring-piring makanan: keluar dari pintu rumah gadang, melengkung ke posisi akhir
        plates.forEach(el => {
            const a = parseFloat(el.dataset.winA);
            const b = parseFloat(el.dataset.winB);
            const toX = parseFloat(el.dataset.toX);
            const toY = parseFloat(el.dataset.toY);
            const toR = parseFloat(el.dataset.toR);
            const toS = parseFloat(el.dataset.toS);
            const depth = parseFloat(el.dataset.depth) || 1.0;

            const raw = clamp((p - a) / (b - a));
            const t = reduced ? 1 : raw;
            const pp = reduced ? 0 : p;

            // Lintasan melengkung (x cepat, y menyusul)
            const tx = easeOutCubic(t);
            const ty = easeOutBack(clamp(t * 1.05));
            const ts = easeOutBack(clamp(t * 1.1));

            const fromR = toR + (toX < 0 ? -35 : 35);

            const x = (lerp(DOOR.x, toX, tx) * vw) / 100;
            const y = ((lerp(DOOR.y, toY, ty) - pp * depth * 22) * vh) / 100;
            const r = lerp(fromR, toR, tx) + pp * depth * 14;
            const s = lerp(0.06, toS, ts) * (1 + pp * 0.1 * depth);

            const outro = 1 - easeInOut(clamp((p - 0.86) / 0.14));
            el.style.opacity = (clamp(t * 3) * (reduced ? 1 : outro));
            el.style.transform = `translate3d(${x}px, ${y}px, 0) rotate(${r}deg) scale(${s})`;

            const tag = el.querySelector('.plate-tag');
            if (tag) tag.style.opacity = clamp((t - 0.7) / 0.3);
        });

        // 4. Bab cerita bergantian (Chapters)
        const start = 0.32;
        const span = (0.94 - start) / chapters.length;
        const lastIdx = chapters.length - 1;

        chapters.forEach((el, i) => {
            if (!el) return;
            const a = start + i * span;
            const local = clamp((p - a) / span);
            const isLast = (i === lastIdx);
            const fadeIn = clamp(local / 0.25);
            const fadeOut = isLast ? 1 : clamp((1 - local) / 0.25);
            const vis = easeInOut(Math.min(fadeIn, fadeOut));

            el.style.opacity = reduced ? 0 : vis;
            el.style.transform = `translate3d(0, ${isLast ? lerp(36, 0, easeOutCubic(fadeIn)) : lerp(36, -36, local)}px, 0)`;
            el.style.pointerEvents = (!reduced && vis > 0.6) ? 'auto' : 'none';
            el.style.visibility = (vis < 0.01) ? 'hidden' : 'visible';
        });

        // 5. Rail Progress & Hint
        if (railFill) railFill.style.transform = `scaleY(${p})`;
        if (hint) hint.style.opacity = (1 - clamp(p / 0.08));
    };

    const readTarget = () => {
        const rect = track.getBoundingClientRect();
        const total = track.offsetHeight - vh;
        target = total > 0 ? clamp(-rect.top / total) : 0;
    };

    // Physics Inersia: gerak lembut mengejar posisi scroll di dua arah (down & up)
    const tick = () => {
        cur += (target - cur) * 0.12;
        if (Math.abs(target - cur) < 0.0004) cur = target;
        apply(cur);
        if (cur !== target) {
            raf = requestAnimationFrame(tick);
        } else {
            raf = 0;
        }
    };

    const kick = () => {
        readTarget();
        if (!raf) raf = requestAnimationFrame(tick);
    };

    measure();

    if (reduced) {
        track.style.height = '100vh';
        apply(0);
        window.addEventListener('resize', measure);
        return;
    }

    readTarget();
    cur = target;
    apply(cur);

    window.addEventListener('scroll', kick, { passive: true });
    window.addEventListener('resize', () => {
        measure();
        kick();
    });
});
</script>
