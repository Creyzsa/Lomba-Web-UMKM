-- ==============================================================
-- DATABASE: db_kuliner_padang
-- Project: Peta Rasa Padang (Lomba IT FEST 2026 - Web Programming)
-- ==============================================================

CREATE DATABASE IF NOT EXISTS db_kuliner_padang;
USE db_kuliner_padang;

-- 1. Tabel users
CREATE TABLE IF NOT EXISTS users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') DEFAULT 'user'
);

-- 2. Tabel toko
CREATE TABLE IF NOT EXISTS toko (
  id_toko INT AUTO_INCREMENT PRIMARY KEY,
  nama_toko VARCHAR(100) NOT NULL,
  alamat VARCHAR(255),
  kategori VARCHAR(50),
  deskripsi TEXT,
  latitude DECIMAL(10,7) NOT NULL,
  longitude DECIMAL(10,7) NOT NULL,
  foto VARCHAR(255),
  no_wa VARCHAR(20) DEFAULT NULL,
  jam_buka VARCHAR(100) DEFAULT NULL,
  is_khas TINYINT(1) DEFAULT 0,
  is_hidden_gem TINYINT(1) DEFAULT 0,
  visit_count INT DEFAULT 0,
  whatsapp_views INT DEFAULT 0
);

-- 3. Tabel menu
CREATE TABLE IF NOT EXISTS menu (
  id_menu INT AUTO_INCREMENT PRIMARY KEY,
  id_toko INT NOT NULL,
  nama_menu VARCHAR(100) NOT NULL,
  harga INT,
  deskripsi VARCHAR(255),
  FOREIGN KEY (id_toko) REFERENCES toko(id_toko) ON DELETE CASCADE
);

-- 4. Tabel makanan_khas (untuk fitur kurasi kuliner khas daerah)
CREATE TABLE IF NOT EXISTS makanan_khas (
  id_makanan INT AUTO_INCREMENT PRIMARY KEY,
  nama_makanan VARCHAR(100) NOT NULL,
  asal_daerah VARCHAR(100) DEFAULT NULL,
  cerita TEXT,
  foto VARCHAR(255) DEFAULT NULL,
  id_toko INT DEFAULT NULL,
  FOREIGN KEY (id_toko) REFERENCES toko(id_toko) ON DELETE SET NULL
);

-- ==============================================================
-- DATA AWAL (SEEDER)
-- ==============================================================

-- Akun admin default:
-- username: admin
-- password: admin123 (hash BCRYPT)
-- Akun user demo:
-- username: user
-- password: user123 (hash BCRYPT)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'admin'),
('user', '$2y$10$tZg44Xf8v7M0w.q8A9zVreR3zU9m7mR4mY7hL2cK9mN8bV5cV6c0.', 'user')
ON DUPLICATE KEY UPDATE id_user=id_user;

