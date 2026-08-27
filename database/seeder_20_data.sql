-- ============================================================
-- SQL SEEDER: 20 Data Tester Komisioning Uji Kelayakan
-- Full Isian Data + Upload Foto/Dokumen diset 'N/A'
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Siapkan variabel ID tipe kendaraan
SET @id_lv = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE kode_tipe = 'LV' OR nama_tipe LIKE '%Light Vehicle%' LIMIT 1);
SET @id_he = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE kode_tipe = 'HE' OR nama_tipe LIKE '%Heavy Equipment%' LIMIT 1);
SET @id_dt = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE kode_tipe = 'DT' OR nama_tipe LIKE '%Dump Truck%' LIMIT 1);
SET @id_bs = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE kode_tipe = 'BS' OR nama_tipe LIKE '%Bus%' LIMIT 1);
SET @id_cr = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE kode_tipe = 'CR' OR nama_tipe LIKE '%Crane%' LIMIT 1);
SET @id_wt = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE nama_tipe LIKE '%Water Truck%' LIMIT 1);
SET @id_sp = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE nama_tipe LIKE '%Support%' LIMIT 1);

SET @id_lv = IFNULL(@id_lv, 1);
SET @id_he = IFNULL(@id_he, 2);
SET @id_dt = IFNULL(@id_dt, 3);
SET @id_bs = IFNULL(@id_bs, 4);
SET @id_cr = IFNULL(@id_cr, 5);
SET @id_wt = IFNULL(@id_wt, 6);
SET @id_sp = IFNULL(@id_sp, 7);

SET @user_admin   = 1;
SET @user_dept    = 2;
SET @user_inspek  = 3;
SET @user_ohs     = 6;
SET @user_manager = 7;

-- ────────────────────────────────────────────────────────────
-- STEP 1: DATA KENDARAAN (20 UNIT FULL DUMMY)
-- ────────────────────────────────────────────────────────────

