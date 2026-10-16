-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_kuliner_padang
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `kuliner_modern`
--

DROP TABLE IF EXISTS `kuliner_modern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kuliner_modern` (
  `id_modern` int(11) NOT NULL AUTO_INCREMENT,
  `nama_makanan` varchar(100) NOT NULL,
  `kategori_inovasi` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto` varchar(255) NOT NULL,
  `id_toko` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_modern`),
  KEY `id_toko` (`id_toko`),
  CONSTRAINT `kuliner_modern_ibfk_1` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kuliner_modern`
--

LOCK TABLES `kuliner_modern` WRITE;
/*!40000 ALTER TABLE `kuliner_modern` DISABLE KEYS */;
INSERT INTO `kuliner_modern` VALUES (1,'Burger Rendang','Western x Minang Fusion','Roti burger (bun) modern dengan isian daging sapi dan saus bumbu rendang pekat pengganti saus BBQ konvensional.','modern_burger_rendang.jpg',16),(2,'Ayam Katsu Balado','Japanese x Minang Fusion','Fillet ayam goreng tepung renyah ala Jepang yang disajikan dengan siraman sambal balado merah khas Minang.','modern_ayam_katsu.jpg',17),(3,'Pizza Rendang','Italian x Minang Fusion','Pizza ber-topping suwiran daging rendang, potongan bawang bombay, dan lelehan keju mozzarella di atasnya.','modern_pizza_rendang.jpg',18),(4,'Nasi Goreng Rendang','Inovasi Nusantara','Nasi goreng yang bumbu utamanya menggunakan racikan bumbu rendang, memberikan cita rasa rempah yang kaya dan wangi.','modern_nasgor_rendang.jpg',20),(5,'Spaghetti Balado','Italian x Minang Fusion','Pasta spaghetti khas Italia yang ditumis menggunakan bumbu sambal balado pedas manis dengan tambahan udang atau daging sapi.','modern_spaghetti_balado.jpg',17),(6,'Cireng Bumbu Rendang','Street Food Inovasi','Camilan aci goreng khas Sunda yang disajikan dengan cocolan saus bumbu rendang kental yang gurih.','modern_cireng_rendang.jpg',18),(7,'Lasagna Rendang','Italian x Minang Fusion','Lapisan pasta lasagna panggang dengan saus keju yang dipadukan dengan isian daging bumbu rendang, menggantikan saus bolognese.','modern_lasagna_rendang.jpg',16),(8,'Kopmil (Kopi Milo) Kekinian','Beverage Modern','Minuman es kopi susu khas kedai Padang yang dicampur dengan bubuk Milo kental dan berbagai pilihan topping modern seperti keju atau boba.','modern_kopmil.jpg',19),(9,'Es Durian Keju Oreo','Dessert Inovasi','Modifikasi es durian legendaris Padang yang ditambahkan es krim vanila, parutan keju melimpah, dan remahan biskuit Oreo.','modern_es_durian.jpg',20),(10,'Teh Talua Green Tea / Cokelat','Beverage Inovasi','Inovasi minuman STMJ khas Minang (teh telur) yang dikombinasikan dengan bubuk matcha atau cokelat untuk rasa yang lebih modern.','modern_teh_talua.jpg',19);
/*!40000 ALTER TABLE `kuliner_modern` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `makanan_khas`
--

DROP TABLE IF EXISTS `makanan_khas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `makanan_khas` (
  `id_makanan` int(11) NOT NULL AUTO_INCREMENT,
  `nama_makanan` varchar(100) NOT NULL,
  `asal_daerah` varchar(100) DEFAULT NULL,
  `cerita` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `id_toko` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_makanan`),
  KEY `id_toko` (`id_toko`),
  CONSTRAINT `makanan_khas_ibfk_1` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `makanan_khas`
--

LOCK TABLES `makanan_khas` WRITE;
/*!40000 ALTER TABLE `makanan_khas` DISABLE KEYS */;
INSERT INTO `makanan_khas` VALUES (1,'Soto Padang','Kota Padang','Sup kaldu sapi bening gurih berisi potongan daging renyah, bihun, perkedel kentang, dan taburan kerupuk merah.','khas_soto_padang.jpg',11),(2,'Ayam Pop','Bukittinggi','Ayam kampung berkulit putih pucat yang direbus dengan air kelapa dan rempah, lalu digoreng sebentar dan disajikan bersama sambal tomat khusus.','khas_ayam_pop.jpg',6),(3,'Dendeng Batokok','Bukittinggi & Padang','Daging sapi yang direbus, dipukul (ditokok) hingga pipih dan lunak, lalu disiram minyak dan sambal cabai hijau mentah.','khas_dendeng_batokok.jpg',12),(4,'Dendeng Balado','Padang','Irisan daging sapi yang digoreng garing hingga renyah (seperti keripik), lalu diaduk dengan sambal cabai merah goreng.','khas_dendeng_balado.jpg',13),(5,'Gulai Tunjang','Minangkabau','Olahan kikil atau urat kaki sapi yang dimasak dalam kuah gulai santan kuning kental yang gurih.','khas_gulai_tunjang.jpg',1),(6,'Asam Padeh','Pesisir Minangkabau','Hidangan ikan (biasanya tongkol atau kakap) yang dimasak dengan kuah merah menyala berasa asam dan pedas tanpa menggunakan santan.','khas_asam_padeh.jpg',14),(7,'Martabak Mesir','Kubang & Padang','Martabak telur renyah berisi daging sapi cincang berbumbu rempah dan daun bawang, dinikmati dengan kuah cuka asam manis pedas.','khas_martabak_mesir.jpg',15),(8,'Bubur Kampiun','Bukittinggi','Menu sarapan manis berupa kombinasi bubur sumsum, ketan putih, kolak pisang/ubi, bubur kacang hijau, candil, dan srikaya dalam satu mangkuk.','khas_bubur_kampiun.jpg',5),(9,'Rendang','Minangkabau','Rendang dimasak perlahan bersama santan dan rempah hingga bumbunya meresap dan mengering. Proses panjang ini menjadikannya sajian penting dalam jamuan dan perayaan keluarga Minang.',NULL,1),(10,'Sate Padang','Padang dan Pariaman','Potongan daging atau jeroan dibakar lalu disajikan dengan kuah kental berbumbu. Ragam daerah menghadirkan kuah dengan warna dan racikan rempah yang berbeda.',NULL,2),(11,'Kawa Daun','Dataran Tinggi Minangkabau','Minuman tradisional ini dibuat dari daun kopi yang dikeringkan dan diseduh. Di sejumlah tempat, kawa daun disajikan dalam tempurung kelapa dengan cita rasa ringan dan beraroma.',NULL,8),(12,'Sala Lauak','Pesisir Pariaman','Gorengan berbentuk bola dari adonan tepung beras berbumbu dan ikan. Sala lauak umum dijumpai sebagai kudapan hangat di kawasan pesisir Sumatera Barat.',NULL,10),(13,'Keripik Sanjai','Bukittinggi','Keripik berbahan singkong ini dikenal dengan pilihan bumbu gurih hingga balado. Sanjai menjadi salah satu oleh-oleh yang lekat dengan kawasan Bukittinggi dan sekitarnya.',NULL,4);
/*!40000 ALTER TABLE `makanan_khas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL AUTO_INCREMENT,
  `id_toko` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `harga` int(11) DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `foto_menu` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_menu`),
  KEY `id_toko` (`id_toko`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,1,'Rendang Daging Sapi Spesial',25000,'Daging sapi pilihan dimasak perlahan dengan kelapa dan rempah hingga hitam meresap.','menu_rendang.jpg'),(2,1,'Gulai Tunjang Kikil Lembut',28000,'Kikil sapi empuk dengan kuah gulai santan kaya bumbu kunyit dan serai.','khas_gulai_tunjang.jpg'),(3,1,'Dendeng Batokok Balado Merah',24000,'Dendeng sapi pipih lembut digoreng kilat lalu disiram ulekan cabai merah segar.','khas_dendeng_batokok.jpg'),(4,1,'Ayam Gulai Kurma',20000,'Ayam kampung kuah putih rempah ketumbar dan pala wangi non-pedas.','menu_ayam_gulai.jpg'),(5,2,'Sate Daging Padang Komplit',28000,'Seporsi sate daging sapi panggang dan ketupat disiram kuah kental kuning gurih.','menu_sate_daging.jpg'),(6,2,'Sate Lidah Sapi Lembut',32000,'Potongan lidah sapi empuk dengan aroma bakaran khas arang batok kelapa.','menu_sate_lidah.jpg'),(7,2,'Ketupat Kuah Sate Ekstra',10000,'Ketupat pulen berbalur kuah sate kuning kental dan taburan bawang goreng.','menu_ketupat_sate.jpg'),(8,2,'Keripik Balado Pelengkap Sate',5000,'Kerupuk singkong balado pedas manis sebagai pelengkap renyah saat bersantap.','menu_keripik_balado.jpg'),(9,3,'Kopi Solok Selatan V60',22000,'Kopi Arabika Solok Selatan seduh manual rasa buah segar asam seimbang.','menu_kopi_solok.jpg'),(10,3,'Es Kopi Susu Aren Gula Ranah',18000,'Espresso lokal dipadu susu segar dan manis legit gula aren murni.','menu_kopi_susu.jpg'),(11,3,'Seduhan Kawa Daun Klasik',12000,'Seduhan daun kopi sangrai di batok kelapa kaya antioksidan dan aroma herbal unik.','menu_kawa_daun.jpg'),(12,3,'Pisang Kapik Keju Cokelat',15000,'Pisang bakar pipih manis gurih topping kelapa parut manis dan sirup kinca.','menu_pisang_kapik.jpg'),(13,4,'Keripik Sanjai Balado Merah (500g)',35000,'Keripik singkong renyah dengan lapisan cabai merah berkilau pedas gurih.','menu_sanjai.jpg'),(14,4,'Rendang Suwir Kemasan Vakum (250g)',65000,'Rendang suwir sapi praktis siap saji tahan lama tanpa bahan pengawet.','menu_rendang_suwir.jpg'),(15,4,'Karak Kaliang Kuning (350g)',20000,'Cemilan angka delapan tepung ubi dengan rasa gurih kunyit dan bawang putih.','menu_karak_kaliang.jpg'),(16,4,'Bolu Pandan Minang Premium',45000,'Kue bolu lembut aroma pandan asli wangi butter legit.','menu_bolu_pandan.jpg'),(17,5,'Es Cendol Durian Segar',22000,'Cendol lembut gula aren ditambah daging durian asli nan wangi legit.','menu_cendol_durian.jpg'),(18,5,'Es Cendol Klasik Gula Aren',14000,'Cendol beras hijau pandan, santan murni, gula aren, dan tapai singkong pulen.','menu_cendol_klasik.jpg'),(19,5,'Bubur Kampiun Komplit',18000,'Campuran kolak pisang, bubur sumsum, candil, ketan putih, dan lupis ketan.','khas_bubur_kampiun.jpg'),(20,5,'Es Tebak Dingin Manis',16000,'Es campur khas Minang berisi tebak, kolang kaling, cincau hitam, dan sirup merah.','menu_es_tebak.jpg'),(21,6,'Gulai Kepala Ikan Kakap Jumbo',75000,'Kepala kakap segar berukuran besar dengan gulai kuning pedas asam segar belimbing wuluh.','menu_gulai_kakap.jpg'),(22,6,'Ayam Pop Sambal Tomat Padang',22000,'Ayam kampung gurih digoreng minyak kelapa sebentar, disajikan sambal tomat asam pedas.','khas_ayam_pop.jpg'),(23,6,'Sambalado Ijo Teri Jengkol',18000,'Cabai hijau ulek kasar ditumis wangi bersama ikan teri asin dan potongan jengkol muda.','menu_sambalado_ijo.jpg'),(24,6,'Gulai Daun Singkong Tumbuk',12000,'Sayur pucuk ubi tumbuk kuah santan kuning sedap beraroma rimbang (tekokak).','menu_gulai_singkong.jpg'),(25,7,'Sate Danguang Daging & Usus',26000,'Sate kuah cokelat rempah gurih lembut dengan taburan kelapa parut sangrai.','menu_sate_danguang.jpg'),(26,7,'Sate Jantung & Daging Campur',28000,'Kombinasi daging sapi empuk dan jantung bakar beraroma khas.','menu_sate_jantung.jpg'),(27,7,'Kerupuk Jangek Kuah Celup',8000,'Kerupuk kulit sapi dicelup kuah sate kental gurih.','menu_jangek_celup.jpg'),(28,8,'Kawa Daun Jahe Hangat Batok',14000,'Air rebusan daun kopi berkhasiat dipadu geprekan jahe merah hangat.','menu_kawa_jahe.jpg'),(29,8,'Kawa Daun Susu Hangat',15000,'Rebusan daun kopi dipadu susu kental manis legit lembut di tenggorokan.','menu_kawa_susu.jpg'),(30,8,'Ketan Gurih Sarikaya Padang',12000,'Beras ketan pulen disajikan bersama selai sarikaya telur gula merah kukus.','menu_ketan_sarikaya.jpg'),(31,9,'Kerupuk Jangek Sapi Renyah (200g)',30000,'Kerupuk kulit sapi goreng mekar sempurna, gurih renyah tanpa bahan kimia.','menu_jangek_renyah.jpg'),(32,9,'Dakak-Dakak Ubi Kuning (250g)',18000,'Potongan dadu singkong renyah gurih berbalur bumbu rempah daun kunyit.','menu_dakak_dakak.jpg'),(33,9,'Keripik Kentang Balado (200g)',28000,'Irisan tipis kentang goreng renyah dibalut sambal merah balado kental.','menu_kentang_balado.jpg'),(34,10,'Sala Lauak Bulat Goreng Panas (10 pcs)',10000,'Bola-bola tepung beras renyah gurih di luar empuk di dalam dengan rasa ikan teri asin.','menu_sala_lauak.jpg'),(35,10,'Pisang Kapok Goreng Pasir (5 pcs)',12000,'Pisang manis dilapisi adonan tepung renyah berpasir nikmat dicocol saus manis.','menu_pisang_goreng.jpg'),(36,10,'Tahu Isi Sayur Udang Pedas (5 pcs)',10000,'Tahu goreng isi tauge, wortel, dan udang cincang dengan bumbu cabai rawit pedas.','menu_tahu_isi.jpg'),(109,11,'Soto Padang Daging Sapi Renyah',28000,'Soto kuah bening kaldu sapi dengan potongan dendeng sapi garing, bihun, dan perkedel kentang.','khas_soto_padang.jpg'),(110,11,'Soto Padang Paru Gurih',27000,'Soto Padang dengan irisan paru sapi goreng krispi berkuah rempah hangat.','khas_soto_padang.jpg'),(111,11,'Perkedel Kentang Minang Tambahan',5000,'Perkedel kentang lembut gurih pelengkap kuah soto.','menu_sala_lauak.jpg'),(112,11,'Kerupuk Merah Soto Sebungkus',4000,'Kerupuk merah khas pendamping wajib Soto Padang.','menu_keripik_balado.jpg'),(113,12,'Dendeng Batokok Lado Mudo Spesial',28000,'Daging sapi ditokok lembut dengan lumuran cabai hijau mentah dan minyak kelapa wangi.','khas_dendeng_batokok.jpg'),(114,12,'Dendeng Batokok Bakar Arang',30000,'Dendeng ditokok lalu dibakar arang beraroma smokey khas ranah Minang.','khas_dendeng_batokok.jpg'),(115,12,'Gulai Pucuk Ubi Ikan Teri',12000,'Sayur daun singkong muda santan gurih pendamping dendeng batokok.','menu_gulai_singkong.jpg'),(116,13,'Dendeng Balado Kering Krispi',27000,'Irisan tipis daging sapi digoreng garing renyah dibalut ulekan cabai merah goreng.','khas_dendeng_balado.jpg'),(117,13,'Dendeng Balado Basah Minyak',29000,'Dendeng daging sapi empuk sedikit berkuah minyak cabai merah gurih.','khas_dendeng_balado.jpg'),(118,13,'Telur Dadar Barendo Padang',12000,'Telur bebek dadar tebal krispi bersarang renyah gurih.','khas_martabak_mesir.jpg'),(119,14,'Gulai Asam Padeh Ikan Tongkol',26000,'Potongan ikan tongkol segar berkuah asam pedas merah menyala tanpa santan.','khas_asam_padeh.jpg'),(120,14,'Asam Padeh Kepala Kakap Laut',45000,'Kepala kakap segar bumbu asam padeh segar belimbing wuluh pedas menggugah selera.','khas_asam_padeh.jpg'),(121,14,'Urap Daun Pepaya & Singkong',10000,'Sayur rebusan segar kelapa parut berbumbu sedap pelepas pedas.','menu_gulai_singkong.jpg'),(122,15,'Martabak Mesir Daging Sapi Komplit',35000,'Martabak telur tebal krispi isi daging sapi cincang, daun bawang, dan kuah cuka pedas manis.','khas_martabak_mesir.jpg'),(123,15,'Martabak Mesir Spesial Dobel Daging',45000,'Martabak telur dengan isian daging sapi dobel porsi mantap.','khas_martabak_mesir.jpg'),(124,15,'Roti Canai Kuah Kari Kambing',20000,'Roti canai pipih gurih dengan siraman kuah kari rempah kambing sedap.','menu_sala_lauak.jpg'),(125,16,'Burger Rendang Sapi Spesial',38000,'Roti bun lembut dengan patty daging sapi panggang berlumur bumbu rendang pekat.','modern_burger_rendang.jpg'),(126,16,'Lasagna Rendang Mozzarella',42000,'Lapisan pasta panggang dengan saus keju dan isian daging rendang gurih.','modern_lasagna_rendang.jpg'),(127,16,'French Fries Saus Rendang Mayo',22000,'Kentang goreng krispi dengan cocolan saus mayones rendang wangi.','menu_kentang_balado.jpg'),(128,17,'Ayam Katsu Sambal Balado Merah',32000,'Fillet ayam goreng tepung renyah khas Jepang berbalur sambal balado merah pedas gurih.','modern_ayam_katsu.jpg'),(129,17,'Spaghetti Balado Udang & Daging',35000,'Pasta spaghetti Italia ditumis sambal balado pedas manis dengan udang segar.','modern_spaghetti_balado.jpg'),(130,17,'Chicken Karaage Lado Hijau',28000,'Ayam goreng tepung gurih dengan siraman ulekan cabai hijau segar.','khas_dendeng_batokok.jpg'),(131,18,'Pizza Rendang Daging Sapi (Medium)',55000,'Pizza Italia ber-topping daging rendang, bawang bombay, dan lelehan mozzarella melimpah.','modern_pizza_rendang.jpg'),(132,18,'Cireng Krispi Bumbu Rendang (8 pcs)',18000,'Camilan aci goreng renyah disajikan dengan cocolan kuah saus bumbu rendang gurih.','modern_cireng_rendang.jpg'),(133,18,'Macaroni Panggang Balado Keju',32000,'Makaroni panggang krimi dengan sentuhan pedas manis sambalado Minang.','modern_lasagna_rendang.jpg'),(134,19,'Kopmil (Kopi Milo) Boba Keju',20000,'Perpaduan kopi hitam, susu kental, dan bubuk Milo kental bertabur keju parut & boba.','modern_kopmil.jpg'),(135,19,'Teh Talua Matcha Green Tea',18000,'Kocokan kuning telur teh rempah Minang berpadu bubuk matcha hijau lembut berbusa.','modern_teh_talua.jpg'),(136,19,'Teh Talua Dark Chocolate',18000,'Teh telur tradisional dengan sensasi cokelat pekat tanpa bau amis.','modern_teh_talua.jpg'),(137,20,'Es Durian Keju Oreo Spesial',26000,'Es durian asli dengan es krim vanila lembut, taburan parutan keju gurih, dan remahan Oreo.','modern_es_durian.jpg'),(138,20,'Nasi Goreng Bumbu Rendang Daging',30000,'Nasi goreng beraroma wangi rempah rendang dengan suwiran daging sapi dan acar segar.','modern_nasgor_rendang.jpg'),(139,20,'Pancake Durian Lumer Padang',22000,'Pancake kulit pandan berisi daging durian murni dan vla manis dingin.','modern_es_durian.jpg'),(140,21,'Gulai Cincang Sapi Bumbu Pekat',26000,'Potongan daging dan urat sapi gurih dengan kuah gulai merah cincang berempah kapulaga dan cengkih.','menu_gulai_tunjang.jpg'),(141,21,'Ayam Bakar Bumbu Padang',22000,'Ayam bakar wangi bumbu kuning santan yang dibakar di atas arang batok kelapa.','menu_ayam_gulai.jpg'),(142,21,'Ikan Nila Goreng Sambalado Merah',20000,'Ikan nila segar goreng renyah disiram ulekan cabai merah goreng gurih segar.','khas_dendeng_balado.jpg'),(143,22,'Sate Daging Kuah Kuning Pariaman',27000,'Seporsi tusukan sate daging sapi bakar dan ketupat pulen bersiram kuah kuning kental gurih pedas.','menu_sate_daging.jpg'),(144,22,'Sate Usus & Lidah Bakar',28000,'Perpaduan usus sapi kenyal gurih dan lidah sapi bakar bumbu rempah kuning.','menu_sate_lidah.jpg'),(145,22,'Kerupuk Kulit Kuah Sate',6000,'Kerupuk jangek sapi renyah disiram kuah sate panas gurih.','menu_jangek_celup.jpg'),(146,23,'Teh Talua 4 Tingkat Tradisional',15000,'Minuman stamina teh telur bebek kocok pekat dengan busa tebal dan perasan jeruk nipis segar.','modern_teh_talua.jpg'),(147,23,'Kopi Hitam Saring Robusta',10000,'Seduhan kopi robusta lokal racikan saring wangi pekat tanpa ampas berlebih.','menu_kopi_solok.jpg'),(148,23,'Lupis Ketan Kelapa Parut Gula Aren',12000,'Kue lupis ketan hijau pulen berbalur kelapa parut kukus dan sirup gula aren kental.','menu_ketan_sarikaya.jpg'),(149,24,'Sala Lauak Goreng Panas (10 pcs)',10000,'Bola-bola tepung beras goreng khas pesisir dengan isian ikan asin cincang gurih renyah di luar.','menu_sala_lauak.jpg'),(150,24,'Pisang Goreng Kipas Krispi (5 pcs)',12000,'Pisang kepok manis berbalut adonan tepung beras renyah krispi.','menu_pisang_goreng.jpg'),(151,24,'Bakwan Jagung Manis Ranah (5 pcs)',10000,'Gorengan perkedel jagung manis renyah berpadu irisan seledri dan daun bawang.','menu_tahu_isi.jpg'),(152,25,'Gulai Kepala Ikan Kakap Simpang',50000,'Kepala ikan kakap segar berkuah santan rempah kuning pedas asam belimbing wuluh segar.','menu_gulai_kakap.jpg'),(153,25,'Ayam Pop Asli Sambal Tomat',22000,'Ayam kampung rebus air kelapa digoreng kilat disajikan sambal tomat manis pedas khas Minang.','khas_ayam_pop.jpg'),(154,25,'Gulai Daun Singkong Tumbuk Teri',12000,'Pucuk daun ubi tumbuk kuah santan gurih beraroma rimbang dan ikan teri asin.','menu_gulai_singkong.jpg'),(155,26,'Sate Danguang Daging Sapi & Serundeng',27000,'Tusukan sate daging sapi kuah cokelat gurih kaya rempah disajikan taburan kelapa sangrai.','menu_sate_danguang.jpg'),(156,26,'Sate Jantung & Daging Campur',28000,'Kombinasi daging sapi empuk dan jantung bakar arang wangi berbumbu gurih.','menu_sate_jantung.jpg'),(157,26,'Ketupat Pulen Kuah Cokelat Ekstra',10000,'Ketupat beras lokal pulen berbalur kuah rempah sate danguang kental.','menu_ketupat_sate.jpg'),(158,27,'Keripik Sanjai Balado Merah (250g)',20000,'Keripik singkong iris tipis renyah dilapisi pasta cabai merah berkilau pedas gurih.','khas_sanjai.jpg'),(159,27,'Dakak-Dakak Ubi Kuning (250g)',18000,'Potongan dadu singkong goreng renyah berbumbu rempah daun kunyit dan bawang putih.','menu_dakak_dakak.jpg'),(160,27,'Karak Kaliang Kuning Angka Delapan',16000,'Camilan tradisional tepung ubi bentuk angka delapan gurih renyah tanpa bahan pengawet.','menu_karak_kaliang.jpg'),(161,28,'Martabak Mesir Daging Sapi Renyah',30000,'Martabak telur krispi isi daging sapi cincang bumbu rempah dengan kuah cuka asam pedas.','khas_martabak_mesir.jpg'),(162,28,'Es Tebak Segar Padang',15000,'Minuman es campur tradisional berisi tebak tepung beras, kolang-kaling, tapai, dan santan sirup.','menu_es_tebak.jpg'),(163,28,'Kopi Tubruk Manis Legit',8000,'Kopi hitam racikan lokal diseduh air mendidih dengan gula pasir manis pas.','menu_kopi_solok.jpg'),(164,29,'Dendeng Batokok Lado Mudo Kampus',24000,'Dendeng sapi pipih lembut disiram cabai hijau ulek kasar dan minyak kelapa wangi.','khas_dendeng_batokok.jpg'),(165,29,'Rendang Daging Sapi Ampera',25000,'Rendang daging sapi santan hitam pekat meresap resep rumahan bundo kanduang.','khas_rendang.jpg'),(166,29,'Telur Dadar Barendo Crispy',10000,'Telur dadar bebek tebal renyah bersarang favorit lauk makan siang hemat.','khas_martabak_mesir.jpg'),(167,30,'Soto Padang Daging Sapi Komplit',28000,'Semangkuk sup kaldu bening sapi gurih berisi daging goreng kering renyah, bihun, dan perkedel.','khas_soto_padang.jpg'),(168,30,'Soto Paru Krispi Padang',27000,'Soto Padang dengan isian paru sapi goreng renyah krispi yang gurih meresap kuah kaldu.','khas_soto_padang.jpg'),(169,30,'Kerupuk Merah Goreng Minang',4000,'Pelengkap khas Soto Padang kerupuk tapioka merah renyah dicelup kuah soto.','menu_keripik_balado.jpg'),(170,31,'Kopmil Cokelat Milo Keju',18000,'Minuman es kopi paduan bubuk Milo kental dengan parutan keju gurih melimpah.','modern_kopmil.jpg'),(171,31,'Teh Talua Matcha Latte',18000,'Kreasi teh telur bebek berpadu bubuk green tea matcha wangi tanpa bau amis.','modern_teh_talua.jpg'),(172,31,'French Fries Bumbu Balado Manis',15000,'Kentang goreng stik renyah dibaluri taburan bumbu balado pedas manis gurih.','menu_kentang_balado.jpg'),(173,32,'Bika Panggang Minang Hangat (3 pcs)',12000,'Kue bika tepung beras kelapa parut panggang cetakan tanah liat manis legit aromatik.','menu_pisang_goreng.jpg'),(174,32,'Lupis Ketan Kelapa Kinca Gula Aren',14000,'Beras ketan putih pulen segitiga dibalur kelapa muda parut dan sirup gula aren kental.','menu_ketan_sarikaya.jpg'),(175,32,'Bubur Kampiun Porsi Mini',16000,'Campuran bubur sumsum lembut, kolak pisang, candil ketan, dan lupis manis sarapan pagi.','khas_bubur_kampiun.jpg'),(176,33,'Gulai Tunjang Kikil Balai Kota',30000,'Kikil kaki sapi empuk lembut dimasak perlahan dalam kuah santan kuning kental kaya rempah.','khas_gulai_tunjang.jpg'),(177,33,'Rendang Daging Sapi Dapur Ranah',26000,'Potongan daging sapi dimasak berjam-jam hingga santan berminyak hitam kaya rempah.','khas_rendang.jpg'),(178,33,'Ayam Gulai Kurma Putih Gurih',22000,'Ayam kampung berkuah putih rempah pala, ketumbar, dan kapulaga gurih ramah anak.','menu_ayam_gulai.jpg'),(179,34,'Sate Padang Daging & Ketupat Porsi Pas',28000,'Daging sapi bakar arang disajikan bersama ketupat pulen berbalur kuah sate kuning berempah.','menu_sate_daging.jpg'),(180,34,'Sate Lidah Sapi Empuk Gurih',32000,'Potongan lidah sapi lembut dengan aroma panggangan khas arang batok kelapa.','menu_sate_lidah.jpg'),(181,34,'Kerupuk Kulit Sapi Jangek Kuah',8000,'Kerupuk kulit sapi goreng renyah mekar dicelup kuah sate panas gurih.','menu_jangek_celup.jpg'),(182,35,'Seduhan Kawa Daun Batok Kelapa',12000,'Air seduhan daun kopi sangrai kaya antioksidan disajikan dalam batok kelapa tradisional.','khas_kawadaun.jpg'),(183,35,'Kawa Daun Jahe Hangat Rempah',15000,'Rebusan daun kopi dipadu geprekan jahe merah hangat melegakan tenggorokan.','menu_kawa_jahe.jpg'),(184,35,'Pisang Kapik Kuah Kinca Kelapa',14000,'Pisang bakar pipih manis gurih disiram saus kinca gula aren dan parutan kelapa muda.','menu_pisang_kapik.jpg');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `toko`
--

DROP TABLE IF EXISTS `toko`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `toko` (
  `id_toko` int(11) NOT NULL AUTO_INCREMENT,
  `nama_toko` varchar(100) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `no_wa` varchar(20) DEFAULT NULL,
  `jam_buka` varchar(100) DEFAULT NULL,
  `is_khas` tinyint(1) DEFAULT 0,
  `is_hidden_gem` tinyint(1) DEFAULT 0,
  `visit_count` int(11) DEFAULT 0,
  `whatsapp_views` int(11) DEFAULT 0,
  `id_pemilik` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_toko`),
  KEY `fk_toko_pemilik` (`id_pemilik`),
  CONSTRAINT `fk_toko_pemilik` FOREIGN KEY (`id_pemilik`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `toko`
--

LOCK TABLES `toko` WRITE;
/*!40000 ALTER TABLE `toko` DISABLE KEYS */;
INSERT INTO `toko` VALUES (1,'RM Gonjong Sakato','Jl. Samudera No. 18, Pantai Padang (Taplau)','Rumah Makan','Rumah makan Padang autentik tepi pantai menyajikan rendang daging legendaris, gulai tunjang, dan dendeng batokok renyah dengan pemandangan sunset Pantai Padang.',-0.9324500,100.3541200,'toko1.jpg','6282286908467',NULL,1,0,9,0,NULL),(2,'Sate Mak Syukur Muaro','Jl. Batang Arau No. 42, Kawasan Kota Tua Muaro','Sate','Spesialis sate Padang kuah kuning gurih berempah khas Pariaman dengan potongan daging sapi empuk dan lidah gurih bertabur bawang goreng renyah.',-0.9632000,100.3685000,'toko2.jpg','6282286908467',NULL,1,0,9,2,NULL),(3,'Kopi Nan Batigo','Jl. KH. Ahmad Dahlan No. 12, Dekat Masjid Raya Sumbar','Kopi','Kedai kopi modern bernuansa Minang dengan racikan kopi Robusta Solok Selatan, Kopi Kawa Daun tradisional, dan cemilan pisang kapik kuah kinca.',-0.9238000,100.3615000,'toko3.jpg','6282286908467',NULL,1,0,0,0,NULL),(4,'Oleh-Oleh Sanjai Rangkiang','Jl. Khatib Sulaiman No. 88, Ulak Karang','Oleh-oleh','Sentra oleh-oleh khas Ranah Minang terlengkap mulai dari keripik sanjai balado pedas manis, karak kaliang, drakot, hingga rendang kemasan vakum siap saji.',-0.9082000,100.3589000,'toko4.jpg','6282285417709',NULL,0,0,0,0,NULL),(5,'Es Cendol Pattimura Ranah','Jl. Pattimura No. 25, Padang Barat','Camilan','Minuman segar legendaris cendol tepung beras dengan gula aren asli, santan kelapa kental, tapai singkong, dan serutan es dingin pelepas dahaga.',-0.9486000,100.3592000,'toko5.jpg','6282286908467',NULL,0,0,2,0,NULL),(6,'RM Silinduang Bulan','Jl. Veteran No. 54, Padang Pasir','Rumah Makan','Menyajikan sajian khas gulai kepala kakap bumbu rempah kuning kental, ayam pop minyak kelapa murni, dan sambalado ijo petai selera bangsawan.',-0.9415000,100.3568000,'toko6.jpg','6282286908467',NULL,0,0,4,0,NULL),(7,'Sate Danguang-Danguang Payakumbuh','Jl. Pemuda No. 15, Olo Ladang','Sate','Sate daging dan jeroan sapi bakar kuah rempah kecokelatan manis gurih khas Luak Limopuluah disajikan dengan kerupuk kulit siram kuah.',-0.9508000,100.3562000,'toko7.jpg','6282285417709',NULL,0,1,0,0,NULL),(8,'Kawa Daun Pondok Raso','Jl. Niaga No. 7, Kawasan Pecinan Pondok','Kopi','Sensasi minum seduhan daun kopi kering di dalam wadah tempurung kelapa asli dengan suasana hangat ruko tua heritage Kota Padang.',-0.9587000,100.3654000,'toko8.jpg','6282285417709',NULL,0,1,0,0,NULL),(9,'Kerupuk Jangek & Dakak Minang','Jl. Belakang Olo No. 3, Padang Barat','Oleh-oleh','Produsen tangan pertama kerupuk jangek (kulit sapi) renyah gurih dan dakak-dakak ubi renyah kunyit khas ranah Minang terfavorit para wisatawan.',-0.9452000,100.3621000,'toko9.jpg','6282285417709',NULL,0,1,0,0,NULL),(10,'Sala Lauak & Pisang Goreng Pasia','Jl. Adinegoro No. 88 (Dekat Pasar Lubuk Buaya), Lubuk Buaya, Koto Tangah','Camilan','Camilan khas pesisir ranah Minang di kawasan Lubuk Buaya, menyajikan sala lauak bulat gurih berisi ikan asin dan pisang kapok goreng tepung beras renyah krispi yang nikmat dinikmati selagi hangat.',-0.8372000,100.3285000,'toko10.jpg','6282286908467',NULL,1,0,2,1,NULL),(11,'Soto Padang Garuda Mande','Jl. S. Parman No. 112, Lolong Belanti','Rumah Makan','Warung spesialis Soto Padang autentik legendaris berkaldu sapi jernih gurih dengan suwiran daging sapi renyah kering, bihun, perkedel kentang, dan taburan kerupuk merah khas Minang.',-0.9168000,100.3552000,'toko11.jpg','6282285417709','07.00 - 21.00 WIB',1,0,0,0,NULL),(12,'Dendeng Batokok Ranah Pauh','Jl. Raya Pasar Baru No. 45, Pauh (Dekat Kampus Unand)','Rumah Makan','Spesialis olahan daging sapi khas Minang dengan resep turun temurun. Daging direbus empuk, ditokok pipih bertekstur lembut, lalu disiram racikan cabai hijau mentah segar dan minyak kelapa gurih.',-0.9234000,100.4187000,'toko12.jpg','6282285417709','09.00 - 22.00 WIB',1,1,0,0,NULL),(13,'Dendeng Balado Dapur Gantiang','Jl. Ganting No. 28, Sawahan','Rumah Makan','Menyajikan dendeng balado kering renyah krispi berbalut cabai merah goreng nikmat. Daging sapi pilihan diiris tipis kering dan tahan lama, favorit santap siang warga lokal.',-0.9542000,100.3735000,'toko13.jpg','6282286908467','08.30 - 21.30 WIB',1,0,0,0,NULL),(14,'Pondok Asam Padeh Pasia Nan Tigo','Jl. Adinegoro No. 45, Lubuk Buaya, Koto Tangah','Rumah Makan','Pondok makan favorit di kawasan Lubuk Buaya dengan menu andalan Gulai Asam Padeh Ikan Tongkol dan Kakap segar tanpa santan, berkuah merah pedas asam segar belimbing wuluh.',-0.8345000,100.3298000,'toko14.jpg','6282286908467','10.00 - 20.00 WIB',1,1,0,0,NULL),(15,'Martabak Mesir & Kubang Hayam Wuruk','Jl. Hayam Wuruk No. 19, Padang Barat','Camilan','Kedai kuliner malam legendaris yang menyajikan Martabak Mesir renyah gurih berisi daging sapi giling berbumbu rempah wangi dan daun bawang, lengkap dengan kuah cuka pedas manis.',-0.9575000,100.3548000,'toko15.jpg','6282286908467','16.00 - 23.30 WIB',1,0,0,0,NULL),(16,'Minang Fusion Bistro','Jl. Raden Saleh No. 34, Rimbo Kaluang','Rumah Makan','Bistro kekinian yang memadukan kelezatan bumbu rendang autentik warisan Minang dengan sajian Western modern seperti Burger Rendang dan Lasagna Rendang berlapis keju leleh.',-0.9284000,100.3582000,'toko16.jpg','6282286908467','11.00 - 22.30 WIB',0,1,0,0,NULL),(17,'Tokyo Balado Kitchen','Jl. Ahmad Yani No. 18, Kampung Jao','Rumah Makan','Restoran kuliner fusion Japanese-Minang pertama di Padang. Menyajikan Chicken Katsu krispi dengan lumuran sambal balado merah segar dan Spaghetti pasta balado pedas manis.',-0.9465000,100.3598000,'toko17.jpg','6282285417709','10.00 - 22.00 WIB',0,1,0,0,NULL),(18,'Ranah Pizza & Pasta Oven','Jl. Kis Mangunsarkoro No. 22, Jati Baru','Rumah Makan','Artisan Pizza panggang tungku khas Italia dengan cita rasa lokal Ranah Minang, menghadirkan Pizza Rendang Keju Mozzarella gurih dan camilan Cireng Bumbu Rendang krispi.',-0.9382000,100.3685000,'toko18.jpg','6282286908467','12.00 - 23.00 WIB',0,0,4,1,NULL),(19,'Kedai Kopi Omben & Kopmil Padang','Jl. Bagindo Aziz Chan No. 15, Alang Laweh','Kopi','Tongkrongan hits pemuda Padang pencetus tren Kopi Milo (Kopmil) premium bertabur keju dan boba kenyal, serta Teh Talua modern rasa Green Tea Matcha dan Cokelat lumer.',-0.9521000,100.3662000,'toko19.jpg','6282286908467','08.00 - 00.00 WIB',0,1,0,0,NULL),(20,'Durianto Cafe & Resto','Jl. Pulau Karam No. 88, Pondok','Camilan','Sentra kuliner modern kreasi buah durian dan hidangan khas Padang, terkenal dengan Es Durian Keju Oreo lumer yang creamy serta Nasi Goreng Bumbu Rendang wangi rempah.',-0.9612000,100.3625000,'toko20.jpg','6282285417709','10.00 - 23.00 WIB',0,0,0,0,NULL),(21,'RM Ampera Bungo Minang','Jl. Raya Bungo Pasang No. 14, Bungo Pasang, Koto Tangah','Rumah Makan','Rumah makan ampera khas Minang di jalur Bungo Pasang dengan pilihan lauk pauk segar harian: gulai cincang sapi berempah pekat, ayam bakar bumbu padang, dan ikan nila goreng balado.',-0.8685000,100.3582000,'toko21.jpg','6282285417709','08.00 - 21.30 WIB',1,0,0,0,NULL),(22,'Sate Padang Pariaman Bungo Indah','Jl. Hercules No. 8, Bungo Pasang, Koto Tangah','Sate','Warung sate padang khas Pariaman dengan kuah kuning kental berbumbu jahe, kunyit, dan serai wangi. Daging bakar empuk disajikan di atas daun pisang dengan taburan bawang merah Sumenep.',-0.8712000,100.3545000,'toko22.jpg','6282285417709','16.00 - 23.00 WIB',1,0,0,0,NULL),(23,'Kedai Kopi & Teh Talua Bungo Pasang','Jl. Anak Air No. 25, Bungo Pasang, Koto Tangah','Kopi','Warung kopi tradisional tempat berkumpul warga dan pekerja dengan menu andalan Teh Talua kocok 4 tingkat berbusa tebal, Kopi Robusta seduh saring, dan cemilan lupis ketan saus kinca aren.',-0.8745000,100.3610000,'toko23.jpg','6282286908467','06.30 - 22.00 WIB',0,1,0,0,NULL),(24,'Gorengan Pesisir & Sala Lauak Anak Air','Jl. Anak Air Raya No. 102, Bungo Pasang, Koto Tangah','Camilan','Kedai camilan gorengan hangat pinggir jalan favorit warga Bungo Pasang. Menyajikan Sala Lauak bulat renyah ikan teri asin, bakwan sayur renyah, dan pisang goreng kipas keju.',-0.8698000,100.3635000,'toko24.jpg','6282285417709','13.30 - 21.00 WIB',1,0,0,0,NULL),(25,'RM Simpang Tunggul Sakato','Jl. Simpang Tunggul No. 18, Dadok Tunggul Hitam, Koto Tangah','Rumah Makan','Rumah makan Minang legendaris di pusat simpang Dadok Tunggul Hitam dengan hidangan andalan Gulai Kepala Kakap kuah kuning asam belimbing wuluh dan Ayam Pop minyak kelapa murni.',-0.8842000,100.3654000,'toko25.jpg','6282285417709','09.00 - 21.00 WIB',1,0,0,0,NULL),(26,'Sate Danguang-Danguang Tunggul Hitam','Jl. Garuda No. 33, Dadok Tunggul Hitam, Koto Tangah','Sate','Warung sate daging sapi khas Luak Limopuluah Payakumbuh dengan kuah cokelat rempah gurih lembut bertabur serundeng kelapa sangrai dan keripik singkong balado pedas manis.',-0.8887000,100.3692000,'toko26.jpg','6282285417709','16.30 - 23.30 WIB',1,1,0,0,NULL),(27,'Oleh-Oleh Sanjai & Dakak Tunggul Mande','Jl. Dakota No. 12, Dadok Tunggul Hitam, Koto Tangah','Oleh-oleh','Pusat oleh-oleh camilan khas Minang terfavorit di Dadok Tunggul Hitam: Keripik Sanjai Balado Merah renyah pedas manis, Dakak-dakak ubi bumbu kunyit, dan Karak Kaliang angka delapan.',-0.8821000,100.3638000,'toko27.jpg','6282285417709','08.00 - 21.00 WIB',0,0,0,0,NULL),(28,'Kedai Kopi & Martabak Mini Tunggul Hitam','Jl. Dadok Raya No. 47, Dadok Tunggul Hitam, Koto Tangah','Camilan','Tempat santai sore menikmati Martabak Mesir telur daging sapi cincang renyah, Es Tebak dingin manis bersirup merah, dan kopi tubruk khas Ranah Minang yang nikmat.',-0.8865000,100.3670000,'toko28.jpg','6282286908467','15.00 - 23.00 WIB',0,0,0,0,NULL),(29,'RM Dendeng Batokok Mahasiswa UNP','Jl. Cendrawasih No. 22, Air Tawar Timur, Padang Utara (Dekat Kampus UNP)','Rumah Makan','Warung makan favorit mahasiswa dan dosen di Air Tawar Timur. Menyajikan Dendeng Batokok empuk berlapis ulekan cabai hijau, Rendang daging sapi gurih, dan paket nasi hemat mahasiswa.',-0.8985000,100.3585000,'toko29.jpg','6282286908467','08.00 - 21.00 WIB',1,0,0,0,NULL),(30,'Soto Padang Stasiun Air Tawar','Jl. Prof. Dr. Hamka No. 58, Air Tawar Timur (Dekat Stasiun Kereta Api Air Tawar)','Rumah Makan','Soto Padang legendaris tepat di seberang stasiun Air Tawar. Menyajikan kuah kaldu sapi bening rempah kapulaga, daging sapi goreng kering renyah krispi, perkedel kentang, dan bihun lembut.',-0.9022000,100.3562000,'toko30.jpg','6282286908467','07.00 - 21.00 WIB',1,0,0,0,NULL),(31,'Kopmil & Cafe Kampus Air Tawar','Jl. Merpati No. 16, Air Tawar Timur, Padang Utara','Kopi','Cafe kekinian tempat nongkrong dan nugas mahasiswa di Air Tawar Timur. Terkenal dengan kreasi Kopi Milo (Kopmil) Padang, Teh Talua Green Tea kekinian, dan camilan kentang balado keju.',-0.8998000,100.3615000,'toko31.jpg','6282285417709','09.00 - 23.30 WIB',0,1,0,0,NULL),(32,'Camilan Tradisional Bika & Lupis Air Tawar','Jl. Belibis No. 9, Air Tawar Timur, Padang Utara','Camilan','Pondok kue tradisional Minang hangat di kawasan Air Tawar Timur, menyajikan Bika bakar kelapa manis harum daun pisang, Lupis ketan saus gula merah aren, dan kolak pisang ubi.',-0.9038000,100.3592000,'toko32.jpg','6282286908467','07.00 - 18.30 WIB',1,0,0,0,NULL),(33,'RM Balai Kota Aia Pacah','Jl. Bypass Km 15 No. 8, Aia Pacah, Koto Tangah (Dekat Kantor Balai Kota Padang)','Rumah Makan','Rumah makan Minang representatif di koridor Bypass Aia Pacah dekat Balai Kota Padang. Menyajikan hidangan bangsawan Minang: Gulai Tunjang kikil lembut, Rendang sapi empuk, dan Ayam Gulai Kurma.',-0.8652000,100.3885000,'toko33.jpg','6282286908467','08.30 - 21.30 WIB',1,0,0,0,NULL),(34,'Sate Padang Mak Syukur Bypass Aia Pacah','Jl. Bypass Aia Pacah No. 42, Aia Pacah, Koto Tangah','Sate','Pilihan persinggahan kuliner sate Padang bagi para pengendara jalur lintas Bypass. Menyajikan Sate Daging Sapi bakar empuk berlumur kuah rempah kuning gurih kental dan taburan bawang goreng renyah.',-0.8690000,100.3920000,'toko34.jpg','6282286908467','11.00 - 22.30 WIB',1,0,0,0,NULL),(35,'Kedai Kawa Daun & Camilan Aia Pacah','Jl. Maransi Raya No. 19, Aia Pacah, Koto Tangah','Kopi','Kedai tradisional bernuansa asri pedesaan di Aia Pacah. Menyajikan seduhan daun kopi kering (Kawa Daun) dalam tempurung kelapa asli, ketan sarikaya gula merah, dan pisang kapik kinca.',-0.8625000,100.3840000,'toko35.jpg','6282286908467','08.00 - 22.00 WIB',1,1,0,0,NULL);
/*!40000 ALTER TABLE `toko` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ulasan`
--

DROP TABLE IF EXISTS `ulasan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ulasan` (
  `id_ulasan` int(11) NOT NULL AUTO_INCREMENT,
  `id_toko` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  `komentar` text DEFAULT NULL,
  `balasan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `replied_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_ulasan`),
  UNIQUE KEY `uq_ulasan_toko_user` (`id_toko`,`id_user`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`) ON DELETE CASCADE,
  CONSTRAINT `ulasan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `chk_ulasan_rating` CHECK (`rating` between 1 and 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ulasan`
--

LOCK TABLES `ulasan` WRITE;
/*!40000 ALTER TABLE `ulasan` DISABLE KEYS */;
/*!40000 ALTER TABLE `ulasan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pemilik','user') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa','admin'),(2,'user','$2y$10$tZg44Xf8v7M0w.q8A9zVreR3zU9m7mR4mY7hL2cK9mN8bV5cV6c0.','user');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-16 19:23:14