-- 10 Toko Fiktif di Area Padang (Pantai Padang/Taplau, Kota Tua/Muaro, Masjid Raya, Khatib Sulaiman, dll)
INSERT INTO toko (id_toko, nama_toko, alamat, kategori, deskripsi, latitude, longitude, foto, no_wa, jam_buka, is_khas, is_hidden_gem) VALUES
(1, 'RM Gonjong Sakato', 'Jl. Samudera No. 18, Pantai Padang (Taplau)', 'Rumah Makan', 'Rumah makan Padang autentik tepi pantai menyajikan rendang daging legendaris, gulai tunjang, dan dendeng batokok renyah dengan pemandangan sunset Pantai Padang.', -0.9324500, 100.3541200, 'toko1.jpg', '6281234567890', '08.00 - 21.00 WIB', 1, 0),
(2, 'Sate Mak Syukur Muaro', 'Jl. Batang Arau No. 42, Kawasan Kota Tua Muaro', 'Sate', 'Spesialis sate Padang kuah kuning gurih berempah khas Pariaman dengan potongan daging sapi empuk dan lidah gurih bertabur bawang goreng renyah.', -0.9632000, 100.3685000, 'toko2.jpg', '6281234567891', '10.00 - 21.00 WIB', 1, 0),
(3, 'Kopi Nan Batigo', 'Jl. KH. Ahmad Dahlan No. 12, Dekat Masjid Raya Sumbar', 'Kopi', 'Kedai kopi modern bernuansa Minang dengan racikan kopi Robusta Solok Selatan, Kopi Kawa Daun tradisional, dan cemilan pisang kapik kuah kinca.', -0.9238000, 100.3615000, 'toko3.jpg', '6281234567892', '07.00 - 23.00 WIB', 1, 0),
(4, 'Oleh-Oleh Sanjai Rangkiang', 'Jl. Khatib Sulaiman No. 88, Ulak Karang', 'Oleh-oleh', 'Sentra oleh-oleh khas Ranah Minang terlengkap mulai dari keripik sanjai balado pedas manis, karak kaliang, drakot, hingga rendang kemasan vakum siap saji.', -0.9082000, 100.3589000, 'toko4.jpg', '6281234567893', '08.00 - 20.00 WIB', 0, 0),
(5, 'Es Cendol Pattimura Ranah', 'Jl. Pattimura No. 25, Padang Barat', 'Camilan', 'Minuman segar legendaris cendol tepung beras dengan gula aren asli, santan kelapa kental, tapai singkong, dan serutan es dingin pelepas dahaga.', -0.9486000, 100.3592000, 'toko5.jpg', '6281234567894', '09.00 - 20.00 WIB', 0, 0),
(6, 'RM Silinduang Bulan', 'Jl. Veteran No. 54, Padang Pasir', 'Rumah Makan', 'Menyajikan sajian khas gulai kepala kakap bumbu rempah kuning kental, ayam pop minyak kelapa murni, dan sambalado ijo petai selera bangsawan.', -0.9415000, 100.3568000, 'toko6.jpg', '6281234567895', '09.00 - 22.00 WIB', 0, 0),
(7, 'Sate Danguang-Danguang Payakumbuh', 'Jl. Pemuda No. 15, Olo Ladang', 'Sate', 'Sate daging dan jeroan sapi bakar kuah rempah kecokelatan manis gurih khas Luak Limopuluah disajikan dengan kerupuk kulit siram kuah.', -0.9508000, 100.3562000, 'toko7.jpg', '6281234567896', '11.00 - 21.00 WIB', 0, 1),
(8, 'Kawa Daun Pondok Raso', 'Jl. Niaga No. 7, Kawasan Pecinan Pondok', 'Kopi', 'Sensasi minum seduhan daun kopi kering di dalam wadah tempurung kelapa asli dengan suasana hangat ruko tua heritage Kota Padang.', -0.9587000, 100.3654000, 'toko8.jpg', '6281234567897', '07.00 - 21.00 WIB', 0, 1),
(9, 'Kerupuk Jangek & Dakak Minang', 'Jl. Belakang Olo No. 3, Padang Barat', 'Oleh-oleh', 'Produsen tangan pertama kerupuk jangek (kulit sapi) renyah gurih dan dakak-dakak ubi renyah kunyit khas ranah Minang terfavorit para wisatawan.', -0.9452000, 100.3621000, 'toko9.jpg', '6281234567898', '07.00 - 19.00 WIB', 0, 1),
(10, 'Sala Lauak & Pisang Goreng Pasia', 'Jl. Pasir Jambak No. 10, Pasie Nan Tigo', 'Camilan', 'Cemilan khas pesisir sala lauak bulat gurih berisi ikan asin dan pisang kapok goreng tepung beras renyah krispi yang nikmat dinikmati sore hari.', -0.8712000, 100.3184000, 'toko10.jpg', '6281234567899', '14.00 - 21.00 WIB', 1, 0)
ON DUPLICATE KEY UPDATE id_toko=id_toko;

-- 3-4 Menu per Toko beserta Harga format INT
INSERT INTO menu (id_toko, nama_menu, harga, deskripsi) VALUES
-- Toko 1: RM Gonjong Sakato (Khas)
(1, 'Rendang Daging Sapi Spesial', 25000, 'Daging sapi pilihan dimasak perlahan dengan kelapa dan rempah hingga hitam meresap.'),
(1, 'Gulai Tunjang Kikil Lembut', 28000, 'Kikil sapi empuk dengan kuah gulai santan kaya bumbu kunyit dan serai.'),
(1, 'Dendeng Batokok Balado Merah', 24000, 'Dendeng sapi pipih lembut digoreng kilat lalu disiram ulekan cabai merah segar.'),
(1, 'Ayam Gulai Kurma', 20000, 'Ayam kampung kuah putih rempah ketumbar dan pala wangi non-pedas.'),

