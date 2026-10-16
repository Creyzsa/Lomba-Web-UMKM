<?php
/**
 * track-wa.php — Tracking klik tombol WhatsApp
 */
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_toko = (int)($_POST['id_toko'] ?? 0);
    if ($id_toko > 0) {
        $stmt = mysqli_prepare($koneksi, "UPDATE toko SET whatsapp_views = whatsapp_views + 1 WHERE id_toko = ?");
        mysqli_stmt_bind_param($stmt, "i", $id_toko);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);