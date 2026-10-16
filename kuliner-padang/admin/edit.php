<?php
/**
 * admin/edit.php — Ubah Data Toko
 * Aturan: Proteksi admin, prepared statement, Leaflet map interaktif, upload foto baru
 */
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

$id_toko = (int)($_GET['id'] ?? 0);
if ($id_toko <= 0) {
    header("Location: dashboard.php");
    exit;
}

// Ambil data toko lama
$stmt_get = mysqli_prepare($koneksi, "SELECT * FROM toko WHERE id_toko = ?");
mysqli_stmt_bind_param($stmt_get, "i", $id_toko);
mysqli_stmt_execute($stmt_get);
$res = mysqli_stmt_get_result($stmt_get);
$toko = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt_get);

if (!$toko) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$kategori_list = ['Rumah Makan', 'Sate', 'Kopi', 'Oleh-oleh', 'Camilan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = trim($_POST['nama_toko'] ?? '');
    $alamat    = trim($_POST['alamat'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $latitude  = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');

    if (empty($nama_toko) || empty($latitude) || empty($longitude)) {
        $error = 'Nama Toko, Latitude, dan Longitude wajib diisi!';
    } else {
        $foto_nama = $toko['foto'];

        // Cek jika upload foto baru
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['foto']['tmp_name'];
            $file_name = $_FILES['foto']['name'];
            $file_size = $_FILES['foto']['size'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_ext = ['jpg', 'jpeg', 'png'];

            if (!in_array($file_ext, $allowed_ext)) {
                $error = 'Format file foto harus JPG atau PNG!';
            } elseif ($file_size > 2 * 1024 * 1024) {
                $error = 'Ukuran file foto maksimal 2 MB!';
            } else {
                $foto_nama = 'toko_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                $target_dir = '../assets/img/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                move_uploaded_file($file_tmp, $target_dir . $foto_nama);
            }
        }

        if (empty($error)) {
            $stmt = mysqli_prepare($koneksi, "UPDATE toko SET nama_toko = ?, alamat = ?, kategori = ?, deskripsi = ?, latitude = ?, longitude = ?, foto = ? WHERE id_toko = ?");
            mysqli_stmt_bind_param($stmt, "ssssddsi", $nama_toko, $alamat, $kategori, $deskripsi, $latitude, $longitude, $foto_nama, $id_toko);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan'] = "Perubahan data toko '" . $nama_toko . "' berhasil disimpan!";
                header("Location: dashboard.php");
                exit;
            } else {
                $error = 'Gagal memperbarui data toko: ' . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Toko — Panel Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <header class="admin-nav">
        <div class="admin-nav-container">
            <div class="admin-brand">
                <span class="gonjong-icon">🏛️</span>
                <span class="admin-title">Panel Admin — Peta Rasa Padang</span>
            </div>
            <div class="admin-user-info">
                <span>Halo, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="dashboard.php" class="btn btn-outline-light btn-sm">← Kembali ke Dashboard</a>
            </div>
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-container form-container">
            <div class="form-card">
                <h2 class="form-title">Edit Data Toko Kuliner</h2>
                <p class="form-subtitle">Perbarui rincian toko "<?= htmlspecialchars($toko['nama_toko']); ?>".</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="edit.php?id=<?= $id_toko; ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="nama_toko">Nama Toko Kuliner <span class="required">*</span></label>
                                <input type="text" id="nama_toko" name="nama_toko" class="form-control" required value="<?= htmlspecialchars($_POST['nama_toko'] ?? $toko['nama_toko']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="kategori">Kategori Kuliner <span class="required">*</span></label>
                                <select id="kategori" name="kategori" class="form-control" required>
                                    <?php 
                                        $curr_kat = $_POST['kategori'] ?? $toko['kategori'];
                                        foreach ($kategori_list as $kat): 
                                    ?>
                                        <option value="<?= $kat; ?>" <?= ($curr_kat === $kat) ? 'selected' : ''; ?>>
                                            <?= $kat; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat Lengkap</label>
                                <textarea id="alamat" name="alamat" class="form-control" rows="2"><?= htmlspecialchars($_POST['alamat'] ?? $toko['alamat']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi Singkat Toko</label>
                                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($_POST['deskripsi'] ?? $toko['deskripsi']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="foto">Ubah Foto Toko (JPG / PNG, maks 2MB)</label>
                                <div class="current-photo-preview" style="margin-bottom: 0.5rem;">
                                    <?php $foto_src = !empty($toko['foto']) ? '../assets/img/' . $toko['foto'] : '../assets/img/default.png'; ?>
                                    <small>Foto saat ini: </small><br>
                                    <img src="<?= htmlspecialchars($foto_src); ?>" alt="Preview" style="height: 60px; border-radius: 4px; object-fit: cover; border: 1px solid #ccc; margin-top: 4px;" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=100&q=80'">
                                </div>
                                <input type="file" id="foto" name="foto" class="form-control" accept=".jpg, .jpeg, .png">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto saat ini.</small>
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label>Sesuaikan Lokasi di Peta Padang (Klik / Geser marker)</label>
                                <div id="mini-map" class="mini-map-box"></div>
                                <small class="text-muted">Klik titik baru di peta untuk memperbarui koordinat toko.</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-half">
                                    <label for="latitude">Latitude <span class="required">*</span></label>
                                    <input type="text" id="latitude" name="latitude" class="form-control" required readonly value="<?= htmlspecialchars($_POST['latitude'] ?? $toko['latitude']); ?>">
                                </div>
                                <div class="form-group col-half">
                                    <label for="longitude">Longitude <span class="required">*</span></label>
                                    <input type="text" id="longitude" name="longitude" class="form-control" required readonly value="<?= htmlspecialchars($_POST['longitude'] ?? $toko['longitude']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                        <a href="dashboard.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const initialLat = parseFloat(document.getElementById('latitude').value) || -0.9471;
        const initialLng = parseFloat(document.getElementById('longitude').value) || 100.4172;

        const miniMap = L.map('mini-map').setView([initialLat, initialLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(miniMap);

        let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(miniMap);

        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(7);
            document.getElementById('longitude').value = lng.toFixed(7);
        }

        miniMap.on('click', function(e) {
            const { lat, lng } = e.latlng;
            marker.setLatLng([lat, lng]);
            updateInputs(lat, lng);
        });

        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            updateInputs(pos.lat, pos.lng);
        });
    </script>
</body>
</html>
