-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 17, 2026 at 04:19 PM
-- Server version: 8.0.30
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uji_kelayakan`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id_log` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `aksi` varchar(100) DEFAULT NULL,
  `tabel` varchar(50) DEFAULT NULL,
  `id_ref` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id_log`, `id_user`, `aksi`, `tabel`, `id_ref`, `created_at`) VALUES
(1, 1, 'login', 'users', 1, '2026-03-11 21:49:23'),
(2, 1, 'login', 'users', 1, '2026-03-12 08:25:15'),
(3, 1, 'buat_pengajuan', 'pengajuan_uji', 7, '2026-03-12 09:02:59'),
(4, 1, 'approve_dept_manager', 'pengajuan_uji', 7, '2026-03-12 09:04:01'),
(5, 1, 'approve_admin_ohs', 'pengajuan_uji', 7, '2026-03-12 09:04:24'),
(6, 1, 'buat_jadwal', 'jadwal_uji', 3, '2026-03-12 09:19:37'),
(7, 1, 'submit_inspeksi', 'uji_kelayakan', 3, '2026-03-12 09:21:01'),
(8, 1, 'approve_admin_ohs_hasil', 'pengajuan_uji', 7, '2026-03-12 09:38:31'),
(9, 1, 'approve_ohs_supt', 'pengajuan_uji', 7, '2026-03-12 09:38:40'),
(10, 1, 'approve_ktt', 'pengajuan_uji', 7, '2026-03-12 09:39:15'),
(11, 1, 'release_stiker', 'pengajuan_uji', 7, '2026-03-12 09:39:37'),
(12, 1, 'login', 'users', 1, '2026-03-25 09:39:43'),
(13, 1, 'approve_dept_manager', 'pengajuan_uji', 3, '2026-03-25 09:46:40'),
(14, 1, 'approve_admin_ohs', 'pengajuan_uji', 3, '2026-03-25 09:46:48'),
(15, 1, 'buat_jadwal', 'jadwal_uji', 4, '2026-03-25 09:47:01'),
(16, 1, 'submit_inspeksi', 'uji_kelayakan', 4, '2026-03-25 09:47:51'),
(17, 1, 'reject_admin_ohs_hasil', 'pengajuan_uji', 3, '2026-03-25 09:58:03'),
(18, 1, 'logout', 'users', 1, '2026-03-25 09:59:36'),
(19, 1, 'login', 'users', 1, '2026-03-26 10:29:51'),
(20, 1, 'logout', 'users', 1, '2026-03-26 10:35:40'),
(21, 1, 'login', 'users', 1, '2026-03-26 10:41:34'),
(22, 1, 'login', 'users', 1, '2026-03-26 20:58:45'),
(23, 1, 'buat_pengajuan', 'pengajuan_uji', 8, '2026-03-26 21:01:41'),
(24, 1, 'buat_pengajuan', 'pengajuan_uji', 9, '2026-03-26 21:03:50'),
(25, 1, 'buat_pengajuan', 'pengajuan_uji', 10, '2026-03-26 21:09:44'),
(26, 1, 'buat_pengajuan', 'pengajuan_uji', 11, '2026-03-26 21:12:55'),
(27, 1, 'login', 'users', 1, '2026-03-27 07:15:07'),
(28, 1, 'approve_dept_manager', 'pengajuan_uji', 8, '2026-03-27 07:29:22'),
(29, 1, 'approve_admin_ohs', 'pengajuan_uji', 8, '2026-03-27 07:29:28'),
(30, 1, 'buat_jadwal', 'jadwal_uji', 5, '2026-03-27 07:29:44'),
(31, 1, 'submit_inspeksi', 'uji_kelayakan', 5, '2026-03-27 07:34:43'),
(32, 1, 'approve_admin_ohs_hasil', 'pengajuan_uji', 8, '2026-03-27 07:35:04'),
(33, 1, 'approve_ohs_supt', 'pengajuan_uji', 8, '2026-03-27 07:35:08'),
(34, 1, 'approve_ktt', 'pengajuan_uji', 8, '2026-03-27 07:35:14'),
(35, 1, 'release_stiker', 'pengajuan_uji', 8, '2026-03-27 07:35:30'),
(36, 1, 'logout', 'users', 1, '2026-03-27 07:36:06'),
(37, 1, 'login', 'users', 1, '2026-04-01 22:21:48'),
(38, 1, 'buat_pengajuan', 'pengajuan_uji', 12, '2026-04-01 22:23:16'),
(39, 1, 'buat_pengajuan', 'pengajuan_uji', 13, '2026-04-01 22:25:18'),
(40, 1, 'approve_dept_manager', 'pengajuan_uji', 13, '2026-04-01 22:35:44'),
(41, 1, 'login', 'users', 1, '2026-04-05 20:14:24'),
(42, 1, 'logout', 'users', 1, '2026-04-05 20:20:37'),
(43, 1, 'login', 'users', 1, '2026-04-05 21:11:32'),
(44, 1, 'buat_pengajuan', 'pengajuan_uji', 14, '2026-04-05 21:12:52'),
(45, 1, 'login', 'users', 1, '2026-04-07 20:57:23'),
(46, 1, 'approve_admin_ohs', 'pengajuan_uji', 13, '2026-04-07 20:59:01'),
(47, 1, 'buat_jadwal', 'jadwal_uji', 6, '2026-04-07 20:59:12'),
(48, 1, 'submit_inspeksi', 'uji_kelayakan', 6, '2026-04-07 21:00:06'),
(49, 1, 'reject_admin_ohs_hasil', 'pengajuan_uji', 13, '2026-04-07 21:05:52'),
(50, 1, 'login', 'users', 1, '2026-04-08 10:44:26'),
(51, 1, 'approve_dept_manager', 'pengajuan_uji', 14, '2026-04-08 10:52:00'),
(52, 1, 'approve_admin_ohs', 'pengajuan_uji', 14, '2026-04-08 10:52:14'),
(53, 1, 'buat_jadwal', 'jadwal_uji', 7, '2026-04-08 10:52:34'),
(54, 1, 'submit_inspeksi', 'uji_kelayakan', 7, '2026-04-08 10:53:14'),
(55, 1, 'login', 'users', 1, '2026-04-08 22:45:59'),
(56, 1, 'approve_dept_manager', 'pengajuan_uji', 13, '2026-04-08 22:52:36'),
(57, 1, 'login', 'users', 1, '2026-04-09 19:31:12'),
(58, 1, 'login', 'users', 1, '2026-04-10 19:00:19'),
(59, 1, 'login', 'users', 1, '2026-04-13 11:39:51'),
(60, 1, 'login', 'users', 1, '2026-04-13 15:22:18'),
(61, 1, 'buat_pengajuan', 'pengajuan_uji', 15, '2026-04-13 16:20:57'),
(62, 1, 'approve_dept_manager', 'pengajuan_uji', 15, '2026-04-13 16:21:15'),
(63, 1, 'approve_admin_ohs', 'pengajuan_uji', 15, '2026-04-13 16:21:41'),
(64, 1, 'buat_jadwal', 'jadwal_uji', 8, '2026-04-13 16:34:15'),
(65, 1, 'submit_inspeksi', 'uji_kelayakan', 8, '2026-04-13 16:36:24'),
(66, 1, 'approve_ohs_supt', 'pengajuan_uji', 15, '2026-04-13 16:36:36'),
(67, 1, 'approve_ktt', 'pengajuan_uji', 15, '2026-04-13 16:36:46'),
(68, 1, 'release_stiker', 'pengajuan_uji', 15, '2026-04-13 16:37:01'),
(69, 1, 'approve_dept_manager', 'pengajuan_uji', 12, '2026-04-13 16:37:54'),
(70, 1, 'approve_admin_ohs', 'pengajuan_uji', 13, '2026-04-13 16:38:06'),
(71, 1, 'buat_jadwal', 'jadwal_uji', 9, '2026-04-13 16:46:06'),
(72, 1, 'submit_inspeksi', 'uji_kelayakan', 6, '2026-04-13 16:47:18'),
(73, 1, 'login', 'users', 1, '2026-04-13 19:42:23'),
(74, 1, 'approve_ohs_supt', 'pengajuan_uji', 13, '2026-04-13 19:42:37'),
(75, 1, 'buat_pengajuan', 'pengajuan_uji', 16, '2026-04-13 19:49:24'),
(76, 1, 'approve_dept_manager', 'pengajuan_uji', 16, '2026-04-13 19:49:29'),
(77, 1, 'approve_admin_ohs', 'pengajuan_uji', 16, '2026-04-13 19:49:43'),
(78, 1, 'buat_jadwal', 'jadwal_uji', 10, '2026-04-13 19:50:09'),
(79, 1, 'logout', 'users', 1, '2026-04-13 19:53:46'),
(80, 1, 'login', 'users', 1, '2026-04-13 20:16:44'),
(81, 1, 'submit_inspeksi', 'uji_kelayakan', 9, '2026-04-13 22:53:10'),
(82, 1, 'approve_dept_manager', 'pengajuan_uji', 16, '2026-04-13 22:54:00'),
(83, 1, 'approve_admin_ohs', 'pengajuan_uji', 16, '2026-04-13 22:55:37'),
(84, 1, 'buat_jadwal', 'jadwal_uji', 11, '2026-04-13 22:56:07'),
(85, 1, 'submit_inspeksi', 'uji_kelayakan', 9, '2026-04-13 22:58:06'),
(86, 1, 'approve_ohs_supt', 'pengajuan_uji', 16, '2026-04-13 22:58:44'),
(87, 1, 'approve_ktt', 'pengajuan_uji', 16, '2026-04-13 22:59:09'),
(88, 1, 'release_stiker', 'pengajuan_uji', 16, '2026-04-13 23:00:26'),
(89, 1, 'approve_ktt', 'pengajuan_uji', 13, '2026-04-13 23:01:07'),
(90, 1, 'buat_pengajuan', 'pengajuan_uji', 17, '2026-04-13 23:10:37'),
(91, 1, 'approve_dept_manager', 'pengajuan_uji', 17, '2026-04-13 23:17:06'),
(92, 1, 'approve_admin_ohs', 'pengajuan_uji', 17, '2026-04-13 23:17:19'),
(93, 1, 'buat_pengajuan', 'pengajuan_uji', 18, '2026-04-13 23:22:39'),
(94, 1, 'approve_dept_manager', 'pengajuan_uji', 18, '2026-04-13 23:22:44'),
(95, 1, 'approve_admin_ohs', 'pengajuan_uji', 18, '2026-04-13 23:22:54'),
(96, 1, 'buat_jadwal', 'jadwal_uji', 12, '2026-04-13 23:23:08'),
(97, 1, 'submit_inspeksi', 'uji_kelayakan', 10, '2026-04-13 23:33:56'),
(98, 1, 'reject_ohs_supt', 'pengajuan_uji', 18, '2026-04-13 23:34:40'),
(99, 1, 'login', 'users', 1, '2026-04-14 14:01:37'),
(100, 1, 'logout', 'users', 1, '2026-04-14 14:03:14'),
(101, 1, 'login', 'users', 1, '2026-04-14 14:03:40'),
(102, 1, 'logout', 'users', 1, '2026-04-14 14:04:04'),
(103, 7, 'login', 'users', 7, '2026-04-14 14:04:12'),
(104, 7, 'logout', 'users', 7, '2026-04-14 14:07:54'),
(105, 1, 'login', 'users', 1, '2026-04-14 14:08:07'),
(106, 1, 'logout', 'users', 1, '2026-04-14 14:13:18'),
(107, 2, 'login', 'users', 2, '2026-04-14 14:13:30'),
(108, 1, 'login', 'users', 1, '2026-04-15 11:26:15'),
(109, 1, 'login', 'users', 1, '2026-04-15 18:45:35'),
(110, 1, 'buat_pengajuan', 'pengajuan_uji', 19, '2026-04-15 18:49:52'),
(111, 1, 'buat_jadwal', 'jadwal_uji', 13, '2026-04-15 18:54:11'),
(112, 1, 'approve_dept_manager', 'pengajuan_uji', 19, '2026-04-15 18:54:48'),
(113, 1, 'approve_admin_ohs', 'pengajuan_uji', 19, '2026-04-15 18:55:12'),
(114, 1, 'login', 'users', 1, '2026-04-15 22:05:00'),
(115, 1, 'login', 'users', 1, '2026-04-16 05:29:07'),
(116, 1, 'login', 'users', 1, '2026-04-16 09:15:45'),
(117, 1, 'submit_inspeksi', 'uji_kelayakan', 11, '2026-04-16 09:19:46'),
(118, 1, 'approve_ohs_supt', 'pengajuan_uji', 17, '2026-04-16 09:19:57'),
(119, 1, 'approve_ktt', 'pengajuan_uji', 17, '2026-04-16 09:20:11'),
(120, 1, 'login', 'users', 1, '2026-04-16 13:47:44'),
(121, 1, 'buat_jadwal', 'jadwal_uji', 14, '2026-04-16 13:48:00'),
(122, 1, 'login', 'users', 1, '2026-04-19 22:48:25'),
(123, 1, 'login', 'users', 1, '2026-04-21 22:03:26'),
(124, 1, 'logout', 'users', 1, '2026-04-21 22:50:58'),
(125, 1, 'login', 'users', 1, '2026-04-21 22:52:53'),
(126, 1, 'buat_pengajuan', 'pengajuan_uji', 20, '2026-04-21 22:56:28'),
(127, 1, 'approve_dept_manager', 'pengajuan_uji', 20, '2026-04-21 22:56:40'),
(128, 1, 'approve_admin_ohs', 'pengajuan_uji', 20, '2026-04-21 22:57:01'),
(129, 1, 'buat_jadwal', 'jadwal_uji', 15, '2026-04-21 23:02:17'),
(130, 1, 'login', 'users', 1, '2026-04-22 09:32:01'),
(131, 1, 'login', 'users', 1, '2026-04-22 12:35:36'),
(132, 1, 'buat_pengajuan', 'pengajuan_uji', 21, '2026-04-22 12:47:12'),
(133, 1, 'approve_dept_manager', 'pengajuan_uji', 21, '2026-04-22 12:47:25'),
(134, 1, 'approve_admin_ohs', 'pengajuan_uji', 21, '2026-04-22 12:47:40'),
(135, 1, 'buat_jadwal', 'jadwal_uji', 16, '2026-04-22 12:47:52'),
(136, 1, 'submit_inspeksi', 'uji_kelayakan', 12, '2026-04-22 12:49:13'),
(137, 1, 'submit_inspeksi', 'uji_kelayakan', 13, '2026-04-22 12:50:43'),
(138, 1, 'resubmit_pengajuan', 'pengajuan_uji', 20, '2026-04-22 12:51:29'),
(139, 1, 'reject_dept_manager', 'pengajuan_uji', 20, '2026-04-22 12:54:38'),
(140, 1, 'login', 'users', 1, '2026-04-22 18:09:11'),
(141, 1, 'submit_inspeksi', 'uji_kelayakan', 14, '2026-04-22 18:10:23'),
(142, 1, 'resubmit_pengajuan', 'pengajuan_uji', 19, '2026-04-22 18:11:03'),
(143, 1, 'resubmit_pengajuan', 'pengajuan_uji', 18, '2026-04-22 18:11:23'),
(144, 1, 'buat_pengajuan', 'pengajuan_uji', 22, '2026-04-22 18:24:11'),
(145, 1, 'reject_dept_manager', 'pengajuan_uji', 22, '2026-04-22 18:24:33'),
(146, 1, 'edit_pengajuan', 'pengajuan_uji', 22, '2026-04-22 18:26:03'),
(147, 1, 'approve_dept_manager', 'pengajuan_uji', 18, '2026-04-22 18:26:19'),
(148, 1, 'login', 'users', 1, '2026-04-22 23:59:01'),
(149, 1, 'logout', 'users', 1, '2026-04-23 00:01:02'),
(150, 2, 'login', 'users', 2, '2026-04-23 00:01:30'),
(151, 2, 'logout', 'users', 2, '2026-04-23 00:03:53'),
(152, 1, 'login', 'users', 1, '2026-04-23 00:04:02'),
(153, 1, 'login', 'users', 1, '2026-04-25 00:10:08'),
(154, 1, 'approve_dept_manager', 'pengajuan_uji', 22, '2026-04-25 00:15:12'),
(155, 1, 'approve_admin_ohs', 'pengajuan_uji', 22, '2026-04-25 00:15:23'),
(156, 1, 'buat_jadwal', 'jadwal_uji', 17, '2026-04-25 00:15:35'),
(157, 1, 'submit_inspeksi', 'uji_kelayakan', 15, '2026-04-25 00:16:10'),
(158, 1, 'login', 'users', 1, '2026-04-27 23:18:05'),
(159, 1, 'approve_admin_ohs', 'pengajuan_uji', 12, '2026-04-28 00:19:25'),
(160, 1, 'buat_jadwal', 'jadwal_uji', 18, '2026-04-28 00:19:38'),
(161, 1, 'submit_inspeksi', 'uji_kelayakan', 16, '2026-04-28 00:21:11'),
(162, 1, 'approve_admin_ohs', 'pengajuan_uji', 18, '2026-04-28 00:22:30'),
(163, 1, 'buat_jadwal', 'jadwal_uji', 19, '2026-04-28 00:22:40'),
(164, 1, 'submit_inspeksi', 'uji_kelayakan', 10, '2026-04-28 00:23:00'),
(165, 1, 'approve_ohs_supt', 'pengajuan_uji', 18, '2026-04-28 00:23:12'),
(166, 1, 'approve_ktt', 'pengajuan_uji', 18, '2026-04-28 00:23:30'),
(167, 1, 'login', 'users', 1, '2026-05-02 22:51:35'),
(168, 1, 'login', 'users', 1, '2026-05-04 00:44:09'),
(169, 1, 'login', 'users', 1, '2026-05-04 19:31:02'),
(170, 1, 'approve_dept_manager', 'pengajuan_uji', 22, '2026-05-04 20:31:21'),
(171, 1, 'approve_admin_ohs', 'pengajuan_uji', 22, '2026-05-04 20:32:03'),
(172, 1, 'approve_dept_manager', 'pengajuan_uji', 19, '2026-05-04 20:32:46'),
(173, 1, 'approve_admin_ohs', 'pengajuan_uji', 19, '2026-05-04 20:33:02'),
(174, 1, 'buat_jadwal', 'jadwal_uji', 20, '2026-05-04 20:33:57'),
(175, 1, 'login', 'users', 1, '2026-05-05 20:56:09'),
(176, 1, 'buat_jadwal', 'jadwal_uji', 21, '2026-05-05 21:25:21'),
(177, 1, 'buat_pengajuan', 'pengajuan_uji', 23, '2026-05-05 21:30:14'),
(178, 1, 'approve_dept_manager', 'pengajuan_uji', 23, '2026-05-05 21:30:19'),
(179, 1, 'approve_admin_ohs', 'pengajuan_uji', 23, '2026-05-05 21:32:13'),
(180, 1, 'buat_jadwal', 'jadwal_uji', 22, '2026-05-05 21:32:56'),
(181, 1, 'buat_pengajuan', 'pengajuan_uji', 24, '2026-05-05 21:40:39'),
(182, 1, 'approve_dept_manager', 'pengajuan_uji', 24, '2026-05-05 21:40:50'),
(183, 1, 'buat_pengajuan', 'pengajuan_uji', 25, '2026-05-05 21:47:43'),
(184, 1, 'approve_dept_manager', 'pengajuan_uji', 25, '2026-05-05 21:47:50'),
(185, 1, 'approve_admin_ohs', 'pengajuan_uji', 25, '2026-05-05 21:48:10'),
(186, 1, 'buat_jadwal', 'jadwal_uji', 23, '2026-05-05 21:48:23'),
(187, 1, 'submit_inspeksi', 'uji_kelayakan', 17, '2026-05-05 21:58:56'),
(188, 1, 'login', 'users', 1, '2026-05-06 20:46:15'),
(189, 1, 'logout', 'users', 1, '2026-05-06 20:47:10'),
(190, 5, 'login', 'users', 5, '2026-05-06 20:47:16'),
(191, 5, 'approve_ktt', 'pengajuan_uji', 18, '2026-05-06 20:47:21'),
(192, 5, 'logout', 'users', 5, '2026-05-06 20:49:48'),
(193, 1, 'login', 'users', 1, '2026-05-08 18:56:22'),
(194, 1, 'submit_inspeksi', 'uji_kelayakan', 18, '2026-05-08 19:03:14'),
(195, 1, 'login', 'users', 1, '2026-05-08 22:06:48'),
(196, 1, 'login', 'users', 1, '2026-05-10 18:50:37'),
(197, 1, 'buat_pengajuan', 'pengajuan_uji', 26, '2026-05-10 19:57:40'),
(198, 1, 'approve_dept_manager', 'pengajuan_uji', 26, '2026-05-10 19:57:45'),
(199, 1, 'approve_admin_ohs', 'pengajuan_uji', 26, '2026-05-10 19:57:50'),
(200, 1, 'buat_jadwal', 'jadwal_uji', 24, '2026-05-10 19:58:01'),
(201, 1, 'submit_inspeksi', 'uji_kelayakan', 19, '2026-05-10 19:59:28'),
(202, 1, 'buat_pengajuan', 'pengajuan_uji', 27, '2026-05-10 20:05:29'),
(203, 1, 'approve_dept_manager', 'pengajuan_uji', 27, '2026-05-10 20:05:33'),
(204, 1, 'approve_admin_ohs', 'pengajuan_uji', 27, '2026-05-10 20:05:37'),
(205, 1, 'buat_jadwal', 'jadwal_uji', 25, '2026-05-10 20:05:48'),
(206, 1, 'submit_inspeksi', 'uji_kelayakan', 20, '2026-05-10 20:07:05'),
(207, 1, 'input_perbaikan', 'perbaikan_unit', 3, '2026-05-10 20:09:08'),
(208, 1, 'submit_inspeksi', 'uji_kelayakan', 20, '2026-05-10 22:19:07'),
(209, 1, 'approve_ohs_supt', 'pengajuan_uji', 27, '2026-05-10 22:31:54'),
(210, 1, 'approve_ktt', 'pengajuan_uji', 27, '2026-05-10 22:32:00'),
(211, 1, 'login', 'users', 1, '2026-05-14 00:25:26'),
(212, 1, 'logout', 'users', 1, '2026-05-14 00:26:43'),
(213, 5, 'login', 'users', 5, '2026-05-14 00:26:48'),
(214, 5, 'approve_ktt', 'pengajuan_uji', 27, '2026-05-14 00:26:54'),
(215, 5, 'logout', 'users', 5, '2026-05-14 00:29:20'),
(216, 1, 'login', 'users', 1, '2026-05-14 00:29:30'),
(217, 1, 'login', 'users', 1, '2026-05-17 22:47:58'),
(218, 1, 'logout', 'users', 1, '2026-05-17 22:52:50'),
(219, 3, 'login', 'users', 3, '2026-05-17 22:52:57'),
(220, 3, 'logout', 'users', 3, '2026-05-17 22:54:13'),
(221, 1, 'login', 'users', 1, '2026-05-17 22:54:22'),
(222, 1, 'buat_pengajuan', 'pengajuan_uji', 28, '2026-05-17 22:55:53'),
(223, 1, 'approve_dept_manager', 'pengajuan_uji', 28, '2026-05-17 22:56:35'),
(224, 1, 'approve_admin_ohs', 'pengajuan_uji', 28, '2026-05-17 22:58:00'),
(225, 1, 'buat_jadwal', 'jadwal_uji', 26, '2026-05-17 22:58:17'),
(226, 1, 'logout', 'users', 1, '2026-05-17 22:58:20'),
(227, 3, 'login', 'users', 3, '2026-05-17 22:58:31'),
(228, 3, 'logout', 'users', 3, '2026-05-17 23:27:37'),
(229, 1, 'login', 'users', 1, '2026-05-17 23:27:44'),
(230, 1, 'submit_inspeksi', 'uji_kelayakan', 21, '2026-05-17 23:28:42'),
(231, 1, 'input_perbaikan', 'perbaikan_unit', 4, '2026-05-17 23:29:30'),
(232, 1, 'approve_inspeksi_verif', 'pengajuan_uji', 28, '2026-05-17 23:29:48'),
(233, 1, 'approve_inspeksi_verif', 'pengajuan_uji', 28, '2026-05-17 23:31:50'),
(234, 1, 'approve_inspeksi_verif', 'pengajuan_uji', 28, '2026-05-17 23:57:23'),
(235, 1, 'approve_verif_perbaikan', 'pengajuan_uji', 28, '2026-05-18 00:17:13'),
(236, 1, 'submit_inspeksi_ulang', 'uji_kelayakan', 21, '2026-05-18 00:17:42');

-- --------------------------------------------------------

--
-- Table structure for table `checklist_item`
--

CREATE TABLE `checklist_item` (
  `id_item` int UNSIGNED NOT NULL,
  `id_template` int UNSIGNED NOT NULL,
  `kategori` enum('CRITICAL','GENERAL') NOT NULL DEFAULT 'GENERAL',
  `no_urut` varchar(10) NOT NULL,
  `kriteria` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `checklist_item`
--

INSERT INTO `checklist_item` (`id_item`, `id_template`, `kategori`, `no_urut`, `kriteria`) VALUES
(1, 1, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(2, 1, 'CRITICAL', '2', 'Fire Extinguisher min. 2kg good and on the right place'),
(3, 1, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(4, 1, 'CRITICAL', '4', 'Reversing alarm operational'),
(5, 1, 'CRITICAL', '5', 'Park and service brake operational'),
(6, 1, 'CRITICAL', '6', 'Seatbelt provided and in good condition'),
(7, 1, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(8, 1, 'CRITICAL', '8', 'Tyres in good condition (min. treads 1.5mm)'),
(9, 1, 'CRITICAL', '9', '4 wheel drive (high and low range)'),
(10, 1, 'GENERAL', '10', 'VHF 2 ways Radio fitted and operational'),
(11, 1, 'GENERAL', '11', 'Horn operational'),
(12, 1, 'GENERAL', '12', 'Emergency Stop / shutdown devices operational'),
(13, 1, 'GENERAL', '13', 'All controls, buttons, levers, etc., clearly labelled'),
(14, 1, 'GENERAL', '14', 'Isolation points lockable'),
(15, 1, 'GENERAL', '15', 'Batteries secured and connections tight'),
(16, 1, 'GENERAL', '16', 'Cable and hoses adequately secured and protected'),
(17, 1, 'GENERAL', '17', 'Cover and guards in good condition and secured'),
(18, 1, 'GENERAL', '18', 'Safety glass and mirror clear and in good condition / no additional accessories'),
(19, 1, 'GENERAL', '19', 'Undercarriage: wheel, rim, axel, spring etc in good condition'),
(20, 1, 'GENERAL', '20', 'Mobile Equipment Identification Number (front, rear, left & right side)'),
(21, 1, 'GENERAL', '21', 'Maintenance records / mechanical inspection reports provided'),
(22, 1, 'GENERAL', '22', 'No oil or fuel leaks'),
(23, 1, 'GENERAL', '23', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(24, 2, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(25, 2, 'CRITICAL', '2', 'Fire Extinguisher min. 6kg good and on the right place'),
(26, 2, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(27, 2, 'CRITICAL', '4', 'Reversing alarm operational'),
(28, 2, 'CRITICAL', '5', 'Park and service brake operational'),
(29, 2, 'CRITICAL', '6', 'Seatbelt provided and in good condition'),
(30, 2, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(31, 2, 'CRITICAL', '8', 'Tyres in good condition'),
(32, 2, 'CRITICAL', '9', '6 or 4 wheel drive (high and low range)'),
(33, 2, 'GENERAL', '10', 'VHF 2 ways Radio fitted and operational'),
(34, 2, 'GENERAL', '11', 'Horn operational'),
(35, 2, 'GENERAL', '12', 'Emergency Stop / shutdown devices operational'),
(36, 2, 'GENERAL', '13', 'All controls, buttons, levers, etc., clearly labelled'),
(37, 2, 'GENERAL', '14', 'Isolation points lockable'),
(38, 2, 'GENERAL', '15', 'Batteries secured and connections tight'),
(39, 2, 'GENERAL', '16', 'Cable and hoses adequately secured and protected'),
(40, 2, 'GENERAL', '17', 'Cover and guards in good condition and secured'),
(41, 2, 'GENERAL', '18', 'Ladder, stairs, walkways and platforms in good condition'),
(42, 2, 'GENERAL', '19', 'Safety glass and mirror clear and in good condition / no additional accessories'),
(43, 2, 'GENERAL', '20', 'Undercarriage: wheel, rim, axel, spring etc in good condition'),
(44, 2, 'GENERAL', '21', 'Mobile Equipment Identification Number (front, rear, left & right side)'),
(45, 2, 'GENERAL', '22', 'Maintenance records / mechanical inspection reports provided'),
(46, 2, 'GENERAL', '23', 'No oil or fuel leaks'),
(47, 2, 'GENERAL', '24', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(48, 3, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(49, 3, 'CRITICAL', '2', 'Fire Extinguisher min. 6kg and fire suppression system fitted, good and on the right place'),
(50, 3, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(51, 3, 'CRITICAL', '4', 'Reversing alarm operational'),
(52, 3, 'CRITICAL', '5', 'Seatbelt provided and in good condition (all seats)'),
(53, 3, 'CRITICAL', '6', 'Tyres in good condition'),
(54, 3, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(55, 3, 'CRITICAL', '8', 'Park and service brake operational'),
(56, 3, 'GENERAL', '9', 'VHF 2 ways Radio fitted and operational'),
(57, 3, 'GENERAL', '10', 'Horn operational'),
(58, 3, 'GENERAL', '11', 'Emergency Stop / shutdown devices operational'),
(59, 3, 'GENERAL', '12', 'All controls, buttons, levers, etc., clearly labelled'),
(60, 3, 'GENERAL', '13', 'Isolation points lockable'),
(61, 3, 'GENERAL', '14', 'Batteries secured and connections tight'),
(62, 3, 'GENERAL', '15', 'Cable and hoses adequately secured and protected'),
(63, 3, 'GENERAL', '16', 'Cover and guards in good condition and secured'),
(64, 3, 'GENERAL', '17', 'Ladder, stairs, walkways and platforms in good condition'),
(65, 3, 'GENERAL', '18', 'Safety glass and mirror clear and in good condition'),
(66, 3, 'GENERAL', '19', 'Undercarriage: wheel, rim, axel, spring etc in good condition'),
(67, 3, 'GENERAL', '20', 'Mobile Equipment Identification Number (front, rear, left & right side)'),
(68, 3, 'GENERAL', '21', 'Maintenance records / mechanical inspection reports provided'),
(69, 3, 'GENERAL', '22', 'No oil or fuel leaks'),
(70, 3, 'GENERAL', '23', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(71, 4, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(72, 4, 'CRITICAL', '2', 'Fire Extinguisher min. 9kg and fire suppression system fitted, good and on the right place'),
(73, 4, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(74, 4, 'CRITICAL', '4', 'Reversing alarm operational'),
(75, 4, 'CRITICAL', '5', 'Seatbelt provided and in good condition (all passenger seats)'),
(76, 4, 'CRITICAL', '6', 'Tyres in good condition'),
(77, 4, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(78, 4, 'CRITICAL', '8', 'Park and service brake operational'),
(79, 4, 'CRITICAL', '9', 'VHF 2 ways Radio fitted and operational'),
(80, 4, 'CRITICAL', '10', 'Buggy whip & Flag (min. 4.5 meters from the ground)'),
(81, 4, 'GENERAL', '11', 'Horn operational'),
(82, 4, 'GENERAL', '12', 'Emergency Stop / shutdown devices operational'),
(83, 4, 'GENERAL', '13', 'All controls, buttons, levers, etc., clearly labelled'),
(84, 4, 'GENERAL', '14', 'Isolation points lockable'),
(85, 4, 'GENERAL', '15', 'Batteries secured and connections tight'),
(86, 4, 'GENERAL', '16', 'Cable and hoses adequately secured and protected'),
(87, 4, 'GENERAL', '17', 'Cover and guards in good condition and secured'),
(88, 4, 'GENERAL', '18', 'Ladder, stairs, walkways and platforms in good condition'),
(89, 4, 'GENERAL', '19', 'Safety glass and mirror clear and in good condition'),
(90, 4, 'GENERAL', '20', 'Undercarriage: wheel, rim, axel, spring etc in good condition'),
(91, 4, 'GENERAL', '21', 'Mobile Equipment Identification Number (front, rear, left & right side)'),
(92, 4, 'GENERAL', '22', 'Maintenance records / mechanical inspection reports provided'),
(93, 4, 'GENERAL', '23', 'No oil or fuel leaks'),
(94, 4, 'GENERAL', '24', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(95, 5, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (pit access only)'),
(96, 5, 'CRITICAL', '2', 'Fire Extinguisher min. 6kg good and on the right place'),
(97, 5, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(98, 5, 'CRITICAL', '4', 'Reversing alarm operational'),
(99, 5, 'CRITICAL', '5', 'Park and service brake operational'),
(100, 5, 'CRITICAL', '6', 'Seatbelt provided and in good condition'),
(101, 5, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(102, 5, 'CRITICAL', '8', 'Tyres in good condition'),
(103, 5, 'GENERAL', '9', 'VHF 2 ways Radio fitted and operational'),
(104, 5, 'GENERAL', '10', 'Horn operational'),
(105, 5, 'GENERAL', '11', 'Emergency Stop / shutdown devices operational'),
(106, 5, 'GENERAL', '12', 'All controls, buttons, levers, etc., clearly labelled'),
(107, 5, 'GENERAL', '13', 'Isolation points lockable'),
(108, 5, 'GENERAL', '14', 'Batteries secured and connections tight'),
(109, 5, 'GENERAL', '15', 'Electric cable, grounding and hoses adequately secured and protected'),
(110, 5, 'GENERAL', '16', 'Cover and guards in good condition and secured'),
(111, 5, 'GENERAL', '17', 'Ladder, stairs, walkways and platforms in good condition'),
(112, 5, 'GENERAL', '18', 'Safety glass and mirror clear and in good condition'),
(113, 5, 'GENERAL', '19', 'Undercarriage: wheel, rim, axel, spring etc in good condition'),
(114, 5, 'GENERAL', '20', 'Mobile Equipment Identification Number (front, rear, left & right side)'),
(115, 5, 'GENERAL', '21', 'Maintenance records / mechanical inspection reports provided'),
(116, 5, 'GENERAL', '22', 'No oil or fuel leaks'),
(117, 5, 'GENERAL', '23', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(118, 6, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(119, 6, 'CRITICAL', '2', 'Fire Extinguisher min. 6kg good and on the right place'),
(120, 6, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(121, 6, 'CRITICAL', '4', 'Reversing alarm operational'),
(122, 6, 'CRITICAL', '5', 'Park and service brake operational'),
(123, 6, 'CRITICAL', '6', 'Seatbelt provided and in good condition'),
(124, 6, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(125, 6, 'CRITICAL', '8', 'Tyres in good condition'),
(126, 6, 'CRITICAL', '9', '6 or 4 wheel drive (high and low range)'),
(127, 6, 'GENERAL', '10', 'VHF 2 ways Radio fitted and operational'),
(128, 6, 'GENERAL', '11', 'Horn operational'),
(129, 6, 'GENERAL', '12', 'Emergency Stop / shutdown devices operational'),
(130, 6, 'GENERAL', '13', 'All controls, buttons, levers, etc., clearly labelled'),
(131, 6, 'GENERAL', '14', 'Isolation points lockable'),
(132, 6, 'GENERAL', '15', 'Batteries secured and connections tight'),
(133, 6, 'GENERAL', '16', 'Cable and hoses adequately secured and protected'),
(134, 6, 'GENERAL', '17', 'Cover and guards in good condition and secured'),
(135, 6, 'GENERAL', '18', 'Ladder, stairs, walkways and platforms in good condition'),
(136, 6, 'GENERAL', '19', 'Roll over / falling object protection (ROPS/FOBS) fitted and in good condition'),
(137, 6, 'GENERAL', '20', 'Safety glass clear and in good condition'),
(138, 6, 'GENERAL', '21', 'Undercarriage: wheel, rim, axel, spring etc in good condition'),
(139, 6, 'GENERAL', '22', 'Mobile Equipment Identification Number (front, rear, left & right side)'),
(140, 6, 'GENERAL', '23', 'Maintenance records / mechanical inspection reports provided'),
(141, 6, 'GENERAL', '24', 'No oil or fuel leaks'),
(142, 6, 'GENERAL', '25', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(143, 7, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(144, 7, 'CRITICAL', '2', 'Fire Extinguisher min. 6kg good and on the right place'),
(145, 7, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(146, 7, 'CRITICAL', '4', 'Reversing alarm operational'),
(147, 7, 'CRITICAL', '5', 'Park and service brake operational'),
(148, 7, 'CRITICAL', '6', 'Seatbelt provided and in good condition'),
(149, 7, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(150, 7, 'CRITICAL', '8', 'Tyres in good condition'),
(151, 7, 'CRITICAL', '9', '6 or 4 wheel drive (high and low range)'),
(152, 7, 'GENERAL', '10', 'VHF 2 ways radio fitted and operational'),
(153, 7, 'GENERAL', '11', 'Boom extension condition'),
(154, 7, 'GENERAL', '12', 'Horn operational'),
(155, 7, 'GENERAL', '13', 'Condition of wire rope (sling)'),
(156, 7, 'GENERAL', '14', 'Condition of jack (outrigger)'),
(157, 7, 'GENERAL', '15', 'Control lever outrigger, good condition'),
(158, 7, 'GENERAL', '16', 'Hook good condition, with latch'),
(159, 7, 'GENERAL', '17', 'Tuas control hydraulic crane labelled'),
(160, 7, 'GENERAL', '18', 'Emergency Stop / shutdown devices operational'),
(161, 7, 'GENERAL', '19', 'All controls, buttons, levers, etc., clearly labelled'),
(162, 7, 'GENERAL', '20', 'Isolation points lockable'),
(163, 7, 'GENERAL', '21', 'Batteries secured and connections tight'),
(164, 7, 'GENERAL', '22', 'Cable and hoses adequately secured and protected'),
(165, 7, 'GENERAL', '23', 'Cover and guards in good condition and secured'),
(166, 7, 'GENERAL', '24', 'Maintenance records / mechanical inspection reports provided'),
(167, 7, 'GENERAL', '25', 'No oil or fuel leaks'),
(168, 7, 'GENERAL', '26', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(169, 8, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(170, 8, 'CRITICAL', '2', 'Fire Extinguisher min. 6kg and fire suppression system fitted, good and on the right place'),
(171, 8, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(172, 8, 'CRITICAL', '4', 'Reversing alarm operational'),
(173, 8, 'CRITICAL', '5', 'Seatbelt provided and in good condition'),
(174, 8, 'CRITICAL', '6', 'Tyres in good condition'),
(175, 8, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(176, 8, 'CRITICAL', '8', 'Park and service brake operational'),
(177, 8, 'GENERAL', '9', 'UHF 2 ways Radio fitted and operational'),
(178, 8, 'GENERAL', '10', 'Horn operational'),
(179, 8, 'GENERAL', '11', 'Emergency Stop / shutdown devices operational'),
(180, 8, 'GENERAL', '12', 'All controls, buttons, levers, etc., clearly labelled'),
(181, 8, 'GENERAL', '13', 'Isolation points lockable'),
(182, 8, 'GENERAL', '14', 'Batteries secured and connections tight'),
(183, 8, 'GENERAL', '15', 'Cable and hoses adequately secured and protected'),
(184, 8, 'GENERAL', '16', 'Cover and guards in good condition and secured'),
(185, 8, 'GENERAL', '17', 'Ladder, stairs, walkways and platforms in good condition'),
(186, 8, 'GENERAL', '18', 'Roll over / falling object protection (ROPS/FOBS) fitted and in good condition'),
(187, 8, 'GENERAL', '19', 'Safety glass clear and in good condition'),
(188, 8, 'GENERAL', '20', 'Articulation lock provided and operational'),
(189, 8, 'GENERAL', '21', 'Mobile Equipment Identification Number (front, rear, left & right side)'),
(190, 8, 'GENERAL', '22', 'Maintenance records / mechanical inspection reports provided'),
(191, 8, 'GENERAL', '23', 'No oil or fuel leaks'),
(192, 8, 'GENERAL', '24', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(193, 9, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(194, 9, 'CRITICAL', '2', 'Fire Extinguisher min. 9kg and fire suppression system fitted, good and on the right place'),
(195, 9, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(196, 9, 'CRITICAL', '4', 'Reversing alarm operational'),
(197, 9, 'CRITICAL', '5', 'Seatbelt provided and in good condition'),
(198, 9, 'CRITICAL', '6', 'Tyres in good condition'),
(199, 9, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(200, 9, 'CRITICAL', '8', 'Park and service brake operational'),
(201, 9, 'GENERAL', '9', 'VHF 2 ways Radio fitted and operational'),
(202, 9, 'GENERAL', '10', 'Horn operational'),
(203, 9, 'GENERAL', '11', 'Emergency Stop / shutdown devices operational'),
(204, 9, 'GENERAL', '12', 'All controls, buttons, levers, etc., clearly labelled'),
(205, 9, 'GENERAL', '13', 'Isolation points lockable'),
(206, 9, 'GENERAL', '14', 'Batteries secured and connections tight'),
(207, 9, 'GENERAL', '15', 'Cable and hoses adequately secured and protected'),
(208, 9, 'GENERAL', '16', 'Cover and guards in good condition and secured'),
(209, 9, 'GENERAL', '17', 'Ladder, stairs, walkways and platforms in good condition'),
(210, 9, 'GENERAL', '18', 'Roll over / falling object protection (ROPS/FOBS) fitted and in good condition'),
(211, 9, 'GENERAL', '19', 'Safety glass clear and in good condition'),
(212, 9, 'GENERAL', '20', 'Undercarriage: wheel, rim, axel, spring etc in good condition'),
(213, 9, 'GENERAL', '21', 'Mobile Equipment Identification Number (front, rear, left & right side)'),
(214, 9, 'GENERAL', '22', 'Maintenance records / mechanical inspection reports provided'),
(215, 9, 'GENERAL', '23', 'No oil or fuel leaks'),
(216, 9, 'GENERAL', '24', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(217, 10, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(218, 10, 'CRITICAL', '2', 'Fire Extinguisher min. 9kg and fire suppression system fitted, good and on the right place'),
(219, 10, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(220, 10, 'CRITICAL', '4', 'Reversing alarm operational'),
(221, 10, 'CRITICAL', '5', 'Seatbelt provided and in good condition'),
(222, 10, 'CRITICAL', '6', 'Tyres in good condition'),
(223, 10, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(224, 10, 'CRITICAL', '8', 'Park and service brake operational'),
(225, 10, 'CRITICAL', '9', 'VHF 2 ways Radio fitted and operational'),
(226, 10, 'CRITICAL', '10', 'Buggy whip & Flag (min. 4.5 meters from the ground)'),
(227, 10, 'GENERAL', '11', 'Horn operational'),
(228, 10, 'GENERAL', '12', 'Emergency Stop / shutdown devices operational'),
(229, 10, 'GENERAL', '13', 'All controls, buttons, levers, etc., clearly labelled'),
(230, 10, 'GENERAL', '14', 'Isolation points lockable'),
(231, 10, 'GENERAL', '15', 'Batteries secured and connections tight'),
(232, 10, 'GENERAL', '16', 'Cable and hoses adequately secured and protected'),
(233, 10, 'GENERAL', '17', 'Cover and guards in good condition and secured'),
(234, 10, 'GENERAL', '18', 'Ladder, stairs, walkways and platforms in good condition'),
(235, 10, 'GENERAL', '19', 'Fork and mast in good condition and secure'),
(236, 10, 'GENERAL', '20', 'Overhead guard fitted and in good condition'),
(237, 10, 'GENERAL', '21', 'Load backrest extension fitted'),
(238, 10, 'GENERAL', '22', 'Maintenance records / mechanical inspection reports provided'),
(239, 10, 'GENERAL', '23', 'No oil or fuel leaks'),
(240, 10, 'GENERAL', '24', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(241, 11, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(242, 11, 'CRITICAL', '2', 'Fire Extinguisher min. 6kg fitted, good and on the right place'),
(243, 11, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(244, 11, 'CRITICAL', '4', 'Reversing or moving alarm operational'),
(245, 11, 'CRITICAL', '5', 'Park and service brake operational'),
(246, 11, 'CRITICAL', '6', 'Seatbelt provided and in good condition'),
(247, 11, 'CRITICAL', '7', 'All controls, buttons, levers, etc., clearly labelled'),
(248, 11, 'GENERAL', '8', 'VHF 2 ways Radio fitted and operational'),
(249, 11, 'GENERAL', '9', 'Powerline warning poster intact and displayed prominently'),
(250, 11, 'GENERAL', '10', 'Horn operational'),
(251, 11, 'GENERAL', '11', 'Safety lock system operational'),
(252, 11, 'GENERAL', '12', 'Emergency Stop / shutdown devices operational'),
(253, 11, 'GENERAL', '13', 'Isolation points lockable'),
(254, 11, 'GENERAL', '14', 'Batteries secured and connections tight'),
(255, 11, 'GENERAL', '15', 'Cable and hoses adequately secured and protected'),
(256, 11, 'GENERAL', '16', 'Cover and guards in good condition and secured'),
(257, 11, 'GENERAL', '17', 'Ladder, stairs, walkways and platforms in good condition'),
(258, 11, 'GENERAL', '18', 'Roll over / falling object protection (ROPS/FOBS) fitted and in good condition'),
(259, 11, 'GENERAL', '19', 'Undercarriage in good condition (track, sprocket, roller)'),
(260, 11, 'GENERAL', '20', 'Boom, arm and bucket in good condition'),
(261, 11, 'GENERAL', '21', 'Maintenance records / mechanical inspection reports provided'),
(262, 11, 'GENERAL', '22', 'No oil or fuel leaks'),
(263, 11, 'GENERAL', '23', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(264, 12, 'CRITICAL', '1', 'Flashing beacon light fitted and operational'),
(265, 12, 'CRITICAL', '2', 'UHF 2 ways Radio fitted and operational'),
(266, 12, 'CRITICAL', '3', 'Fire Extinguisher fitted, good and on the right place'),
(267, 12, 'CRITICAL', '4', 'Head, tail, brake, indicator and clearance lights operational'),
(268, 12, 'CRITICAL', '5', 'Horn operational'),
(269, 12, 'CRITICAL', '6', 'Reversing alarm operational'),
(270, 12, 'CRITICAL', '7', 'Park and service brake operational'),
(271, 12, 'GENERAL', '8', 'Emergency Stop / shutdown devices operational'),
(272, 12, 'GENERAL', '9', 'All controls, buttons, levers, etc., clearly labelled'),
(273, 12, 'GENERAL', '10', 'Seatbelt provided and in good condition'),
(274, 12, 'GENERAL', '11', 'Isolation points lockable'),
(275, 12, 'GENERAL', '12', 'Batteries secured and connections tight'),
(276, 12, 'GENERAL', '13', 'Cable and hoses adequately secured and protected'),
(277, 12, 'GENERAL', '14', 'Cover and guards in good condition and secured'),
(278, 12, 'GENERAL', '15', 'Ladder, stairs, walkways and platforms in good condition'),
(279, 12, 'GENERAL', '16', 'Roll over / falling object protection (ROPS/FOBS) fitted and in good condition'),
(280, 12, 'GENERAL', '17', 'Safety glass clear and in good condition'),
(281, 12, 'GENERAL', '18', 'Tyres in good condition'),
(282, 12, 'GENERAL', '19', 'Articulation lock provided'),
(283, 12, 'GENERAL', '20', 'Drum in good condition'),
(284, 12, 'GENERAL', '21', 'Maintenance records / mechanical inspection reports provided'),
(285, 12, 'GENERAL', '22', 'No oil or fuel leaks'),
(286, 12, 'GENERAL', '23', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(287, 13, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(288, 13, 'CRITICAL', '2', 'Fire Extinguisher min. 6kg fitted, good and on the right place'),
(289, 13, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(290, 13, 'CRITICAL', '4', 'Reversing or moving alarm operational'),
(291, 13, 'CRITICAL', '5', 'Park and service brake operational'),
(292, 13, 'CRITICAL', '6', 'Seatbelt provided and in good condition'),
(293, 13, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(294, 13, 'CRITICAL', '8', 'Adequate tyre treads'),
(295, 13, 'GENERAL', '9', 'UHF 2 ways Radio fitted and operational'),
(296, 13, 'GENERAL', '10', 'Powerline warning poster intact and displayed prominently'),
(297, 13, 'GENERAL', '11', 'Horn operational'),
(298, 13, 'GENERAL', '12', 'Safety lock system operational'),
(299, 13, 'GENERAL', '13', 'Emergency Stop / shutdown devices operational'),
(300, 13, 'GENERAL', '14', 'All controls, buttons, levers, etc., clearly labelled'),
(301, 13, 'GENERAL', '15', 'Isolation points lockable'),
(302, 13, 'GENERAL', '16', 'Batteries secured and connections tight'),
(303, 13, 'GENERAL', '17', 'Cable and hoses adequately secured and protected'),
(304, 13, 'GENERAL', '18', 'Cover and guards in good condition and secured'),
(305, 13, 'GENERAL', '19', 'Ladder, stairs, walkways and platforms in good condition'),
(306, 13, 'GENERAL', '20', 'Roll over / falling object protection (ROPS/FOBS) fitted and in good condition'),
(307, 13, 'GENERAL', '21', 'Blade and circle drive in good condition'),
(308, 13, 'GENERAL', '22', 'Maintenance records / mechanical inspection reports provided'),
(309, 13, 'GENERAL', '23', 'No oil or fuel leaks'),
(310, 13, 'GENERAL', '24', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(311, 14, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(312, 14, 'CRITICAL', '2', 'Fire Extinguisher fitted, good and on the right place'),
(313, 14, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(314, 14, 'CRITICAL', '4', 'Reversing alarm operational'),
(315, 14, 'CRITICAL', '5', 'Park and service brake operational'),
(316, 14, 'CRITICAL', '6', 'Seat and Seatbelt in good condition'),
(317, 14, 'CRITICAL', '7', 'Standard OEM steering wheel'),
(318, 14, 'CRITICAL', '8', 'Tyres in good condition'),
(319, 14, 'GENERAL', '9', 'UHF 2 ways Radio fitted and operational'),
(320, 14, 'GENERAL', '10', 'Horn operational'),
(321, 14, 'GENERAL', '11', 'Emergency Stop / shutdown devices operational'),
(322, 14, 'GENERAL', '12', 'All controls, buttons, levers, etc., clearly labelled'),
(323, 14, 'GENERAL', '13', 'Isolation points lockable'),
(324, 14, 'GENERAL', '14', 'Batteries secured and connections tight'),
(325, 14, 'GENERAL', '15', 'Cable and hoses adequately secured and protected'),
(326, 14, 'GENERAL', '16', 'Cover and guards in good condition and secured'),
(327, 14, 'GENERAL', '17', 'Ladder, stairs, walkways and platforms in good condition'),
(328, 14, 'GENERAL', '18', 'Roll over / falling object protection (ROPS/FOBS) fitted and in good condition'),
(329, 14, 'GENERAL', '19', 'Bucket and arms in good condition'),
(330, 14, 'GENERAL', '20', 'Articulation lock provided and operational'),
(331, 14, 'GENERAL', '21', 'Maintenance records / mechanical inspection reports provided'),
(332, 14, 'GENERAL', '22', 'No oil or fuel leaks'),
(333, 14, 'GENERAL', '23', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(334, 15, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (access pit only)'),
(335, 15, 'CRITICAL', '2', 'Fire Extinguisher min. 6kg and fire suppression system (OEM) fitted, good and on the right place'),
(336, 15, 'CRITICAL', '3', 'Head, tail, brake, indicator and clearance lights operational'),
(337, 15, 'CRITICAL', '4', 'Reversing or moving alarm operational'),
(338, 15, 'CRITICAL', '5', 'Park and service brake operational'),
(339, 15, 'CRITICAL', '6', 'Seatbelt provided and in good condition'),
(340, 15, 'CRITICAL', '7', 'All controls, buttons, levers, etc., clearly labelled'),
(341, 15, 'GENERAL', '8', 'UHF 2 ways Radio fitted and operational'),
(342, 15, 'GENERAL', '9', 'Horn operational'),
(343, 15, 'GENERAL', '10', 'Emergency Stop / shutdown devices operational'),
(344, 15, 'GENERAL', '11', 'Isolation points lockable'),
(345, 15, 'GENERAL', '12', 'Batteries secured and connections tight'),
(346, 15, 'GENERAL', '13', 'Cable and hoses adequately secured and protected'),
(347, 15, 'GENERAL', '14', 'Cover and guards in good condition and secured'),
(348, 15, 'GENERAL', '15', 'Ladder, stairs, walkways and platforms in good condition'),
(349, 15, 'GENERAL', '16', 'Roll over / falling object protection (ROPS/FOBS) fitted and in good condition'),
(350, 15, 'GENERAL', '17', 'Cabin, Door, Window, Seat, AC (air ventilation system) in good condition'),
(351, 15, 'GENERAL', '18', 'Blade and ripper in good condition'),
(352, 15, 'GENERAL', '19', 'Undercarriage in good condition (track, sprocket, roller)'),
(353, 15, 'GENERAL', '20', 'Maintenance records / mechanical inspection reports provided'),
(354, 15, 'GENERAL', '21', 'No oil or fuel leaks'),
(355, 15, 'GENERAL', '22', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(356, 16, 'CRITICAL', '1', 'Flashing beacon light fitted and operational'),
(357, 16, 'CRITICAL', '2', 'Fire Extinguisher fitted, good and on the right place'),
(358, 16, 'CRITICAL', '3', 'Horn operational'),
(359, 16, 'CRITICAL', '4', 'Emergency Stop / shutdown devices operational'),
(360, 16, 'GENERAL', '5', 'All controls, buttons, levers, etc., clearly labelled'),
(361, 16, 'GENERAL', '6', 'Seatbelt provided and in good condition'),
(362, 16, 'GENERAL', '7', 'Isolation points lockable'),
(363, 16, 'GENERAL', '8', 'Batteries secured and connections tight'),
(364, 16, 'GENERAL', '9', 'Cable and hoses adequately secured and protected'),
(365, 16, 'GENERAL', '10', 'Cover and guards in good condition and secured'),
(366, 16, 'GENERAL', '11', 'Ladder, stairs, walkways and platforms in good condition'),
(367, 16, 'GENERAL', '12', 'Undercarriage in good condition (track, sprocket, roller)'),
(368, 16, 'GENERAL', '13', 'Maintenance records / mechanical inspection reports provided'),
(369, 16, 'GENERAL', '14', 'No oil or fuel leaks'),
(370, 16, 'GENERAL', '15', 'Machine in good condition'),
(371, 17, 'GENERAL', '1', 'Semua lampu dalam keadaan baik'),
(372, 17, 'GENERAL', '2', 'Lampu rotary (Amber color flashing light)'),
(373, 17, 'GENERAL', '3', 'Nomor identitas unit 3 Sides'),
(374, 17, 'GENERAL', '4', 'Klakson berfungsi dengan baik'),
(375, 17, 'GENERAL', '5', 'Alarm mundur berfungsi'),
(376, 17, 'GENERAL', '6', 'Semua meteran/pengukur dan tombol/tuas lengkap dengan label'),
(377, 17, 'GENERAL', '7', 'Emergency stop'),
(378, 17, 'GENERAL', '8', 'Safety release katup udara pada tangki'),
(379, 17, 'GENERAL', '9', 'Ball valve saluran udara bertekanan'),
(380, 17, 'GENERAL', '10', 'Sistem pengereman'),
(381, 17, 'GENERAL', '11', 'Wire mesh pelindung mesin yang berputar'),
(382, 17, 'GENERAL', '12', 'Kondisi tali kawat (Wire rope)'),
(383, 17, 'GENERAL', '13', 'Semua alat ukur listrik berfungsi'),
(384, 17, 'GENERAL', '14', 'Semua kabel listrik terlindung'),
(385, 17, 'GENERAL', '15', 'Catatan perawatan / laporan inspeksi mekanik tersedia'),
(386, 17, 'GENERAL', '16', 'Tidak ada kebocoran oli atau bahan bakar'),
(387, 17, 'GENERAL', '17', 'Mesin dalam kondisi baik'),
(388, 18, 'CRITICAL', '1', 'Flashing beacon light fitted and operational'),
(389, 18, 'CRITICAL', '2', 'VHF 2 ways Radio fitted and operational'),
(390, 18, 'CRITICAL', '3', 'Powerline warning poster intact and displayed prominently'),
(391, 18, 'CRITICAL', '4', 'Pilot on jumbo power cable in good condition'),
(392, 18, 'CRITICAL', '5', 'Fire Extinguisher fitted, good and on the right place'),
(393, 18, 'CRITICAL', '6', 'Fire Suppression System fitted, good, proper pressure, module in good function'),
(394, 18, 'CRITICAL', '7', 'Head, tail, brake, indicator and clearance lights operational'),
(395, 18, 'CRITICAL', '8', 'Horn operational'),
(396, 18, 'CRITICAL', '9', 'Reversing/operating alarm operational'),
(397, 18, 'CRITICAL', '10', 'Park and service brake operational'),
(398, 18, 'GENERAL', '11', 'Engine Emergency Stop / shutdown devices operational'),
(399, 18, 'GENERAL', '12', 'Boom Emergency Stop / shutdown device operational'),
(400, 18, 'GENERAL', '13', 'All controls, buttons, levers, etc., clearly labelled'),
(401, 18, 'GENERAL', '14', 'Operator seat, seatbelt provided and in good condition'),
(402, 18, 'GENERAL', '15', 'Isolation points lockable'),
(403, 18, 'GENERAL', '16', 'Batteries secured and connections tight'),
(404, 18, 'GENERAL', '17', 'Cable and hoses adequately secured and protected'),
(405, 18, 'GENERAL', '18', 'Cover and guards in good condition and secured'),
(406, 18, 'GENERAL', '19', 'Ladder, stairs, walkways and platforms in good condition'),
(407, 18, 'GENERAL', '20', 'Boom and drill steel in good condition'),
(408, 18, 'GENERAL', '21', 'Water and flushing system operational'),
(409, 18, 'GENERAL', '22', 'Maintenance records / mechanical inspection reports provided'),
(410, 18, 'GENERAL', '23', 'No oil or fuel leaks'),
(411, 18, 'GENERAL', '24', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(412, 19, 'CRITICAL', '1', 'Flashing beacon light fitted and operational (if mobile)'),
(413, 19, 'CRITICAL', '2', 'Fire Extinguisher fitted, good and on the right place'),
(414, 19, 'CRITICAL', '3', 'Emergency Stop / shutdown devices operational'),
(415, 19, 'GENERAL', '4', 'All controls, buttons, gauges, levers, etc., clearly labelled'),
(416, 19, 'GENERAL', '5', 'Isolation points lockable'),
(417, 19, 'GENERAL', '6', 'Batteries secured and connections tight'),
(418, 19, 'GENERAL', '7', 'Cable and hoses adequately secured and protected'),
(419, 19, 'GENERAL', '8', 'Cover and guards in good condition and secured'),
(420, 19, 'GENERAL', '9', 'Earthing/grounding in good condition'),
(421, 19, 'GENERAL', '10', 'Exhaust properly directed / muffler in good condition'),
(422, 19, 'GENERAL', '11', 'Fuel tank in good condition, no leaks'),
(423, 19, 'GENERAL', '12', 'Oil level adequate, no leaks'),
(424, 19, 'GENERAL', '13', 'Cooling system in good condition'),
(425, 19, 'GENERAL', '14', 'Electrical connections tight and insulated'),
(426, 19, 'GENERAL', '15', 'Output voltage/pressure within specification'),
(427, 19, 'GENERAL', '16', 'Safety relief valve fitted and operational (Compressor/Pump)'),
(428, 19, 'GENERAL', '17', 'Maintenance records / mechanical inspection reports provided'),
(429, 19, 'GENERAL', '18', 'Machine/Engine in good condition (Operational testing/Running engine)'),
(430, 20, 'CRITICAL', '1', 'Testing');

-- --------------------------------------------------------

--
-- Table structure for table `checklist_template`
--

CREATE TABLE `checklist_template` (
  `id_template` int UNSIGNED NOT NULL,
  `kode` varchar(20) NOT NULL,
  `id_tipe_kendaraan` int UNSIGNED DEFAULT NULL COMMENT 'FK → tipe_kendaraan',
  `nama_template` varchar(200) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `checklist_template`
--

INSERT INTO `checklist_template` (`id_template`, `kode`, `id_tipe_kendaraan`, `nama_template`, `is_active`, `created_at`) VALUES
(1, '002C', 1, 'Light Vehicle Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(2, '002D', 2, 'Light Truck Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(3, '002E', 3, 'Bus Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(4, '002F', 4, 'Bus Manhaul Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(5, '002G', 5, 'Fuel Truck Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(6, '002H', 6, 'Dump Truck Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(7, '002I', 7, 'Crane Truck Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(8, '002J', 8, 'Articulated Dump Truck (ADT) Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(9, '002K', 9, 'Haul Truck / Heavy Duty Dump Truck Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(10, '002L', 10, 'Forklift Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(11, '002M', 11, 'Excavator Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(12, '002N', 12, 'Compactor Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(13, '002O', 13, 'Grader Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(14, '002P', 14, 'Wheel Loader Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(15, '002Q', 15, 'Dozer Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(16, '002R', 16, 'Crawler Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(17, '002S', 17, 'Drill Rig Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(18, '002T', 18, 'Jumbo Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(19, '002U', 19, 'Equipment Support (Genset/Compressor/Lighting/Pump) Commissioning Checklist', 1, '2026-02-24 01:19:10'),
(20, '00GZ', 21, 'Gen Z (GZ) Commissioning Checklist', 1, '2026-05-05 21:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('54oo3l696ni8gvahkt5pp2hse8g1r1js', '127.0.0.1', 1779031970, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393033313937303b636170746368615f636f64657c693a31343b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('84r6s0k0gf3io5ramll9801f1f66uh1o', '127.0.0.1', 1778423456, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383432333435363b636170746368615f636f64657c693a31323b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('9ed8r2nmpj3r72u1hr1hd26blrin8n5a', '127.0.0.1', 1778422614, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383432323631343b636170746368615f636f64657c693a31323b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('bssc57m7gf3tln3pdik7i7n8om6jqh1h', '127.0.0.1', 1779033439, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393033333433393b636170746368615f636f64657c693a31343b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('echkgcujbf9iahqbor385r5n2vgbphqr', '127.0.0.1', 1779029517, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393032393531373b636170746368615f636f64657c693a31333b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('eh8e19anuhr3a74826md897v2vui6ik8', '127.0.0.1', 1779030998, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393033303939383b636170746368615f636f64657c693a31333b69645f757365727c693a333b6e616d617c733a393a22496e7370656b746f72223b757365726e616d657c733a393a22696e7370656b746f72223b656d61696c7c733a31393a22696e7370656b746f7240676d61696c2e636f6d223b666f746f7c4e3b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a343b726f6c65737c613a313a7b693a303b693a343b7d726f6c65735f6e616d65737c613a313a7b693a303b733a393a22496e7370656b746f72223b7d6c6f676765645f696e7c623a313b),
('g9vn3adf0jh86ifftafsrqi5512ns6rl', '127.0.0.1', 1779033951, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393033333935313b636170746368615f636f64657c693a31343b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('ho98af4b4adhpipqbk5ujo54kua7k1v3', '127.0.0.1', 1778423902, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383432333838363b636170746368615f636f64657c693a31323b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('i9agkuaf73ltbubjm5lrgjabseiad5u6', '127.0.0.1', 1778423886, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383432333838363b636170746368615f636f64657c693a31323b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('ii9ijog6h4e23l4ki2bb9bor1caoi2d4', '127.0.0.1', 1778423119, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383432333131393b636170746368615f636f64657c693a31323b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('km796jc423ab2oadnb7gpb9auf3k4c61', '127.0.0.1', 1778690185, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383639303138353b636170746368615f636f64657c693a31333b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('o7hmlg0fcvhhtm8tnoofha3o2qfgqg3j', '127.0.0.1', 1779031392, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393033313339323b636170746368615f636f64657c693a31333b69645f757365727c693a333b6e616d617c733a393a22496e7370656b746f72223b757365726e616d657c733a393a22696e7370656b746f72223b656d61696c7c733a31393a22696e7370656b746f7240676d61696c2e636f6d223b666f746f7c4e3b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a343b726f6c65737c613a313a7b693a303b693a343b7d726f6c65735f6e616d65737c613a313a7b693a303b733a393a22496e7370656b746f72223b7d6c6f676765645f696e7c623a313b),
('pa9ffcnsfhhdpq2dgk167qbtojeldoob', '127.0.0.1', 1778690205, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383639303138353b636170746368615f636f64657c693a31333b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b6572726f727c733a32353a2254656d706c61746520746964616b20646974656d756b616e2e223b5f5f63695f766172737c613a313a7b733a353a226572726f72223b733a333a226f6c64223b7d),
('pnfkj65qevrci2nr9ldtf0lrotuqe3ec', '127.0.0.1', 1778421675, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383432313637353b636170746368615f636f64657c693a31323b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('qqm76gd2di42rfg4t7dgp9gar9h17dlq', '127.0.0.1', 1779030515, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393033303531353b636170746368615f636f64657c693a31333b69645f757365727c693a333b6e616d617c733a393a22496e7370656b746f72223b757365726e616d657c733a393a22696e7370656b746f72223b656d61696c7c733a31393a22696e7370656b746f7240676d61696c2e636f6d223b666f746f7c4e3b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a343b726f6c65737c613a313a7b693a303b693a343b7d726f6c65735f6e616d65737c613a313a7b693a303b733a393a22496e7370656b746f72223b7d6c6f676765645f696e7c623a313b),
('rferrd63aq2t3vtredr8sjsdun473cjv', '127.0.0.1', 1779034586, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393033343538363b636170746368615f636f64657c693a31343b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('satvborl4uamv3id7qfp0h0h3kim0q2m', '127.0.0.1', 1778422107, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383432323130373b636170746368615f636f64657c693a31323b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('seaf14qapg5tj2jt0qm1dhen4v9l51km', '127.0.0.1', 1779034735, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393033343538363b636170746368615f636f64657c693a31343b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b),
('ua7r0tsse5q60ju727i9cd3bt6i588qn', '127.0.0.1', 1778415686, 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383431353638363b636170746368615f636f64657c693a31323b69645f757365727c693a313b6e616d617c733a31333a2241646d696e6973747261746f72223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a31353a2261646d696e40676d61696c2e636f6d223b666f746f7c733a33393a2275706c6f6164732f666f746f5f757365722f757365725f315f313737353831383836322e706e67223b6a61626174616e7c4e3b646570617274656d656e7c4e3b726f6c657c693a313b726f6c65737c613a313a7b693a303b693a313b7d726f6c65735f6e616d65737c613a313a7b693a303b733a31313a2253757065722041646d696e223b7d6c6f676765645f696e7c623a313b);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_uji`
--

