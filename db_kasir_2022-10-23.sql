# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.4.21-MariaDB)
# Database: db_kasir
# Generation Time: 2022-10-23 00:59:36 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table tb_currency
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tb_currency`;

CREATE TABLE `tb_currency` (
  `id_currency` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama_currency` varchar(200) NOT NULL DEFAULT '',
  `country` varchar(255) DEFAULT NULL,
  `harga_currency` float(20,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table tb_detail_transaksi
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tb_detail_transaksi`;

CREATE TABLE `tb_detail_transaksi` (
  `id_detail_transaksi` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_transaksi` int(11) unsigned DEFAULT NULL,
  `currency_id` int(11) unsigned DEFAULT NULL,
  `jumlah_currency` decimal(10,0) DEFAULT NULL,
  `jumlah_tukar` int(20) DEFAULT NULL,
  `total_tukar` float(20,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail_transaksi`),
  KEY `FOREIGN` (`id_transaksi`,`currency_id`),
  KEY `Detail -> Currency` (`currency_id`),
  CONSTRAINT `Detail -> Currency` FOREIGN KEY (`currency_id`) REFERENCES `tb_currency` (`id_currency`) ON DELETE CASCADE,
  CONSTRAINT `Detail -> Transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table tb_jurnal
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tb_jurnal`;

CREATE TABLE `tb_jurnal` (
  `id_jurnal` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_pegawai` int(11) unsigned DEFAULT NULL,
  `id_transaksi` int(11) unsigned DEFAULT NULL,
  `id_modal` int(11) unsigned DEFAULT NULL,
  `id_currency` int(11) unsigned DEFAULT NULL,
  `kurs` float(20,2) DEFAULT NULL,
  `jumlah_tukar` int(20) DEFAULT NULL,
  `jumlah_modal` decimal(10,0) DEFAULT NULL,
  `tanggal_jurnal` date DEFAULT NULL,
  `total_tukar` float(20,2) DEFAULT NULL,
  `jenis_jurnal` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_jurnal`),
  KEY `FOREIGN` (`id_pegawai`,`id_currency`,`id_transaksi`,`id_modal`),
  KEY `Jurnal -> Currency` (`id_currency`),
  KEY `Jurnal -> Transaksi` (`id_transaksi`),
  KEY `Jurnal -> Modal` (`id_modal`),
  CONSTRAINT `Jurnal -> Currency` FOREIGN KEY (`id_currency`) REFERENCES `tb_currency` (`id_currency`) ON DELETE CASCADE,
  CONSTRAINT `Jurnal -> Modal` FOREIGN KEY (`id_modal`) REFERENCES `tb_modal_transaksi` (`id_modal`) ON DELETE CASCADE,
  CONSTRAINT `Jurnal -> Transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`) ON DELETE CASCADE,
  CONSTRAINT `Jurnal -> User` FOREIGN KEY (`id_pegawai`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table tb_modal_transaksi
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tb_modal_transaksi`;

CREATE TABLE `tb_modal_transaksi` (
  `id_modal` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_pegawai` int(11) unsigned NOT NULL,
  `jumlah_modal` decimal(10,0) DEFAULT NULL,
  `tanggal_modal` date DEFAULT NULL,
  `status_modal` varchar(255) DEFAULT NULL,
  `riwayat_modal` decimal(10,0) DEFAULT NULL,
  `pengajuan_tambah` decimal(10,0) DEFAULT NULL,
  `keterangan_approval` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `jenis_modal` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_modal`),
  KEY `FOREIGN` (`id_pegawai`),
  CONSTRAINT `Modal -> Users` FOREIGN KEY (`id_pegawai`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table tb_transaksi
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tb_transaksi`;

CREATE TABLE `tb_transaksi` (
  `id_transaksi` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_pegawai` int(11) unsigned DEFAULT NULL,
  `id_modal` int(11) unsigned DEFAULT NULL,
  `tanggal_transaksi` date DEFAULT NULL,
  `kode_transaksi` varchar(50) DEFAULT NULL,
  `total` decimal(10,0) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_transaksi`),
  KEY `FOREIGN` (`id_pegawai`,`id_modal`),
  KEY `Transaksi -> Modal` (`id_modal`),
  CONSTRAINT `Transaksi -> Modal` FOREIGN KEY (`id_modal`) REFERENCES `tb_modal_transaksi` (`id_modal`) ON DELETE CASCADE,
  CONSTRAINT `Transaksi -> Pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `nama_panggilan` varchar(100) DEFAULT '',
  `jenis_kelamin` varchar(100) DEFAULT '',
  `phone_number` char(16) DEFAULT '',
  `alamat` text DEFAULT NULL,
  `role` enum('Owner','Pegawai') DEFAULT 'Owner',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `nama_panggilan`, `jenis_kelamin`, `phone_number`, `alamat`, `role`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,'Adim','adimertap@gmail.com','$2a$04$LLfrCfqMdQu/u1L9hAwl5OaGag4Cvy7y4emccAUx72U4.DRJvqbTW','Adim','Laki-Laki','081246602400','Tabanan','Owner','2022-10-23 08:46:07',NULL,NULL,'2022-10-23 08:46:07','2022-10-23 08:46:07');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
