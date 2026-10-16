<?php
/**
 * detail.php — Halaman Detail UMKM Kuliner
 * Menampilkan info lengkap toko, menu dengan tombol pesan WA, dan peta lokasi
 */
session_start();
require_once 'koneksi.php';

$id_toko = (int)($_GET['id'] ?? 0);
if ($id_toko <= 0) {
    header("Location: toko.php");
    exit;
}

// Ambil data toko
$stmt = mysqli_prepare($koneksi, "SELECT * FROM toko WHERE id_toko = ?");
mysqli_stmt_bind_param($stmt, "i", $id_toko);
mysqli_stmt_execute($stmt);
$toko = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$toko) {
    header("Location: toko.php");
    exit;
}

// Update visit count
$stmt_up = mysqli_prepare($koneksi, "UPDATE toko SET visit_count = visit_count + 1 WHERE id_toko = ?");
mysqli_stmt_bind_param($stmt_up, "i", $id_toko);
mysqli_stmt_execute($stmt_up);
mysqli_stmt_close($stmt_up);

// Ambil menu
$stmt_m = mysqli_prepare($koneksi, "SELECT * FROM menu WHERE id_toko = ? ORDER BY harga ASC");
mysqli_stmt_bind_param($stmt_m, "i", $id_toko);
mysqli_stmt_execute($stmt_m);
$res_menu = mysqli_stmt_get_result($stmt_m);
mysqli_stmt_close($stmt_m);

$foto = !empty($toko['foto']) ? 'assets/img/' . $toko['foto'] : 'assets/img/default.png';
$maps_url = "https://www.google.com/maps?q=" . $toko['latitude'] . "," . $toko['longitude'];
$no_wa = $toko['no_wa'] ?? '';

$badges = [];
if (!empty($toko['is_khas'])) $badges[] = 'Khas Daerah';
if (!empty($toko['is_hidden_gem'])) $badges[] = 'Hidden Gem';