INSERT INTO `kendaraan` 
(`id_kendaraan`, `no_polisi`, `nomor_unit`, `model_unit`, `id_tipe_kendaraan`, `merk`, `tipe`, `tahun`, `perusahaan`, `is_unit_baru`, `created_at`) 
VALUES
(101, 'KT 1101 PA', 'LV-101', 'Hilux Double Cabin 4x4', @id_lv, 'Toyota', 'Double Cabin 2.4 G', 2023, 'PT Pamapersada Nusantara', 1, '2026-01-10 08:00:00'),
(102, 'KT 1102 PT', 'LV-102', 'Triton Ultimate 4x4', @id_lv, 'Mitsubishi', 'DC 2.4 Ultimate', 2022, 'PT Petrosea Tbk', 1, '2026-01-12 09:15:00'),
(103, 'KT 1103 VI', 'LV-103', 'D-Max Rodeo 3.0', @id_lv, 'Isuzu', 'Single Cab 3.0', 2021, 'PT Vale Indonesia', 0, '2026-01-15 10:30:00'),
(104, 'KT 1104 UT', 'UG-104', 'HZJ79 Underground Spec', @id_lv, 'Toyota', 'Troop Carrier UG', 2020, 'PT United Tractors', 0, '2026-01-18 11:45:00'),
(105, 'KT 1105 BM', 'LV-105', 'Ranger XLT 2.0 BiTurbo', @id_lv, 'Ford', 'Double Cab 2.0', 2023, 'PT Bukit Makmur Mandiri Utama', 1, '2026-02-01 13:00:00'),
(106, 'KT 2201 TP', 'DT-201', 'FM 260 TI 6x4', @id_dt, 'Hino', 'Dump Truck 20 ton', 2022, 'PT Transcoal Pacific', 1, '2026-02-05 14:15:00'),
(107, 'KT 2202 PA', 'DT-202', 'Scania P360 Heavy Duty', @id_dt, 'Scania', 'Dump Truck 30 ton', 2021, 'PT Pamapersada Nusantara', 0, '2026-02-10 15:30:00'),
(108, 'KT 2203 BM', 'UG-203', 'Axor 2528 Underground', @id_dt, 'Mercedes-Benz', 'Rigged Dump Truck', 2023, 'PT Bukit Makmur Mandiri Utama', 1, '2026-02-12 08:30:00'),
(109, 'N/A', 'HE-301', 'PC200-8 Excavator', @id_he, 'Komatsu', 'Hydraulic Excavator', 2020, 'PT United Tractors', 0, '2026-02-15 09:45:00'),
(110, 'N/A', 'UG-302', 'R1300G Underground LHD', @id_he, 'Caterpillar', 'Scooptram LHD Loader', 2022, 'PT Hexindo Adiperkasa', 1, '2026-02-18 10:00:00'),
(111, 'N/A', 'HE-303', 'D85ESS-2 Bulldozer', @id_he, 'Komatsu', 'Crawler Dozer 200HP', 2019, 'PT Pamapersada Nusantara', 0, '2026-02-20 11:15:00'),
(112, 'N/A', 'HE-304', '777D Off-Highway Truck', @id_he, 'Caterpillar', 'Haul Truck 100 Ton', 2021, 'PT Vale Indonesia', 0, '2026-02-22 13:30:00'),
(113, 'KT 4401 SB', 'UG-401', 'UTRANS Utility Transporter', @id_cr, 'Normet', 'Underground Utility', 2020, 'PT Sanggar Sarana Baja', 0, '2026-03-01 14:00:00'),
(114, 'KT 5501 TF', 'BS-501', 'NQR 71 Bus 30-Seat', @id_bs, 'Isuzu', 'Medium Bus', 2022, 'PT Transporter Fleet', 1, '2026-03-05 15:15:00'),
(115, 'KT 5502 TF', 'BS-502', 'Colt Diesel Coaster', @id_bs, 'Mitsubishi', 'Micro Bus 16-Seat', 2021, 'PT Transporter Fleet', 0, '2026-03-08 09:00:00'),
(116, 'KT 6601 PA', 'WT-601', 'Ranger Water Truck 20KL', @id_wt, 'Hino', 'Water Truck 20.000L', 2022, 'PT Pamapersada Nusantara', 1, '2026-03-10 10:20:00'),
(117, 'N/A', 'SP-701', 'DCA-45SPI Lighting Genset', @id_sp, 'Denyo', 'Mobile Lighting Genset', 2023, 'PT Power Systems', 1, '2026-03-12 11:40:00'),
(118, 'KT 1106 VI', 'LV-106', 'Fortuner 2.8 VRZ 4x4', @id_lv, 'Toyota', 'SUV 4x4 2.8L', 2024, 'PT Vale Indonesia', 1, '2026-03-15 13:00:00'),
(119, 'N/A', 'UG-305', 'LH410 Underground Loader', @id_he, 'Sandvik', 'Subsurface Loader', 2021, 'PT Petrosea Tbk', 0, '2026-03-18 14:10:00'),
(120, 'KT 2204 BM', 'UG-204', 'FMX 440 Tunnel Tipper', @id_dt, 'Volvo', 'Underground Tipper', 2023, 'PT Bukit Makmur Mandiri Utama', 1, '2026-03-20 15:30:00')
ON DUPLICATE KEY UPDATE `no_polisi` = VALUES(`no_polisi`), `nomor_unit` = VALUES(`nomor_unit`), `model_unit` = VALUES(`model_unit`), `merk` = VALUES(`merk`), `perusahaan` = VALUES(`perusahaan`);

-- ────────────────────────────────────────────────────────────
-- STEP 2: PENGAJUAN UJI (FULL ISIAN 20 PENGAJUAN)
-- ────────────────────────────────────────────────────────────

