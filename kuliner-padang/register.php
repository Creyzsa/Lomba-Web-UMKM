<?php
/**
 * register.php — Pendaftaran Akun Pengguna
 * Aturan: Prepared statement, password_hash(), role default 'user'
 */
session_start();
require_once 'koneksi.php';

$error = '';
$sukses = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
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
            // Hash password dan insert user baru
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role_default = 'user';

            $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "sss", $username, $password_hash, $role_default);

            if (mysqli_stmt_execute($stmt_insert)) {
                $sukses = 'Pendaftaran berhasil! Silakan login.';
            } else {
                $error = 'Terjadi kesalahan sistem saat mendaftar: ' . mysqli_error($koneksi);
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
