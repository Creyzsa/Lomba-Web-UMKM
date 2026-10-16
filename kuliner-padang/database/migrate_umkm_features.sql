USE db_kuliner_padang;

ALTER TABLE users
  MODIFY role ENUM('admin','pemilik','user') NOT NULL DEFAULT 'user';

UPDATE users
SET role = 'pemilik'
WHERE role = '';

ALTER TABLE toko
  ADD COLUMN IF NOT EXISTS no_wa VARCHAR(20) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS jam_buka VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS is_khas TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS is_hidden_gem TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS visit_count INT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS whatsapp_views INT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS id_pemilik INT DEFAULT NULL;

SET @fk_pemilik_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'toko'
    AND CONSTRAINT_NAME = 'fk_toko_pemilik'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @fk_pemilik_sql = IF(
  @fk_pemilik_exists = 0,
  'ALTER TABLE toko ADD CONSTRAINT fk_toko_pemilik FOREIGN KEY (id_pemilik) REFERENCES users(id_user) ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE fk_pemilik_stmt FROM @fk_pemilik_sql;
EXECUTE fk_pemilik_stmt;
DEALLOCATE PREPARE fk_pemilik_stmt;

CREATE TABLE IF NOT EXISTS ulasan (
  id_ulasan INT AUTO_INCREMENT PRIMARY KEY,
  id_toko INT NOT NULL,
  id_user INT NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,
  komentar TEXT,
  balasan TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  replied_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY uq_ulasan_toko_user (id_toko, id_user),
  CONSTRAINT chk_ulasan_rating CHECK (rating BETWEEN 1 AND 5),
  FOREIGN KEY (id_toko) REFERENCES toko(id_toko) ON DELETE CASCADE,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS makanan_khas (
  id_makanan INT AUTO_INCREMENT PRIMARY KEY,
  nama_makanan VARCHAR(100) NOT NULL,
  asal_daerah VARCHAR(100) DEFAULT NULL,
  cerita TEXT,
  foto VARCHAR(255) DEFAULT NULL,
  id_toko INT DEFAULT NULL,
  FOREIGN KEY (id_toko) REFERENCES toko(id_toko) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

UPDATE toko SET is_khas = 1
WHERE nama_toko IN ('RM Gonjong Sakato', 'Sate Mak Syukur Muaro', 'Kopi Nan Batigo', 'Sala Lauak & Pisang Goreng Pasia');

UPDATE toko SET is_hidden_gem = 1
WHERE nama_toko IN ('Sate Danguang-Danguang Payakumbuh', 'Kawa Daun Pondok Raso', 'Kerupuk Jangek & Dakak Minang');

INSERT INTO makanan_khas (nama_makanan, asal_daerah, cerita, id_toko)
SELECT 'Rendang', 'Minangkabau', 'Rendang dimasak perlahan bersama santan dan rempah hingga bumbunya meresap dan mengering. Proses panjang ini menjadikannya sajian penting dalam jamuan dan perayaan keluarga Minang.',
       (SELECT id_toko FROM toko WHERE nama_toko = 'RM Gonjong Sakato' ORDER BY id_toko LIMIT 1)
WHERE NOT EXISTS (SELECT 1 FROM makanan_khas WHERE nama_makanan = 'Rendang');

INSERT INTO makanan_khas (nama_makanan, asal_daerah, cerita, id_toko)
SELECT 'Sate Padang', 'Padang dan Pariaman', 'Potongan daging atau jeroan dibakar lalu disajikan dengan kuah kental berbumbu. Ragam daerah menghadirkan kuah dengan warna dan racikan rempah yang berbeda.',
       (SELECT id_toko FROM toko WHERE nama_toko = 'Sate Mak Syukur Muaro' ORDER BY id_toko LIMIT 1)
WHERE NOT EXISTS (SELECT 1 FROM makanan_khas WHERE nama_makanan = 'Sate Padang');

INSERT INTO makanan_khas (nama_makanan, asal_daerah, cerita, id_toko)
SELECT 'Kawa Daun', 'Dataran Tinggi Minangkabau', 'Minuman tradisional ini dibuat dari daun kopi yang dikeringkan dan diseduh. Di sejumlah tempat, kawa daun disajikan dalam tempurung kelapa dengan cita rasa ringan dan beraroma.',
       (SELECT id_toko FROM toko WHERE nama_toko = 'Kawa Daun Pondok Raso' ORDER BY id_toko LIMIT 1)
WHERE NOT EXISTS (SELECT 1 FROM makanan_khas WHERE nama_makanan = 'Kawa Daun');

INSERT INTO makanan_khas (nama_makanan, asal_daerah, cerita, id_toko)
SELECT 'Sala Lauak', 'Pesisir Pariaman', 'Gorengan berbentuk bola dari adonan tepung beras berbumbu dan ikan. Sala lauak umum dijumpai sebagai kudapan hangat di kawasan pesisir Sumatera Barat.',
       (SELECT id_toko FROM toko WHERE nama_toko = 'Sala Lauak & Pisang Goreng Pasia' ORDER BY id_toko LIMIT 1)
WHERE NOT EXISTS (SELECT 1 FROM makanan_khas WHERE nama_makanan = 'Sala Lauak');

INSERT INTO makanan_khas (nama_makanan, asal_daerah, cerita, id_toko)
SELECT 'Keripik Sanjai', 'Bukittinggi', 'Keripik berbahan singkong ini dikenal dengan pilihan bumbu gurih hingga balado. Sanjai menjadi salah satu oleh-oleh yang lekat dengan kawasan Bukittinggi dan sekitarnya.',
       (SELECT id_toko FROM toko WHERE nama_toko = 'Oleh-Oleh Sanjai Rangkiang' ORDER BY id_toko LIMIT 1)
WHERE NOT EXISTS (SELECT 1 FROM makanan_khas WHERE nama_makanan = 'Keripik Sanjai');