INSERT INTO `pengajuan_uji` 
(`id_pengajuan`, `id_kendaraan`, `id_pemohon`, `email_pemohon`, `tipe_pengajuan`, `tipe_akses`, `tujuan`, `nomor_mesin`, `is_na_nomor_mesin`, `nomor_rangka`, `is_na_nomor_polisi`, `pernah_maintenance_luar`, `status`, `tanggal_pengajuan`, `tgl_acc_ktt`) 
VALUES
(101, 101, 1, 'admin@tactic.co.id', 'baru', 'mining', 'Patroli K3 & Inspeksi Pit Utama Tambang', 'ENG-2GD-90811', 0, 'CHS-8801-LV', 0, 0, 'stiker_keluar', '2026-01-10 08:30:00', '2026-01-12 10:00:00'),
(102, 102, 1, 'admin@tactic.co.id', 'baru', 'non_mining', 'Operasional Office Main Base & Port Yard', 'ENG-4D56-11204', 0, 'CHS-4412-LV', 0, 0, 'stiker_keluar', '2026-01-12 09:30:00', '2026-01-14 11:15:00'),
(103, 103, 1, 'admin@tactic.co.id', 'perpanjangan', 'underground', 'Inspeksi Geoteknik Portal Tunnel Shaft 1', 'ENG-4JJ1-33419', 0, 'CHS-9921-LV', 0, 1, 'stiker_keluar', '2026-01-15 11:00:00', '2026-01-17 14:20:00'),
(104, 104, 1, 'admin@tactic.co.id', 'perpanjangan', 'mining', 'Transport Crew Support Area Pit West', 'ENG-1HZ-55102', 0, 'CHS-1049-UG', 0, 0, 'stiker_keluar', '2025-07-20 13:00:00', '2025-07-22 15:00:00'),
(105, 105, 1, 'admin@tactic.co.id', 'baru', 'non_mining', 'Pengawasan Konstruksi Mess & Townsite', 'ENG-YN2S-77891', 0, 'CHS-3301-LV', 0, 0, 'acc_ktt', '2026-02-01 13:30:00', '2026-02-04 09:00:00'),
(106, 106, 1, 'admin@tactic.co.id', 'baru', 'underground', 'Hauling Ore Decline Tunnel Shaft Level 2', 'ENG-J08E-44102', 0, 'CHS-6612-DT', 0, 0, 'stiker_keluar', '2026-02-05 14:30:00', '2026-02-08 10:30:00'),
(107, 107, 1, 'admin@tactic.co.id', 'recommissioning', 'mining', 'Hauling Overburden Pit Central Waste Dump', 'ENG-DC13-11029', 0, 'CHS-7719-DT', 0, 1, 'pengajuan_baru', '2026-08-18 09:00:00', NULL),
(108, 108, 1, 'admin@tactic.co.id', 'baru', 'non_mining', 'Transport Agregat Material Workshop Main', 'ENG-OM906-8812', 0, 'CHS-8819-UG', 0, 0, 'diterima_manager', '2026-08-19 10:15:00', NULL),
(109, 109, 1, 'admin@tactic.co.id', 'recommissioning', 'underground', 'Mucking Ore Underground Stope Shaft 3', 'ENG-SAA6D-1192', 0, 'CHS-2201-HE', 1, 0, 'dijadwalkan', '2026-08-15 11:30:00', NULL),
(110, 110, 1, 'admin@tactic.co.id', 'baru', 'mining', 'Loading Batubara Stockpile 2 Pit South', 'ENG-C11-44912', 0, 'CHS-5501-UG', 1, 0, 'diterima_admin_ohs', '2026-08-12 14:00:00', NULL),
(111, 111, 1, 'admin@tactic.co.id', 'recommissioning', 'non_mining', 'Land Clearing & Leveling Area Port Stockpile', 'ENG-S6D125-331', 0, 'CHS-9901-HE', 1, 0, 'tidak_lulus_inspeksi', '2026-08-10 08:30:00', NULL),
(112, 112, 1, 'admin@tactic.co.id', 'perpanjangan', 'underground', 'Transport Overburden Underground Decline', 'ENG-3412-99011', 0, 'CHS-1129-HE', 1, 1, 'inspeksi_ulang', '2026-08-08 10:00:00', NULL),
(113, 113, 1, 'admin@tactic.co.id', 'recommissioning', 'mining', 'Lifting Komponen Heavy Machinery Pit Workshop', 'ENG-NOR-88102', 0, 'CHS-4401-UG', 0, 0, 'stiker_keluar', '2026-02-15 11:00:00', '2026-02-18 16:00:00'),
(114, 114, 1, 'admin@tactic.co.id', 'baru', 'non_mining', 'Shuttle Bus Karyawan Port to Townsite', 'ENG-4HK1-55019', 0, 'CHS-5501-BS', 0, 0, 'stiker_keluar', '2026-03-05 15:30:00', '2026-03-08 11:00:00'),
(115, 115, 1, 'admin@tactic.co.id', 'baru', 'underground', 'Transport Special Crew Portal Tunnel Station', 'ENG-4D34-99120', 0, 'CHS-5502-BS', 0, 0, 'ditolak_manager', '2026-03-08 09:30:00', NULL),
(116, 116, 1, 'admin@tactic.co.id', 'baru', 'mining', 'Penyiraman Hauling Road Anti-Debu Pit Main', 'ENG-J08E-77890', 0, 'CHS-6601-WT', 0, 0, 'diterima_ohs_supt', '2026-08-16 10:40:00', NULL),
(117, 117, 1, 'admin@tactic.co.id', 'baru', 'non_mining', 'Emergency Power Supply Office Main & Camp', 'ENG-DEN-33901', 0, 'CHS-7701-SP', 1, 0, 'stiker_keluar', '2026-03-12 12:00:00', '2026-03-15 14:30:00'),
(118, 118, 1, 'admin@tactic.co.id', 'baru', 'underground', 'Inspeksi Manajemen Tambang Bawah Tanah', 'ENG-1GD-44901', 0, 'CHS-1106-LV', 0, 0, 'draft', '2026-08-20 07:45:00', NULL),
(119, 119, 1, 'admin@tactic.co.id', 'perpanjangan', 'mining', 'Blending Batubara Staging Area Pit ROM', 'ENG-SAN-88129', 0, 'CHS-3305-UG', 1, 0, 'dijadwalkan', '2026-08-19 15:00:00', NULL),
(120, 120, 1, 'admin@tactic.co.id', 'baru', 'non_mining', 'Transport Material Logistik Port Yard', 'ENG-D13-99012', 0, 'CHS-2204-UG', 0, 0, 'stiker_keluar', '2025-05-10 09:00:00', '2025-05-12 11:00:00')
ON DUPLICATE KEY UPDATE `tipe_akses` = VALUES(`tipe_akses`), `tujuan` = VALUES(`tujuan`), `status` = VALUES(`status`), `tgl_acc_ktt` = VALUES(`tgl_acc_ktt`);

