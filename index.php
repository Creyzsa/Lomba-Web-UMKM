<?php
/**
 * index.php — Beranda Peta Kuliner Padang
 * Tema: Minang Modern Luxury (Peta Rasa Padang)
 */
session_start();
require_once 'koneksi.php';

// Ambil statistik ringkas untuk header/counter
$res_stat_toko = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM toko");
$total_toko = ($res_stat_toko) ? mysqli_fetch_assoc($res_stat_toko)['total'] : 0;

$res_stat_menu = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM menu");
$total_menu = ($res_stat_menu) ? mysqli_fetch_assoc($res_stat_menu)['total'] : 0;

// Ambil 6 toko unggulan untuk preview beranda
$res_toko_preview = mysqli_query($koneksi, "SELECT * FROM toko ORDER BY id_toko ASC LIMIT 6");

$active_nav = 'beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Rasa Padang — Etalase Digital Kuliner Minangkabau</title>
    
    <!-- Google Fonts: Cinzel & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Utama Tema Minang Modern -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Hero Section dengan MinangScrollHero Songket (Scroll-Scrubbed Kain Songket & Rangkiang) -->
    <?php include 'components/minang-scroll-hero-songket.php'; ?>

    <!-- Section Kategori Unggulan -->
    <section class="section-container">
        <div class="section-header text-center">
            <span class="section-kicker">KATEGORI PILIHAN</span>
            <h2 class="section-heading">Kekayaan Rasa Ranah Minang</h2>
            <div class="minang-divider">
                <span>◆</span><span>◆</span><span class="center-diamond"><i class="fa-solid fa-diamond text-gold"></i></span><span>◆</span><span>◆</span>
            </div>
            <p class="section-subtext">Pilih kategori kuliner untuk menelusuri lokasi toko dan menu favorit Anda langsung di peta interaktif.</p>
        </div>

        <div class="category-cards-grid">
            <a href="peta.php?kategori=Rumah+Makan" class="category-feature-card">
                <span class="cat-card-icon"><i class="fa-solid fa-bowl-rice"></i></span>
                <h3>Rumah Makan</h3>
                <p>Rendang daging legendaris, gulai tunjang, dendeng batokok, dan ayam pop khas Padang.</p>
                <span class="cat-card-link">Lihat di Peta →</span>
            </a>
            <a href="peta.php?kategori=Sate" class="category-feature-card">
                <span class="cat-card-icon"><i class="fa-solid fa-stroopwafel"></i></span>
                <h3>Sate Padang</h3>
                <p>Sate daging dan lidah sapi beraroma bakaran arang dengan kuah rempah kuning dan cokelat.</p>
                <span class="cat-card-link">Lihat di Peta →</span>
            </a>
            <a href="peta.php?kategori=Kopi" class="category-feature-card">
                <span class="cat-card-icon"><i class="fa-solid fa-mug-hot"></i></span>
                <h3>Kopi & Kawa Daun</h3>
                <p>Sensasi seduhan daun kopi kering di tempurung kelapa dan biji kopi arabika dataran tinggi Solok.</p>
                <span class="cat-card-link">Lihat di Peta →</span>
            </a>
            <a href="peta.php?kategori=Oleh-oleh" class="category-feature-card">
                <span class="cat-card-icon"><i class="fa-solid fa-gift"></i></span>
                <h3>Oleh-Oleh Khas</h3>
                <p>Keripik sanjai balado pedas manis, karak kaliang, kerupuk jangek, hingga rendang vakum.</p>
                <span class="cat-card-link">Lihat di Peta →</span>
            </a>
        </div>
    </section>

    <!-- Ornamen Songket Strip -->
    <div class="songket-strip"></div>

    <!-- Section Highlight Preview Toko -->
    <section class="section-container">
        <div class="section-header">
            <div class="header-left">
                <span class="section-kicker">ETALASE UNGGULAN</span>
                <h2 class="section-heading">Destinasi Kuliner Populer</h2>
                <p class="section-subtext">Beberapa pilihan kedai dan rumah makan favorit wisatawan di Kota Padang.</p>
            </div>
            <div class="header-right">
                <a href="toko.php" class="btn btn-outline-gold btn-sm" style="color:var(--merah-utama); border-color:var(--merah-utama);">Lihat Semua Toko (<?= $total_toko; ?>) →</a>
            </div>
        </div>

        <div class="toko-grid">
            <?php while ($t = mysqli_fetch_assoc($res_toko_preview)): ?>
                <?php 
                    $foto = !empty($t['foto']) ? 'assets/img/' . $t['foto'] : 'assets/img/default.png'; 
                ?>
                <div class="toko-card" onclick="window.location.href='peta.php?toko=<?= $t['id_toko']; ?>'">
                    <div class="card-img-wrapper">
                        <img src="<?= htmlspecialchars($foto); ?>" alt="<?= htmlspecialchars($t['nama_toko']); ?>" class="card-img" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=500&q=80'">
                        <span class="card-badge"><?= htmlspecialchars($t['kategori']); ?></span>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($t['nama_toko']); ?></h3>
                        <p class="card-address"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($t['alamat']); ?></p>
                        <?php if (!empty($t['jam_buka'])): ?>
                            <p class="card-address" style="color:#0284C7; font-weight:600; margin-top:-4px; margin-bottom:8px;">
                                <i class="fa-regular fa-clock"></i> <?= htmlspecialchars($t['jam_buka']); ?>
                            </p>
                        <?php endif; ?>
                        <p class="card-desc"><?= htmlspecialchars($t['deskripsi']); ?></p>
                        <div class="card-actions">
                            <a href="peta.php?toko=<?= $t['id_toko']; ?>" class="btn btn-primary btn-sm btn-block">
                                <i class="fa-solid fa-location-dot"></i> Lihat Lokasi di Peta
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="text-center" style="margin-top: 40px;">
            <a href="toko.php" class="btn btn-gold">Lihat Seluruh Daftar Toko & Menu Lengkap ↗</a>
        </div>
    </section>

    <!-- Banner Ajakan Eksplorasi Peta -->
    <section class="section-container" style="margin-bottom: 80px;">
        <div class="cta-banner">
            <div class="cta-content">
                <span class="section-kicker text-gold">PANDUAN LOKASI GEOGRAFIS</span>
                <h2>Jelajahi Lewat Peta Interaktif</h2>
                <p>Buka peta presisi Kota Padang dengan titik sebaran rumah makan, koordinat akurat, rute Google Maps, serta daftar harga transparan.</p>
                <a href="peta.php" class="btn btn-hero-primary" style="margin-top: 14px;">Buka Peta Kuliner Sekarang ↗</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
