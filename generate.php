-- 1. Buat database db_toko
CREATE DATABASE IF NOT EXISTS db_toko;

-- 2. Gunakan database tersebut
USE db_toko;

-- 3. Buat tabel produk untuk menyimpan data barang
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    stok INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Buat tabel user untuk sistem login
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- 5. Masukkan akun admin default (Username: admin, Password: admin123)
INSERT INTO user (username, password) VALUES ('admin', 'admin123'); 