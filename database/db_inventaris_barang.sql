-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3000
-- Generation Time: Dec 22, 2024 at 12:25 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_inventaris_barang`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_brg` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_ktg` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_brg` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok_brg` int NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_brg`, `id_ktg`, `nama_brg`, `stok_brg`, `satuan`, `created_at`, `updated_at`) VALUES
('brg-001', 'ktg-001', 'Kertas A4', 6, 'rim', '2024-12-17 21:52:36', '2024-12-20 23:59:18'),
('brg-002', 'ktg-002', 'Deterjen', 1, 'pcs', '2024-12-18 02:36:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id_bk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_bk` date NOT NULL,
  `id_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_bk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_bk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang_keluar`
--

INSERT INTO `barang_keluar` (`id_bk`, `tgl_bk`, `id_user`, `bukti_bk`, `status_bk`, `created_at`, `updated_at`) VALUES
('bk-2024-001', '2024-12-24', 'id_002', '/storage/upload/barang_keluar/bukti/qyVA04Rq-2024-12-20.jpg', 'diterima', '2024-12-20 21:46:27', '2024-12-20 23:58:28');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_barang_keluar`
--

CREATE TABLE `detail_barang_keluar` (
  `id_dbk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_bk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_brg` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jum_bk` int NOT NULL,
  `jum_setuju_bk` int DEFAULT NULL,
  `ket_bk` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_barang_keluar`
--

INSERT INTO `detail_barang_keluar` (`id_dbk`, `id_bk`, `id_brg`, `jum_bk`, `jum_setuju_bk`, `ket_bk`, `created_at`, `updated_at`) VALUES
('dbk-12-2024-001', 'bk-2024-001', 'brg-001', 3, 3, 'okeee', '2024-12-20 21:46:28', '2024-12-20 23:58:28');

-- --------------------------------------------------------

--
-- Table structure for table `detail_permohonan`
--

CREATE TABLE `detail_permohonan` (
  `id_dp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_permo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_brg` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_per` int NOT NULL,
  `jumlah_setuju` int DEFAULT NULL,
  `ket_permo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_permohonan`
--

INSERT INTO `detail_permohonan` (`id_dp`, `id_permo`, `id_brg`, `jumlah_per`, `jumlah_setuju`, `ket_permo`, `created_at`, `updated_at`) VALUES
('dp-12-2024-001', 'per-2024-001', 'brg-001', 1, 1, 'ok', '2024-12-19 03:39:55', '2024-12-20 01:04:05'),
('dp-12-2024-002', 'per-2024-002', 'brg-002', 1, 1, 'lanjut', '2024-12-20 01:05:50', '2024-12-20 01:06:19'),
('dp-12-2024-003', 'per-2024-003', 'brg-001', 2, 2, 'lanjutkan', '2024-12-20 01:06:47', '2024-12-20 01:07:15'),
('dp-12-2024-004', 'per-2024-004', 'brg-001', 5, 0, 'terlalu banyak', '2024-12-20 01:09:46', '2024-12-20 01:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_ktg` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_ktg`, `nama_kategori`, `created_at`, `updated_at`) VALUES
('ktg-001', 'atk', '2024-12-16 20:06:45', NULL),
('ktg-002', 'kebersihan', '2024-12-16 21:09:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permohonan`
--

CREATE TABLE `permohonan` (
  `id_permo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_permo` date NOT NULL,
  `bukti_permo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permohonan`
--

INSERT INTO `permohonan` (`id_permo`, `tgl_permo`, `bukti_permo`, `status`, `created_at`, `updated_at`) VALUES
('per-2024-001', '2024-12-19', '/storage/upload/permohonan/bukti/qYzkDwHE-2024-12-19.jpg', 'diterima', '2024-12-19 03:39:54', '2024-12-20 01:04:05'),
('per-2024-002', '2024-12-21', '/storage/upload/permohonan/bukti/jvX7w8fA-2024-12-20.jpg', 'diterima', '2024-12-20 01:05:49', '2024-12-20 01:06:19'),
('per-2024-003', '2024-12-20', '/storage/upload/permohonan/bukti/sLlxuapf-2024-12-20.jpg', 'diterima', '2024-12-20 01:06:46', '2024-12-20 01:07:14'),
('per-2024-004', '2024-12-21', '/storage/upload/permohonan/bukti/WozWcgc0-2024-12-20.jpg', 'ditolak', '2024-12-20 01:09:45', '2024-12-20 01:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id_role` bigint UNSIGNED NOT NULL,
  `nama_role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id_role`, `nama_role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2024-12-16 19:20:22', NULL),
(2, 'guru', '2024-12-16 19:20:22', NULL),
(3, 'kepsek', '2024-12-16 19:20:22', NULL),
(4, 'bendahara', '2024-12-16 19:20:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0wRt083XNQxkAzDxUeVfSiuGrbGANptKVQbUM3ue', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YToyOntzOjY6Il9mbGFzaCI7YToyOntzOjM6Im5ldyI7YTowOnt9czozOiJvbGQiO2E6MDp7fX1zOjY6Il90b2tlbiI7czo0MDoiV2s2WGxpZEtkU2pmU1ZaSjdFcHpaTHM0bjJOUmJKNDhpejNURHQ0aCI7fQ==', 1734868751),
('wFRpZKEJpXp8A6bnohjBIa6YTYRuGTytVcCaY1tx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YTozOntzOjY6Il9mbGFzaCI7YToyOntzOjM6Im5ldyI7YTowOnt9czozOiJvbGQiO2E6MDp7fX1zOjY6Il90b2tlbiI7czo0MDoiVXZjaTZScGpDcXdra2ozaWRFSVZHS3V3R3NJdE9wMVBUUkV4VTZySSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fX0=', 1734870292);

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `id_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `unit`
--

INSERT INTO `unit` (`id_unit`, `nama_unit`, `created_at`, `updated_at`) VALUES
('unit-2024-001', 'tata usaha', '2024-12-16 19:20:23', NULL),
('unit-2024-002', 'keuangan', '2024-12-16 19:20:23', NULL),
('unit-2024-003', 'lab komputer', '2024-12-16 19:20:23', NULL),
('unit-2024-004', 'perpustakaan', '2024-12-17 02:50:53', '2024-12-17 02:52:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_role` bigint UNSIGNED NOT NULL,
  `id_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `email`, `password`, `id_role`, `id_unit`, `remember_token`, `created_at`, `updated_at`) VALUES
('id_001', 'glory', 'glory@gmail.com', '$2y$12$fLbYwHRVsGLfmvQyA63bqeO4Fb0SGTKyBq5z2eKfMJ1ZngwltpmUG', 1, 'unit-2024-001', NULL, '2024-12-16 19:20:31', NULL),
('id_002', 'abel', 'abel@example.net', '$2y$12$pWgH7mw9xIwk04ap9e1m5Oc3aVaG4Cr5l3k6TEI4C6atH0E7UqES6', 2, 'unit-2024-002', NULL, '2024-12-16 19:34:06', NULL),
('id_003', 'lazu', 'lazu@example.net', '$2y$12$PztUWG5ZYs.kENn9ucysFur4ZgEJDjQneRf37rz1zEsFHdjNsaSIW', 3, 'unit-2024-003', NULL, '2024-12-18 02:19:42', NULL),
('id_004', 'henry', 'henry@example.net', '$2y$12$y0G64DR0CHaQCortDh293ejdoMxQL8OUk24VehfDgDqoYv.8cNq1G', 4, 'unit-2024-004', NULL, '2024-12-18 02:20:22', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_brg`);

--
-- Indexes for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id_bk`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `detail_barang_keluar`
--
ALTER TABLE `detail_barang_keluar`
  ADD PRIMARY KEY (`id_dbk`);

--
-- Indexes for table `detail_permohonan`
--
ALTER TABLE `detail_permohonan`
  ADD PRIMARY KEY (`id_dp`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_ktg`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permohonan`
--
ALTER TABLE `permohonan`
  ADD PRIMARY KEY (`id_permo`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`id_unit`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_id_role_foreign` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_id_role_foreign` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
