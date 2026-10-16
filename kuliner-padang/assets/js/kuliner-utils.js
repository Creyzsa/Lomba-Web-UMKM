/**
 * kuliner-utils.js — Utilitas Peta Rasa Padang
 * 
 * Modul fungsi murni untuk:
 * - Filter menu berdasarkan budget
 * - Penyusun rute wisata kuliner (nearest neighbor)
 * - Format pesan WhatsApp
 * 
 * Semua fungsi stateless agar mudah diuji.
 */

// -----------------------------------------------------------------------
// 1. FILTER BUDGET — Cari menu yang harganya <= budget pengguna
// -----------------------------------------------------------------------

/**
 * filterMenuByBudget — Filter daftar toko untuk menampilkan menu sesuai budget.
 * @param {Array} daftarToko — Array toko dari API (masing-masing punya .menu[])
 * @param {number} budgetMaks — Anggaran maksimal dalam Rupiah
 * @param {object} opts — { jarakMaks: number (km), userLat: number, userLng: number }
 * @returns {Array} — Array toko dengan field .menu_filtered[], .totalHarga, .jarakKm
 */
function filterMenuByBudget(daftarToko, budgetMaks, opts) {
    if (!Array.isArray(daftarToko) || !budgetMaks || budgetMaks <= 0) {
        return [];
    }

    const jarakMaks = (opts && opts.jarakMaks > 0) ? opts.jarakMaks : Infinity;
    const userLat = (opts && opts.userLat != null) ? opts.userLat : null;
    const userLng = (opts && opts.userLng != null) ? opts.userLng : null;

    const hasil = [];

    daftarToko.forEach(toko => {
        // Filter menu yang harganya <= budget
        const menuSesuai = (toko.menu || []).filter(m => m.harga > 0 && m.harga <= budgetMaks);
        if (menuSesuai.length === 0) return;

        // Hitung jarak dari user (jika koordinat tersedia)
        let jarakKm = null;
        if (userLat !== null && userLng !== null && toko.latitude != null && toko.longitude != null) {
            jarakKm = hitungJarak(userLat, userLng, toko.latitude, toko.longitude);
            if (jarakKm > jarakMaks) return;
        }

        // Hitung total termurah dari 1 menu
        const hargaTerendah = Math.min(...menuSesuai.map(m => m.harga));
        const totalHarga = hargaTerendah;

        // Skor kecocokan: makin murah & makin dekat => makin tinggi
        let skor = 100 - (hargaTerendah / budgetMaks) * 50;
        if (jarakKm !== null) {
            const pembagiJarak = Number.isFinite(jarakMaks) ? jarakMaks : 10;
            skor -= Math.min(jarakKm / pembagiJarak, 1) * 30;
        }
        // Bonus untuk Hidden Gem / Khas Daerah
        if (toko.badges && toko.badges.includes('Hidden Gem')) skor += 10;
        if (toko.badges && toko.badges.includes('Khas Daerah')) skor += 5;

        hasil.push({
            ...toko,
            menu_filtered: menuSesuai,
            jarakKm: jarakKm,
            hargaTerendah: hargaTerendah,
            totalHarga: hargaTerendah,
            skor: Math.round(skor * 10) / 10
        });
    });

    // Urutkan: skor tertinggi dulu
    hasil.sort((a, b) => b.skor - a.skor);

    return hasil;
}

// -----------------------------------------------------------------------
// 2. SMART CULINARY ROUTE — Susun rute 2-4 UMKM dalam anggaran
// -----------------------------------------------------------------------

/**
 * planCulinaryRoute — Susun rute wisata kuliner berdasarkan budget dan preferensi.
 * Menggunakan algoritma nearest neighbor sederhana.
 * 
 * @param {Array} daftarToko — Semua toko dari API
 * @param {number} totalBudget — Total anggaran (Rp)
 * @param {object} preferensi — { kategori: [], jumlahBerhenti: 2-4 }
 * @param {object} origin — { lat: number, lng: number } (opsional)
 * @returns {Array} — Array rute terurut [ { toko, menuDipilih, subtotal }, ... ]
 */
