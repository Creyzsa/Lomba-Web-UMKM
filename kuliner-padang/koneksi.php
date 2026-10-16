<?php
/**
 * koneksi.php — Konfigurasi Database db_kuliner_padang
 * Stack: PHP native, MySQL via mysqli
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_kuliner_padang';

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set karakter UTF-8
mysqli_set_charset($koneksi, "utf8mb4");
?>