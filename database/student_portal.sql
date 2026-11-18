-- =========================================
-- DATABASE STUDENT PORTAL
-- =========================================

CREATE DATABASE IF NOT EXISTS student_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE student_portal;

-- =========================================
-- TABEL ADMIN
-- =========================================

CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE = InnoDB;

-- =========================================
-- TABEL PROGRAM STUDI
-- =========================================

CREATE TABLE IF NOT EXISTS program_studi (
    id_prodi INT AUTO_INCREMENT PRIMARY KEY,
    kd_prodi VARCHAR(10) NOT NULL UNIQUE,
    nama_prodi VARCHAR(100) NOT NULL
) ENGINE = InnoDB;

-- =========================================
-- TABEL MAHASISWA
-- =========================================

CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    id_prodi INT NOT NULL,

-- Nilai akademik
nilai_tugas DECIMAL(5, 2) NOT NULL DEFAULT 0,
nilai_uts DECIMAL(5, 2) NOT NULL DEFAULT 0,
nilai_uas DECIMAL(5, 2) NOT NULL DEFAULT 0,
kehadiran DECIMAL(5, 2) NOT NULL DEFAULT 0,

-- Relasi ke tabel program studi
CONSTRAINT fk_mahasiswa_prodi
        FOREIGN KEY (id_prodi)
        REFERENCES program_studi(id_prodi)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB;