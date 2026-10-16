<?php
/**
 * pemilik/ulasan.php — Pemilik melihat rating dan membalas ulasan toko
 */
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pemilik' || empty($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

$id_pemilik = (int)$_SESSION['id_user'];
$id_toko = (int)($_GET['id_toko'] ?? 0);
if ($id_toko <= 0) {
    header("Location: dashboard.php");
    exit;
}

$stmt_toko = mysqli_prepare($koneksi, "SELECT id_toko, nama_toko FROM toko WHERE id_toko = ? AND id_pemilik = ?");
mysqli_stmt_bind_param($stmt_toko, "ii", $id_toko, $id_pemilik);
mysqli_stmt_execute($stmt_toko);
$toko = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_toko));
mysqli_stmt_close($stmt_toko);
if (!$toko) {
    header("Location: dashboard.php");
    exit;
}

$csrf_token = $_SESSION['csrf_balasan_ulasan'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_balasan_ulasan'] = $csrf_token;
$pesan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ulasan = (int)($_POST['id_ulasan'] ?? 0);
    $balasan = trim($_POST['balasan'] ?? '');

    if (!isset($_POST['csrf_token']) || !hash_equals($csrf_token, $_POST['csrf_token'])) {
        $error = 'Permintaan tidak valid. Muat ulang halaman dan coba lagi.';
    } elseif ($id_ulasan <= 0 || $balasan === '' || strlen($balasan) > 2000) {
        $error = 'Balasan wajib diisi dan maksimal 2000 karakter.';
    } else {
        $stmt_check = mysqli_prepare($koneksi, "SELECT id_ulasan FROM ulasan WHERE id_ulasan = ? AND id_toko = ?");
        mysqli_stmt_bind_param($stmt_check, "ii", $id_ulasan, $id_toko);
        mysqli_stmt_execute($stmt_check);
        $ulasan_ada = mysqli_stmt_get_result($stmt_check);
        mysqli_stmt_close($stmt_check);

        if (mysqli_num_rows($ulasan_ada) === 0) {
            $error = 'Ulasan tidak ditemukan untuk toko ini.';
        } else {
            $stmt_reply = mysqli_prepare($koneksi, "UPDATE ulasan SET balasan = ?, replied_at = CURRENT_TIMESTAMP WHERE id_ulasan = ? AND id_toko = ?");
            mysqli_stmt_bind_param($stmt_reply, "sii", $balasan, $id_ulasan, $id_toko);
            if (mysqli_stmt_execute($stmt_reply)) {
                $pesan = 'Balasan ulasan berhasil disimpan.';
            } else {
                $error = 'Balasan gagal disimpan: ' . mysqli_stmt_error($stmt_reply);
            }
            mysqli_stmt_close($stmt_reply);
        }
    }
}

$stmt_rating = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total_ulasan, COALESCE(AVG(rating), 0) AS rata_rating FROM ulasan WHERE id_toko = ?");
mysqli_stmt_bind_param($stmt_rating, "i", $id_toko);
mysqli_stmt_execute($stmt_rating);
$ringkasan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_rating));
mysqli_stmt_close($stmt_rating);

$stmt_ulasan = mysqli_prepare(
    $koneksi,
    "SELECT u.id_ulasan, u.rating, u.komentar, u.balasan, u.created_at, u.replied_at, users.username
     FROM ulasan u INNER JOIN users ON users.id_user = u.id_user
     WHERE u.id_toko = ?
     ORDER BY u.created_at DESC"
);
mysqli_stmt_bind_param($stmt_ulasan, "i", $id_toko);
mysqli_stmt_execute($stmt_ulasan);
$result_ulasan = mysqli_stmt_get_result($stmt_ulasan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan <?= htmlspecialchars($toko['nama_toko']); ?> — Peta Rasa Padang</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=owner-reviews-20261016">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="owner-page">
    <?php $nav_prefix = '../'; include '../navbar.php'; ?>

    <section class="page-banner owner-banner">
        <div class="page-banner-overlay"></div>
        <div class="page-banner-content">
            <span class="hero-badge">SUARA PELANGGAN</span>
            <h1 class="page-banner-title">Rating & Ulasan</h1>
            <p class="page-banner-desc"><?= htmlspecialchars($toko['nama_toko']); ?></p>
        </div>
        <div class="songket-border-bottom"></div>
    </section>

    <main class="owner-dashboard">
        <div class="section-container owner-review-container">
            <a href="dashboard.php" class="owner-back-link">← Kembali ke dashboard</a>

            <section class="owner-rating-overview">
                <span class="owner-rating-star" aria-hidden="true">★</span>
                <div>
                    <strong><?= number_format((float)$ringkasan['rata_rating'], 1); ?></strong>
                    <span>dari 5 · <?= (int)$ringkasan['total_ulasan']; ?> ulasan</span>
                </div>
            </section>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-success"><?= htmlspecialchars($pesan); ?></div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="owner-review-list">
                <?php if (mysqli_num_rows($result_ulasan) > 0): ?>
                    <?php while ($ulasan = mysqli_fetch_assoc($result_ulasan)): ?>
                        <article class="owner-review-card">
                            <div class="owner-review-card-heading">
                                <div>
                                    <strong><?= htmlspecialchars($ulasan['username']); ?></strong>
                                    <span class="review-stars" aria-label="<?= (int)$ulasan['rating']; ?> dari 5 bintang">
                                        <?= str_repeat('★', (int)$ulasan['rating']); ?><span class="review-stars-muted"><?= str_repeat('★', 5 - (int)$ulasan['rating']); ?></span>
                                    </span>
                                </div>
                                <time datetime="<?= htmlspecialchars($ulasan['created_at']); ?>"><?= date('d M Y', strtotime($ulasan['created_at'])); ?></time>
                            </div>

                            <p class="review-comment">
                                <?php if (trim((string)$ulasan['komentar']) !== ''): ?>
                                    <?= nl2br(htmlspecialchars($ulasan['komentar'])); ?>
                                <?php else: ?>
                                    <span class="text-muted">Pembeli hanya memberikan rating tanpa komentar.</span>
                                <?php endif; ?>
                            </p>

                            <form method="POST" action="ulasan.php?id_toko=<?= $id_toko; ?>" class="owner-reply-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="id_ulasan" value="<?= (int)$ulasan['id_ulasan']; ?>">
                                <label for="balasan-<?= (int)$ulasan['id_ulasan']; ?>">Balasan untuk pembeli</label>
                                <textarea id="balasan-<?= (int)$ulasan['id_ulasan']; ?>" name="balasan" class="form-control" rows="3" maxlength="2000" required placeholder="Tulis balasan dengan sopan..."><?= htmlspecialchars($ulasan['balasan'] ?? ''); ?></textarea>
                                <button type="submit" class="btn btn-primary"><?= !empty($ulasan['balasan']) ? 'Perbarui Balasan' : 'Kirim Balasan'; ?></button>
                            </form>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="owner-empty-state">
                        <span class="owner-empty-icon" aria-hidden="true">⭐</span>
                        <h2>Belum ada rating</h2>
                        <p>Ulasan pembeli akan tampil di sini setelah mereka memberikan rating pada halaman detail toko Anda.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../footer.php'; ?>
</body>
</html>
<?php mysqli_stmt_close($stmt_ulasan); ?>
