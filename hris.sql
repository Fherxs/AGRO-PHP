-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 05:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hris`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpha') DEFAULT 'Hadir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `karyawan_id`, `tanggal`, `status`) VALUES
(1, 2, '2025-10-20', 'Hadir'),
(2, 3, '2025-10-20', 'Hadir'),
(3, 4, '2025-10-20', 'Hadir'),
(4, 2, '2025-10-21', 'Hadir'),
(5, 3, '2025-10-21', 'Izin'),
(6, 4, '2025-10-21', 'Hadir'),
(7, 2, '2025-10-22', 'Hadir'),
(8, 3, '2025-10-22', 'Hadir'),
(9, 4, '2025-10-22', 'Hadir'),
(10, 5, '2025-10-22', 'Hadir'),
(11, 12, '2025-10-22', 'Alpha'),
(12, 15, '2025-10-22', 'Hadir'),
(13, 16, '2025-10-22', 'Hadir');

-- --------------------------------------------------------

--
-- Table structure for table `anggaran`
--

CREATE TABLE `anggaran` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tgl_pengajuan` date NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `nominal` bigint(20) NOT NULL,
  `status` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggaran`
--

INSERT INTO `anggaran` (`id`, `karyawan_id`, `tgl_pengajuan`, `keterangan`, `nominal`, `status`) VALUES
(8, 5, '2025-10-22', 'membeli produk untuk kecantikan', 100, 'Disetujui'),
(9, 5, '2025-10-22', 'membeli produk untuk kecantikan', 100, 'Disetujui'),
(10, 5, '2025-10-22', 'membeli produk untuk kecantikan', 100, 'Disetujui'),
(11, 5, '2025-10-22', 'membeli produk untuk kecantikan', 100, 'Disetujui'),
(12, 5, '2025-10-22', 'membeli produk untuk kecantikan', 100, 'Disetujui'),
(13, 5, '2025-10-22', 'membeli produk untuk kecantikan', 100, 'Disetujui'),
(14, 5, '2025-10-22', 'membeli tukang', 100, 'Disetujui'),
(15, 5, '2025-10-22', 'membeli tukang', 100, 'Disetujui'),
(16, 5, '2025-10-22', 'p', 100000, 'Disetujui'),
(17, 5, '2025-10-22', 'p', 100000, 'Disetujui'),
(18, 12, '2025-10-22', 'membeli mobil', 100000, 'Disetujui'),
(19, 12, '2025-10-22', 'membeli rumah', 1000000, 'Disetujui'),
(20, 15, '2025-10-22', 'membeli mobil', 100000000, 'Disetujui'),
(21, 16, '2025-10-22', 'membeli mobil', 100000000, 'Disetujui');

-- --------------------------------------------------------

--
-- Table structure for table `cuti`
--

CREATE TABLE `cuti` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `alasan` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `hari_cuti` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','karyawan') DEFAULT 'karyawan',
  `sisa_cuti` int(11) DEFAULT 4
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nama`, `email`, `password`, `role`, `sisa_cuti`) VALUES
(2, 'Ahmad', 'ahmad@hris.com', '\".PASSWORD_HASH_HERE.\"', 'karyawan', 4),
(3, 'Budi', 'budi@hris.com', '\".PASSWORD_HASH_HERE.\"', 'karyawan', 4),
(4, 'Citra', 'citra@hris.com', '\".PASSWORD_HASH_HERE.\"', 'karyawan', 4),
(5, 'haris', 'haris@gmail.com', '$2y$10$bhqd0Lgu6S91NNF37Qm7OeMBfVAZIPHEvXa9eB0jcLNjqaPg75.k.', 'karyawan', 4),
(12, 'fherry', 'super@gmail.com', '$2y$10$vc2UjlKo53w1RYahS8M3VO0ytGt2kpDOeQdEhLtCGn5IEOcMSPjxa', 'karyawan', 4),
(13, 'Admin HRIS', 'admin@hris.com', 'HASIL_HASH_PASSWORD', 'admin', 4),
(14, 'Admin HRIS', 'admin@gmail.com', '$2y$10$z9oSqvW7cZ7njZUTNkUYXeNH4MY4eXyvqMbEOrPN7nEZJ5xdKSCSe', 'admin', 4),
(15, 'ujang', 'ujang@gmail.com', '$2y$10$p74RM8XVr.352zYT96IC5eVOIm8Tne0lIn9T7Eo8e0ddgLuackCWO', 'karyawan', 4),
(16, 'ayu', 'ayu@gmail.com', '$2y$10$9WA/bSQTK0TQFFDlNz/svO8tXbWZWdmBsuO.6uax2pEcypMfoqnwG', 'karyawan', 4);

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL,
  `tunjangan` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `karyawan_id`, `gaji_pokok`, `tunjangan`) VALUES
(1, 2, 5000000.00, 500000.00),
(2, 3, 4500000.00, 450000.00),
(3, 4, 4800000.00, 480000.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `anggaran`
--
ALTER TABLE `anggaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `cuti`
--
ALTER TABLE `cuti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `anggaran`
--
ALTER TABLE `anggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `cuti`
--
ALTER TABLE `cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `anggaran`
--
ALTER TABLE `anggaran`
  ADD CONSTRAINT `anggaran_ibfk_1` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cuti`
--
ALTER TABLE `cuti`
  ADD CONSTRAINT `cuti_ibfk_1` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
