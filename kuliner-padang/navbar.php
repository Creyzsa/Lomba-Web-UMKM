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
            <span class="brand-icon"><img src="assets/img/logo-minang.svg" alt="Logo Peta Rasa Padang"></span>
            <div class="brand-text">
                <span class="brand-title">Peta Rasa Padang</span>
                <span class="brand-tagline">Etalase Kuliner Ranah Minang</span>
            </div>
        </a>
        
        <nav class="nav-links">
            <a href="index.php" class="nav-link <?= ($active_nav === 'beranda') ? 'active-link' : ''; ?>">Beranda</a>
            <a href="peta.php" class="nav-link <?= ($active_nav === 'peta') ? 'active-link' : ''; ?>">Peta Kuliner</a>
            <a href="toko.php" class="nav-link <?= ($active_nav === 'toko') ? 'active-link' : ''; ?>">Daftar Toko</a>
            <a href="khas-daerah.php" class="nav-link <?= ($active_nav === 'khas') ? 'active-link' : ''; ?>">Khas Daerah</a>
            <a href="kuliner-modern.php" class="nav-link <?= ($active_nav === 'modern') ? 'active-link' : ''; ?>">Modern &amp; Inovasi</a>
            <a href="kuliner-budget.php" class="nav-link <?= ($active_nav === 'budget') ? 'active-link' : ''; ?>">Sesuai Budget</a>
            <a href="smart-route.php" class="nav-link <?= ($active_nav === 'route') ? 'active-link' : ''; ?>">Rute Pintar</a>
            <a href="tentang.php" class="nav-link <?= ($active_nav === 'tentang') ? 'active-link' : ''; ?>">Tentang</a>
        </nav>
    </div>
</header>
