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
  foto VARCHAR(255)
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

-- ==============================================================
-- DATA AWAL (SEEDER)
-- ==============================================================

-- Akun admin default:
-- username: admin
-- password: admin123 (hash BCRYPT $2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa)
-- Akun user demo:
-- username: user
-- password: user123 (hash BCRYPT $2y$10$y6tW8q8Zq0P8U2O8R6Z9veiM7mCkWqR7q2Y1K2O0Q8R6Z9veiM7mC)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'admin'),
('user', '$2y$10$tZg44Xf8v7M0w.q8A9zVreR3zU9m7mR4mY7hL2cK9mN8bV5cV6c0.', 'user')
ON DUPLICATE KEY UPDATE id_user=id_user;

-- 10 Toko Fiktif di Area Padang (Pantai Padang/Taplau, Kota Tua/Muaro, Masjid Raya, Khatib Sulaiman, dll)
-- Kategori: Rumah Makan, Sate, Kopi, Oleh-oleh, Camilan
INSERT INTO toko (id_toko, nama_toko, alamat, kategori, deskripsi, latitude, longitude, foto) VALUES
(1, 'RM Gonjong Sakato', 'Jl. Samudera No. 18, Pantai Padang (Taplau)', 'Rumah Makan', 'Rumah makan Padang autentik tepi pantai menyajikan rendang daging legendaris, gulai tunjang, dan dendeng batokok renyah dengan pemandangan sunset Pantai Padang.', -0.9324500, 100.3541200, 'toko1.jpg'),
(2, 'Sate Mak Syukur Muaro', 'Jl. Batang Arau No. 42, Kawasan Kota Tua Muaro', 'Sate', 'Spesialis sate Padang kuah kuning gurih berempah khas Pariaman dengan potongan daging sapi empuk dan lidah gurih bertabur bawang goreng renyah.', -0.9632000, 100.3685000, 'toko2.jpg'),
(3, 'Kopi Nan Batigo', 'Jl. KH. Ahmad Dahlan No. 12, Dekat Masjid Raya Sumbar', 'Kopi', 'Kedai kopi modern bernuansa Minang dengan racikan kopi Robusta Solok Selatan, Kopi Kawa Daun tradisional, dan cemilan pisang kapik kuah kinca.', -0.9238000, 100.3615000, 'toko3.jpg'),
(4, 'Oleh-Oleh Sanjai Rangkiang', 'Jl. Khatib Sulaiman No. 88, Ulak Karang', 'Oleh-oleh', 'Sentra oleh-oleh khas Ranah Minang terlengkap mulai dari keripik sanjai balado pedas manis, karak kaliang, drakot, hingga rendang kemasan vakum siap saji.', -0.9082000, 100.3589000, 'toko4.jpg'),
(5, 'Es Cendol Pattimura Ranah', 'Jl. Pattimura No. 25, Padang Barat', 'Camilan', 'Minuman segar legendaris cendol tepung beras dengan gula aren asli, santan kelapa kental, tapai singkong, dan serutan es dingin pelepas dahaga.', -0.9486000, 100.3592000, 'toko5.jpg'),
(6, 'RM Silinduang Bulan', 'Jl. Veteran No. 54, Padang Pasir', 'Rumah Makan', 'Menyajikan sajian khas gulai kepala kakap bumbu rempah kuning kental, ayam pop minyak kelapa murni, dan sambalado ijo petai selera bangsawan.', -0.9415000, 100.3568000, 'toko6.jpg'),
(7, 'Sate Danguang-Danguang Payakumbuh', 'Jl. Pemuda No. 15, Olo Ladang', 'Sate', 'Sate daging dan jeroan sapi bakar kuah rempah kecokelatan manis gurih khas Luak Limopuluah disajikan dengan kerupuk kulit siram kuah.', -0.9508000, 100.3562000, 'toko7.jpg'),
(8, 'Kawa Daun Pondok Raso', 'Jl. Niaga No. 7, Kawasan Pecinan Pondok', 'Kopi', 'Sensasi minum seduhan daun kopi kering di dalam wadah tempurung kelapa asli dengan suasana hangat ruko tua heritage Kota Padang.', -0.9587000, 100.3654000, 'toko8.jpg'),
(9, 'Kerupuk Jangek & Dakak Minang', 'Jl. Belakang Olo No. 3, Padang Barat', 'Oleh-oleh', 'Produsen tangan pertama kerupuk jangek (kulit sapi) renyah gurih dan dakak-dakak ubi renyah kunyit khas ranah Minang terfavorit para wisatawan.', -0.9452000, 100.3621000, 'toko9.jpg'),
(10, 'Sala Lauak & Pisang Goreng Pasia', 'Jl. Pasir Jambak No. 10, Pasie Nan Tigo', 'Camilan', 'Cemilan khas pesisir sala lauak bulat gurih berisi ikan asin dan pisang kapok goreng tepung beras renyah krispi yang nikmat dinikmati sore hari.', -0.8712000, 100.3184000, 'toko10.jpg')
ON DUPLICATE KEY UPDATE id_toko=id_toko;

