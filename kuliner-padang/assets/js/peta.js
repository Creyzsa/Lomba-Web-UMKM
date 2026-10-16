/**
 * assets/js/peta.js — Peta Rasa Padang Leaflet Controller
 * Fitur:
 * - Titik tengah Padang: -0.9471, 100.4172, zoom 13
 * - Ambil data dinamis dari api_toko.php
 * - Custom Icon Pin bergaya Gonjong / Kategori Minang
 * - Popup interaktif: Nama toko, deskripsi, daftar menu + harga format Rp12.000, tombol Google Maps & WA
 * - Grid kartu toko dengan tombol Fly-to lokasi & buka popup
 * - Fitur Filter Kategori & Pencarian real-time
 * - Efek navbar saat di-scroll
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inisialisasi Peta Padang
    const centerPadang = [-0.9471, 100.4172];
    const map = L.map('map', {
        center: centerPadang,
        zoom: 13,
        scrollWheelZoom: true
    });

    // Tile Layer OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Variabel Penampung Data
    let semuaToko = [];
    let markersMap = {}; // mapping id_toko -> L.marker
    let markersLayer = L.layerGroup().addTo(map);

    // Kategori & Emoji Icon
    const kategoriIcons = {
        'Rumah Makan': '🍛',
        'Sate': '🍢',
        'Kopi': '☕',
        'Oleh-oleh': '🎁',
        'Camilan': '🥮'
    };

    // Fungsi membuat custom icon pin gonjong Minang
    function createGonjongIcon(kategori) {
        const iconSymbol = kategoriIcons[kategori] || '🏛️';
        return L.divIcon({
            className: 'custom-gonjong-marker',
            html: `
                <div class="marker-pin">
                    <span class="marker-roof">▲</span>
                    <span class="marker-symbol">${iconSymbol}</span>
                </div>
            `,
            iconSize: [36, 42],
            iconAnchor: [18, 42],
            popupAnchor: [0, -38]
        });
    }

    // 2. Fetch Data dari api_toko.php
    fetch('api_toko.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal memuat data API toko (Status: ' + response.status + ')');
            }
            return response.json();
        })
        .then(res => {
            if (res.status === 'success' && Array.isArray(res.data)) {
                semuaToko = res.data;
                renderSemua(semuaToko);
            } else {
                console.error('Format data tidak sesuai:', res);
            }
        })
        .catch(err => {
            console.error('Error fetching data toko:', err);
            const grid = document.getElementById('toko-grid-container');
            if (grid) {
                grid.innerHTML = `
                    <div class="error-msg-box">
                        <p>⚠️ Gagal memuat data toko dari database.</p>
                        <small>Pastikan web server Apache & MySQL XAMPP aktif serta database <code>db_kuliner_padang</code> telah diimpor.</small>
                    </div>
                `;
            }
        });

    // 3. Render Markers di Peta dan Kartu di Grid
    function renderSemua(daftarToko) {
        // Bersihkan marker lama
        markersLayer.clearLayers();
        markersMap = {};

        const gridContainer = document.getElementById('toko-grid-container');
        gridContainer.innerHTML = '';

        if (daftarToko.length === 0) {
            gridContainer.innerHTML = `
                <div class="empty-state">
                    <p>Tidak ada toko yang sesuai dengan pencarian atau filter kategori yang dipilih.</p>
                </div>
            `;
            document.getElementById('counter-toko-tampil').innerText = '0 Toko Ditemukan';
            return;
        }

        document.getElementById('counter-toko-tampil').innerText = `${daftarToko.length} Toko Ditampilkan`;

        daftarToko.forEach(toko => {
            // A. Buat Marker & Popup Leaflet
            const markerIcon = createGonjongIcon(toko.kategori);
            const marker = L.marker([toko.latitude, toko.longitude], { icon: markerIcon });

            // Susun Konten Popup
            let menuHtml = '';
            if (toko.menu && toko.menu.length > 0) {
                menuHtml = '<div class="popup-menu-list"><strong>Daftar Menu & Harga:</strong><ul>';
                toko.menu.forEach(m => {
                    menuHtml += `
                        <li>
                            <span class="menu-name">${escapeHtml(m.nama_menu)}</span>
                            <span class="menu-price">${escapeHtml(m.harga_fmt)}</span>
                        </li>
                    `;
                });
                menuHtml += '</ul></div>';
            } else {
                menuHtml = '<p class="text-muted"><small>Belum ada daftar menu terdaftar.</small></p>';
            }

            // WhatsApp link (nomor fiktif contoh/customer care)
            const waText = encodeURIComponent(`Halo ${toko.nama_toko}, saya melihat etalase Anda di Peta Rasa Padang dan ingin memesan.`);
            const waUrl = `https://wa.me/6281234567890?text=${waText}`;

            const popupContent = `
                <div class="minang-popup">
                    <div class="popup-header">
                        <span class="popup-badge">${escapeHtml(toko.kategori)}</span>
                        <h3 class="popup-title">${escapeHtml(toko.nama_toko)}</h3>
                    </div>
                    <p class="popup-address">📍 ${escapeHtml(toko.alamat)}</p>
                    <p class="popup-desc">${escapeHtml(toko.deskripsi)}</p>
                    ${menuHtml}
                    <div class="popup-buttons">
                        <a href="${toko.maps_url}" target="_blank" class="btn-popup-maps">🗺️ Buka di Google Maps</a>
                        <a href="${waUrl}" target="_blank" class="btn-popup-wa">💬 Hubungi via WA</a>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent, { maxWidth: 320 });
            markersLayer.addLayer(marker);
            markersMap[toko.id_toko] = marker;

            // B. Buat Kartu Toko di Grid
            const card = document.createElement('div');
            card.className = 'toko-card';
            card.setAttribute('data-id', toko.id_toko);

            // Ringkasan 2 menu utama
            let previewMenuHtml = '';
            if (toko.menu && toko.menu.length > 0) {
                previewMenuHtml = '<div class="card-menu-preview">';
                toko.menu.slice(0, 2).forEach(m => {
                    previewMenuHtml += `
                        <div class="preview-menu-row">
                            <span>${escapeHtml(m.nama_menu)}</span>
                            <span class="price-tag">${escapeHtml(m.harga_fmt)}</span>
                        </div>
                    `;
                });
                previewMenuHtml += '</div>';
            }

            card.innerHTML = `
                <div class="card-img-wrapper">
                    <img src="${escapeHtml(toko.foto)}" alt="${escapeHtml(toko.nama_toko)}" class="card-img" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=500&q=80'">
                    <span class="card-badge">${escapeHtml(toko.kategori)}</span>
                </div>
                <div class="card-body">
                    <h3 class="card-title">${escapeHtml(toko.nama_toko)}</h3>
                    <p class="card-address">📍 ${escapeHtml(toko.alamat)}</p>
                    <p class="card-desc">${escapeHtml(toko.deskripsi)}</p>
                    ${previewMenuHtml}
                    <div class="card-actions">
                        <button class="btn btn-primary btn-sm btn-fly" data-id="${toko.id_toko}">
                            🎯 Sorot di Peta
                        </button>
                        <a href="${toko.maps_url}" target="_blank" class="btn btn-outline-gold btn-sm" title="Google Maps">
                            🗺️ Rute
                        </a>
                    </div>
                </div>
            `;

            // Event Klik Kartu Toko → Fly to Map & Open Popup
            card.addEventListener('click', function (e) {
                // Cegah double trigger jika klik link Google Maps
                if (e.target.tagName.toLowerCase() === 'a') return;
                flyToToko(toko.id_toko, toko.latitude, toko.longitude);
            });

            gridContainer.appendChild(card);
        });
    }

    // 4. Aksi FlyTo Lokasi Toko
    function flyToToko(idToko, lat, lng) {
        // Scroll halus ke area peta
        const petaSection = document.getElementById('peta-section');
        if (petaSection) {
            petaSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Animasi terbang di Leaflet
        map.flyTo([lat, lng], 16, {
            animate: true,
            duration: 1.2
        });

        // Buka popup setelah pergerakan selesai
        setTimeout(() => {
            const marker = markersMap[idToko];
            if (marker) {
                marker.openPopup();
            }
        }, 1200);
    }

    // 5. Filter Kategori & Pencarian
    let currentCategory = 'Semua';
    let searchQuery = '';

    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchInput = document.getElementById('input-search');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.getAttribute('data-category');
            applyFilter();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            searchQuery = this.value.toLowerCase().trim();
            applyFilter();
        });
    }

    function applyFilter() {
        const filtered = semuaToko.filter(toko => {
            const matchCat = (currentCategory === 'Semua') || (toko.kategori === currentCategory);

            let matchSearch = true;
            if (searchQuery !== '') {
                const namaMatch = toko.nama_toko.toLowerCase().includes(searchQuery);
                const alamatMatch = (toko.alamat || '').toLowerCase().includes(searchQuery);
                const descMatch = (toko.deskripsi || '').toLowerCase().includes(searchQuery);
                
                // Cek nama menu makanan
                const menuMatch = toko.menu && toko.menu.some(m => m.nama_menu.toLowerCase().includes(searchQuery));

                matchSearch = namaMatch || alamatMatch || descMatch || menuMatch;
            }

            return matchCat && matchSearch;
        });

        renderSemua(filtered);
    }

    // Fungsi global agar bisa dipanggil dari footer link
    window.filterByCat = function(kat) {
        currentCategory = kat;
        filterButtons.forEach(b => {
            if (b.getAttribute('data-category') === kat) {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });
        applyFilter();
    };

    // 6. Animasi Navbar Scroll
    const navbar = document.getElementById('main-navbar');
    window.addEventListener('scroll', function () {
        if (window.scrollY > 40) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Helper Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
