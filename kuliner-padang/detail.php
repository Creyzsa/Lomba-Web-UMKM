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

$rating_error = '';
$rating_pesan = $_SESSION['pesan_ulasan'] ?? '';
unset($_SESSION['pesan_ulasan']);
$csrf_ulasan = $_SESSION['csrf_ulasan'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_ulasan'] = $csrf_ulasan;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ulasan') {
    if (($_SESSION['role'] ?? '') !== 'user' || empty($_SESSION['id_user'])) {
        $rating_error = 'Masuk menggunakan akun pembeli untuk memberi atau mengubah ulasan.';
    } elseif (!isset($_POST['csrf_token']) || !hash_equals($csrf_ulasan, $_POST['csrf_token'])) {
        $rating_error = 'Permintaan tidak valid. Muat ulang halaman dan coba lagi.';
    } else {
        $rating_input = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
        $komentar = trim($_POST['komentar'] ?? '');
        if ($rating_input === false || $rating_input < 1 || $rating_input > 5) {
            $rating_error = 'Pilih rating antara 1 sampai 5 bintang.';
        } elseif (strlen($komentar) > 2000) {
            $rating_error = 'Ulasan maksimal 2000 karakter.';
        } else {
            $id_user = (int)$_SESSION['id_user'];
            $stmt_ulasan = mysqli_prepare(
                $koneksi,
                "INSERT INTO ulasan (id_toko, id_user, rating, komentar)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE rating = VALUES(rating), komentar = VALUES(komentar),
                   balasan = NULL, replied_at = NULL, created_at = CURRENT_TIMESTAMP"
            );
            mysqli_stmt_bind_param($stmt_ulasan, "iiis", $id_toko, $id_user, $rating_input, $komentar);
            if (mysqli_stmt_execute($stmt_ulasan)) {
                $_SESSION['pesan_ulasan'] = 'Terima kasih! Rating dan ulasan Anda berhasil disimpan.';
                mysqli_stmt_close($stmt_ulasan);
                header("Location: detail.php?id=" . $id_toko . "#ulasan");
                exit;
            }
            $rating_error = 'Ulasan gagal disimpan: ' . mysqli_stmt_error($stmt_ulasan);
            mysqli_stmt_close($stmt_ulasan);
        }
    }
}

$stmt_rating = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS total_ulasan, COALESCE(AVG(rating), 0) AS rata_rating
     FROM ulasan WHERE id_toko = ?"
);
mysqli_stmt_bind_param($stmt_rating, "i", $id_toko);
mysqli_stmt_execute($stmt_rating);
$ringkasan_rating = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_rating));
mysqli_stmt_close($stmt_rating);

$ulasan_saya = null;
if (($_SESSION['role'] ?? '') === 'user' && !empty($_SESSION['id_user'])) {
    $id_user = (int)$_SESSION['id_user'];
    $stmt_saya = mysqli_prepare($koneksi, "SELECT rating, komentar FROM ulasan WHERE id_toko = ? AND id_user = ?");
    mysqli_stmt_bind_param($stmt_saya, "ii", $id_toko, $id_user);
    mysqli_stmt_execute($stmt_saya);
    $ulasan_saya = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_saya));
    mysqli_stmt_close($stmt_saya);
}

