# Peta Rasa Padang — Panduan Menjalankan Aplikasi

Project website ini dibuat khusus untuk lomba **IT FEST 2026 – Web Programming** (UIN Mahmud Yunus Batusangkar).
Tema: **Digitalisasi UMKM: Membangun Etalase Digital yang Menarik, Interaktif, dan Fungsional**.

---

## Lokasi Proyek
Folder aplikasi pada workspace ini berada di `C:\xampp1\htdocs\Lomba-Web-UMKM\kuliner-padang`.

---

## 🚀 Cara Menjalankan di XAMPP

1. **Aktifkan Server XAMPP:**
   - Buka **XAMPP Control Panel**.
   - Klik **Start** pada modul **Apache** dan **MySQL**.

2. **Impor Database:**
   - Buka browser dan kunjungi: `http://localhost/phpmyadmin`
   - Buat database baru bernama `db_kuliner_padang` (atau langsung klik tab **Import**).
   - Pilih file: `database/db_kuliner.sql`
   - Klik **Go / Impor**.
   - Jika memakai database dari versi sebelumnya, jalankan juga `database/migrate_umkm_features.sql` melalui tab **Import** agar peran pemilik, relasi pemilik toko, kolom statistik/kurasi, dan tabel makanan khas tersedia. Migrasi ini menambah struktur tanpa menghapus data toko dan memulihkan akun pemilik yang sebelumnya tersimpan dengan peran kosong.

3. **Akses Website:**
   - **Halaman Pengunjung (Peta Kuliner):**
   `http://localhost/Lomba-Web-UMKM/kuliner-padang/`
   - **Halaman Login Admin / Pengguna:**
   `http://localhost/Lomba-Web-UMKM/kuliner-padang/login.php`
   - **Panel Admin:**
   `http://localhost/Lomba-Web-UMKM/kuliner-padang/admin/dashboard.php`

---

## 🔑 Akun Uji Coba (Demo)
- **Admin:**
  - Username: `admin`
  - Password: `admin123`
  - Hak akses: CRUD data toko (dengan peta interaktif set koordinat) dan CRUD menu makanan.
- **Pengguna Biasa:**
  - Username: `user`
  - Password: `user123`
- Akun pemilik dapat dibuat melalui **Daftar** dengan memilih peran **Pemilik Toko**. Pemilik dapat mendaftarkan sendiri profil toko dan lokasinya dari dashboard, kemudian langsung mengelola profil serta menu.

---

## ✨ Fitur Unggulan Sesuai SPEC.md
1. **Peta Interaktif Leaflet 1.9.4 & OpenStreetMap:**
   - Titik pusat Kota Padang (`-0.9471, 100.4172`).
   - Pin gonjong Minang dinamis per kategori kuliner.
   - Popup info toko: alamat, deskripsi, daftar menu + harga format Rupiah, tombol Google Maps, dan tombol WhatsApp.
   - Fitur **Fly-To**: Mengklik kartu toko di etalase bawah otomatis mengarahkan peta ke koordinat toko dan membuka popup.
2. **Filter & Pencarian Real-Time:**
   - Filter cepat berdasarkan kategori: *Rumah Makan, Sate, Kopi, Oleh-oleh, Camilan*.
   - Kotak pencarian responsif untuk mencari nama toko, menu, maupun alamat.
3. **Panel Admin dan Pemilik Terproteksi Session:**
   - Manajemen Toko (Tambah, Edit, Hapus dengan konfirmasi JS).
   - Form tambah & edit dilengkapi **peta mini** (klik peta otomatis mengisi latitude dan longitude).
   - Pemilik dapat mendaftarkan profil toko dan lokasinya sendiri; toko otomatis terhubung hanya ke akun pendaftarnya.
   - Pemilik dapat mengubah nama, kategori, deskripsi, alamat, foto, nomor WhatsApp, jam buka, dan posisi peta hanya pada tokonya sendiri.
   - Pemilik dapat menambah, mengubah, dan menghapus menu hanya pada tokonya sendiri.
   - Penghapusan menu menggunakan POST dengan token CSRF.
   - Relasi database aman dengan `ON DELETE CASCADE`.
4. **Keamanan Kode:**
   - Seluruh query menggunakan **Prepared Statements** (`mysqli_prepare`).
   - Password terenkripsi dengan **`password_hash()`** dan diverifikasi dengan **`password_verify()`**.
   - Proteksi output HTML dengan **`htmlspecialchars()`**.
   - API data JSON terstruktur via `api_toko.php`.
5. **Tema Visual Minangkabau:**
   - Nuansa warna Merah Marun (`#8B1A1A`), Emas (`#D4A017`), Hitam (`#1A1A1A`), dan Krem (`#FFF6E5`).
   - Ornamen pembatas songket Minang, siluet rumah gadang/gonjong.
   - Navbar interaktif dengan garis bawah emas animasi hover dan efek blur saat di-scroll.
   - Desain responsif di layar Mobile, Tablet, dan Desktop.

## Fitur Etalase UMKM
- **Kuliner Sesuai Budget:** filter harga menu, kategori, dan jarak dari lokasi pengguna; hasil ditandai pada peta dan diurutkan dari kecocokan harga serta jarak.
- **Rute Kuliner Pintar:** menyusun 2–4 pemberhentian sesuai pilihan dan plafon belanja, memprioritaskan Hidden Gem, menggambar garis rute, serta membuka navigasi eksternal Google Maps.
- **Pesan via WhatsApp:** jumlah pesanan dapat diatur dari kartu menu dan detail; website hanya membuka WhatsApp, tanpa memproses pembayaran.
- **Kuliner Khas Daerah:** cerita makanan tradisional dan tautan ke UMKM yang terkait.
- Utilitas filter budget, penyusun rute, format pesan, dan pencatatan klik WA berada di `assets/js/kuliner-utils.js`.

## Catatan Data dan Batas Demo
Data toko/menu pada `database/db_kuliner.sql`, termasuk nomor WhatsApp, adalah data ilustrasi untuk demo dan bukan kontak usaha nyata. Ganti dengan data yang telah diverifikasi dan mendapat izin sebelum dipublikasikan. Pemilik dapat langsung mendaftarkan profil toko setelah login tanpa penautan manual oleh admin.

## Alur Demo Singkat
1. Buka **Kuliner Sesuai Budget**, aktifkan lokasi bila ingin menguji filter jarak, lalu cari menu dengan anggaran Rp15.000.
2. Buka **Rute Pintar**, pilih anggaran Rp50.000 dan 2–4 pemberhentian; tunjukkan ringkasan serta garis rute.
3. Ubah jumlah pada salah satu destinasi dan tunjukkan estimasi belanja/sisa anggaran ikut berubah.
4. Buka detail UMKM, pilih jumlah menu, lalu tunjukkan pesan yang terisi di WhatsApp tanpa menyelesaikan transaksi.
5. Buka **Khas Daerah** untuk melihat cerita makanan dan hubungan ke UMKM; jelaskan bahwa data yang tampil masih ilustrasi.
