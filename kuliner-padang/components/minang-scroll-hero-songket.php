<?php
/**
 * components/minang-scroll-hero-songket.php
 * Porting terintegrasi dari MinangScrollHero-songket.tsx untuk PHP Native
 * Efek Scroll-Scrubbed:
 * 1. Rumah Gadang di latar belakang melakukan zoom halus ke arah pintu
 * 2. Jalan naik dari bawah, dua Rangkiang masuk dari tepi kiri & kanan berdiri di atas jalan
 * 3. Motif Songket Minangkabau turun dari atas menjuntai selang-seling dengan benang emas & simpul
 * 4. Bab cerita bergantian (Peta Rasa Padang -> Warisan -> 35 Titik UMKM, Menu, & Tombol)
 */
$hero_bg = 'assets/img/rumah-gadang.jpg';
$songket_src = 'assets/img/songketminangkabau.png';
$road_src = 'assets/img/jalan.webp';
$rangkiang_left_src = 'assets/img/rangkiang-kiri.webp';
$rangkiang_right_src = 'assets/img/rangkiang-kanan.webp';

$songket_items = [
    ["id" => "r1", "label" => "Songket 1", "x" => 17, "len" => 14, "size" => 9, "flip" => false, "window" => [0.02, 0.24]],
    ["id" => "l1", "label" => "Songket 2", "x" => -17, "len" => 40, "size" => 8.5, "flip" => true, "window" => [0.05, 0.27]],
    ["id" => "r2", "label" => "Songket 3", "x" => 26, "len" => 36, "size" => 9.5, "flip" => true, "window" => [0.08, 0.3]],
    ["id" => "l2", "label" => "Songket 4", "x" => -26, "len" => 12, "size" => 9, "flip" => false, "window" => [0.11, 0.33]],
    ["id" => "r3", "label" => "Songket 5", "x" => 35, "len" => 22, "size" => 8.5, "flip" => false, "window" => [0.14, 0.36]],
    ["id" => "l3", "label" => "Songket 6", "x" => -35, "len" => 34, "size" => 9.5, "flip" => true, "window" => [0.17, 0.39]],
    ["id" => "r4", "label" => "Songket 7", "x" => 44, "len" => 44, "size" => 8, "flip" => true, "window" => [0.2, 0.42]],
    ["id" => "l4", "label" => "Songket 8", "x" => -44, "len" => 20, "size" => 8.5, "flip" => false, "window" => [0.23, 0.45]],
];
?>

