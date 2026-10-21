-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 21, 2026 at 06:36 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `id_detail` int(11) NOT NULL,
  `id_penjualan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`id_detail`, `id_penjualan`, `id_produk`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 2, 150000.00),
(2, 2, 1, 1, 75000.00),
(3, 2, 6, 3, 10500.00),
(4, 2, 2, 2, 36000.00),
(5, 3, 6, 2, 7000.00),
(6, 4, 6, 2, 7000.00),
(7, 5, 9, 2, 38000.00),
(8, 6, 1, 1, 75000.00),
(9, 6, 4, 1, 29000.00),
(10, 6, 6, 1, 3500.00),
(11, 7, 1, 1, 75000.00),
(12, 7, 2, 1, 18000.00),
(13, 7, 3, 1, 17000.00),
(14, 7, 4, 1, 29000.00),
(15, 7, 6, 1, 3500.00),
(16, 7, 10, 1, 5000.00),
(17, 8, 2, 1, 18000.00),
(18, 8, 9, 1, 19000.00),
(19, 8, 12, 1, 5000.00),
(20, 9, 1, 5, 375000.00),
(21, 9, 2, 5, 90000.00),
(22, 9, 3, 5, 85000.00),
(23, 9, 4, 5, 145000.00),
(24, 9, 6, 25, 87500.00),
(25, 9, 7, 5, 10000.00),
(26, 9, 8, 5, 50000.00),
(27, 10, 14, 1, 18000.00),
(28, 11, 4, 50, 1450000.00),
(29, 12, 1, 3, 225000.00),
(30, 12, 4, 1, 29000.00),
(31, 13, 1, 1, 75000.00),
(32, 13, 2, 1, 18000.00),
(33, 13, 4, 1, 29000.00),
(34, 13, 6, 1, 3500.00),
(35, 14, 1, 2, 150000.00),
(36, 14, 4, 1, 29000.00),
(37, 14, 6, 1, 3500.00),
(38, 14, 7, 1, 2000.00),
(39, 14, 9, 1, 19000.00);

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `waktu` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu`) VALUES
(1, 1, 'Logout dari sistem', '2026-10-04 00:36:36'),
(2, 1, 'Login ke sistem', '2026-10-04 00:36:51'),
(3, 1, 'Login ke sistem', '2026-10-04 15:56:32'),
(4, 1, 'Menambahkan pelanggan: alex', '2026-10-04 15:57:28'),
(5, 1, 'Mengedit data pelanggan: Khirana', '2026-10-04 17:18:11'),
(6, 1, 'Mengedit data pelanggan: Khirana', '2026-10-04 17:18:22'),
(7, 1, 'Logout dari sistem', '2026-10-04 17:18:24'),
(8, 1, 'Login ke sistem', '2026-10-04 17:18:35'),
(9, 1, 'Menghapus pelanggan: alex', '2026-10-04 17:28:38'),
(10, 1, 'Login ke sistem', '2026-10-06 21:00:15'),
(11, 1, 'Mengedit data produk: Garam 500 Gram', '2026-10-06 21:58:25'),
(12, 1, 'Mengedit data produk: Sabun Mandi', '2026-10-06 22:03:45'),
(13, 1, 'Menambahkan produk: Teh Botol', '2026-10-06 22:14:27'),
(14, 1, 'Menghapus produk: Teh Botol', '2026-10-06 22:27:49'),
(15, 1, 'Login ke sistem', '2026-10-11 21:08:05'),
(16, 1, 'Logout dari sistem', '2026-10-11 21:18:12'),
(17, 1, 'Login ke sistem', '2026-10-11 21:18:57'),
(18, 1, 'Membuat transaksi #14 - Pelanggan: Riski', '2026-10-11 21:21:32'),
(19, 1, 'Menyelesaikan transaksi #14 - Pelanggan: Riski', '2026-10-11 21:21:45');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `alamat` varchar(200) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `alamat`, `no_telepon`) VALUES
(1, 'Nabila', 'Kendal', '081234567890'),
(5, 'Riski', 'Semarang', '085670079009'),
(6, 'Abel', 'Brayo', '08562740103'),
(7, 'Syavira', 'Campurjo', '0899786909808'),
(8, 'Lingling', 'Candi Golf', '085640439009'),
(9, 'Meiling', 'BSB Ivy Park', '085640061269'),
(10, 'Huifen', 'Perum Greenwich Golf, Candi Golf, Semarang', '089658790890'),
(12, 'Khirana', 'Semarang', '08765433999');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id_penjualan` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `tanggal_penjualan` date NOT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id_penjualan`, `id_pelanggan`, `tanggal_penjualan`, `total`) VALUES
(1, 1, '2026-09-20', 150000.00),
(2, 1, '2026-09-20', 121500.00),
(3, 1, '2026-09-20', 7000.00),
(4, 5, '2026-09-20', 7000.00),
(5, 6, '2026-09-23', 38000.00),
(6, 5, '2026-09-23', 107500.00),
(7, 5, '2026-09-23', 147500.00),
(8, 7, '2026-09-23', 42000.00),
(9, 8, '2026-09-24', 842500.00),
(10, 5, '2026-09-24', 18000.00),
(11, 10, '2026-09-24', 1450000.00),
(12, 12, '2026-09-30', 254000.00),
(13, 1, '2026-10-03', 125500.00),
(14, 5, '2026-10-11', 203500.00);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `harga`, `stok`) VALUES
(1, 'Beras 5 Kg', 75000.00, 98),
(2, 'Minyak Goreng 1 Liter', 18000.00, 50),
(3, 'Gula Pasir 1 Kg', 17000.00, 100),
(4, 'Telur Ayam 1 Kg', 29000.00, 49),
(6, 'Mie Instan', 3500.00, 99),
(7, 'Kopi Sachet', 2000.00, 49),
(8, 'Teh Celup', 10000.00, 100),
(9, 'Susu UHT 1 Liter', 19000.00, 99),
(10, 'Garam 500 Gram', 10000.00, 50),
(12, 'Sabun Mandi', 5000.00, 100),
(13, 'Deterjen', 15000.00, 100),
(14, 'Gas 3 Kg', 18000.00, 50);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_penjualan` int(11) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL DEFAULT current_timestamp(),
  `total_transaksi` decimal(12,2) NOT NULL DEFAULT 0.00,
  `uang_bayar` decimal(12,2) NOT NULL DEFAULT 0.00,
  `uang_kembali` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status_transaksi` enum('selesai','pending','batal') NOT NULL DEFAULT 'selesai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_penjualan`, `tanggal_transaksi`, `total_transaksi`, `uang_bayar`, `uang_kembali`, `status_transaksi`) VALUES
