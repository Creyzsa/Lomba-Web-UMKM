<?php
/**
 * khas-daerah.php — Kurasi Kuliner Khas Daerah Minangkabau
 * Fitur: Menampilkan makanan khas, cerita, dan UMKM terkait
 */
session_start();
require_once 'koneksi.php';
$active_nav = 'khas';

// Ambil makanan khas
$res_makanan = mysqli_query($koneksi, "SELECT * FROM makanan_khas ORDER BY id_makanan ASC");

// Ambil toko yang punya badge khas atau hidden gem
$res_toko_khas = mysqli_query($koneksi, "SELECT * FROM toko WHERE is_khas = 1 OR is_hidden_gem = 1 ORDER BY is_khas DESC, id_toko ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuliner Khas Daerah — Peta Rasa Padang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <section class="page-banner">
        <div class="page-banner-overlay"></div>
        <div class="page-banner-content">
            <span class="hero-badge"><i class="fa-solid fa-bowl-rice"></i> WARISAN RASA NUSANTARA</span>
            <h1 class="page-banner-title">Kuliner Khas Daerah</h1>
            <p class="page-banner-desc">Jelajahi cerita di balik makanan tradisional Minangkabau, dari rendang legendaris hingga sala lauak pesisir. Temukan juga UMKM yang menyajikannya.</p>
        </div>
        <div class="songket-border-bottom"></div>
    </section>

    <!-- Daftar Makanan Khas -->
    <section class="section-container" style="margin-top:40px;">
        <div class="section-header text-center">
            <span class="section-kicker">MAKANAN IKONIK</span>
            <h2 class="section-heading">Khas Daerah Minangkabau</h2>
            <div class="minang-divider">
                <span>◆</span><span>◆</span><span class="center-diamond"><i class="fa-solid fa-diamond text-gold"></i></span><span>◆</span><span>◆</span>
            </div>
            <p class="section-subtext">Setiap makanan memiliki cerita dan filosofi yang mencerminkan budaya masyarakat Minang.</p>
        </div>

        <?php if ($res_makanan && mysqli_num_rows($res_makanan) > 0): ?>
            <div class="khas-grid">
                <?php while ($m = mysqli_fetch_assoc($res_makanan)): 
                    $foto_m = !empty($m['foto']) ? 'assets/img/' . $m['foto'] : 'assets/img/default.png';
                    
                    // Ambil info toko dan lokasinya jika ada id_toko
                    $toko_k = null;
                    if (!empty($m['id_toko'])) {
                        $stmt_t = mysqli_prepare($koneksi, "SELECT id_toko, nama_toko, alamat, latitude, longitude FROM toko WHERE id_toko = ?");
                        mysqli_stmt_bind_param($stmt_t, "i", $m['id_toko']);
                        mysqli_stmt_execute($stmt_t);
                        $toko_k = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_t));
                        mysqli_stmt_close($stmt_t);
                    }
                ?>
                    <div class="khas-card">
                        <div class="khas-card-media">
                            <img src="<?= htmlspecialchars($foto_m); ?>" alt="<?= htmlspecialchars($m['nama_makanan']); ?>" class="khas-img" onerror="this.src='assets/img/default.png'">
                            <span class="khas-origin-badge"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($m['asal_daerah']); ?></span>
                        </div>
                        <div class="khas-card-content">
                            <h3 class="khas-nama"><?= htmlspecialchars($m['nama_makanan']); ?></h3>
                            <p class="khas-cerita"><?= htmlspecialchars($m['cerita']); ?></p>

                            <?php if ($toko_k): ?>
                                <div class="khas-location-box">
                                    <span class="khas-loc-title"><i class="fa-solid fa-store"></i> Rekomendasi Tempat Cicip:</span>
                                    <strong class="khas-loc-name"><?= htmlspecialchars($toko_k['nama_toko']); ?></strong>
                                    <p class="khas-loc-address"><i class="fa-solid fa-map-pin"></i> <?= htmlspecialchars($toko_k['alamat']); ?></p>
                            <?php if (!empty($toko_k['jam_buka'])): ?>
                                <p class="khas-loc-address" style="color:#0284C7; font-weight:600; margin-top:2px;">
                                    <i class="fa-regular fa-clock"></i> Buka: <?= htmlspecialchars($toko_k['jam_buka']); ?>
                                </p>
                            <?php endif; ?>
                                </div>
                                <div class="khas-card-actions">
                                    <a href="detail.php?id=<?= $toko_k['id_toko']; ?>" class="btn btn-sm btn-gold">
                                        <i class="fa-solid fa-utensils"></i> Kunjungi Toko
                                    </a>
                                    <a href="peta.php?toko=<?= $toko_k['id_toko']; ?>" class="btn btn-sm btn-outline-gold">
                                        <i class="fa-solid fa-map-location-dot"></i> Lihat di Peta
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="error-msg-box">
                <p>Belum ada data makanan khas daerah. <a href="detail.php?id=1">Lihat contoh UMKM</a></p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Ornamen -->
    <div class="songket-strip"></div>

    <!-- Daftar UMKM dengan Badge Khas / Hidden Gem -->
    <section class="section-container">
        <div class="section-header">
            <div class="header-left">
                <span class="section-kicker">UMKM TERKURASI</span>
                <h2 class="section-heading">Toko dengan Kuliner Khas</h2>
                <p class="section-subtext">UMKM yang telah mendapat badge "Khas Daerah" atau "Hidden Gem" — direkomendasikan untuk mencicipi cita rasa autentik.</p>
            </div>
            <div class="header-right">
                <a href="peta.php" class="btn btn-outline-gold btn-sm"><i class="fa-solid fa-location-dot"></i> Lihat di Peta</a>
            </div>
        </div>

        <?php if ($res_toko_khas && mysqli_num_rows($res_toko_khas) > 0): ?>
            <div class="toko-grid">
                <?php while ($t = mysqli_fetch_assoc($res_toko_khas)): 
                    $foto_src = !empty($t['foto']) ? 'assets/img/' . $t['foto'] : 'assets/img/default.png';
                    $badge_label = !empty($t['is_khas']) ? 'Khas Daerah' : 'Hidden Gem';
                    $badge_type = !empty($t['is_khas']) ? 'badge-khas' : 'badge-hidden';
                ?>
                    <div class="toko-card">
                        <div class="card-img-wrapper">
                            <img src="<?= htmlspecialchars($foto_src); ?>" alt="<?= htmlspecialchars($t['nama_toko']); ?>" class="card-img" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=500&q=80'">
                            <span class="card-badge <?= $badge_type; ?>"><?= $badge_label; ?></span>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($t['nama_toko']); ?></h3>
                            <p class="card-address"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($t['alamat']); ?></p>
                            <p class="card-desc"><?= htmlspecialchars($t['deskripsi']); ?></p>
                            <div class="card-actions">
                                <a href="detail.php?id=<?= $t['id_toko']; ?>" class="btn btn-primary btn-sm" style="flex:1;"><i class="fa-regular fa-file-lines"></i> Detail</a>
                                <a href="peta.php?toko=<?= $t['id_toko']; ?>" class="btn btn-outline-gold btn-sm"><i class="fa-solid fa-location-dot"></i> Peta</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="error-msg-box">
                <p>Belum ada UMKM dengan badge khas daerah.</p>
            </div>
        <?php endif; ?>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>