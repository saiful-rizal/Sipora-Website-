-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 28, 2025 at 07:18 AM
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
  `tipe_dokumen` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `abstrak` text COLLATE utf8mb4_general_ci,
  `tgl_unggah` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `uploader_id` int NOT NULL,
  `file_path` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `file_size` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `id_tema` int NOT NULL,
  `id_jurusan` int NOT NULL,
  `id_prodi` int NOT NULL,
  `year_id` int NOT NULL,
  `status_id` int NOT NULL,
  `format_id` int NOT NULL,
  `policy_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokumen`
--

INSERT INTO `dokumen` (`dokumen_id`, `judul`, `tipe_dokumen`, `abstrak`, `tgl_unggah`, `uploader_id`, `file_path`, `file_size`, `id_tema`, `id_jurusan`, `id_prodi`, `year_id`, `status_id`, `format_id`, `policy_id`) VALUES
(11, 'Computer Networking', 'final_project', 'qqwq', '2025-10-23 06:31:05', 3, 'uploads/documents/68f9cba96c258.pdf', '218446', 1, 8, 6, 4, 4, 2, 1),
(12, 'Computer Networking', 'final_project', 'qqwq', '2025-10-23 06:31:05', 3, 'uploads/documents/68f9cba98e18c.pdf', '218446', 1, 8, 6, 4, 4, 2, 1),
(13, 'Computer Networking', 'final_project', 'qqwq', '2025-10-23 06:31:05', 3, 'uploads/documents/68f9cba9a75a5.pdf', '218446', 1, 8, 6, 4, 4, 2, 1),
(14, 'Zzaasa', 'thesis', 'sasasas', '2025-10-23 06:32:25', 3, 'uploads/documents/68f9cbf926a95.pdf', '218446', 2, 8, 8, 6, 4, 6, 4),
(15, 'hai', 'journal', 'xzzx', '2025-10-23 07:06:28', 3, 'uploads/documents/68f9d3f47a28c.pdf', '246724', 1, 8, 6, 5, 2, 2, 2),
(16, 'hai', 'journal', 'xzzx', '2025-10-23 07:06:28', 3, 'uploads/documents/68f9d3f495821.pdf', '246724', 1, 8, 6, 5, 2, 2, 2),
(17, 'xxzxZxz', 'ebook', 'aas', '2025-10-23 07:35:08', 3, 'uploads/documents/68f9daac74d7c.jpg', '1150696', 4, 8, 8, 5, 2, 2, 2),
(18, 'xxzxZxz', 'ebook', 'aas', '2025-10-23 07:35:08', 3, 'uploads/documents/68f9daac7fbf3.pdf', '1150696', 4, 8, 8, 5, 2, 2, 2),
(19, 'xxzxZxz', 'journal', 'asa', '2025-10-28 00:38:39', 5, 'uploads/documents/6900108f36cc9.pdf', '928167', 5, 8, 2, 7, 5, 2, 1);

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

--
-- Dumping data for table `master_dokumen`
--

INSERT INTO `master_dokumen` (`format_id`, `nama_format`) VALUES
(2, 'PDF'),
(3, 'DOC/DOCX'),
(4, 'PPT/PPTX'),
(5, 'ZIP'),
(6, 'Gambar');

-- --------------------------------------------------------

--
-- Table structure for table `master_jurusan`
--