CREATE TABLE `jadwal_uji` (
  `id_jadwal` int NOT NULL,
  `id_pengajuan` int NOT NULL,
  `id_mekanik` int NOT NULL,
  `id_mekanik_master` int DEFAULT NULL,
  `id_inspektor` int DEFAULT NULL,
  `tanggal_uji` datetime NOT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `keterangan` text,
  `status` enum('scheduled','done','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `dibuat_oleh` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jadwal_uji`
--

INSERT INTO `jadwal_uji` (`id_jadwal`, `id_pengajuan`, `id_mekanik`, `id_mekanik_master`, `id_inspektor`, `tanggal_uji`, `lokasi`, `keterangan`, `status`, `created_at`, `dibuat_oleh`) VALUES
(1, 6, 3, NULL, NULL, '2026-03-03 12:00:00', 'OHSAA', 'Yaa', 'scheduled', '2026-02-28 00:40:16', 1),
(2, 5, 3, NULL, NULL, '2026-03-13 12:00:00', 'OHSAA', 'Sesuai Jam yah', 'scheduled', '2026-03-10 00:02:11', 1),
(3, 7, 3, NULL, NULL, '2026-03-14 12:00:00', 'OHSAA', 'Sukses', 'scheduled', '2026-03-12 09:19:35', 1),
(4, 3, 3, NULL, NULL, '2026-03-26 12:00:00', 'OHSAA', '', 'done', '2026-03-25 09:46:57', 1),
(5, 8, 3, NULL, NULL, '2026-03-27 12:00:00', 'OHSAA', 'Test', 'done', '2026-03-27 07:29:42', 1),
(6, 13, 3, NULL, NULL, '2026-04-08 12:00:00', 'OHSAA', 'TT', 'done', '2026-04-07 20:59:11', 1),
(7, 14, 3, NULL, NULL, '2026-04-17 12:00:00', 'OHSAA', 'Testing', 'done', '2026-04-08 10:52:30', 1),
(8, 15, 3, 1, 3, '2026-04-13 16:32:00', 'OHSAA', 'Test', 'done', '2026-04-13 16:34:14', 1),
(9, 13, 3, 2, 3, '2026-04-14 13:00:00', 'OHSAA', '', 'done', '2026-04-13 16:46:06', 1),
(10, 16, 3, 2, 3, '2026-04-15 12:00:00', 'OHSAA', 'Testing', 'done', '2026-04-13 19:50:08', 1),
(11, 16, 3, 2, 3, '2026-04-15 12:00:00', 'OHSAA', 'Test', 'done', '2026-04-13 22:56:07', 1),
(12, 18, 3, 3, 3, '2026-04-15 12:00:00', 'OHSAA', 'Test', 'done', '2026-04-13 23:23:08', 1),
(13, 17, 3, 3, 3, '2026-04-17 12:00:00', 'OHSAA', '', 'done', '2026-04-15 18:54:10', 1),
(14, 19, 8, 2, 8, '2026-04-18 12:00:00', 'OHSAA', '', 'done', '2026-04-16 13:47:58', 1),
(15, 20, 8, 2, 8, '2026-04-21 12:00:00', 'OHSAA', '', 'done', '2026-04-21 23:02:15', 1),
(16, 21, 8, 3, 8, '2026-04-23 12:00:00', 'OHSAA', '', 'done', '2026-04-22 12:47:51', 1),
(17, 22, 3, 4, 3, '2026-04-26 12:00:00', 'OHSAA', '', 'done', '2026-04-25 00:15:33', 1),
(18, 12, 8, 3, 8, '2026-04-28 12:00:00', 'OHSAA', '', 'done', '2026-04-28 00:19:37', 1),
(19, 18, 3, 4, 3, '2026-04-30 12:00:00', 'OHSAA', '', 'done', '2026-04-28 00:22:40', 1),
(20, 22, 8, 3, 8, '2026-05-04 12:00:00', 'OHSAA', '', 'scheduled', '2026-05-04 20:33:55', 1),
(21, 19, 8, 1, 8, '2026-05-07 12:00:00', 'OHSAA', '', 'scheduled', '2026-05-05 21:25:19', 1),
(22, 23, 3, 3, 3, '2026-05-07 12:00:00', 'OHSAA', '', 'done', '2026-05-05 21:32:56', 1),
(23, 25, 3, 2, 3, '2026-05-15 12:00:00', 'Pit A', '', 'done', '2026-05-05 21:48:22', 1),
(24, 26, 3, 1, 3, '2026-05-17 12:00:00', 'OHSAA', '', 'done', '2026-05-10 19:57:59', 1),
(25, 27, 3, 1, 3, '2026-05-10 12:00:00', 'OHSAA', 'test', 'done', '2026-05-10 20:05:48', 1),
(26, 28, 8, 1, 8, '2026-05-17 12:00:00', 'OHSAA', 'yy', 'done', '2026-05-17 22:58:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id_kendaraan` int NOT NULL,
  `no_polisi` varchar(20) NOT NULL,
  `is_na_no_polisi` tinyint(1) NOT NULL DEFAULT '0',
  `nomor_unit` varchar(50) DEFAULT NULL,
  `id_tipe_kendaraan` int DEFAULT NULL COMMENT 'FK → tipe_kendaraan.id_tipe_kendaraan',
  `merk` varchar(50) DEFAULT NULL,
  `tipe` varchar(50) DEFAULT NULL,
  `model_unit` varchar(50) DEFAULT NULL,
  `tahun` year DEFAULT NULL,
  `perusahaan` varchar(100) DEFAULT NULL,
  `is_unit_baru` tinyint(1) DEFAULT '0',
  `is_na_nomor_mesin` tinyint(1) NOT NULL DEFAULT '0',
  `is_na_nomor_unit` tinyint(1) NOT NULL DEFAULT '0',
  `is_na_model_unit` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id_kendaraan`, `no_polisi`, `is_na_no_polisi`, `nomor_unit`, `id_tipe_kendaraan`, `merk`, `tipe`, `model_unit`, `tahun`, `perusahaan`, `is_unit_baru`, `is_na_nomor_mesin`, `is_na_nomor_unit`, `is_na_model_unit`, `created_at`) VALUES
(1, 'DB 1234 GT', 0, NULL, 6, 'Volvo', 'D6T', NULL, 2020, NULL, 1, 0, 0, 0, '2026-02-23 04:59:52'),
(2, 'DB 1234 GG', 0, NULL, 6, 'Volvo', 'D6T', NULL, 2021, NULL, 0, 0, 0, 0, '2026-02-23 17:39:49'),
(3, 'DB 12222 GG', 0, 'BD-001', 15, 'Volvo', 'BDD-22DDA', 'BDD-22DDA', 2023, 'PT. Energy Logistics', 1, 0, 0, 0, '2026-02-27 14:42:50'),
(4, 'DB 6728 GG', 0, 'BUS-001', 3, 'Toyota', 'DDZ-22233', 'DDZ-22233', 2023, 'CV. Daya Kreasitama', 1, 0, 0, 0, '2026-03-12 09:02:59'),
(5, 'DB 1111 TT', 0, 'BD-001', 15, 'Volvo', 'DDZ-22233', 'DDZ-22233', 2025, 'CV. Daya Kreasitama', 1, 0, 0, 0, '2026-03-26 21:01:29'),
(6, 'DT 3333 DD', 0, 'FT-2222', 10, 'ZZZZ', 'AAEWE', 'AAEWE', 2025, 'On the Job Training', 1, 0, 0, 0, '2026-03-26 21:03:41'),
(7, 'DB 1232 GG', 0, 'BD-001', 16, 'Volvo', 'BDD-22DDA', 'BDD-22DDA', 0000, 'CV. Puncak Kencana', 1, 0, 0, 0, '2026-03-26 21:09:35'),
(8, 'GG 1111 TT', 0, 'HT2222', 9, 'Toyota', 'BDD-22DDA', 'BDD-22DDA', 0000, 'CV. Daya Kreasitama', 1, 0, 0, 0, '2026-03-26 21:12:46'),
(9, 'GL 5555 DD', 0, 'CT-2222', 12, 'Taata', '2323', '2323', 2021, 'On the Job Training', 1, 0, 0, 0, '2026-04-01 22:23:06'),
(10, 'AZ 2323 GG', 0, 'LT-2222', 2, 'Tres', ' DT ASSSS', ' DT ASSSS', 2025, 'CV. Puncak Kencana', 1, 0, 0, 0, '2026-04-01 22:25:09'),
(11, 'DD 111 TT', 0, 'DT 1111', 4, 'wewe', 'wewewe', 'wewewe', 0000, 'CV. Puncak Kencana', 1, 0, 0, 0, '2026-04-05 21:12:35'),
(12, 'DD 1111 TT', 0, 'BS1111', 3, 'BUS', 'DAZZ222', 'DAZZ222', 2022, 'CV. Daya Kreasitama', 1, 0, 0, 0, '2026-04-13 16:20:46'),
(13, 'DZ 222 DD', 0, 'DT-2222', 15, 'Vaaa', 'dw', 'dw', 2021, 'CV. Charisma', 1, 0, 0, 0, '2026-04-13 19:49:15'),
(14, 'GG 11311 TT', 0, 'DT2232', 11, 'CCC', ' ewewe', ' ewewe', 2022, 'CV. Charisma', 1, 0, 0, 0, '2026-04-13 23:10:28'),
(15, 'XX 333 RR', 0, 'DDD', 4, 'wewew', '23123', '23123', 2021, 'CV. Puncak Kencana', 1, 0, 0, 0, '2026-04-13 23:22:31'),
(16, 'FF 11111 TT', 0, 'DD', 14, 'Volova', 'DD11111', 'DD11111', 2023, 'On the Job Training', 1, 0, 0, 0, '2026-04-15 18:49:52'),
(17, 'DD 4444 TT', 0, 'ADT', 8, 'Testt', 'DD 232323', 'DD 232323', 2021, 'CV. Puncak Kencana', 1, 0, 0, 0, '2026-04-21 22:56:28'),
(18, 'N/A', 0, 'N/A', 8, 'Valve', 'N/A', 'N/A', 2019, 'CV. Puncak Kencana', 1, 0, 0, 0, '2026-04-22 12:47:11'),
(19, 'N/A', 1, 'N/A', 8, 'Test', 'N/A', 'N/A', 0000, 'CV. Daya Kreasitama', 1, 0, 1, 1, '2026-04-22 18:24:11'),
(21, 'N/A', 1, 'N/A', 8, 'Volvo', 'N/A', 'N/A', 2022, 'PT. Hexindo', 1, 0, 1, 1, '2026-05-05 21:30:14'),
(22, 'BZ 2312 TT', 0, 'DD 22222', 17, 'Volvo', 'N/A', 'N/A', 2023, 'PT. Intertek', 1, 0, 0, 1, '2026-05-05 21:40:39'),
(23, 'N/A', 1, 'DD zzz', 15, 'Volvo', 'D23231', 'D23231', 2021, 'CV. Daya Kreasitama', 1, 0, 0, 0, '2026-05-05 21:47:43'),
(24, 'DZ 6565 TT', 0, 'JM11111', 18, 'Jumbo Ter', 'JT-23231', 'JT-23231', 2022, 'CV. Puncak Kencana', 1, 0, 0, 0, '2026-05-10 19:57:40'),
(25, 'DG 2323 TG', 0, 'FORK2222', 10, 'FOKRZ', 'FL2233D', 'FL2233D', 2022, 'CV. Puncak Kencana', 1, 0, 0, 0, '2026-05-10 20:05:29'),
(26, 'DD 3333 XX', 0, 'DZ 2222', 11, 'EXCA', 'DAw2231', 'DAw2231', 2021, 'PT. Indos Cakra Mandiri', 1, 0, 0, 0, '2026-05-17 22:55:53');

-- --------------------------------------------------------

--
-- Table structure for table `ktt_approval`
--

CREATE TABLE `ktt_approval` (
  `id_ktt_approval` int UNSIGNED NOT NULL,
  `id_pengajuan` int NOT NULL,
  `id_ktt` int NOT NULL COMMENT 'id_user KTT yang approve',
  `aksi` enum('approve','reject') COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Approval KTT per-user; stiker rilis setelah 2 KTT approve';

--
-- Dumping data for table `ktt_approval`
--

INSERT INTO `ktt_approval` (`id_ktt_approval`, `id_pengajuan`, `id_ktt`, `aksi`, `catatan`, `created_at`) VALUES
(1, 18, 1, 'approve', '', '2026-04-28 00:23:30'),
(2, 18, 5, 'approve', '', '2026-05-06 20:47:21'),
(3, 27, 1, 'approve', '', '2026-05-10 22:32:00'),
(4, 27, 5, 'approve', '', '2026-05-14 00:26:54');

-- --------------------------------------------------------

--
-- Table structure for table `mekanik_master`
--

CREATE TABLE `mekanik_master` (
  `id_mekanik` int UNSIGNED NOT NULL,
  `nama` varchar(200) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `perusahaan` varchar(200) DEFAULT NULL COMMENT 'Perusahaan/instansi asal mekanik',
  `jabatan` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mekanik_master`
--

INSERT INTO `mekanik_master` (`id_mekanik`, `nama`, `no_hp`, `email`, `perusahaan`, `jabatan`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Claude', '23233', 'Test@gmail.com', 'TT', 'Senior', 1, '2026-04-13 16:23:01', '2026-05-05 21:24:37'),
(2, 'Grok', '23232323', 'Test@gmail.com', 'Az', 'Senior', 1, '2026-04-13 16:45:25', '2026-05-05 21:24:44'),
(3, 'Anti Gravity', '23232323', 'Test@gmail.com', 'Toka', 'Senior', 1, '2026-04-13 23:16:49', '2026-05-05 21:24:31'),
(4, 'Tes', '23232323', 'Test@gmail.com', 'TT', 'Senior', 1, '2026-04-21 22:06:43', '2026-05-05 21:24:49');

-- --------------------------------------------------------

--
-- Table structure for table `mekanik_tipe_kendaraan`
--

CREATE TABLE `mekanik_tipe_kendaraan` (
  `id` int UNSIGNED NOT NULL,
  `id_mekanik` int UNSIGNED NOT NULL,
  `id_tipe_kendaraan` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mekanik_tipe_kendaraan`
--

INSERT INTO `mekanik_tipe_kendaraan` (`id`, `id_mekanik`, `id_tipe_kendaraan`) VALUES
(37, 1, 1),
(36, 1, 2),
(23, 1, 3),
(24, 1, 4),
(33, 1, 5),
(29, 1, 6),
(26, 1, 7),
(21, 1, 8),
(34, 1, 9),
(32, 1, 10),
(31, 1, 11),
(25, 1, 12),
(38, 1, 13),
(40, 1, 14),
(22, 1, 15),
(27, 1, 16),
(28, 1, 17),
(35, 1, 18),
(30, 1, 19),
(39, 1, 20),
(41, 2, 8),
(43, 2, 9),
(42, 2, 15),
(44, 2, 20),
(17, 3, 1),
(16, 3, 2),
(3, 3, 3),
(4, 3, 4),
(13, 3, 5),
(9, 3, 6),
(6, 3, 7),
(1, 3, 8),
(14, 3, 9),
(12, 3, 10),
(11, 3, 11),
(5, 3, 12),
(18, 3, 13),
(20, 3, 14),
(2, 3, 15),
(7, 3, 16),
(8, 3, 17),
(15, 3, 18),
(10, 3, 19),
(19, 3, 20),
(45, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `notif_stiker`
--

CREATE TABLE `notif_stiker` (
  `id_notif` int UNSIGNED NOT NULL,
  `id_sticker` int NOT NULL,
  `id_pengajuan` int NOT NULL,
  `stage` enum('30_hari','14_hari','7_hari','1_hari') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracking notifikasi ekspirasi stiker bertahap';

-- --------------------------------------------------------

--
-- Table structure for table `pencabutan_stiker`
--

CREATE TABLE `pencabutan_stiker` (
  `id_cabut` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_sticker` int NOT NULL COMMENT 'FK ke sticker_release',
  `id_pengajuan` int NOT NULL,
  `id_pemohon` int DEFAULT NULL COMMENT 'User yang mengajukan pencabutan',
  `role_pemohon` int DEFAULT NULL COMMENT 'Role ID pemohon (2=KTT, 3=OHS Supt, 4=Inspektor)',
  `id_ktt` int DEFAULT NULL COMMENT 'Legacy/KTT yang memerintahkan',
  `alasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_request` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_ohs_supt' COMMENT 'menunggu_ohs_supt, menunggu_ktt_1, menunggu_ktt_2, siap_dicabut, dilaksanakan, ditolak',
  `ohs_supt_by` int DEFAULT NULL,
  `ohs_supt_at` datetime DEFAULT NULL,
  `ktt_1_by` int DEFAULT NULL,
  `ktt_1_at` datetime DEFAULT NULL,
  `ktt_2_by` int DEFAULT NULL,
  `ktt_2_at` datetime DEFAULT NULL,
  `catatan_penolakan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_perintah` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_dilaksanakan` datetime DEFAULT NULL,
  `status` enum('diperintahkan','dilaksanakan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diperintahkan',
  `dilaksanakan_oleh` int DEFAULT NULL COMMENT 'id_user Admin OHS yang eksekusi',
  PRIMARY KEY (`id_cabut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pengajuan dan perintah pencabutan stiker kelayakan';

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_approval`
--

CREATE TABLE `pengajuan_approval` (
  `id_approval` int NOT NULL,
  `id_approver` int NOT NULL,
  `id_pengajuan` int NOT NULL,
  `level_approval` varchar(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `catatan` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengajuan_approval`
--

INSERT INTO `pengajuan_approval` (`id_approval`, `id_approver`, `id_pengajuan`, `level_approval`, `status`, `catatan`, `created_at`) VALUES
(2, 1, 3, 'manager', 'approved', '', '2026-02-26 22:49:27'),
(3, 1, 5, 'manager', 'approved', '', '2026-02-26 22:04:06'),
(4, 1, 5, 'admin', 'approved', '', '2026-02-27 14:23:46'),
(5, 1, 3, 'admin_ohs', 'approved', '', '2026-02-26 22:53:05'),
(6, 1, 3, 'admin_ohs', 'approved', '', '2026-02-26 22:51:22'),
(7, 1, 3, 'admin_ohs', 'approved', '', '2026-02-26 23:01:42'),
(8, 1, 3, 'admin_ohs', 'approved', '', '2026-02-26 22:53:11'),
(9, 1, 3, 'admin_ohs', 'approved', '', '2026-02-27 14:22:52'),
(10, 1, 3, 'admin_ohs', 'approved', '', '2026-02-26 23:01:48'),
(11, 1, 5, 'admin_ohs', 'approved', '', '2026-02-27 14:09:01'),
(12, 1, 3, 'admin_ohs', 'approved', '', '2026-02-27 14:47:19'),
(13, 1, 3, 'admin_ohs', 'approved', '', '2026-02-27 14:22:59'),
(14, 1, 5, 'admin_ohs', 'approved', '', '2026-03-10 00:00:01'),
(15, 1, 5, 'admin_ohs', 'approved', '', '2026-02-27 14:23:52'),
(16, 1, 6, 'manager', 'approved', '', '2026-02-27 14:44:14'),
(17, 1, 6, 'admin_ohs', 'approved', '', '2026-02-27 14:49:47'),
(18, 0, 3, 'admin_ohs', 'pending', NULL, '2026-02-27 14:47:19'),
(19, 1, 6, 'admin_ohs', 'approved', '', '2026-02-27 14:47:30'),
(20, 0, 6, 'admin_ohs', 'pending', NULL, '2026-02-27 14:49:47'),
(21, 1, 6, 'admin_ohs', 'approved', '', '2026-02-27 14:49:52'),
(22, 1, 5, 'admin_ohs', 'approved', '', '2026-03-10 00:00:11'),
(23, 1, 5, 'admin_ohs', 'approved', '', '2026-03-10 00:08:28'),
(24, 1, 5, 'ohs_supt', 'approved', '', '2026-03-10 00:20:22'),
(25, 1, 5, 'ktt', 'approved', '', '2026-03-10 00:20:33'),
(26, 0, 7, 'dept_manage', 'pending', NULL, '2026-03-12 09:02:59'),
(27, 1, 7, 'dept_manage', 'approved', '', '2026-03-12 09:04:01'),
(28, 1, 7, 'admin_ohs', 'approved', '', '2026-03-12 09:04:24'),
(29, 1, 7, 'admin_ohs_h', 'approved', '', '2026-03-12 09:38:31'),
(30, 1, 7, 'ohs_supt', 'approved', '', '2026-03-12 09:38:40'),
(31, 1, 7, 'ktt', 'approved', '', '2026-03-12 09:39:15'),
(32, 1, 7, 'release_sti', 'approved', 'Nomor stiker: 111111', '2026-03-12 09:39:35'),
(33, 1, 3, 'dept_manage', 'approved', '', '2026-03-25 09:46:40'),
(34, 1, 3, 'admin_ohs', 'approved', '', '2026-03-25 09:46:48'),
(35, 1, 3, 'admin_ohs_h', 'rejected', 'test', '2026-03-25 09:58:03'),
(36, 0, 8, 'dept_manage', 'pending', NULL, '2026-03-26 21:01:29'),
(37, 0, 9, 'dept_manage', 'pending', NULL, '2026-03-26 21:03:41'),
(38, 0, 10, 'dept_manage', 'pending', NULL, '2026-03-26 21:09:35'),
(39, 0, 11, 'dept_manage', 'pending', NULL, '2026-03-26 21:12:46'),
(40, 1, 8, 'dept_manage', 'approved', '', '2026-03-27 07:29:22'),
(41, 1, 8, 'admin_ohs', 'approved', '', '2026-03-27 07:29:28'),
(42, 1, 8, 'admin_ohs_h', 'approved', '', '2026-03-27 07:35:04'),
(43, 1, 8, 'ohs_supt', 'approved', '', '2026-03-27 07:35:08'),
(44, 1, 8, 'ktt', 'approved', '', '2026-03-27 07:35:14'),
(45, 1, 8, 'release_sti', 'approved', 'Nomor stiker: STIKER-111', '2026-03-27 07:35:30'),
(46, 0, 12, 'dept_manage', 'pending', NULL, '2026-04-01 22:23:07'),
(47, 0, 13, 'dept_manage', 'pending', NULL, '2026-04-01 22:25:09'),
(48, 1, 13, 'dept_manage', 'approved', '', '2026-04-01 22:35:44'),
(49, 0, 14, 'dept_manage', 'pending', NULL, '2026-04-05 21:12:35'),
(50, 1, 13, 'admin_ohs', 'approved', '', '2026-04-07 20:59:01'),
(51, 1, 13, 'admin_ohs_h', 'rejected', 'Belum Lengkap', '2026-04-07 21:05:52'),
(52, 1, 14, 'dept_manage', 'approved', '', '2026-04-08 10:52:00'),
(53, 1, 14, 'admin_ohs', 'approved', '', '2026-04-08 10:52:14'),
(54, 1, 13, 'dept_manage', 'approved', '', '2026-04-08 22:52:36'),
(55, 0, 15, 'dept_manage', 'pending', NULL, '2026-04-13 16:20:46'),
(56, 1, 15, 'dept_manage', 'approved', '', '2026-04-13 16:21:15'),
(57, 1, 15, 'admin_ohs', 'approved', '', '2026-04-13 16:21:41'),
(58, 1, 15, 'ohs_supt', 'approved', '', '2026-04-13 16:36:36'),
(59, 1, 15, 'ktt', 'approved', '', '2026-04-13 16:36:46'),
(60, 1, 15, 'release_sti', 'approved', 'Nomor stiker: STK', '2026-04-13 16:37:01'),
(61, 1, 12, 'dept_manage', 'approved', '', '2026-04-13 16:37:54'),
(62, 1, 13, 'admin_ohs', 'approved', '', '2026-04-13 16:38:06'),
(63, 1, 13, 'ohs_supt', 'approved', '', '2026-04-13 19:42:37'),
(64, 0, 16, 'dept_manage', 'pending', NULL, '2026-04-13 19:49:15'),
(65, 1, 16, 'dept_manage', 'approved', '', '2026-04-13 19:49:29'),
(66, 1, 16, 'admin_ohs', 'approved', '', '2026-04-13 19:49:43'),
(67, 1, 16, 'dept_manage', 'approved', '', '2026-04-13 22:54:00'),
(68, 1, 16, 'admin_ohs', 'approved', '', '2026-04-13 22:55:37'),
(69, 1, 16, 'ohs_supt', 'approved', '', '2026-04-13 22:58:44'),
(70, 1, 16, 'ktt', 'approved', '', '2026-04-13 22:59:09'),
(71, 1, 16, 'release_sti', 'approved', 'Nomor stiker: STIKER-0000113123', '2026-04-13 23:00:26'),
(72, 1, 13, 'ktt', 'approved', '', '2026-04-13 23:01:07'),
(73, 0, 17, 'dept_manage', 'pending', NULL, '2026-04-13 23:10:28'),
(74, 1, 17, 'dept_manage', 'approved', '', '2026-04-13 23:17:06'),
(75, 1, 17, 'admin_ohs', 'approved', '', '2026-04-13 23:17:19'),
(76, 0, 18, 'dept_manage', 'pending', NULL, '2026-04-13 23:22:31'),
(77, 1, 18, 'dept_manage', 'approved', '', '2026-04-13 23:22:44'),
(78, 1, 18, 'admin_ohs', 'approved', '', '2026-04-13 23:22:54'),
(79, 1, 18, 'ohs_supt', 'rejected', 'Masih ada yg salah', '2026-04-13 23:34:40'),
(80, 0, 19, 'dept_manage', 'pending', NULL, '2026-04-15 18:49:52'),
(81, 1, 19, 'dept_manage', 'approved', '', '2026-04-15 18:54:48'),
(82, 1, 19, 'admin_ohs', 'approved', '', '2026-04-15 18:55:12'),
(83, 1, 17, 'ohs_supt', 'approved', '', '2026-04-16 09:19:57'),
(84, 1, 17, 'ktt', 'approved', '', '2026-04-16 09:20:11'),
(85, 0, 20, 'dept_manage', 'pending', NULL, '2026-04-21 22:56:28'),
(86, 1, 20, 'dept_manage', 'approved', '', '2026-04-21 22:56:40'),
(87, 1, 20, 'admin_ohs', 'approved', '', '2026-04-21 22:57:01'),
(88, 0, 21, 'dept_manage', 'pending', NULL, '2026-04-22 12:47:12'),
(89, 1, 21, 'dept_manage', 'approved', '', '2026-04-22 12:47:25'),
(90, 1, 21, 'admin_ohs', 'approved', '', '2026-04-22 12:47:40'),
(91, 1, 20, 'resubmit_ad', 'approved', 'Pengajuan ulang: Test aeawea s ee', '2026-04-22 12:51:29'),
(92, 1, 20, 'dept_manage', 'rejected', 'test', '2026-04-22 12:54:38'),
(93, 1, 19, 'resubmit_ad', 'approved', 'Pengajuan ulang: test test test test', '2026-04-22 18:11:03'),
(94, 1, 18, 'resubmit_ad', 'approved', 'Pengajuan ulang: test test test', '2026-04-22 18:11:23'),
(95, 0, 22, 'dept_manage', 'pending', NULL, '2026-04-22 18:24:11'),
(96, 1, 22, 'dept_manage', 'rejected', 'tolak', '2026-04-22 18:24:33'),
(97, 1, 22, 'edit_admin_', 'approved', 'Diedit dan diajukan ulang: sudah st weawe', '2026-04-22 18:26:03'),
(98, 1, 18, 'dept_manage', 'approved', '', '2026-04-22 18:26:19'),
(99, 1, 22, 'dept_manage', 'approved', '', '2026-04-25 00:15:12'),
(100, 1, 22, 'admin_ohs', 'approved', '', '2026-04-25 00:15:23'),
(101, 1, 12, 'admin_ohs', 'approved', '', '2026-04-28 00:19:25'),
(102, 1, 18, 'admin_ohs', 'approved', '', '2026-04-28 00:22:30'),
(103, 1, 18, 'ohs_supt', 'approved', '', '2026-04-28 00:23:12'),
(104, 1, 18, 'ktt', 'approved', ' [KTT ke-1 — Menunggu KTT ke-2]', '2026-04-28 00:23:30'),
(105, 1, 22, 'dept_manage', 'approved', '', '2026-05-04 20:31:21'),
(106, 1, 22, 'admin_ohs', 'approved', '', '2026-05-04 20:32:03'),
(107, 1, 19, 'dept_manage', 'approved', '', '2026-05-04 20:32:46'),
(108, 1, 19, 'admin_ohs', 'approved', '', '2026-05-04 20:33:02'),
(109, 0, 23, 'dept_manage', 'pending', NULL, '2026-05-05 21:30:14'),
(110, 1, 23, 'dept_manage', 'approved', '', '2026-05-05 21:30:19'),
(111, 1, 23, 'admin_ohs', 'approved', '', '2026-05-05 21:32:13'),
(112, 0, 24, 'dept_manage', 'pending', NULL, '2026-05-05 21:40:39'),
(113, 1, 24, 'dept_manage', 'approved', '', '2026-05-05 21:40:50'),
(114, 0, 25, 'dept_manage', 'pending', NULL, '2026-05-05 21:47:43'),
(115, 1, 25, 'dept_manage', 'approved', '', '2026-05-05 21:47:50'),
(116, 1, 25, 'admin_ohs', 'approved', '', '2026-05-05 21:48:10'),
(117, 5, 18, 'ktt', 'approved', ' [KTT ke-2 — ACC FINAL]', '2026-05-06 20:47:21'),
(118, 0, 26, 'dept_manage', 'pending', NULL, '2026-05-10 19:57:40'),
(119, 1, 26, 'dept_manage', 'approved', '', '2026-05-10 19:57:45'),
(120, 1, 26, 'admin_ohs', 'approved', '', '2026-05-10 19:57:50'),
(121, 0, 27, 'dept_manage', 'pending', NULL, '2026-05-10 20:05:29'),
(122, 1, 27, 'dept_manage', 'approved', '', '2026-05-10 20:05:33'),
(123, 1, 27, 'admin_ohs', 'approved', '', '2026-05-10 20:05:37'),
(124, 1, 27, 'perbaikan_u', 'approved', 'Perbaikan unit selesai. Siap inspeksi ulang. Catatan: All controls, buttons, levers, etc., clearly labelled sudah di perbaiki\r\nReversing alarm operational sudah diperbaiki\r\nPark and service brake operational sudah diperbaiki', '2026-05-10 20:09:08'),
(125, 1, 27, 'ohs_supt', 'approved', '', '2026-05-10 22:31:54'),
(126, 1, 27, 'ktt', 'approved', ' [KTT ke-1 — Menunggu KTT ke-2]', '2026-05-10 22:32:00'),
(127, 5, 27, 'ktt', 'approved', ' [KTT ke-2 — ACC FINAL]', '2026-05-14 00:26:54'),
(128, 0, 28, 'dept_manage', 'pending', NULL, '2026-05-17 22:55:53'),
(129, 1, 28, 'dept_manage', 'approved', '', '2026-05-17 22:56:35'),
(130, 1, 28, 'admin_ohs', 'approved', '', '2026-05-17 22:58:00'),
(131, 1, 28, 'perbaikan_u', 'approved', 'Perbaikan unit selesai. Siap inspeksi ulang. Catatan: weraeaea \r\neaweaweawe', '2026-05-17 23:29:30'),
(132, 1, 28, 'inspeksi_ve', 'approved', '', '2026-05-17 23:29:48'),
(133, 1, 28, 'inspeksi_ve', 'approved', '', '2026-05-17 23:31:50'),
(134, 1, 28, 'inspeksi_ve', 'approved', '', '2026-05-17 23:57:23'),
(135, 1, 28, 'verif_perba', 'approved', 'Verifikasi fisik DITERIMA. Unit siap pengujian ulang.', '2026-05-18 00:17:13');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_lampiran`
--

CREATE TABLE `pengajuan_lampiran` (
  `id_lampiran` int NOT NULL,
  `id_pengajuan` int NOT NULL,
  `jenis_lampiran` enum('stnk','unit_depan','unit_belakang','unit_kiri','unit_kanan','maintenance_record','bukti_perbaikan') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengajuan_lampiran`
--

INSERT INTO `pengajuan_lampiran` (`id_lampiran`, `id_pengajuan`, `jenis_lampiran`, `file_path`, `uploaded_at`) VALUES
(5, 5, 'stnk', 'uploads/lampiran/5/stnk_1771970061.jpg', '2026-02-25 05:54:21'),
(6, 5, 'unit_depan', 'uploads/lampiran/5/unit_depan_1771970061.png', '2026-02-25 05:54:21'),
(7, 5, 'unit_belakang', 'uploads/lampiran/5/unit_belakang_1771970061.jpeg', '2026-02-25 05:54:21'),
(8, 5, 'unit_kiri', 'uploads/lampiran/5/unit_kiri_1771970061.jpeg', '2026-02-25 05:54:21'),
(9, 5, 'unit_kanan', 'uploads/lampiran/5/unit_kanan_1771970061.png', '2026-02-25 05:54:21'),
(10, 6, 'stnk', 'uploads/lampiran/6/stnk_1772174570.jpg', '2026-02-27 14:42:50'),
(11, 6, 'unit_depan', 'uploads/lampiran/6/unit_depan_1772174570.png', '2026-02-27 14:42:50'),
(12, 6, 'unit_belakang', 'uploads/lampiran/6/unit_belakang_1772174570.png', '2026-02-27 14:42:50'),
(13, 6, 'unit_kiri', 'uploads/lampiran/6/unit_kiri_1772174570.png', '2026-02-27 14:42:50'),
(14, 6, 'unit_kanan', 'uploads/lampiran/6/unit_kanan_1772174570.png', '2026-02-27 14:42:50'),
(15, 7, 'stnk', 'uploads/lampiran/7/stnk_1773277379.jpg', '2026-03-12 09:02:59'),
(16, 7, 'unit_depan', 'uploads/lampiran/7/unit_depan_1773277379.png', '2026-03-12 09:02:59'),
(17, 7, 'unit_belakang', 'uploads/lampiran/7/unit_belakang_1773277379.png', '2026-03-12 09:02:59'),
(18, 7, 'unit_kiri', 'uploads/lampiran/7/unit_kiri_1773277379.png', '2026-03-12 09:02:59'),
(19, 7, 'unit_kanan', 'uploads/lampiran/7/unit_kanan_1773277379.png', '2026-03-12 09:02:59'),
(20, 8, 'stnk', 'uploads/lampiran/8/stnk_1774530089.png', '2026-03-26 21:01:29'),
(21, 8, 'unit_depan', 'uploads/lampiran/8/unit_depan_1774530089.png', '2026-03-26 21:01:29'),
(22, 8, 'unit_belakang', 'uploads/lampiran/8/unit_belakang_1774530089.png', '2026-03-26 21:01:29'),
(23, 8, 'unit_kiri', 'uploads/lampiran/8/unit_kiri_1774530089.png', '2026-03-26 21:01:29'),
(24, 8, 'unit_kanan', 'uploads/lampiran/8/unit_kanan_1774530089.png', '2026-03-26 21:01:29'),
(25, 9, 'stnk', 'uploads/lampiran/9/stnk_1774530221.png', '2026-03-26 21:03:41'),
(26, 9, 'unit_depan', 'uploads/lampiran/9/unit_depan_1774530221.png', '2026-03-26 21:03:41'),
(27, 9, 'unit_belakang', 'uploads/lampiran/9/unit_belakang_1774530221.png', '2026-03-26 21:03:41'),
(28, 9, 'unit_kiri', 'uploads/lampiran/9/unit_kiri_1774530221.png', '2026-03-26 21:03:41'),
(29, 9, 'unit_kanan', 'uploads/lampiran/9/unit_kanan_1774530221.png', '2026-03-26 21:03:41'),
(30, 10, 'stnk', 'uploads/lampiran/10/stnk_1774530575.png', '2026-03-26 21:09:35'),
(31, 10, 'unit_depan', 'uploads/lampiran/10/unit_depan_1774530575.png', '2026-03-26 21:09:35'),
(32, 10, 'unit_belakang', 'uploads/lampiran/10/unit_belakang_1774530575.png', '2026-03-26 21:09:35'),
(33, 10, 'unit_kiri', 'uploads/lampiran/10/unit_kiri_1774530575.png', '2026-03-26 21:09:35'),
(34, 10, 'unit_kanan', 'uploads/lampiran/10/unit_kanan_1774530575.png', '2026-03-26 21:09:35'),
(35, 11, 'stnk', 'uploads/lampiran/11/stnk_1774530766.png', '2026-03-26 21:12:46'),
(36, 11, 'unit_depan', 'uploads/lampiran/11/unit_depan_1774530766.png', '2026-03-26 21:12:46'),
(37, 11, 'unit_belakang', 'uploads/lampiran/11/unit_belakang_1774530766.png', '2026-03-26 21:12:46'),
(38, 11, 'unit_kiri', 'uploads/lampiran/11/unit_kiri_1774530766.png', '2026-03-26 21:12:46'),
(39, 11, 'unit_kanan', 'uploads/lampiran/11/unit_kanan_1774530766.png', '2026-03-26 21:12:46'),
(40, 12, 'stnk', 'uploads/lampiran/12/stnk_1775053386.png', '2026-04-01 22:23:06'),
(41, 12, 'unit_depan', 'uploads/lampiran/12/unit_depan_1775053386.png', '2026-04-01 22:23:06'),
(42, 12, 'unit_belakang', 'uploads/lampiran/12/unit_belakang_1775053386.png', '2026-04-01 22:23:07'),
(43, 12, 'unit_kiri', 'uploads/lampiran/12/unit_kiri_1775053387.png', '2026-04-01 22:23:07'),
(44, 12, 'unit_kanan', 'uploads/lampiran/12/unit_kanan_1775053387.png', '2026-04-01 22:23:07'),
(45, 13, 'stnk', 'uploads/lampiran/13/stnk_1775053509.png', '2026-04-01 22:25:09'),
(46, 13, 'unit_depan', 'uploads/lampiran/13/unit_depan_1775053509.png', '2026-04-01 22:25:09'),
(47, 13, 'unit_belakang', 'uploads/lampiran/13/unit_belakang_1775053509.png', '2026-04-01 22:25:09'),
(48, 13, 'unit_kiri', 'uploads/lampiran/13/unit_kiri_1775053509.png', '2026-04-01 22:25:09'),
(49, 13, 'unit_kanan', 'uploads/lampiran/13/unit_kanan_1775053509.png', '2026-04-01 22:25:09'),
(50, 14, 'stnk', 'uploads/lampiran/14/stnk_1775394755.png', '2026-04-05 21:12:35'),
(51, 14, 'unit_depan', 'uploads/lampiran/14/unit_depan_1775394755.png', '2026-04-05 21:12:35'),
(52, 14, 'unit_belakang', 'uploads/lampiran/14/unit_belakang_1775394755.png', '2026-04-05 21:12:35'),
(53, 14, 'unit_kiri', 'uploads/lampiran/14/unit_kiri_1775394755.png', '2026-04-05 21:12:35'),
(54, 14, 'unit_kanan', 'uploads/lampiran/14/unit_kanan_1775394755.png', '2026-04-05 21:12:35'),
(55, 15, 'stnk', 'uploads/lampiran/15/stnk_1776068446.png', '2026-04-13 16:20:46'),
(56, 15, 'unit_depan', 'uploads/lampiran/15/unit_depan_1776068446.png', '2026-04-13 16:20:46'),
(57, 15, 'unit_belakang', 'uploads/lampiran/15/unit_belakang_1776068446.png', '2026-04-13 16:20:46'),
(58, 15, 'unit_kiri', 'uploads/lampiran/15/unit_kiri_1776068446.png', '2026-04-13 16:20:46'),
(59, 15, 'unit_kanan', 'uploads/lampiran/15/unit_kanan_1776068446.png', '2026-04-13 16:20:46'),
(60, 16, 'stnk', 'uploads/lampiran/16/stnk_1776080955.png', '2026-04-13 19:49:15'),
(61, 16, 'unit_depan', 'uploads/lampiran/16/unit_depan_1776080955.png', '2026-04-13 19:49:15'),
(62, 16, 'unit_belakang', 'uploads/lampiran/16/unit_belakang_1776080955.png', '2026-04-13 19:49:15'),
(63, 16, 'unit_kiri', 'uploads/lampiran/16/unit_kiri_1776080955.png', '2026-04-13 19:49:15'),
(64, 16, 'unit_kanan', 'uploads/lampiran/16/unit_kanan_1776080955.png', '2026-04-13 19:49:15'),
(65, 17, 'stnk', 'uploads/lampiran/17/stnk_1776093028.png', '2026-04-13 23:10:28'),
(66, 17, 'unit_depan', 'uploads/lampiran/17/unit_depan_1776093028.png', '2026-04-13 23:10:28'),
(67, 17, 'unit_belakang', 'uploads/lampiran/17/unit_belakang_1776093028.png', '2026-04-13 23:10:28'),
(68, 17, 'unit_kiri', 'uploads/lampiran/17/unit_kiri_1776093028.png', '2026-04-13 23:10:28'),
(69, 17, 'unit_kanan', 'uploads/lampiran/17/unit_kanan_1776093028.png', '2026-04-13 23:10:28'),
(70, 18, 'stnk', 'uploads/lampiran/18/stnk_1776093751.png', '2026-04-13 23:22:31'),
(71, 18, 'unit_depan', 'uploads/lampiran/18/unit_depan_1776093751.png', '2026-04-13 23:22:31'),
(72, 18, 'unit_belakang', 'uploads/lampiran/18/unit_belakang_1776093751.png', '2026-04-13 23:22:31'),
(73, 18, 'unit_kiri', 'uploads/lampiran/18/unit_kiri_1776093751.png', '2026-04-13 23:22:31'),
(74, 18, 'unit_kanan', 'uploads/lampiran/18/unit_kanan_1776093751.png', '2026-04-13 23:22:31'),
(75, 19, 'stnk', 'uploads/lampiran/19/stnk_1776250192.jpg', '2026-04-15 18:49:52'),
(76, 19, 'unit_depan', 'uploads/lampiran/19/unit_depan_1776250192.jpg', '2026-04-15 18:49:52'),
(77, 19, 'unit_belakang', 'uploads/lampiran/19/unit_belakang_1776250192.jpg', '2026-04-15 18:49:52'),
(78, 19, 'unit_kiri', 'uploads/lampiran/19/unit_kiri_1776250192.jpg', '2026-04-15 18:49:52'),
(79, 19, 'unit_kanan', 'uploads/lampiran/19/unit_kanan_1776250192.jpg', '2026-04-15 18:49:52'),
(80, 20, 'stnk', 'uploads/lampiran/20/stnk_1776783388.jpg', '2026-04-21 22:56:28'),
(81, 20, 'unit_depan', 'uploads/lampiran/20/unit_depan_1776783388.jpg', '2026-04-21 22:56:28'),
(82, 20, 'unit_belakang', 'uploads/lampiran/20/unit_belakang_1776783388.jpg', '2026-04-21 22:56:28'),
(83, 20, 'unit_kiri', 'uploads/lampiran/20/unit_kiri_1776783388.jpg', '2026-04-21 22:56:28'),
(84, 20, 'unit_kanan', 'uploads/lampiran/20/unit_kanan_1776783388.jpg', '2026-04-21 22:56:28'),
(85, 21, 'stnk', 'uploads/lampiran/21/stnk_1776833231.png', '2026-04-22 12:47:11'),
(86, 21, 'unit_depan', 'uploads/lampiran/21/unit_depan_1776833231.png', '2026-04-22 12:47:11'),
(87, 21, 'unit_belakang', 'uploads/lampiran/21/unit_belakang_1776833231.png', '2026-04-22 12:47:12'),
(88, 21, 'unit_kiri', 'uploads/lampiran/21/unit_kiri_1776833232.png', '2026-04-22 12:47:12'),
(89, 21, 'unit_kanan', 'uploads/lampiran/21/unit_kanan_1776833232.png', '2026-04-22 12:47:12'),
(90, 26, 'stnk', 'uploads/lampiran/26/stnk_1778414260.png', '2026-05-10 19:57:40'),
(91, 26, 'unit_depan', 'uploads/lampiran/26/unit_depan_1778414260.png', '2026-05-10 19:57:40'),
(92, 26, 'unit_belakang', 'uploads/lampiran/26/unit_belakang_1778414260.png', '2026-05-10 19:57:40'),
(93, 26, 'unit_kiri', 'uploads/lampiran/26/unit_kiri_1778414260.png', '2026-05-10 19:57:40'),
(94, 26, 'unit_kanan', 'uploads/lampiran/26/unit_kanan_1778414260.png', '2026-05-10 19:57:40'),
(95, 27, 'stnk', 'uploads/lampiran/27/stnk_1778414729.png', '2026-05-10 20:05:29'),
(96, 27, 'unit_depan', 'uploads/lampiran/27/unit_depan_1778414729.png', '2026-05-10 20:05:29'),
(97, 27, 'unit_belakang', 'uploads/lampiran/27/unit_belakang_1778414729.png', '2026-05-10 20:05:29'),
(98, 27, 'unit_kiri', 'uploads/lampiran/27/unit_kiri_1778414729.png', '2026-05-10 20:05:29'),
(99, 27, 'unit_kanan', 'uploads/lampiran/27/unit_kanan_1778414729.png', '2026-05-10 20:05:29'),
(100, 28, 'unit_depan', 'uploads/lampiran/28/unit_depan_1779029753.png', '2026-05-17 22:55:53'),
(101, 28, 'unit_belakang', 'uploads/lampiran/28/unit_belakang_1779029753.png', '2026-05-17 22:55:53'),
(102, 28, 'unit_kiri', 'uploads/lampiran/28/unit_kiri_1779029753.png', '2026-05-17 22:55:53'),
(103, 28, 'unit_kanan', 'uploads/lampiran/28/unit_kanan_1779029753.png', '2026-05-17 22:55:53');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_uji`
--

CREATE TABLE `pengajuan_uji` (
  `id_pengajuan` int NOT NULL,
  `id_kendaraan` int NOT NULL,
  `id_pemohon` int NOT NULL,
  `email_pemohon` varchar(100) DEFAULT NULL,
  `tipe_pengajuan` varchar(100) NOT NULL,
  `tipe_akses` varchar(100) NOT NULL DEFAULT 'mining',
  `tujuan` varchar(200) NOT NULL,
  `alasan_pengajuan_ulang` text,
  `pernah_maintenance_luar` tinyint(1) NOT NULL DEFAULT '0',
  `nomor_mesin` varchar(100) NOT NULL,
  `is_na_nomor_mesin` tinyint(1) NOT NULL DEFAULT '0',
  `nomor_rangka` varchar(100) NOT NULL,
  `is_na_nomor_polisi` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('draft','pengajuan_baru','pengajuan_ulang','diterima_manager','ditolak_manager','dijadwalkan','ditolak_admin_ohs','selesai_inspeksi','lulus_inspeksi','tidak_lulus_inspeksi','siap_verifikasi','inspeksi_ulang','diterima_admin_ohs','diterima_ohs_supt','ditolak_ohs_supt','menunggu_ktt_2','acc_ktt','ditolak_ktt','stiker_keluar','dicabut_ktt','rejected') NOT NULL DEFAULT 'draft' COMMENT 'siap_verifikasi = Admin Dept sudah input perbaikan, inspektor belum verifikasi fisik.\r\n         inspeksi_ulang  = Inspektor acc verifikasi fisik, siap pengujian ulang form checklist.',
  `tanggal_pengajuan` datetime DEFAULT CURRENT_TIMESTAMP,
  `tgl_acc_ktt` datetime DEFAULT NULL,
  `ktt_approve_count` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Jumlah KTT yang sudah approve (max 2)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengajuan_uji`
--

INSERT INTO `pengajuan_uji` (`id_pengajuan`, `id_kendaraan`, `id_pemohon`, `email_pemohon`, `tipe_pengajuan`, `tipe_akses`, `tujuan`, `alasan_pengajuan_ulang`, `pernah_maintenance_luar`, `nomor_mesin`, `is_na_nomor_mesin`, `nomor_rangka`, `is_na_nomor_polisi`, `status`, `tanggal_pengajuan`, `tgl_acc_ktt`, `ktt_approve_count`) VALUES
(3, 2, 1, 'jack@gmail.com', 'new_commissioning', 'mining', 'Ayyyyy', NULL, 0, '232323', 0, '232323', 0, 'ditolak_admin_ohs', '2026-02-24 01:40:36', NULL, 0),
(5, 1, 1, 'jack@gmail.com', 'new_commissioning', 'mining', 'tesss', NULL, 0, '222222', 0, '222222', 0, 'stiker_keluar', '2026-02-25 05:54:21', NULL, 0),
(6, 3, 1, 'jack@gmail.com', 'new_commissioning', 'mining', 'test tett e ekkaekaea', NULL, 0, '313123123123', 0, '2323231', 0, 'rejected', '2026-02-27 14:42:50', NULL, 0),
(7, 4, 1, 'harlypoluan99@gmail.com', 'new_commissioning', 'mining', 'Testing', NULL, 0, '13131232323', 0, '111233231', 0, 'stiker_keluar', '2026-03-12 09:02:59', NULL, 0),
(8, 5, 1, 'gideongacha7@gmail.com', 'new_commissioning', 'mining', 'Testing', NULL, 0, '232323', 0, '222222', 0, 'stiker_keluar', '2026-03-26 21:01:29', NULL, 0),
(9, 6, 1, 'gideongacha7@gmail.com', 'new_commissioning', 'mining', 'Testing', NULL, 0, '123123123', 0, '12313', 0, 'pengajuan_baru', '2026-03-26 21:03:41', NULL, 0),
(10, 7, 1, 'gideongacha7@gmail.com', 'new_commissioning', 'mining', 'Testing', NULL, 0, '222222', 0, '1212', 0, 'pengajuan_baru', '2026-03-26 21:09:35', NULL, 0),
(11, 8, 1, 'gideongacha7@gmail.com', 'new_commissioning', 'mining', 'Test', NULL, 0, '131231231231', 0, '1123123123', 0, 'pengajuan_baru', '2026-03-26 21:12:46', NULL, 0),
(12, 9, 1, 'gideongacha7@gmail.com', 'new_commissioning', 'mining', 'Testing', NULL, 0, '123123123', 0, '123123', 0, 'tidak_lulus_inspeksi', '2026-04-01 22:23:06', NULL, 0),
(13, 10, 1, 'gideongacha7@gmail.com', 'new_commissioning', 'non_mining', 'Testing', NULL, 0, '1231231231', 0, '2112312312', 0, 'acc_ktt', '2026-04-01 22:25:09', NULL, 0),
(14, 11, 1, 'gideongacha7@gmail.com', 'new_commissioning', 'non_mining', 'Test', NULL, 0, '1231312', 0, '12313', 0, 'selesai_inspeksi', '2026-04-05 21:12:35', NULL, 0),
(15, 12, 1, 'admin@gmail.com', 'new_commissioning', 'mining', 'Test', NULL, 0, '121111', 0, '21111', 0, 'stiker_keluar', '2026-04-13 16:20:46', NULL, 0),
(16, 13, 1, 'admin@gmail.com', 'new_commissioning', 'mining', 'Test', NULL, 0, '123', 0, '123', 0, 'stiker_keluar', '2026-04-13 19:49:15', NULL, 0),
(17, 14, 1, 'gideongacha7@gmail.com', 'new_commissioning', 'mining', 'test', NULL, 0, '123123123', 0, '1123123', 0, 'acc_ktt', '2026-04-13 23:10:28', '2026-04-16 09:20:11', 0),
(18, 15, 1, 'admin@gmail.com', 'new_commissioning', 'mining', 'Test', 'test test test', 0, '123123', 0, '123123', 0, 'acc_ktt', '2026-04-22 18:11:23', '2026-05-06 20:47:21', 2),
(19, 16, 1, 'admin@gmail.com', 'new_commissioning', 'mining', 'Testing', 'test test test test', 0, '123123', 0, '12313', 0, 'dijadwalkan', '2026-04-22 18:11:03', NULL, 0),
(20, 17, 1, 'admin@gmail.com', 'new_commissioning', 'mining', 'testing', 'Test aeawea s ee', 0, '1312312313', 0, '2313123', 0, 'ditolak_manager', '2026-04-22 12:51:29', NULL, 0),
(21, 18, 1, 'admin@gmail.com', 'new_commissioning', 'mining', 'tesst', NULL, 0, 'N/A', 0, '231313123123', 0, 'lulus_inspeksi', '2026-04-22 12:47:11', NULL, 0),
(22, 19, 1, 'admin@gmail.com', 'new_commissioning', 'mining', 'test', 'sudah st weawe', 0, 'N/A', 0, '12331231231', 0, 'dijadwalkan', '2026-04-22 18:26:03', NULL, 0),
(23, 21, 1, 'admin@test.com', 'new_commissioning', 'mining', 'test', NULL, 0, 'N/A', 0, '323232323', 0, 'tidak_lulus_inspeksi', '2026-05-05 21:30:14', NULL, 0),
(24, 22, 1, 'admin@test.com', 'new_commissioning', 'mining', 'test', NULL, 0, '232323', 0, '2312313123', 0, 'diterima_manager', '2026-05-05 21:40:39', NULL, 0),
(25, 23, 1, 'admin@gmail.com', 'new_commissioning', 'mining', 'test', NULL, 0, '3123123123123', 0, '3231231231', 0, 'tidak_lulus_inspeksi', '2026-05-05 21:47:43', NULL, 0),
(26, 24, 1, 'admin@test.com', 'new_commissioning', 'mining', 'Tujuan', NULL, 0, '1231312313', 0, '13131312232', 0, 'tidak_lulus_inspeksi', '2026-05-10 19:57:40', NULL, 0),
(27, 25, 1, 'adminz@test.com', 'new_commissioning', 'mining', 'Test', NULL, 0, '1231321312312', 0, '231313123123', 0, 'acc_ktt', '2026-05-10 20:05:29', '2026-05-14 00:26:54', 2),
(28, 26, 1, 'admin@test.com', 'new_commissioning', 'mining', 'test', NULL, 0, '123123123123123', 0, '13123123123123', 0, 'lulus_inspeksi', '2026-05-17 22:55:53', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `perbaikan_lampiran`
--

CREATE TABLE `perbaikan_lampiran` (
  `id_lampiran` int UNSIGNED NOT NULL,
  `id_perbaikan` int UNSIGNED NOT NULL,
  `file_path` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bukti_perbaikan',
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perbaikan_lampiran`
--

INSERT INTO `perbaikan_lampiran` (`id_lampiran`, `id_perbaikan`, `file_path`, `jenis`, `uploaded_at`) VALUES
(1, 3, 'uploads/perbaikan/27/bukti_0_1778414948.png', 'bukti_perbaikan', '2026-05-10 20:09:08'),
(2, 3, 'uploads/perbaikan/27/bukti_1_1778414948.png', 'bukti_perbaikan', '2026-05-10 20:09:08'),
(3, 3, 'uploads/perbaikan/27/bukti_2_1778414948.png', 'bukti_perbaikan', '2026-05-10 20:09:08'),
(4, 4, 'uploads/perbaikan/28/bukti_0_1779031770.jpg', 'bukti_perbaikan', '2026-05-17 23:29:30'),
(5, 4, 'uploads/perbaikan/28/bukti_1_1779031770.PNG', 'bukti_perbaikan', '2026-05-17 23:29:30');

-- --------------------------------------------------------

--
-- Table structure for table `perbaikan_unit`
--

CREATE TABLE `perbaikan_unit` (
  `id_perbaikan` int UNSIGNED NOT NULL,
  `id_pengajuan` int NOT NULL,
  `id_uji` int NOT NULL COMMENT 'Referensi hasil inspeksi tidak lulus',
  `tgl_max_perbaikan` date NOT NULL COMMENT 'Deadline perbaikan unit',
  `tgl_selesai` date DEFAULT NULL COMMENT 'Tanggal unit selesai diperbaiki',
  `id_verifikator` int UNSIGNED DEFAULT NULL COMMENT 'User inspektor yang memverifikasi perbaikan',
  `catatan_perbaikan` text COLLATE utf8mb4_unicode_ci COMMENT 'Keterangan perbaikan yang dilakukan',
  `status` enum('menunggu','menunggu_verifikasi','diverifikasi','ditolak_verifikasi','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data perbaikan unit setelah tidak lulus inspeksi';

--
-- Dumping data for table `perbaikan_unit`
--

INSERT INTO `perbaikan_unit` (`id_perbaikan`, `id_pengajuan`, `id_uji`, `tgl_max_perbaikan`, `tgl_selesai`, `id_verifikator`, `catatan_perbaikan`, `status`, `created_at`, `updated_at`) VALUES
(1, 23, 18, '2026-05-30', NULL, 1, 'testing', 'menunggu', '2026-05-08 19:03:14', NULL),
(2, 26, 19, '2026-05-23', NULL, 1, 'Ada beberapa', 'menunggu', '2026-05-10 19:59:28', NULL),
(3, 27, 20, '2026-05-30', '2026-05-10', 1, 'All controls, buttons, levers, etc., clearly labelled sudah di perbaiki\r\nReversing alarm operational sudah diperbaiki\r\nPark and service brake operational sudah diperbaiki', 'diverifikasi', '2026-05-10 20:07:05', '2026-05-10 22:19:07'),
(4, 28, 21, '2026-05-31', '2026-05-18', 1, 'weraeaea \r\neaweaweawe', 'diverifikasi', '2026-05-17 23:28:42', '2026-05-18 00:17:42');

-- --------------------------------------------------------

--
-- Table structure for table `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id_perusahaan` int NOT NULL,
  `nama_perusahaan` varchar(100) NOT NULL,
  `singkatan` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `perusahaan`
--

INSERT INTO `perusahaan` (`id_perusahaan`, `nama_perusahaan`, `singkatan`, `is_active`) VALUES
(1, 'CV. Cahaya Dwi Perkasa', 'CDP', 1),
(2, 'CV. Charisma', 'CHR', 1),
(3, 'CV. Daya Kreasitama', 'CDK', 1),
(4, 'CV. Puncak Kencana', 'CPK', 1),
(5, 'On the Job Training', 'OJT', 1),
(6, 'Police', 'POL', 1),
(7, 'PT Batu Biru Nusantara', 'BBN', 1),
(8, 'PT. AKR', 'AKR', 1),
(9, 'PT. Anggun Permai Tekindo', 'APT', 1),
(10, 'PT. Arlie Labora Utama', 'ALU', 1),
(11, 'PT. Bromindo Mekar Mitra', 'BMM', 1),
(12, 'PT. DNX Indonesia', 'DNX', 1),
(13, 'PT. Eka Dharma Jaya Sakti', 'EDJS', 1),
(14, 'PT. Energy Logistics', 'ELG', 1),
(15, 'PT. G4S', 'G4S', 1),
(16, 'PT. Geopersada Mulia Abadi (GMA)', 'GMA', 1),
(17, 'PT. Hanwha Mining Services Indonesia', 'HMSI', 1),
(18, 'PT. Hexindo', 'HXD', 1),
(19, 'PT. Indos Cakra Mandiri', 'ICM', 1),
(20, 'PT. Intertek', 'ITK', 1),
(21, 'PT. Kilat Jaya', 'KLJ', 1),
(22, 'PT. Liotec Mitra Utama', 'LMU', 1),
(23, 'PT. Macmahon Indonesia', 'MMI', 1),
(24, 'PT. Manado Karya Anugerah', 'MKA', 1),
(25, 'PT. Mandara Fasilitas Indonesia', 'MFI', 1),
(26, 'PT. Maxidrill', NULL, 1),
(27, 'PT. Metso Outotec', NULL, 1),
(28, 'PT. Panca', NULL, 1),
(29, 'PT. Pilar Muda Indotama', NULL, 1),
(30, 'PT. PSI Drilling Service', NULL, 1),
(31, 'PT. Samudera Mulia Abadi (SMA)', NULL, 1),
(32, 'PT. Saribuana Manado', NULL, 1),
(33, 'PT. Tata Wisata', NULL, 1),
(34, 'PT. Tombers Karya Bersama', NULL, 1),
(35, 'PT. Tou Maesa Sejahtera (TMS)', NULL, 1),
(36, 'PT. Trakindo', NULL, 1),
(37, 'PT. Tumou Tou Manado', NULL, 1),
(38, 'PT. United Tractor', NULL, 1),
(39, 'Siloam Hospital', NULL, 1),
(40, 'Visitor', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id_role` int NOT NULL,
  `nama_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `nama_role`) VALUES
(1, 'Super Admin'),
(2, 'KTT'),
(3, 'OHS Superintendent'),
(4, 'Inspektor'),
(5, 'Admin OHS'),
(6, 'Dept Manager'),
(7, 'Admin Departemen'),
(8, 'Planner/Safety');

-- --------------------------------------------------------

--
-- Table structure for table `sticker_release`
--

CREATE TABLE `sticker_release` (
  `id_sticker` int NOT NULL,
  `id_pengajuan` int NOT NULL,
  `nomor_sticker` varchar(50) DEFAULT NULL,
  `tanggal_release` datetime DEFAULT NULL,
  `tgl_expired` datetime DEFAULT NULL,
  `is_expired` tinyint(1) NOT NULL DEFAULT '0',
  `warning_sent` tinyint(1) NOT NULL DEFAULT '0',
  `dicabut` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = stiker sudah dicabut per perintah KTT',
  `tgl_dicabut` datetime DEFAULT NULL,
  `released_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sticker_release`
--

INSERT INTO `sticker_release` (`id_sticker`, `id_pengajuan`, `nomor_sticker`, `tanggal_release`, `tgl_expired`, `is_expired`, `warning_sent`, `dicabut`, `tgl_dicabut`, `released_by`) VALUES
(1, 7, '111111', '2026-03-12 09:39:35', '2026-09-12 09:39:35', 0, 0, 0, NULL, 1),
(2, 8, 'STIKER-111', '2026-03-27 07:35:30', '2026-09-27 07:35:30', 0, 0, 0, NULL, 1),
(3, 15, 'STK', '2026-04-13 16:37:01', '2026-10-13 16:37:01', 0, 0, 0, NULL, 1),
(4, 16, 'STIKER-0000113123', '2026-04-13 23:00:26', '2026-10-13 23:00:26', 0, 0, 0, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tipe_kendaraan`
--

CREATE TABLE `tipe_kendaraan` (
  `id_tipe_kendaraan` int NOT NULL,
  `nama_tipe` varchar(100) NOT NULL,
  `kode_tipe` varchar(30) DEFAULT NULL,
  `doc_no` varchar(30) DEFAULT NULL COMMENT 'Nomor dokumen, mis. TT-OHS-FRO-002E',
  `title_id` varchar(200) DEFAULT NULL COMMENT 'Judul dokumen Bahasa Indonesia',
  `title_en` varchar(200) DEFAULT NULL COMMENT 'Judul dokumen Bahasa Inggris',
  `doc_name_id` varchar(200) DEFAULT NULL COMMENT 'Nama dokumen footer (ID)',
  `doc_name_en` varchar(200) DEFAULT NULL COMMENT 'Nama dokumen footer (EN)',
  `tgl_terbit` date DEFAULT NULL COMMENT 'Tanggal terbit dokumen',
  `tgl_review` date DEFAULT NULL COMMENT 'Tanggal tinjau ulang',
  `no_revisi` varchar(10) DEFAULT '01' COMMENT 'Nomor revisi dokumen',
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tipe_kendaraan`
--

INSERT INTO `tipe_kendaraan` (`id_tipe_kendaraan`, `nama_tipe`, `kode_tipe`, `doc_no`, `title_id`, `title_en`, `doc_name_id`, `doc_name_en`, `tgl_terbit`, `tgl_review`, `no_revisi`, `is_active`) VALUES
(1, 'Light Vehicle', 'LV', 'TT-OHS-FRO-002A', 'DAFTAR PERIKSA UJI KELAYAKAN KENDARAAN RINGAN', 'Light Vehicle Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Kendaraan Ringan', 'Light Vehicle Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(2, 'Light Truck', 'LT', 'TT-OHS-FRO-002D', 'DAFTAR PERIKSA UJI KELAYAKAN KENDARAAN RINGAN', 'Light Truck Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Kendaraan Ringan', 'Light Truck Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(3, 'Bus', 'BUS', 'TT-OHS-FRO-002E', 'DAFTAR PERIKSA UJI KELAYAKAN BIS', 'Bus Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Bis', 'Bus Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(4, 'Bus Manhaul', 'BUS_MH', 'TT-OHS-FRO-002F', 'DAFTAR PERIKSA UJI KELAYAKAN BIS MANHAUL', 'Bus Manhaul Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Bis Manhaul', 'Bus Manhaul Commissioning Checklist', '2025-12-26', '2028-12-27', '01', 1),
(5, 'Fuel Truck', 'FT', 'TT-OHS-FRO-002G', 'DAFTAR PERIKSA UJI KELAYAKAN FUEL TRUCK', 'Fuel Truck Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Fuel Truck', 'Fuel Truck Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(6, 'Dump Truck', 'DT', 'TT-OHS-FRO-002B', 'DAFTAR PERIKSA UJI KELAYAKAN DUMP TRUCK', 'Dump Truck Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Dump Truck', 'Dump Truck Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(7, 'Crane Truck', 'CT', 'TT-OHS-FRO-002I', 'DAFTAR PERIKSA UJI KELAYAKAN CRANE TRUCK', 'Crane Truck Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Crane Truck', 'Crane Truck Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(8, 'ADT', 'ADT', 'TT-OHS-FRO-002J', 'DAFTAR PERIKSA UJI KELAYAKAN ADT', 'Articulated Dump Truck (ADT) Commissioning Checklist', 'Daftar Periksa Uji Kelayakan ADT', 'Articulated Dump Truck (ADT) Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(9, 'Haul Truck', 'HT', 'TT-OHS-FRO-002C', 'DAFTAR PERIKSA UJI KELAYAKAN HAUL TRUCK', 'Haul Truck Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Haul Truck', 'Haul Truck Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(10, 'Forklift', 'FL', 'TT-OHS-FRO-002K', 'DAFTAR PERIKSA UJI KELAYAKAN FORKLIFT', 'Forklift Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Forklift', 'Forklift Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(11, 'Excavator', 'EX', 'TT-OHS-FRO-002L', 'DAFTAR PERIKSA UJI KELAYAKAN EXCAVATOR', 'Excavator Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Excavator', 'Excavator Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(12, 'Compactor', 'CP', 'TT-OHS-FRO-002M', 'DAFTAR PERIKSA UJI KELAYAKAN COMPACTOR', 'Compactor Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Compactor', 'Compactor Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(13, 'Motor Grader', 'MG', 'TT-OHS-FRO-002N', 'DAFTAR PERIKSA UJI KELAYAKAN MOTOR GRADER', 'Motor Grader Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Motor Grader', 'Motor Grader Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(14, 'Wheel Loader', 'WL', 'TT-OHS-FRO-002O', 'DAFTAR PERIKSA UJI KELAYAKAN WHEEL LOADER', 'Wheel Loader Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Wheel Loader', 'Wheel Loader Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(15, 'Bulldozer', 'BD', 'TT-OHS-FRO-002P', 'DAFTAR PERIKSA UJI KELAYAKAN BULLDOZER', 'Bulldozer Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Bulldozer', 'Bulldozer Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(16, 'Crawler', 'CW', 'TT-OHS-FRO-002Q', 'DAFTAR PERIKSA UJI KELAYAKAN CRAWLER', 'Crawler Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Crawler', 'Crawler Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(17, 'Drill Rig', 'DR', 'TT-OHS-FRO-002R', 'DAFTAR PERIKSA UJI KELAYAKAN DRILL RIG', 'Drill Rig Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Drill Rig', 'Drill Rig Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(18, 'Jumbo', 'JB', 'TT-OHS-FRO-002S', 'DAFTAR PERIKSA UJI KELAYAKAN JUMBO', 'Jumbo Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Jumbo', 'Jumbo Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(19, 'Equipment Support', 'ES', 'TT-OHS-FRO-002T', 'DAFTAR PERIKSA UJI KELAYAKAN EQUIPMENT SUPPORT', 'Equipment Support Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Equipment Support', 'Equipment Support Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(20, 'Water Truck', 'WT', 'TT-OHS-FRO-002H', 'DAFTAR PERIKSA UJI KELAYAKAN WATER TRUCK', 'Water Truck Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Water Truck', 'Water Truck Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1),
(21, 'Gen Z', 'GZ', 'TT-OHS-FRO-002U', 'DAFTAR PERIKSA UJI KELAYAKAN GEN Z', 'Gen Z Commissioning Checklist', 'Daftar Periksa Uji Kelayakan Gen Z', 'Gen Z Commissioning Checklist', '2025-12-27', '2028-12-27', '01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `uji_checklist`
--

CREATE TABLE `uji_checklist` (
  `id_checklist` int NOT NULL,
  `id_uji` int NOT NULL,
  `id_item` int UNSIGNED NOT NULL,
  `hasil` enum('yes','no','na') NOT NULL DEFAULT 'na',
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `uji_checklist`
--

INSERT INTO `uji_checklist` (`id_checklist`, `id_uji`, `id_item`, `hasil`, `keterangan`) VALUES
(1, 1, 334, 'na', ''),
(2, 1, 335, 'no', 'kurang'),
(3, 1, 336, 'na', ''),
(4, 1, 337, 'yes', ''),
(5, 1, 338, 'no', 'kurang'),
(6, 1, 339, 'yes', ''),
(7, 1, 340, 'yes', ''),
(8, 1, 341, 'yes', ''),
(9, 1, 342, 'no', 'kurang'),
(10, 1, 343, 'yes', ''),
(11, 1, 344, 'yes', ''),
(12, 1, 345, 'no', 'kurang'),
(13, 1, 346, 'yes', ''),
(14, 1, 347, 'yes', ''),
(15, 1, 348, 'yes', ''),
(16, 1, 349, 'no', 'kurang'),
(17, 1, 350, 'yes', ''),
(18, 1, 351, 'yes', ''),
(19, 1, 352, 'no', ''),
(20, 1, 353, 'yes', ''),
(21, 1, 354, 'no', 'kurang'),
(22, 1, 355, 'yes', ''),
(23, 2, 118, 'yes', ''),
(24, 2, 119, 'yes', ''),
(25, 2, 120, 'yes', ''),
(26, 2, 121, 'yes', ''),
(27, 2, 122, 'yes', ''),
(28, 2, 123, 'yes', ''),
(29, 2, 124, 'yes', ''),
(30, 2, 125, 'yes', ''),
(31, 2, 126, 'yes', ''),
(32, 2, 127, 'yes', ''),
(33, 2, 128, 'yes', ''),
(34, 2, 129, 'yes', ''),
(35, 2, 130, 'yes', ''),
(36, 2, 131, 'yes', ''),
(37, 2, 132, 'yes', ''),
(38, 2, 133, 'yes', ''),
(39, 2, 134, 'yes', ''),
(40, 2, 135, 'yes', ''),
(41, 2, 136, 'yes', ''),
(42, 2, 137, 'yes', ''),
(43, 2, 138, 'yes', ''),
(44, 2, 139, 'yes', ''),
(45, 2, 140, 'yes', ''),
(46, 2, 141, 'yes', ''),
(47, 2, 142, 'yes', ''),
(48, 3, 48, 'yes', ''),
(49, 3, 49, 'yes', ''),
(50, 3, 50, 'yes', ''),
(51, 3, 51, 'yes', ''),
(52, 3, 52, 'yes', ''),
(53, 3, 53, 'yes', ''),
(54, 3, 54, 'yes', ''),
(55, 3, 55, 'yes', ''),
(56, 3, 56, 'yes', ''),
(57, 3, 57, 'yes', ''),
(58, 3, 58, 'yes', ''),
(59, 3, 59, 'yes', ''),
(60, 3, 60, 'yes', ''),
(61, 3, 61, 'yes', ''),
(62, 3, 62, 'yes', ''),
(63, 3, 63, 'yes', ''),
(64, 3, 64, 'yes', ''),
(65, 3, 65, 'yes', ''),
(66, 3, 66, 'yes', ''),
(67, 3, 67, 'yes', ''),
(68, 3, 68, 'yes', ''),
(69, 3, 69, 'yes', ''),
(70, 3, 70, 'yes', ''),
(71, 4, 118, 'yes', ''),
(72, 4, 119, 'yes', ''),
(73, 4, 120, 'yes', ''),
(74, 4, 121, 'no', ''),
(75, 4, 122, 'yes', ''),
(76, 4, 123, 'no', ''),
(77, 4, 124, 'yes', ''),
(78, 4, 125, 'no', ''),
(79, 4, 126, 'yes', ''),
(80, 4, 127, 'no', ''),
(81, 4, 128, 'no', ''),
(82, 4, 129, 'yes', ''),
(83, 4, 130, 'yes', ''),
(84, 4, 131, 'yes', ''),
(85, 4, 132, 'yes', ''),
(86, 4, 133, 'yes', ''),
(87, 4, 134, 'yes', ''),
(88, 4, 135, 'yes', ''),
(89, 4, 136, 'yes', ''),
(90, 4, 137, 'yes', ''),
(91, 4, 138, 'yes', ''),
(92, 4, 139, 'yes', ''),
(93, 4, 140, 'no', ''),
(94, 4, 141, 'no', ''),
(95, 4, 142, 'no', ''),
(96, 5, 334, 'yes', ''),
(97, 5, 335, 'yes', ''),
(98, 5, 336, 'yes', ''),
(99, 5, 337, 'yes', ''),
(100, 5, 338, 'yes', ''),
(101, 5, 339, 'yes', ''),
(102, 5, 340, 'yes', ''),
(103, 5, 341, 'yes', ''),
(104, 5, 342, 'yes', ''),
(105, 5, 343, 'yes', ''),
(106, 5, 344, 'yes', ''),
(107, 5, 345, 'yes', ''),
(108, 5, 346, 'yes', ''),
(109, 5, 347, 'yes', ''),
(110, 5, 348, 'yes', ''),
(111, 5, 349, 'yes', ''),
(112, 5, 350, 'yes', ''),
(113, 5, 351, 'yes', ''),
(114, 5, 352, 'yes', ''),
(115, 5, 353, 'yes', ''),
(116, 5, 354, 'yes', ''),
(117, 5, 355, 'yes', ''),
(142, 7, 71, 'yes', ''),
(143, 7, 72, 'yes', ''),
(144, 7, 73, 'yes', ''),
(145, 7, 74, 'yes', ''),
(146, 7, 75, 'yes', ''),
(147, 7, 76, 'no', ''),
(148, 7, 77, 'yes', ''),
(149, 7, 78, 'yes', ''),
(150, 7, 79, 'yes', ''),
(151, 7, 80, 'yes', ''),
(152, 7, 81, 'yes', ''),
(153, 7, 82, 'yes', ''),
(154, 7, 83, 'yes', ''),
(155, 7, 84, 'no', ''),
(156, 7, 85, 'yes', ''),
(157, 7, 86, 'yes', ''),
(158, 7, 87, 'yes', ''),
(159, 7, 88, 'yes', ''),
(160, 7, 89, 'yes', ''),
(161, 7, 90, 'yes', ''),
(162, 7, 91, 'yes', ''),
(163, 7, 92, 'yes', ''),
(164, 7, 93, 'yes', ''),
(165, 7, 94, 'yes', ''),
(166, 8, 48, 'yes', ''),
(167, 8, 49, 'yes', ''),
(168, 8, 50, 'yes', ''),
(169, 8, 51, 'yes', ''),
(170, 8, 52, 'yes', ''),
(171, 8, 53, 'yes', ''),
(172, 8, 54, 'yes', ''),
(173, 8, 55, 'yes', ''),
(174, 8, 56, 'yes', ''),
(175, 8, 57, 'yes', ''),
(176, 8, 58, 'yes', ''),
(177, 8, 59, 'yes', ''),
(178, 8, 60, 'yes', ''),
(179, 8, 61, 'yes', ''),
(180, 8, 62, 'yes', ''),
(181, 8, 63, 'yes', ''),
(182, 8, 64, 'yes', ''),
(183, 8, 65, 'yes', ''),
(184, 8, 66, 'yes', ''),
(185, 8, 67, 'yes', ''),
(186, 8, 68, 'yes', ''),
(187, 8, 69, 'yes', ''),
(188, 8, 70, 'yes', ''),
(189, 6, 24, 'yes', ''),
(190, 6, 25, 'yes', ''),
(191, 6, 26, 'yes', ''),
(192, 6, 27, 'yes', ''),
(193, 6, 28, 'yes', ''),
(194, 6, 29, 'yes', ''),
(195, 6, 30, 'yes', ''),
(196, 6, 31, 'yes', ''),
(197, 6, 32, 'yes', ''),
(198, 6, 33, 'yes', ''),
(199, 6, 34, 'yes', ''),
(200, 6, 35, 'yes', ''),
(201, 6, 36, 'yes', ''),
(202, 6, 37, 'yes', ''),
(203, 6, 38, 'yes', ''),
(204, 6, 39, 'yes', ''),
(205, 6, 40, 'yes', ''),
(206, 6, 41, 'yes', ''),
(207, 6, 42, 'yes', ''),
(208, 6, 43, 'yes', ''),
(209, 6, 44, 'yes', ''),
(210, 6, 45, 'yes', ''),
(211, 6, 46, 'yes', ''),
(212, 6, 47, 'yes', ''),
(235, 9, 334, 'yes', ''),
(236, 9, 335, 'yes', ''),
(237, 9, 336, 'yes', ''),
(238, 9, 337, 'yes', ''),
(239, 9, 338, 'yes', ''),
(240, 9, 339, 'yes', ''),
(241, 9, 340, 'yes', ''),
(242, 9, 341, 'yes', ''),
(243, 9, 342, 'yes', ''),
(244, 9, 343, 'yes', ''),
(245, 9, 344, 'yes', ''),
(246, 9, 345, 'yes', ''),
(247, 9, 346, 'yes', ''),
(248, 9, 347, 'yes', ''),
(249, 9, 348, 'yes', ''),
(250, 9, 349, 'yes', ''),
(251, 9, 350, 'yes', ''),
(252, 9, 351, 'yes', ''),
(253, 9, 352, 'yes', ''),
(254, 9, 353, 'yes', ''),
(255, 9, 354, 'yes', ''),
(256, 9, 355, 'yes', ''),
(281, 11, 241, 'yes', ''),
(282, 11, 242, 'yes', ''),
(283, 11, 243, 'yes', ''),
(284, 11, 244, 'yes', ''),
(285, 11, 245, 'yes', ''),
(286, 11, 246, 'yes', ''),
(287, 11, 247, 'yes', ''),
(288, 11, 248, 'yes', ''),
(289, 11, 249, 'yes', ''),
(290, 11, 250, 'yes', ''),
(291, 11, 251, 'yes', ''),
(292, 11, 252, 'yes', ''),
(293, 11, 253, 'yes', ''),
(294, 11, 254, 'yes', ''),
(295, 11, 255, 'yes', ''),
(296, 11, 256, 'yes', ''),
(297, 11, 257, 'yes', ''),
(298, 11, 258, 'yes', ''),
(299, 11, 259, 'yes', ''),
(300, 11, 260, 'yes', ''),
(301, 11, 261, 'yes', ''),
(302, 11, 262, 'yes', ''),
(303, 11, 263, 'yes', ''),
(304, 12, 169, 'yes', ''),
(305, 12, 170, 'yes', ''),
(306, 12, 171, 'yes', ''),
(307, 12, 172, 'yes', ''),
(308, 12, 173, 'yes', ''),
(309, 12, 174, 'yes', ''),
(310, 12, 175, 'yes', ''),
(311, 12, 176, 'yes', ''),
(312, 12, 177, 'yes', ''),
(313, 12, 178, 'yes', ''),
(314, 12, 179, 'yes', ''),
(315, 12, 180, 'yes', ''),
(316, 12, 181, 'yes', ''),
(317, 12, 182, 'yes', ''),
(318, 12, 183, 'yes', ''),
(319, 12, 184, 'yes', ''),
(320, 12, 185, 'yes', ''),
(321, 12, 186, 'yes', ''),
(322, 12, 187, 'yes', ''),
(323, 12, 188, 'yes', ''),
(324, 12, 189, 'yes', ''),
(325, 12, 190, 'yes', ''),
(326, 12, 191, 'yes', ''),
(327, 12, 192, 'yes', ''),
(328, 13, 169, 'no', ''),
(329, 13, 170, 'yes', ''),
(330, 13, 171, 'yes', ''),
(331, 13, 172, 'yes', ''),
(332, 13, 173, 'yes', ''),
(333, 13, 174, 'no', ''),
(334, 13, 175, 'yes', ''),
(335, 13, 176, 'no', ''),
(336, 13, 177, 'no', ''),
(337, 13, 178, 'yes', ''),
(338, 13, 179, 'yes', ''),
(339, 13, 180, 'yes', ''),
(340, 13, 181, 'yes', ''),
(341, 13, 182, 'yes', ''),
(342, 13, 183, 'yes', ''),
(343, 13, 184, 'yes', ''),
(344, 13, 185, 'yes', ''),
(345, 13, 186, 'yes', ''),
(346, 13, 187, 'yes', ''),
(347, 13, 188, 'yes', ''),
(348, 13, 189, 'no', ''),
(349, 13, 190, 'yes', ''),
(350, 13, 191, 'yes', ''),
(351, 13, 192, 'no', ''),
(352, 14, 311, 'yes', ''),
(353, 14, 312, 'no', ''),
(354, 14, 313, 'no', ''),
(355, 14, 314, 'no', ''),
(356, 14, 315, 'yes', ''),
(357, 14, 316, 'yes', ''),
(358, 14, 317, 'yes', ''),
(359, 14, 318, 'no', ''),
(360, 14, 319, 'yes', ''),
(361, 14, 320, 'yes', ''),
(362, 14, 321, 'yes', ''),
(363, 14, 322, 'yes', ''),
(364, 14, 323, 'yes', ''),
(365, 14, 324, 'yes', ''),
(366, 14, 325, 'yes', ''),
(367, 14, 326, 'yes', ''),
(368, 14, 327, 'no', ''),
(369, 14, 328, 'yes', ''),
(370, 14, 329, 'yes', ''),
(371, 14, 330, 'yes', ''),
(372, 14, 331, 'yes', ''),
(373, 14, 332, 'yes', ''),
(374, 14, 333, 'yes', ''),
(375, 15, 169, 'no', ''),
(376, 15, 170, 'yes', ''),
(377, 15, 171, 'yes', ''),
(378, 15, 172, 'no', ''),
(379, 15, 173, 'yes', ''),
(380, 15, 174, 'yes', ''),
(381, 15, 175, 'yes', ''),
(382, 15, 176, 'yes', ''),
(383, 15, 177, 'yes', ''),
(384, 15, 178, 'yes', ''),
(385, 15, 179, 'no', ''),
(386, 15, 180, 'yes', ''),
(387, 15, 181, 'yes', ''),
(388, 15, 182, 'yes', ''),
(389, 15, 183, 'yes', ''),
(390, 15, 184, 'yes', ''),
(391, 15, 185, 'yes', ''),
(392, 15, 186, 'no', ''),
(393, 15, 187, 'yes', ''),
(394, 15, 188, 'yes', ''),
(395, 15, 189, 'yes', ''),
(396, 15, 190, 'yes', ''),
(397, 15, 191, 'yes', ''),
(398, 15, 192, 'yes', ''),
(399, 16, 264, 'yes', ''),
(400, 16, 265, 'yes', ''),
(401, 16, 266, 'yes', ''),
(402, 16, 267, 'yes', ''),
(403, 16, 268, 'yes', ''),
(404, 16, 269, 'yes', ''),
(405, 16, 270, 'yes', ''),
(406, 16, 271, 'no', ''),
(407, 16, 272, 'yes', ''),
(408, 16, 273, 'yes', ''),
(409, 16, 274, 'yes', ''),
(410, 16, 275, 'yes', ''),
(411, 16, 276, 'yes', ''),
(412, 16, 277, 'yes', ''),
(413, 16, 278, 'yes', ''),
(414, 16, 279, 'yes', ''),
(415, 16, 280, 'yes', ''),
(416, 16, 281, 'yes', ''),
(417, 16, 282, 'yes', ''),
(418, 16, 283, 'yes', ''),
(419, 16, 284, 'no', ''),
(420, 16, 285, 'yes', ''),
(421, 16, 286, 'yes', ''),
(422, 10, 71, 'yes', ''),
(423, 10, 72, 'yes', ''),
(424, 10, 73, 'yes', ''),
(425, 10, 74, 'yes', ''),
(426, 10, 75, 'yes', ''),
(427, 10, 76, 'yes', ''),
(428, 10, 77, 'yes', ''),
(429, 10, 78, 'yes', ''),
(430, 10, 79, 'yes', ''),
(431, 10, 80, 'yes', ''),
(432, 10, 81, 'yes', ''),
(433, 10, 82, 'yes', ''),
(434, 10, 83, 'yes', ''),
(435, 10, 84, 'yes', ''),
(436, 10, 85, 'yes', ''),
(437, 10, 86, 'yes', ''),
(438, 10, 87, 'yes', ''),
(439, 10, 88, 'yes', ''),
(440, 10, 89, 'yes', ''),
(441, 10, 90, 'yes', ''),
(442, 10, 91, 'yes', ''),
(443, 10, 92, 'yes', ''),
(444, 10, 93, 'yes', ''),
(445, 10, 94, 'yes', ''),
(446, 17, 334, 'yes', ''),
(447, 17, 335, 'yes', ''),
(448, 17, 336, 'no', ''),
(449, 17, 337, 'yes', ''),
(450, 17, 338, 'no', ''),
(451, 17, 339, 'yes', ''),
(452, 17, 340, 'no', ''),
(453, 17, 341, 'yes', ''),
(454, 17, 342, 'yes', ''),
(455, 17, 343, 'no', ''),
(456, 17, 344, 'yes', ''),
(457, 17, 345, 'yes', ''),
(458, 17, 346, 'yes', ''),
(459, 17, 347, 'yes', ''),
(460, 17, 348, 'yes', ''),
(461, 17, 349, 'yes', ''),
(462, 17, 350, 'yes', ''),
(463, 17, 351, 'yes', ''),
(464, 17, 352, 'no', ''),
(465, 17, 353, 'yes', ''),
(466, 17, 354, 'yes', ''),
(467, 17, 355, 'yes', ''),
(468, 18, 169, 'no', ''),
(469, 18, 170, 'yes', ''),
(470, 18, 171, 'yes', ''),
(471, 18, 172, 'no', ''),
(472, 18, 173, 'yes', ''),
(473, 18, 174, 'no', ''),
(474, 18, 175, 'yes', ''),
(475, 18, 176, 'no', ''),
(476, 18, 177, 'no', ''),
(477, 18, 178, 'yes', ''),
(478, 18, 179, 'yes', ''),
(479, 18, 180, 'yes', ''),
(480, 18, 181, 'yes', ''),
(481, 18, 182, 'yes', ''),
(482, 18, 183, 'yes', ''),
(483, 18, 184, 'yes', ''),
(484, 18, 185, 'yes', ''),
(485, 18, 186, 'yes', ''),
(486, 18, 187, 'yes', ''),
(487, 18, 188, 'yes', ''),
(488, 18, 189, 'yes', ''),
(489, 18, 190, 'no', ''),
(490, 18, 191, 'yes', ''),
(491, 18, 192, 'yes', ''),
(492, 19, 388, 'yes', ''),
(493, 19, 389, 'yes', ''),
(494, 19, 390, 'yes', ''),
(495, 19, 391, 'no', 'Error'),
(496, 19, 392, 'yes', ''),
(497, 19, 393, 'yes', ''),
(498, 19, 394, 'yes', ''),
(499, 19, 395, 'yes', ''),
(500, 19, 396, 'yes', ''),
(501, 19, 397, 'yes', ''),
(502, 19, 398, 'yes', ''),
(503, 19, 399, 'yes', ''),
(504, 19, 400, 'no', 'Error'),
(505, 19, 401, 'no', 'Error'),
(506, 19, 402, 'yes', ''),
(507, 19, 403, 'yes', ''),
(508, 19, 404, 'yes', ''),
(509, 19, 405, 'yes', ''),
(510, 19, 406, 'yes', ''),
(511, 19, 407, 'yes', ''),
(512, 19, 408, 'yes', ''),
(513, 19, 409, 'yes', ''),
(514, 19, 410, 'yes', ''),
(515, 19, 411, 'yes', ''),
(540, 20, 217, 'yes', ''),
(541, 20, 218, 'yes', ''),
(542, 20, 219, 'yes', ''),
(543, 20, 220, 'yes', ''),
(544, 20, 221, 'yes', ''),
(545, 20, 222, 'yes', ''),
(546, 20, 223, 'yes', ''),
(547, 20, 224, 'yes', ''),
(548, 20, 225, 'yes', ''),
(549, 20, 226, 'yes', ''),
(550, 20, 227, 'yes', ''),
(551, 20, 228, 'yes', ''),
(552, 20, 229, 'yes', ''),
(553, 20, 230, 'yes', ''),
(554, 20, 231, 'yes', ''),
(555, 20, 232, 'yes', ''),
(556, 20, 233, 'yes', ''),
(557, 20, 234, 'yes', ''),
(558, 20, 235, 'yes', ''),
(559, 20, 236, 'yes', ''),
(560, 20, 237, 'yes', ''),
(561, 20, 238, 'yes', ''),
(562, 20, 239, 'yes', ''),
(563, 20, 240, 'yes', ''),
(587, 21, 248, 'yes', ''),
(588, 21, 249, 'yes', ''),
(589, 21, 250, 'yes', ''),
(590, 21, 251, 'yes', ''),
(591, 21, 252, 'yes', ''),
(592, 21, 253, 'yes', ''),
(593, 21, 254, 'yes', ''),
(594, 21, 255, 'yes', ''),
(595, 21, 256, 'yes', ''),
(596, 21, 257, 'yes', ''),
(597, 21, 258, 'yes', ''),
(598, 21, 259, 'yes', ''),
(599, 21, 260, 'yes', ''),
(600, 21, 261, 'yes', ''),
(601, 21, 262, 'yes', ''),
(602, 21, 263, 'yes', ''),
(603, 21, 241, 'yes', ''),
(604, 21, 242, 'yes', ''),
(605, 21, 243, 'yes', ''),
(606, 21, 244, 'yes', ''),
(607, 21, 245, 'yes', ''),
(608, 21, 246, 'yes', ''),
(609, 21, 247, 'yes', '');

-- --------------------------------------------------------

--
-- Table structure for table `uji_checklist_history`
--

CREATE TABLE `uji_checklist_history` (
  `id_history` int UNSIGNED NOT NULL,
  `id_uji` int NOT NULL COMMENT 'FK ke uji_kelayakan.id_uji',
  `id_pengajuan` int NOT NULL COMMENT 'Denormalisasi untuk query cepat',
  `versi` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Urutan inspeksi: 1=pertama, 2=ulang pertama, dst.',
  `id_item` int UNSIGNED NOT NULL COMMENT 'FK ke checklist_item.id_item',
  `hasil` enum('yes','no','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `snapshot_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu snapshot diambil (= waktu submit inspeksi)',
  `hasil_uji` enum('lulus','tidak_lulus') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hasil keseluruhan uji pada versi ini',
  `nama_inspektor` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `perusahaan_inspektor` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_temuan` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Snapshot uji_checklist sebelum ditimpa pada inspeksi ulang';

--
-- Dumping data for table `uji_checklist_history`
--

INSERT INTO `uji_checklist_history` (`id_history`, `id_uji`, `id_pengajuan`, `versi`, `id_item`, `hasil`, `keterangan`, `snapshot_at`, `hasil_uji`, `nama_inspektor`, `perusahaan_inspektor`, `catatan_temuan`) VALUES
(1, 1, 6, 1, 334, 'na', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(2, 1, 6, 1, 335, 'no', 'kurang', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(3, 1, 6, 1, 336, 'na', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(4, 1, 6, 1, 337, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(5, 1, 6, 1, 338, 'no', 'kurang', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(6, 1, 6, 1, 339, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(7, 1, 6, 1, 340, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(8, 1, 6, 1, 341, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(9, 1, 6, 1, 342, 'no', 'kurang', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(10, 1, 6, 1, 343, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(11, 1, 6, 1, 344, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(12, 1, 6, 1, 345, 'no', 'kurang', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(13, 1, 6, 1, 346, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(14, 1, 6, 1, 347, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(15, 1, 6, 1, 348, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(16, 1, 6, 1, 349, 'no', 'kurang', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(17, 1, 6, 1, 350, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(18, 1, 6, 1, 351, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(19, 1, 6, 1, 352, 'no', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(20, 1, 6, 1, 353, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(21, 1, 6, 1, 354, 'no', 'kurang', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(22, 1, 6, 1, 355, 'yes', '', '2026-02-28 00:47:06', 'tidak_lulus', NULL, NULL, 'Banyak kekurangan'),
(23, 2, 5, 1, 118, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(24, 2, 5, 1, 119, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(25, 2, 5, 1, 120, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(26, 2, 5, 1, 121, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(27, 2, 5, 1, 122, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(28, 2, 5, 1, 123, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(29, 2, 5, 1, 124, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(30, 2, 5, 1, 125, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(31, 2, 5, 1, 126, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(32, 2, 5, 1, 127, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(33, 2, 5, 1, 128, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(34, 2, 5, 1, 129, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(35, 2, 5, 1, 130, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(36, 2, 5, 1, 131, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(37, 2, 5, 1, 132, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(38, 2, 5, 1, 133, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(39, 2, 5, 1, 134, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(40, 2, 5, 1, 135, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(41, 2, 5, 1, 136, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(42, 2, 5, 1, 137, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(43, 2, 5, 1, 138, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(44, 2, 5, 1, 139, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(45, 2, 5, 1, 140, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(46, 2, 5, 1, 141, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(47, 2, 5, 1, 142, 'yes', '', '2026-03-10 00:03:23', 'lulus', NULL, NULL, 'mantap'),
(48, 3, 7, 1, 48, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(49, 3, 7, 1, 49, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(50, 3, 7, 1, 50, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(51, 3, 7, 1, 51, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(52, 3, 7, 1, 52, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(53, 3, 7, 1, 53, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(54, 3, 7, 1, 54, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(55, 3, 7, 1, 55, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(56, 3, 7, 1, 56, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(57, 3, 7, 1, 57, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(58, 3, 7, 1, 58, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(59, 3, 7, 1, 59, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(60, 3, 7, 1, 60, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(61, 3, 7, 1, 61, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(62, 3, 7, 1, 62, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(63, 3, 7, 1, 63, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(64, 3, 7, 1, 64, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(65, 3, 7, 1, 65, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(66, 3, 7, 1, 66, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(67, 3, 7, 1, 67, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(68, 3, 7, 1, 68, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(69, 3, 7, 1, 69, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(70, 3, 7, 1, 70, 'yes', '', '2026-03-12 09:20:58', 'lulus', NULL, NULL, 'Cocok'),
(71, 4, 3, 1, 118, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(72, 4, 3, 1, 119, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(73, 4, 3, 1, 120, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(74, 4, 3, 1, 121, 'no', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(75, 4, 3, 1, 122, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(76, 4, 3, 1, 123, 'no', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(77, 4, 3, 1, 124, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(78, 4, 3, 1, 125, 'no', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(79, 4, 3, 1, 126, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(80, 4, 3, 1, 127, 'no', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(81, 4, 3, 1, 128, 'no', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(82, 4, 3, 1, 129, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(83, 4, 3, 1, 130, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(84, 4, 3, 1, 131, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(85, 4, 3, 1, 132, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(86, 4, 3, 1, 133, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(87, 4, 3, 1, 134, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(88, 4, 3, 1, 135, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(89, 4, 3, 1, 136, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(90, 4, 3, 1, 137, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(91, 4, 3, 1, 138, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(92, 4, 3, 1, 139, 'yes', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(93, 4, 3, 1, 140, 'no', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(94, 4, 3, 1, 141, 'no', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(95, 4, 3, 1, 142, 'no', '', '2026-03-25 09:47:51', 'tidak_lulus', NULL, NULL, ''),
(96, 5, 8, 1, 334, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(97, 5, 8, 1, 335, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(98, 5, 8, 1, 336, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(99, 5, 8, 1, 337, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(100, 5, 8, 1, 338, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(101, 5, 8, 1, 339, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(102, 5, 8, 1, 340, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(103, 5, 8, 1, 341, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(104, 5, 8, 1, 342, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(105, 5, 8, 1, 343, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(106, 5, 8, 1, 344, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(107, 5, 8, 1, 345, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(108, 5, 8, 1, 346, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(109, 5, 8, 1, 347, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(110, 5, 8, 1, 348, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(111, 5, 8, 1, 349, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(112, 5, 8, 1, 350, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(113, 5, 8, 1, 351, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(114, 5, 8, 1, 352, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(115, 5, 8, 1, 353, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(116, 5, 8, 1, 354, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(117, 5, 8, 1, 355, 'yes', '', '2026-03-27 07:34:43', 'lulus', NULL, NULL, ''),
(118, 6, 13, 1, 24, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(119, 6, 13, 1, 25, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(120, 6, 13, 1, 26, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(121, 6, 13, 1, 27, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(122, 6, 13, 1, 28, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(123, 6, 13, 1, 29, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(124, 6, 13, 1, 30, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(125, 6, 13, 1, 31, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(126, 6, 13, 1, 32, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(127, 6, 13, 1, 33, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(128, 6, 13, 1, 34, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(129, 6, 13, 1, 35, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(130, 6, 13, 1, 36, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(131, 6, 13, 1, 37, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(132, 6, 13, 1, 38, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(133, 6, 13, 1, 39, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(134, 6, 13, 1, 40, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(135, 6, 13, 1, 41, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(136, 6, 13, 1, 42, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(137, 6, 13, 1, 43, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(138, 6, 13, 1, 44, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(139, 6, 13, 1, 45, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(140, 6, 13, 1, 46, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(141, 6, 13, 1, 47, 'yes', '', '2026-04-13 16:47:18', 'lulus', 'Jack', 'Cv Gpt', ''),
(142, 7, 14, 1, 71, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(143, 7, 14, 1, 72, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(144, 7, 14, 1, 73, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(145, 7, 14, 1, 74, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(146, 7, 14, 1, 75, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(147, 7, 14, 1, 76, 'no', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(148, 7, 14, 1, 77, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(149, 7, 14, 1, 78, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(150, 7, 14, 1, 79, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(151, 7, 14, 1, 80, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(152, 7, 14, 1, 81, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(153, 7, 14, 1, 82, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(154, 7, 14, 1, 83, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(155, 7, 14, 1, 84, 'no', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(156, 7, 14, 1, 85, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(157, 7, 14, 1, 86, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(158, 7, 14, 1, 87, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(159, 7, 14, 1, 88, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(160, 7, 14, 1, 89, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(161, 7, 14, 1, 90, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(162, 7, 14, 1, 91, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(163, 7, 14, 1, 92, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(164, 7, 14, 1, 93, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(165, 7, 14, 1, 94, 'yes', '', '2026-04-08 10:53:14', 'tidak_lulus', NULL, NULL, ''),
(166, 8, 15, 1, 48, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(167, 8, 15, 1, 49, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(168, 8, 15, 1, 50, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(169, 8, 15, 1, 51, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(170, 8, 15, 1, 52, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(171, 8, 15, 1, 53, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(172, 8, 15, 1, 54, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(173, 8, 15, 1, 55, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(174, 8, 15, 1, 56, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(175, 8, 15, 1, 57, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(176, 8, 15, 1, 58, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(177, 8, 15, 1, 59, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(178, 8, 15, 1, 60, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(179, 8, 15, 1, 61, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(180, 8, 15, 1, 62, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(181, 8, 15, 1, 63, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(182, 8, 15, 1, 64, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(183, 8, 15, 1, 65, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(184, 8, 15, 1, 66, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(185, 8, 15, 1, 67, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(186, 8, 15, 1, 68, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(187, 8, 15, 1, 69, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(188, 8, 15, 1, 70, 'yes', '', '2026-04-13 16:36:24', 'lulus', 'Jack', 'Cv Gpt', 'Span'),
(189, 9, 16, 1, 334, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(190, 9, 16, 1, 335, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(191, 9, 16, 1, 336, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(192, 9, 16, 1, 337, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(193, 9, 16, 1, 338, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(194, 9, 16, 1, 339, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(195, 9, 16, 1, 340, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(196, 9, 16, 1, 341, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(197, 9, 16, 1, 342, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(198, 9, 16, 1, 343, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(199, 9, 16, 1, 344, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(200, 9, 16, 1, 345, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(201, 9, 16, 1, 346, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(202, 9, 16, 1, 347, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(203, 9, 16, 1, 348, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(204, 9, 16, 1, 349, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(205, 9, 16, 1, 350, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(206, 9, 16, 1, 351, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(207, 9, 16, 1, 352, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(208, 9, 16, 1, 353, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(209, 9, 16, 1, 354, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(210, 9, 16, 1, 355, 'yes', '', '2026-04-13 22:58:06', 'lulus', 'Inspektor', 'GPT', ''),
(211, 10, 18, 1, 71, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(212, 10, 18, 1, 72, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(213, 10, 18, 1, 73, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(214, 10, 18, 1, 74, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(215, 10, 18, 1, 75, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(216, 10, 18, 1, 76, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(217, 10, 18, 1, 77, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(218, 10, 18, 1, 78, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(219, 10, 18, 1, 79, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(220, 10, 18, 1, 80, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(221, 10, 18, 1, 81, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(222, 10, 18, 1, 82, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(223, 10, 18, 1, 83, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(224, 10, 18, 1, 84, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(225, 10, 18, 1, 85, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(226, 10, 18, 1, 86, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(227, 10, 18, 1, 87, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(228, 10, 18, 1, 88, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(229, 10, 18, 1, 89, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(230, 10, 18, 1, 90, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(231, 10, 18, 1, 91, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(232, 10, 18, 1, 92, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(233, 10, 18, 1, 93, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(234, 10, 18, 1, 94, 'yes', '', '2026-04-28 00:23:00', 'lulus', 'Inspektor', 'Cv Gpt', ''),
(235, 11, 17, 1, 241, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(236, 11, 17, 1, 242, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(237, 11, 17, 1, 243, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(238, 11, 17, 1, 244, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(239, 11, 17, 1, 245, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(240, 11, 17, 1, 246, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(241, 11, 17, 1, 247, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(242, 11, 17, 1, 248, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(243, 11, 17, 1, 249, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(244, 11, 17, 1, 250, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(245, 11, 17, 1, 251, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(246, 11, 17, 1, 252, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(247, 11, 17, 1, 253, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(248, 11, 17, 1, 254, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(249, 11, 17, 1, 255, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(250, 11, 17, 1, 256, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(251, 11, 17, 1, 257, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(252, 11, 17, 1, 258, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(253, 11, 17, 1, 259, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(254, 11, 17, 1, 260, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(255, 11, 17, 1, 261, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(256, 11, 17, 1, 262, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(257, 11, 17, 1, 263, 'yes', '', '2026-04-16 09:19:46', 'lulus', 'Inspektor Ard', 'Cv Gpt', ''),
(258, 12, 21, 1, 169, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(259, 12, 21, 1, 170, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(260, 12, 21, 1, 171, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(261, 12, 21, 1, 172, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(262, 12, 21, 1, 173, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(263, 12, 21, 1, 174, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(264, 12, 21, 1, 175, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(265, 12, 21, 1, 176, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(266, 12, 21, 1, 177, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(267, 12, 21, 1, 178, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(268, 12, 21, 1, 179, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(269, 12, 21, 1, 180, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(270, 12, 21, 1, 181, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(271, 12, 21, 1, 182, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(272, 12, 21, 1, 183, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(273, 12, 21, 1, 184, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(274, 12, 21, 1, 185, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(275, 12, 21, 1, 186, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(276, 12, 21, 1, 187, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(277, 12, 21, 1, 188, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(278, 12, 21, 1, 189, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(279, 12, 21, 1, 190, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(280, 12, 21, 1, 191, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(281, 12, 21, 1, 192, 'yes', '', '2026-04-22 12:49:13', 'lulus', 'Test Ganti', 'Test', 'Tes'),
(282, 13, 20, 1, 169, 'no', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(283, 13, 20, 1, 170, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(284, 13, 20, 1, 171, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(285, 13, 20, 1, 172, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(286, 13, 20, 1, 173, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(287, 13, 20, 1, 174, 'no', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(288, 13, 20, 1, 175, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(289, 13, 20, 1, 176, 'no', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(290, 13, 20, 1, 177, 'no', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(291, 13, 20, 1, 178, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(292, 13, 20, 1, 179, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(293, 13, 20, 1, 180, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(294, 13, 20, 1, 181, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(295, 13, 20, 1, 182, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(296, 13, 20, 1, 183, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(297, 13, 20, 1, 184, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(298, 13, 20, 1, 185, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(299, 13, 20, 1, 186, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(300, 13, 20, 1, 187, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(301, 13, 20, 1, 188, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(302, 13, 20, 1, 189, 'no', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(303, 13, 20, 1, 190, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(304, 13, 20, 1, 191, 'yes', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(305, 13, 20, 1, 192, 'no', '', '2026-04-22 12:50:43', 'tidak_lulus', 'Jack Poluan', 'Ayy', 'test'),
(306, 14, 19, 1, 311, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(307, 14, 19, 1, 312, 'no', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(308, 14, 19, 1, 313, 'no', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(309, 14, 19, 1, 314, 'no', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(310, 14, 19, 1, 315, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(311, 14, 19, 1, 316, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(312, 14, 19, 1, 317, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(313, 14, 19, 1, 318, 'no', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(314, 14, 19, 1, 319, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(315, 14, 19, 1, 320, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(316, 14, 19, 1, 321, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(317, 14, 19, 1, 322, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(318, 14, 19, 1, 323, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(319, 14, 19, 1, 324, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(320, 14, 19, 1, 325, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(321, 14, 19, 1, 326, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(322, 14, 19, 1, 327, 'no', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(323, 14, 19, 1, 328, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(324, 14, 19, 1, 329, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(325, 14, 19, 1, 330, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(326, 14, 19, 1, 331, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(327, 14, 19, 1, 332, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(328, 14, 19, 1, 333, 'yes', '', '2026-04-22 18:10:23', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(329, 15, 22, 1, 169, 'no', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(330, 15, 22, 1, 170, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(331, 15, 22, 1, 171, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(332, 15, 22, 1, 172, 'no', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(333, 15, 22, 1, 173, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(334, 15, 22, 1, 174, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(335, 15, 22, 1, 175, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(336, 15, 22, 1, 176, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(337, 15, 22, 1, 177, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(338, 15, 22, 1, 178, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(339, 15, 22, 1, 179, 'no', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(340, 15, 22, 1, 180, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(341, 15, 22, 1, 181, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(342, 15, 22, 1, 182, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(343, 15, 22, 1, 183, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(344, 15, 22, 1, 184, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(345, 15, 22, 1, 185, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(346, 15, 22, 1, 186, 'no', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(347, 15, 22, 1, 187, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(348, 15, 22, 1, 188, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(349, 15, 22, 1, 189, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(350, 15, 22, 1, 190, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(351, 15, 22, 1, 191, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(352, 15, 22, 1, 192, 'yes', '', '2026-04-25 00:16:10', 'tidak_lulus', 'Inspektor', 'GPT', ''),
(353, 16, 12, 1, 264, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(354, 16, 12, 1, 265, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(355, 16, 12, 1, 266, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(356, 16, 12, 1, 267, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(357, 16, 12, 1, 268, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(358, 16, 12, 1, 269, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(359, 16, 12, 1, 270, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(360, 16, 12, 1, 271, 'no', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(361, 16, 12, 1, 272, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(362, 16, 12, 1, 273, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(363, 16, 12, 1, 274, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(364, 16, 12, 1, 275, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(365, 16, 12, 1, 276, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(366, 16, 12, 1, 277, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(367, 16, 12, 1, 278, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(368, 16, 12, 1, 279, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(369, 16, 12, 1, 280, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(370, 16, 12, 1, 281, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(371, 16, 12, 1, 282, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(372, 16, 12, 1, 283, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(373, 16, 12, 1, 284, 'no', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(374, 16, 12, 1, 285, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(375, 16, 12, 1, 286, 'yes', '', '2026-04-28 00:21:11', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', 'Test'),
(376, 17, 25, 1, 334, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(377, 17, 25, 1, 335, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(378, 17, 25, 1, 336, 'no', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(379, 17, 25, 1, 337, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(380, 17, 25, 1, 338, 'no', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(381, 17, 25, 1, 339, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(382, 17, 25, 1, 340, 'no', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(383, 17, 25, 1, 341, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(384, 17, 25, 1, 342, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(385, 17, 25, 1, 343, 'no', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(386, 17, 25, 1, 344, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(387, 17, 25, 1, 345, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(388, 17, 25, 1, 346, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(389, 17, 25, 1, 347, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(390, 17, 25, 1, 348, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(391, 17, 25, 1, 349, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(392, 17, 25, 1, 350, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(393, 17, 25, 1, 351, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(394, 17, 25, 1, 352, 'no', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(395, 17, 25, 1, 353, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(396, 17, 25, 1, 354, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(397, 17, 25, 1, 355, 'yes', '', '2026-05-05 21:58:56', 'tidak_lulus', 'Inspektor', 'Ayy', 'Masih banyak temuan'),
(398, 18, 23, 1, 169, 'no', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(399, 18, 23, 1, 170, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(400, 18, 23, 1, 171, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(401, 18, 23, 1, 172, 'no', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(402, 18, 23, 1, 173, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(403, 18, 23, 1, 174, 'no', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(404, 18, 23, 1, 175, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(405, 18, 23, 1, 176, 'no', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(406, 18, 23, 1, 177, 'no', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(407, 18, 23, 1, 178, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(408, 18, 23, 1, 179, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(409, 18, 23, 1, 180, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(410, 18, 23, 1, 181, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(411, 18, 23, 1, 182, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(412, 18, 23, 1, 183, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(413, 18, 23, 1, 184, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(414, 18, 23, 1, 185, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(415, 18, 23, 1, 186, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(416, 18, 23, 1, 187, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(417, 18, 23, 1, 188, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(418, 18, 23, 1, 189, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(419, 18, 23, 1, 190, 'no', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(420, 18, 23, 1, 191, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(421, 18, 23, 1, 192, 'yes', '', '2026-05-08 19:03:14', 'tidak_lulus', 'Inspektor', 'Cv Gpt', 'testing'),
(512, 20, 27, 1, 217, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(513, 20, 27, 1, 218, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(514, 20, 27, 1, 219, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(515, 20, 27, 1, 220, 'no', 'masih kurang', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(516, 20, 27, 1, 221, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(517, 20, 27, 1, 222, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(518, 20, 27, 1, 223, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(519, 20, 27, 1, 224, 'no', 'masih kurang', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(520, 20, 27, 1, 225, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(521, 20, 27, 1, 226, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(522, 20, 27, 1, 227, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(523, 20, 27, 1, 228, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(524, 20, 27, 1, 229, 'no', 'masih kurang', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(525, 20, 27, 1, 230, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(526, 20, 27, 1, 231, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(527, 20, 27, 1, 232, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(528, 20, 27, 1, 233, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(529, 20, 27, 1, 234, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(530, 20, 27, 1, 235, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(531, 20, 27, 1, 236, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(532, 20, 27, 1, 237, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(533, 20, 27, 1, 238, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(534, 20, 27, 1, 239, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(535, 20, 27, 1, 240, 'yes', '', '2026-05-10 22:19:07', 'tidak_lulus', 'Inspektor', 'Ayy', 'Sudah aman'),
(536, 21, 28, 1, 241, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(537, 21, 28, 1, 242, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(538, 21, 28, 1, 243, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(539, 21, 28, 1, 244, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(540, 21, 28, 1, 245, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(541, 21, 28, 1, 246, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(542, 21, 28, 1, 247, 'no', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(543, 21, 28, 1, 248, 'no', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(544, 21, 28, 1, 249, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(545, 21, 28, 1, 250, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(546, 21, 28, 1, 251, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(547, 21, 28, 1, 252, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(548, 21, 28, 1, 253, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(549, 21, 28, 1, 254, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(550, 21, 28, 1, 255, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(551, 21, 28, 1, 256, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(552, 21, 28, 1, 257, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(553, 21, 28, 1, 258, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(554, 21, 28, 1, 259, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(555, 21, 28, 1, 260, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(556, 21, 28, 1, 261, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(557, 21, 28, 1, 262, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', ''),
(558, 21, 28, 1, 263, 'yes', '', '2026-05-18 00:17:42', 'tidak_lulus', 'Jack Poluan', 'Cv Gpt', '');

-- --------------------------------------------------------

--
-- Table structure for table `uji_foto`
--

CREATE TABLE `uji_foto` (
  `id_foto` int UNSIGNED NOT NULL,
  `id_uji` int NOT NULL,
  `jenis` enum('mekanik','temuan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'temuan' COMMENT 'mekanik=foto peserta commissioning, temuan=foto hasil/temuan',
  `file_path` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Foto bukti mekanik & temuan inspeksi';

--
-- Dumping data for table `uji_foto`
--

INSERT INTO `uji_foto` (`id_foto`, `id_uji`, `jenis`, `file_path`, `keterangan`, `uploaded_at`) VALUES
(1, 20, 'mekanik', 'uploads/inspeksi_foto/20/mekanik_1778414825.png', 'test', '2026-05-10 20:07:05'),
(2, 20, 'temuan', 'uploads/inspeksi_foto/20/temuan_0_1778414825.png', 'perbaiki 1', '2026-05-10 20:07:05'),
(3, 20, 'temuan', 'uploads/inspeksi_foto/20/temuan_1_1778414825.png', 'perbaiki 2', '2026-05-10 20:07:05'),
(4, 20, 'temuan', 'uploads/inspeksi_foto/20/temuan_2_1778414825.png', 'perbaiki 3', '2026-05-10 20:07:05'),
(5, 21, 'mekanik', 'uploads/inspeksi_foto/21/mekanik_1779031722.png', NULL, '2026-05-17 23:28:42'),
(6, 21, 'temuan', 'uploads/inspeksi_foto/21/temuan_0_1779031722.png', 'test', '2026-05-17 23:28:42'),
(7, 21, 'temuan', 'uploads/inspeksi_foto/21/temuan_1_1779031722.png', 'test', '2026-05-17 23:28:42');

-- --------------------------------------------------------

--
-- Table structure for table `uji_kelayakan`
--

CREATE TABLE `uji_kelayakan` (
  `id_uji` int NOT NULL,
  `id_pengajuan` int NOT NULL,
  `id_mekanik` int DEFAULT NULL,
  `id_mekanik_master` int UNSIGNED DEFAULT NULL COMMENT 'FK ke mekanik_master.id_mekanik',
  `nama_mekanik` varchar(200) DEFAULT NULL,
  `perusahaan_mekanik` varchar(200) DEFAULT NULL,
  `nama_inspektor` varchar(200) DEFAULT NULL,
  `perusahaan_inspektor` varchar(200) DEFAULT NULL,
  `id_template` int UNSIGNED DEFAULT NULL,
  `tanggal_uji` datetime DEFAULT NULL,
  `hasil` enum('lulus','tidak_lulus') DEFAULT NULL,
  `catatan_temuan` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `uji_kelayakan`
--

INSERT INTO `uji_kelayakan` (`id_uji`, `id_pengajuan`, `id_mekanik`, `id_mekanik_master`, `nama_mekanik`, `perusahaan_mekanik`, `nama_inspektor`, `perusahaan_inspektor`, `id_template`, `tanggal_uji`, `hasil`, `catatan_temuan`, `updated_at`, `created_at`) VALUES
(1, 6, 3, NULL, NULL, NULL, NULL, NULL, 15, '2026-02-28 00:00:00', 'tidak_lulus', 'Banyak kekurangan', NULL, '2026-02-28 00:47:06'),
(2, 5, 1, NULL, NULL, NULL, NULL, NULL, 6, '2026-03-10 00:00:00', 'lulus', 'mantap', NULL, '2026-03-10 00:03:23'),
(3, 7, 1, NULL, NULL, NULL, NULL, NULL, 3, '2026-03-12 00:00:00', 'lulus', 'Cocok', NULL, '2026-03-12 09:20:58'),
(4, 3, 1, NULL, NULL, NULL, NULL, NULL, 6, '2026-03-25 00:00:00', 'tidak_lulus', '', '2026-03-25 09:47:51', '2026-03-25 09:47:51'),
(5, 8, 1, NULL, NULL, NULL, NULL, NULL, 15, '2026-03-27 00:00:00', 'lulus', '', '2026-03-27 07:34:43', '2026-03-27 07:34:43'),
(6, 13, 1, 2, NULL, NULL, 'Jack', 'Cv Gpt', 2, '2026-04-07 00:00:00', 'lulus', '', '2026-04-13 16:47:18', '2026-04-07 21:00:06'),
(7, 14, 1, NULL, NULL, NULL, NULL, NULL, 4, '2026-04-08 00:00:00', 'tidak_lulus', '', '2026-04-08 10:53:14', '2026-04-08 10:53:14'),
(8, 15, 1, 1, NULL, NULL, 'Jack', 'Cv Gpt', 3, '2026-04-13 00:00:00', 'lulus', 'Span', '2026-04-13 16:36:24', '2026-04-13 16:36:24'),
(9, 16, 1, 2, NULL, NULL, 'Inspektor', 'GPT', 15, '2026-04-13 00:00:00', 'lulus', '', '2026-04-13 22:58:06', '2026-04-13 22:53:10'),
(10, 18, 1, 3, 'Administrator', 'Az', 'Inspektor', 'Cv Gpt', 4, '2026-04-13 00:00:00', 'lulus', '', '2026-04-28 00:23:00', '2026-04-13 23:33:56'),
(11, 17, 1, 3, NULL, NULL, 'Inspektor Ard', 'Cv Gpt', 11, '2026-04-16 00:00:00', 'lulus', '', '2026-04-16 09:19:46', '2026-04-16 09:19:46'),
(12, 21, 1, NULL, 'Test Ganti', 'Toka', 'Test Ganti', 'Test', 8, '2026-04-22 00:00:00', 'lulus', 'Tes', '2026-04-22 12:49:13', '2026-04-22 12:49:13'),
(13, 20, 1, NULL, 'Grok', 'Az', 'Jack Poluan', 'Ayy', 8, '2026-04-22 00:00:00', 'tidak_lulus', 'test', '2026-04-22 12:50:43', '2026-04-22 12:50:43'),
(14, 19, 1, NULL, 'Grok', 'Az', 'Jack Poluan', 'Cv Gpt', 14, '2026-04-22 00:00:00', 'tidak_lulus', '', '2026-04-22 18:10:23', '2026-04-22 18:10:23'),
(15, 22, 1, NULL, 'Tes', 'TT', 'Inspektor', 'GPT', 8, '2026-04-25 00:00:00', 'tidak_lulus', '', '2026-04-25 00:16:10', '2026-04-25 00:16:10'),
(16, 12, 1, NULL, 'Anti Gravity', 'Toka', 'Jack Poluan', 'Cv Gpt', 12, '2026-04-28 00:00:00', 'tidak_lulus', 'Test', '2026-04-28 00:21:11', '2026-04-28 00:21:11'),
(17, 25, 1, NULL, 'Grok', 'Az', 'Inspektor', 'Ayy', 15, '2026-05-05 00:00:00', 'tidak_lulus', 'Masih banyak temuan', '2026-05-05 21:58:56', '2026-05-05 21:58:56'),
(18, 23, 1, NULL, 'Anti Gravity', 'Toka', 'Inspektor', 'Cv Gpt', 8, '2026-05-08 00:00:00', 'tidak_lulus', 'testing', '2026-05-08 19:03:14', '2026-05-08 19:03:14'),
(19, 26, 1, NULL, 'Claude', 'TT', 'Inspektor', 'GPT', 18, '2026-05-10 00:00:00', 'tidak_lulus', 'Ada beberapa', '2026-05-10 19:59:28', '2026-05-10 19:59:28'),
(20, 27, 1, NULL, 'Administrator', 'TT', 'Inspektor', 'Ayy', 10, '2026-05-10 00:00:00', 'lulus', 'Sudah aman', '2026-05-10 22:19:07', '2026-05-10 20:07:05'),
(21, 28, 1, NULL, 'Administrator', 'TT', 'Jack Poluan', 'Cv Gpt', 11, '2026-05-17 00:00:00', 'lulus', '', '2026-05-18 00:17:42', '2026-05-17 23:28:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `id_role` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `foto` varchar(200) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `departemen` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `id_role`, `nama`, `username`, `email`, `foto`, `jabatan`, `no_hp`, `departemen`, `password`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrator', 'admin', 'admin@gmail.com', 'uploads/foto_user/user_1_1775818862.png', NULL, NULL, NULL, '$2y$10$9JmqLtLuImhzuXlJTxvKqeL1VgCZ/WOcGlDRClBv2XJ/8ZsGW9JnO', 1, '2026-02-23 02:46:54', '2026-04-10 19:01:02'),
(2, 7, 'User Test', 'user', 'user@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$9JmqLtLuImhzuXlJTxvKqeL1VgCZ/WOcGlDRClBv2XJ/8ZsGW9JnO', 1, '2026-02-25 05:25:34', '2026-03-12 08:39:30'),
(3, 4, 'Inspektor', 'inspektor', 'inspektor@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$9JmqLtLuImhzuXlJTxvKqeL1VgCZ/WOcGlDRClBv2XJ/8ZsGW9JnO', 1, '2026-02-25 05:25:34', '2026-04-13 16:43:12'),
(4, 1, 'OHS Test', 'ohs', 'ohs@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$9JmqLtLuImhzuXlJTxvKqeL1VgCZ/WOcGlDRClBv2XJ/8ZsGW9JnO', 1, '2026-02-25 05:25:34', '2026-04-13 22:50:08'),
(5, 2, 'KTT Test', 'ktt', 'ktt@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$9JmqLtLuImhzuXlJTxvKqeL1VgCZ/WOcGlDRClBv2XJ/8ZsGW9JnO', 1, '2026-02-25 05:25:34', '2026-03-12 08:39:30'),
(6, 3, 'OHS Superintendent', 'ohssupt', 'ohssupt@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$9JmqLtLuImhzuXlJTxvKqeL1VgCZ/WOcGlDRClBv2XJ/8ZsGW9JnO', 1, '2026-03-12 08:39:56', '2026-03-12 08:39:56'),
(7, 6, 'Dept Manager', 'deptmgr', 'deptmgr@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$9JmqLtLuImhzuXlJTxvKqeL1VgCZ/WOcGlDRClBv2XJ/8ZsGW9JnO', 1, '2026-03-12 08:39:56', '2026-03-12 08:39:56'),
(8, 4, 'Jack Poluan', 'jack22', 'test@ggg.com', NULL, 'Manajr', '23232323', 'CV', '$2y$10$f48cjVgtzRyQNDVRGI9rCOqQ2mKD5X0ziq82De9MilhwyZD0Voxiq', 1, '2026-04-13 23:18:35', '2026-04-13 23:18:35');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id_user_role` int NOT NULL,
  `id_user` int NOT NULL,
  `id_role` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id_user_role`, `id_user`, `id_role`) VALUES
(6, 1, 1),
(7, 2, 7),
(8, 3, 4),
(10, 5, 2),
(11, 6, 3),
(12, 7, 6),
(13, 4, 1),
(14, 4, 5),
(15, 8, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `tabel` (`tabel`),
  ADD KEY `id_ref` (`id_ref`);

--
-- Indexes for table `checklist_item`
--
ALTER TABLE `checklist_item`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `fk_item_template` (`id_template`);

--
-- Indexes for table `checklist_template`
--
ALTER TABLE `checklist_template`
  ADD PRIMARY KEY (`id_template`),
  ADD UNIQUE KEY `uk_kode` (`kode`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indexes for table `jadwal_uji`
--
ALTER TABLE `jadwal_uji`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`),
  ADD KEY `id_pengajuan` (`id_pengajuan`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD KEY `idx_kendaraan_tipe` (`id_tipe_kendaraan`);

--
-- Indexes for table `ktt_approval`
--
ALTER TABLE `ktt_approval`
  ADD PRIMARY KEY (`id_ktt_approval`),
  ADD UNIQUE KEY `uq_ktt_pengajuan` (`id_pengajuan`,`id_ktt`),
  ADD KEY `idx_ktt_pengajuan` (`id_pengajuan`),
  ADD KEY `fk_ktt_approval_user` (`id_ktt`);

--
-- Indexes for table `mekanik_master`
--
ALTER TABLE `mekanik_master`
  ADD PRIMARY KEY (`id_mekanik`);

--
-- Indexes for table `mekanik_tipe_kendaraan`
--
ALTER TABLE `mekanik_tipe_kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mekanik_tipe` (`id_mekanik`,`id_tipe_kendaraan`),
  ADD KEY `fk_mtk_tipe` (`id_tipe_kendaraan`);

--
-- Indexes for table `notif_stiker`
--
ALTER TABLE `notif_stiker`
  ADD PRIMARY KEY (`id_notif`),
  ADD UNIQUE KEY `uq_notif_stage` (`id_sticker`,`stage`),
  ADD KEY `idx_notif_pengajuan` (`id_pengajuan`);

--
-- Indexes for table `pencabutan_stiker`
--
ALTER TABLE `pencabutan_stiker`
  ADD PRIMARY KEY (`id_cabut`),
  ADD KEY `idx_cabut_pengajuan` (`id_pengajuan`),
  ADD KEY `idx_cabut_sticker` (`id_sticker`),
  ADD KEY `fk_cabut_ktt` (`id_ktt`),
  ADD KEY `fk_cabut_oleh` (`dilaksanakan_oleh`);

--
-- Indexes for table `pengajuan_approval`
--
ALTER TABLE `pengajuan_approval`
  ADD PRIMARY KEY (`id_approval`);

--
-- Indexes for table `pengajuan_lampiran`
--
ALTER TABLE `pengajuan_lampiran`
  ADD PRIMARY KEY (`id_lampiran`),
  ADD KEY `id_pengajuan` (`id_pengajuan`);

--
-- Indexes for table `pengajuan_uji`
--
ALTER TABLE `pengajuan_uji`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `id_kendaraan` (`id_kendaraan`),
  ADD KEY `id_pemohon` (`id_pemohon`),
  ADD KEY `status` (`status`),
  ADD KEY `idx_tgl_pengajuan` (`tanggal_pengajuan`),
  ADD KEY `idx_status_tgl` (`status`,`tanggal_pengajuan`);

--
-- Indexes for table `perbaikan_lampiran`
--
ALTER TABLE `perbaikan_lampiran`
  ADD PRIMARY KEY (`id_lampiran`),
  ADD KEY `fk_pl_perbaikan` (`id_perbaikan`);

--
-- Indexes for table `perbaikan_unit`
--
ALTER TABLE `perbaikan_unit`
  ADD PRIMARY KEY (`id_perbaikan`),
  ADD KEY `fk_pb_pengajuan` (`id_pengajuan`),
  ADD KEY `fk_pb_uji` (`id_uji`),
  ADD KEY `idx_pb_pengajuan` (`id_pengajuan`);

--
-- Indexes for table `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id_perusahaan`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `sticker_release`
--
ALTER TABLE `sticker_release`
  ADD PRIMARY KEY (`id_sticker`),
  ADD KEY `released_by` (`released_by`),
  ADD KEY `id_pengajuan` (`id_pengajuan`),
  ADD KEY `idx_tgl_expired` (`tgl_expired`),
  ADD KEY `idx_is_expired` (`is_expired`),
  ADD KEY `idx_dicabut` (`dicabut`);

--
-- Indexes for table `tipe_kendaraan`
--
ALTER TABLE `tipe_kendaraan`
  ADD PRIMARY KEY (`id_tipe_kendaraan`);

--
-- Indexes for table `uji_checklist`
--
ALTER TABLE `uji_checklist`
  ADD PRIMARY KEY (`id_checklist`),
  ADD UNIQUE KEY `uk_uji_item` (`id_uji`,`id_item`),
  ADD KEY `id_uji` (`id_uji`),
  ADD KEY `id_item` (`id_item`);

--
-- Indexes for table `uji_checklist_history`
--
ALTER TABLE `uji_checklist_history`
  ADD PRIMARY KEY (`id_history`),
  ADD KEY `idx_hist_uji` (`id_uji`),
  ADD KEY `idx_hist_pengajuan` (`id_pengajuan`),
  ADD KEY `idx_hist_versi` (`id_uji`,`versi`),
  ADD KEY `idx_hist_item` (`id_item`);

--
-- Indexes for table `uji_foto`
--
ALTER TABLE `uji_foto`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `idx_uji_foto` (`id_uji`),
  ADD KEY `idx_uji_foto_jenis` (`id_uji`,`jenis`);

--
-- Indexes for table `uji_kelayakan`
--
ALTER TABLE `uji_kelayakan`
  ADD PRIMARY KEY (`id_uji`),
  ADD KEY `id_mekanik` (`id_mekanik`),
  ADD KEY `id_pengajuan` (`id_pengajuan`),
  ADD KEY `id_mekanik_master` (`id_mekanik_master`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id_user_role`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT for table `checklist_item`
--
ALTER TABLE `checklist_item`
  MODIFY `id_item` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=431;

--
-- AUTO_INCREMENT for table `checklist_template`
--
ALTER TABLE `checklist_template`
  MODIFY `id_template` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `jadwal_uji`
--
ALTER TABLE `jadwal_uji`
  MODIFY `id_jadwal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id_kendaraan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `ktt_approval`
--
ALTER TABLE `ktt_approval`
  MODIFY `id_ktt_approval` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mekanik_master`
--
ALTER TABLE `mekanik_master`
  MODIFY `id_mekanik` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mekanik_tipe_kendaraan`
--
ALTER TABLE `mekanik_tipe_kendaraan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `notif_stiker`
--
ALTER TABLE `notif_stiker`
  MODIFY `id_notif` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pencabutan_stiker`
--
ALTER TABLE `pencabutan_stiker`
  MODIFY `id_cabut` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_approval`
--
ALTER TABLE `pengajuan_approval`
  MODIFY `id_approval` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `pengajuan_lampiran`
--
ALTER TABLE `pengajuan_lampiran`
  MODIFY `id_lampiran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `pengajuan_uji`
--
ALTER TABLE `pengajuan_uji`
  MODIFY `id_pengajuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `perbaikan_lampiran`
--
ALTER TABLE `perbaikan_lampiran`
  MODIFY `id_lampiran` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `perbaikan_unit`
--
ALTER TABLE `perbaikan_unit`
  MODIFY `id_perbaikan` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id_perusahaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sticker_release`
--
ALTER TABLE `sticker_release`
  MODIFY `id_sticker` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tipe_kendaraan`
--
ALTER TABLE `tipe_kendaraan`
  MODIFY `id_tipe_kendaraan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `uji_checklist`
--
ALTER TABLE `uji_checklist`
  MODIFY `id_checklist` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=610;

--
-- AUTO_INCREMENT for table `uji_checklist_history`
--
ALTER TABLE `uji_checklist_history`
  MODIFY `id_history` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=559;

--
-- AUTO_INCREMENT for table `uji_foto`
--
ALTER TABLE `uji_foto`
  MODIFY `id_foto` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `uji_kelayakan`
--
ALTER TABLE `uji_kelayakan`
  MODIFY `id_uji` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id_user_role` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checklist_item`
--
ALTER TABLE `checklist_item`
  ADD CONSTRAINT `fk_item_template` FOREIGN KEY (`id_template`) REFERENCES `checklist_template` (`id_template`);

--
-- Constraints for table `jadwal_uji`
--
ALTER TABLE `jadwal_uji`
  ADD CONSTRAINT `jadwal_uji_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan_uji` (`id_pengajuan`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_uji_ibfk_2` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD CONSTRAINT `fk_kendaraan_tipe` FOREIGN KEY (`id_tipe_kendaraan`) REFERENCES `tipe_kendaraan` (`id_tipe_kendaraan`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `ktt_approval`
--
ALTER TABLE `ktt_approval`
  ADD CONSTRAINT `fk_ktt_approval_pengajuan` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan_uji` (`id_pengajuan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ktt_approval_user` FOREIGN KEY (`id_ktt`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `mekanik_tipe_kendaraan`
--
ALTER TABLE `mekanik_tipe_kendaraan`
  ADD CONSTRAINT `fk_mtk_mekanik` FOREIGN KEY (`id_mekanik`) REFERENCES `mekanik_master` (`id_mekanik`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mtk_tipe` FOREIGN KEY (`id_tipe_kendaraan`) REFERENCES `tipe_kendaraan` (`id_tipe_kendaraan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notif_stiker`
--
ALTER TABLE `notif_stiker`
  ADD CONSTRAINT `fk_notif_stiker` FOREIGN KEY (`id_sticker`) REFERENCES `sticker_release` (`id_sticker`) ON DELETE CASCADE;

--
-- Constraints for table `pencabutan_stiker`
--
ALTER TABLE `pencabutan_stiker`
  ADD CONSTRAINT `fk_cabut_ktt` FOREIGN KEY (`id_ktt`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `fk_cabut_oleh` FOREIGN KEY (`dilaksanakan_oleh`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cabut_pengajuan` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan_uji` (`id_pengajuan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cabut_sticker` FOREIGN KEY (`id_sticker`) REFERENCES `sticker_release` (`id_sticker`) ON DELETE CASCADE;

--
-- Constraints for table `pengajuan_lampiran`
--
ALTER TABLE `pengajuan_lampiran`
  ADD CONSTRAINT `pengajuan_lampiran_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan_uji` (`id_pengajuan`) ON DELETE CASCADE;

--
-- Constraints for table `pengajuan_uji`
--
ALTER TABLE `pengajuan_uji`
  ADD CONSTRAINT `pengajuan_uji_ibfk_1` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`),
  ADD CONSTRAINT `pengajuan_uji_ibfk_2` FOREIGN KEY (`id_pemohon`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `perbaikan_lampiran`
--
ALTER TABLE `perbaikan_lampiran`
  ADD CONSTRAINT `fk_pl_perbaikan` FOREIGN KEY (`id_perbaikan`) REFERENCES `perbaikan_unit` (`id_perbaikan`) ON DELETE CASCADE;

--
-- Constraints for table `sticker_release`
--
ALTER TABLE `sticker_release`
  ADD CONSTRAINT `sticker_release_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan_uji` (`id_pengajuan`) ON DELETE CASCADE,
  ADD CONSTRAINT `sticker_release_ibfk_2` FOREIGN KEY (`released_by`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `uji_foto`
--
ALTER TABLE `uji_foto`
  ADD CONSTRAINT `fk_uji_foto_uji` FOREIGN KEY (`id_uji`) REFERENCES `uji_kelayakan` (`id_uji`) ON DELETE CASCADE;

--
-- Constraints for table `uji_kelayakan`
--
ALTER TABLE `uji_kelayakan`
  ADD CONSTRAINT `uji_kelayakan_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan_uji` (`id_pengajuan`) ON DELETE CASCADE,
  ADD CONSTRAINT `uji_kelayakan_ibfk_2` FOREIGN KEY (`id_mekanik`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `uji_kelayakan_ibfk_3` FOREIGN KEY (`id_mekanik_master`) REFERENCES `mekanik_master` (`id_mekanik`) ON DELETE SET NULL;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
