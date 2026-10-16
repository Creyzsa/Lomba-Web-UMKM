<?php
/**
 * peta.php — Halaman Peta Kuliner Interaktif Kota Padang
 * Fitur: Leaflet 1.9.4, Pin Kategori Gonjong, Popup Menu & Harga, Search, Filter Kategori, Fly-To Toko
 */
session_start();
require_once 'koneksi.php';

$active_nav = 'peta';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Kuliner Interaktif — Peta Rasa Padang</title>
    
    <!-- Google Fonts: Cinzel & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS 1.9.4 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
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
            <span class="hero-badge">GEOGRAPHIC CULINARY MAP</span>
            <h1 class="page-banner-title">Peta Interaktif Kuliner Padang</h1>
            <p class="page-banner-desc">Temukan titik koordinat sentra kuliner Kota Padang secara presisi. Klik pin untuk melihat ulasan menu, harga, navigasi Google Maps, dan kontak WhatsApp.</p>
        </div>
        <div class="songket-border-bottom"></div>
    </section>

    <!-- Main Map Section -->
    <section class="section-container" style="margin-top: 40px;">
        <!-- Filter & Search Bar Kontrol -->
        <div class="map-controls-card">
            <div class="search-box-wrapper">
                <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" id="input-search" class="search-input" placeholder="Cari nama toko, menu (rendang, sate, kopi...), atau alamat...">
            </div>

            <div class="category-filters" id="category-filter-group">
                <button class="filter-btn active" data-category="Semua">Semua Kategori</button>
                <button class="filter-btn" data-category="Rumah Makan"><i class="fa-solid fa-bowl-rice"></i> Rumah Makan</button>
                <button class="filter-btn" data-category="Sate"><i class="fa-solid fa-stroopwafel"></i> Sate</button>
                <button class="filter-btn" data-category="Kopi"><i class="fa-solid fa-mug-hot"></i> Kopi & Kawa</button>
                <button class="filter-btn" data-category="Oleh-oleh"><i class="fa-solid fa-gift"></i> Oleh-oleh</button>
                <button class="filter-btn" data-category="Camilan"><i class="fa-solid fa-cookie-bite"></i> Camilan</button>
            </div>
        </div>

        <!-- Container Peta Leaflet -->
        <div class="map-wrapper">
            <div id="map" class="main-map"></div>
            <div class="map-legend">
                <div class="legend-item"><span class="legend-icon rm-icon"><i class="fa-solid fa-bowl-rice"></i></span> Rumah Makan</div>
                <div class="legend-item"><span class="legend-icon sate-icon"><i class="fa-solid fa-stroopwafel"></i></span> Sate Padang</div>
                <div class="legend-item"><span class="legend-icon kopi-icon"><i class="fa-solid fa-mug-hot"></i></span> Kedai Kopi</div>
                <div class="legend-item"><span class="legend-icon oleh-icon"><i class="fa-solid fa-gift"></i></span> Oleh-oleh</div>
                <div class="legend-item"><span class="legend-icon camilan-icon"><i class="fa-solid fa-cookie-bite"></i></span> Camilan</div>
            </div>
        </div>

        <!-- Section Etalase Kartu Toko (Sinkron dengan Peta) -->
        <div style="margin-top: 50px;">
            <div class="section-header">
                <div class="header-left">
                    <span class="section-kicker">SOROT DI PETA</span>
                    <h2 class="section-heading">Toko & Kedai Kuliner Terpilih</h2>
                    <p class="section-subtext">Klik tombol "Sorot di Peta" pada kartu di bawah ini untuk mengarahkan kamera peta ke titik lokasi toko (Fly-to).</p>
                </div>
                <div class="header-right">
                    <span id="counter-toko-tampil" class="badge-total-view">Memuat toko...</span>
                </div>
            </div>

            <!-- Grid Container Toko -->
            <div class="toko-grid" id="toko-grid-container">
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Leaflet JS 1.9.4 -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Script Interaktif Peta & Filter -->
    <script src="assets/js/peta.js"></script>

    <script>
        // Tangani parameter URL (?kategori=... atau ?toko=...)
        document.addEventListener('DOMContentLoaded', function () {
            const params = new URLSearchParams(window.location.search);
            const katParam = params.get('kategori');
            if (katParam && window.filterByCat) {
                setTimeout(() => {
                    window.filterByCat(katParam);
                }, 500);
            }
        });
    </script>
</body>
</html>
