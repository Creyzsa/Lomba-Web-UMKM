<?php
/**
 * pemilik/dashboard.php — Dashboard Pemilik UMKM Modern Minang
 */
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pemilik' || empty($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

$pesan = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);

$id_pemilik = (int)$_SESSION['id_user'];
$active_nav = 'owner';
$nav_prefix = '../';

// Ambil data toko milik user yang sedang login
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT t.id_toko, t.nama_toko, t.kategori, t.alamat, t.deskripsi, t.foto, t.jam_buka, t.no_wa, t.visit_count,
       (SELECT COUNT(*) FROM menu m WHERE m.id_toko = t.id_toko) AS total_menu,
       (SELECT COUNT(*) FROM ulasan u WHERE u.id_toko = t.id_toko) AS total_ulasan,
       (SELECT COALESCE(AVG(u.rating), 0) FROM ulasan u WHERE u.id_toko = t.id_toko) AS rata_rating
     FROM toko t
     WHERE t.id_pemilik = ?
     ORDER BY t.id_toko DESC"
);
mysqli_stmt_bind_param($stmt, "i", $id_pemilik);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$toko_list = [];
$total_menu = 0;
$total_ulasan = 0;
$total_kunjungan = 0;

while ($toko = mysqli_fetch_assoc($result)) {
    $toko_list[] = $toko;
    $total_menu += (int)$toko['total_menu'];
    $total_ulasan += (int)$toko['total_ulasan'];
    $total_kunjungan += (int)($toko['visit_count'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemilik UMKM — Peta Rasa Padang</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=20261016-modern">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .owner-hero {
            background: linear-gradient(135deg, rgba(74, 10, 10, 0.95), rgba(30, 4, 4, 0.98)), url('../assets/img/hero-padang.jpg') center/cover;
            padding: 42px 0 54px;
            color: #fff;
            position: relative;
        }
        .owner-hero-inner {
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .owner-badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            background: rgba(212, 160, 23, 0.18);
            border: 1px solid rgba(247, 208, 112, 0.4);
            border-radius: 999px;
            color: var(--emas-terang);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .owner-header-title {
            font-family: var(--font-heading);
            font-size: 2.1rem;
            font-weight: 800;
            color: var(--emas-terang);
            margin-bottom: 6px;
            line-height: 1.2;
        }
        .owner-header-desc {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.85);
            max-width: 600px;
            line-height: 1.5;
        }
        .btn-daftarkan-toko {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--emas-terang), var(--emas-utama));
            color: var(--merah-gelap);
            padding: 14px 26px;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 999px;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(212, 160, 23, 0.35);
            transition: all 0.25s ease;
        }
        .btn-daftarkan-toko:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 26px rgba(212, 160, 23, 0.5);
            color: #2E0404;
        }

        /* 4 Kolom Statistik Elegan */
        .owner-metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-top: -26px;
            position: relative;
            z-index: 3;
            max-width: 1440px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 28px;
        }
        @media (max-width: 992px) {
            .owner-metrics-grid { grid-template-columns: repeat(2, 1fr); margin-top: 20px; }
        }
        @media (max-width: 576px) {
            .owner-metrics-grid { grid-template-columns: 1fr; }
        }
        .metric-card {
            background: #FFFFFF;
            padding: 22px 24px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-halus);
            box-shadow: 0 10px 25px rgba(18, 19, 22, 0.08);
            display: flex;
            align-items: center;
            gap: 18px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 32px rgba(74, 10, 10, 0.12);
            border-color: var(--emas-utama);
        }
        .metric-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(122, 20, 20, 0.1), rgba(212, 160, 23, 0.15));
            border: 1.5px solid var(--emas-utama);
            display: grid;
            place-items: center;
            font-size: 1.35rem;
            color: var(--merah-utama);
            flex-shrink: 0;
        }
        .metric-number {
            font-family: var(--font-heading);
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--merah-utama);
            line-height: 1.1;
            display: block;
        }
        .metric-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        /* Card Toko Modern */
        .owner-card-refined {
            background: #FFFFFF;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1.5px solid var(--border-halus);
            box-shadow: 0 8px 24px rgba(18, 19, 22, 0.06);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        .owner-card-refined:hover {
            transform: translateY(-6px);
            border-color: var(--emas-utama);
            box-shadow: 0 18px 40px rgba(74, 10, 10, 0.14);
        }
        .owner-media-wrap {
            position: relative;
            height: 210px;
            width: 100%;
            overflow: hidden;
            background: #2E0404;
        }
        .owner-media-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        .owner-card-refined:hover .owner-media-img {
            transform: scale(1.06);
        }
        .owner-cat-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            background: rgba(30, 4, 4, 0.88);
            backdrop-filter: blur(8px);
            color: var(--emas-terang);
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid var(--border-glass);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .owner-card-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .owner-store-name {
            font-family: var(--font-heading);
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--merah-utama);
            margin-bottom: 6px;
            line-height: 1.25;
        }
        .owner-store-loc {
            font-size: 0.84rem;
            color: #64748B;
            line-height: 1.4;
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }
        .owner-store-summary-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: #FAF8F2;
            border-radius: 8px;
            margin-bottom: 14px;
            font-size: 0.82rem;
            color: #334155;
            font-weight: 600;
        }
        .owner-store-summary-row span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .owner-desc-text {
            font-size: 0.88rem;
            color: #475569;
            line-height: 1.5;
            margin-bottom: 18px;
            flex: 1;
        }
        .owner-action-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: auto;
        }
        .owner-action-group .btn {
            font-size: 0.84rem;
            padding: 9px 12px;
            text-align: center;
            justify-content: center;
        }
        .owner-action-group .btn-full {
            grid-column: span 2;
        }
    </style>
