<?php
/**
 * koneksi.php — Konfigurasi Database db_kuliner_padang
 * Stack: PHP native, MySQL via mysqli
 */

// Konfigurasi Database db_kuliner_padang
$host = '127.0.0.1'; // Pakai IPv4 langsung agar tidak fallback ke IPv6 (::1) jika localhost bermasalah
$user = 'root';
$pass = '';
$db   = 'db_kuliner_padang';
$port = 3306;

// Tangani error koneksi secara graceful agar tidak memunculkan uncaught warning
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = @mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    die("Koneksi database gagal: MySQL XAMPP belum aktif atau koneksi ditolak (" . mysqli_connect_error() . "). Pastikan MySQL di XAMPP Control Panel sudah berstatus 'Running'.");
}

// Set karakter UTF-8
mysqli_set_charset($koneksi, "utf8mb4");

// Set timezone WIB (Padang, Sumatera Barat)
date_default_timezone_set('Asia/Jakarta');

/**
 * get_status_buka() — Mengecek apakah toko sedang buka atau tutup saat ini
 * @param string|null $jam_str Format contoh: '08.00 - 21.00 WIB'
 * @param string|null $custom_time Format 'H:i' jika ingin uji coba waktu tertentu
 * @return array
 */
function get_status_buka($jam_str, $custom_time = null) {
    if (empty($jam_str)) {
        return [
            'is_open' => false,
            'status_label' => 'Jam Belum Diatur',
            'badge_class' => 'jam-badge-unknown',
            'jam_teks' => 'Jam belum tersedia',
            'keterangan' => 'Jam operasional belum dicantumkan'
        ];
    }

    $jam_clean = trim($jam_str);
    if (preg_match('/(\d{1,2})[.:](\d{2})\s*[-–—]\s*(\d{1,2})[.:](\d{2})/', $jam_clean, $m)) {
        $bukaH = (int)$m[1]; $bukaM = (int)$m[2];
        $tutupH = (int)$m[3]; $tutupM = (int)$m[4];

        $bukaMin = $bukaH * 60 + $bukaM;
        $tutupMin = $tutupH * 60 + $tutupM;
        if ($tutupMin === 0 && $bukaMin > 0) {
            $tutupMin = 24 * 60; // 00.00 malam
        }

        $nowStr = $custom_time ?? date('H:i');
        list($nowH, $nowM) = explode(':', $nowStr);
        $nowMin = (int)$nowH * 60 + (int)$nowM;

        $is_open = false;
        if ($tutupMin > $bukaMin) {
            $is_open = ($nowMin >= $bukaMin && $nowMin < $tutupMin);
        } else {
            // Melewati tengah malam
            $is_open = ($nowMin >= $bukaMin || $nowMin < $tutupMin);
        }

        $bukaFmt = sprintf('%02d.%02d', $bukaH, $bukaM);
        $tutupFmt = sprintf('%02d.%02d', $tutupH, $tutupM);

        if ($is_open) {
            $sisaMenit = ($tutupMin > $nowMin) ? ($tutupMin - $nowMin) : ($tutupMin + 24 * 60 - $nowMin);
            $ket = ($sisaMenit <= 60) ? "Segera tutup dlm {$sisaMenit} mnt" : "Buka sampai {$tutupFmt} WIB";
            return [
                'is_open' => true,
                'status_label' => 'Buka Sekarang',
                'badge_class' => 'jam-badge-open',
                'jam_teks' => $jam_clean,
                'keterangan' => $ket,
                'buka' => $bukaFmt,
                'tutup' => $tutupFmt
            ];
        } else {
            $ket = "Tutup • Buka pukul {$bukaFmt} WIB";
            return [
                'is_open' => false,
                'status_label' => 'Sedang Tutup',
                'badge_class' => 'jam-badge-closed',
                'jam_teks' => $jam_clean,
                'keterangan' => $ket,
                'buka' => $bukaFmt,
                'tutup' => $tutupFmt
            ];
        }
    }

    return [
        'is_open' => false,
        'status_label' => 'Lihat Jadwal',
        'badge_class' => 'jam-badge-unknown',
        'jam_teks' => $jam_clean,
        'keterangan' => $jam_clean
    ];
}
?>