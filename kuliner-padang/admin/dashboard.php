<?php
/**
 * admin/dashboard.php — Dashboard Admin Manajemen Toko
 * Aturan: Proteksi sesi admin, tabel daftar toko, tombol Tambah/Edit/Hapus/Kelola Menu
 */
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

// Ambil pesan notifikasi jika ada
$pesan = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);

// Ambil data toko beserta total menunya
$sql = "SELECT t.*, COUNT(m.id_menu) AS total_menu 
        FROM toko t 
        LEFT JOIN menu m ON t.id_toko = m.id_toko 
        GROUP BY t.id_toko 
        ORDER BY t.id_toko DESC";
$result = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Peta Rasa Padang</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="admin-body">
    <header class="admin-nav">
        <div class="admin-nav-container">
            <div class="admin-brand">
                <span class="brand-icon"><img src="../assets/img/logo-minang.svg" alt="Logo Peta Rasa Padang" style="width:48px;height:48px;"></span>
                <span class="admin-title">Panel Admin — Peta Rasa Padang</span>
            </div>
            <div class="admin-user-info">
                <span>Halo, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong> (Admin)</span>
                <a href="../index.php" target="_blank" class="btn btn-outline-light btn-sm">Lihat Web Peta ↗</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Keluar</a>
            </div>
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-container">
            <div class="admin-header-flex">
                <div>
                    <h2 class="section-title">Manajemen Data Toko Kuliner</h2>
                    <p class="section-desc">Kelola etalase digital UMKM kuliner di Kota Padang</p>
                </div>
                <div>
                    <a href="tambah.php" class="btn btn-primary btn-add">
                        <span>+</span> Tambah Toko Baru
                    </a>
                </div>
            </div>

            <?php if (!empty($pesan)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($pesan); ?>
                </div>
            <?php endif; ?>

            <div class="card-table">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Foto</th>
                                <th>Nama Toko</th>
                                <th>Kategori</th>
                                <th>Alamat</th>
                                <th>Koordinat</th>
                                <th>Total Menu</th>
                                <th style="width: 260px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while ($toko = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td>
                                            <?php 
                                                $foto_src = !empty($toko['foto']) ? '../assets/img/' . $toko['foto'] : '../assets/img/default.png';
                                            ?>
                                            <img src="<?= htmlspecialchars($foto_src); ?>" alt="Foto" class="thumb-table" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=100&q=80'">
                                        </td>
                                        <td><strong><?= htmlspecialchars($toko['nama_toko']); ?></strong></td>
                                        <td><span class="badge badge-gold"><?= htmlspecialchars($toko['kategori']); ?></span></td>
                                        <td><?= htmlspecialchars($toko['alamat']); ?></td>
                                        <td><small><?= htmlspecialchars($toko['latitude']); ?>, <?= htmlspecialchars($toko['longitude']); ?></small></td>
                                        <td style="text-align: center;">
                                            <span class="badge badge-count"><?= (int)$toko['total_menu']; ?> Menu</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="btn-group">
                                                <a href="menu.php?id_toko=<?= $toko['id_toko']; ?>" class="btn btn-sm btn-info" title="Kelola Menu">
                                                    <i class="fa-solid fa-utensils"></i> Menu
                                                </a>
                                                <a href="edit.php?id=<?= $toko['id_toko']; ?>" class="btn btn-sm btn-warning" title="Edit Toko">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </a>
                                                <a href="hapus.php?id=<?= $toko['id_toko']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus toko \'<?= addslashes(htmlspecialchars($toko['nama_toko'])); ?>\' beserta seluruh menunya?');" title="Hapus Toko">
                                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2rem;">Belum ada data toko kuliner. Klik tombol "Tambah Toko Baru" untuk mengisi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="admin-footer">
        <p>&copy; 2026 Peta Rasa Padang — Portal Admin Pengelola UMKM</p>
    </footer>
</body>
</html>
