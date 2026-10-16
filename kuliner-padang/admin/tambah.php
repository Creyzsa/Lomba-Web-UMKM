<?php
/**
 * admin/tambah.php — Tambah Data Toko Baru
 * Aturan: Proteksi admin, prepared statement, Leaflet map interaktif untuk set lat & lng, upload foto (.jpg/.png maks 2MB)
 */
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

$error = '';
$kategori_list = ['Rumah Makan', 'Sate', 'Kopi', 'Oleh-oleh', 'Camilan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = trim($_POST['nama_toko'] ?? '');
    $alamat    = trim($_POST['alamat'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $latitude  = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');
    $no_wa     = trim($_POST['no_wa'] ?? '');

    // Validasi input
    if (empty($nama_toko) || empty($latitude) || empty($longitude)) {
        $error = 'Nama Toko, Latitude, dan Longitude wajib diisi!';
    } else {
        $foto_nama = 'default.png';

        // Proses upload foto jika ada
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
                // Generate nama file unik
                $foto_nama = 'toko_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                $target_dir = '../assets/img/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                move_uploaded_file($file_tmp, $target_dir . $foto_nama);
            }
        }

        if (empty($error)) {
            // Include no_wa in insert
            $stmt = mysqli_prepare($koneksi, "INSERT INTO toko (nama_toko, alamat, kategori, deskripsi, latitude, longitude, foto, no_wa) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssddss", $nama_toko, $alamat, $kategori, $deskripsi, $latitude, $longitude, $foto_nama, $no_wa);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan'] = "Toko '" . $nama_toko . "' berhasil ditambahkan!";
                header("Location: dashboard.php");
                exit;
            } else {
                $error = 'Gagal menyimpan data toko: ' . mysqli_error($koneksi);
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
    <title>Tambah Toko — Panel Admin</title>
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
                <a href="dashboard.php" class="btn btn-outline-light btn-sm">&larr; Kembali ke Dashboard</a>
            </div>
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-container form-container">
            <div class="form-card">
                <h2 class="form-title">Tambah Toko Kuliner Baru</h2>
                <p class="form-subtitle">Lengkapi formulir di bawah ini. Klik titik pada peta untuk menentukan koordinat otomatis.</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="tambah.php" method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="nama_toko">Nama Toko Kuliner <span class="required">*</span></label>
                                <input type="text" id="nama_toko" name="nama_toko" class="form-control" placeholder="Contoh: RM Lamun Ombak Khatib" required value="<?= htmlspecialchars($_POST['nama_toko'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="kategori">Kategori Kuliner <span class="required">*</span></label>
                                <select id="kategori" name="kategori" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($kategori_list as $kat): ?>
                                        <option value="<?= $kat; ?>" <?= (($_POST['kategori'] ?? '') === $kat) ? 'selected' : ''; ?>>
                                            <?= $kat; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat Lengkap</label>
                                <textarea id="alamat" name="alamat" class="form-control" rows="2" placeholder="Contoh: Jl. Khatib Sulaiman No. 99, Padang><?= htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi Singkat Toko</label>
                                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Ceritakan keistimewaan rasa, sejarah, atau menu andalan toko...><?= htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="no_wa">Nomor WhatsApp Penjual (opsional)</label>
                                <input type="text" id="no_wa" name="no_wa" class="form-control" placeholder="6281234567890" value="<?= htmlspecialchars($_POST['no_wa'] ?? ''); ?>">
                                <small class="text-muted">Format internasional tanpa tanda + (mis. 6281...).</small>
                            </div>

                            <div class="form-group">
                                <label for="foto">Foto Toko (JPG / PNG, maks 2MB)</label>
                                <input type="file" id="foto" name="foto" class="form-control" accept=".jpg, .jpeg, .png">
                                <small class="text-muted">Biarkan kosong jika ingin menggunakan gambar default.</small>
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label>Pilih Lokasi di Peta Padang (Klik pada peta)</label>
                                <div id="mini-map" class="mini-map-box"></div>
                                <small class="text-muted">Geser atau klik peta untuk meletakkan pin koordinat.</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-half">
                                    <label for="latitude">Latitude <span class="required">*</span></label>
                                    <input type="text" id="latitude" name="latitude" class="form-control" placeholder="-0.9471000" required value="<?= htmlspecialchars($_POST['latitude'] ?? '-0.9471000'); ?>">
                                </div>
                                <div class="form-group col-half">
                                    <label for="longitude">Longitude <span class="required">*</span></label>
                                    <input type="text" id="longitude" name="longitude" class="form-control" placeholder="100.4172000" required value="<?= htmlspecialchars($_POST['longitude'] ?? '100.4172000'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Simpan Toko</button>
                        <a href="dashboard.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta mini Padang
        const initialLat = parseFloat(document.getElementById('latitude').value) || -0.9471;
        const initialLng = parseFloat(document.getElementById('longitude').value) || 100.4172;

        const miniMap = L.map('mini-map').setView([initialLat, initialLng], 13);

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