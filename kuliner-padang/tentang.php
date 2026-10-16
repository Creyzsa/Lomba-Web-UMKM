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
                        <span>◆</span><span>◆</span><span class="center-diamond">❖</span>
                    </div>
                    <p><strong>Peta Rasa Padang</strong> dikembangkan sebagai partisipasi aktif dalam ajang <strong>IT FEST 2026 – Web Programming Competition</strong> yang diselenggarakan oleh <strong>UIN Mahmud Yunus Batusangkar</strong>.</p>
                    <p>Mengusung tema <em>"Digitalisasi UMKM: Membangun Etalase Digital yang Menarik, Interaktif, dan Fungsional"</em>, platform ini memadukan teknologi pemetaan terbuka (OpenStreetMap & Leaflet) dengan kekayaan warisan kuliner Minangkabau.</p>
                    <p>Sistem ini memberikan ruang bagi pelaku usaha kuliner (rumah makan tradisional, pedagang sate, kedai kopi/kawa daun, hingga sentra keripik sanjai) untuk menampilkan identitas produk, transparansi harga, koordinat presisi, serta saluran pemesanan online dalam satu wadah digital terintegrasi.</p>
                    
                    <div class="features-list">
                        <div class="feature-badge">✓ Peta Interaktif Presisi Geospasial</div>
                        <div class="feature-badge">✓ Katalog Menu & Transparansi Harga</div>
                        <div class="feature-badge">✓ UI/UX Bernuansa Minangkabau Modern</div>
                        <div class="feature-badge">✓ Panel CRUD Pengelolaan UMKM Terpadu</div>
                    </div>
                </div>

                <div class="about-visual">
                    <div class="gonjong-box">
                        <div class="gonjong-roof">🏛️</div>
                        <h3>Peta Rasa Padang</h3>
                        <p>Karya Inovasi Lomba IT FEST 2026<br>Web Programming Competition<br><strong>UIN Mahmud Yunus Batusangkar</strong></p>
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
                    <span>◆</span><span>◆</span><span class="center-diamond">❖</span><span>◆</span><span>◆</span>
                </div>
            </div>

            <div class="category-cards-grid">
                <div class="category-feature-card" style="cursor:default;">
                    <span class="cat-card-icon">🗺️</span>
                    <h3>Interaktif & Geospasial</h3>
                    <p>Memudahkan wisatawan lokal dan mancanegara menemukan lokasi autentik kedai kuliner hingga ke sudut gang sejarah Kota Tua dan Pantai Padang.</p>
                </div>
                <div class="category-feature-card" style="cursor:default;">
                    <span class="cat-card-icon">⚡</span>
                    <h3>Fungsional & Terbuka</h3>
                    <p>Dibangun dengan arsitektur PHP native dan MySQL yang ringan, aman dengan prepared statement, serta responsif di semua perangkat (HP, tablet, PC).</p>
                </div>
                <div class="category-feature-card" style="cursor:default;">
                    <span class="cat-card-icon">🎨</span>
                    <h3>Estetika Budaya Minang</h3>
                    <p>Memadukan warna marun, emas ukiran songket, serta siluet gonjong dengan kaidah desain web modern (glassmorphism dan micro-interactions).</p>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="text-center" style="margin-top: 50px; margin-bottom: 30px;">
            <a href="peta.php" class="btn btn-primary" style="padding: 14px 28px; font-size: 1rem;">📍 Mulai Eksplorasi Peta Kuliner</a>
            <a href="toko.php" class="btn btn-outline-gold" style="color:var(--merah-utama); border-color:var(--merah-utama); padding: 14px 28px; font-size: 1rem; margin-left:12px;">🍽️ Lihat Daftar Toko</a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