<section id="scroll-hero-songket-track" class="scroll-hero-songket-container" style="height: 360vh; position: relative;">
    <style>
        .scroll-hero-songket-container {
            position: relative;
            background: #140406;
            width: 100%;
        }
        .shs-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .shs-house {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            will-change: transform;
            transform-origin: 50% 65%;
            transform: scale(1.05);
        }
        .shs-overlay {
            position: absolute;
            inset: 0;
            background: rgba(58, 13, 18, 0.68);
        }
        .shs-vignette {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at center, transparent 32%, rgba(20, 4, 6, 0.82) 100%);
        }
        .shs-dim {
            position: absolute;
            inset: 0;
            background: #140406;
            opacity: 0.1;
            transition: opacity 0.1s linear;
        }
        .shs-glow {
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

        /* Songket Menjuntai dari Atas */
        .shs-songket-item {
            position: absolute;
            top: 0;
            left: 50%;
            pointer-events: none;
            will-change: transform;
            transform-origin: top center;
            z-index: 15;
            visibility: hidden;
        }
        .shs-songket-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            height: 100%;
        }
        .shs-thread {
            width: 1.5px;
            flex-shrink: 0;
            background: linear-gradient(to bottom, rgba(232, 178, 92, 0.25), #E8B25C);
        }
        .shs-knot {
            margin-bottom: -3px;
            height: 7px;
            width: 7px;
            flex-shrink: 0;
            transform: rotate(45deg);
            background: #F4D58D;
            box-shadow: 0 0 8px rgba(244, 213, 141, 0.7);
        }
        .shs-motif {
            display: block;
            width: 100%;
            user-select: none;
            filter: drop-shadow(0 10px 16px rgba(0,0,0,0.55)) drop-shadow(0 0 10px rgba(232, 178, 92, 0.25));
        }

        /* Scene Bawah: Jalan & Dua Rangkiang
           Wadah ini harus selebar layar (left:0 + right:0), supaya rangkiang
           menempel di ujung kiri dan ujung kanan layar. */
        .shs-bottom-scene {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            pointer-events: none;
            z-index: 18;
            overflow: visible;
        }
        .shs-road {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            pointer-events: none;
            user-select: none;
            will-change: transform;
            filter: drop-shadow(0 -8px 24px rgba(0,0,0,0.4));
        }
        .shs-rangkiang {
            position: absolute;
            bottom: 0;
            pointer-events: none;
            user-select: none;
            will-change: transform;
            filter: drop-shadow(0 12px 24px rgba(0,0,0,0.6));
            z-index: 19;
        }
        .shs-rangkiang-left {
            left: 0;
        }
        .shs-rangkiang-right {
            right: 0;
        }

        /* Konten Hero Awal */
        .shs-initial-hero {
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
        .shs-chapter {
            position: absolute;
            inset: 0;
            z-index: 22;
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
        .shs-chapter-kicker {
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #F4D58D;
            margin-bottom: 12px;
            text-shadow: 0 0 12px rgba(244, 213, 141, 0.45);
        }
        .shs-chapter-title {
            font-family: var(--font-heading);
            font-size: clamp(2.2rem, 4.4vw, 3.6rem);
            font-weight: 900;
            line-height: 1.18;
            max-width: 920px;
            margin: 0 auto;
            background: linear-gradient(135deg, #FFF8EC 0%, #FDE4AA 25%, #E8B25C 50%, #FFD475 75%, #F4D58D 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 4px 16px rgba(0, 0, 0, 0.85)) drop-shadow(0 0 28px rgba(232, 178, 92, 0.4));
            animation: shsTitleShine 5s linear infinite, shsTitlePulse 3.5s ease-in-out infinite alternate;
        }
        @keyframes shsTitleShine {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes shsTitlePulse {
            0% {
                filter: drop-shadow(0 4px 16px rgba(0, 0, 0, 0.85)) drop-shadow(0 0 20px rgba(232, 178, 92, 0.3));
            }
            100% {
                filter: drop-shadow(0 6px 22px rgba(0, 0, 0, 0.95)) drop-shadow(0 0 38px rgba(244, 213, 141, 0.65));
            }
        }
        .shs-chapter-text {
            font-size: clamp(1.05rem, 1.4vw, 1.2rem);
            color: #FFFFFF;
            max-width: 740px;
            margin: 20px auto 0;
            line-height: 1.75;
            text-shadow: 0 2px 14px rgba(0, 0, 0, 0.85), 0 0 20px rgba(0, 0, 0, 0.6);
        }

        /* Efek Elegan Eksklusif pada Judul Hero Utama Peta Rasa Padang */
        .hero-title {
            margin-bottom: 16px !important;
        }
        .hero-title-gradient {
            background: linear-gradient(135deg, #FFFFFF 0%, #FFF4DE 35%, #F4D58D 70%, #E8B25C 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            filter: drop-shadow(0 4px 18px rgba(0, 0, 0, 0.75)) !important;
            letter-spacing: -0.02em !important;
            display: inline-block !important;
            animation: heroTitleFloat 4s ease-in-out infinite alternate !important;
        }
        @keyframes heroTitleFloat {
            0% {
                transform: translateY(0);
                filter: drop-shadow(0 4px 18px rgba(0, 0, 0, 0.75));
            }
            100% {
                transform: translateY(-3px);
                filter: drop-shadow(0 8px 24px rgba(232, 178, 92, 0.35));
            }
        }
        .shs-scroll-hint {
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
        .shs-rail {
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
        .shs-rail-fill {
            width: 100%;
            height: 100%;
            background: var(--emas-utama);
            transform-origin: top;
            transform: scaleY(0);
        }
        @media (max-width: 768px) {
            .shs-rail { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            .shs-scroll-hint { animation: none !important; }
        }
    </style>

    <div class="shs-sticky">
        <!-- 1. Background Rumah Gadang Zoom Dinamis -->
        <div id="shs-house" class="shs-house" style="background-image: url('<?= $hero_bg; ?>');"></div>

        <!-- 2. Overlay Maroon Gelap + Vignette -->
        <div class="shs-overlay"></div>
        <div class="shs-vignette"></div>
        <div id="shs-dim" class="shs-dim"></div>

        <!-- 3. Glow Emas Hangat -->
        <div id="shs-glow" class="shs-glow"></div>

        <!-- 4. Songket Menggantung Selang-Seling dari Atas -->
        <?php foreach ($songket_items as $idx => $it): ?>
            <div class="shs-songket-item" id="songket-item-<?= $idx; ?>" 
                 data-x="<?= $it['x']; ?>" 
                 data-len="<?= $it['len']; ?>" 
                 data-size="<?= $it['size']; ?>" 
                 data-win-a="<?= $it['window'][0]; ?>" 
                 data-win-b="<?= $it['window'][1]; ?>"
                 style="left: calc(50% + <?= $it['x']; ?>vw);">
                <div class="shs-songket-wrap">
                    <div class="shs-thread"></div>
                    <div class="shs-knot"></div>
                    <img src="<?= $songket_src; ?>" alt="<?= $it['label']; ?>" class="shs-motif" 
                         style="<?= $it['flip'] ? 'transform: scaleX(-1);' : ''; ?>" loading="eager">
                </div>
            </div>
        <?php endforeach; ?>

        <!-- 5. Scene Bawah: Jalan & Rangkiang Kiri-Kanan -->
        <div class="shs-bottom-scene">
            <div id="shs-road" class="shs-road" style="background-image: url('<?= $road_src; ?>'); background-size: 100% 100%; background-repeat: no-repeat; backface-visibility: hidden;"></div>
            <div id="shs-rangkiang-left" class="shs-rangkiang shs-rangkiang-left" style="background-image: url('<?= $rangkiang_left_src; ?>'); background-size: 100% 100%; background-repeat: no-repeat;"></div>
            <div id="shs-rangkiang-right" class="shs-rangkiang shs-rangkiang-right" style="background-image: url('<?= $rangkiang_right_src; ?>'); background-size: 100% 100%; background-repeat: no-repeat;"></div>
        </div>

        <!-- 6. Konten Hero Awal -->
        <div id="shs-initial-hero" class="shs-initial-hero">
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

        <!-- 7. Bab Cerita Bergantian -->
        <div class="shs-chapter" id="shs-chapter-0">
            <div class="shs-chapter-card">
                <span class="shs-chapter-kicker"><i class="fa-solid fa-pepper-hot"></i> Warisan Rasa &amp; Rempah Minangkabau</span>
                <h2 class="shs-chapter-title">Kelezatan Autentik Dapur Kuliner Ranah Minang</h2>
                <p class="shs-chapter-text">Dari kuali rendang beraroma rempah kelapa hingga gurihnya gulai kapau dan hangatnya seduhan kawa daun, setiap suapan menghadirkan cita rasa warisan yang mendunia.</p>
            </div>
        </div>

        <div class="shs-chapter" id="shs-chapter-1">
            <div class="shs-chapter-card">
                <span class="shs-chapter-kicker"><i class="fa-solid fa-store"></i> Simbol Kemakmuran UMKM</span>
                <h2 class="shs-chapter-title">Lumbung Pangan &amp; UMKM Kebanggaan Ranah Minang</h2>
                <p class="shs-chapter-text">Rangkiang melambangkan ketahanan dan kesejahteraan. Kami hadir mendampingi <span class="shs-accent-badge">35+ UMKM kuliner Padang</span> agar terus bertumbuh dan mandiri secara digital.</p>
            </div>
        </div>

        <div class="shs-chapter" id="shs-chapter-2">
            <div class="shs-chapter-card">
                <span class="shs-chapter-kicker"><i class="fa-solid fa-map-location-dot"></i> Siap Menjelajah Kota Padang?</span>
                <h2 class="shs-chapter-title">Temukan Kelezatan Terdekat Sekarang</h2>
                <p class="shs-chapter-text" style="margin-bottom: 24px;">Gunakan peta pintar kami untuk menemukan tempat makan terdekat, susun rute perjalanan, atau pesan langsung via WhatsApp.</p>
                <div style="display:flex; gap:14px; justify-content:center; flex-wrap:wrap; margin-top: 10px;">
                    <a href="peta.php" class="btn btn-hero-primary" style="padding: 13px 30px; font-size: 0.95rem; box-shadow: 0 8px 24px rgba(232, 178, 92, 0.35);">
                        <i class="fa-solid fa-map-location-dot"></i> Buka Peta Interaktif
                    </a>
                    <a href="smart-route.php" class="btn btn-hero-secondary" style="padding: 13px 30px; font-size: 0.95rem;">
                        <i class="fa-solid fa-route"></i> Susun Rute Pintar
                    </a>
                </div>
            </div>
        </div>

        <!-- 8. Indikator Scroll & Progress Rail -->
        <div id="shs-hint" class="shs-scroll-hint">
            <span>↓ Scroll ke Bawah untuk Menjelajah</span>
        </div>
        <div class="shs-rail">
            <div id="shs-rail-fill" class="shs-rail-fill"></div>
        </div>

        <!-- Ornamen Pembatas Songket -->
        <div class="songket-border-bottom"></div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const track = document.getElementById('scroll-hero-songket-track');
    const house = document.getElementById('shs-house');
    const dim = document.getElementById('shs-dim');
    const glow = document.getElementById('shs-glow');
    const initHero = document.getElementById('shs-initial-hero');
    const road = document.getElementById('shs-road');
    const rangLeft = document.getElementById('shs-rangkiang-left');
    const rangRight = document.getElementById('shs-rangkiang-right');
    const railFill = document.getElementById('shs-rail-fill');
    const hint = document.getElementById('shs-hint');
    const songketEls = Array.from(document.querySelectorAll('.shs-songket-item'));
    const chapters = [
        document.getElementById('shs-chapter-0'),
        document.getElementById('shs-chapter-1'),
        document.getElementById('shs-chapter-2')
    ];

    if (!track) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const clamp = (v, a = 0, b = 1) => Math.min(Math.max(v, a), b);
    const lerp = (a, b, t) => a + (b - a) * t;
    const easeInOut = (t) => t * t * (3 - 2 * t);
    const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);
    const easeInOutCubic = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

    const MOTIF_RATIO = 1.49;
    const ROAD_RATIO = 152 / 2128;
    const LEFT_RATIO = 975 / 360;
    const RIGHT_RATIO = 975 / 357;

    const ROAD_MAX_VH = 0.08;   // tinggi jalan diperkecil: maksimal 8% tinggi layar, lebar tetap 100%
    let roadRatio = ROAD_RATIO; // diganti otomatis dengan rasio asli file

    /* ===== PENGATURAN UKURAN (ubah angka di sini saja) ===== */
    // Ukuran rangkiang: 1 = standar, 1.3 = 30% lebih besar, 0.8 = 20% lebih kecil
    const SIZE_LEFT = 1;
    const SIZE_RIGHT = 1;
    /* ======================================================= */

    let vw = window.innerWidth;
    let vh = window.innerHeight;
    let target = 0;
    let cur = 0;
    let raf = 0;

    let scene = { roadH: 0, wl: 0, wr: 0 };
    let songketHeights = [];

    const measure = () => {
        vw = window.innerWidth;
        vh = window.innerHeight;

        const cw = track.clientWidth || vw;
        const roadW = cw;
        const roadH = Math.min(roadW * roadRatio, vh * ROAD_MAX_VH);
        const houseH = Math.min(vh * 0.5, cw * 0.3 * LEFT_RATIO);
        const hl = houseH * SIZE_LEFT;
        const hr = houseH * SIZE_RIGHT;
        const wl = hl / LEFT_RATIO;
        const wr = hr / RIGHT_RATIO;
        scene = { roadH, wl, wr };

        if (road) {
            road.style.height = roadH + 'px';
        }
        const footY = roadH * 0.42;
        if (rangLeft) {
            rangLeft.style.height = hl + 'px';
            rangLeft.style.width = wl + 'px';
            rangLeft.style.bottom = footY + 'px';
        }
        if (rangRight) {
            rangRight.style.height = hr + 'px';
            rangRight.style.width = wr + 'px';
            rangRight.style.bottom = footY + 'px';
        }

        songketEls.forEach((el, i) => {
            const sizeVw = parseFloat(el.dataset.size) || 9;
            const lenVh = parseFloat(el.dataset.len) || 20;
            const px = clamp((sizeVw * vw) / 100, 52, 170);
            const lenPx = (lenVh * vh) / 100;
            el.style.width = px + 'px';
            el.style.marginLeft = (-px / 2) + 'px';

            const thread = el.querySelector('.shs-thread');
            if (thread) thread.style.height = lenPx + 'px';

            songketHeights[i] = lenPx + 4 + px * MOTIF_RATIO;
        });
    };

    const apply = (p) => {
        // 1. Rumah Gadang Zoom
        if (house) {
            const s = 1.05 + easeInOut(p) * 0.6;
            house.style.transform = `translate3d(0, ${-p * vh * 0.08}px, 0) scale(${s})`;
        }
        if (dim) {
            dim.style.opacity = (0.1 + easeInOut(clamp(p / 0.8)) * 0.55);
        }
        if (glow) {
            glow.style.opacity = (0.9 - easeInOut(clamp(p / 0.5)) * 0.9);
        }

        // 2. Konten Hero Awal Memudar & Naik
        if (initHero) {
            const c = easeInOut(clamp(p / 0.26));
            initHero.style.opacity = (1 - c);
            initHero.style.transform = `translate3d(0, ${-c * 70}px, 0) scale(${1 - c * 0.04})`;
            initHero.style.pointerEvents = (c > 0.4) ? 'none' : 'auto';
        }

        // 3. Scene Bawah: Jalan naik dari bawah, Rangkiang masuk dari kiri & kanan
        {
            const { roadH, wl, wr } = scene;
            const roadIn = reduced ? 1 : easeInOutCubic(clamp(p / 0.22));
            const roadOut = reduced ? 0 : easeInOutCubic(clamp((p - 0.86) / 0.14));
            if (road) {
                const y = (roadH + 48) * (1 - roadIn + roadOut);
                road.style.transform = `translate3d(0, ${y}px, 0)`;
                road.style.visibility = (roadIn <= 0 || roadOut >= 1) ? 'hidden' : 'visible';
            }

            // Rangkiang kiri masuk dari luar layar dan menempel di margin luar kiri
            // masuk: melambat saat tiba (easeOutCubic) supaya terasa "mendarat" di tepi layar
            const leftIn = reduced ? 1 : easeOutCubic(clamp((p - 0.1) / 0.26));
            const rightIn = reduced ? 1 : easeOutCubic(clamp((p - 0.14) / 0.26));
            const leftOut = reduced ? 0 : easeInOutCubic(clamp((p - 0.8) / 0.12));
            const rightOut = reduced ? 0 : easeInOutCubic(clamp((p - 0.82) / 0.12));

            if (rangLeft) {
                const slide = Math.max(0, 1 - leftIn + leftOut);
                // +56px: bayangan ikut tersembunyi di luar layar; tepi potong tidak pernah melewati tepi kiri ke dalam
                const x = -(wl + 56) * slide;
                // sedikit naik dari bawah saat masuk, supaya gerakan tidak kaku
                const y = 28 * slide;
                rangLeft.style.transform = `translate3d(${x}px, ${y}px, 0)`;
                rangLeft.style.visibility = (leftIn <= 0 || leftOut >= 1) ? 'hidden' : 'visible';
            }

            // Rangkiang kanan masuk dari luar layar dan menempel di margin luar kanan
            if (rangRight) {
                const slide = Math.max(0, 1 - rightIn + rightOut);
                const x = (wr + 56) * slide;
                const y = 28 * slide;
                rangRight.style.transform = `translate3d(${x}px, ${y}px, 0)`;
                rangRight.style.visibility = (rightIn <= 0 || rightOut >= 1) ? 'hidden' : 'visible';
            }
        }

        // 4. Songket: Turun dari atas lalu ditarik naik saat hero selesai
        const n = songketEls.length;
        songketEls.forEach((el, i) => {
            const winA = parseFloat(el.dataset.winA);
            const winB = parseFloat(el.dataset.winB);
            const posX = parseFloat(el.dataset.x);
            const H = (songketHeights[i] || 400) + 24;
            const dir = (posX < 0) ? -1 : 1;

            const t = reduced ? 1 : clamp((p - winA) / (winB - winA));
            const enter = easeInOutCubic(t);

            const exitStart = 0.8 + (n > 1 ? (i / (n - 1)) * 0.06 : 0);
            const ex = reduced ? 0 : easeInOutCubic(clamp((p - exitStart) / 0.14));

            const y = -H * (1 - enter) - H * ex;
            const swing = dir * 7 * Math.pow(1 - t, 1.6) * Math.cos(t * Math.PI * 2.5);
            const pull = dir * 3.5 * Math.sin(ex * Math.PI);

            el.style.transform = `translate3d(0, ${y}px, 0) rotate(${swing + pull}deg)`;
            el.style.visibility = (t <= 0 || ex >= 1) ? 'hidden' : 'visible';
        });

        // 5. Bab Cerita Bergantian
        const start = 0.34;
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

            if (vis > 0.45) {
                el.classList.add('active-step');
            } else {
                el.classList.remove('active-step');
            }
        });

        // 6. Rail Progress & Hint
        if (railFill) railFill.style.transform = `scaleY(${p})`;
        if (hint) hint.style.opacity = (1 - clamp(p / 0.08));
    };

    const readTarget = () => {
        const rect = track.getBoundingClientRect();
        const total = track.offsetHeight - vh;
        target = total > 0 ? clamp(-rect.top / total) : 0;
    };

    let last = 0;
    const tick = (now) => {
        const dt = last ? Math.min((now - last) / 1000, 0.05) : 1 / 60;
        last = now;
        cur += (target - cur) * (1 - Math.exp(-dt * 7));
        if (Math.abs(target - cur) < 0.0002) cur = target;
        apply(cur);
        if (cur !== target) {
            raf = requestAnimationFrame(tick);
        } else {
            raf = 0;
            last = 0;
        }
    };

    const kick = () => {
        readTarget();
        if (!raf) raf = requestAnimationFrame(tick);
    };

    const roadProbe = new Image();
    roadProbe.onload = () => {
        if (roadProbe.naturalWidth > 0) {
            roadRatio = roadProbe.naturalHeight / roadProbe.naturalWidth;
            measure();
            apply(cur);
        }
    };
    roadProbe.src = '<?= $road_src; ?>';

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