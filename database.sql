
-- DATABASE LAUNDRY NIKI
-- Import langsung ke database yang dipilih di phpMyAdmin

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','karyawan','pelanggan') NOT NULL DEFAULT 'pelanggan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE layanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_layanan VARCHAR(50) NOT NULL,
  tarif_per_kg INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  layanan_id INT NOT NULL,
  berat_perkiraan DECIMAL(6,1) NOT NULL,
  berat_final DECIMAL(6,1) NULL,
  total_biaya INT NULL,
  catatan TEXT,
  pakaian_khusus VARCHAR(255) NULL,
  status ENUM(
    'menunggu',
    'diterima',
    'dicuci',
    'disetrika',
    'siap diambil',
    'selesai'
  ) NOT NULL DEFAULT 'menunggu',
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (layanan_id) REFERENCES layanan(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DATA LAYANAN
INSERT INTO layanan (nama_layanan, tarif_per_kg) VALUES
('Cuci Kering', 5000),
('Cuci Setrika', 7000),
('Setrika Saja', 4000);

-- AKUN ADMIN
-- Email: admin@laundry.com
-- Password: admin123
INSERT INTO users (nama, email, password, role) VALUES
('Admin Laundry Ni-Ki', 'admin@laundry.com',
'$2y$12$aBAdQthjwQWNcFl8xqRdqOAcd4PZRJ550QPnC9nuhjUBQ0ujJHXAW', 'admin');

-- AKUN KARYAWAN
-- Email: karyawan@laundry.com
-- Password: karyawan123
INSERT INTO users (nama, email, password, role) VALUES
('Karyawan Laundry Ni-Ki', 'karyawan@laundry.com',
'$2y$12$cEs4aw27YSUZiv3WYmCtauGQT0R4y.jl5PJUChqTEGgHPuPtEynE2', 'karyawan');