-- ────────────────────────────────────────────────────────────
-- STEP 3: PENGAJUAN LAMPIRAN (120 RECORD DENGAN FILE_PATH = 'N/A')
-- ────────────────────────────────────────────────────────────

DELETE FROM `pengajuan_lampiran` WHERE `id_pengajuan` BETWEEN 101 AND 120;

INSERT INTO `pengajuan_lampiran` (`id_lampiran`, `id_pengajuan`, `jenis_lampiran`, `file_path`, `uploaded_at`) VALUES
(1001, 101, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1002, 101, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1003, 101, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1004, 101, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1005, 101, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1006, 101, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1007, 102, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1008, 102, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1009, 102, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1010, 102, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1011, 102, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1012, 102, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1013, 103, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1014, 103, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1015, 103, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1016, 103, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1017, 103, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1018, 103, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1019, 104, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1020, 104, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1021, 104, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1022, 104, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1023, 104, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1024, 104, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1025, 105, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1026, 105, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1027, 105, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1028, 105, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1029, 105, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1030, 105, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1031, 106, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1032, 106, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1033, 106, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1034, 106, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1035, 106, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1036, 106, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1037, 107, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1038, 107, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1039, 107, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1040, 107, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1041, 107, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1042, 107, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1043, 108, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1044, 108, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1045, 108, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1046, 108, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1047, 108, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1048, 108, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1049, 109, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1050, 109, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1051, 109, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1052, 109, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1053, 109, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1054, 109, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1055, 110, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1056, 110, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1057, 110, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1058, 110, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1059, 110, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1060, 110, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1061, 111, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1062, 111, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1063, 111, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1064, 111, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1065, 111, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1066, 111, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1067, 112, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1068, 112, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1069, 112, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1070, 112, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1071, 112, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1072, 112, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1073, 113, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1074, 113, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1075, 113, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1076, 113, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1077, 113, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1078, 113, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1079, 114, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1080, 114, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1081, 114, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1082, 114, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1083, 114, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1084, 114, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1085, 115, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1086, 115, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1087, 115, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1088, 115, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1089, 115, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1090, 115, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1091, 116, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1092, 116, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1093, 116, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1094, 116, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1095, 116, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1096, 116, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1097, 117, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1098, 117, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1099, 117, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1100, 117, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1101, 117, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1102, 117, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1103, 118, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1104, 118, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1105, 118, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1106, 118, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1107, 118, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1108, 118, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1109, 119, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1110, 119, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1111, 119, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1112, 119, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1113, 119, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1114, 119, 'maintenance_record', 'N/A', '2026-01-10 08:35:00'),
(1115, 120, 'stnk', 'N/A', '2026-01-10 08:35:00'),
(1116, 120, 'unit_depan', 'N/A', '2026-01-10 08:35:00'),
(1117, 120, 'unit_belakang', 'N/A', '2026-01-10 08:35:00'),
(1118, 120, 'unit_kiri', 'N/A', '2026-01-10 08:35:00'),
(1119, 120, 'unit_kanan', 'N/A', '2026-01-10 08:35:00'),
(1120, 120, 'maintenance_record', 'N/A', '2026-01-10 08:35:00')
ON DUPLICATE KEY UPDATE `file_path` = VALUES(`file_path`);