$stmt_ulasan_list = mysqli_prepare(
    $koneksi,
    "SELECT u.rating, u.komentar, u.balasan, u.created_at, u.replied_at, users.username
     FROM ulasan u
     INNER JOIN users ON users.id_user = u.id_user
     WHERE u.id_toko = ?
     ORDER BY u.created_at DESC"
);
mysqli_stmt_bind_param($stmt_ulasan_list, "i", $id_toko);
mysqli_stmt_execute($stmt_ulasan_list);
$res_ulasan = mysqli_stmt_get_result($stmt_ulasan_list);

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

        <section class="review-section" id="ulasan">
            <div class="review-heading">
                <div>
                    <span class="section-kicker">CERITA PENGUNJUNG</span>
                    <h2 class="section-heading">Rating & Ulasan</h2>
                    <p class="text-muted">Bagikan pengalaman Anda dan bantu pengunjung lain memilih.</p>
                </div>
                <div class="review-summary" aria-label="Rating rata-rata <?= number_format((float)$ringkasan_rating['rata_rating'], 1); ?> dari 5">
                    <strong><?= number_format((float)$ringkasan_rating['rata_rating'], 1); ?></strong>
                    <span class="review-stars" aria-hidden="true">★</span>
                    <small><?= (int)$ringkasan_rating['total_ulasan']; ?> ulasan</small>
                </div>
            </div>

            <?php if ($rating_pesan !== ''): ?>
                <div class="alert alert-success"><?= htmlspecialchars($rating_pesan); ?></div>
            <?php endif; ?>
            <?php if ($rating_error !== ''): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($rating_error); ?></div>
            <?php endif; ?>

            <?php if (($_SESSION['role'] ?? '') === 'user' && !empty($_SESSION['id_user'])): ?>
                <form method="POST" action="detail.php?id=<?= $id_toko; ?>#ulasan" class="review-form">
                    <input type="hidden" name="action" value="ulasan">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_ulasan); ?>">
                    <label for="rating">Rating Anda</label>
                    <select id="rating" name="rating" class="form-control" required>
                        <option value="">Pilih bintang</option>
                        <?php for ($bintang = 5; $bintang >= 1; $bintang--): ?>
                            <option value="<?= $bintang; ?>" <?= (int)($ulasan_saya['rating'] ?? 0) === $bintang ? 'selected' : ''; ?>>
                                <?= $bintang; ?> <?= $bintang === 1 ? 'bintang' : 'bintang'; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <label for="komentar">Ulasan (opsional)</label>
                    <textarea id="komentar" name="komentar" class="form-control" rows="3" maxlength="2000" placeholder="Ceritakan pengalaman Anda..."><?= htmlspecialchars($ulasan_saya['komentar'] ?? ''); ?></textarea>
                    <button type="submit" class="btn btn-primary">
                        <?= $ulasan_saya ? 'Perbarui Ulasan' : 'Kirim Ulasan'; ?>
                    </button>
                    <?php if ($ulasan_saya): ?>
                        <small class="text-muted">Anda sudah memberi rating. Menyimpan ulasan baru akan menggantikan rating sebelumnya.</small>
                    <?php endif; ?>
                </form>
            <?php elseif (!isset($_SESSION['id_user'])): ?>
                <p class="review-login-note">Ingin memberikan rating? <a href="login.php">Masuk sebagai pembeli</a> terlebih dahulu.</p>
            <?php endif; ?>

            <div class="review-list">
                <?php if (mysqli_num_rows($res_ulasan) > 0): ?>
                    <?php while ($ulasan = mysqli_fetch_assoc($res_ulasan)): ?>
                        <article class="review-card">
                            <div class="review-card-heading">
                                <div>
                                    <strong><?= htmlspecialchars($ulasan['username']); ?></strong>
                                    <span class="review-stars" aria-label="<?= (int)$ulasan['rating']; ?> dari 5 bintang">
                                        <?= str_repeat('★', (int)$ulasan['rating']); ?><span class="review-stars-muted"><?= str_repeat('★', 5 - (int)$ulasan['rating']); ?></span>
                                    </span>
                                </div>
                                <time datetime="<?= htmlspecialchars($ulasan['created_at']); ?>"><?= date('d M Y', strtotime($ulasan['created_at'])); ?></time>
                            </div>
                            <?php if (trim((string)$ulasan['komentar']) !== ''): ?>
                                <p class="review-comment"><?= nl2br(htmlspecialchars($ulasan['komentar'])); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($ulasan['balasan'])): ?>
                                <div class="review-reply">
                                    <strong>Balasan pemilik</strong>
                                    <p><?= nl2br(htmlspecialchars($ulasan['balasan'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="review-empty">Belum ada ulasan. Jadilah yang pertama berbagi pengalaman.</div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Tombol Kembali -->
        <div style="text-align:center;margin-top:40px;">
            <a href="toko.php" class="btn btn-outline-gold">← Kembali ke Daftar Toko</a>
            <a href="peta.php?toko=<?= $id_toko; ?>" class="btn btn-primary">📍 Lihat di Peta Interaktif</a>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    <?php mysqli_stmt_close($stmt_ulasan_list); ?>

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