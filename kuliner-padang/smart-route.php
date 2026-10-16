<?php
/**
 * smart-route.php — Smart Culinary Route
 * Fitur: Input total budget & preferensi, susun rute wisata kuliner 2-4 UMKM
 */
session_start();
require_once 'koneksi.php';
$active_nav = 'route';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rute Kuliner Pintar — Peta Rasa Padang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Tampilan halaman Smart Culinary Route.
           Semua class memakai prefix .rt- agar tidak bentrok dengan style.css. */

        .route-page {
            --r-red: var(--merah-utama, #a4161a);
            --r-gold: #c9971c;
            --r-gold-soft: #fff7e0;
            --r-green: #16a34a;
            --r-green-dark: #15803d;
            --r-ink: #1e293b;
            --r-muted: #64748b;
            --r-line: #e8e2d6;
            --r-radius: 16px;
            --r-shadow: 0 2px 12px rgba(30, 41, 59, .07);
            --r-shadow-hover: 0 10px 28px rgba(30, 41, 59, .14);
        }

        .rt-wrap { max-width: 1180px; margin: 40px auto 0; padding: 0 20px; }

        /* ---------- Form ---------- */
        .rt-form {
            background: #fff;
            border: 1px solid var(--r-line);
            border-top: 4px solid var(--r-gold);
            border-radius: var(--r-radius);
            box-shadow: var(--r-shadow);
            padding: 24px;
            margin-bottom: 32px;
        }
        .rt-form-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1.3fr auto;
            gap: 16px;
            align-items: start;
        }
        .rt-field { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
        .rt-field label { font-size: .85rem; font-weight: 600; color: var(--r-ink); }
        .rt-hint { font-size: .78rem; color: var(--r-muted); }

        .rt-control {
            width: 100%; height: 48px; padding: 0 14px;
            font: inherit; font-size: .95rem; color: var(--r-ink);
            background: #fff; border: 1.5px solid var(--r-line); border-radius: 12px;
            transition: border-color .15s, box-shadow .15s;
        }
        .rt-control:focus { outline: none; border-color: var(--r-gold); box-shadow: 0 0 0 3px rgba(201, 151, 28, .22); }

        .rt-money {
            display: flex; align-items: stretch; height: 48px; overflow: hidden;
            background: #fff; border: 1.5px solid var(--r-line); border-radius: 12px;
            transition: border-color .15s, box-shadow .15s;
        }
        .rt-money:focus-within { border-color: var(--r-gold); box-shadow: 0 0 0 3px rgba(201, 151, 28, .22); }
        .rt-money span {
            display: flex; align-items: center; padding: 0 14px;
            font-weight: 700; color: var(--r-red);
            background: var(--r-gold-soft); border-right: 1.5px solid var(--r-line);
        }
        .rt-money input {
            flex: 1; min-width: 0; padding: 0 14px; border: 0;
            font: inherit; font-size: 1.05rem; font-weight: 600; color: var(--r-ink); background: transparent;
        }
        .rt-money input:focus { outline: none; }

        .rt-submit {
            height: 48px; margin-top: 23px; padding: 0 28px;
            font: inherit; font-weight: 700; color: #fff; white-space: nowrap;
            background: var(--r-red); border: 0; border-radius: 12px; cursor: pointer;
            transition: transform .15s, box-shadow .15s, filter .15s;
        }
        .rt-submit:hover { filter: brightness(1.08); box-shadow: 0 6px 16px rgba(164, 22, 26, .3); transform: translateY(-1px); }
        .rt-submit:focus-visible { outline: 3px solid rgba(201, 151, 28, .5); outline-offset: 2px; }

        .rt-location {
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
            margin-top: 18px; padding: 12px 16px;
            font-size: .88rem; color: var(--r-ink);
            background: var(--r-gold-soft); border: 1px dashed var(--r-gold); border-radius: 12px;
        }
        .rt-location button {
            height: 38px; padding: 0 16px;
            font: inherit; font-size: .85rem; font-weight: 600; color: var(--r-red);
            background: #fff; border: 1.5px solid var(--r-gold); border-radius: 999px; cursor: pointer;
            transition: background .15s, color .15s;
        }
        .rt-location button:hover:not(:disabled) { background: var(--r-gold); color: #fff; }
        .rt-location button:disabled { color: var(--r-green-dark); border-color: var(--r-green); cursor: default; }

        .rt-error {
            margin-top: 14px; padding: 10px 14px;
            font-size: .88rem; font-weight: 500; color: #991b1b;
            background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px;
        }

        /* ---------- Ringkasan ---------- */
        .rt-summary {
            margin-bottom: 28px; padding: 24px;
            background: #fff; border: 1px solid var(--r-line); border-radius: var(--r-radius); box-shadow: var(--r-shadow);
        }
        .rt-summary .section-heading { margin: 4px 0 18px; }

        .rt-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; }
        .rt-stat {
            display: flex; flex-direction: column; gap: 2px; padding: 14px 16px;
            background: #fbf9f4; border: 1px solid var(--r-line); border-radius: 12px;
        }
        .rt-stat-icon { font-size: 1.3rem; line-height: 1; margin-bottom: 4px; }
        .rt-stat-label { font-size: .75rem; font-weight: 600; letter-spacing: .03em; text-transform: uppercase; color: var(--r-muted); }
        .rt-stat-value { font-size: 1.1rem; font-weight: 700; color: var(--r-red); overflow-wrap: anywhere; }
        .rt-stat-value.rt-over { color: #b91c1c; }
        .rt-stat-value.rt-over::before { content: "<i class="fa-solid fa-triangle-exclamation"></i> "; }

        .rt-note { margin: 14px 0 0; font-size: .8rem; line-height: 1.5; color: var(--r-muted); }

        /* ---------- Peta ---------- */
        .rt-map {
            margin-bottom: 32px; overflow: hidden;
            border: 1px solid var(--r-line); border-radius: var(--r-radius); box-shadow: var(--r-shadow);
        }
        .rt-map #route-map { height: 400px; width: 100%; }

        /* ---------- Daftar destinasi (timeline) ---------- */
        .rt-list-title { margin: 0 0 20px; font-size: 1.2rem; color: var(--r-red); }
        .rt-list { display: flex; flex-direction: column; }

        .rt-stop { position: relative; display: grid; grid-template-columns: 44px 1fr; gap: 16px; padding-bottom: 20px; }
        .rt-stop:last-child { padding-bottom: 0; }
        .rt-stop:not(:last-child)::before {
            content: ""; position: absolute; left: 21px; top: 44px; bottom: 0; width: 2px;
            background: repeating-linear-gradient(to bottom, var(--r-gold) 0 6px, transparent 6px 12px);
        }

        .rt-num {
            display: flex; align-items: center; justify-content: center;
            width: 44px; height: 44px;
            font-size: 1.1rem; font-weight: 800; color: #fff;
            background: var(--r-red); border: 3px solid var(--r-gold-soft); border-radius: 50%;
            box-shadow: 0 2px 8px rgba(164, 22, 26, .35);
        }

        .rt-card {
            display: flex; flex-direction: column; gap: 14px; padding: 18px; min-width: 0;
            background: #fff; border: 1px solid var(--r-line); border-radius: var(--r-radius); box-shadow: var(--r-shadow);
            transition: box-shadow .2s, transform .2s;
        }
        .rt-card:hover { box-shadow: var(--r-shadow-hover); transform: translateY(-2px); }

        .rt-card-top { display: flex; gap: 14px; align-items: flex-start; }
        .rt-thumb { flex-shrink: 0; width: 76px; height: 76px; object-fit: cover; border-radius: 12px; background: #f1ece0; }
        .rt-card-info { flex: 1; min-width: 0; }
        .rt-cat { display: inline-block; margin-bottom: 4px; font-size: .75rem; font-weight: 700; color: var(--r-red); }
        .rt-name { margin: 0; font-size: 1.08rem; line-height: 1.3; color: var(--r-ink); }
        .rt-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
        .rt-badge {
            padding: 3px 10px; font-size: .7rem; font-weight: 700; color: #8a6200;
            background: var(--r-gold-soft); border: 1px solid var(--r-gold); border-radius: 999px;
        }
        .rt-addr { margin: 8px 0 0; font-size: .84rem; line-height: 1.45; color: var(--r-muted); }

        .rt-leg {
            display: inline-flex; align-self: flex-start; align-items: center; gap: 4px; width: fit-content;
            padding: 5px 12px; font-size: .78rem; font-weight: 600; color: var(--r-ink);
            background: #f1f5f9; border-radius: 999px;
        }

        .rt-dish {
            display: flex; justify-content: space-between; align-items: baseline; gap: 12px;
            padding: 12px 14px; font-size: .92rem; color: var(--r-ink);
            background: #fbf9f4; border: 1px solid var(--r-line); border-radius: 12px;
        }
        .rt-dish-label { display: block; margin-bottom: 2px; font-size: .72rem; font-weight: 700; letter-spacing: .03em; text-transform: uppercase; color: var(--r-red); }
        .rt-dish-name { font-weight: 600; }
        .rt-dish-price { font-weight: 700; color: var(--r-red); white-space: nowrap; }

        .rt-qty { display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: .86rem; font-weight: 600; color: var(--r-ink); }
        .rt-qty input {
            width: 72px; height: 38px; padding: 0 8px; font: inherit; font-weight: 600; text-align: center;
            border: 1.5px solid var(--r-line); border-radius: 10px;
        }
        .rt-qty input:focus { outline: none; border-color: var(--r-gold); box-shadow: 0 0 0 3px rgba(201, 151, 28, .22); }

        .rt-actions { display: grid; grid-template-columns: 1fr 1.5fr; gap: 10px; }
        .rt-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            height: 44px; padding: 0 12px;
            font: inherit; font-size: .88rem; font-weight: 700; text-decoration: none;
            border: 1.5px solid transparent; border-radius: 12px; cursor: pointer;
            transition: filter .15s, background .15s, color .15s;
        }
        .rt-btn:focus-visible { outline: 3px solid rgba(201, 151, 28, .6); outline-offset: 2px; }
        .rt-btn-maps { color: var(--r-red); background: #fff; border-color: var(--r-red); }
        .rt-btn-maps:hover { background: var(--r-red); color: #fff; }
        .rt-btn-wa { color: #fff; background: var(--r-green); }
        .rt-btn-wa:hover { background: var(--r-green-dark); }
        .rt-btn-off { color: var(--r-muted); background: #f1f5f9; cursor: not-allowed; }

        /* ---------- CTA, empty, panduan ---------- */
        .rt-cta {
            margin-top: 28px; padding: 22px; text-align: center;
            background: var(--r-gold-soft); border: 1px dashed var(--r-gold); border-radius: var(--r-radius);
        }
        .rt-cta p { margin: 0 0 14px; font-size: .9rem; color: var(--r-ink); }
        .rt-cta .rt-btn { display: inline-flex; padding: 0 28px; height: 48px; color: #fff; background: var(--r-red); }
        .rt-cta .rt-btn:hover { filter: brightness(1.1); }

        .rt-empty {
            padding: 40px 20px; text-align: center; color: var(--r-ink);
            background: #fff; border: 1px dashed var(--r-line); border-radius: var(--r-radius);
        }
        .rt-empty p { margin: 0 0 6px; font-size: 1.05rem; }
        .rt-empty small { color: var(--r-muted); }

        .rt-tip {
            display: flex; gap: 16px; align-items: flex-start; padding: 20px 22px;
            font-size: .92rem; line-height: 1.6; color: var(--r-ink);
            background: var(--r-gold-soft); border-left: 4px solid var(--r-gold); border-radius: 12px;
        }
        .rt-tip-icon { font-size: 1.6rem; line-height: 1; }

        /* ---------- Responsif ---------- */
        @media (max-width: 960px) {
            .rt-form-grid { grid-template-columns: 1fr 1fr; }
            .rt-field-budget { grid-column: 1 / -1; }
            .rt-submit { grid-column: 1 / -1; width: 100%; margin-top: 0; }
        }
        @media (max-width: 560px) {
            .rt-wrap { padding: 0 14px; margin-top: 28px; }
            .rt-form, .rt-summary { padding: 18px; }
            .rt-form-grid { grid-template-columns: 1fr; }
            .rt-location { flex-direction: column; align-items: stretch; text-align: center; }
            .rt-map #route-map { height: 300px; }
            .rt-stop { grid-template-columns: 34px 1fr; gap: 10px; }
            .rt-num { width: 34px; height: 34px; font-size: .95rem; }
            .rt-stop:not(:last-child)::before { left: 16px; top: 34px; }
            .rt-thumb { width: 60px; height: 60px; }
            .rt-actions { grid-template-columns: 1fr; }
        }
        @media (prefers-reduced-motion: reduce) {
            .rt-card, .rt-submit { transition: none; }
            .rt-card:hover, .rt-submit:hover { transform: none; }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="route-page">
    <?php include 'navbar.php'; ?>

    <section class="page-banner">
        <div class="page-banner-overlay"></div>
        <div class="page-banner-content">
            <span class="hero-badge"><i class="fa-solid fa-map-location-dot"></i> WISATA KULINER CERDAS</span>
            <h1 class="page-banner-title">Smart Culinary Route</h1>
            <p class="page-banner-desc">Masukkan total anggaran dan pilih preferensi — kami akan menyusun rute wisata kuliner 2–4 UMKM dengan total belanja yang pas di kantong.</p>
        </div>
        <div class="songket-border-bottom"></div>
    </section>

    <main class="rt-wrap">
        <!-- Form -->
        <div class="rt-form">
            <div class="rt-form-grid">
                <div class="rt-field rt-field-budget">
                    <label for="route-budget"><i class="fa-solid fa-wallet"></i> Total Anggaran</label>
                    <div class="rt-money">
                        <span>Rp</span>
                        <input type="number" id="route-budget" inputmode="numeric" placeholder="50000" min="5000" step="5000" value="50000">
                    </div>
                    <small class="rt-hint">Total budget untuk seluruh perjalanan</small>
                </div>
                <div class="rt-field">
                    <label for="route-stops"><i class="fa-solid fa-circle-stop"></i> Jumlah Pemberhentian</label>
                    <select id="route-stops" class="rt-control">
                        <option value="2">2 Tempat</option>
                        <option value="3" selected>3 Tempat</option>
                        <option value="4">4 Tempat</option>
                    </select>
                </div>
                <div class="rt-field">
                    <label for="route-category"><i class="fa-solid fa-utensils"></i> Preferensi Kategori</label>
                    <select id="route-category" class="rt-control">
                        <option value="">Semua Kategori (bervariasi)</option>
                        <option value="Rumah Makan">Makanan Utama</option>
                        <option value="Sate">Sate</option>
                        <option value="Camilan">Camilan &amp; Minuman</option>
                        <option value="Kopi">Kopi &amp; Kawa</option>
                        <option value="Oleh-oleh">Oleh-oleh</option>
                    </select>
                    <small class="rt-hint">Pilih "Semua" untuk variasi maksimal</small>
                </div>
                <button type="button" id="btn-susun-rute" class="rt-submit"><i class="fa-solid fa-map-location-dot"></i> Susun Rute</button>
            </div>

            <div class="rt-location" id="lokasi-route-row" style="display:flex; flex-direction:column; gap:10px; align-items:stretch;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                    <span id="status-lokasi-route"><i class="fa-solid fa-location-dot"></i> <strong>Titik Awal Rute:</strong> Belum ditentukan (optimasi berdasarkan klaster toko terdekat)</span>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <button type="button" id="btn-ambil-lokasi-route"><i class="fa-solid fa-crosshairs"></i> GPS Saya</button>
                        <button type="button" id="btn-pilih-peta-route" style="background:var(--merah-utama); color:#fff; border-color:var(--merah-utama);"><i class="fa-solid fa-map-pin"></i> Tentukan Titik di Peta</button>
                    </div>
                </div>
                <!-- Opsi Cepat Titik Lokasi Populer Padang -->
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; font-size:0.82rem; border-top:1px dashed rgba(212,160,23,0.3); padding-top:8px;">
                    <span style="color:var(--merah-utama); font-weight:700;"><i class="fa-solid fa-compass"></i> Titik Cepat:</span>
                    <button type="button" class="btn-quick-loc-route" data-lat="-0.9525" data-lng="100.3585" data-name="Pasar Raya / Pusat Kota">Pasar Raya</button>
                    <button type="button" class="btn-quick-loc-route" data-lat="-0.9324" data-lng="100.3541" data-name="Pantai Padang (Taplau)">Pantai Padang</button>
                    <button type="button" class="btn-quick-loc-route" data-lat="-0.8985" data-lng="100.3585" data-name="Air Tawar / UNP">Air Tawar (UNP)</button>
                    <button type="button" class="btn-quick-loc-route" data-lat="-0.8842" data-lng="100.3654" data-name="Tunggul Hitam">Tunggul Hitam</button>
                    <button type="button" class="btn-quick-loc-route" data-lat="-0.8685" data-lng="100.3582" data-name="Bungo Pasang">Bungo Pasang</button>
                    <button type="button" class="btn-quick-loc-route" data-lat="-0.8355" data-lng="100.3290" data-name="Lubuk Buaya">Lubuk Buaya</button>
                    <button type="button" class="btn-quick-loc-route" data-lat="-0.8652" data-lng="100.3885" data-name="Aia Pacah / Balai Kota">Aia Pacah</button>
                </div>
                <!-- Interactive Mini Map Picker -->
                <div id="box-picker-peta-route" style="display:none; margin-top:8px; border:1.5px solid var(--emas-utama); border-radius:8px; overflow:hidden;">
                    <div style="background:#2E0404; color:#fff; padding:6px 12px; font-size:0.8rem; display:flex; justify-content:space-between; align-items:center;">
                        <span><i class="fa-solid fa-map-pin" style="color:var(--emas-terang);"></i> <strong>Klik pada peta</strong> atau geser pin merah untuk menentukan titik mulai rute</span>
                        <button type="button" id="btn-tutup-picker-route" style="background:transparent; border:none; color:#fff; font-size:1rem; cursor:pointer;">&times; Tutup</button>
                    </div>
                    <div id="map-picker-route" style="height:220px; width:100%;"></div>
                </div>
            </div>

            <div id="route-error" class="rt-error" role="alert" hidden></div>
        </div>

        <!-- Hasil Rute -->
        <div id="route-hasil-container" style="display:none;">
            <div class="rt-summary">
                <span class="section-kicker">RUTE ANDA</span>
                <h2 class="section-heading">Rencana Wisata Kuliner</h2>
                <div class="rt-stats">
                    <div class="rt-stat">
                        <span class="rt-stat-icon" aria-hidden="true"><i class="fa-solid fa-wallet"></i></span>
                        <span class="rt-stat-label">Anggaran</span>
                        <span class="rt-stat-value" id="rs-anggaran">Rp0</span>
                    </div>
                    <div class="rt-stat">
                        <span class="rt-stat-icon" aria-hidden="true"><i class="fa-solid fa-circle-stop"></i></span>
                        <span class="rt-stat-label">Destinasi</span>
                        <span class="rt-stat-value" id="rs-destinasi">0</span>
                    </div>
                    <div class="rt-stat">
                        <span class="rt-stat-icon" aria-hidden="true"><i class="fa-solid fa-money-bill-wave"></i></span>
                        <span class="rt-stat-label">Estimasi Belanja</span>
                        <span class="rt-stat-value" id="rs-total">Rp0</span>
                    </div>
                    <div class="rt-stat">
                        <span class="rt-stat-icon" aria-hidden="true"><i class="fa-solid fa-arrows-rotate"></i></span>
                        <span class="rt-stat-label">Sisa Budget</span>
                        <span class="rt-stat-value" id="rs-sisa">Rp0</span>
                    </div>
                    <div class="rt-stat">
                        <span class="rt-stat-icon" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
                        <span class="rt-stat-label">Jarak &amp; Waktu</span>
                        <span class="rt-stat-value" id="rs-jarak">0 km</span>
                    </div>
                </div>
                <p class="rt-note">Waktu dihitung dari jarak garis lurus dengan asumsi 25 km/jam dan 15 menit di tiap pemberhentian; kondisi jalan dapat berbeda.</p>
            </div>

            <div class="rt-map">
                <div id="route-map"></div>
            </div>

            <h3 class="rt-list-title"><i class="fa-solid fa-location-dot"></i> Destinasi &amp; Pesanan</h3>
            <div class="rt-list" id="route-destinasi-grid"></div>

            <div class="rt-cta">
                <p>Untuk navigasi belokan demi belokan, buka rute di Google Maps:</p>
                <a id="route-google-maps-all" href="#" target="_blank" rel="noopener" class="rt-btn"><i class="fa-solid fa-map-location-dot"></i> Buka Rute di Google Maps</a>
            </div>
        </div>

        <!-- Empty State -->
        <div id="route-empty" class="rt-empty" style="display:none;">
            <p><i class="fa-regular fa-face-frown"></i> Tidak dapat menyusun rute dengan kriteria tersebut.</p>
            <small>Coba naikkan anggaran, kurangi jumlah destinasi, atau perluas preferensi kategori.</small>
        </div>

        <!-- Panduan -->
        <div id="route-panduan" class="rt-tip">
            <div class="rt-tip-icon" aria-hidden="true"><i class="fa-solid fa-map-location-dot"></i></div>
            <div>
                <strong>Cara kerja:</strong> Masukkan total budget dan jumlah tempat yang ingin dikunjungi.
                Sistem akan menyusun rute 2–4 UMKM dengan menu yang sesuai budget dan diurutkan berdasarkan jarak terdekat. Cocok untuk "jadwal jalan-jalan kuliner"!
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="assets/js/kuliner-utils.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('route-map', { center: [-0.9471, 100.4172], zoom: 13, scrollWheelZoom: true });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            let semuaToko = [];
            let userLat = null, userLng = null;
            let rutePolyline = null;
            const markersLayer = L.layerGroup().addTo(map);

            const kategoriIcons = { 'Rumah Makan': '<i class="fa-solid fa-bowl-rice"></i>', 'Sate': '<i class="fa-solid fa-stroopwafel"></i>', 'Kopi': '<i class="fa-solid fa-mug-hot"></i>', 'Oleh-oleh': '<i class="fa-solid fa-gift"></i>', 'Camilan': '<i class="fa-solid fa-cookie-bite"></i>' };
            const rupiah = n => 'Rp' + Number(n).toLocaleString('id-ID');
            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
            const errBox = document.getElementById('route-error');
            const hasilBox = document.getElementById('route-hasil-container');
            const emptyBox = document.getElementById('route-empty');

            function tampilError(pesan) { errBox.textContent = pesan; errBox.hidden = false; }
            function hapusError() { errBox.hidden = true; }

            fetch('api_toko.php')
                .then(r => r.json())
                .then(res => { if (res.status === 'success') semuaToko = res.data; })
                .catch(e => { console.error(e); tampilError('Gagal memuat data UMKM. Muat ulang halaman.'); });

            // Variabel lokasi acuan (bisa dari GPS, Peta, atau Quick Loc)
            let pickerMapRoute = null;
            let pickerMarkerRoute = null;

            function setAcuanLokasiRoute(lat, lng, nama) {
                userLat = lat;
                userLng = lng;
                document.getElementById('status-lokasi-route').innerHTML = `<i class="fa-solid fa-circle-check text-success"></i> <strong>Titik Awal Rute:</strong> ${nama} (${userLat.toFixed(4)}, ${userLng.toFixed(4)})`;
                hapusError();

                // Update marker picker jika ada
                if (pickerMapRoute) {
                    if (pickerMarkerRoute) {
                        pickerMarkerRoute.setLatLng([lat, lng]);
                    } else {
                        pickerMarkerRoute = L.marker([lat, lng], { draggable: true }).addTo(pickerMapRoute);
                        pickerMarkerRoute.on('dragend', function(e) {
                            const p = e.target.getLatLng();
                            setAcuanLokasiRoute(p.lat, p.lng, 'Titik Pilihan Peta');
                        });
                    }
                    pickerMapRoute.panTo([lat, lng]);
                }

                // Tandai tombol quick loc aktif
                document.querySelectorAll('.btn-quick-loc-route').forEach(btn => {
                    if (btn.dataset.name === nama) {
                        btn.style.background = 'var(--merah-utama)';
                        btn.style.color = '#fff';
                        btn.style.borderColor = 'var(--merah-utama)';
                    } else {
                        btn.style.background = '#fff';
                        btn.style.color = 'var(--teks-utama)';
                        btn.style.borderColor = 'var(--border-halus)';
                    }
                });
            }

            // Quick Loc buttons
            document.querySelectorAll('.btn-quick-loc-route').forEach(btn => {
                btn.addEventListener('click', function() {
                    const lat = parseFloat(this.dataset.lat);
                    const lng = parseFloat(this.dataset.lng);
                    const name = this.dataset.name;
                    setAcuanLokasiRoute(lat, lng, name);
                });
            });

            // Toggle Map Picker
            document.getElementById('btn-pilih-peta-route').addEventListener('click', function() {
                const box = document.getElementById('box-picker-peta-route');
                if (box.style.display === 'none' || box.style.display === '') {
                    box.style.display = 'block';
                    initPickerMapRoute();
                } else {
                    box.style.display = 'none';
                }
            });

            document.getElementById('btn-tutup-picker-route').addEventListener('click', function() {
                document.getElementById('box-picker-peta-route').style.display = 'none';
            });

            function initPickerMapRoute() {
                if (!pickerMapRoute) {
                    const initialLat = userLat || -0.9324;
                    const initialLng = userLng || 100.3541;
                    pickerMapRoute = L.map('map-picker-route').setView([initialLat, initialLng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19, attribution: '&copy; OpenStreetMap'
                    }).addTo(pickerMapRoute);

                    pickerMarkerRoute = L.marker([initialLat, initialLng], { draggable: true }).addTo(pickerMapRoute);
                    pickerMarkerRoute.bindPopup('Geser pin ini ke titik awal keberangkatan').openPopup();

                    pickerMapRoute.on('click', function(e) {
                        pickerMarkerRoute.setLatLng(e.latlng);
                        setAcuanLokasiRoute(e.latlng.lat, e.latlng.lng, 'Titik Pilihan Peta');
                    });

                    pickerMarkerRoute.on('dragend', function(e) {
                        const p = e.target.getLatLng();
                        setAcuanLokasiRoute(p.lat, p.lng, 'Titik Pilihan Peta');
                    });
                }
                setTimeout(() => pickerMapRoute.invalidateSize(), 200);
            }

            // Tombol GPS
            document.getElementById('btn-ambil-lokasi-route').addEventListener('click', function () {
                if (!navigator.geolocation) {
                    document.getElementById('status-lokasi-route').innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Geolokasi tidak didukung.';
                    return;
                }
                const btn = this;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mendeteksi...';
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        setAcuanLokasiRoute(pos.coords.latitude, pos.coords.longitude, 'Lokasi GPS Saya');
                        btn.innerHTML = '<i class="fa-solid fa-circle-check text-success"></i> GPS Aktif';
                    },
                    () => {
                        btn.innerHTML = '<i class="fa-solid fa-crosshairs"></i> GPS Saya';
                        document.getElementById('status-lokasi-route').innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Izin GPS ditolak. Silakan gunakan <strong>Titik Cepat</strong> atau <strong>Tentukan di Peta</strong>.';
                    }
                );
            });

            document.getElementById('btn-susun-rute').addEventListener('click', susunRute);
            document.getElementById('route-budget').addEventListener('keydown', e => { if (e.key === 'Enter') susunRute(); });

            function susunRute() {
                hapusError();
                const totalBudget = parseInt(document.getElementById('route-budget').value) || 0;
                if (totalBudget < 5000) { tampilError('Masukkan total anggaran yang valid (minimal Rp5.000).'); return; }
                const jumlahBerhenti = parseInt(document.getElementById('route-stops').value) || 3;
                const kategoriVal = document.getElementById('route-category').value;

                const preferensi = { jumlahBerhenti };
                if (kategoriVal) preferensi.kategori = [kategoriVal];

                const origin = (userLat !== null) ? { lat: userLat, lng: userLng } : null;
                const hasil = window.kulinerUtils.planCulinaryRoute(semuaToko, totalBudget, preferensi, origin);

                document.getElementById('route-panduan').style.display = 'none';

                if (!hasil || !hasil.rute || hasil.rute.length === 0) {
                    hasilBox.style.display = 'none';
                    emptyBox.style.display = 'block';
                    return;
                }
                emptyBox.style.display = 'none';
                hasilBox.style.display = 'block';
                map.invalidateSize();

                // Ringkasan
                document.getElementById('rs-anggaran').innerText = rupiah(hasil.totalAnggaran);
                document.getElementById('rs-destinasi').innerText = hasil.jumlahBerhenti + ' tempat';
                document.getElementById('rs-total').innerText = rupiah(hasil.totalBiaya);
                const estimasiMenit = Math.ceil((hasil.totalJarak / 25) * 60) + (hasil.jumlahBerhenti * 15);
                document.getElementById('rs-jarak').innerText = `${hasil.totalJarak.toFixed(1)} km / ~${estimasiMenit} menit`;
                const sisaEl = document.getElementById('rs-sisa');
                sisaEl.innerText = rupiah(hasil.sisaAnggaran);
                sisaEl.classList.remove('rt-over');

                // Peta: hapus marker & polyline lama
                markersLayer.clearLayers();
                if (rutePolyline) { map.removeLayer(rutePolyline); rutePolyline = null; }

                const latLngs = [];

                if (origin) {
                    latLngs.push([origin.lat, origin.lng]);
                    L.circleMarker([origin.lat, origin.lng], {
                        radius: 8, color: '#D4A017', fillColor: '#F7D070', fillOpacity: 0.7
                    }).addTo(markersLayer).bindPopup('<strong><i class="fa-solid fa-location-dot"></i> Titik Awal</strong> (Lokasi Anda)');
                }

                const grid = document.getElementById('route-destinasi-grid');
                grid.innerHTML = '';

                hasil.rute.forEach((item, idx) => {
                    const t = item.toko;
                    const menu = item.menuDipilih;
                    latLngs.push([t.latitude, t.longitude]);

                    const iconSymbol = kategoriIcons[t.kategori] || '<i class="fa-solid fa-utensils"></i>';
                    const markerI = L.divIcon({
                        className: 'custom-gonjong-marker',
                        html: `<div class="marker-pin"><span class="marker-roof">▲</span><span class="marker-symbol">${iconSymbol}</span></div>`,
                        iconSize: [36, 42], iconAnchor: [18, 42], popupAnchor: [0, -38]
                    });
                    const marker = L.marker([t.latitude, t.longitude], { icon: markerI });

                    const badgesHtml = (t.badges && t.badges.length)
                        ? `<div class="rt-badges">${t.badges.map(b => `<span class="rt-badge">${esc(b)}</span>`).join('')}</div>`
                        : '';

                    marker.bindPopup(`
                        <div class="minang-popup">
                            <div class="popup-header">
                                <span class="popup-badge"><i class="fa-solid fa-flag-checkered"></i> Pemberhentian ${idx + 1}</span>
                                <h3 class="popup-title">${esc(t.nama_toko)}</h3>
                            </div>
                            <p class="popup-address"><i class="fa-solid fa-location-dot"></i> ${esc(t.alamat)}</p>
                            <p class="popup-desc"><strong><i class="fa-solid fa-utensils"></i> ${esc(menu.nama_menu)}</strong> — ${esc(menu.harga_fmt)}</p>
                            <div class="popup-buttons">
                                <a href="${t.maps_url}" target="_blank" rel="noopener" class="btn-popup-maps"><i class="fa-solid fa-map-location-dot"></i> Google Maps</a>
                            </div>
                        </div>
                    `, { maxWidth: 320 });
                    markersLayer.addLayer(marker);

                    // Jarak dari titik sebelumnya
                    let legHtml = '';
                    if (idx > 0) {
                        const p = hasil.rute[idx - 1].toko;
                        legHtml = `<span class="rt-leg"><i class="fa-solid fa-location-dot"></i> ${window.kulinerUtils.hitungJarak(p.latitude, p.longitude, t.latitude, t.longitude).toFixed(1)} km dari sebelumnya</span>`;
                    } else if (origin) {
                        legHtml = `<span class="rt-leg"><i class="fa-solid fa-location-dot"></i> ${window.kulinerUtils.hitungJarak(origin.lat, origin.lng, t.latitude, t.longitude).toFixed(1)} km dari lokasi Anda</span>`;
                    }

                    const aksiHtml = t.no_wa
                        ? `<label class="rt-qty" for="route-qty-${t.id_toko}">Jumlah porsi
                               <input id="route-qty-${t.id_toko}" class="route-qty-input" type="number" min="1" max="99" step="1" value="1" data-harga="${menu.harga}" aria-label="Jumlah ${esc(menu.nama_menu)}">
                           </label>
                           <div class="rt-actions">
                               <a href="${t.maps_url}" target="_blank" rel="noopener" class="rt-btn rt-btn-maps"><i class="fa-solid fa-map-location-dot"></i> Buka Maps</a>
                               <button type="button" class="rt-btn rt-btn-wa btn-wa-route" data-id-toko="${t.id_toko}" data-nama="${esc(t.nama_toko)}" data-nohp="${esc(t.no_wa)}" data-menu="${esc(menu.nama_menu)}" data-harga="${menu.harga}"><i class="fa-brands fa-whatsapp"></i> Pesan via WhatsApp</button>
                           </div>`
                        : `<div class="rt-actions">
                               <a href="${t.maps_url}" target="_blank" rel="noopener" class="rt-btn rt-btn-maps"><i class="fa-solid fa-map-location-dot"></i> Buka Maps</a>
                               <span class="rt-btn rt-btn-off"><i class="fa-brands fa-whatsapp"></i> WA tidak tersedia</span>
                           </div>`;

                    const stop = document.createElement('div');
                    stop.className = 'rt-stop route-dest-card';
                    stop.innerHTML = `
                        <div class="rt-num" aria-hidden="true">${idx + 1}</div>
                        <article class="rt-card">
                            <div class="rt-card-top">
                                <img class="rt-thumb" src="${t.foto}" alt="${esc(t.nama_toko)}" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=200&q=80'">
                                <div class="rt-card-info">
                                    <span class="rt-cat">${iconSymbol} ${esc(t.kategori)}</span>
                                    <h4 class="rt-name">${esc(t.nama_toko)}</h4>
                                    ${badgesHtml}
                                    <p class="rt-addr"><i class="fa-solid fa-location-dot"></i> ${esc(t.alamat)}</p>
                                </div>
                            </div>
                            ${legHtml}
                            <div class="rt-dish">
                                <div>
                                    <span class="rt-dish-label">Menu pilihan</span>
                                    <span class="rt-dish-name">${esc(menu.nama_menu)}</span>
                                </div>
                                <span class="rt-dish-price">${esc(menu.harga_fmt)}</span>
                            </div>
                            ${aksiHtml}
                        </article>
                    `;
                    grid.appendChild(stop);
                });

                function perbaruiEstimasiPesanan() {
                    const totalPesanan = Array.from(grid.querySelectorAll('.route-qty-input')).reduce((total, input) => {
                        const jumlah = Math.max(1, Math.min(99, parseInt(input.value) || 1));
                        return total + Number(input.dataset.harga) * jumlah;
                    }, 0);
                    const sisa = hasil.totalAnggaran - totalPesanan;
                    document.getElementById('rs-total').innerText = rupiah(totalPesanan);
                    sisaEl.innerText = sisa >= 0 ? rupiah(sisa) : 'Melebihi ' + rupiah(Math.abs(sisa));
                    sisaEl.classList.toggle('rt-over', sisa < 0);
                }

                grid.querySelectorAll('.route-qty-input').forEach(input => {
                    input.addEventListener('input', perbaruiEstimasiPesanan);
                });

                // Polyline rute
                if (latLngs.length > 1) {
                    rutePolyline = L.polyline(latLngs, {
                        color: '#D4A017', weight: 4, opacity: 0.7, dashArray: '10, 10'
                    }).addTo(map);
                    map.fitBounds(rutePolyline.getBounds(), { padding: [60, 60] });
                } else if (latLngs.length === 1) {
                    map.setView(latLngs[0], 15);
                }

                // URL Google Maps
                const originGmaps = `${latLngs[0][0]},${latLngs[0][1]}`;
                const destGmaps = `${latLngs[latLngs.length - 1][0]},${latLngs[latLngs.length - 1][1]}`;
                const waypoints = latLngs.slice(1, -1).map(p => `${p[0]},${p[1]}`).join('|');
                let url = `https://www.google.com/maps/dir/?api=1&origin=${originGmaps}&destination=${destGmaps}`;
                if (waypoints) url += `&waypoints=${waypoints}`;
                document.getElementById('route-google-maps-all').href = url;

                // Tombol WhatsApp
                grid.querySelectorAll('.btn-wa-route').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const jumlah = Math.max(1, Math.min(99, parseInt(this.closest('.route-dest-card').querySelector('.route-qty-input').value) || 1));
                        const pesan = window.kulinerUtils.formatWaPesan(this.dataset.nama, this.dataset.menu, jumlah, parseInt(this.dataset.harga));
                        window.kulinerUtils.bukaWaLink(this.dataset.nohp, pesan);
                        window.kulinerUtils.catatKlikWa(this.dataset.idToko);
                    });
                });
            }
        });
    </script>
</body>
</html>