$active_nav = 'toko';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($toko['nama_toko']); ?> — Peta Rasa Padang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Breadcrumb -->
    <section style="background:#F1F5F9;padding:12px 0;">
        <div style="max-width:1280px;margin:0 auto;padding:0 28px;font-size:0.85rem;color:#64748B;">
            <a href="index.php" style="color:var(--merah-utama);">Beranda</a> 
            / <a href="toko.php" style="color:var(--merah-utama);">Daftar Toko</a> 
            / <span style="font-weight:600;"><?= htmlspecialchars($toko['nama_toko']); ?></span>
        </div>
    </section>

    <!-- Detail Toko -->
    <section class="section-container" style="margin-top:30px;">
        <div class="detail-grid">
            <!-- Kiri: Foto & Info -->
            <div class="detail-left">
                <div class="detail-img-wrapper">
                    <img src="<?= htmlspecialchars($foto); ?>" alt="<?= htmlspecialchars($toko['nama_toko']); ?>" class="detail-img" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=500&q=80'">
                    <span class="card-badge" style="position:absolute;top:14px;right:14px;"><?= htmlspecialchars($toko['kategori']); ?></span>
                </div>
                <div class="detail-badges">
                    <?php foreach ($badges as $b): ?>
                        <span class="badge badge-gold" style="font-size:0.85rem;padding:6px 14px;"><?= htmlspecialchars($b); ?></span>
                    <?php endforeach; ?>
                </div>
                <h1 class="detail-title"><?= htmlspecialchars($toko['nama_toko']); ?></h1>
                <p class="detail-alamat">📍 <?= htmlspecialchars($toko['alamat']); ?></p>
                <?php if (!empty($toko['jam_buka'])): ?>
                    <p class="detail-jam">🕐 <?= htmlspecialchars($toko['jam_buka']); ?></p>
                <?php endif; ?>
                <p class="detail-deskripsi"><?= htmlspecialchars($toko['deskripsi']); ?></p>

                <div class="detail-stat-row">
                    <span>👁️ <?= (int)$toko['visit_count']; ?> kunjungan profil</span>
                </div>

                <div class="detail-actions">
                    <a href="<?= $maps_url; ?>" target="_blank" class="btn btn-primary">🗺️ Buka Google Maps</a>
                    <?php if (!empty($no_wa)): ?>
                        <a href="https://wa.me/<?= htmlspecialchars($no_wa); ?>?text=<?= urlencode("Halo " . $toko['nama_toko'] . ", saya melihat profil Anda di Peta Rasa Padang."); ?>" target="_blank" class="btn" style="background:#16a34a;color:#fff;">💬 Hubungi via WhatsApp</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kanan: Menu & Peta -->
            <div class="detail-right">
                <!-- Map -->
                <div class="map-wrapper" style="margin-bottom:28px;">
                    <div id="detail-map" class="main-map" style="height:300px;"></div>
                </div>

                <!-- Daftar Menu -->
                <div class="detail-menu-section">
                    <h2 class="detail-menu-title">🍽️ Daftar Menu & Harga</h2>
                    <?php if (mysqli_num_rows($res_menu) > 0): ?>
                        <div class="detail-menu-list">
                            <?php while ($m = mysqli_fetch_assoc($res_menu)): 
                                $harga_fmt = 'Rp' . number_format($m['harga'], 0, ',', '.');
                            ?>
                                <div class="detail-menu-item" data-id_menu="<?= $m['id_menu']; ?>" data-nama="<?= htmlspecialchars($m['nama_menu']); ?>" data-harga="<?= $m['harga']; ?>" data-nama_toko="<?= htmlspecialchars($toko['nama_toko']); ?>" data-no_wa="<?= htmlspecialchars($no_wa); ?>">
                                    <div class="menu-item-info">
                                        <strong><?= htmlspecialchars($m['nama_menu']); ?></strong>
                                        <?php if (!empty($m['deskripsi'])): ?>
                                            <small class="text-muted"><?= htmlspecialchars($m['deskripsi']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="menu-item-right">
                                        <span class="menu-item-price"><?= $harga_fmt; ?></span>
                                        <?php if (!empty($no_wa)): ?>
                                            <div class="menu-item-qty">
                                                <button class="qty-btn qty-minus">−</button>
                                                <input type="number" class="qty-input" value="1" min="1" max="99">
                                                <button class="qty-btn qty-plus">+</button>
                                            </div>
                                            <button class="btn btn-sm btn-wa-menu" style="background:#16a34a;color:#fff;white-space:nowrap;">
                                                💬 Pesan
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size:0.78rem;">WA tidak tersedia</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Belum ada menu terdaftar untuk toko ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tombol Kembali -->
        <div style="text-align:center;margin-top:40px;">
            <a href="toko.php" class="btn btn-outline-gold">← Kembali ke Daftar Toko</a>
            <a href="peta.php?toko=<?= $id_toko; ?>" class="btn btn-primary">📍 Lihat di Peta Interaktif</a>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="assets/js/kuliner-utils.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Init peta
            const map = L.map('detail-map', {
                center: [<?= $toko['latitude']; ?>, <?= $toko['longitude']; ?>],
                zoom: 16,
                scrollWheelZoom: false
            });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19, attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            // Marker
            const iconSymbol = '<?= $toko['kategori'] === 'Rumah Makan' ? '🍛' : ($toko['kategori'] === 'Sate' ? '🍢' : ($toko['kategori'] === 'Kopi' ? '☕' : ($toko['kategori'] === 'Oleh-oleh' ? '🎁' : '🥮'))); ?>';
            const marker = L.marker([<?= $toko['latitude']; ?>, <?= $toko['longitude']; ?>], {
                icon: L.divIcon({
                    className: 'custom-gonjong-marker',
                    html: `<div class="marker-pin"><span class="marker-roof">▲</span><span class="marker-symbol">${iconSymbol}</span></div>`,
                    iconSize: [36, 42], iconAnchor: [18, 42], popupAnchor: [0, -38]
                })
            }).addTo(map);
            marker.bindPopup(`<strong><?= htmlspecialchars($toko['nama_toko']); ?></strong><br><?= htmlspecialchars($toko['alamat']); ?>`);

            // Quantity buttons
            document.querySelectorAll('.qty-minus').forEach(btn => {
                btn.addEventListener('click', function () {
                    const input = this.parentElement.querySelector('.qty-input');
                    let val = parseInt(input.value) || 1;
                    if (val > 1) input.value = val - 1;
                });
            });
            document.querySelectorAll('.qty-plus').forEach(btn => {
                btn.addEventListener('click', function () {
                    const input = this.parentElement.querySelector('.qty-input');
                    let val = parseInt(input.value) || 1;
                    if (val < 99) input.value = val + 1;
                });
            });

            // WA buttons
            document.querySelectorAll('.btn-wa-menu').forEach(btn => {
                btn.addEventListener('click', function () {
                    const item = this.closest('.detail-menu-item');
                    const namaToko = item.dataset.nama_toko;
                    const namaMenu = item.dataset.nama;
                    const harga = parseInt(item.dataset.harga);
                    const noHp = item.dataset.no_wa;
                    const jumlah = parseInt(item.querySelector('.qty-input').value) || 1;
                    const pesan = window.kulinerUtils.formatWaPesan(namaToko, namaMenu, jumlah, harga);
                    window.kulinerUtils.bukaWaLink(noHp, pesan);

                    // Track klik WA
                    window.kulinerUtils.catatKlikWa(<?= $id_toko; ?>);
                });
            });
        });
    </script>
</body>
</html>