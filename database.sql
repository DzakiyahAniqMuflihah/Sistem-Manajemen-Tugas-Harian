-- ============================================
-- DATABASE: db_taskmanager
-- Sistem Manajemen Tugas Harian
-- ============================================

CREATE DATABASE IF NOT EXISTS db_taskmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_taskmanager;

-- Tabel 1: users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel 2: tugas
CREATE TABLE tugas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    kategori ENUM('belajar','pekerjaan','pribadi','kesehatan','lainnya') DEFAULT 'lainnya',
    prioritas ENUM('tinggi','sedang','rendah') DEFAULT 'sedang',
    status ENUM('belum','proses','selesai') DEFAULT 'belum',
    deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Data awal: admin
INSERT INTO users (nama, username, password, role) VALUES
('Administrator', 'admin', MD5('admin123'), 'admin'),
('Budi Santoso', 'budi', MD5('budi123'), 'user'),
('Siti Rahayu', 'siti', MD5('siti123'), 'user');

-- Data awal: tugas
INSERT INTO tugas (user_id, judul, deskripsi, kategori, prioritas, status, deadline) VALUES
(2, 'Review materi pemrograman web', 'Baca ulang materi PHP dan MySQL bab 5-7', 'belajar', 'tinggi', 'proses', CURDATE()),
(2, 'Kerjakan laporan mingguan', 'Laporan progress project tugas besar', 'pekerjaan', 'tinggi', 'belum', DATE_ADD(CURDATE(), INTERVAL 2 DAY)),
(2, 'Olahraga 30 menit', 'Jogging pagi di sekitar rumah', 'kesehatan', 'rendah', 'selesai', CURDATE()),
(3, 'Baca buku pemrograman', 'Clean Code oleh Robert C. Martin', 'belajar', 'sedang', 'belum', DATE_ADD(CURDATE(), INTERVAL 5 DAY)),
(3, 'Persiapan presentasi', 'Slide untuk mata kuliah rekayasa perangkat lunak', 'pekerjaan', 'tinggi', 'proses', DATE_ADD(CURDATE(), INTERVAL 1 DAY));
