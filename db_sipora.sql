-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 22, 2025 at 06:33 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sipora`
--

-- --------------------------------------------------------

--
-- Table structure for table `dokumen`
--

CREATE TABLE `dokumen` (
  `dokumen_id` int NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `abstrak` text COLLATE utf8mb4_general_ci,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tgl_unggah` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `uploader_id` int NOT NULL,
  `id_tema` int NOT NULL,
  `id_jurusan` int NOT NULL,
  `id_prodi` int NOT NULL,
  `year_id` int NOT NULL,
  `status_id` int NOT NULL,
  `format_id` int NOT NULL,
  `policy_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_author`
--

CREATE TABLE `dokumen_author` (
  `dokumen_author_id` int NOT NULL,
  `dokumen_id` int NOT NULL,
  `author_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_keyword`
--

CREATE TABLE `dokumen_keyword` (
  `dokumen_keyword_id` int NOT NULL,
  `dokumen_id` int NOT NULL,
  `keyword_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_review`
--

CREATE TABLE `log_review` (
  `log_id` int NOT NULL,
  `dokumen_id` int NOT NULL,
  `reviewer_id` int NOT NULL,
  `tgl_review` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `catatan_review` text COLLATE utf8mb4_general_ci,
  `status_sebelum` int NOT NULL,
  `status_sesudah` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_author`
--

CREATE TABLE `master_author` (
  `author_id` int NOT NULL,
  `nama_author` varchar(150) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_dokumen`
--

CREATE TABLE `master_dokumen` (
  `format_id` int NOT NULL,
  `nama_format` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_jurusan`
--

CREATE TABLE `master_jurusan` (
  `id_jurusan` int NOT NULL,
  `nama_jurusan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_keyword`
--

CREATE TABLE `master_keyword` (
  `keyword_id` int NOT NULL,
  `nama_keyword` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_policy`
--

CREATE TABLE `master_policy` (
  `policy_id` int NOT NULL,
  `nama_policy` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_prodi`
--

CREATE TABLE `master_prodi` (
  `id_prodi` int NOT NULL,
  `nama_prodi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_status_dokumen`
--

CREATE TABLE `master_status_dokumen` (
  `status_id` int NOT NULL,
  `nama_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_tahun`
--

CREATE TABLE `master_tahun` (
  `year_id` int NOT NULL,
  `tahun` varchar(4) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_tema`
--

CREATE TABLE `master_tema` (
  `id_tema` int NOT NULL,
  `nama_tema` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nomor_induk` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `pasword_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role_id` int NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nomor_induk`, `email`, `username`, `pasword_hash`, `role_id`, `status`, `created_at`) VALUES
(8, 'USR20256051', 'e41240390@student.polije.ac.id', 'Saiful Rizal', '$2y$10$5BZLjt4zZnyXr/uSUCx7/Oe5lSjUhFQ1zTh0fb5.bIMI7NPfasJ1G', 2, 'approved', '2025-10-21 10:25:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`dokumen_id`),
  ADD KEY `uploader_id` (`uploader_id`),
  ADD KEY `id_tema` (`id_tema`),
  ADD KEY `id_jurusan` (`id_jurusan`),
  ADD KEY `id_prodi` (`id_prodi`),
  ADD KEY `year_id` (`year_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `format_id` (`format_id`),
  ADD KEY `policy_id` (`policy_id`);

--
-- Indexes for table `dokumen_author`
--
ALTER TABLE `dokumen_author`
  ADD PRIMARY KEY (`dokumen_author_id`),
  ADD KEY `dokumen_id` (`dokumen_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `dokumen_keyword`
--
ALTER TABLE `dokumen_keyword`
  ADD PRIMARY KEY (`dokumen_keyword_id`),
  ADD KEY `dokumen_id` (`dokumen_id`),
  ADD KEY `keyword_id` (`keyword_id`);

--
-- Indexes for table `log_review`
--
ALTER TABLE `log_review`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `dokumen_id` (`dokumen_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `status_sebelum` (`status_sebelum`),
  ADD KEY `status_sesudah` (`status_sesudah`);

--
-- Indexes for table `master_author`
--
ALTER TABLE `master_author`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `master_dokumen`
--
ALTER TABLE `master_dokumen`
  ADD PRIMARY KEY (`format_id`);

--
-- Indexes for table `master_jurusan`
--
ALTER TABLE `master_jurusan`
  ADD PRIMARY KEY (`id_jurusan`);

--
-- Indexes for table `master_keyword`
--
ALTER TABLE `master_keyword`
  ADD PRIMARY KEY (`keyword_id`);

--
-- Indexes for table `master_policy`
--
ALTER TABLE `master_policy`
  ADD PRIMARY KEY (`policy_id`);

--
-- Indexes for table `master_prodi`
--
ALTER TABLE `master_prodi`
  ADD PRIMARY KEY (`id_prodi`);

--
-- Indexes for table `master_status_dokumen`
--
ALTER TABLE `master_status_dokumen`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `master_tahun`
--
ALTER TABLE `master_tahun`
  ADD PRIMARY KEY (`year_id`);

--
-- Indexes for table `master_tema`
--
ALTER TABLE `master_tema`
  ADD PRIMARY KEY (`id_tema`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `dokumen_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dokumen_author`
--
ALTER TABLE `dokumen_author`
  MODIFY `dokumen_author_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dokumen_keyword`
--
ALTER TABLE `dokumen_keyword`
  MODIFY `dokumen_keyword_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_review`
--
ALTER TABLE `log_review`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_author`
--
ALTER TABLE `master_author`
  MODIFY `author_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_dokumen`
--
ALTER TABLE `master_dokumen`
  MODIFY `format_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_jurusan`
--
ALTER TABLE `master_jurusan`
  MODIFY `id_jurusan` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_keyword`
--
ALTER TABLE `master_keyword`
  MODIFY `keyword_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_policy`
--
ALTER TABLE `master_policy`
  MODIFY `policy_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_prodi`
--
ALTER TABLE `master_prodi`
  MODIFY `id_prodi` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_status_dokumen`
--
ALTER TABLE `master_status_dokumen`
  MODIFY `status_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_tahun`
--
ALTER TABLE `master_tahun`
  MODIFY `year_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_tema`
--
ALTER TABLE `master_tema`
  MODIFY `id_tema` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD CONSTRAINT `dokumen_ibfk_1` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `dokumen_ibfk_2` FOREIGN KEY (`id_tema`) REFERENCES `master_tema` (`id_tema`),
  ADD CONSTRAINT `dokumen_ibfk_3` FOREIGN KEY (`id_jurusan`) REFERENCES `master_jurusan` (`id_jurusan`),
  ADD CONSTRAINT `dokumen_ibfk_4` FOREIGN KEY (`id_prodi`) REFERENCES `master_prodi` (`id_prodi`),
  ADD CONSTRAINT `dokumen_ibfk_5` FOREIGN KEY (`year_id`) REFERENCES `master_tahun` (`year_id`),
  ADD CONSTRAINT `dokumen_ibfk_6` FOREIGN KEY (`status_id`) REFERENCES `master_status_dokumen` (`status_id`),
  ADD CONSTRAINT `dokumen_ibfk_7` FOREIGN KEY (`format_id`) REFERENCES `master_dokumen` (`format_id`),
  ADD CONSTRAINT `dokumen_ibfk_8` FOREIGN KEY (`policy_id`) REFERENCES `master_policy` (`policy_id`);

--
-- Constraints for table `dokumen_author`
--
ALTER TABLE `dokumen_author`
  ADD CONSTRAINT `dokumen_author_ibfk_1` FOREIGN KEY (`dokumen_id`) REFERENCES `dokumen` (`dokumen_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dokumen_author_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `master_author` (`author_id`) ON DELETE CASCADE;

--
-- Constraints for table `dokumen_keyword`
--
ALTER TABLE `dokumen_keyword`
  ADD CONSTRAINT `dokumen_keyword_ibfk_1` FOREIGN KEY (`dokumen_id`) REFERENCES `dokumen` (`dokumen_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dokumen_keyword_ibfk_2` FOREIGN KEY (`keyword_id`) REFERENCES `master_keyword` (`keyword_id`) ON DELETE CASCADE;

--
-- Constraints for table `log_review`
--
ALTER TABLE `log_review`
  ADD CONSTRAINT `log_review_ibfk_1` FOREIGN KEY (`dokumen_id`) REFERENCES `dokumen` (`dokumen_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `log_review_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `log_review_ibfk_3` FOREIGN KEY (`status_sebelum`) REFERENCES `master_status_dokumen` (`status_id`),
  ADD CONSTRAINT `log_review_ibfk_4` FOREIGN KEY (`status_sesudah`) REFERENCES `master_status_dokumen` (`status_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
