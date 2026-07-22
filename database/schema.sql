-- ============================================================
-- Skema Database: Aplikasi Pesan Rahasia (mirip Secreto/NGL)
-- ============================================================
-- Cara pakai:
--   1. Buka phpMyAdmin (XAMPP/Laragon) atau mysql CLI
--   2. Import file ini, atau jalankan: mysql -u root -p < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS rahasia_app
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE rahasia_app;

-- Tabel akun (hanya dibuat lewat Login Google)
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(30)  UNIQUE,           -- NULL sementara sebelum user pilih username
  email         VARCHAR(190) NOT NULL UNIQUE,
  google_id     VARCHAR(64)  NOT NULL UNIQUE,
  display_name  VARCHAR(100),
  avatar_url    VARCHAR(500),
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel pesan rahasia (siapa pun bisa insert tanpa login)
CREATE TABLE IF NOT EXISTS messages (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  receiver_id   INT NOT NULL,
  content       TEXT NOT NULL,
  sender_hash   VARCHAR(64),                   -- untuk anti-spam ringan, BUKAN identitas
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  is_hidden     TINYINT(1) NOT NULL DEFAULT 0, -- 1 = disembunyikan dari publik, tetap terlihat oleh pemilik akun
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_receiver (receiver_id)
) ENGINE=InnoDB;

-- Tabel balasan (satu balasan per pesan, hanya pemilik akun yang boleh insert)
CREATE TABLE IF NOT EXISTS replies (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  message_id    INT NOT NULL UNIQUE,            -- UNIQUE = 1 pesan hanya boleh 1 balasan
  content       TEXT NOT NULL,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE
) ENGINE=InnoDB;