function planCulinaryRoute(daftarToko, totalBudget, preferensi, origin) {
    if (!Array.isArray(daftarToko) || !totalBudget || totalBudget <= 0) {
        return [];
    }

    const kategoriDipilih = (preferensi && Array.isArray(preferensi.kategori) && preferensi.kategori.length > 0)
        ? preferensi.kategori
        : ['Rumah Makan', 'Sate', 'Kopi', 'Camilan', 'Oleh-oleh'];
    const jumlahBerhenti = Math.max(2, Math.min(4, Number(preferensi && preferensi.jumlahBerhenti) || 3));

    // Filter toko: pastikan ada menu yang terjangkau
    const tokoValid = daftarToko.filter(toko => {
        if (kategoriDipilih.length > 0 && !kategoriDipilih.includes(toko.kategori)) return false;
        const menuTerjangkau = (toko.menu || []).filter(m => m.harga > 0 && m.harga <= totalBudget);
        return menuTerjangkau.length > 0;
    });

    if (tokoValid.length < jumlahBerhenti) return [];

    let ruteTerbaik = null;
    let skorTerbaik = -Infinity;

    function cobaKombinasi(mulai, kombinasi, hargaTotal) {
        if (kombinasi.length === jumlahBerhenti) {
            const ruteKandidat = kombinasi.map(item => ({ ...item }));
            urutkanRuteNearestNeighbor(ruteKandidat, origin);

            let jarakKandidat = 0;
            let posisi = origin && origin.lat != null && origin.lng != null
                ? { lat: Number(origin.lat), lng: Number(origin.lng) }
                : { lat: Number(ruteKandidat[0].toko.latitude), lng: Number(ruteKandidat[0].toko.longitude) };
            const mulaiDari = origin && origin.lat != null && origin.lng != null ? 0 : 1;

            for (let i = mulaiDari; i < ruteKandidat.length; i++) {
                const toko = ruteKandidat[i].toko;
                const lat = Number(toko.latitude);
                const lng = Number(toko.longitude);
                if (Number.isFinite(posisi.lat) && Number.isFinite(posisi.lng) && Number.isFinite(lat) && Number.isFinite(lng)) {
                    jarakKandidat += hitungJarak(posisi.lat, posisi.lng, lat, lng);
                }
                posisi = { lat, lng };
            }
            const jumlahHiddenGem = ruteKandidat.filter(item => item.toko.badges && item.toko.badges.includes('Hidden Gem')).length;
            const skor = (hargaTotal / totalBudget) * 100 + jumlahHiddenGem * 3 - jarakKandidat * 1.5;
            if (skor > skorTerbaik) {
                skorTerbaik = skor;
                ruteTerbaik = ruteKandidat;
            }
            return;
        }
        if (kombinasi.length >= jumlahBerhenti) return;

        for (let i = mulai; i < tokoValid.length; i++) {
            const toko = tokoValid[i];
            const menuMuated = (toko.menu || [])
                .filter(m => m.harga > 0 && (hargaTotal + m.harga <= totalBudget))
                .sort((a, b) => b.harga - a.harga);
            if (menuMuated.length === 0) continue;

            menuMuated.forEach(menuPilih => {
                cobaKombinasi(i + 1, [...kombinasi, {
                    toko: toko,
                    menuDipilih: menuPilih,
                    subtotal: menuPilih.harga
                }], hargaTotal + menuPilih.harga);
            });
        }
    }

    cobaKombinasi(0, [], 0);
    if (!ruteTerbaik) return [];

    // Hitung total dan jarak
    let totalBiaya = 0;
    let totalJarak = 0;
    let urutanJarak = [];

    ruteTerbaik.forEach((item, idx) => {
        totalBiaya += item.menuDipilih.harga;
        if (idx > 0) {
            const prev = ruteTerbaik[idx - 1].toko;
            const d = hitungJarak(prev.latitude, prev.longitude, item.toko.latitude, item.toko.longitude);
            totalJarak += d;
            urutanJarak.push(d);
        } else if (origin && origin.lat != null) {
            const d = hitungJarak(origin.lat, origin.lng, item.toko.latitude, item.toko.longitude);
            totalJarak += d;
            urutanJarak.push(d);
        }
    });

    return {
        rute: ruteTerbaik,
        totalAnggaran: totalBudget,
        totalBiaya: totalBiaya,
        sisaAnggaran: totalBudget - totalBiaya,
        totalJarak: Math.round(totalJarak * 10) / 10,
        jumlahBerhenti: ruteTerbaik.length,
        jarakAntar: urutanJarak
    };
}

/**
 * urutkanRuteNearestNeighbor — Urutkan array rute agar jarak antar toko minimal.
 * Mulai dari titik origin, pilih toko terdekat, lalu lanjut ke toko terdekat berikutnya.
 */
function urutkanRuteNearestNeighbor(ruteArr, origin) {
    if (ruteArr.length <= 1) return;

    const belumDikunjungi = ruteArr.map((item, idx) => ({ idx, ...item }));
    const hasilUrut = [];
    let posisiSekarang = origin && origin.lat != null && origin.lng != null
        ? { lat: Number(origin.lat), lng: Number(origin.lng) }
        : { lat: Number(ruteArr[0].toko.latitude), lng: Number(ruteArr[0].toko.longitude) };

    while (belumDikunjungi.length > 0) {
        let terdekat = 0;
        let jarakTerdekat = Infinity;

        belumDikunjungi.forEach((item, i) => {
            const d = hitungJarak(posisiSekarang.lat, posisiSekarang.lng, Number(item.toko.latitude), Number(item.toko.longitude));
            if (d < jarakTerdekat) {
                jarakTerdekat = d;
                terdekat = i;
            }
        });

        const dipilih = belumDikunjungi.splice(terdekat, 1)[0];
        hasilUrut.push({
            toko: dipilih.toko,
            menuDipilih: dipilih.menuDipilih,
            subtotal: dipilih.subtotal
        });
        posisiSekarang = { lat: dipilih.toko.latitude, lng: dipilih.toko.longitude };
    }

    // Salin hasil urutan kembali ke array asli
    ruteArr.length = 0;
    hasilUrut.forEach(item => ruteArr.push(item));
}

// -----------------------------------------------------------------------
// 3. FORMAT PESAN WHATSAPP
// -----------------------------------------------------------------------

/**
 * formatWaPesan — Buat teks pesan WhatsApp dengan format yang rapi.
 * @param {string} namaToko — Nama toko/UMKM
 * @param {string} namaMenu — Nama menu
 * @param {number} jumlah — Quantity
 * @param {number} hargaSatuan — Harga per item
 * @param {number} [totalHarga] — Total (hitung otomatis jika tidak diisi)
 * @returns {string} — String pesan yang sudah di-encode URI
 */
function formatWaPesan(namaToko, namaMenu, jumlah, hargaSatuan, totalHarga) {
    const jumlahValid = Math.floor(Number(jumlah));
    const hargaValid = Number(hargaSatuan);
    if (!namaToko || !namaMenu || !Number.isFinite(jumlahValid) || jumlahValid < 1 || !Number.isFinite(hargaValid) || hargaValid <= 0) return '';
    const total = Number(totalHarga) || (jumlahValid * hargaValid);
    const fmtRupiah = (nom) => 'Rp' + Number(nom).toLocaleString('id-ID');

    const teks = [
        `Halo *${namaToko}*,`,
        '',
        `Saya ingin memesan *${namaMenu}* melalui Peta Rasa Padang.`,
        '',
        `📋 Detail Pesanan:`,
        `   Menu   : ${namaMenu}`,
        `   Jumlah : ${jumlahValid}`,
        `   Harga  : ${fmtRupiah(hargaValid)} × ${jumlahValid}`,
        `   *Total : ${fmtRupiah(total)}*`,
        '',
        'Mohon info ketersediaan dan estimasi waktu pesanan. Terima kasih!'
    ].join('\n');

    return encodeURIComponent(teks);
}

/**
 * bukaWaLink — Generate URL WhatsApp dan buka di tab baru.
 * @param {string} noWa — Nomor WhatsApp (dengan kode negara, tanpa +)
 * @param {string} pesan — Pesan yang sudah di-encode
 */
function bukaWaLink(noWa, pesan) {
    const nomorValid = String(noWa || '').replace(/\D/g, '');
    if (!nomorValid || !pesan) {
        alert('Nomor WhatsApp untuk toko ini belum tersedia.');
        return;
    }
    const url = `https://wa.me/${nomorValid}?text=${pesan}`;
    window.open(url, '_blank', 'noopener,noreferrer');
}

function catatKlikWa(idToko) {
    if (!idToko) return Promise.resolve();
    return fetch('track-wa.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ id_toko: String(idToko) })
    }).catch(() => {});
}

/**
 * hitungJarak — Hitung jarak antara dua titik koordinat (Haversine)
 * @returns {number} Jarak dalam kilometer
 */
function hitungJarak(lat1, lng1, lat2, lng2) {
    const R = 6371; // Radius bumi (km)
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) ** 2 +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLng / 2) ** 2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// -----------------------------------------------------------------------
// EKSPOR (legacy global untuk vanilla JS)
// -----------------------------------------------------------------------
// Ekspos ke window agar bisa dipanggil dari halaman lain
window.kulinerUtils = {
    filterMenuByBudget: filterMenuByBudget,
    planCulinaryRoute: planCulinaryRoute,
    formatWaPesan: formatWaPesan,
    bukaWaLink: bukaWaLink,
    catatKlikWa: catatKlikWa,
    hitungJarak: hitungJarak
};