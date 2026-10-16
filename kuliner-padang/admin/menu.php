<?php
/**
 * admin/menu.php — CRUD Menu Makanan/Minuman per Toko
 * Aturan: Proteksi admin, prepared statement, tampilkan nama toko di judul
 */
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

$id_toko = (int)($_GET['id_toko'] ?? 0);
if ($id_toko <= 0) {
    header("Location: dashboard.php");
    exit;
}

// Ambil info detail toko
$stmt_toko = mysqli_prepare($koneksi, "SELECT * FROM toko WHERE id_toko = ?");
mysqli_stmt_bind_param($stmt_toko, "i", $id_toko);
mysqli_stmt_execute($stmt_toko);
$res_toko = mysqli_stmt_get_result($stmt_toko);
$toko = mysqli_fetch_assoc($res_toko);
mysqli_stmt_close($stmt_toko);

if (!$toko) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$pesan = '';

// Proses Hapus Menu (via GET action=hapus)
if (isset($_GET['action']) && $_GET['action'] === 'hapus') {
    $id_menu_del = (int)($_GET['id_menu'] ?? 0);
    if ($id_menu_del > 0) {
        $stmt_del = mysqli_prepare($koneksi, "DELETE FROM menu WHERE id_menu = ? AND id_toko = ?");
        mysqli_stmt_bind_param($stmt_del, "ii", $id_menu_del, $id_toko);
        if (mysqli_stmt_execute($stmt_del)) {
            $pesan = "Menu berhasil dihapus!";
        } else {
            $error = "Gagal menghapus menu: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt_del);
    }
}

// Proses Tambah / Update Menu (via POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_menu_edit = (int)($_POST['id_menu'] ?? 0);
    $nama_menu    = trim($_POST['nama_menu'] ?? '');
    $harga        = (int)($_POST['harga'] ?? 0);
    $deskripsi    = trim($_POST['deskripsi'] ?? '');

    if (empty($nama_menu) || $harga <= 0) {
        $error = "Nama menu dan harga (harus lebih dari 0) wajib diisi!";
    } else {
        if ($id_menu_edit > 0) {
            // Mode Update Menu
            $stmt_up = mysqli_prepare($koneksi, "UPDATE menu SET nama_menu = ?, harga = ?, deskripsi = ? WHERE id_menu = ? AND id_toko = ?");
            mysqli_stmt_bind_param($stmt_up, "sisii", $nama_menu, $harga, $deskripsi, $id_menu_edit, $id_toko);
            if (mysqli_stmt_execute($stmt_up)) {
                $pesan = "Menu '" . $nama_menu . "' berhasil diperbarui!";
            } else {
                $error = "Gagal memperbarui menu: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt_up);
        } else {
            // Mode Tambah Menu Baru
            $stmt_in = mysqli_prepare($koneksi, "INSERT INTO menu (id_toko, nama_menu, harga, deskripsi) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_in, "isis", $id_toko, $nama_menu, $harga, $deskripsi);
            if (mysqli_stmt_execute($stmt_in)) {
                $pesan = "Menu baru '" . $nama_menu . "' berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan menu: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt_in);
        }
    }
}

// Ambil data menu yang sedang diedit jika ada
$edit_item = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $stmt_e = mysqli_prepare($koneksi, "SELECT * FROM menu WHERE id_menu = ? AND id_toko = ?");
    mysqli_stmt_bind_param($stmt_e, "ii", $id_edit, $id_toko);
    mysqli_stmt_execute($stmt_e);
    $edit_item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_e));
    mysqli_stmt_close($stmt_e);
}