-- ────────────────────────────────────────────────────────────
-- STEP 4: PENGAJUAN APPROVAL (RIWAYAT TRAIL APPROVAL)
-- ────────────────────────────────────────────────────────────

INSERT INTO `pengajuan_approval` (`id_approval`, `id_approver`, `id_pengajuan`, `level_approval`, `status`, `catatan`, `created_at`) VALUES
(101, @user_manager, 101, 'dept_manager', 'setuju', 'Dokumen pengajuan lengkap & unit siap diuji.', '2026-01-10 09:00:00'),
(102, @user_ohs,     101, 'admin_ohs',    'setuju', 'Penjadwalan inspeksi disetujui.',             '2026-01-10 10:00:00'),
(103, @user_ohs,     101, 'ohs_supt',     'setuju', 'Hasil kelayakan memenuhi standar OHS.',       '2026-01-11 14:00:00'),
(104, @user_admin,   101, 'ktt',          'setuju', 'ACC KTT & stiker diterbitkan.',              '2026-01-12 10:00:00'),
(105, @user_manager, 102, 'dept_manager', 'setuju', 'Disetujui untuk area port & office.',        '2026-01-12 10:00:00'),
(106, @user_ohs,     102, 'admin_ohs',    'setuju', 'Verifikasi dokumen administrasi ok.',        '2026-01-12 11:00:00'),
(107, @user_manager, 105, 'dept_manager', 'setuju', 'Persetujuan Manager Dept.',                  '2026-02-01 14:00:00'),
(108, @user_ohs,     105, 'admin_ohs',    'setuju', 'Persetujuan Admin OHS.',                     '2026-02-01 15:00:00'),
(109, @user_ohs,     105, 'ohs_supt',     'setuju', 'Persetujuan OHS Superintendent.',            '2026-02-03 10:00:00'),
(110, @user_admin,   105, 'ktt',          'setuju', 'Disetujui KTT.',                             '2026-02-04 09:00:00'),
(111, @user_manager, 106, 'dept_manager', 'setuju', 'Disetujui untuk Underground Shaft.',         '2026-02-05 15:00:00'),
(112, @user_manager, 108, 'dept_manager', 'setuju', 'Disetujui Manager.',                          '2026-08-19 11:00:00'),
(113, @user_manager, 110, 'dept_manager', 'setuju', 'Disetujui Manager.',                          '2026-08-12 15:00:00'),
(114, @user_ohs,     110, 'admin_ohs',    'setuju', 'Disetujui Admin OHS.',                       '2026-08-13 09:00:00'),
(115, @user_manager, 115, 'dept_manager', 'tolak',  'Spesifikasi bus belum sesuai standar UG.',   '2026-03-08 10:00:00')
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `catatan` = VALUES(`catatan`);

