<?php
/**
 * pemilik/dashboard.php — Daftar toko yang dikelola pemilik
 */
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pemilik' || empty($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

$pesan = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);

$id_pemilik = (int)$_SESSION['id_user'];
$active_nav = 'owner';
$nav_prefix = '../';
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT t.id_toko, t.nama_toko, t.kategori, t.alamat, t.deskripsi, t.foto, t.jam_buka,
       (SELECT COUNT(*) FROM menu m WHERE m.id_toko = t.id_toko) AS total_menu,
       (SELECT COUNT(*) FROM ulasan u WHERE u.id_toko = t.id_toko) AS total_ulasan,
       (SELECT COALESCE(AVG(u.rating), 0) FROM ulasan u WHERE u.id_toko = t.id_toko) AS rata_rating
     FROM toko t
     WHERE t.id_pemilik = ?
     ORDER BY t.nama_toko ASC"
);
mysqli_stmt_bind_param($stmt, "i", $id_pemilik);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$toko_list = [];
$total_menu = 0;
$total_ulasan = 0;
while ($toko = mysqli_fetch_assoc($result)) {
    $toko_list[] = $toko;
    $total_menu += (int)$toko['total_menu'];
    $total_ulasan += (int)$toko['total_ulasan'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Toko — Peta Rasa Padang</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=owner-reviews-20261016">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="owner-page">
    <?php include '../navbar.php'; ?>

    <section class="page-banner owner-banner">
        <div class="page-banner-overlay"></div>
        <div class="page-banner-content">
            <span class="hero-badge">RUANG PENGELOLA UMKM</span>
            <h1 class="page-banner-title">Halo, <?= htmlspecialchars($_SESSION['username']); ?></h1>
            <p class="page-banner-desc">Kelola profil usaha dan menu kuliner Anda dalam satu tempat.</p>
        </div>
        <div class="songket-border-bottom"></div>
    </section>

    <main class="owner-dashboard">
        <div class="section-container">
            <div class="owner-welcome-row">
                <div>
                    <span class="owner-eyebrow">DASHBOARD PEMILIK</span>
                    <h2 class="owner-section-title">Toko yang Saya Kelola</h2>
                    <p class="owner-section-desc">Perbarui informasi toko, atur menu, dan pastikan etalase Anda selalu terbaru.</p>
                </div>
                <a href="tambah-toko.php" class="btn btn-primary owner-add-button">
                    <span aria-hidden="true">＋</span> Daftarkan Toko
                </a>
            </div>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-success owner-alert"><?= htmlspecialchars($pesan); ?></div>
            <?php endif; ?>

            <div class="owner-stats-grid">
                <div class="owner-stat-card">
                    <span class="owner-stat-icon" aria-hidden="true">🏪</span>
                    <div>
                        <span class="owner-stat-label">Toko terdaftar</span>
                        <strong class="owner-stat-value"><?= count($toko_list); ?></strong>
                    </div>
                </div>
                <div class="owner-stat-card">
                    <span class="owner-stat-icon" aria-hidden="true">🍽️</span>
                    <div>
                        <span class="owner-stat-label">Total menu</span>
                        <strong class="owner-stat-value"><?= $total_menu; ?></strong>
                    </div>
                </div>
                <div class="owner-stat-card">
                    <span class="owner-stat-icon" aria-hidden="true">⭐</span>
                    <div>
                        <span class="owner-stat-label">Total ulasan</span>
                        <strong class="owner-stat-value"><?= $total_ulasan; ?></strong>
                    </div>
                </div>
            </div>

            <?php if (count($toko_list) > 0): ?>
                <div class="owner-store-grid">
                    <?php foreach ($toko_list as $toko): ?>
                        <?php
                            $foto = !empty($toko['foto']) ? '../assets/img/' . $toko['foto'] : '../assets/img/default.png';
                            $id_toko = (int)$toko['id_toko'];
                        ?>
                        <article class="owner-store-card">
                            <a class="owner-store-image" href="../detail.php?id=<?= $id_toko; ?>" aria-label="Lihat <?= htmlspecialchars($toko['nama_toko']); ?>">
                                <img src="<?= htmlspecialchars($foto); ?>" alt="Foto <?= htmlspecialchars($toko['nama_toko']); ?>" onerror="this.src='../assets/img/default.png'">
                                <span class="owner-store-category"><?= htmlspecialchars($toko['kategori'] ?? 'Kuliner'); ?></span>
                            </a>
                            <div class="owner-store-content">
                                <div class="owner-store-heading">
                                    <div>
                                        <h3><?= htmlspecialchars($toko['nama_toko']); ?></h3>
                                        <p class="owner-store-address">📍 <?= htmlspecialchars($toko['alamat'] ?: 'Alamat belum diisi'); ?></p>
                                    </div>
                                    <span class="owner-menu-count"><?= (int)$toko['total_menu']; ?> menu</span>
                                </div>
                                <div class="owner-store-rating">
                                    <span aria-hidden="true">★</span>
                                    <strong><?= number_format((float)$toko['rata_rating'], 1); ?></strong>
                                    <span><?= (int)$toko['total_ulasan']; ?> ulasan</span>
                                </div>
                                <p class="owner-store-description">
                                    <?= htmlspecialchars($toko['deskripsi'] ?: 'Tambahkan deskripsi untuk memperkenalkan toko dan menu andalan Anda.'); ?>
                                </p>
                                <div class="owner-store-meta">
                                    <span>🕒 <?= htmlspecialchars($toko['jam_buka'] ?: 'Jam buka belum diisi'); ?></span>
                                </div>
                                <div class="owner-store-actions">
                                    <a href="edit-toko.php?id=<?= $id_toko; ?>" class="btn btn-secondary">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit Profil
                                    </a>
                                    <a href="kelola-menu.php?id_toko=<?= $id_toko; ?>" class="btn btn-primary">
                                        <i class="fa-solid fa-utensils"></i> Kelola Menu
                                    </a>
                                    <a href="ulasan.php?id_toko=<?= $id_toko; ?>" class="btn btn-secondary">
                                        <i class="fa-solid fa-star"></i> Ulasan (<?= (int)$toko['total_ulasan']; ?>)
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="owner-empty-state">
                    <span class="owner-empty-icon" aria-hidden="true">🏮</span>
                    <h3>Mulai buat etalase toko Anda</h3>
                    <p>Tambahkan nama usaha, deskripsi, foto, kontak, dan lokasi. Setelah disimpan, Anda bisa langsung mengatur daftar menu.</p>
                    <a href="tambah-toko.php" class="btn btn-primary">＋ Daftarkan Toko Pertama</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../footer.php'; ?>
</body>
</html>
<?php mysqli_stmt_close($stmt); ?>
