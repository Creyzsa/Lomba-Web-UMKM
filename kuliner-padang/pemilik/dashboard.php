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
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT t.id_toko, t.nama_toko, t.kategori, t.alamat, COUNT(m.id_menu) AS total_menu
     FROM toko t
     LEFT JOIN menu m ON t.id_toko = m.id_toko
     WHERE t.id_pemilik = ?
     GROUP BY t.id_toko
     ORDER BY t.nama_toko ASC"
);
mysqli_stmt_bind_param($stmt, "i", $id_pemilik);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Toko — Peta Rasa Padang</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <header class="admin-nav">
        <div class="admin-nav-container">
            <div class="admin-brand">
                <span class="gonjong-icon">🏛️</span>
                <span class="admin-title">Panel Pemilik — Peta Rasa Padang</span>
            </div>
            <div class="admin-user-info">
                <span>Halo, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="../index.php" class="btn btn-outline-light btn-sm">Lihat Website</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Keluar</a>
            </div>
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-container">
            <div class="admin-header-flex">
                <div>
                    <h2 class="section-title">Toko yang Saya Kelola</h2>
                    <p class="section-desc">Kelola profil toko dan menu pada toko yang ditautkan ke akun Anda.</p>
                </div>
                <a href="tambah-toko.php" class="btn btn-primary">+ Daftarkan Toko</a>
            </div>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-success"><?= htmlspecialchars($pesan); ?></div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="card-table">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nama Toko</th>
                                    <th>Kategori</th>
                                    <th>Alamat</th>
                                    <th style="text-align:center;">Jumlah Menu</th>
                                    <th style="text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($toko = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($toko['nama_toko']); ?></strong></td>
                                        <td><?= htmlspecialchars($toko['kategori']); ?></td>
                                        <td><?= htmlspecialchars($toko['alamat']); ?></td>
                                        <td style="text-align:center;"><?= (int)$toko['total_menu']; ?></td>
                                        <td style="text-align:center;">
                                            <a href="../admin/edit.php?id=<?= (int)$toko['id_toko']; ?>" class="btn btn-sm btn-warning">
                                                ✏️ Edit Profil
                                            </a>
                                            <a href="../admin/menu.php?id_toko=<?= (int)$toko['id_toko']; ?>" class="btn btn-sm btn-primary">
                                                🍽️ Kelola Menu
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Anda belum mendaftarkan toko. Klik <strong>Daftarkan Toko</strong> untuk mengisi informasi usaha, foto, dan lokasi. Setelah disimpan, Anda dapat langsung mengelola profil dan menu toko.
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
<?php mysqli_stmt_close($stmt); ?>
