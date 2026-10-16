<?php
/**
 * footer.php — Komponen Footer Minang
 */
?>
<!-- Footer Nuansa Minang Modern -->
<footer class="footer-minang">
    <div class="footer-songket-top"></div>
    <div class="footer-container">
        <div class="footer-col brand-col">
            <div class="brand footer-brand">
                <span class="brand-icon">🏛️</span>
                <div>
                    <h3 class="footer-logo">Peta Rasa Padang</h3>
                    <small>Warisan Cita Rasa di Ujung Jari</small>
                </div>
            </div>
            <p class="footer-bio">Mendukung digitalisasi dan kemandirian pelaku UMKM kuliner di Kota Padang dan sekitarnya melalui teknologi web terbuka.</p>
            <p class="footer-bio"><strong>Catatan demo:</strong> data toko, menu, lokasi, dan nomor WhatsApp bawaan merupakan ilustrasi, bukan kontak usaha nyata.</p>
        </div>

        <div class="footer-col">
            <h4 class="footer-heading">Halaman</h4>
            <ul class="footer-links">
                <li><a href="<?= $nav_prefix ?? ''; ?>index.php">Beranda</a></li>
                <li><a href="<?= $nav_prefix ?? ''; ?>peta.php">Peta Kuliner Interaktif</a></li>
                <li><a href="<?= $nav_prefix ?? ''; ?>kuliner-budget.php">Kuliner Sesuai Budget</a></li>
                <li><a href="<?= $nav_prefix ?? ''; ?>smart-route.php">Rute Kuliner Pintar</a></li>
                <li><a href="<?= $nav_prefix ?? ''; ?>toko.php">Daftar Toko & Menu</a></li>
                <li><a href="<?= $nav_prefix ?? ''; ?>khas-daerah.php">Kuliner Khas Daerah</a></li>
                <li><a href="<?= $nav_prefix ?? ''; ?>tentang.php">Tentang Inovasi</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="footer-heading">Kategori Kuliner</h4>
            <ul class="footer-links">
                <li><a href="<?= $nav_prefix ?? ''; ?>peta.php?kategori=Rumah+Makan">Rumah Makan Padang</a></li>
                <li><a href="<?= $nav_prefix ?? ''; ?>peta.php?kategori=Sate">Sate Padang & Pariaman</a></li>
                <li><a href="<?= $nav_prefix ?? ''; ?>peta.php?kategori=Kopi">Kedai Kawa & Kopi Solok</a></li>
                <li><a href="<?= $nav_prefix ?? ''; ?>peta.php?kategori=Oleh-oleh">Sanjai & Kerupuk Jangek</a></li>
                <li><a href="<?= $nav_prefix ?? ''; ?>peta.php?kategori=Camilan">Camilan & Minuman Tradisional</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="footer-heading">Akses Pengelola</h4>
            <p class="text-sm">Pemilik UMKM dapat mendaftarkan dan mengelola profil toko serta menu secara mandiri.</p>
            <a href="<?= $nav_prefix ?? ''; ?>pemilik/dashboard.php" class="btn btn-gold btn-sm" style="display:inline-block; margin-top:10px;">Kelola Toko</a>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 <strong>Peta Rasa Padang</strong> — Peserta IT FEST 2026 Web Programming UIN Mahmud Yunus Batusangkar.</p>
    </div>
</footer>