-- Database Schema for Sistem Manajemen Surat & Arsip Digital Instansi

-- Tabel roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    avatar VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- Tabel surat_masuk
CREATE TABLE surat_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(100) NOT NULL UNIQUE,
    tanggal_surat DATE NOT NULL,
    tanggal_diterima DATE NOT NULL,
    pengirim VARCHAR(255) NOT NULL,
    perihal VARCHAR(500) NOT NULL,
    klasifikasi ENUM('rahasia', 'penting', 'umum') DEFAULT 'umum',
    status ENUM('baru', 'diproses', 'selesai') DEFAULT 'baru',
    file_surat VARCHAR(500),
    catatan TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nomor_surat (nomor_surat),
    INDEX idx_tanggal_surat (tanggal_surat),
    INDEX idx_pengirim (pengirim),
    INDEX idx_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Tabel surat_keluar
CREATE TABLE surat_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(100) NOT NULL UNIQUE,
    tanggal_surat DATE NOT NULL,
    tujuan VARCHAR(255) NOT NULL,
    perihal VARCHAR(500) NOT NULL,
    klasifikasi ENUM('rahasia', 'penting', 'umum') DEFAULT 'umum',
    tembusan TEXT,
    penandatangan VARCHAR(255) NOT NULL,
    file_surat VARCHAR(500),
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nomor_surat (nomor_surat),
    INDEX idx_tanggal_surat (tanggal_surat),
    INDEX idx_tujuan (tujuan),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Tabel disposisi
CREATE TABLE disposisi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surat_masuk_id INT NOT NULL,
    dari_user_id INT NOT NULL,
    kepada_user_id INT NOT NULL,
    catatan TEXT,
    dibaca BOOLEAN DEFAULT FALSE,
    tanggal_disposisi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_surat_masuk_id (surat_masuk_id),
    INDEX idx_kepada_user_id (kepada_user_id),
    FOREIGN KEY (surat_masuk_id) REFERENCES surat_masuk(id) ON DELETE CASCADE,
    FOREIGN KEY (dari_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (kepada_user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Tabel laporan
CREATE TABLE laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    jenis ENUM('surat_masuk', 'surat_keluar') NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    data_laporan JSON,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Insert data awal untuk roles
INSERT INTO roles (id, name, description) VALUES
(1, 'admin', 'Administrator sistem'),
(2, 'pimpinan', 'Pimpinan instansi'),
(3, 'staff', 'Staff administrasi');