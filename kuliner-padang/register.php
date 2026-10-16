<?php
/**
 * register.php — Pendaftaran Akun Pengguna
 * Aturan: Prepared statement, password_hash(), pilihan peran pembeli / pemilik toko
 *
 * Peran di tabel users.role:
 *   'user'    = Pembeli       (daftar sendiri)
 *   'pemilik' = Pemilik toko  (daftar sendiri)
 *   'admin'   = Admin         (TIDAK bisa dipilih di form; dibuat manual lewat database)
 *
 * Jika kolom role bertipe ENUM, jalankan sekali:
 *   ALTER TABLE users MODIFY role ENUM('admin','pemilik','user') NOT NULL DEFAULT 'user';
 */
session_start();
require_once 'koneksi.php';

// Daftar peran yang boleh dipilih saat mendaftar (whitelist). 'admin' sengaja tidak ada.
$role_tersedia = [
    'user'    => ['label' => 'Pembeli',       'ikon' => '🛍️', 'desc' => 'Cari kuliner, filter sesuai budget, dan pesan lewat WhatsApp.'],
    'pemilik' => ['label' => 'Pemilik Toko',  'ikon' => '🏪', 'desc' => 'Daftarkan usaha, kelola menu & harga, dan lihat statistik toko.'],
];

$error = '';
$sukses = '';
$role_dipilih = 'user';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';
    $role_input = $_POST['role'] ?? '';

    if (isset($role_tersedia[$role_input])) {
        $role_dipilih = $role_input;
    }

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } elseif (!isset($role_tersedia[$role_input])) {
        $error = 'Pilih jenis akun: Pembeli atau Pemilik Toko.';
    } elseif (strlen($username) < 4) {
        $error = 'Username minimal 4 karakter!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek apakah username sudah ada
        $stmt_cek = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt_cek, "s", $username);
        mysqli_stmt_execute($stmt_cek);
        mysqli_stmt_store_result($stmt_cek);

        if (mysqli_stmt_num_rows($stmt_cek) > 0) {
            $error = 'Username sudah terdaftar, silakan pilih yang lain.';
        } else {
            // Hash password dan insert user baru dengan peran yang dipilih
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "sss", $username, $password_hash, $role_input);

            if (mysqli_stmt_execute($stmt_insert)) {
                $sukses = ($role_input === 'pemilik')
                    ? 'Akun Pemilik Toko berhasil dibuat! Silakan login untuk mendaftarkan usaha kamu.'
                    : 'Akun Pembeli berhasil dibuat! Silakan login untuk mulai menjelajah kuliner.';
            } else {
                error_log('Register gagal: ' . mysqli_error($koneksi));
                $error = 'Terjadi kesalahan sistem saat mendaftar. Silakan coba lagi.';
            }
            mysqli_stmt_close($stmt_insert);
        }
        mysqli_stmt_close($stmt_cek);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — Peta Rasa Padang</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Pilihan peran */
        .role-label { display: block; margin-bottom: 8px; font-weight: 600; }
        .role-options { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .role-option { position: relative; }
        .role-option input {
            position: absolute; inset: 0; width: 100%; height: 100%;
            opacity: 0; cursor: pointer; margin: 0;
        }
        .role-card {
            display: flex; flex-direction: column; gap: 4px; height: 100%;
            padding: 14px; text-align: left;
            background: #fff; border: 2px solid #e8e2d6; border-radius: 14px;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .role-icon { font-size: 1.6rem; line-height: 1; }
        .role-name { font-weight: 700; font-size: .95rem; color: #1e293b; }
        .role-desc { font-size: .76rem; line-height: 1.4; color: #64748b; }
        .role-option:hover .role-card { border-color: #c9971c; }
        .role-option input:checked + .role-card {
            border-color: #c9971c; background: #fff7e0;
            box-shadow: 0 0 0 3px rgba(201, 151, 28, .22);
        }
        .role-option input:focus-visible + .role-card { outline: 3px solid rgba(201, 151, 28, .6); outline-offset: 2px; }
        @media (max-width: 480px) { .role-options { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="index.php" class="brand-link">
                    <span class="gonjong-icon">🏛️</span>
                    <h1 class="brand-title">Peta Rasa Padang</h1>
                </a>
                <p class="auth-subtitle">Daftarkan akun untuk menjelajahi kekayaan kuliner Minang</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($sukses)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($sukses); ?>
                    <a href="login.php" class="alert-link">Klik di sini untuk login</a>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="auth-form">
                <div class="form-group">
                    <span class="role-label" id="role-label">Daftar sebagai</span>
                    <div class="role-options" role="radiogroup" aria-labelledby="role-label">
                        <?php foreach ($role_tersedia as $kode => $r): ?>
                            <label class="role-option">
                                <input type="radio" name="role" value="<?= htmlspecialchars($kode); ?>" <?= $role_dipilih === $kode ? 'checked' : ''; ?> required>
                                <span class="role-card">
                                    <span class="role-icon" aria-hidden="true"><?= $r['ikon']; ?></span>
                                    <span class="role-name"><?= htmlspecialchars($r['label']); ?></span>
                                    <span class="role-desc"><?= htmlspecialchars($r['desc']); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Contoh: kulineria" required value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>

                <div class="form-group">
                    <label for="konfirmasi_password">Ulangi Password</label>
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-control" placeholder="Ketik ulang password Anda" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Daftar Akun Baru</button>
            </form>

            <div class="auth-footer">
                <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
                <p><a href="index.php" class="back-home">← Kembali ke Beranda Peta</a></p>
            </div>
        </div>
    </div>
</body>
</html>