-- ────────────────────────────────────────────────────────────
-- STEP 5: JADWAL INSPEKSI UJI KELAYAKAN
-- ────────────────────────────────────────────────────────────

INSERT INTO `jadwal_uji` (`id_jadwal`, `id_pengajuan`, `id_mekanik`, `id_mekanik_master`, `id_inspektor`, `tanggal_uji`, `lokasi`, `keterangan`, `status`, `created_at`, `dibuat_oleh`) VALUES
(101, 101, 1, 1, 3, '2026-01-11 09:00:00', 'Pit Main Workshop',          'Inspeksi lapangan', 'done',      '2026-01-10 10:00:00', 1),
(102, 102, 1, 1, 3, '2026-01-13 10:00:00', 'Petrosea Non-Mining Yard',   'Inspeksi lapangan', 'done',      '2026-01-12 11:00:00', 1),
(103, 103, 1, 1, 3, '2026-01-16 11:00:00', 'UG Portal Workshop Shaft 1', 'Inspeksi UG',       'done',      '2026-01-15 13:00:00', 1),
(104, 104, 1, 1, 3, '2025-07-21 09:30:00', 'United Tractors Pit Workshop','Inspeksi pit',      'done',      '2025-07-20 14:00:00', 1),
(105, 105, 1, 1, 3, '2026-02-02 14:00:00', 'BUMA Workshop Non-Pit KM 9', 'Inspeksi non mining','done',      '2026-02-01 15:00:00', 1),
(106, 106, 1, 1, 3, '2026-02-06 09:00:00', 'UG Decline Staging Station', 'Inspeksi UG',       'done',      '2026-02-05 16:00:00', 1),
(109, 109, 1, 1, 3, '2026-08-21 09:00:00', 'UG Mine Workshop Level 3',   'Jadwal mendatang',  'scheduled', '2026-08-16 10:00:00', 1),
(110, 110, 1, 1, 3, '2026-08-14 10:30:00', 'Pit South Service Point',    'Inspeksi pit',      'done',      '2026-08-13 09:00:00', 1),
(111, 111, 1, 1, 3, '2026-08-11 13:00:00', 'Port Stockpile Yard Bay',    'Inspeksi port',     'done',      '2026-08-10 14:00:00', 1),
(112, 112, 1, 1, 3, '2026-08-09 10:00:00', 'UG Decline Heavy Workshop',  'Inspeksi UG',       'done',      '2026-08-08 11:00:00', 1),
(113, 113, 1, 1, 3, '2026-02-16 09:00:00', 'Pit Crane Maintenance Bay',  'Inspeksi crane',    'done',      '2026-02-15 14:00:00', 1),
(114, 114, 1, 1, 3, '2026-03-06 10:00:00', 'Fleet Port Main Terminal',   'Inspeksi bus',      'done',      '2026-03-05 16:30:00', 1),
(116, 116, 1, 1, 3, '2026-08-17 11:00:00', 'Pama Water Truck Pit Base',  'Inspeksi WT',       'done',      '2026-08-16 12:00:00', 1),
(117, 117, 1, 1, 3, '2026-03-13 14:00:00', 'Power Systems Office Base',  'Inspeksi genset',   'done',      '2026-03-12 13:00:00', 1),
(119, 119, 1, 1, 3, '2026-08-22 10:00:00', 'Pit ROM Staging Yard',       'Jadwal mendatang',  'scheduled', '2026-08-20 08:00:00', 1),
(120, 120, 1, 1, 3, '2025-05-11 10:00:00', 'Port Logistik Bay KM 12',    'Inspeksi logistik', 'done',      '2025-05-10 10:00:00', 1)
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `tanggal_uji` = VALUES(`tanggal_uji`), `lokasi` = VALUES(`lokasi`);

