# Peta Rasa Padang — Panduan Menjalankan Aplikasi

Project website ini dibuat khusus untuk lomba **IT FEST 2026 – Web Programming** (UIN Mahmud Yunus Batusangkar).
Tema: **Digitalisasi UMKM: Membangun Etalase Digital yang Menarik, Interaktif, dan Fungsional**.

---

## 📁 Lokasi File Project
Folder project terletak di:
`C:\Lomba web UMKM\kuliner-padang`

---

## 🚀 Cara Menjalankan di XAMPP

1. **Pindahkan/Copy Folder:**
   - Copy folder `kuliner-padang` ke dalam direktori `htdocs` XAMPP Anda:
     Contoh: `C:\xampp\htdocs\kuliner-padang`

2. **Aktifkan Server XAMPP:**
   - Buka **XAMPP Control Panel**.
   - Klik **Start** pada modul **Apache** dan **MySQL**.

3. **Impor Database:**
   - Buka browser dan kunjungi: `http://localhost/phpmyadmin`
   - Buat database baru bernama `db_kuliner_padang` (atau langsung klik tab **Import**).
   - Pilih file: `database/db_kuliner.sql`
   - Klik **Go / Impor**.

4. **Akses Website:**
   - **Halaman Pengunjung (Peta Kuliner):**
     `http://localhost/kuliner-padang/`
   - **Halaman Login Admin / Pengguna:**
     `http://localhost/kuliner-padang/login.php`
   - **Panel Admin:**
     `http://localhost/kuliner-padang/admin/dashboard.php`

---

## 🔑 Akun Uji Coba (Demo)
- **Admin:**
  - Username: `admin`
  - Password: `admin123`
  - Hak akses: CRUD data toko (dengan peta interaktif set koordinat) dan CRUD menu makanan.
- **Pengguna Biasa:**
  - Username: `user`
  - Password: `user123`

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
3. **Panel Admin Terproteksi Session:**
   - Manajemen Toko (Tambah, Edit, Hapus dengan konfirmasi JS).
   - Form tambah & edit dilengkapi **peta mini** (klik peta otomatis mengisi latitude dan longitude).
   - Manajemen Menu per Toko (tambah, edit, dan hapus menu makanan).
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