// Ambil seluruh menu untuk toko ini
$stmt_list = mysqli_prepare($koneksi, "SELECT * FROM menu WHERE id_toko = ? ORDER BY id_menu DESC");
mysqli_stmt_bind_param($stmt_list, "i", $id_toko);
mysqli_stmt_execute($stmt_list);
$res_menu = mysqli_stmt_get_result($stmt_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu — <?= htmlspecialchars($toko['nama_toko']); ?></title>
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
                <span>Halo, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="dashboard.php" class="btn btn-outline-light btn-sm">← Kembali ke Dashboard Toko</a>
            </div>
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-container">
            <div class="admin-header-flex">
                <div>
                    <h2 class="section-title">Daftar Menu: <span class="text-gold"><?= htmlspecialchars($toko['nama_toko']); ?></span></h2>
                    <p class="section-desc">Kategori: <strong><?= htmlspecialchars($toko['kategori']); ?></strong> | Alamat: <?= htmlspecialchars($toko['alamat']); ?></p>
                </div>
                <div>
                    <a href="dashboard.php" class="btn btn-secondary">← Kembali</a>
                </div>
            </div>

            <?php if (!empty($pesan)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($pesan); ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="admin-two-cols">
                <!-- Kolom Kiri: Form Tambah/Edit Menu -->
                <div class="col-form">
                    <div class="form-card">
                        <h3 class="form-title-sm"><?= $edit_item ? '<i class="fa-solid fa-pen-to-square"></i> Edit Menu' : '<i class="fa-solid fa-plus"></i> Tambah Menu Baru'; ?></h3>
                        <form action="menu.php?id_toko=<?= $id_toko; ?>" method="POST">
                            <input type="hidden" name="id_menu" value="<?= $edit_item ? $edit_item['id_menu'] : 0; ?>">

                            <div class="form-group">
                                <label for="nama_menu">Nama Menu Makanan/Minuman <span class="required">*</span></label>
                                <input type="text" id="nama_menu" name="nama_menu" class="form-control" placeholder="Contoh: Rendang Daging Sapi" required value="<?= htmlspecialchars($edit_item['nama_menu'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="harga">Harga (Rupiah) <span class="required">*</span></label>
                                <input type="number" id="harga" name="harga" class="form-control" placeholder="Contoh: 25000" min="500" step="500" required value="<?= htmlspecialchars($edit_item['harga'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi Singkat Menu</label>
                                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan porsi, rasa, atau bahan utama..."><?= htmlspecialchars($edit_item['deskripsi'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <?= $edit_item ? '<i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan' : '<i class="fa-solid fa-plus"></i> Tambah Menu'; ?>
                                </button>
                                <?php if ($edit_item): ?>
                                    <a href="menu.php?id_toko=<?= $id_toko; ?>" class="btn btn-secondary">Batal Edit</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Kolom Kanan: Tabel Menu -->
                <div class="col-table">
                    <div class="card-table">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">No</th>
                                        <th>Nama Menu</th>
                                        <th>Harga</th>
                                        <th>Deskripsi</th>
                                        <th style="width: 140px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($res_menu) > 0): ?>
                                        <?php $no = 1; while ($m = mysqli_fetch_assoc($res_menu)): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><strong><?= htmlspecialchars($m['nama_menu']); ?></strong></td>
                                                <td><span class="price-badge">Rp<?= number_format($m['harga'], 0, ',', '.'); ?></span></td>
                                                <td><small><?= htmlspecialchars($m['deskripsi'] ?? '-'); ?></small></td>
                                                <td style="text-align: center;">
                                                    <div class="btn-group">
                                                        <a href="menu.php?id_toko=<?= $id_toko; ?>&edit=<?= $m['id_menu']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                        <a href="menu.php?id_toko=<?= $id_toko; ?>&action=hapus&id_menu=<?= $m['id_menu']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus menu \'<?= addslashes(htmlspecialchars($m['nama_menu'])); ?>\'?');" title="Hapus">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 2rem;">Belum ada menu terdaftar untuk toko ini. Silakan gunakan formulir di samping untuk menambah menu.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="admin-footer">
        <p>&copy; 2026 Peta Rasa Padang — Portal Admin Pengelola UMKM</p>
    </footer>
</body>
</html>
