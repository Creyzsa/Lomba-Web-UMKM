<?php
/**
 * api_toko.php — Endpoint JSON Data Toko Beserta Menu
 * Aturan: Content-Type: application/json, data terstruktur untuk Leaflet
 */
header('Content-Type: application/json; charset=utf-8');
require_once 'koneksi.php';

// Ambil semua data toko
$sql_toko = "SELECT * FROM toko ORDER BY id_toko ASC";
$res_toko = mysqli_query($koneksi, $sql_toko);

$daftar_toko = [];

if ($res_toko) {
    while ($toko = mysqli_fetch_assoc($res_toko)) {
        $id_toko = (int)$toko['id_toko'];

        // Ambil daftar menu untuk toko ini
        $stmt_menu = mysqli_prepare($koneksi, "SELECT id_menu, nama_menu, harga, deskripsi FROM menu WHERE id_toko = ? ORDER BY id_menu ASC");
        mysqli_stmt_bind_param($stmt_menu, "i", $id_toko);
        mysqli_stmt_execute($stmt_menu);
        $res_menu = mysqli_stmt_get_result($stmt_menu);

        $daftar_menu = [];
        while ($menu = mysqli_fetch_assoc($res_menu)) {
            $daftar_menu[] = [
                'id_menu'   => (int)$menu['id_menu'],
                'nama_menu' => $menu['nama_menu'],
                'harga'     => (int)$menu['harga'],
                'harga_fmt' => 'Rp' . number_format((int)$menu['harga'], 0, ',', '.'),
                'deskripsi' => $menu['deskripsi'] ?? ''
            ];
        }
        mysqli_stmt_close($stmt_menu);

        $foto_url = !empty($toko['foto']) ? 'assets/img/' . $toko['foto'] : 'assets/img/default.png';

        // Siapkan badges
        $badges = [];
        if (!empty($toko['is_khas'])) $badges[] = 'Khas Daerah';
        if (!empty($toko['is_hidden_gem'])) $badges[] = 'Hidden Gem';
        $no_wa = $toko['no_wa'] ?? '';

        $daftar_toko[] = [
            'id_toko'       => $id_toko,
            'nama_toko'     => $toko['nama_toko'],
            'alamat'        => $toko['alamat'] ?? '',
            'kategori'      => $toko['kategori'] ?? '',
            'deskripsi'     => $toko['deskripsi'] ?? '',
            'latitude'      => (float)$toko['latitude'],
            'longitude'     => (float)$toko['longitude'],
            'foto'          => $foto_url,
            'no_wa'         => $no_wa,
            'jam_buka'      => $toko['jam_buka'] ?? '',
            'badges'        => $badges,
            'visit_count'   => (int)($toko['visit_count'] ?? 0),
            'maps_url'      => 'https://www.google.com/maps?q=' . $toko['latitude'] . ',' . $toko['longitude'],
            'menu'          => $daftar_menu
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'total'  => count($daftar_toko),
    'data'   => $daftar_toko
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>