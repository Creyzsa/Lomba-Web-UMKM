<?php
/**
 * navbar.php — Komponen Navigasi Utama Peta Rasa Padang
 * $active_nav = 'beranda' | 'peta' | 'toko' | 'khas' | 'modern' | 'budget' | 'route' | 'tentang' | 'owner'
 */
if (!isset($active_nav)) {
    $active_nav = '';
}
$nav_prefix = $nav_prefix ?? '';
?>
<!-- Navbar Interaktif Modern Minang -->
<header class="navbar" id="main-navbar">
    <div class="nav-container">
        <a href="<?= $nav_prefix; ?>index.php" class="brand">
            <span class="brand-icon"><img src="<?= $nav_prefix; ?>assets/img/logo-minang.svg" alt="Logo Peta Rasa Padang"></span>
            <div class="brand-text">
                <span class="brand-title">Peta Rasa Padang</span>
                <span class="brand-tagline">Etalase Kuliner Ranah Minang</span>
            </div>
        </a>
        
        <nav class="nav-links">
            <a href="<?= $nav_prefix; ?>index.php" class="nav-link <?= ($active_nav === 'beranda') ? 'active-link' : ''; ?>">Beranda</a>
            <a href="<?= $nav_prefix; ?>peta.php" class="nav-link <?= ($active_nav === 'peta') ? 'active-link' : ''; ?>">Peta Kuliner</a>
            <a href="<?= $nav_prefix; ?>toko.php" class="nav-link <?= ($active_nav === 'toko') ? 'active-link' : ''; ?>">Daftar Toko</a>
            <a href="<?= $nav_prefix; ?>khas-daerah.php" class="nav-link <?= ($active_nav === 'khas') ? 'active-link' : ''; ?>">Khas Daerah</a>
            <a href="<?= $nav_prefix; ?>kuliner-modern.php" class="nav-link <?= ($active_nav === 'modern') ? 'active-link' : ''; ?>">Modern &amp; Inovasi</a>
            <a href="<?= $nav_prefix; ?>kuliner-budget.php" class="nav-link <?= ($active_nav === 'budget') ? 'active-link' : ''; ?>">Sesuai Budget</a>
            <a href="<?= $nav_prefix; ?>smart-route.php" class="nav-link <?= ($active_nav === 'route') ? 'active-link' : ''; ?>">Rute Pintar</a>
            <a href="<?= $nav_prefix; ?>tentang.php" class="nav-link <?= ($active_nav === 'tentang') ? 'active-link' : ''; ?>">Tentang</a>
            
            <?php if (isset($_SESSION['username'])): ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pemilik'): ?>
                    <a href="<?= $nav_prefix; ?>pemilik/dashboard.php" class="btn btn-gold btn-sm"><i class="fa-solid fa-store"></i> Kelola Toko</a>
                <?php else: ?>
                    <span class="user-greeting" style="color:var(--emas-terang); font-size:0.85rem; font-weight:600; padding:6px 10px;">
                        <i class="fa-solid fa-user"></i> <?= htmlspecialchars($_SESSION['username']); ?>
                    </span>
                <?php endif; ?>
                <a href="<?= $nav_prefix; ?>logout.php" class="btn btn-outline-gold btn-sm">Keluar</a>
            <?php else: ?>
                <a href="<?= $nav_prefix; ?>login.php" class="btn btn-outline-gold btn-sm">Masuk</a>
                <a href="<?= $nav_prefix; ?>register.php" class="btn btn-gold btn-sm">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
