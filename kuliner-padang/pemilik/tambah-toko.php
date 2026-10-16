<?php
/**
 * pemilik/tambah-toko.php — Pemilik mendaftarkan tokonya sendiri
 */
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pemilik' || empty($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

$error = '';
$kategori_list = ['Rumah Makan', 'Sate', 'Kopi', 'Oleh-oleh', 'Camilan'];
$csrf_token = $_SESSION['csrf_tambah_toko'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_tambah_toko'] = $csrf_token;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = trim($_POST['nama_toko'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');
    $no_wa = trim($_POST['no_wa'] ?? '');
    $jam_buka = trim($_POST['jam_buka'] ?? '');
    $latitude_number = filter_var($latitude, FILTER_VALIDATE_FLOAT);
    $longitude_number = filter_var($longitude, FILTER_VALIDATE_FLOAT);

    if (!isset($_POST['csrf_token']) || !hash_equals($csrf_token, $_POST['csrf_token'])) {
        $error = 'Permintaan tidak valid. Muat ulang halaman dan coba lagi.';
    } elseif ($nama_toko === '' || strlen($nama_toko) > 100) {
        $error = 'Nama toko wajib diisi dan maksimal 100 karakter.';
    } elseif (!in_array($kategori, $kategori_list, true)) {
        $error = 'Pilih kategori toko yang tersedia.';
    } elseif ($latitude_number === false || $latitude_number < -90 || $latitude_number > 90
        || $longitude_number === false || $longitude_number < -180 || $longitude_number > 180) {
        $error = 'Koordinat lokasi tidak valid. Pilih posisi toko pada peta.';
    } elseif (strlen($alamat) > 255 || strlen($no_wa) > 20 || strlen($jam_buka) > 100) {
        $error = 'Alamat, nomor WhatsApp, atau jam buka melebihi batas karakter.';
    }

    $foto_nama = 'default.png';
    if ($error === '' && isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Foto gagal diunggah. Silakan coba lagi.';
        } else {
            $file_tmp = $_FILES['foto']['tmp_name'];
            $image_info = @getimagesize($file_tmp);
            $allowed_mime = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
            $mime_type = $image_info['mime'] ?? '';

            if (!isset($allowed_mime[$mime_type])) {
                $error = 'Format foto harus berupa gambar JPG atau PNG yang valid.';
            } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                $error = 'Ukuran file foto maksimal 2 MB.';
            } else {
                $foto_nama = 'toko_' . bin2hex(random_bytes(8)) . '.' . $allowed_mime[$mime_type];
                $target_dir = '../assets/img/';
                if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
                    $error = 'Folder penyimpanan foto belum tersedia.';
                } elseif (!move_uploaded_file($file_tmp, $target_dir . $foto_nama)) {
                    $error = 'Foto tidak dapat disimpan. Silakan coba lagi.';
                }
            }
        }
    }

    if ($error === '') {
        $id_pemilik = (int)$_SESSION['id_user'];
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO toko (nama_toko, alamat, kategori, deskripsi, latitude, longitude, foto, no_wa, jam_buka, id_pemilik)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "ssssddsssi",
            $nama_toko,
            $alamat,
            $kategori,
            $deskripsi,
            $latitude_number,
            $longitude_number,
            $foto_nama,
            $no_wa,
            $jam_buka,
            $id_pemilik
        );

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Toko '" . $nama_toko . "' berhasil didaftarkan. Sekarang Anda dapat mengelola profil dan menunya.";
            mysqli_stmt_close($stmt);
            header("Location: dashboard.php");
            exit;
        }

        $error = 'Gagal menyimpan data toko: ' . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        if ($foto_nama !== 'default.png') {
            $foto_path = '../assets/img/' . basename($foto_nama);
            if (is_file($foto_path)) {
                unlink($foto_path);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Toko — Peta Rasa Padang</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
                <a href="dashboard.php" class="btn btn-outline-light btn-sm">← Dashboard Toko</a>
            </div>
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-container form-container">
            <div class="form-card">
                <h2 class="form-title">Daftarkan Toko Anda</h2>
                <p class="form-subtitle">Isi informasi usaha dan tentukan lokasinya di peta. Setelah disimpan, toko langsung terhubung ke akun Anda.</p>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="tambah-toko.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="nama_toko">Nama Toko <span class="required">*</span></label>
                                <input type="text" id="nama_toko" name="nama_toko" class="form-control" maxlength="100" required value="<?= htmlspecialchars($_POST['nama_toko'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="kategori">Kategori <span class="required">*</span></label>
                                <select id="kategori" name="kategori" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($kategori_list as $kat): ?>
                                        <option value="<?= htmlspecialchars($kat); ?>" <?= (($_POST['kategori'] ?? '') === $kat) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($kat); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea id="alamat" name="alamat" class="form-control" rows="2" maxlength="255"><?= htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi Toko</label>
                                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="no_wa">Nomor WhatsApp</label>
                                <input type="text" id="no_wa" name="no_wa" class="form-control" maxlength="20" placeholder="6281234567890" value="<?= htmlspecialchars($_POST['no_wa'] ?? ''); ?>">
                                <small class="text-muted">Gunakan format internasional tanpa tanda +.</small>
                            </div>

                            <div class="form-group">
                                <label for="jam_buka">Jam Buka</label>
                                <input type="text" id="jam_buka" name="jam_buka" class="form-control" maxlength="100" placeholder="Contoh: 08.00 - 21.00 WIB" value="<?= htmlspecialchars($_POST['jam_buka'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="foto">Foto Toko (JPG/PNG, maksimal 2 MB)</label>
                                <input type="file" id="foto" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label>Pilih Lokasi Toko di Peta <span class="required">*</span></label>
                                <div id="mini-map" class="mini-map-box"></div>
                                <small class="text-muted">Klik peta atau geser pin ke lokasi toko Anda.</small>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-half">
                                    <label for="latitude">Latitude <span class="required">*</span></label>
                                    <input type="text" id="latitude" name="latitude" class="form-control" required value="<?= htmlspecialchars($_POST['latitude'] ?? '-0.9471000'); ?>">
                                </div>
                                <div class="form-group col-half">
                                    <label for="longitude">Longitude <span class="required">*</span></label>
                                    <input type="text" id="longitude" name="longitude" class="form-control" required value="<?= htmlspecialchars($_POST['longitude'] ?? '100.4172000'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Simpan dan Kelola Toko</button>
                        <a href="dashboard.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const initialLat = parseFloat(latitudeInput.value) || -0.9471;
        const initialLng = parseFloat(longitudeInput.value) || 100.4172;
        const miniMap = L.map('mini-map').setView([initialLat, initialLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(miniMap);

        const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(miniMap);
        function updateInputs(lat, lng) {
            latitudeInput.value = lat.toFixed(7);
            longitudeInput.value = lng.toFixed(7);
        }

        miniMap.on('click', event => {
            marker.setLatLng(event.latlng);
            updateInputs(event.latlng.lat, event.latlng.lng);
        });
        marker.on('dragend', () => {
            const position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
        });
    </script>
</body>
</html>
