SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Data untuk Tabel: users
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `nama_rumah_tangga`, `role`, `status_aktif`, `created_at`, `foto_profil`, `max_tasks_per_day`, `max_tasks_per_week`) VALUES 
('1', 'admin_utama', '$2y$10$klnX6iKMuCIbKKeL7XWaaeKg1FFy0ImVK8YqkCJ/eEl2QmCmNUkl2', 'Admin Utama', 'Admin System', 'admin', '1', '2026-04-14 21:37:28', 'default.png', NULL, NULL),
('2', 'admin_budi', '$2y$10$NFLCxa1YtcMfutpd9gZq/.390366D683YaC0YIhlWRmud.45lrKmS', 'Budi Santoso', 'Admin Support', 'admin', '1', '2026-04-14 21:37:28', 'default.png', NULL, NULL),
('3', 'keluarga_bapak_ahmad', '$2y$10$klnX6iKMuCIbKKeL7XWaaeKg1FFy0ImVK8YqkCJ/eEl2QmCmNUkl2', 'Ahmad Rizki', 'Keluarga Ahmad', 'user', '1', '2026-04-14 21:37:28', '6a0fb59652f85.png', '2', '10'),
('4', 'keluarga_bapak_doni', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Doni Prasetyo', 'Keluarga Doni', 'user', '1', '2026-04-14 21:37:28', 'default.png', '2', '10'),
('5', 'keluarga_bapak_reza', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Reza Firmansyah', 'Keluarga Reza', 'user', '1', '2026-04-14 21:37:28', 'default.png', '2', '10'),
('12', 'ahmad', 'ahmad', 'ahmad', 'ahmad', 'admin', '1', '2026-05-15 21:51:12', 'default.png', NULL, NULL),
('13', 'fdhil273', '$2y$10$29qlua0TrHiSiv3ShzM.BuA18fhgXlZI3gfsvmdzXK4rGMLnXyVyu', 'nama lengkap', 'rumah ', 'user', '1', '2026-05-18 09:00:12', 'default.png', '2', '10'),
('14', 'manman', '$2y$10$.OsmUhTKtYXQ49vz2rvD.uHb7BulbCUFCKvRXqw9khHJPiIWDPHse', 'manman', 'manman', 'user', '1', '2026-05-18 09:02:47', 'default.png', '2', '10'),
('15', 'adek', '$2y$10$yOp7uojmGznFoMbXoYCLHue5i8kT.Vop9S4/BUA3vp1xaPcVCMfga', 'adek', 'adek', 'user', '1', '2026-05-18 09:17:22', 'default.png', '2', '10'),
('16', 'Aziz', '$2y$10$PWi7GHBFvUSJo3tdRsWvkuhU/dqjNbchEmzHEOdOdkJwLqzC8RrJq', 'Aziz_Walawe', 'Mantap', 'user', '1', '2026-05-18 09:19:37', 'default.png', '2', '10'),
('20', 'myname', '$2y$10$fddLdutY6rWpM5Sb6OBce.S1hhNv92UCLMHmcmyu/rYR35bdduExO', 'myname', 'myname', 'user', '1', '2026-05-20 07:36:30', 'default.png', '2', '10'),
('22', 'bosku', '$2y$10$KOct.gDKPVvjzgxDuHdiWuvREuufifbQtlCB7eGYSb6UxaNNOIazC', 'bosku', 'bosku', 'user', '1', '2026-05-20 08:26:16', '6a0d0db8d3c94.jpg', '2', '10'),
('24', 'tes', '$2y$10$dlz4N1OiVTctPBztaajsvuFNwf0ZQ/LlK4jGsRJ1dUTc7pAEG02C2', 'tes', 'tes', 'user', '1', '2026-05-20 08:51:01', 'default.png', '2', '10'),
('25', 'tesakun', '$2y$10$ILLgdln/17H0ERmtBxnmyu9cBLz91TSQzZrGg8papoaNFk0fCtcOS', 'tesakun', 'tesakun', 'user', '1', '2026-05-20 15:31:36', '6a0d716912dec.png', '2', '10'),
('26', 'akunbaru', '$2y$10$XcLOZaHb.EBLty/4c24M6euqTNovZ5nFUbIBwDNGyjxO1VM6vPuPS', 'akunbaru', 'akunbaru', 'user', '1', '2026-05-20 15:32:31', '6a0d719fb6abf.png', '2', '10'),
('27', 'azizkun', '$2y$10$71SPXSKYK0LZT7IcEmxydeGIHwwvRSVwULPleoBeZAiPcPY4LD56K', 'azizkun', 'azizkun', 'user', '1', '2026-05-20 15:35:17', '6a0d72457d930.png', '2', '10');

-- --------------------------------------------------------
-- Data untuk Tabel: family_members
-- --------------------------------------------------------
INSERT INTO `family_members` (`id`, `user_id`, `nama`, `role_dalam_keluarga`, `status`, `username`, `password`, `foto_profil`, `max_tasks_per_day`, `max_tasks_per_week`) VALUES 
('9', '2', 'Ibu Siti', 'Istri', '1', 'ibu_siti', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2', '10'),
('10', '2', 'Anak Pertama', 'Anak', '1', 'anak_pertama', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2', '10'),
('11', '2', 'Anak Kedua', 'Anak', '1', 'anak_kedua', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2', '10'),
('16', '3', 'Ibu Ahmad', 'Istri', '1', 'ibu_ahmad', '$2y$10$5Zl6tsAXXKXcUeurB7b4POomNUBnK5rwoR/MW4YOI8vWyWzCdqVxO', '6a0fb5b4536e1.png', '2', '10'),
('18', '3', 'Zahid', 'adik', '1', 'zahid', '$2y$10$e2NyllSbPQYTbHn8AYvJqOHZjj88qASKt4O8uadN1QhyZP9cxsGKq', 'default.png', '2', '10');

-- --------------------------------------------------------
-- Data untuk Tabel: items
-- --------------------------------------------------------
INSERT INTO `items` (`id`, `user_id`, `nama_barang`, `lokasi_detail`, `created_at`, `updated_at`) VALUES 
('1', '3', 'Dyson V15 Detect', 'Gudang Peralatan', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('2', '3', 'Nespresso Vertuo', 'Dapur > Meja Utama', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('3', '3', 'PlayStation 5', 'Ruang Tamu', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('4', '3', 'Kotak P3K', 'Lemari Dapur Atas', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('6', '4', 'Robot Vacuum Cleaner', 'Ruang Tamu > Sudut Kiri', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('7', '4', 'Mixer Philips', 'Dapur > Rak Bawah', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('8', '4', 'Obeng Set 24 in 1', 'Garasi > Lemari Alat', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('9', '4', 'Rak Buku Kayu', 'Kamar Tidur Utama', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('10', '4', 'Dispenser Air', 'Dapur > Sudut Kanan', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('11', '5', 'Blender Panasonic', 'Dapur > Meja Tengah', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('12', '5', 'Sepatu Gunung', 'Lemari Masuk > Rak Bawah', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('13', '5', 'Kamera DSLR Canon', 'Ruang Kerja > Rak Atas', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('14', '5', 'Alat Jahit Portable', 'Kamar Tidur > Laci Meja', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('15', '5', 'Tenda Camping 4 Orang', 'Gudang Belakang > Rak Tinggi', '2026-04-14 21:37:28', '2026-04-14 21:37:28'),
('17', '3', 'HP jadul', 'Meja Hijau', '2026-05-17 12:47:27', '2026-05-17 12:47:27'),
('19', '3', 'Teko', 'Kamar Mandi', '2026-05-21 10:20:29', '2026-05-21 10:20:29');

-- --------------------------------------------------------
-- Data untuk Tabel: task_pool
-- --------------------------------------------------------
INSERT INTO `task_pool` (`id`, `user_id`, `nama_tugas`, `created_at`) VALUES 
('6', '3', 'Nyiram Tanaman', '2026-05-16 13:56:08'),
('7', '3', 'Cuci Motor', '2026-05-17 14:08:37'),
('8', '3', 'Cuci Mobil', '2026-05-17 14:08:44'),
('9', '3', 'Belanja Mobil', '2026-05-21 08:13:50');

-- --------------------------------------------------------
-- Data untuk Tabel: tasks
-- --------------------------------------------------------
INSERT INTO `tasks` (`id`, `user_id`, `nama_tugas`, `prioritas`, `assigned_to`, `jadwal`, `status`, `created_at`, `approval_status`, `bukti_foto`, `notified_overdue`) VALUES 
('9', '3', 'Matikan kompor', 'Rendah', 'Ahmad Rizki', '2026-05-25', 'Selesai', '2026-05-25 08:51:28', 'Disetujui', NULL, '0'),
('10', '3', 'Matikan kompor', 'Rendah', 'Ibu Ahmad', '2026-05-27', 'Pending', '2026-05-25 08:51:42', 'Belum Selesai', NULL, '0'),
('14', '3', 'Beli Baju Baru', 'Sedang', 'Ibu Ahmad', '2026-05-25', 'Pending', '2026-05-25 10:21:37', 'Belum Selesai', NULL, '0');

-- --------------------------------------------------------
-- Data untuk Tabel: assigned_tasks
-- --------------------------------------------------------
INSERT INTO `assigned_tasks` (`id`, `user_id`, `assigned_to`, `nama_tugas`, `tanggal`, `status`, `is_random`, `created_at`, `approval_status`, `bukti_foto`, `notified_overdue`) VALUES 
('29', '3', 'Zahid', 'Nyiram Tanaman', '2026-05-21', 'Pending', '1', '2026-05-21 11:08:03', 'Belum Selesai', NULL, '1'),
('30', '3', 'Ibu Ahmad', 'Cuci Motor', '2026-05-21', 'Selesai', '1', '2026-05-21 11:08:03', 'Disetujui', '6a0ea82160bd8.jpg', '0'),
('31', '3', 'Ahmad Rizki', 'Cuci Mobil', '2026-05-21', 'Pending', '1', '2026-05-21 11:08:03', 'Belum Selesai', NULL, '1'),
('59', '3', 'Ibu Ahmad', 'Belanja Mobil', '2026-05-25', 'Pending', '1', '2026-05-25 07:31:41', 'Belum Selesai', '6a13ba18dd1f4.jpg', '0');

-- --------------------------------------------------------
-- Data untuk Tabel: notifications
-- --------------------------------------------------------
INSERT INTO `notifications` (`id`, `user_id`, `member_id`, `title`, `message`, `is_read`, `created_at`, `is_late`) VALUES 
('1', NULL, '16', 'Tugas Pribadi Baru', 'Admin memberikan tugas baru: Nyapu Kamar Depan.', '1', '2026-05-22 08:20:57', '0'),
('5', NULL, '18', 'Tugas Terlambat!', 'Tugas terjadwal Anda: \"Nyiram Tanaman\" telah melewati batas waktu.', '0', '2026-05-25 09:37:45', '1');

SET FOREIGN_KEY_CHECKS = 1;