-- ────────────────────────────────────────────────────────────
-- STEP 6: HASIL UJI KELAYAKAN (INSPEKSI)
-- ────────────────────────────────────────────────────────────

INSERT INTO `uji_kelayakan` (`id_uji`, `id_pengajuan`, `id_mekanik_master`, `tanggal_uji`, `hasil`, `nama_inspektor`, `nama_mekanik`, `perusahaan_mekanik`, `catatan_temuan`, `created_at`) VALUES
(101, 101, 1, '2026-01-11 09:30:00', 'lulus',       'Inspektor OHS', 'Budi Mechanic', 'PT Pamapersada', 'Unit dalam kondisi sangat baik & lengkap standar safety tambang.', '2026-01-11 10:00:00'),
(102, 102, 1, '2026-01-13 10:30:00', 'lulus',       'Inspektor OHS', 'Eko Technician', 'PT Petrosea',   'Fitur keselamatan aktif, APAR & P3K terverifikasi di area non-mining.', '2026-01-13 11:00:00'),
(103, 103, 1, '2026-01-16 11:30:00', 'lulus',       'Inspektor OHS', 'Deni Specialist','PT Vale',      'Scrubber gas buang & lampu UG berfungsi normal.', '2026-01-16 12:00:00'),
(104, 104, 1, '2025-07-21 10:00:00', 'lulus',       'Inspektor OHS', 'Agus Mechanic',   'PT UT',        'Buggymip & rollbar standar pit tambang terpasang.', '2025-07-21 10:30:00'),
(105, 105, 1, '2026-02-02 14:30:00', 'lulus',       'Inspektor OHS', 'Fajar Tech',    'PT BUMA',      'Lulus inspeksi standar Non Mining LV.', '2026-02-02 15:00:00'),
(106, 106, 1, '2026-02-06 09:45:00', 'lulus',       'Inspektor OHS', 'Hadi Mechanic',   'PT Transcoal', 'Fire suppression system UG teruji sempurna.', '2026-02-06 10:15:00'),
(110, 110, 1, '2026-08-14 11:00:00', 'lulus',       'Inspektor OHS', 'Joko Expert',   'PT Hexindo',   'Hidrolik & struktur boom dalam batas aman toleransi pit.', '2026-08-14 11:30:00'),
(111, 111, 1, '2026-08-11 13:45:00', 'tidak_lulus', 'Inspektor OHS', 'Budi Mechanic', 'PT Pamapersada', 'Temuan critical: Kampas rem aus & oli hydraulic leak.', '2026-08-11 14:00:00'),
(112, 112, 1, '2026-08-09 10:45:00', 'tidak_lulus', 'Inspektor OHS', 'Deni Specialist','PT Vale',      'Steering cylinder UG bocor halus & tire pressure error.', '2026-08-09 11:15:00'),
(113, 113, 1, '2026-02-16 09:30:00', 'lulus',       'Inspektor OHS', 'Kusno Crane Spec','PT SSB',      'Overhead crane lock & outrigger terverifikasi.', '2026-02-16 10:00:00'),
(114, 114, 1, '2026-03-06 10:30:00', 'lulus',       'Inspektor OHS', 'Lukman Mechanic','PT Fleet',     'Emergency exit window & seatbelt 3-point aman.', '2026-03-06 11:00:00'),
(116, 116, 1, '2026-08-17 11:30:00', 'lulus',       'Inspektor OHS', 'Budi Mechanic', 'PT Pamapersada', 'Pompa penyiram water cannon & nozzle berfungsi presisi.', '2026-08-17 12:00:00'),
(117, 117, 1, '2026-03-13 14:30:00', 'lulus',       'Inspektor OHS', 'Miftah Electric','PT Power',     'Master switch & grounding strap dalam kondisi baik.', '2026-03-13 15:00:00'),
(120, 120, 1, '2025-05-11 10:30:00', 'lulus',       'Inspektor OHS', 'Fajar Tech',    'PT BUMA',      'Semua indikator mesin normal.', '2025-05-11 11:00:00')
ON DUPLICATE KEY UPDATE `hasil` = VALUES(`hasil`), `catatan_temuan` = VALUES(`catatan_temuan`);