-- 3-4 Menu per Toko beserta Harga format INT
INSERT INTO menu (id_toko, nama_menu, harga, deskripsi) VALUES
-- Toko 1: RM Gonjong Sakato
(1, 'Rendang Daging Sapi Spesial', 25000, 'Daging sapi pilihan dimasak perlahan dengan kelapa dan rempah hingga hitam meresap.'),
(1, 'Gulai Tunjang Kikil Lembut', 28000, 'Kikil sapi empuk dengan kuah gulai santan kaya bumbu kunyit dan serai.'),
(1, 'Dendeng Batokok Balado Merah', 24000, 'Dendeng sapi pipih lembut digoreng kilat lalu disiram ulekan cabai merah segar.'),
(1, 'Ayam Gulai Kurma', 20000, 'Ayam kampung kuah putih rempah ketumbar dan pala wangi non-pedas.'),

-- Toko 2: Sate Mak Syukur Muaro
(2, 'Sate Daging Padang Komplit', 28000, 'Seporsi sate daging sapi panggang dan ketupat disiram kuah kental kuning gurih.'),
(2, 'Sate Lidah Sapi Lembut', 32000, 'Potongan lidah sapi empuk dengan aroma bakaran khas arang batok kelapa.'),
(2, 'Ketupat Kuah Sate Ekstra', 10000, 'Ketupat pulen berbalur kuah sate kuning kental dan taburan bawang goreng.'),
(2, 'Keripik Balado Pelengkap Sate', 5000, 'Kerupuk singkong balado pedas manis sebagai pelengkap renyah saat bersantap.'),

-- Toko 3: Kopi Nan Batigo
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

-- Toko 7: Sate Danguang-Danguang Payakumbuh
(7, 'Sate Danguang Daging & Usus', 26000, 'Sate kuah cokelat rempah gurih lembut dengan taburan kelapa parut sangrai.'),
(7, 'Sate Jantung & Daging Campur', 28000, 'Kombinasi daging sapi empuk dan jantung bakar beraroma khas.'),
(7, 'Kerupuk Jangek Kuah Celup', 8000, 'Kerupuk kulit sapi dicelup kuah sate kental gurih.'),

-- Toko 8: Kawa Daun Pondok Raso
(8, 'Kawa Daun Jahe Hangat Batok', 14000, 'Air rebusan daun kopi berkhasiat dipadu geprekan jahe merah hangat.'),
(8, 'Kawa Daun Susu Hangat', 15000, 'Rebusan daun kopi dipadu susu kental manis legit lembut di tenggorokan.'),
(8, 'Ketan Gurih Sarikaya Padang', 12000, 'Beras ketan pulen disajikan bersama selai sarikaya telur gula merah kukus.'),

-- Toko 9: Kerupuk Jangek & Dakak Minang
(9, 'Kerupuk Jangek Sapi Renyah (200g)', 30000, 'Kerupuk kulit sapi goreng mekar sempurna, gurih renyah tanpa bahan kimia.'),
(9, 'Dakak-Dakak Ubi Kuning (250g)', 18000, 'Potongan dadu singkong renyah gurih berbalur bumbu rempah daun kunyit.'),
(9, 'Keripik Kentang Balado (200g)', 28000, 'Irisan tipis kentang goreng renyah dibalut sambal merah balado kental.'),

-- Toko 10: Sala Lauak & Pisang Goreng Pasia
(10, 'Sala Lauak Bulat Goreng Panas (10 pcs)', 10000, 'Bola-bola tepung beras renyah gurih di luar empuk di dalam dengan rasa ikan teri asin.'),
(10, 'Pisang Kapok Goreng Pasir (5 pcs)', 12000, 'Pisang manis dilapisi adonan tepung renyah berpasir nikmat dicocol saus manis.'),
(10, 'Tahu Isi Sayur Udang Pedas (5 pcs)', 10000, 'Tahu goreng isi tauge, wortel, dan udang cincang dengan bumbu cabai rawit pedas.');