-- Toko 2: Sate Mak Syukur Muaro (Khas)
(2, 'Sate Daging Padang Komplit', 28000, 'Seporsi sate daging sapi panggang dan ketupat disiram kuah kental kuning gurih.'),
(2, 'Sate Lidah Sapi Lembut', 32000, 'Potongan lidah sapi empuk dengan aroma bakaran khas arang batok kelapa.'),
(2, 'Ketupat Kuah Sate Ekstra', 10000, 'Ketupat pulen berbalur kuah sate kuning kental dan taburan bawang goreng.'),
(2, 'Keripik Balado Pelengkap Sate', 5000, 'Kerupuk singkong balado pedas manis sebagai pelengkap renyah saat bersantap.'),

-- Toko 3: Kopi Nan Batigo (Khas)
(3, 'Kopi Solok Selatan V60', 22000, 'Kopi Arabika Solok Selatan seduh manual rasa buah segar asam seimbang.'),
(3, 'Es Kopi Susu Aren Gula Ranah', 18000, 'Espresso lokal dipadu susu segar dan manis legit gula aren murni.'),
(3, 'Seduhan Kawa Daun Klasik', 12000, 'Seduhan daun kopi sangrai di batok kelapa kaya antioksidan dan aroma herbal unik.'),
(3, 'Pisang Kapik Keju Cokelat', 15000, 'Pisang bakar pipih manis gurih topping kelapa parut manis dan sirup kinca.'),

-- Toko 4: Oleh-Oleh Sanjai Rangkiang
(4, 'Keripik Sanjai Balado Merah (500g)', 35000, 'Keripik singkong renyah dengan lapisan cabai merah berkilau pedas gurih.'),
(4, 'Rendang Suwir Kemasan Vakum (250g)', 65000, 'Rendang suwir sapi praktis siap saji tahan lama tanpa bahan pengawet.'),
(4, 'Karak Kaliang Kuning (350g)', 20000, 'Cemilan angka delapan tepung ubi dengan rasa gurih kunyit dan bawang putih.'),
(4, 'Bolu Pandan Minang Premium', 45000, 'Kue bolu lembut aroma pandan asli wangi butter legit.'),

-- Toko 5: Es Cendol Pattimura Ranah
(5, 'Es Cendol Durian Segar', 22000, 'Cendol lembut gula aren ditambah daging durian asli nan wangi legit.'),
(5, 'Es Cendol Klasik Gula Aren', 14000, 'Cendol beras hijau pandan, santan murni, gula aren, dan tapai singkong pulen.'),
(5, 'Bubur Kampiun Komplit', 18000, 'Campuran kolak pisang, bubur sumsum, candil, ketan putih, dan lupis ketan.'),
(5, 'Es Tebak Dingin Manis', 16000, 'Es campur khas Minang berisi tebak, kolang kaling, cincau hitam, dan sirup merah.'),

-- Toko 6: RM Silinduang Bulan
(6, 'Gulai Kepala Ikan Kakap Jumbo', 75000, 'Kepala kakap segar berukuran besar dengan gulai kuning pedas asam segar belimbing wuluh.'),
(6, 'Ayam Pop Sambal Tomat Padang', 22000, 'Ayam kampung gurih digoreng minyak kelapa sebentar, disajikan sambal tomat asam pedas.'),
(6, 'Sambalado Ijo Teri Jengkol', 18000, 'Cabai hijau ulek kasar ditumis wangi bersama ikan teri asin dan potongan jengkol muda.'),
(6, 'Gulai Daun Singkong Tumbuk', 12000, 'Sayur pucuk ubi tumbuk kuah santan kuning sedap beraroma rimbang (tekokak).'),

-- Toko 7: Sate Danguang-Danguang Payakumbuh (Hidden Gem)
(7, 'Sate Danguang Daging & Usus', 26000, 'Sate kuah cokelat rempah gurih lembut dengan taburan kelapa parut sangrai.'),
(7, 'Sate Jantung & Daging Campur', 28000, 'Kombinasi daging sapi empuk dan jantung bakar beraroma khas.'),
(7, 'Kerupuk Jangek Kuah Celup', 8000, 'Kerupuk kulit sapi dicelup kuah sate kental gurih.'),

