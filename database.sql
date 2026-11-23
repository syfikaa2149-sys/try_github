-- =====================================================================
-- database.sql — skema database untuk form-login (database: latihan_santri)
-- Pilih SALAH SATU skenario di bawah. Jangan menjalankan semuanya sekaligus.
-- =====================================================================

-- SKENARIO A: JIKA KAMU BELUM PUNYA DATABASE ATAU TABEL users
CREATE DATABASE IF NOT EXISTS latihan_santri
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE latihan_santri;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('santri', 'admin') NOT NULL DEFAULT 'santri',
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- SKENARIO B: JIKA TABEL users SUDAH ADA TAPI BELUM PUNYA KOLOM role
-- Jalankan perintah ALTER di bawah ini pada tab SQL database latihan_santri:
-- ALTER TABLE users
--     ADD COLUMN role ENUM('santri', 'admin') NOT NULL DEFAULT 'santri' AFTER password;

-- SKENARIO C: CARA MENGUBAH AKUN SANTRI MENJADI ADMIN (Lewat phpMyAdmin)
-- UPDATE users SET role = 'admin' WHERE username = 'nama_akun_uji';
-- SELECT id, username, role FROM users;
