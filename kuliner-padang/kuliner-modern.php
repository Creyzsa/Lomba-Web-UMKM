<?php
/**
 * kuliner-modern.php — Inovasi & Kuliner Modern Minang
 * Menampilkan kreasi kuliner kontemporer & fusion di Kota Padang
 */
session_start();
require_once 'koneksi.php';
$active_nav = 'modern';

// Ambil data kuliner modern
$res_modern = mysqli_query($koneksi, "SELECT * FROM kuliner_modern ORDER BY id_modern ASC");

// Ambil toko-toko modern / inovasi (id_toko 16 - 20)
$res_toko_modern = mysqli_query($koneksi, "SELECT * FROM toko WHERE id_toko >= 16 ORDER BY id_toko ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuliner Modern & Inovasi — Peta Rasa Padang</title>
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
            <span class="hero-badge"><i class="fa-solid fa-sparkles"></i> KREASI FUSION KEKINIAN</span>
            <h1 class="page-banner-title">Modern &amp; Inovasi Kuliner</h1>
            <p class="page-banner-desc">Eksplorasi perpaduan bumbu autentik Minangkabau dengan hidangan Western, Japanese, dan tren minuman kekinian generasi muda Kota Padang.</p>
        </div>
        <div class="songket-border-bottom"></div>
    </section>

    <!-- Daftar Makanan Modern & Inovasi -->
    <section class="section-container" style="margin-top:40px;">
        <div class="section-header text-center">
            <span class="section-kicker">INOVASI CITARASA</span>
            <h2 class="section-heading">Kreasi Menu Modern</h2>
            <div class="minang-divider">
                <span>◆</span><span>◆</span><span class="center-diamond"><i class="fa-solid fa-diamond text-gold"></i></span><span>◆</span><span>◆</span>
            </div>
            <p class="section-subtext">Menu fusion unik di mana cita rasa rendang, sambal balado, dan rempah Minang berpadu dengan kuliner dunia.</p>
        </div>

        <?php if ($res_modern && mysqli_num_rows($res_modern) > 0): ?>
            <div class="khas-grid">
                <?php while ($m = mysqli_fetch_assoc($res_modern)): 
                    $foto_m = !empty($m['foto']) ? 'assets/img/' . $m['foto'] : 'assets/img/default.png';
                    
                    $toko_m = null;
                    if (!empty($m['id_toko'])) {
                        $stmt_t = mysqli_prepare($koneksi, "SELECT id_toko, nama_toko, alamat, latitude, longitude FROM toko WHERE id_toko = ?");
                        mysqli_stmt_bind_param($stmt_t, "i", $m['id_toko']);
                        mysqli_stmt_execute($stmt_t);
                        $toko_m = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_t));
                        mysqli_stmt_close($stmt_t);
                    }
                ?>
                    <div class="khas-card">
                        <div class="khas-card-media">
                            <img src="<?= htmlspecialchars($foto_m); ?>" alt="<?= htmlspecialchars($m['nama_makanan']); ?>" class="khas-img" onerror="this.src='assets/img/default.png'">
                            <span class="khas-origin-badge" style="background:rgba(122,20,20,0.9);"><i class="fa-solid fa-fire"></i> <?= htmlspecialchars($m['kategori_inovasi']); ?></span>
                        </div>
                        <div class="khas-card-content">
                            <h3 class="khas-nama"><?= htmlspecialchars($m['nama_makanan']); ?></h3>
                            <p class="khas-cerita"><?= htmlspecialchars($m['deskripsi']); ?></p>

                            <?php if ($toko_m): ?>
                                <div class="khas-location-box">
                                    <span class="khas-loc-title"><i class="fa-solid fa-store"></i> Tersedia di Resto/Cafe:</span>
                                    <strong class="khas-loc-name"><?= htmlspecialchars($toko_m['nama_toko']); ?></strong>
                                    <p class="khas-loc-address"><i class="fa-solid fa-map-pin"></i> <?= htmlspecialchars($toko_m['alamat']); ?></p>
                                </div>
                                <div class="khas-card-actions">
                                    <a href="detail.php?id=<?= $toko_m['id_toko']; ?>" class="btn btn-sm btn-gold">
                                        <i class="fa-solid fa-utensils"></i> Lihat Menu Cafe
                                    </a>
                                    <a href="peta.php?toko=<?= $toko_m['id_toko']; ?>" class="btn btn-sm btn-outline-gold">
                                        <i class="fa-solid fa-map-location-dot"></i> Peta
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="error-msg-box">
                <p>Belum ada data inovasi kuliner modern.</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Ornamen Songket -->
    <div class="songket-strip"></div>

    <!-- Toko & Cafe Modern yang Terdaftar -->
    <section class="section-container">
        <div class="section-header">
            <div class="header-left">
                <span class="section-kicker">CAFE &amp; BISTRO KEKINIAN</span>
                <h2 class="section-heading">Toko &amp; Cafe Kuliner Modern</h2>
                <p class="section-subtext">Kunjungi tempat makan dan cafe kekinian di Padang yang menghadirkan menu inovasi fusion rasa Minang.</p>
            </div>
            <div class="header-right">
                <a href="peta.php" class="btn btn-outline-gold btn-sm"><i class="fa-solid fa-location-dot"></i> Lihat Semua di Peta</a>
            </div>
        </div>

        <?php if ($res_toko_modern && mysqli_num_rows($res_toko_modern) > 0): ?>
            <div class="toko-grid">
                <?php while ($t = mysqli_fetch_assoc($res_toko_modern)): 
                    $foto_src = !empty($t['foto']) ? 'assets/img/' . $t['foto'] : 'assets/img/default.png';
                ?>
                    <div class="toko-card">
                        <div class="card-img-wrapper">
                            <img src="<?= htmlspecialchars($foto_src); ?>" alt="<?= htmlspecialchars($t['nama_toko']); ?>" class="card-img" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=500&q=80'">
                            <span class="card-badge" style="background:#16a34a;"><i class="fa-solid fa-sparkles"></i> Modern Inovasi</span>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($t['nama_toko']); ?></h3>
                            <p class="card-address"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($t['alamat']); ?></p>
                            <p class="card-desc"><?= htmlspecialchars($t['deskripsi']); ?></p>
                            <div class="card-actions" style="margin-top:auto;display:flex;gap:8px;">
                                <a href="detail.php?id=<?= $t['id_toko']; ?>" class="btn btn-gold btn-sm" style="flex:1;">
                                    <i class="fa-solid fa-utensils"></i> Detail Menu
                                </a>
                                <a href="peta.php?toko=<?= $t['id_toko']; ?>" class="btn btn-outline-gold btn-sm" style="flex:1;">
                                    <i class="fa-solid fa-map-location-dot"></i> Peta
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