-- Toko 8: Kawa Daun Pondok Raso (Hidden Gem)
(8, 'Kawa Daun Jahe Hangat Batok', 14000, 'Air rebusan daun kopi berkhasiat dipadu geprekan jahe merah hangat.'),
(8, 'Kawa Daun Susu Hangat', 15000, 'Rebusan daun kopi dipadu susu kental manis legit lembut di tenggorokan.'),
(8, 'Ketan Gurih Sarikaya Padang', 12000, 'Beras ketan pulen disajikan bersama selai sarikaya telur gula merah kukus.'),

-- Toko 9: Kerupuk Jangek & Dakak Minang (Hidden Gem)
(9, 'Kerupuk Jangek Sapi Renyah (200g)', 30000, 'Kerupuk kulit sapi goreng mekar sempurna, gurih renyah tanpa bahan kimia.'),
(9, 'Dakak-Dakak Ubi Kuning (250g)', 18000, 'Potongan dadu singkong renyah gurih berbalur bumbu rempah daun kunyit.'),
(9, 'Keripik Kentang Balado (200g)', 28000, 'Irisan tipis kentang goreng renyah dibalut sambal merah balado kental.'),

-- Toko 10: Sala Lauak & Pisang Goreng Pasia (Khas)
(10, 'Sala Lauak Bulat Goreng Panas (10 pcs)', 10000, 'Bola-bola tepung beras renyah gurih di luar empuk di dalam dengan rasa ikan teri asin.'),
(10, 'Pisang Kapok Goreng Pasir (5 pcs)', 12000, 'Pisang manis dilapisi adonan tepung renyah berpasir nikmat dicocol saus manis.'),
(10, 'Tahu Isi Sayur Udang Pedas (5 pcs)', 10000, 'Tahu goreng isi tauge, wortel, dan udang cincang dengan bumbu cabai rawit pedas.');

-- Data Makanan Khas Daerah
INSERT INTO makanan_khas (id_makanan, nama_makanan, asal_daerah, cerita, id_toko) VALUES
(1, 'Rendang', 'Minangkabau', 'Rendang adalah masakan daging berbumbu rempah kaya yang dimasak perlahan hingga kering. Ditetapkan sebagai "Makanan Terenak di Dunia" versi CNN pada tahun 2011, 2017, dan 2023. Rendang melambangkan kesabaran dan ketekunan masyarakat Minang karena proses memasaknya yang panjang.', 1),
(2, 'Sate Padang', 'Pariaman/Padang', 'Sate Padang terdiri dari potongan daging dan jeroan sapi yang ditusuk, dibakar, lalu disiram kuah kental kaya rempah. Kuahnya biasanya berwarna kuning (khas Pariaman) atau cokelat (khas Payakumbuh). Berbeda dengan sate dari daerah lain, sate Padang menggunakan daging yang tidak dimarinasi lama agar tetap segar.', 2),
(3, 'Kawa Daun', 'Dataran Tinggi Minangkabau', 'Kawa daun adalah minuman tradisional dari seduhan daun kopi yang dikeringkan, bukan dari biji kopi. Tradisi ini lahir ketika masyarakat Minang tidak ingin menyia-nyiakan daun kopi yang melimpah. Disajikan dalam tempurung kelapa, kawa daun memiliki cita rasa earthy dan herbal yang unik.', 8),
(4, 'Sala Lauak', 'Pesisir Padang', 'Sala lauak adalah gorengan khas pesisir Padang berupa bola-bola tepung berisi ikan asin. Berbeda dengan pempek Palembang, sala lauak memiliki tekstur renyah di luar dan gurih di dalam. Biasanya dinikmati sore hari sebagai teman minum teh atau kopi di pinggir pantai.', 10),
(5, 'Keripik Sanjai', 'Bukittinggi/Padang', 'Keripik sanjai adalah keripik singkong khas Minang yang dilapisi bumbu cabai merah. Dinamai "sanjai" dari nama daerah asalnya (Sanja, Bukittinggi). Ciri khasnya adalah rasa pedas manis yang berlapis, dengan tekstur renyah yang tidak alot.', 4);