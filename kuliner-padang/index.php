<?php
/**
 * index.php — Beranda Peta Kuliner Padang
 * Tema: Minang (Peta Rasa Padang)
 * Fitur: Hero siluet Rumah Gadang, Leaflet Map 1.9.4, Filter Kategori, Search Box, Grid Kartu Toko, Modal Menu, Footer Minang
 */
session_start();
require_once 'koneksi.php';

// Ambil statistik ringkas untuk header/counter
$res_stat_toko = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM toko");
$total_toko = ($res_stat_toko) ? mysqli_fetch_assoc($res_stat_toko)['total'] : 0;

$res_stat_menu = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM menu");
$total_menu = ($res_stat_menu) ? mysqli_fetch_assoc($res_stat_menu)['total'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Rasa Padang — Etalase Digital Kuliner Minangkabau</title>
    
    <!-- Google Fonts: Cinzel (Heading Klasik/Minang vibe) & Plus Jakarta Sans (Body modern) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS 1.9.4 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <!-- CSS Utama Tema Minang -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar Interaktif Minang -->
    <header class="navbar" id="main-navbar">
        <div class="nav-container">
            <a href="index.php" class="brand">
                <span class="brand-icon">🏛️</span>
                <div class="brand-text">
                    <span class="brand-title">Peta Rasa Padang</span>
                    <span class="brand-tagline">Etalase Kuliner Ranah Minang</span>
                </div>
            </a>
            
            <nav class="nav-links">
                <a href="#peta-section" class="nav-link">Peta Kuliner</a>
                <a href="#toko-section" class="nav-link">Daftar Toko</a>
                <a href="#tentang-section" class="nav-link">Tentang</a>
                
                <?php if (isset($_SESSION['role'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin/dashboard.php" class="btn btn-gold btn-sm">⚙️ Panel Admin</a>
                    <?php else: ?>
                        <span class="user-greeting">Halo, <?= htmlspecialchars($_SESSION['username']); ?></span>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-outline-gold btn-sm">Keluar</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-gold btn-sm">Masuk</a>
                    <a href="register.php" class="btn btn-gold btn-sm">Daftar</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section dengan Siluet Gonjong Rumah Gadang -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-badge">🏆 IT FEST 2026 — Digitalisasi UMKM Kuliner</div>
            <h1 class="hero-title">Peta Rasa Padang</h1>
            <p class="hero-tagline">Jelajahi Cita Rasa Autentik Ranah Minang Langsung dari Sentra Kuliner Kota Padang</p>
            <p class="hero-subtext">Temukan rumah makan legendaris, sate kuah rempah, seduhan kawa daun hangat, hingga oleh-oleh khas Padang dalam satu etalase interaktif.</p>
            
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
                <a href="#peta-section" class="btn btn-hero-primary">📍 Eksplorasi Peta Sekarang</a>
                <a href="#toko-section" class="btn btn-hero-secondary">🍽️ Lihat Semua Toko</a>
            </div>
        </div>

        <!-- Ornamen Pembatas Songket / Gonjong -->
        <div class="songket-border-bottom"></div>
    </section>

    <!-- Section Peta Interaktif -->
    <section class="section-container" id="peta-section">
        <div class="section-header text-center">
            <span class="section-kicker">INTERACTIVE CULINARY MAP</span>
            <h2 class="section-heading">Peta Interaktif Kuliner Kota Padang</h2>
            <div class="minang-divider">
                <span>◆</span><span>◆</span><span class="center-diamond">❖</span><span>◆</span><span>◆</span>
            </div>
            <p class="section-subtext">Klik pin di peta untuk melihat nama toko, ulasan menu andalan, dan rincian harga. Gunakan tombol filter di bawah untuk memudahkan pencarian.</p>
        </div>

        <!-- Filter & Search Bar Kontrol -->
        <div class="map-controls-card">
            <div class="search-box-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" id="input-search" class="search-input" placeholder="Cari nama toko, menu (rendang, sate, kopi...), atau alamat...">
            </div>

            <div class="category-filters" id="category-filter-group">
                <button class="filter-btn active" data-category="Semua">Semua Kategori</button>
                <button class="filter-btn" data-category="Rumah Makan">🍛 Rumah Makan</button>
                <button class="filter-btn" data-category="Sate">🍢 Sate</button>
                <button class="filter-btn" data-category="Kopi">☕ Kopi & Kawa</button>
                <button class="filter-btn" data-category="Oleh-oleh">🎁 Oleh-oleh</button>
                <button class="filter-btn" data-category="Camilan">🥮 Camilan</button>
            </div>
        </div>

        <!-- Container Peta Leaflet -->
        <div class="map-wrapper">
            <div id="map" class="main-map"></div>
            <div class="map-legend">
                <div class="legend-item"><span class="legend-icon rm-icon">🍛</span> Rumah Makan</div>
                <div class="legend-item"><span class="legend-icon sate-icon">🍢</span> Sate Padang</div>
                <div class="legend-item"><span class="legend-icon kopi-icon">☕</span> Kedai Kopi</div>
                <div class="legend-item"><span class="legend-icon oleh-icon">🎁</span> Oleh-oleh</div>
                <div class="legend-item"><span class="legend-icon camilan-icon">🥮</span> Camilan</div>
            </div>
        </div>
    </section>

    <!-- Ornamen Songket Strip -->
    <div class="songket-strip"></div>

    <!-- Section Grid Kartu Toko -->
    <section class="section-container" id="toko-section">
        <div class="section-header">
            <div class="header-left">
                <span class="section-kicker">DAFTAR ETALASE UMKM</span>
                <h2 class="section-heading">Jelajahi Toko & Kedai Pilihan</h2>
                <p class="section-subtext">Klik pada salah satu kartu toko untuk menyorot lokasinya langsung di peta (Fly-to).</p>
            </div>
            <div class="header-right">
                <span id="counter-toko-tampil" class="badge-total-view">Memuat toko...</span>
            </div>
        </div>

        <!-- Grid Container Toko (akan diisi dinamis via JavaScript dari api_toko.php) -->
        <div class="toko-grid" id="toko-grid-container">
            <!-- Loading Skeleton -->
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
        </div>
    </section>

    <!-- Section Tentang & Profil Singkat Project -->
    <section class="section-container about-section" id="tentang-section">
        <div class="about-card">
            <div class="about-grid">
                <div class="about-info">
                    <span class="section-kicker text-gold">TENTANG INOVASI KAMI</span>
                    <h2 class="about-title">Digitalisasi UMKM Kuliner Tradisional Padang</h2>
                    <div class="minang-divider-left">
                        <span>◆</span><span>◆</span><span class="center-diamond">❖</span>
                    </div>
                    <p><strong>Peta Rasa Padang</strong> dirancang sebagai solusi etalase digital interaktif yang menjembatani para pelaku usaha kuliner lokal dengan wisatawan dan masyarakat pecinta kuliner Ranah Minang.</p>
                    <p>Melalui integrasi OpenStreetMap dan Leaflet, pengguna dapat menemukan lokasi presisi kedai legendaris, mencermati ragam menu beserta transparansi harga, serta langsung mendapatkan panduan navigasi Google Maps maupun kontak WhatsApp pemesanan.</p>
                    <div class="features-list">
                        <div class="feature-badge">✓ Peta Interaktif Presisi Geografis</div>
                        <div class="feature-badge">✓ Katalog Menu & Transparansi Harga</div>
                        <div class="feature-badge">✓ Desain Kebudayaan Minangkabau Modern</div>
                        <div class="feature-badge">✓ Panel Pengelolaan Data UMKM Terpadu</div>
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
    </section>

    <!-- Footer Nuansa Minang -->
    <footer class="footer-minang">
        <div class="footer-songket-top"></div>
        <div class="footer-container">
            <div class="footer-col brand-col">
                <div class="brand footer-brand">
                    <span class="brand-icon">🏛️</span>
                    <div>
                        <h3 class="footer-logo">Peta Rasa Padang</h3>
                        <small>Warisan Cita Rasa di Ujung Jari</small>
                    </div>
                </div>
                <p class="footer-bio">Mendukung digitalisasi dan kemandirian pelaku UMKM kuliner di Kota Padang dan sekitarnya melalui teknologi web terbuka.</p>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Navigasi Utama</h4>
                <ul class="footer-links">
                    <li><a href="#main-navbar">Beranda</a></li>
                    <li><a href="#peta-section">Peta Kuliner</a></li>
                    <li><a href="#toko-section">Katalog Toko</a></li>
                    <li><a href="#tentang-section">Tentang Inovasi</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Kategori Kuliner</h4>
                <ul class="footer-links">
                    <li><a href="#peta-section" onclick="filterByCat('Rumah Makan')">Rumah Makan Padang</a></li>
                    <li><a href="#peta-section" onclick="filterByCat('Sate')">Sate Padang & Pariaman</a></li>
                    <li><a href="#peta-section" onclick="filterByCat('Kopi')">Kedai Kawa & Kopi Solok</a></li>
                    <li><a href="#peta-section" onclick="filterByCat('Oleh-oleh')">Sanjai & Kerupuk Jangek</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Akses Pengelola</h4>
                <p class="text-sm">Bagi pemilik UMKM atau panitia yang ingin mengelola data toko:</p>
                <a href="admin/dashboard.php" class="btn btn-gold btn-sm" style="display:inline-block; margin-top:8px;">Masuk ke Panel Admin</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 <strong>Peta Rasa Padang</strong> — Peserta IT FEST 2026 Web Programming UIN Mahmud Yunus Batusangkar.</p>
        </div>
    </footer>

    <!-- Leaflet JS 1.9.4 -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Script Interaktif Peta & Filter -->
    <script src="assets/js/peta.js"></script>
</body>
</html>
