-- Database TechHub (sesuai kolom yang dipakai pages/home/index.php)
CREATE DATABASE IF NOT EXISTS db_techhub CHARACTER SET utf8mb4;
USE db_techhub;

CREATE TABLE IF NOT EXISTS categories (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(100) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT DEFAULT NULL,
    title       VARCHAR(150) NOT NULL,
    price       INT NOT NULL,
    rating      DECIMAL(2,1) DEFAULT 5.0,
    image       VARCHAR(150) DEFAULT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO categories (name, icon) VALUES
('CPU','cat-cpu.png'),('Motherboard','cat-motherboard.png'),('RAM','cat-ram.png'),
('SSD','cat-ssd.png'),('HDD','cat-hdd.png'),('GPU','cat-gpu.png'),
('PSU','cat-psu.png'),('Monitor','cat-monitor.png');

-- Urutan = urutan tampil Featured Product di desain
INSERT INTO products (category_id, title, price, rating, image) VALUES
(7,'POWER SUPPLY MSI MAG A650BN',880000,5.0,'psu-msi.png'),
(2,'MSI PRO B650-S WIFI Mainboard',2650000,4.8,'motherboard.webp'),
(8,'Lenovo Legion 27 Q-2C Monitor 67b',3159000,4.5,'monitor-legion.png'),
(1,'AMD Ryzen 7 7700X',6510000,4.8,'AMD ryzen 7.webp'),
(4,'V-GeN SSD Platinum SATA III',543900,4.9,'SSD.webp');

-- Tabel admin (Halaman Edit Profile)
CREATE TABLE IF NOT EXISTS users (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username  VARCHAR(50)  NOT NULL UNIQUE,
    email     VARCHAR(100) NOT NULL UNIQUE,
    phone     VARCHAR(20)  DEFAULT NULL,
    address   VARCHAR(200) DEFAULT NULL,
    bio       TEXT         DEFAULT NULL,
    photo     VARCHAR(150) DEFAULT NULL,
    role      VARCHAR(20)  DEFAULT 'admin'
);

INSERT INTO users (full_name, username, email, phone, address, bio) VALUES
('Lenpit TechHub','Lenpit','lenpit@gmail.com','08123456789','Jl. Harapan Palsu No.123','System administrator at TechHub.');

-- Tambahan produk agar rekomendasi lebih bervariasi (gambar sudah ada di asset/images)
INSERT INTO products (category_id, title, price, rating, image) VALUES
(1,'Intel Core Ultra 5 245K',5199000,4.8,'Intel ulra 5.webp'),
(1,'AMD Ryzen 5 5500',1699000,4.7,'AMD ryzen 5 5500.webp'),
(1,'AMD Ryzen 3 4100',1099000,4.5,'AMD ryazen 3.jpg'),
(1,'Intel Core i3-10100F',1050000,4.6,'intel i3 gen 10.webp'),
(1,'Intel Core i7-13700K',6850000,4.8,'Intel i7 gen 13.jpg'),
(1,'Intel Core i9-14900K',9500000,4.9,'Intel i9.webp'),
(1,'AMD Ryzen 9 9900X',10990000,4.9,'amd ryzen 9 9000.webp'),
(1,'AMD Athlon 3000G',780000,4.3,'AMD atlon.webp'),
(3,'RAM DDR4 16GB (2x8GB) 3200MHz',850000,4.7,'Ram.png');