CREATE TABLE `master_jurusan` (
  `id_jurusan` int NOT NULL,
  `nama_jurusan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_jurusan`
--

INSERT INTO `master_jurusan` (`id_jurusan`, `nama_jurusan`) VALUES
(2, 'Teknologi Informasi'),
(3, 'Produksi Pertanian'),
(4, 'Peternakan'),
(5, 'Kesehatan'),
(6, 'Bahasa, Komunikasi, dan Pariwisata'),
(7, 'Teknik'),
(8, 'Manajemen Agribisnis');

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

--
-- Dumping data for table `master_policy`
--

INSERT INTO `master_policy` (`policy_id`, `nama_policy`) VALUES
(1, 'Publik'),
(2, 'Publik'),
(3, 'Terbatas'),
(4, 'Rahasia');

-- --------------------------------------------------------

--
-- Table structure for table `master_prodi`
--

CREATE TABLE `master_prodi` (
  `id_prodi` int NOT NULL,
  `nama_prodi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_prodi`
--

INSERT INTO `master_prodi` (`id_prodi`, `nama_prodi`) VALUES
(2, 'D-IV Teknik Informatika'),
(3, 'D-III Manajemen Informatika'),
(4, 'D-IV Agribisnis'),
(5, 'D-IV Produksi Ternak'),
(6, 'D-III Keperawatan'),
(7, 'D-IV Bahasa Inggris Terapan'),
(8, 'D-IV Teknik Energi Terbarukan');

-- --------------------------------------------------------

--
-- Table structure for table `master_status_dokumen`
--

CREATE TABLE `master_status_dokumen` (
  `status_id` int NOT NULL,
  `nama_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_status_dokumen`
--

INSERT INTO `master_status_dokumen` (`status_id`, `nama_status`) VALUES
(2, 'Diajukan'),
(3, 'Diperiksa'),
(4, 'Disetujui'),
(5, 'Ditolak'),
(6, 'Publikasi');

-- --------------------------------------------------------

--
-- Table structure for table `master_tahun`
--

CREATE TABLE `master_tahun` (
  `year_id` int NOT NULL,
  `tahun` varchar(4) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_tahun`
--

INSERT INTO `master_tahun` (`year_id`, `tahun`) VALUES
(1, '2025'),
(2, '2020'),
(3, '2021'),
(4, '2022'),
(5, '2023'),
(6, '2024'),
(7, '2025');

-- --------------------------------------------------------

--
-- Table structure for table `master_tema`
--

CREATE TABLE `master_tema` (
  `id_tema` int NOT NULL,
  `nama_tema` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_tema`
--

INSERT INTO `master_tema` (`id_tema`, `nama_tema`) VALUES
(1, 'Kesehatan'),
(2, 'Teknologi'),
(3, 'Pertanian'),
(4, 'Pendidikan'),
(5, 'Lingkungan'),
(6, 'Teknologi Informasi');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nomor_induk` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `pasword_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role_id` int NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `nomor_induk`, `email`, `username`, `pasword_hash`, `role_id`, `status`, `created_at`) VALUES
(1, 'Administrator SIPORA', 'ADM001', 'admin@sipora.com', 'admin', '$2y$10$PLvM4ZJcHvyA6o6bP9pK3u2g9MfCtXXtuKiFscU/NqML3VUVNglzO', 1, 'approved', '2025-10-07 03:19:59'),
(2, 'hilda', 'E41240353', 'hildaaprilia@gmail.com', 'hilda', '$2y$10$/6bHYVd5M4cSbUhzDlKGtOLuLextYU/gHyMZPVDwaEcXvOdDZduM2', 1, 'approved', '2025-10-07 06:51:35'),
(3, 'fikri', 'H942233', 'e41240353@student.polije.ac.id', 'fikri', '$2y$10$FVeuG9YNkiOCzefx504uCeRj7FkNMCkpRdOQLEquvaqGdXbdrYjZa', 1, 'approved', '2025-10-07 11:04:10'),
(4, 'Talitha', 'E41240073', 'e41240073@student.polije.ac.id', 'talitha', '$2y$10$zUKTxDtSgKywiuPdLpmAvea1LIr1RnqOWj/6vVf1HHVbGMRvV/5ui', 2, 'approved', '2025-10-21 01:54:10'),
(5, 'saiful rizal', '2323132', 'e41240390@student.polije.ac.id', 'Saiful Rizal', '$2y$10$3ikCQGGtPBB9TBM9qz2L7.YZMFLwS5.tCv.alvcG4mpA50ULwFQ12', 2, 'approved', '2025-10-24 00:09:51');

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
  MODIFY `dokumen_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `format_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `master_jurusan`
--
ALTER TABLE `master_jurusan`
  MODIFY `id_jurusan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `master_keyword`
--
ALTER TABLE `master_keyword`
  MODIFY `keyword_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_policy`
--
ALTER TABLE `master_policy`
  MODIFY `policy_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `master_prodi`
--
ALTER TABLE `master_prodi`
  MODIFY `id_prodi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `master_status_dokumen`
--
ALTER TABLE `master_status_dokumen`
  MODIFY `status_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `master_tahun`
--
ALTER TABLE `master_tahun`
  MODIFY `year_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `master_tema`
--
ALTER TABLE `master_tema`
  MODIFY `id_tema` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
