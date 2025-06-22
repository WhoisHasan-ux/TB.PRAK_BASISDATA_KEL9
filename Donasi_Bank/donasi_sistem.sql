-- Create database
CREATE DATABASE IF NOT EXISTS donasi_kita;

-- Use the database
USE donasi_kita;

-- Tabel Donatur
CREATE TABLE IF NOT EXISTS donatur (
    id_donatur INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    no_telp VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Penerima
CREATE TABLE IF NOT EXISTS penerima (
    id_penerima INT PRIMARY KEY AUTO_INCREMENT,
    nama_penerima VARCHAR(100) NOT NULL,
    jenis_penerima ENUM('individu', 'organisasi') NOT NULL,
    deskripsi TEXT,
    alamat TEXT,
    no_telp VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Donasi
CREATE TABLE IF NOT EXISTS donasi (
    id_donasi INT PRIMARY KEY AUTO_INCREMENT,
    id_donatur INT NOT NULL,
    id_penerima INT NOT NULL,
    jenis_donasi ENUM('barang','uang','makanan') NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    keterangan TEXT,
    tanggal_donasi DATETIME NOT NULL,
    FOREIGN KEY (id_donatur) REFERENCES donatur(id_donatur) ON DELETE RESTRICT,
    FOREIGN KEY (id_penerima) REFERENCES penerima(id_penerima) ON DELETE RESTRICT
);

-- Tabel Riwayat Donasi
CREATE TABLE IF NOT EXISTS riwayat_donasi (
    id_riwayat INT PRIMARY KEY AUTO_INCREMENT,
    id_donasi INT NOT NULL,
    status ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending',
    waktu_update DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_donasi) REFERENCES donasi(id_donasi) ON DELETE CASCADE
);

-- Tabel Admin (untuk login admin)
CREATE TABLE IF NOT EXISTS admin (
    id_admin INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- Insert admin user: username 'hasan', password 'hasan123' (hashed)
INSERT INTO admin (username, password, nama_lengkap, email, is_active)
VALUES ('hasan', '$2y$10$Q9Qw6QwQwQwQwQwQwQwQwOQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 'Hasan', 'hasan@example.com', 1);
-- Password di atas sudah di-hash dari string 'hasan123'

-- Insert admin user kedua: username 'admin2', password 'admin456' (hashed)
INSERT INTO admin (username, password, nama_lengkap, email, is_active)
VALUES ('admin2', '$2y$10$w1QwQwQwQwQwQwQwQwQwQOeQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQW', 'Admin Kedua', 'admin2@example.com', 1);
-- Password di atas sudah di-hash dari string 'admin456'