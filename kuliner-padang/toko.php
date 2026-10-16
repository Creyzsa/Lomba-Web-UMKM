<?php
/**
 * toko.php — Halaman Daftar Toko & Menu Kuliner Lengkap
 * Fitur: Filter Kategori, Kotak Pencarian, Rincian Seluruh Menu per Toko, Akses Google Maps & WhatsApp
 */
session_start();
require_once 'koneksi.php';

$active_nav = 'toko';

// Ambil parameter filter & search jika ada via GET
$kategori_filter = trim($_GET['kategori'] ?? '');
$search_query    = trim($_GET['q'] ?? '');

$query = "SELECT t.*, COUNT(m.id_menu) as total_menu 
          FROM toko t 
          LEFT JOIN menu m ON t.id_toko = m.id_toko";

$where_clauses = [];
$params = [];
$types = "";

if (!empty($kategori_filter) && $kategori_filter !== 'Semua') {
    if ($kategori_filter === 'Modern & Inovasi') {
        $where_clauses[] = "t.id_toko >= 16";
    } else {
        $where_clauses[] = "t.kategori = ?";
        $params[] = $kategori_filter;
        $types .= "s";
    }
}

if (!empty($search_query)) {
    $where_clauses[] = "(t.nama_toko LIKE ? OR t.alamat LIKE ? OR t.deskripsi LIKE ?)";
    $like = "%" . $search_query . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

if (count($where_clauses) > 0) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " GROUP BY t.id_toko ORDER BY t.id_toko ASC";

if (count($params) > 0) {
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $res_toko = mysqli_stmt_get_result($stmt);
} else {
    $res_toko = mysqli_query($koneksi, $query);
}

$kategori_list = ['Semua', 'Rumah Makan', 'Sate', 'Kopi', 'Oleh-oleh', 'Camilan', 'Modern & Inovasi'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Toko & Menu Kuliner — Peta Rasa Padang</title>
    
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
            <span class="hero-badge">DIGITAL SHOWCASE UMKM</span>
            <h1 class="page-banner-title">Daftar Toko Kuliner Padang</h1>
            <p class="page-banner-desc">Eksplorasi etalase digital lengkap UMKM kuliner di Kota Padang dengan rincian menu makanan, transparansi harga, dan navigasi langsung ke lokasi.</p>
        </div>
        <div class="songket-border-bottom"></div>
    </section>

    <!-- Main Content -->
    <section class="section-container" style="margin-top: 40px;">
        <!-- Search & Filter Controls -->
        <div class="map-controls-card">
            <form action="toko.php" method="GET" class="search-form-flex">
                <div class="search-box-wrapper" style="flex:1;">
                    <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" class="search-input" placeholder="Cari nama toko kuliner, masakan, atau alamat..." value="<?= htmlspecialchars($search_query); ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 14px 24px;">Cari Toko</button>
                <?php if (!empty($search_query) || !empty($kategori_filter)): ?>
                    <a href="toko.php" class="btn btn-secondary" style="padding: 14px 18px;">Reset</a>
                <?php endif; ?>
            </form>

            <div class="category-filters">
                <?php foreach ($kategori_list as $kat): ?>
                    <?php 
                        $is_active = ($kategori_filter === $kat) || (empty($kategori_filter) && $kat === 'Semua');
                        $url_kat = ($kat === 'Semua') ? 'toko.php' : 'toko.php?kategori=' . urlencode($kat) . (!empty($search_query) ? '&q=' . urlencode($search_query) : '');
                    ?>
                    <a href="<?= $url_kat; ?>" class="filter-btn <?= $is_active ? 'active' : ''; ?>">
                        <?= htmlspecialchars($kat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Grid Toko & Menu Lengkap -->
        <?php if ($res_toko && mysqli_num_rows($res_toko) > 0): ?>
            <div style="margin-bottom: 20px; display:flex; justify-content:space-between; align-items:center;">
                <span class="text-muted">Menampilkan <strong><?= mysqli_num_rows($res_toko); ?></strong> toko kuliner</span>
                <a href="peta.php" class="btn btn-outline-gold btn-sm" style="color:var(--merah-utama); border-color:var(--merah-utama);"><i class="fa-solid fa-location-dot"></i> Lihat Semua di Peta Interaktif ↗</a>
            </div>

            <div class="toko-grid">
                <?php while ($toko = mysqli_fetch_assoc($res_toko)): ?>
                    <?php
                        $id_t = $toko['id_toko'];
                        $foto_src = !empty($toko['foto']) ? 'assets/img/' . $toko['foto'] : 'assets/img/default.png';
                        $maps_link = "https://www.google.com/maps?q=" . $toko['latitude'] . "," . $toko['longitude'];
                        $no_wa_toko = !empty($toko['no_wa']) ? preg_replace('/[^0-9]/', '', $toko['no_wa']) : '6282286908467';
                        $wa_text = urlencode("Halo *" . $toko['nama_toko'] . "*, saya ingin bertanya seputar menu & pemesanan via Peta Rasa Padang.");
                        $wa_link = "https://wa.me/" . $no_wa_toko . "?text=" . $wa_text;

                        // Ambil daftar menu toko ini
                        $stmt_m = mysqli_prepare($koneksi, "SELECT * FROM menu WHERE id_toko = ? ORDER BY id_menu ASC");
                        mysqli_stmt_bind_param($stmt_m, "i", $id_t);
                        mysqli_stmt_execute($stmt_m);
                        $res_m = mysqli_stmt_get_result($stmt_m);
                    ?>
                    <div class="toko-card">
                        <div class="card-img-wrapper">
                            <img src="<?= htmlspecialchars($foto_src); ?>" alt="<?= htmlspecialchars($toko['nama_toko']); ?>" class="card-img" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=500&q=80'">
                            <span class="card-badge"><?= htmlspecialchars($toko['kategori']); ?></span>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($toko['nama_toko']); ?></h3>
                            <p class="card-address"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($toko['alamat']); ?></p>
                            <p class="card-desc"><?= htmlspecialchars($toko['deskripsi']); ?></p>

                            <!-- Rincian Menu Makanan -->
                            <div class="card-menu-preview" style="background:#FAF8F2;">
                                <strong style="color:var(--merah-utama); font-size:0.84rem; display:block; margin-bottom:6px;"><i class="fa-solid fa-utensils"></i> Menu & Harga:</strong>
                                <?php if ($res_m && mysqli_num_rows($res_m) > 0): ?>
                                    <?php while ($m = mysqli_fetch_assoc($res_m)): ?>
                                        <div class="preview-menu-row">
                                            <span><?= htmlspecialchars($m['nama_menu']); ?></span>
                                            <span class="price-tag">Rp<?= number_format($m['harga'], 0, ',', '.'); ?></span>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:0.8rem;">Belum ada data menu.</span>
                                <?php endif; ?>
                                <?php if (($_SESSION['role'] ?? '') === 'pemilik' && (int)($toko['id_pemilik'] ?? 0) === (int)($_SESSION['id_user'] ?? 0)): ?>
                                    <a href="pemilik/menu.php?id_toko=<?= (int)$id_t; ?>" class="btn btn-secondary btn-sm" style="margin-top:10px;">
                                        <i class="fa-solid fa-utensils"></i> Kelola Menu Toko Ini
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="card-actions" style="margin-top:auto; display:flex; gap:8px;">
                                <a href="detail.php?id=<?= $id_t; ?>" class="btn btn-gold btn-sm" style="flex:1.2;">
                                    <i class="fa-solid fa-utensils"></i> Detail Menu
                                </a>
                                <a href="peta.php?toko=<?= $id_t; ?>" class="btn btn-outline-gold btn-sm" style="flex:1;" title="Lihat di Peta">
                                    <i class="fa-solid fa-location-dot"></i> Peta
                                </a>
                                <a href="<?= $wa_link; ?>" target="_blank" class="btn btn-sm" style="background:#16a34a; color:#fff;" title="WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i> WA
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php mysqli_stmt_close($stmt_m); ?>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="error-msg-box" style="margin-top:20px;">
                <p><i class="fa-solid fa-triangle-exclamation"></i> Tidak ada toko kuliner yang ditemukan untuk kriteria pencarian ini.</p>
                <a href="toko.php" class="btn btn-primary btn-sm" style="margin-top:10px;">Lihat Semua Toko</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