(1, 1, '2026-09-20 18:55:02', 150000.00, 200000.00, 50000.00, 'selesai'),
(2, 2, '2026-09-20 19:14:19', 121500.00, 121500.00, 0.00, 'selesai'),
(3, 3, '2026-09-20 19:37:31', 7000.00, 10000.00, 3000.00, 'selesai'),
(4, 4, '2026-09-20 19:38:38', 7000.00, 10000.00, 3000.00, 'selesai'),
(5, 5, '2026-09-23 12:46:15', 38000.00, 40000.00, 2000.00, 'selesai'),
(6, 6, '2026-09-23 12:54:36', 107500.00, 200000.00, 92500.00, 'selesai'),
(7, 7, '2026-09-23 13:46:24', 147500.00, 150000.00, 2500.00, 'selesai'),
(8, 8, '2026-09-23 13:54:21', 42000.00, 50000.00, 8000.00, 'selesai'),
(9, 9, '2026-09-24 07:53:58', 842500.00, 850000.00, 7500.00, 'selesai'),
(10, 10, '2026-09-24 08:07:29', 18000.00, 20000.00, 2000.00, 'selesai'),
(11, 11, '2026-09-24 08:14:31', 1450000.00, 1450000.00, 0.00, 'selesai'),
(12, 12, '2026-09-30 10:40:09', 254000.00, 300000.00, 46000.00, 'selesai'),
(13, 13, '2026-10-03 21:21:22', 125500.00, 125500.00, 0.00, 'selesai'),
(14, 14, '2026-10-11 21:21:32', 203500.00, 205000.00, 1500.00, 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_user`, `username`, `password`) VALUES
(1, 'Administrator', 'admin', 'admin123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `idx_id_penjualan` (`id_penjualan`),
  ADD KEY `idx_id_produk` (`id_produk`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD KEY `idx_nama_pelanggan` (`nama_pelanggan`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id_penjualan`),
  ADD KEY `idx_id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `idx_nama_produk` (`nama_produk`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `idx_id_penjualan_transaksi` (`id_penjualan`),
  ADD KEY `idx_tanggal_transaksi` (`tanggal_transaksi`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id_penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `fk_detail_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id_penjualan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `fk_penjualan_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id_penjualan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
