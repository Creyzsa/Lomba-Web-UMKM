<?php
/**
 * admin/hapus.php — Hapus Toko
 * Aturan: Proteksi admin, prepared statement, menu otomatis terhapus lewat ON DELETE CASCADE
 */
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

$id_toko = (int)($_GET['id'] ?? 0);

if ($id_toko > 0) {
    // Ambil info nama toko dan foto sebelum dihapus
    $stmt_info = mysqli_prepare($koneksi, "SELECT nama_toko, foto FROM toko WHERE id_toko = ?");
    mysqli_stmt_bind_param($stmt_info, "i", $id_toko);
    mysqli_stmt_execute($stmt_info);
    $res = mysqli_stmt_get_result($stmt_info);
    $data_toko = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt_info);

    if ($data_toko) {
        $stmt_del = mysqli_prepare($koneksi, "DELETE FROM toko WHERE id_toko = ?");
        mysqli_stmt_bind_param($stmt_del, "i", $id_toko);
        
        if (mysqli_stmt_execute($stmt_del)) {
            // Hapus file foto jika bukan default.png dan bukan toko sample
            if (!empty($data_toko['foto']) && strpos($data_toko['foto'], 'toko_') === 0) {
                $foto_path = '../assets/img/' . $data_toko['foto'];
                if (file_exists($foto_path)) {
                    @unlink($foto_path);
                }
            }
            $_SESSION['pesan'] = "Toko '" . $data_toko['nama_toko'] . "' beserta seluruh menunya berhasil dihapus!";
        } else {
            $_SESSION['pesan'] = "Gagal menghapus toko: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt_del);
    }
}

header("Location: dashboard.php");
exit;
?>