-- ────────────────────────────────────────────────────────────
-- STEP 7: PERBAIKAN UNIT
-- ────────────────────────────────────────────────────────────

INSERT INTO `perbaikan_unit` (`id_perbaikan`, `id_pengajuan`, `id_uji`, `tgl_max_perbaikan`, `tgl_selesai`, `id_verifikator`, `catatan_perbaikan`, `status`, `created_at`) VALUES
(101, 111, 111, '2026-08-25', NULL,         NULL,        'Menunggu perbaikan kampas rem & penggantian seal hydraulic dari workshop Port.', 'menunggu', '2026-08-11 14:30:00'),
(102, 112, 112, '2026-08-20', '2026-08-18', @user_admin, 'Penggantian seal steering cylinder UG & kalibrasi sensor tekanan ban telah dilakukan.', 'diverifikasi', '2026-08-09 11:30:00')
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `catatan_perbaikan` = VALUES(`catatan_perbaikan`);

-- ────────────────────────────────────────────────────────────
-- STEP 8: STIKER RELEASE (RELEASE DOKUMEN / STIKER)
-- ────────────────────────────────────────────────────────────

INSERT INTO `sticker_release` (`id_sticker`, `id_pengajuan`, `nomor_sticker`, `tanggal_release`, `tgl_expired`, `released_by`) VALUES
(101, 101, 'STK-2026-LV101', '2026-01-12 10:15:00', '2027-01-12 00:00:00', 1),
(102, 102, 'STK-2026-LV102', '2026-01-14 11:30:00', '2026-10-14 00:00:00', 1),
(103, 103, 'STK-2026-UG103', '2026-01-17 14:30:00', '2026-09-04 00:00:00', 1),
(104, 104, 'STK-2025-LV104', '2025-07-22 15:15:00', '2026-08-10 00:00:00', 1),
(106, 106, 'STK-2026-UG201', '2026-02-08 10:45:00', '2027-02-08 00:00:00', 1),
(113, 113, 'STK-2026-CR401', '2026-02-18 16:15:00', '2027-02-18 00:00:00', 1),
(114, 114, 'STK-2026-BS501', '2026-03-08 11:15:00', '2027-03-08 00:00:00', 1),
(117, 117, 'STK-2026-SP701', '2026-03-15 14:45:00', '2027-03-15 00:00:00', 1),
(120, 120, 'STK-2025-DT204', '2025-05-12 11:15:00', '2026-05-12 00:00:00', 1)
ON DUPLICATE KEY UPDATE `nomor_sticker` = VALUES(`nomor_sticker`), `tgl_expired` = VALUES(`tgl_expired`);

SET FOREIGN_KEY_CHECKS = 1;