</head>
<body class="owner-page">
    <?php include '../navbar.php'; ?>

    <!-- Banner Header Pengelola -->
    <header class="owner-hero">
        <div class="owner-hero-inner">
            <div>
                <span class="owner-badge-tag"><i class="fa-solid fa-store"></i> Ruang Pengelola UMKM</span>
                <h1 class="owner-header-title">Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p class="owner-header-desc">Kelola profil usaha kuliner, atur menu harian &amp; harga, pantau ulasan pembeli, serta perluas jangkauan digital tokomu di Ranah Minang.</p>
            </div>
            <div>
                <a href="tambah-toko.php" class="btn-daftarkan-toko">
                    <i class="fa-solid fa-plus-circle"></i> Daftarkan Toko Baru
                </a>
            </div>
        </div>
    </header>
    <div class="songket-strip" style="margin: 0; height: 28px;"></div>

    <!-- 4 Metrik Statistik Kunci -->
    <section class="owner-metrics-grid">
        <div class="metric-card">
            <div class="metric-icon-wrap"><i class="fa-solid fa-store"></i></div>
            <div>
                <span class="metric-label">Toko Dikelola</span>
                <strong class="metric-number"><?= count($toko_list); ?></strong>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon-wrap"><i class="fa-solid fa-utensils"></i></div>
            <div>
                <span class="metric-label">Total Menu Aktif</span>
                <strong class="metric-number"><?= $total_menu; ?></strong>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon-wrap"><i class="fa-solid fa-star"></i></div>
            <div>
                <span class="metric-label">Ulasan Masuk</span>
                <strong class="metric-number"><?= $total_ulasan; ?></strong>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon-wrap"><i class="fa-solid fa-chart-line"></i></div>
            <div>
                <span class="metric-label">Total Kunjungan</span>
                <strong class="metric-number"><?= $total_kunjungan; ?></strong>
            </div>
        </div>
    </section>

    <!-- Konten Utama Daftar Toko -->
    <main class="section-container" style="margin-top: 45px; margin-bottom: 70px;">
        <div class="section-header" style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px;">
            <div>
                <span class="section-kicker">MANAJEMEN ETALASE</span>
                <h2 class="section-heading">Daftar Toko Kuliner Saya</h2>
                <p class="section-subtext">Pastikan informasi alamat, kontak WhatsApp, jam operasional, dan daftar menu toko Anda selalu diperbarui.</p>
            </div>
            <div>
                <a href="../toko.php" class="btn btn-outline-gold btn-sm"><i class="fa-solid fa-globe"></i> Lihat Etalase Publik</a>
            </div>
        </div>

        <?php if ($pesan !== ''): ?>
            <div class="alert alert-success" style="margin-bottom: 25px;">
                <i class="fa-solid fa-circle-check text-success"></i> <?= htmlspecialchars($pesan); ?>
            </div>
        <?php endif; ?>

        <?php if (count($toko_list) > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 26px;">
                <?php foreach ($toko_list as $toko): 
                    $foto = !empty($toko['foto']) ? '../assets/img/' . $toko['foto'] : '../assets/img/default.png';
                    $id_toko = (int)$toko['id_toko'];
                ?>
                    <article class="owner-card-refined">
                        <div class="owner-media-wrap">
                            <img src="<?= htmlspecialchars($foto); ?>" alt="Foto <?= htmlspecialchars($toko['nama_toko']); ?>" class="owner-media-img" onerror="this.src='../assets/img/default.png'">
                            <span class="owner-cat-badge"><?= htmlspecialchars($toko['kategori'] ?? 'Kuliner'); ?></span>
                        </div>
                        <div class="owner-card-body">
                            <h3 class="owner-store-name"><?= htmlspecialchars($toko['nama_toko']); ?></h3>
                            <p class="owner-store-loc">
                                <i class="fa-solid fa-location-dot" style="color:var(--merah-utama); margin-top:2px;"></i> 
                                <?= htmlspecialchars($toko['alamat'] ?: 'Alamat belum diisi'); ?>
                            </p>

                            <div class="owner-store-summary-row">
                                <span style="color:var(--merah-utama);"><i class="fa-solid fa-utensils"></i> <?= (int)$toko['total_menu']; ?> Menu</span>
                                <span>•</span>
                                <span style="color:#D97706;"><i class="fa-solid fa-star"></i> <?= number_format((float)$toko['rata_rating'], 1); ?> (<?= (int)$toko['total_ulasan']; ?> ulasan)</span>
                                <span>•</span>
                                <span style="color:#0284C7;"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($toko['jam_buka'] ?: '08.00-21.00'); ?></span>
                            </div>

                            <p class="owner-desc-text">
                                <?= htmlspecialchars($toko['deskripsi'] ?: 'Perkenalkan menu andalan dan keunikan cita rasa toko Anda kepada pelanggan.'); ?>
                            </p>

                            <div class="owner-action-group">
                                <a href="menu.php?id_toko=<?= $id_toko; ?>" class="btn btn-gold btn-full">
                                    <i class="fa-solid fa-utensils"></i> Atur &amp; Tambah Menu (<?= (int)$toko['total_menu']; ?>)
                                </a>
                                <a href="edit.php?id=<?= $id_toko; ?>" class="btn btn-secondary">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit Toko
                                </a>
                                <a href="ulasan.php?id_toko=<?= $id_toko; ?>" class="btn btn-secondary">
                                    <i class="fa-solid fa-star"></i> Ulasan (<?= (int)$toko['total_ulasan']; ?>)
                                </a>
                                <a href="../detail.php?id=<?= $id_toko; ?>" target="_blank" class="btn btn-outline-gold btn-full" style="font-size:0.8rem;">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Pratinjau Tampilan Toko di Web
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="background:#FFFFFF; border:2px dashed var(--emas-utama); border-radius:18px; padding:60px 24px; text-align:center; max-width:650px; margin:40px auto; box-shadow:var(--shadow-sm);">
                <div style="width:72px; height:72px; border-radius:50%; background:#FFF7E0; display:grid; place-items:center; margin:0 auto 16px; font-size:2rem; color:var(--emas-utama);">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h3 style="font-family:var(--font-heading); color:var(--merah-utama); font-size:1.6rem; margin-bottom:8px;">Belum Ada Toko Terdaftar</h3>
                <p style="color:#64748B; font-size:0.95rem; line-height:1.6; margin-bottom:24px;">Kamu belum mendaftarkan toko kuliner. Daftarkan usaha kulinermu sekarang untuk mulai menampilkan menu, foto, dan lokasi tokomu di Peta Rasa Padang!</p>
                <a href="tambah-toko.php" class="btn-daftarkan-toko">
                    <i class="fa-solid fa-plus-circle"></i> Daftarkan Toko Pertama Saya
                </a>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../footer.php'; ?>
</body>
</html>
<?php mysqli_stmt_close($stmt); ?>
