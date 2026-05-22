CREATE DATABASE  `smart_home`;

USE `smart_home`;

DROP TABLE IF EXISTS `activity_logs`;

CREATE TABLE `activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `family_id` int DEFAULT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `fk_family_id` (`family_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_family_id` FOREIGN KEY (`family_id`) REFERENCES `family_members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `activity_logs` VALUES (1,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-15 03:44:22'),(2,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-15 03:44:40'),(3,2,NULL,'login','admin_budi masuk ke sistem','2026-05-15 03:45:45'),(4,2,NULL,'logout','admin_budi keluar dari sistem','2026-05-15 03:45:54'),(5,3,16,'login','Member Ibu Ahmad masuk ke sistem','2026-05-15 03:46:14'),(6,3,16,'logout','Member Ibu Ahmad keluar dari sistem','2026-05-15 03:46:19'),(7,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-15 03:55:59'),(8,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-15 04:07:24'),(9,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-15 04:07:30'),(10,1,NULL,'login','admin_utama masuk ke sistem','2026-05-15 04:07:50'),(11,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-15 04:08:05'),(12,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-15 04:08:12'),(13,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-15 04:08:14'),(14,1,NULL,'login','admin_utama masuk ke sistem','2026-05-15 06:21:36'),(15,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-15 06:29:43'),(16,1,NULL,'login','admin_utama masuk ke sistem','2026-05-15 14:47:11'),(17,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-15 14:54:14'),(18,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-16 05:23:55'),(19,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-16 06:35:48'),(20,3,18,'login','Member Zahid masuk ke sistem','2026-05-16 07:05:26'),(21,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-16 13:33:38'),(22,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-17 00:53:15'),(23,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-17 00:59:21'),(24,3,16,'login','Member Ibu Ahmad masuk ke sistem','2026-05-17 00:59:30'),(25,3,16,'logout','Member Ibu Ahmad keluar dari sistem','2026-05-17 01:07:27'),(26,1,NULL,'login','admin_utama masuk ke sistem','2026-05-17 01:07:33'),(27,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-17 01:20:08'),(28,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-17 01:20:16'),(29,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-17 01:20:35'),(30,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-17 01:38:01'),(31,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-17 01:38:48'),(32,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-17 01:39:18'),(33,1,NULL,'login','admin_utama masuk ke sistem','2026-05-17 03:13:02'),(34,1,NULL,'login','admin_utama masuk ke sistem','2026-05-17 03:13:31'),(35,1,NULL,'login','admin_utama masuk ke sistem','2026-05-17 03:22:38'),(36,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-17 03:24:04'),(37,1,NULL,'login','admin_utama masuk ke sistem','2026-05-17 03:24:39'),(38,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-17 05:10:03'),(39,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-17 06:40:07'),(40,1,NULL,'login','admin_utama masuk ke sistem','2026-05-17 06:51:39'),(41,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-17 06:54:48'),(42,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-17 06:54:54'),(43,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-17 14:53:59'),(44,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-18 01:32:32'),(45,3,16,'login','Member Ibu Ahmad masuk ke sistem','2026-05-18 01:34:54'),(46,13,NULL,'login','fdhil273 masuk ke sistem','2026-05-18 02:00:50'),(47,13,NULL,'logout','fdhil273 keluar dari sistem','2026-05-18 02:01:37'),(48,16,NULL,'login','Aziz masuk ke sistem','2026-05-18 02:20:00'),(49,NULL,NULL,'login','kevin masuk ke sistem','2026-05-18 02:23:03'),(50,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-18 12:27:07'),(51,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-18 12:30:08'),(52,1,NULL,'login','admin_utama masuk ke sistem','2026-05-18 12:35:46'),(53,1,NULL,'login','admin_utama masuk ke sistem','2026-05-19 03:41:51'),(54,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-19 03:42:14'),(55,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-19 03:42:21'),(56,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-20 00:30:09'),(57,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-20 00:33:42'),(58,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-20 00:33:54'),(59,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-20 00:34:06'),(60,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-20 00:34:51'),(61,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-20 00:35:00'),(62,1,NULL,'login','admin_utama masuk ke sistem','2026-05-20 00:35:07'),(63,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-20 00:35:21'),(64,20,NULL,'login','myname masuk ke sistem','2026-05-20 00:36:37'),(65,20,NULL,'logout','myname keluar dari sistem','2026-05-20 00:36:58'),(66,NULL,NULL,'login','helo masuk ke sistem','2026-05-20 00:54:18'),(67,NULL,NULL,'logout','helo keluar dari sistem','2026-05-20 01:17:25'),(68,22,NULL,'login','bosku masuk ke sistem','2026-05-20 01:26:28'),(69,22,NULL,'logout','bosku keluar dari sistem','2026-05-20 01:27:30'),(70,1,NULL,'login','admin_utama masuk ke sistem','2026-05-20 01:27:39'),(71,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-20 01:29:26'),(72,NULL,NULL,'login','helo masuk ke sistem','2026-05-20 01:29:32'),(73,NULL,NULL,'logout','helo keluar dari sistem','2026-05-20 01:29:39'),(74,NULL,NULL,'login','kevin masuk ke sistem','2026-05-20 01:29:46'),(75,NULL,NULL,'logout','kevin keluar dari sistem','2026-05-20 01:31:26'),(76,NULL,NULL,'login','admin_luqman masuk ke sistem','2026-05-20 01:48:03'),(77,NULL,NULL,'logout','admin_luqman keluar dari sistem','2026-05-20 01:49:08'),(78,1,NULL,'login','admin_utama masuk ke sistem','2026-05-20 01:49:16'),(79,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-20 01:49:37'),(80,22,NULL,'login','bosku masuk ke sistem','2026-05-20 01:50:13'),(81,22,NULL,'logout','bosku keluar dari sistem','2026-05-20 01:50:16'),(82,NULL,NULL,'login','helo masuk ke sistem','2026-05-20 01:50:24'),(83,NULL,NULL,'logout','helo keluar dari sistem','2026-05-20 01:50:27'),(84,1,NULL,'login','admin_utama masuk ke sistem','2026-05-20 01:50:35'),(85,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-20 01:50:39'),(86,NULL,NULL,'login','helo masuk ke sistem','2026-05-20 01:50:45'),(87,NULL,NULL,'logout','helo keluar dari sistem','2026-05-20 01:50:47'),(88,24,NULL,'login','tes masuk ke sistem','2026-05-20 01:51:28'),(89,24,NULL,'logout','tes keluar dari sistem','2026-05-20 01:51:31'),(90,1,NULL,'login','admin_utama masuk ke sistem','2026-05-20 01:51:41'),(91,26,NULL,'login','akunbaru masuk ke sistem','2026-05-20 08:32:39'),(92,26,NULL,'logout','akunbaru keluar dari sistem','2026-05-20 08:34:05'),(93,27,NULL,'login','azizkun masuk ke sistem','2026-05-20 08:35:23'),(94,1,NULL,'login','admin_utama masuk ke sistem','2026-05-20 12:44:41'),(95,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-20 13:02:22'),(96,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-20 13:02:36'),(97,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-20 14:39:09'),(98,3,16,'login','Member Ibu Ahmad masuk ke sistem','2026-05-20 14:39:15'),(99,3,16,'login','Member Ibu Ahmad masuk ke sistem','2026-05-20 14:39:52'),(100,3,16,'logout','Member Ibu Ahmad keluar dari sistem','2026-05-20 14:55:05'),(101,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-20 14:56:07'),(102,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-20 14:56:26'),(103,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-20 14:59:50'),(104,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-20 15:01:34'),(105,1,NULL,'login','admin_utama masuk ke sistem','2026-05-20 15:01:41'),(106,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-20 15:01:58'),(107,NULL,NULL,'login','hasss masuk ke sistem','2026-05-20 15:02:39'),(108,NULL,NULL,'logout','hasss keluar dari sistem','2026-05-20 15:02:56'),(109,3,16,'login','Member Ibu Ahmad masuk ke sistem','2026-05-20 15:03:03'),(110,3,16,'logout','Member Ibu Ahmad keluar dari sistem','2026-05-20 15:03:10'),(111,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-20 15:03:19'),(112,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-20 15:03:54'),(113,3,16,'login','Member Ibu Ahmad masuk ke sistem','2026-05-20 15:04:01'),(114,3,16,'logout','Member Ibu Ahmad keluar dari sistem','2026-05-20 15:04:07'),(115,1,NULL,'login','admin_utama masuk ke sistem','2026-05-21 01:03:10'),(116,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-21 01:03:36'),(117,3,16,'login','Member Ibu Ahmad masuk ke sistem','2026-05-21 01:03:53'),(118,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-21 01:10:11'),(119,1,NULL,'login','admin_utama masuk ke sistem','2026-05-21 02:10:04'),(120,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-21 02:23:03'),(121,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-21 02:23:13'),(122,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-21 02:23:34'),(123,3,16,'login','Member Ibu Ahmad masuk ke sistem','2026-05-21 02:23:44'),(124,3,16,'logout','Member Ibu Ahmad keluar dari sistem','2026-05-21 02:32:07'),(125,1,NULL,'login','admin_utama masuk ke sistem','2026-05-21 02:32:14'),(126,1,NULL,'logout','admin_utama keluar dari sistem','2026-05-21 03:16:53'),(127,3,NULL,'login','keluarga_bapak_ahmad masuk ke sistem','2026-05-21 03:17:09'),(128,3,NULL,'login','Anggota Keluarga Ibu Ahmad berhasil masuk.','2026-05-21 03:55:35'),(129,3,NULL,'logout','keluarga_bapak_ahmad keluar dari sistem','2026-05-21 04:02:12'),(130,1,NULL,'login','User admin_utama berhasil masuk.','2026-05-21 04:02:19'),(131,1,NULL,'logout',' keluar dari sistem','2026-05-21 04:02:25'),(132,3,NULL,'login','User keluarga_bapak_ahmad berhasil masuk.','2026-05-21 04:02:31'),(133,3,NULL,'login','User keluarga_bapak_ahmad berhasil masuk.','2026-05-21 06:12:38'),(134,3,NULL,'login','Anggota Keluarga Ibu Ahmad berhasil masuk.','2026-05-21 06:13:08'),(135,3,NULL,'login','Ahmad Rizki login ke sistem','2026-05-21 06:50:19'),(136,3,NULL,'logout','Ahmad Rizki logout dari sistem','2026-05-21 07:08:36'),(137,3,NULL,'login','User keluarga_bapak_ahmad berhasil masuk.','2026-05-21 07:08:46'),(138,3,NULL,'login','User keluarga_bapak_ahmad berhasil masuk.','2026-05-21 07:23:47'),(139,3,NULL,'login','Anggota Keluarga Ibu Ahmad berhasil masuk.','2026-05-21 07:26:09'),(140,3,NULL,'login','Anggota Keluarga Ibu Ahmad berhasil masuk.','2026-05-21 07:26:24'),(141,3,NULL,'login','User keluarga_bapak_ahmad berhasil masuk.','2026-05-21 07:57:35'),(142,3,NULL,'login','User keluarga_bapak_ahmad berhasil masuk.','2026-05-22 00:45:06'),(143,3,NULL,'logout',' keluar dari sistem','2026-05-22 00:46:40'),(144,1,NULL,'login','User admin_utama berhasil masuk.','2026-05-22 00:46:48'),(145,1,NULL,'logout',' keluar dari sistem','2026-05-22 00:52:53'),(146,3,NULL,'login','Anggota Keluarga Ibu Ahmad berhasil masuk.','2026-05-22 00:53:11'),(147,3,NULL,'login','User keluarga_bapak_ahmad berhasil masuk.','2026-05-22 00:54:35'),(148,3,16,'logout','Member Ibu Ahmad keluar dari sistem','2026-05-22 00:55:56'),(149,1,NULL,'login','User admin_utama berhasil masuk.','2026-05-22 01:05:25'),(150,1,NULL,'logout',' keluar dari sistem','2026-05-22 01:05:31'),(151,1,NULL,'login','User admin_utama berhasil masuk.','2026-05-22 01:05:44'),(152,1,NULL,'logout',' keluar dari sistem','2026-05-22 01:06:08'),(153,3,NULL,'logout',' keluar dari sistem','2026-05-22 01:20:08'),(154,3,NULL,'login','Anggota Keluarga Ibu Ahmad berhasil masuk.','2026-05-22 01:20:13'),(155,3,NULL,'login','User keluarga_bapak_ahmad berhasil masuk.','2026-05-22 01:20:26');

DROP TABLE IF EXISTS `assigned_tasks`;

CREATE TABLE `assigned_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `assigned_to` varchar(100) NOT NULL,
  `nama_tugas` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Pending','Selesai') DEFAULT 'Pending',
  `is_random` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `approval_status` varchar(50) DEFAULT 'Belum Selesai',
  `bukti_foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `assigned_tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `assigned_tasks` VALUES (29,3,'Zahid','Nyiram Tanaman','2026-05-21','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(30,3,'Ibu Ahmad','Cuci Motor','2026-05-21','Selesai',1,'2026-05-21 04:08:03','Disetujui','6a0ea82160bd8.jpg'),(31,3,'Ahmad Rizki','Cuci Mobil','2026-05-21','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(32,3,'Zahid','Belanja Mobil','2026-05-21','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(33,3,'Ibu Ahmad','Nyiram Tanaman','2026-05-22','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(34,3,'Ahmad Rizki','Cuci Motor','2026-05-22','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(35,3,'Zahid','Cuci Mobil','2026-05-22','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(36,3,'Ibu Ahmad','Belanja Mobil','2026-05-22','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(37,3,'Ahmad Rizki','Nyiram Tanaman','2026-05-23','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(38,3,'Zahid','Cuci Motor','2026-05-23','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(39,3,'Ibu Ahmad','Cuci Mobil','2026-05-23','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(40,3,'Ahmad Rizki','Belanja Mobil','2026-05-23','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(41,3,'Zahid','Nyiram Tanaman','2026-05-24','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(42,3,'Ibu Ahmad','Cuci Motor','2026-05-24','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(43,3,'Ahmad Rizki','Cuci Mobil','2026-05-24','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(44,3,'Zahid','Belanja Mobil','2026-05-24','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(45,3,'Ibu Ahmad','Nyiram Tanaman','2026-05-25','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(46,3,'Ahmad Rizki','Cuci Motor','2026-05-25','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(47,3,'Zahid','Cuci Mobil','2026-05-25','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(48,3,'Ibu Ahmad','Belanja Mobil','2026-05-25','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(49,3,'Ahmad Rizki','Nyiram Tanaman','2026-05-26','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(50,3,'Zahid','Cuci Motor','2026-05-26','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(51,3,'Ibu Ahmad','Cuci Mobil','2026-05-26','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(52,3,'Ahmad Rizki','Belanja Mobil','2026-05-26','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(53,3,'Zahid','Nyiram Tanaman','2026-05-27','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(54,3,'Ibu Ahmad','Cuci Motor','2026-05-27','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(55,3,'Ahmad Rizki','Cuci Mobil','2026-05-27','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL),(56,3,'Zahid','Belanja Mobil','2026-05-27','Pending',1,'2026-05-21 04:08:03','Belum Selesai',NULL);

DROP TABLE IF EXISTS `family_members`;

CREATE TABLE `family_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role_dalam_keluarga` varchar(50) DEFAULT 'Anggota',
  `status` tinyint(1) DEFAULT '1',
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT 'default.png',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `family_members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `family_members` VALUES (9,2,'Ibu Siti','Istri',1,'ibu_siti','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png'),(10,2,'Anak Pertama','Anak',1,'anak_pertama','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png'),(11,2,'Anak Kedua','Anak',1,'anak_kedua','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png'),(16,3,'Ibu Ahmad','Istri',1,'ibu_ahmad','$2y$10$5Zl6tsAXXKXcUeurB7b4POomNUBnK5rwoR/MW4YOI8vWyWzCdqVxO','6a0fb5b4536e1.png'),(18,3,'Zahid','adik',1,'zahid','$2y$10$e2NyllSbPQYTbHn8AYvJqOHZjj88qASKt4O8uadN1QhyZP9cxsGKq','default.png');

DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `lokasi_detail` varchar(200) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `items` VALUES (1,3,'Dyson V15 Detect','Gudang Peralatan','2026-04-14 14:37:28','2026-04-14 14:37:28'),(2,3,'Nespresso Vertuo','Dapur > Meja Utama','2026-04-14 14:37:28','2026-04-14 14:37:28'),(3,3,'PlayStation 5','Ruang Tamu','2026-04-14 14:37:28','2026-04-14 14:37:28'),(4,3,'Kotak P3K','Lemari Dapur Atas','2026-04-14 14:37:28','2026-04-14 14:37:28'),(6,4,'Robot Vacuum Cleaner','Ruang Tamu > Sudut Kiri','2026-04-14 14:37:28','2026-04-14 14:37:28'),(7,4,'Mixer Philips','Dapur > Rak Bawah','2026-04-14 14:37:28','2026-04-14 14:37:28'),(8,4,'Obeng Set 24 in 1','Garasi > Lemari Alat','2026-04-14 14:37:28','2026-04-14 14:37:28'),(9,4,'Rak Buku Kayu','Kamar Tidur Utama','2026-04-14 14:37:28','2026-04-14 14:37:28'),(10,4,'Dispenser Air','Dapur > Sudut Kanan','2026-04-14 14:37:28','2026-04-14 14:37:28'),(11,5,'Blender Panasonic','Dapur > Meja Tengah','2026-04-14 14:37:28','2026-04-14 14:37:28'),(12,5,'Sepatu Gunung','Lemari Masuk > Rak Bawah','2026-04-14 14:37:28','2026-04-14 14:37:28'),(13,5,'Kamera DSLR Canon','Ruang Kerja > Rak Atas','2026-04-14 14:37:28','2026-04-14 14:37:28'),(14,5,'Alat Jahit Portable','Kamar Tidur > Laci Meja','2026-04-14 14:37:28','2026-04-14 14:37:28'),(15,5,'Tenda Camping 4 Orang','Gudang Belakang > Rak Tinggi','2026-04-14 14:37:28','2026-04-14 14:37:28'),(17,3,'HP jadul','Meja Hijau','2026-05-17 05:47:27','2026-05-17 05:47:27'),(19,3,'Teko','Kamar Mandi','2026-05-21 03:20:29','2026-05-21 03:20:29');

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `member_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `family_members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `notifications` VALUES (1,NULL,16,'Tugas Pribadi Baru','Admin memberikan tugas baru: Nyapu Kamar Depan. Silakan cek di Dashboard Anda.',1,'2026-05-22 01:20:57'),(2,3,NULL,'Permintaan Persetujuan','Member Anda Ibu Ahmad Meminta Persetujuan Penyelesaian Tugas pribadi: Nyapu Kamar Depan',1,'2026-05-22 01:22:06');

DROP TABLE IF EXISTS `task_pool`;

CREATE TABLE `task_pool` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `nama_tugas` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `task_pool_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `task_pool` VALUES (6,3,'Nyiram Tanaman','2026-05-16 06:56:08'),(7,3,'Cuci Motor','2026-05-17 07:08:37'),(8,3,'Cuci Mobil','2026-05-17 07:08:44'),(9,3,'Belanja Mobil','2026-05-21 01:13:50');

DROP TABLE IF EXISTS `tasks`;

CREATE TABLE `tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `nama_tugas` varchar(150) NOT NULL,
  `prioritas` enum('Rendah','Sedang','Tinggi') DEFAULT 'Sedang',
  `assigned_to` varchar(100) DEFAULT 'Belum Ditugaskan',
  `jadwal` date DEFAULT NULL,
  `status` enum('Pending','Progress','Selesai','Dibatalkan') DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `approval_status` enum('Belum Selesai','Menunggu Persetujuan','Disetujui') DEFAULT 'Belum Selesai',
  `bukti_foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `tasks` VALUES (2,3,'Masak nasi','Tinggi','Adik Mamad','2026-05-18','Selesai','2026-05-17 06:09:08','Belum Selesai',NULL),(3,3,'Nyuci Rumah','Sedang','Ayah','2026-05-30','Selesai','2026-05-20 13:03:07','Belum Selesai',NULL),(4,3,'Masak Nasi','Sedang','Luqman','2026-05-22','Selesai','2026-05-21 01:11:37','Belum Selesai',NULL),(5,3,'Masak nasi','Rendah','Ahmad Rizki','2026-05-22','Selesai','2026-05-21 03:25:47','Disetujui',NULL),(6,3,'Kotak Amal Masjidil Haram','Tinggi','Ibu Ahmad','2026-05-29','Selesai','2026-05-21 03:56:06','Disetujui',NULL),(7,3,'Masak nasi','Rendah','Ibu Ahmad','2026-05-22','Selesai','2026-05-21 04:07:27','Disetujui','6a0fa92eb8419.png'),(8,3,'Nyapu Kamar Depan','Rendah','Ibu Ahmad','2026-05-23','Selesai','2026-05-22 01:20:57','Disetujui','6a0fafbec89c9.png');

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nama_rumah_tangga` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `status_aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `foto_profil` varchar(255) DEFAULT 'default.png',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` VALUES (1,'admin_utama','$2y$10$klnX6iKMuCIbKKeL7XWaaeKg1FFy0ImVK8YqkCJ/eEl2QmCmNUkl2','Admin Utama','Admin System','admin',1,'2026-04-14 14:37:28','default.png'),(2,'admin_budi','$2y$10$NFLCxa1YtcMfutpd9gZq/.390366D683YaC0YIhlWRmud.45lrKmS','Budi Santoso','Admin Support','admin',1,'2026-04-14 14:37:28','default.png'),(3,'keluarga_bapak_ahmad','$2y$10$klnX6iKMuCIbKKeL7XWaaeKg1FFy0ImVK8YqkCJ/eEl2QmCmNUkl2','Ahmad Rizki','Keluarga Ahmad','user',1,'2026-04-14 14:37:28','6a0fb59652f85.png'),(4,'keluarga_bapak_doni','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Doni Prasetyo','Keluarga Doni','user',1,'2026-04-14 14:37:28','default.png'),(5,'keluarga_bapak_reza','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Reza Firmansyah','Keluarga Reza','user',1,'2026-04-14 14:37:28','default.png'),(12,'ahmad','ahmad','ahmad','ahmad','admin',1,'2026-05-15 14:51:12','default.png'),(13,'fdhil273','$2y$10$29qlua0TrHiSiv3ShzM.BuA18fhgXlZI3gfsvmdzXK4rGMLnXyVyu','nama lengkap','rumah ','user',1,'2026-05-18 02:00:12','default.png'),(14,'manman','$2y$10$.OsmUhTKtYXQ49vz2rvD.uHb7BulbCUFCKvRXqw9khHJPiIWDPHse','manman','manman','user',1,'2026-05-18 02:02:47','default.png'),(15,'adek','$2y$10$yOp7uojmGznFoMbXoYCLHue5i8kT.Vop9S4/BUA3vp1xaPcVCMfga','adek','adek','user',1,'2026-05-18 02:17:22','default.png'),(16,'Aziz','$2y$10$PWi7GHBFvUSJo3tdRsWvkuhU/dqjNbchEmzHEOdOdkJwLqzC8RrJq','Aziz_Walawe','Mantap','user',1,'2026-05-18 02:19:37','default.png'),(20,'myname','$2y$10$fddLdutY6rWpM5Sb6OBce.S1hhNv92UCLMHmcmyu/rYR35bdduExO','myname','myname','user',1,'2026-05-20 00:36:30','default.png'),(22,'bosku','$2y$10$KOct.gDKPVvjzgxDuHdiWuvREuufifbQtlCB7eGYSb6UxaNNOIazC','bosku','bosku','user',1,'2026-05-20 01:26:16','6a0d0db8d3c94.jpg'),(24,'tes','$2y$10$dlz4N1OiVTctPBztaajsvuFNwf0ZQ/LlK4jGsRJ1dUTc7pAEG02C2','tes','tes','user',1,'2026-05-20 01:51:01','default.png'),(25,'tesakun','$2y$10$ILLgdln/17H0ERmtBxnmyu9cBLz91TSQzZrGg8papoaNFk0fCtcOS','tesakun','tesakun','user',1,'2026-05-20 08:31:36','6a0d716912dec.png'),(26,'akunbaru','$2y$10$XcLOZaHb.EBLty/4c24M6euqTNovZ5nFUbIBwDNGyjxO1VM6vPuPS','akunbaru','akunbaru','user',1,'2026-05-20 08:32:31','6a0d719fb6abf.png'),(27,'azizkun','$2y$10$71SPXSKYK0LZT7IcEmxydeGIHwwvRSVwULPleoBeZAiPcPY4LD56K','azizkun','azizkun','user',1,'2026-05-20 08:35:17','6a0d72457d930.png');