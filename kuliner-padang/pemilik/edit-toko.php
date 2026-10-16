<?php
/**
 * admin/edit.php — Admin atau pemilik mengubah profil toko
 */
session_start();
$is_pemilik = isset($_SESSION['role']) && $_SESSION['role'] === 'pemilik';
if ((!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pemilik'], true))
    || ($is_pemilik && empty($_SESSION['id_user']))) {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

$id_pemilik_login = $is_pemilik ? (int)$_SESSION['id_user'] : 0;
$id_toko = (int)($_GET['id'] ?? 0);
if ($id_toko <= 0) {
    header("Location: " . ($is_pemilik ? "../pemilik/dashboard.php" : "dashboard.php"));
    exit;
}

// Pemilik hanya dapat membuka toko yang ditautkan pada akunnya.
$query_get = $is_pemilik
    ? "SELECT * FROM toko WHERE id_toko = ? AND id_pemilik = ?"
    : "SELECT * FROM toko WHERE id_toko = ?";
$stmt_get = mysqli_prepare($koneksi, $query_get);
if ($is_pemilik) {
    mysqli_stmt_bind_param($stmt_get, "ii", $id_toko, $id_pemilik_login);
} else {
    mysqli_stmt_bind_param($stmt_get, "i", $id_toko);
}
mysqli_stmt_execute($stmt_get);
$res = mysqli_stmt_get_result($stmt_get);
$toko = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt_get);

if (!$toko) {
    header("Location: " . ($is_pemilik ? "../pemilik/dashboard.php" : "dashboard.php"));
    exit;
}

$error = '';
$kategori_list = ['Rumah Makan', 'Sate', 'Kopi', 'Oleh-oleh', 'Camilan'];
if (!empty($toko['kategori']) && !in_array($toko['kategori'], $kategori_list, true)) {
    $kategori_list[] = $toko['kategori'];
}
$result_pemilik = $is_pemilik ? null : mysqli_query($koneksi, "SELECT id_user, username FROM users WHERE role = 'pemilik' ORDER BY username ASC");
$csrf_key = $is_pemilik ? 'csrf_edit_toko_pemilik' : 'csrf_edit_toko';
$csrf_token = $_SESSION[$csrf_key] ?? bin2hex(random_bytes(32));
$_SESSION[$csrf_key] = $csrf_token;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = trim($_POST['nama_toko'] ?? '');
    $alamat    = trim($_POST['alamat'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $latitude  = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');
    $no_wa     = trim($_POST['no_wa'] ?? '');
    $jam_buka  = trim($_POST['jam_buka'] ?? '');
    $id_pemilik = $is_pemilik ? $id_pemilik_login : (int)($_POST['id_pemilik'] ?? 0);
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
    } elseif (!$is_pemilik && $id_pemilik > 0) {
        $stmt_owner = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE id_user = ? AND role = 'pemilik'");
        mysqli_stmt_bind_param($stmt_owner, "i", $id_pemilik);
        mysqli_stmt_execute($stmt_owner);
        $owner_exists = mysqli_stmt_get_result($stmt_owner);
        if (mysqli_num_rows($owner_exists) === 0) {
            $error = 'Akun pemilik toko yang dipilih tidak valid.';
        }
        mysqli_stmt_close($stmt_owner);
    }

    if (empty($error)) {
        $foto_nama = $toko['foto'];

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Foto gagal diunggah. Silakan coba lagi.';
            } else {
                $file_tmp  = $_FILES['foto']['tmp_name'];
                $file_size = $_FILES['foto']['size'];
                $image_info = @getimagesize($file_tmp);
                $allowed_mime = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
                $mime_type = $image_info['mime'] ?? '';
                if (!isset($allowed_mime[$mime_type])) {
                    $error = 'Format foto harus berupa gambar JPG atau PNG yang valid.';
                } elseif ($file_size > 2 * 1024 * 1024) {
                    $error = 'Ukuran file foto maksimal 2 MB!';
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

        if (empty($error)) {
            if ($is_pemilik) {
                $stmt = mysqli_prepare($koneksi, "UPDATE toko SET nama_toko = ?, alamat = ?, kategori = ?, deskripsi = ?, latitude = ?, longitude = ?, foto = ?, no_wa = ?, jam_buka = ? WHERE id_toko = ? AND id_pemilik = ?");
                mysqli_stmt_bind_param($stmt, "ssssddsssii", $nama_toko, $alamat, $kategori, $deskripsi, $latitude_number, $longitude_number, $foto_nama, $no_wa, $jam_buka, $id_toko, $id_pemilik_login);
            } else {
                $stmt = mysqli_prepare($koneksi, "UPDATE toko SET nama_toko = ?, alamat = ?, kategori = ?, deskripsi = ?, latitude = ?, longitude = ?, foto = ?, no_wa = ?, jam_buka = ?, id_pemilik = NULLIF(?, 0) WHERE id_toko = ?");
                mysqli_stmt_bind_param($stmt, "ssssddsssii", $nama_toko, $alamat, $kategori, $deskripsi, $latitude_number, $longitude_number, $foto_nama, $no_wa, $jam_buka, $id_pemilik, $id_toko);
            }

            if (mysqli_stmt_execute($stmt)) {
                if ($foto_nama !== $toko['foto'] && !empty($toko['foto']) && strpos($toko['foto'], 'toko_') === 0) {
                    $foto_lama_path = '../assets/img/' . basename($toko['foto']);
                    if (is_file($foto_lama_path)) {
                        unlink($foto_lama_path);
                    }
                }
                $_SESSION['pesan'] = "Perubahan profil toko '" . $nama_toko . "' berhasil disimpan!";
                header("Location: " . ($is_pemilik ? "../pemilik/dashboard.php" : "dashboard.php"));
                exit;
            } else {
                $error = 'Gagal memperbarui data toko: ' . mysqli_stmt_error($stmt);
                if ($foto_nama !== $toko['foto']) {
                    $foto_baru_path = '../assets/img/' . basename($foto_nama);
                    if (is_file($foto_baru_path)) {
                        unlink($foto_baru_path);
                    }
                }
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
    <title>Edit Profil Toko — <?= htmlspecialchars($toko['nama_toko']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="owner-page">
    <header class="navbar" id="main-navbar">
        <div class="nav-container">
            <a href="../index.php" class="brand">
                <span class="brand-icon"><img src="../assets/img/logo-minang.svg" alt="Logo Peta Rasa Padang"></span>
                <div class="brand-text">
                    <span class="brand-title">Peta Rasa Padang</span>
                    <span class="brand-tagline">Portal Pemilik Toko</span>
                </div>
            </a>
            <div class="nav-links">
                <span class="user-greeting" style="color:var(--emas-terang); font-size:0.85rem; font-weight:600;">
                    <i class="fa-solid fa-user"></i> <?= htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="dashboard.php" class="btn btn-outline-gold btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard Toko</a>
            </div>
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-container form-container">
            <div class="form-card">
                <h2 class="form-title">Edit Profil Toko</h2>
                <p class="form-subtitle">Perbarui informasi toko "<?= htmlspecialchars($toko['nama_toko']); ?>", termasuk foto dan lokasi pada peta.</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="edit.php?id=<?= $id_toko; ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
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

                            <?php if (!$is_pemilik): ?>
                            <div class="form-group">
                                <label for="id_pemilik">Akun Pemilik Toko</label>
                                <select id="id_pemilik" name="id_pemilik" class="form-control">
                                    <option value="0">Belum ditautkan</option>
                                    <?php
                                        $id_pemilik_terpilih = (int)($_POST['id_pemilik'] ?? $toko['id_pemilik'] ?? 0);
                                        while ($pemilik = mysqli_fetch_assoc($result_pemilik)):
                                    ?>
                                        <option value="<?= (int)$pemilik['id_user']; ?>" <?= $id_pemilik_terpilih === (int)$pemilik['id_user'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($pemilik['username']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <small class="text-muted">Hanya akun dengan peran Pemilik Toko yang dapat dipilih.</small>
                            </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="no_wa">Nomor WhatsApp</label>
                                <input type="text" id="no_wa" name="no_wa" class="form-control" maxlength="20" placeholder="6281234567890" value="<?= htmlspecialchars($_POST['no_wa'] ?? $toko['no_wa'] ?? ''); ?>">
                                <small class="text-muted">Format internasional tanpa tanda +, contoh 6281...</small>
                            </div>

                            <div class="form-group">
                                <label for="jam_buka">Jam Buka</label>
                                <input type="text" id="jam_buka" name="jam_buka" class="form-control" maxlength="100" placeholder="Contoh: 08.00 - 21.00 WIB" value="<?= htmlspecialchars($_POST['jam_buka'] ?? $toko['jam_buka'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="foto">Foto Toko (JPG / PNG, maks 2MB)</label>
                                <div class="current-photo-preview" style="margin-bottom: 0.5rem;">
                                    <?php $foto_src = !empty($toko['foto']) ? '../assets/img/' . $toko['foto'] : '../assets/img/default.png'; ?>
                                    <small>Foto saat ini: </small><br>
                                    <img src="<?= htmlspecialchars($foto_src); ?>" alt="Preview" style="height: 60px; border-radius: 4px; object-fit: cover; border: 1px solid #ccc; margin-top: 4px;" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=100&q=80'">
                                </div>
                                <input type="file" id="foto" name="foto" class="form-control" accept=".jpg, .jpeg, .png">
                                <small class="text-muted">Biarkan kosong untuk tetap menggunakan foto saat ini.</small>
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
                        <a href="<?= $is_pemilik ? '../pemilik/dashboard.php' : 'dashboard.php'; ?>" class="btn btn-secondary">Batal</a>
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
