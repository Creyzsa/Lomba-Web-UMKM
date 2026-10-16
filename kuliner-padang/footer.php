<?php
/**
 * footer.php — Komponen Footer Minang
 */
$nav_prefix = $nav_prefix ?? '';
?>
<!-- Footer Nuansa Minang Modern -->
<footer class="footer-minang">
    <div class="footer-songket-top"></div>
    <div class="footer-container">
        <div class="footer-col brand-col">
            <div class="brand footer-brand">
                <span class="brand-icon"><img src="<?= $nav_prefix; ?>assets/img/logo-minang.svg" alt="Logo Peta Rasa Padang"></span>
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
                <li><a href="<?= $nav_prefix; ?>index.php">Beranda</a></li>
                <li><a href="<?= $nav_prefix; ?>peta.php">Peta Kuliner Interaktif</a></li>
                <li><a href="<?= $nav_prefix; ?>kuliner-budget.php">Kuliner Sesuai Budget</a></li>
                <li><a href="<?= $nav_prefix; ?>smart-route.php">Rute Kuliner Pintar</a></li>
                <li><a href="<?= $nav_prefix; ?>toko.php">Daftar Toko &amp; Menu</a></li>
                <li><a href="<?= $nav_prefix; ?>khas-daerah.php">Kuliner Khas Daerah</a></li>
                <li><a href="<?= $nav_prefix; ?>kuliner-modern.php">Modern &amp; Inovasi</a></li>
                <li><a href="<?= $nav_prefix; ?>tentang.php">Tentang Inovasi</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="footer-heading">Kategori Kuliner</h4>
            <ul class="footer-links">
                <li><a href="<?= $nav_prefix; ?>peta.php?kategori=Rumah+Makan">Rumah Makan Padang</a></li>
                <li><a href="<?= $nav_prefix; ?>peta.php?kategori=Sate">Sate Padang &amp; Pariaman</a></li>
                <li><a href="<?= $nav_prefix; ?>peta.php?kategori=Kopi">Kedai Kawa &amp; Kopi Solok</a></li>
                <li><a href="<?= $nav_prefix; ?>peta.php?kategori=Oleh-oleh">Sanjai &amp; Kerupuk Jangek</a></li>
                <li><a href="<?= $nav_prefix; ?>peta.php?kategori=Camilan">Camilan &amp; Minuman Tradisional</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="footer-heading">Hubungi Admin</h4>
            <p class="text-sm" style="margin-bottom:8px;">Bantuan &amp; pendaftaran UMKM dapat menghubungi admin via WhatsApp:</p>
            <div style="display:flex; flex-direction:column; gap:8px; margin-top:10px; margin-bottom:14px;">
                <a href="https://wa.me/6282286908467?text=<?= urlencode('Halo Admin Nindy, saya ingin bertanya seputar platform Peta Rasa Padang.'); ?>" target="_blank" style="color:var(--emas-terang); text-decoration:none; font-size:0.9rem; display:inline-flex; align-items:center; gap:8px;">
                    <i class="fa-brands fa-whatsapp" style="color:#22c55e; font-size:1.1rem;"></i> Nindy: +62 822-8690-8467
                </a>
                <a href="https://wa.me/6282285417709?text=<?= urlencode('Halo Admin Cha, saya ingin bertanya seputar platform Peta Rasa Padang.'); ?>" target="_blank" style="color:var(--emas-terang); text-decoration:none; font-size:0.9rem; display:inline-flex; align-items:center; gap:8px;">
                    <i class="fa-brands fa-whatsapp" style="color:#22c55e; font-size:1.1rem;"></i> Cha: +62 822-8541-7709
                </a>
            </div>
            <a href="<?= $nav_prefix; ?>pemilik/dashboard.php" class="btn btn-gold btn-sm" style="display:inline-block;"><i class="fa-solid fa-store"></i> Portal Pemilik Toko</a>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 <strong>Peta Rasa Padang</strong> — Platform Digitalisasi UMKM Kuliner Minangkabau.</p>
    </div>
</footer>
