<?php
/**
 * tentang.php — Halaman Tentang Inovasi Peta Rasa Padang
 * Tema: Minang Modern Luxury (IT FEST 2026 - UIN Mahmud Yunus Batusangkar)
 */
session_start();
require_once 'koneksi.php';

$active_nav = 'tentang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Inovasi — Peta Rasa Padang</title>
    
    <!-- Google Fonts: Cinzel & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Utama Tema Minang Modern -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Subpage Banner Header -->
    <section class="page-banner">
        <div class="page-banner-overlay"></div>
        <div class="page-banner-content">
            <span class="hero-badge">ABOUT INNOVATION</span>
            <h1 class="page-banner-title">Tentang Peta Rasa Padang</h1>
            <p class="page-banner-desc">Inovasi etalase digital geospasial kuliner Minangkabau untuk mendukung kemandirian, visibilitas, dan keberlanjutan UMKM lokal di Kota Padang.</p>
        </div>
        <div class="songket-border-bottom"></div>
    </section>

    <!-- Main Content Section -->
    <section class="section-container" style="margin-top: 50px;">
        <div class="about-card">
            <div class="about-grid">
                <div class="about-info">
                    <span class="section-kicker text-gold">LATAR BELAKANG INOVASI</span>
                    <h2 class="about-title">Digitalisasi Etalase UMKM Kuliner Ranah Minang</h2>
                    <div class="minang-divider-left">
                        <span>◆</span><span>◆</span><span class="center-diamond"><i class="fa-solid fa-diamond text-gold"></i></span>
                    </div>
                    <p><strong>Peta Rasa Padang</strong> dikembangkan sebagai platform etalase digital interaktif yang memberdayakan pelaku <strong>UMKM Kuliner Ranah Minang</strong> agar lebih mudah ditemukan dan dijangkau oleh wisatawan maupun masyarakat luas.</p>
                    <p>Mengusung tema <em>"Digitalisasi UMKM: Membangun Etalase Digital yang Menarik, Interaktif, dan Fungsional"</em>, platform ini memadukan teknologi pemetaan terbuka (OpenStreetMap & Leaflet) dengan kekayaan warisan kuliner Minangkabau.</p>
                    <p>Sistem ini memberikan ruang bagi pelaku usaha kuliner (rumah makan tradisional, pedagang sate, kedai kopi/kawa daun, hingga sentra keripik sanjai) untuk menampilkan identitas produk, transparansi harga, koordinat presisi, serta saluran pemesanan online dalam satu wadah digital terintegrasi.</p>
                    
                    <div class="features-list">
                        <div class="feature-badge"><i class="fa-solid fa-check text-gold"></i> Peta Interaktif Presisi Geospasial</div>
                        <div class="feature-badge"><i class="fa-solid fa-check text-gold"></i> Katalog Menu & Transparansi Harga</div>
                        <div class="feature-badge"><i class="fa-solid fa-check text-gold"></i> UI/UX Bernuansa Minangkabau Modern</div>
                        <div class="feature-badge"><i class="fa-solid fa-check text-gold"></i> Rekomendasi Rute & Budget Otomatis</div>
                    </div>

                    <!-- Kontak Admin WhatsApp -->
                    <div style="margin-top:28px; padding:18px; background:#FAF8F2; border-left:4px solid var(--emas-utama); border-radius:8px;">
                        <h4 style="font-family:var(--font-heading); color:var(--merah-utama); margin-bottom:6px; font-size:1.05rem;">
                            <i class="fa-brands fa-whatsapp" style="color:#22c55e;"></i> Layanan Bantuan &amp; Pendaftaran UMKM
                        </h4>
                        <p style="font-size:0.88rem; color:#475569; margin-bottom:12px;">Untuk pendaftaran toko baru, verifikasi data, atau informasi kemitraan, silakan hubungi tim pengelola:</p>
                        <div style="display:flex; flex-wrap:wrap; gap:12px;">
                            <a href="https://wa.me/6282286908467?text=<?= urlencode('Halo Admin Nindy, saya ingin mendaftarkan toko kuliner di Peta Rasa Padang.'); ?>" target="_blank" class="btn btn-sm" style="background:#16a34a; color:#fff;">
                                <i class="fa-brands fa-whatsapp"></i> Chat Nindy (+62 822-8690-8467)
                            </a>
                            <a href="https://wa.me/6282285417709?text=<?= urlencode('Halo Admin Cha, saya ingin mendaftarkan toko kuliner di Peta Rasa Padang.'); ?>" target="_blank" class="btn btn-sm" style="background:#16a34a; color:#fff;">
                                <i class="fa-brands fa-whatsapp"></i> Chat Cha (+62 822-8541-7709)
                            </a>
                        </div>
                    </div>
                </div>

                <div class="about-visual">
                    <div class="gonjong-box">
                        <div class="gonjong-roof"><img src="assets/img/logo-minang.svg" alt="Logo Peta Rasa Padang" style="width:54px;height:54px;"></div>
                        <h3>Peta Rasa Padang</h3>
                        <p>Platform Inovasi Digital UMKM<br>Pemberdayaan Kuliner Tradisional<br><strong>Kota Padang & Ranah Minang</strong></p>
                        <div class="badge-award">Tema: Digitalisasi UMKM</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Pilar Keunggulan -->
        <div style="margin-top: 60px;">
            <div class="section-header text-center">
                <span class="section-kicker">PILAR TEKNOLOGI & DAMPAK</span>
                <h2 class="section-heading">Tiga Pilar Utama Sistem</h2>
                <div class="minang-divider">
                    <span>◆</span><span>◆</span><span class="center-diamond"><i class="fa-solid fa-diamond text-gold"></i></span><span>◆</span><span>◆</span>
                </div>
            </div>

            <div class="category-cards-grid">
                <div class="category-feature-card" style="cursor:default;">
                    <span class="cat-card-icon"><i class="fa-solid fa-map-location-dot"></i></span>
                    <h3>Interaktif & Geospasial</h3>
                    <p>Memudahkan wisatawan lokal dan mancanegara menemukan lokasi autentik kedai kuliner hingga ke sudut gang sejarah Kota Tua dan Pantai Padang.</p>
                </div>
                <div class="category-feature-card" style="cursor:default;">
                    <span class="cat-card-icon"><i class="fa-solid fa-bolt"></i></span>
                    <h3>Fungsional & Terbuka</h3>
                    <p>Dibangun dengan arsitektur PHP native dan MySQL yang ringan, aman dengan prepared statement, serta responsif di semua perangkat (HP, tablet, PC).</p>
                </div>
                <div class="category-feature-card" style="cursor:default;">
                    <span class="cat-card-icon"><i class="fa-solid fa-palette"></i></span>
                    <h3>Estetika Budaya Minang</h3>
                    <p>Memadukan warna marun, emas ukiran songket, serta siluet gonjong dengan kaidah desain web modern (glassmorphism dan micro-interactions).</p>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="text-center" style="margin-top: 50px; margin-bottom: 30px;">
            <a href="peta.php" class="btn btn-primary" style="padding: 14px 28px; font-size: 1rem;"><i class="fa-solid fa-location-dot"></i> Mulai Eksplorasi Peta Kuliner</a>
            <a href="toko.php" class="btn btn-outline-gold" style="color:var(--merah-utama); border-color:var(--merah-utama); padding: 14px 28px; font-size: 1rem; margin-left:12px;"><i class="fa-solid fa-utensils"></i> Lihat Daftar Toko</a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
