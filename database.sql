-- Database & tabel untuk Sistem Informasi Data Mahasiswa
CREATE DATABASE IF NOT EXISTS db_akademik
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE db_akademik;

CREATE TABLE IF NOT EXISTS mahasiswa (
  nim           VARCHAR(20)  NOT NULL,
  nama          VARCHAR(100) NOT NULL,
  program_studi VARCHAR(50)  NOT NULL,
  alamat        TEXT         NOT NULL,
  PRIMARY KEY (nim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (Opsional) data contoh
INSERT INTO mahasiswa (nim, nama, program_studi, alamat) VALUES
('2201001', 'Ayu Lestari',  'Teknik Informatika', 'Jl. Kartini No. 12, Bandar Lampung'),
('2201002', 'Bima Saputra', 'Sistem Informasi',   'Jl. Sudirman No. 45, Metro'),
('2201003', 'Citra Dewi',   'Manajemen',          'Jl. Pramuka No. 8, Bandar Lampung');
