<?php
/**
 * kuliner-budget.php — Mode Kuliner Sesuai Budget
 * Fitur: Input anggaran, filter menu yang terjangkau, tampilkan di peta
 */
session_start();
require_once 'koneksi.php';
$active_nav = 'budget';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuliner Sesuai Budget — Peta Rasa Padang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
    /* =========================================================
       Tampilan halaman "Kuliner Sesuai Budget"
       Semua class memakai prefix .bgt- agar tidak bentrok dengan style.css.
       ========================================================= */

    .budget-page {
        --b-red: var(--merah-utama, #a4161a);
        --b-gold: #c9971c;
        --b-gold-soft: #fff7e0;
        --b-green: #16a34a;
        --b-green-dark: #15803d;
        --b-ink: #1e293b;
        --b-muted: #64748b;
        --b-line: #e8e2d6;
        --b-bg: #ffffff;
        --b-radius: 16px;
        --b-shadow: 0 2px 12px rgba(30, 41, 59, .07);
        --b-shadow-hover: 0 10px 28px rgba(30, 41, 59, .14);
    }

    .bgt-wrap {
        max-width: 1180px;
        margin: 40px auto 0;
        padding: 0 20px;
    }

    /* ---------- Form ---------- */
    .bgt-form {
        background: var(--b-bg);
        border: 1px solid var(--b-line);
        border-top: 4px solid var(--b-gold);
        border-radius: var(--b-radius);
        box-shadow: var(--b-shadow);
        padding: 24px;
        margin-bottom: 32px;
    }

    .bgt-form-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr 1fr auto;
        gap: 16px;
        align-items: end;
    }

    .bgt-field { display: flex; flex-direction: column; gap: 6px; min-width: 0; }

    .bgt-field label {
        font-size: .85rem;
        font-weight: 600;
        color: var(--b-ink);
    }

    .bgt-control {
        width: 100%;
        height: 48px;
        padding: 0 14px;
        font: inherit;
        font-size: .95rem;
        color: var(--b-ink);
        background: #fff;
        border: 1.5px solid var(--b-line);
        border-radius: 12px;
        transition: border-color .15s, box-shadow .15s;
    }

    .bgt-control:focus {
        outline: none;
        border-color: var(--b-gold);
        box-shadow: 0 0 0 3px rgba(201, 151, 28, .22);
    }

    .bgt-money {
        display: flex;
        align-items: stretch;
        height: 48px;
        border: 1.5px solid var(--b-line);
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
    }

    .bgt-money:focus-within {
        border-color: var(--b-gold);
        box-shadow: 0 0 0 3px rgba(201, 151, 28, .22);
    }

    .bgt-money span {
        display: flex;
        align-items: center;
        padding: 0 14px;
        font-weight: 700;
        color: var(--b-red);
        background: var(--b-gold-soft);
        border-right: 1.5px solid var(--b-line);
    }

    .bgt-money input {
        flex: 1;
        min-width: 0;
        border: 0;
        padding: 0 14px;
        font: inherit;
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--b-ink);
        background: transparent;
    }

    .bgt-money input:focus { outline: none; }

    .bgt-hint { font-size: .78rem; color: var(--b-muted); }

    .bgt-submit {
        height: 48px;
        padding: 0 28px;
        font: inherit;
        font-weight: 700;
        color: #fff;
        background: var(--b-red);
        border: 0;
        border-radius: 12px;
        cursor: pointer;
        white-space: nowrap;
        transition: transform .15s, box-shadow .15s, filter .15s;
    }

    .bgt-submit:hover { filter: brightness(1.08); box-shadow: 0 6px 16px rgba(164, 22, 26, .3); transform: translateY(-1px); }
    .bgt-submit:focus-visible { outline: 3px solid rgba(201, 151, 28, .5); outline-offset: 2px; }

    /* Baris lokasi */
    .bgt-location {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
        padding: 12px 16px;
        background: var(--b-gold-soft);
        border: 1px dashed var(--b-gold);
        border-radius: 12px;
        font-size: .88rem;
        color: var(--b-ink);
    }

    .bgt-location button {
        height: 38px;
        padding: 0 16px;
        font: inherit;
        font-size: .85rem;
        font-weight: 600;
        color: var(--b-red);
        background: #fff;
        border: 1.5px solid var(--b-gold);
        border-radius: 999px;
        cursor: pointer;
        transition: background .15s;
    }

    .bgt-location button:hover:not(:disabled) { background: var(--b-gold); color: #fff; }
    .bgt-location button:disabled { color: var(--b-green-dark); border-color: var(--b-green); cursor: default; }

    .bgt-error {
        margin-top: 14px;
        padding: 10px 14px;
        font-size: .88rem;
        font-weight: 500;
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
    }

    /* ---------- Hasil ---------- */
    .bgt-results-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .bgt-results-head .section-heading { margin: 4px 0; }
    .bgt-results-head .section-subtext { margin: 0; color: var(--b-muted); }

    .bgt-count {
        padding: 8px 16px;
        font-size: .85rem;
        font-weight: 700;
        color: var(--b-red);
        background: var(--b-gold-soft);
        border: 1px solid var(--b-gold);
        border-radius: 999px;
        white-space: nowrap;
    }

    .bgt-map {
        margin-bottom: 32px;
        border: 1px solid var(--b-line);
        border-radius: var(--b-radius);
        box-shadow: var(--b-shadow);
        overflow: hidden;
    }

    .bgt-map #budget-map { height: 380px; width: 100%; }

    /* ---------- Grid & kartu ---------- */
    .bgt-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 24px;
    }

    .bgt-card {
        display: flex;
        flex-direction: column;
        background: var(--b-bg);
        border: 1px solid var(--b-line);
        border-radius: var(--b-radius);
        box-shadow: var(--b-shadow);
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }

    .bgt-card:hover { transform: translateY(-4px); box-shadow: var(--b-shadow-hover); }

    .bgt-media { position: relative; aspect-ratio: 16 / 10; background: #f1ece0; }
    .bgt-media img { width: 100%; height: 100%; object-fit: cover; display: block; }

    .bgt-chip,
    .bgt-dist {
        position: absolute;
        top: 12px;
        padding: 5px 12px;
        font-size: .75rem;
        font-weight: 700;
        border-radius: 999px;
        backdrop-filter: blur(4px);
    }

    .bgt-chip { left: 12px; color: #fff; background: rgba(164, 22, 26, .92); }
    .bgt-dist { right: 12px; color: var(--b-ink); background: rgba(255, 255, 255, .94); }

    .bgt-body { display: flex; flex-direction: column; flex: 1; gap: 12px; padding: 18px; }

    .bgt-title { margin: 0; font-size: 1.08rem; line-height: 1.3; color: var(--b-ink); }

    .bgt-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }

    .bgt-badge {
        padding: 3px 10px;
        font-size: .7rem;
        font-weight: 700;
        color: #8a6200;
        background: var(--b-gold-soft);
        border: 1px solid var(--b-gold);
        border-radius: 999px;
    }

    .bgt-addr {
        margin: 0;
        font-size: .84rem;
        line-height: 1.45;
        color: var(--b-muted);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .bgt-menu {
        padding: 12px 14px;
        background: #fbf9f4;
        border: 1px solid var(--b-line);
        border-radius: 12px;
    }

    .bgt-menu-title {
        margin: 0 0 8px;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
        color: var(--b-red);
    }

    .bgt-menu ul { list-style: none; margin: 0; padding: 0; }

    .bgt-menu li {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 12px;
        padding: 6px 0;
        font-size: .88rem;
        color: var(--b-ink);
        border-bottom: 1px dashed var(--b-line);
    }

    .bgt-menu li:last-child { border-bottom: 0; padding-bottom: 0; }
    .bgt-menu li:first-child { padding-top: 0; }
    .bgt-menu .bgt-price { font-weight: 700; color: var(--b-red); white-space: nowrap; }
    .bgt-menu .bgt-more { justify-content: flex-start; font-size: .8rem; color: var(--b-muted); }

    .bgt-from {
        margin-top: auto;
        font-size: .85rem;
        color: var(--b-muted);
    }

    .bgt-from strong { font-size: 1.05rem; color: var(--b-red); }

    /* Pesan */
    .bgt-order {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        font-size: .84rem;
        color: var(--b-ink);
    }

    .bgt-order-name { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .bgt-qty {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        font-weight: 600;
    }

    .bgt-qty input {
        width: 64px;
        height: 38px;
        padding: 0 8px;
        font: inherit;
        font-weight: 600;
        text-align: center;
        border: 1.5px solid var(--b-line);
        border-radius: 10px;
    }

    .bgt-qty input:focus { outline: none; border-color: var(--b-gold); box-shadow: 0 0 0 3px rgba(201, 151, 28, .22); }

    .bgt-actions {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 10px;
    }

    .bgt-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 44px;
        padding: 0 12px;
        font: inherit;
        font-size: .88rem;
        font-weight: 700;
        text-decoration: none;
        border-radius: 12px;
        border: 1.5px solid transparent;
        cursor: pointer;
        transition: filter .15s, background .15s, color .15s;
    }

    .bgt-btn-detail { color: var(--b-red); background: #fff; border-color: var(--b-red); }
    .bgt-btn-detail:hover { background: var(--b-red); color: #fff; }

    .bgt-btn-wa { color: #fff; background: var(--b-green); }
    .bgt-btn-wa:hover { background: var(--b-green-dark); }

    .bgt-btn-off { grid-column: 1 / -1; color: var(--b-muted); background: #f1f5f9; cursor: not-allowed; }

    .bgt-btn:focus-visible { outline: 3px solid rgba(201, 151, 28, .6); outline-offset: 2px; }

    /* ---------- Empty & panduan ---------- */
    .bgt-empty {
        text-align: center;
        padding: 40px 20px;
        color: var(--b-ink);
        background: var(--b-bg);
        border: 1px dashed var(--b-line);
        border-radius: var(--b-radius);
    }

    .bgt-empty p { margin: 0 0 6px; font-size: 1.05rem; }
    .bgt-empty small { color: var(--b-muted); }

    .bgt-tip {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        padding: 20px 22px;
        font-size: .92rem;
        line-height: 1.6;
        color: var(--b-ink);
        background: var(--b-gold-soft);
        border-left: 4px solid var(--b-gold);
        border-radius: 12px;
    }

    .bgt-tip-icon { font-size: 1.6rem; line-height: 1; }

    /* ---------- Responsif ---------- */
    @media (max-width: 960px) {
        .bgt-form-grid { grid-template-columns: 1fr 1fr; }
        .bgt-field-budget { grid-column: 1 / -1; }
        .bgt-submit { width: 100%; grid-column: 1 / -1; }
    }

    @media (max-width: 560px) {
        .bgt-wrap { padding: 0 14px; margin-top: 28px; }
        .bgt-form { padding: 18px; }
        .bgt-form-grid { grid-template-columns: 1fr; }
        .bgt-location { flex-direction: column; align-items: stretch; text-align: center; }
        .bgt-results-head { align-items: flex-start; }
        .bgt-map #budget-map { height: 300px; }
        .bgt-grid { grid-template-columns: 1fr; }
    }

    @media (prefers-reduced-motion: reduce) {
        .bgt-card, .bgt-submit { transition: none; }
        .bgt-card:hover, .bgt-submit:hover { transform: none; }
    }
    </style>
</head>
<body class="budget-page">
    <?php include 'navbar.php'; ?>

    <section class="page-banner">
        <div class="page-banner-overlay"></div>
        <div class="page-banner-content">
            <span class="hero-badge">💰 MAKAN ENAK TANPA BONCOS</span>
            <h1 class="page-banner-title">Kuliner Sesuai Budget</h1>
            <p class="page-banner-desc">Masukkan anggaran kamu, dan kami akan menampilkan menu UMKM yang harganya pas di kantong — lengkap dengan lokasi di peta.</p>
        </div>
        <div class="songket-border-bottom"></div>
    </section>

    <main class="bgt-wrap">
        <!-- Form -->
        <div class="bgt-form">
            <div class="bgt-form-grid">
                <div class="bgt-field bgt-field-budget">
                    <label for="budget-input">💰 Anggaran Kamu</label>
                    <div class="bgt-money">
                        <span>Rp</span>
                        <input type="number" id="budget-input" inputmode="numeric" placeholder="15000" min="1000" step="1000" value="15000">
                    </div>
                    <small class="bgt-hint">Minimal Rp1.000 — cari menu yang sesuai kantong</small>
                </div>
                <div class="bgt-field">
                    <label for="jarak-input">📍 Jarak (opsional)</label>
                    <select id="jarak-input" class="bgt-control">
                        <option value="">Tanpa batas jarak</option>
                        <option value="1">Dalam 1 km</option>
                        <option value="3">Dalam 3 km</option>
                        <option value="5">Dalam 5 km</option>
                        <option value="10">Dalam 10 km</option>
                    </select>
                </div>
                <div class="bgt-field">
                    <label for="kategori-budget">🍽️ Kategori</label>
                    <select id="kategori-budget" class="bgt-control">
                        <option value="">Semua Kategori</option>
                        <option value="Rumah Makan">Rumah Makan</option>
                        <option value="Sate">Sate</option>
                        <option value="Kopi">Kopi &amp; Kawa</option>
                        <option value="Camilan">Camilan</option>
                        <option value="Oleh-oleh">Oleh-oleh</option>
                    </select>
                </div>
                <button type="button" id="btn-cari-budget" class="bgt-submit">🔍 Cari Menu</button>
            </div>

            <div class="bgt-location" id="lokasi-row">
                <span id="status-lokasi">📍 Gunakan lokasi saya untuk urutkan berdasarkan jarak terdekat</span>
                <button type="button" id="btn-ambil-lokasi">📍 Aktifkan Lokasi</button>
            </div>

            <div id="budget-error" class="bgt-error" role="alert" hidden></div>
        </div>

        <!-- Hasil -->
        <div id="budget-hasil-container" style="display:none;">
            <div class="bgt-results-head">
                <div>
                    <span class="section-kicker">HASIL PENCARIAN</span>
                    <h2 class="section-heading" id="budget-heading">Menu dalam Budget</h2>
                    <p class="section-subtext" id="budget-subtext"></p>
                </div>
                <span id="budget-counter" class="bgt-count">0 ditemukan</span>
            </div>

            <div class="bgt-map">
                <div id="budget-map"></div>
            </div>

            <div class="bgt-grid" id="budget-grid"></div>

            <div id="budget-empty" class="bgt-empty" style="display:none;">
                <p>😕 Tidak ada menu yang sesuai dengan budget <strong>Rp<span id="empty-budget-val">0</span></strong>.</p>
                <small>Coba naikkan anggaran atau hapus filter jarak.</small>
            </div>
        </div>

        <!-- Panduan -->
        <div id="budget-panduan" class="bgt-tip">
            <div class="bgt-tip-icon" aria-hidden="true">💡</div>
            <div>
                <strong>Tips:</strong> Masukkan nominal anggaran makan kamu (contoh: Rp15.000 untuk sekali makan).
                Sistem akan menampilkan UMKM yang memiliki menu dengan harga sesuai budget, diurutkan dari yang paling cocok.
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="assets/js/kuliner-utils.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('budget-map', { center: [-0.9471, 100.4172], zoom: 13, scrollWheelZoom: true });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const markersLayer = L.layerGroup().addTo(map);
            const kategoriIcons = { 'Rumah Makan': '🍛', 'Sate': '🍢', 'Kopi': '☕', 'Oleh-oleh': '🎁', 'Camilan': '🥮' };
            const rupiah = n => 'Rp' + Number(n).toLocaleString('id-ID');
            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
            const errBox = document.getElementById('budget-error');
            let semuaToko = [];
            let userLat = null, userLng = null;

            function tampilError(pesan) {
                errBox.textContent = pesan;
                errBox.hidden = false;
            }
            function hapusError() { errBox.hidden = true; }

            // Ambil data toko
            fetch('api_toko.php')
                .then(r => r.json())
                .then(res => { if (res.status === 'success') semuaToko = res.data; })
                .catch(e => { console.error(e); tampilError('Gagal memuat data UMKM. Muat ulang halaman.'); });

            // Tombol lokasi
            document.getElementById('btn-ambil-lokasi').addEventListener('click', function () {
                if (!navigator.geolocation) {
                    document.getElementById('status-lokasi').innerText = '⚠️ Geolokasi tidak didukung browser Anda.';
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        userLat = pos.coords.latitude;
                        userLng = pos.coords.longitude;
                        document.getElementById('status-lokasi').innerHTML = `📍 Lokasi aktif (${userLat.toFixed(4)}, ${userLng.toFixed(4)}) — jarak dihitung otomatis.`;
                        const b = document.getElementById('btn-ambil-lokasi');
                        b.innerText = '✅ Lokasi Aktif';
                        b.disabled = true;
                        hapusError();
                    },
                    function () {
                        document.getElementById('status-lokasi').innerText = '⚠️ Tidak dapat mengakses lokasi. Izin lokasi tidak diberikan.';
                    }
                );
            });

            document.getElementById('btn-cari-budget').addEventListener('click', cariBudget);
            document.getElementById('budget-input').addEventListener('keydown', e => { if (e.key === 'Enter') cariBudget(); });

            function cariBudget() {
                hapusError();
                const budgetMaks = parseInt(document.getElementById('budget-input').value) || 0;
                if (budgetMaks < 1000) { tampilError('Masukkan anggaran yang valid (minimal Rp1.000).'); return; }

                const jarakOpt = document.getElementById('jarak-input').value;
                const kategoriVal = document.getElementById('kategori-budget').value;
                const jarakMaks = jarakOpt ? parseFloat(jarakOpt) : 0;

                if (jarakMaks > 0 && userLat === null) {
                    tampilError('Aktifkan lokasi terlebih dahulu agar filter jarak dapat digunakan.');
                    return;
                }

                const opts = {};
                if (jarakMaks > 0 && userLat !== null) {
                    opts.jarakMaks = jarakMaks;
                    opts.userLat = userLat;
                    opts.userLng = userLng;
                }

                let dataFilter = semuaToko;
                if (kategoriVal) dataFilter = dataFilter.filter(t => t.kategori === kategoriVal);

                const hasil = window.kulinerUtils.filterMenuByBudget(dataFilter, budgetMaks, opts);

                document.getElementById('budget-panduan').style.display = 'none';
                document.getElementById('budget-hasil-container').style.display = 'block';
                document.getElementById('budget-heading').innerText = `Menu ${rupiah(budgetMaks)} ke Bawah`;
                document.getElementById('budget-subtext').innerText = `Ditemukan ${hasil.length} UMKM dengan menu terjangkau${kategoriVal ? ' — ' + kategoriVal : ''}`;
                document.getElementById('budget-counter').innerText = hasil.length + ' UMKM ditemukan';
                map.invalidateSize();

                const grid = document.getElementById('budget-grid');
                const empty = document.getElementById('budget-empty');
                grid.innerHTML = '';
                markersLayer.clearLayers();

                if (hasil.length === 0) {
                    document.getElementById('empty-budget-val').innerText = Number(budgetMaks).toLocaleString('id-ID');
                    empty.style.display = 'block';
                    return;
                }
                empty.style.display = 'none';

                hasil.forEach(toko => {
                    const iconSymbol = kategoriIcons[toko.kategori] || '🏛️';
                    const markerIcon = L.divIcon({
                        className: 'custom-gonjong-marker',
                        html: `<div class="marker-pin"><span class="marker-roof">▲</span><span class="marker-symbol">${iconSymbol}</span></div>`,
                        iconSize: [36, 42], iconAnchor: [18, 42], popupAnchor: [0, -38]
                    });
                    const marker = L.marker([toko.latitude, toko.longitude], { icon: markerIcon });

                    let menuHtml = '<div class="popup-menu-list"><strong>🍽️ Menu Sesuai Budget:</strong><ul>';
                    toko.menu_filtered.forEach(m => {
                        menuHtml += `<li><span class="menu-name">${esc(m.nama_menu)}</span><span class="menu-price">${esc(m.harga_fmt)}</span></li>`;
                    });
                    menuHtml += '</ul></div>';

                    const waText = encodeURIComponent(`Halo ${toko.nama_toko}, saya melihat menu Anda di Peta Rasa Padang (Budget Mode) dan ingin memesan.`);
                    const waUrl = toko.no_wa ? `https://wa.me/${toko.no_wa}?text=${waText}` : '#';
                    const termurah = toko.harga_fmt_terendah || rupiah(toko.hargaTerendah);

                    marker.bindPopup(`
                        <div class="minang-popup">
                            <div class="popup-header">
                                <span class="popup-badge">${esc(toko.kategori)}</span>
                                <h3 class="popup-title">${esc(toko.nama_toko)}</h3>
                            </div>
                            <p class="popup-address">📍 ${esc(toko.alamat)}</p>
                            ${menuHtml}
                            <p style="font-size:0.82rem;color:#334155;"><strong>💰 Termurah:</strong> ${esc(termurah)} ${toko.jarakKm !== null ? '| 📍 ' + toko.jarakKm.toFixed(1) + ' km' : ''}</p>
                            <div class="popup-buttons">
                                <a href="${toko.maps_url}" target="_blank" rel="noopener" class="btn-popup-maps">🗺️ Google Maps</a>
                                ${waUrl !== '#' ? `<a href="${waUrl}" target="_blank" rel="noopener" class="btn-popup-wa">💬 Pesan WA</a>` : '<span class="btn-popup-wa" style="opacity:0.5;background:#999;">💬 WA tidak tersedia</span>'}
                            </div>
                        </div>
                    `, { maxWidth: 320 });
                    markersLayer.addLayer(marker);

                    // ----- Kartu hasil -----
                    const menuUtama = toko.menu_filtered[0];
                    const badgeHtml = (toko.badges && toko.badges.length)
                        ? `<div class="bgt-badges">${toko.badges.map(b => `<span class="bgt-badge">${esc(b)}</span>`).join('')}</div>`
                        : '';

                    let menuItems = toko.menu_filtered.slice(0, 3)
                        .map(m => `<li><span>${esc(m.nama_menu)}</span><span class="bgt-price">${esc(m.harga_fmt)}</span></li>`).join('');
                    if (toko.menu_filtered.length > 3) {
                        menuItems += `<li class="bgt-more">+${toko.menu_filtered.length - 3} menu lainnya</li>`;
                    }

                    const jarakChip = toko.jarakKm !== null ? `<span class="bgt-dist">📍 ${toko.jarakKm.toFixed(1)} km</span>` : '';

                    const aksiHtml = toko.no_wa
                        ? `<div class="bgt-order">
                               <span class="bgt-order-name" title="${esc(menuUtama.nama_menu)}">Pesan: <strong>${esc(menuUtama.nama_menu)}</strong></span>
                               <label class="bgt-qty" for="budget-qty-${toko.id_toko}">Jumlah
                                   <input id="budget-qty-${toko.id_toko}" class="budget-qty-input" type="number" min="1" max="99" step="1" value="1" aria-label="Jumlah ${esc(menuUtama.nama_menu)}">
                               </label>
                           </div>
                           <div class="bgt-actions">
                               <a href="detail.php?id=${toko.id_toko}" class="bgt-btn bgt-btn-detail">📄 Detail</a>
                               <button type="button" class="bgt-btn bgt-btn-wa btn-wa-pesan" data-id-toko="${toko.id_toko}" data-nama="${esc(toko.nama_toko)}" data-nohp="${esc(toko.no_wa)}" data-menu="${esc(menuUtama.nama_menu)}" data-harga="${menuUtama.harga}">💬 Pesan via WhatsApp</button>
                           </div>`
                        : `<div class="bgt-actions">
                               <a href="detail.php?id=${toko.id_toko}" class="bgt-btn bgt-btn-detail">📄 Detail</a>
                               <span class="bgt-btn bgt-btn-off">💬 WA tidak tersedia</span>
                           </div>`;

                    const card = document.createElement('article');
                    card.className = 'bgt-card budget-card';
                    card.innerHTML = `
                        <div class="bgt-media">
                            <img src="${toko.foto}" alt="${esc(toko.nama_toko)}" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=500&q=80'">
                            <span class="bgt-chip">${esc(toko.kategori)}</span>
                            ${jarakChip}
                        </div>
                        <div class="bgt-body">
                            <div>
                                <h3 class="bgt-title">${esc(toko.nama_toko)}</h3>
                                ${badgeHtml}
                            </div>
                            <p class="bgt-addr">📍 ${esc(toko.alamat)}</p>
                            <div class="bgt-menu">
                                <p class="bgt-menu-title">✅ Menu sesuai budget</p>
                                <ul>${menuItems}</ul>
                            </div>
                            <div class="bgt-from">Mulai <strong>${rupiah(toko.hargaTerendah)}</strong></div>
                            ${aksiHtml}
                        </div>
                    `;
                    grid.appendChild(card);
                });

                // Event tombol WhatsApp
                document.querySelectorAll('.btn-wa-pesan').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const nama = this.dataset.nama;
                        const nohp = this.dataset.nohp;
                        const menu = this.dataset.menu;
                        const harga = parseInt(this.dataset.harga);
                        const jumlah = Math.max(1, Math.min(99, parseInt(this.closest('.budget-card').querySelector('.budget-qty-input').value) || 1));
                        const pesan = window.kulinerUtils.formatWaPesan(nama, menu, jumlah, harga);
                        window.kulinerUtils.bukaWaLink(nohp, pesan);
                        window.kulinerUtils.catatKlikWa(this.dataset.idToko);
                    });
                });

                // Zoom ke marker
                map.fitBounds(hasil.map(t => [t.latitude, t.longitude]), { padding: [50, 50] });
            }
        });
    </script>
</body>
</html>