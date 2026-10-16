<?php
/**
 * login.php — Autentikasi Pengguna & Pemilik Toko
 * Aturan: Prepared statement, password_verify(), session_start(), arahkan pemilik ke pemilik/dashboard.php
 */
session_start();
require_once 'koneksi.php';

// Jika sudah login, langsung arahkan ke halaman sesuai peran.
if (isset($_SESSION['role']) && $_SESSION['role'] === 'pemilik') {
    header("Location: pemilik/dashboard.php");
    exit;
} elseif (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT id_user, username, password, role FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            // Verifikasi hash password (mendukung BCRYPT & fallback plain-text lama)
            $password_valid = password_verify($password, $user['password']);
            if (!$password_valid && $password === $user['password']) {
                $password_valid = true;
                // Re-hash otomatis ke BCRYPT yang aman
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt_up = mysqli_prepare($koneksi, "UPDATE users SET password = ? WHERE id_user = ?");
                mysqli_stmt_bind_param($stmt_up, "si", $new_hash, $user['id_user']);
                mysqli_stmt_execute($stmt_up);
                mysqli_stmt_close($stmt_up);
            }

            if ($password_valid) {
                // Set session
                $_SESSION['id_user']  = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                // Arahkan sesuai peran
                if ($user['role'] === 'pemilik') {
                    header("Location: pemilik/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = 'Password salah, silakan coba lagi!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Peta Rasa Padang</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="index.php" class="brand-link">
                    <span class="brand-icon"><img src="assets/img/logo-minang.svg" alt="Logo Peta Rasa Padang" style="width:48px;height:48px;"></span>
                    <h1 class="brand-title">Peta Rasa Padang</h1>
                </a>
                <p class="auth-subtitle">Silakan masuk ke akun Anda</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Form Login -->
            <form action="login.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Masuk Sekarang</button>
            </form>

            <div class="auth-footer">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                <p><a href="index.php" class="back-home">← Kembali ke Beranda Peta</a></p>
            </div>
        </div>
    </div>
</body>
</html>
