<?php
/**
 * navbar.php — Komponen Navigasi Utama Peta Rasa Padang
 * $active_nav = 'beranda' | 'peta' | 'toko' | 'tentang'
 */
if (!isset($active_nav)) {
    $active_nav = '';
}
?>
<!-- Navbar Interaktif Modern Minang -->
<header class="navbar" id="main-navbar">
    <div class="nav-container">
        <a href="index.php" class="brand">
            <span class="brand-icon">🏛️</span>
            <div class="brand-text">
                <span class="brand-title">Peta Rasa Padang</span>
                <span class="brand-tagline">Etalase Kuliner Ranah Minang</span>
            </div>
        </a>
        
        <nav class="nav-links">
            <a href="index.php" class="nav-link <?= ($active_nav === 'beranda') ? 'active-link' : ''; ?>">Beranda</a>
            <a href="peta.php" class="nav-link <?= ($active_nav === 'peta') ? 'active-link' : ''; ?>">Peta Kuliner</a>
            <a href="toko.php" class="nav-link <?= ($active_nav === 'toko') ? 'active-link' : ''; ?>">Daftar Toko</a>
            <a href="tentang.php" class="nav-link <?= ($active_nav === 'tentang') ? 'active-link' : ''; ?>">Tentang</a>
            
            <?php if (isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin/dashboard.php" class="btn btn-gold btn-sm">⚙️ Panel Admin</a>
                <?php else: ?>
                    <span class="user-greeting">Halo, <?= htmlspecialchars($_SESSION['username']); ?></span>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-outline-gold btn-sm">Keluar</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-gold btn-sm">Masuk</a>
                <a href="register.php" class="btn btn-gold btn-sm">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
