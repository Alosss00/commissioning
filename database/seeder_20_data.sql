-- ============================================================
-- SQL SEEDER: 20 Data Tester Uji Kelayakan Unit Kendaraan
-- Variasi Tipe Akses Beda-Beda Setiap Pengajuan (Mining / Non Mining / Underground)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Siapkan variabel ID tipe kendaraan berdasarkan nama/kode
SET @id_lv   = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE kode_tipe = 'LV' OR nama_tipe LIKE '%Light Vehicle%' LIMIT 1);
SET @id_he   = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE kode_tipe = 'HE' OR nama_tipe LIKE '%Heavy Equipment%' LIMIT 1);
SET @id_dt   = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE kode_tipe = 'DT' OR nama_tipe LIKE '%Dump Truck%' LIMIT 1);
SET @id_bs   = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE kode_tipe = 'BS' OR nama_tipe LIKE '%Bus%' LIMIT 1);
SET @id_cr   = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE kode_tipe = 'CR' OR nama_tipe LIKE '%Crane%' LIMIT 1);
SET @id_wt   = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE nama_tipe LIKE '%Water Truck%' LIMIT 1);
SET @id_sp   = (SELECT id_tipe_kendaraan FROM tipe_kendaraan WHERE nama_tipe LIKE '%Support%' LIMIT 1);

-- Fallback jika id_tipe_kendaraan belum ada
SET @id_lv   = IFNULL(@id_lv, 1);
SET @id_he   = IFNULL(@id_he, 2);
SET @id_dt   = IFNULL(@id_dt, 3);
SET @id_bs   = IFNULL(@id_bs, 4);
SET @id_cr   = IFNULL(@id_cr, 5);
SET @id_wt   = IFNULL(@id_wt, 6);
SET @id_sp   = IFNULL(@id_sp, 7);

-- ID Pemohon / Inspector default dari user super admin atau user id = 1
SET @user_admin = 1;

-- ────────────────────────────────────────────────────────────
-- STEP 1: INSERT DATA KENDARAAN (20 UNIT DUMMY)
-- ────────────────────────────────────────────────────────────

INSERT INTO `kendaraan` 
(`id_kendaraan`, `no_polisi`, `nomor_unit`, `model_unit`, `id_tipe_kendaraan`, `merk`, `tipe`, `tahun`, `perusahaan`, `is_unit_baru`, `created_at`) 
VALUES
(101, 'KT 1101 PA', 'LV-101', 'Hilux Double Cabin 4x4', @id_lv, 'Toyota', 'Double Cabin 2.4 G', 2023, 'PT Pamapersada Nusantara', 1, '2026-01-10 08:00:00'),
(102, 'KT 1102 PT', 'LV-102', 'Triton Ultimate 4x4',    @id_lv, 'Mitsubishi', 'DC 2.4 Ultimate',   2022, 'PT Petrosea Tbk',         1, '2026-01-12 09:15:00'),
(103, 'KT 1103 VI', 'LV-103', 'D-Max Rodeo 3.0',       @id_lv, 'Isuzu',      'Single Cab 3.0',     2021, 'PT Vale Indonesia',       0, '2026-01-15 10:30:00'),
(104, 'KT 1104 UT', 'UG-104', 'HZJ79 Underground Spec', @id_lv, 'Toyota',     'Troop Carrier UG',   2020, 'PT United Tractors',      0, '2026-01-18 11:45:00'),
(105, 'KT 1105 BM', 'LV-105', 'Ranger XLT 2.0 BiTurbo', @id_lv, 'Ford',       'Double Cab 2.0',     2023, 'PT Bukit Makmur Mandiri Utama', 1, '2026-02-01 13:00:00'),
(106, 'KT 2201 TP', 'DT-201', 'FM 260 TI 6x4',         @id_dt, 'Hino',       'Dump Truck 20 ton',  2022, 'PT Transcoal Pacific',    1, '2026-02-05 14:15:00'),
(107, 'KT 2202 PA', 'DT-202', 'Scania P360 Heavy Duty', @id_dt, 'Scania',     'Dump Truck 30 ton',  2021, 'PT Pamapersada Nusantara', 0, '2026-02-10 15:30:00'),
(108, 'KT 2203 BM', 'UG-203', 'Axor 2528 Underground', @id_dt, 'Mercedes-Benz', 'Rigged Dump Truck',  2023, 'PT Bukit Makmur Mandiri Utama', 1, '2026-02-12 08:30:00'),
(109, 'N/A',        'HE-301', 'PC200-8 Excavator',     @id_he, 'Komatsu',    'Hydraulic Excavator', 2020, 'PT United Tractors',      0, '2026-02-15 09:45:00'),
(110, 'N/A',        'UG-302', 'R1300G Underground LHD',@id_he, 'Caterpillar', 'Scooptram LHD Loader', 2022, 'PT Hexindo Adiperkasa',   1, '2026-02-18 10:00:00'),
(111, 'N/A',        'HE-303', 'D85ESS-2 Bulldozer',    @id_he, 'Komatsu',    'Crawler Dozer 200HP', 2019, 'PT Pamapersada Nusantara', 0, '2026-02-20 11:15:00'),
(112, 'N/A',        'HE-304', '777D Off-Highway Truck',@id_he, 'Caterpillar', 'Haul Truck 100 Ton', 2021, 'PT Vale Indonesia',       0, '2026-02-22 13:30:00'),
(113, 'KT 4401 SB', 'UG-401', 'UTRANS Utility Transporter',@id_cr,'Normet',   'Underground Utility', 2020, 'PT Sanggar Sarana Baja',  0, '2026-03-01 14:00:00'),
(114, 'KT 5501 TF', 'BS-501', 'NQR 71 Bus 30-Seat',    @id_bs, 'Isuzu',      'Medium Bus',          2022, 'PT Transporter Fleet',    1, '2026-03-05 15:15:00'),
(115, 'KT 5502 TF', 'BS-502', 'Colt Diesel Coaster',    @id_bs, 'Mitsubishi', 'Micro Bus 16-Seat',  2021, 'PT Transporter Fleet',    0, '2026-03-08 09:00:00'),
(116, 'KT 6601 PA', 'WT-601', 'Ranger Water Truck 20KL',@id_wt, 'Hino',       'Water Truck 20.000L', 2022, 'PT Pamapersada Nusantara', 1, '2026-03-10 10:20:00'),
(117, 'N/A',        'SP-701', 'DCA-45SPI Lighting Genset',@id_sp,'Denyo',     'Mobile Lighting Genset',2023,'PT Power Systems',        1, '2026-03-12 11:40:00'),
(118, 'KT 1106 VI', 'LV-106', 'Fortuner 2.8 VRZ 4x4',  @id_lv, 'Toyota',     'SUV 4x4 2.8L',        2024, 'PT Vale Indonesia',       1, '2026-03-15 13:00:00'),
(119, 'N/A',        'UG-305', 'LH410 Underground Loader',@id_he,'Sandvik',    'Subsurface Loader',   2021, 'PT Petrosea Tbk',         0, '2026-03-18 14:10:00'),
(120, 'KT 2204 BM', 'UG-204', 'FMX 440 Tunnel Tipper', @id_dt, 'Volvo',      'Underground Tipper',  2023, 'PT Bukit Makmur Mandiri Utama', 1, '2026-03-20 15:30:00')
ON DUPLICATE KEY UPDATE 
`no_polisi` = VALUES(`no_polisi`), `nomor_unit` = VALUES(`nomor_unit`), `model_unit` = VALUES(`model_unit`), `merk` = VALUES(`merk`), `perusahaan` = VALUES(`perusahaan`);


-- ────────────────────────────────────────────────────────────
-- STEP 2: INSERT DATA PENGAJUAN UJI (TIPE AKSES BEDA-BEDA BERSELING)
-- ────────────────────────────────────────────────────────────

INSERT INTO `pengajuan_uji`
(`id_pengajuan`, `id_kendaraan`, `id_pemohon`, `tipe_pengajuan`, `tipe_akses`, `tujuan`, `status`, `tanggal_pengajuan`, `tgl_acc_ktt`)
VALUES
(101, 101, @user_admin, 'baru',           'mining',      'Patroli K3 & Inspeksi Pit Utama Tambang',   'stiker_keluar',        '2026-01-10 08:30:00', '2026-01-12 10:00:00'),
(102, 102, @user_admin, 'baru',           'non_mining',  'Operasional Office Main Base & Port Yard',  'stiker_keluar',        '2026-01-12 09:30:00', '2026-01-14 11:15:00'),
(103, 103, @user_admin, 'perpanjangan',   'underground', 'Inspeksi Geoteknik Portal Tunnel Shaft 1',  'stiker_keluar',        '2026-01-15 11:00:00', '2026-01-17 14:20:00'),
(104, 104, @user_admin, 'perpanjangan',   'mining',      'Transport Crew Support Area Pit West',      'stiker_keluar',        '2025-07-20 13:00:00', '2025-07-22 15:00:00'),
(105, 105, @user_admin, 'baru',           'non_mining',  'Pengawasan Konstruksi Mess & Townsite',     'acc_ktt',              '2026-02-01 13:30:00', '2026-02-04 09:00:00'),
(106, 106, @user_admin, 'baru',           'underground', 'Hauling Ore Decline Tunnel Shaft Level 2',  'stiker_keluar',        '2026-02-05 14:30:00', '2026-02-08 10:30:00'),
(107, 107, @user_admin, 'recommissioning','mining',      'Hauling Overburden Pit Central Waste Dump', 'menunggu_dept_manager','2026-08-18 09:00:00', NULL),
(108, 108, @user_admin, 'baru',           'non_mining',  'Transport Agregat Material Workshop Main',  'menunggu_admin_ohs',   '2026-08-19 10:15:00', NULL),
(109, 109, @user_admin, 'recommissioning','underground', 'Mucking Ore Underground Stope Shaft 3',    'terjadwal',            '2026-08-15 11:30:00', NULL),
(110, 110, @user_admin, 'baru',           'mining',      'Loading Batubara Stockpile 2 Pit South',    'menunggu_ohs_supt',    '2026-08-12 14:00:00', NULL),
(111, 111, @user_admin, 'recommissioning','non_mining',  'Land Clearing & Leveling Area Port Stockpile','tidak_lulus_inspeksi','2026-08-10 08:30:00', NULL),
(112, 112, @user_admin, 'perpanjangan',   'underground', 'Transport Overburden Underground Decline',  'perbaikan_unit',       '2026-08-08 10:00:00', NULL),
(113, 113, @user_admin, 'recommissioning','mining',      'Lifting Komponen Heavy Machinery Pit Workshop','stiker_keluar',     '2026-02-15 11:00:00', '2026-02-18 16:00:00'),
(114, 114, @user_admin, 'baru',           'non_mining',  'Shuttle Bus Karyawan Port to Townsite',     'stiker_keluar',        '2026-03-05 15:30:00', '2026-03-08 11:00:00'),
(115, 115, @user_admin, 'baru',           'underground', 'Transport Special Crew Portal Tunnel Station','ditolak',           '2026-03-08 09:30:00', NULL),
(116, 116, @user_admin, 'baru',           'mining',      'Penyiraman Hauling Road Anti-Debu Pit Main','menunggu_ktt',         '2026-08-16 10:40:00', NULL),
(117, 117, @user_admin, 'baru',           'non_mining',  'Emergency Power Supply Office Main & Camp', 'stiker_keluar',        '2026-03-12 12:00:00', '2026-03-15 14:30:00'),
(118, 118, @user_admin, 'baru',           'underground', 'Inspeksi Manajemen Tambang Bawah Tanah',    'draft',                '2026-08-20 07:45:00', NULL),
(119, 119, @user_admin, 'perpanjangan',   'mining',      'Blending Batubara Staging Area Pit ROM',    'terjadwal',            '2026-08-19 15:00:00', NULL),
(120, 120, @user_admin, 'baru',           'non_mining',  'Transport Material Logistik Port Yard',     'stiker_keluar',        '2025-05-10 09:00:00', '2025-05-12 11:00:00')
ON DUPLICATE KEY UPDATE
`tipe_akses` = VALUES(`tipe_akses`), `tujuan` = VALUES(`tujuan`), `status` = VALUES(`status`), `tgl_acc_ktt` = VALUES(`tgl_acc_ktt`);


-- ────────────────────────────────────────────────────────────
-- STEP 3: INSERT JADWAL INSPEKSI UJI KELAYAKAN
-- ────────────────────────────────────────────────────────────

INSERT INTO `jadwal_uji`
(`id_jadwal`, `id_pengajuan`, `tanggal_uji`, `lokasi_uji`, `id_inspektor`, `id_mekanik_master`, `status`, `created_at`)
VALUES
(101, 101, '2026-01-11 09:00:00', 'Pit Main Workshop',          @user_admin, 1, 'selesai',    '2026-01-10 10:00:00'),
(102, 102, '2026-01-13 10:00:00', 'Petrosea Non-Mining Yard',   @user_admin, 1, 'selesai',    '2026-01-12 11:00:00'),
(103, 103, '2026-01-16 11:00:00', 'UG Portal Workshop Shaft 1', @user_admin, 1, 'selesai',    '2026-01-15 13:00:00'),
(104, 104, '2025-07-21 09:30:00', 'United Tractors Pit Workshop',@user_admin,1, 'selesai',    '2025-07-20 14:00:00'),
(105, 105, '2026-02-02 14:00:00', 'BUMA Workshop Non-Pit KM 9',@user_admin, 1, 'selesai',    '2026-02-01 15:00:00'),
(106, 106, '2026-02-06 09:00:00', 'UG Decline Staging Station', @user_admin, 1, 'selesai',    '2026-02-05 16:00:00'),
(109, 109, '2026-08-21 09:00:00', 'UG Mine Workshop Level 3',   @user_admin, 1, 'terjadwal', '2026-08-16 10:00:00'),
(110, 110, '2026-08-14 10:30:00', 'Pit South Service Point',    @user_admin, 1, 'selesai',    '2026-08-13 09:00:00'),
(111, 111, '2026-08-11 13:00:00', 'Port Stockpile Yard Bay',    @user_admin, 1, 'selesai',    '2026-08-10 14:00:00'),
(112, 112, '2026-08-09 10:00:00', 'UG Decline Heavy Workshop',  @user_admin, 1, 'selesai',    '2026-08-08 11:00:00'),
(113, 113, '2026-02-16 09:00:00', 'Pit Crane Maintenance Bay',  @user_admin, 1, 'selesai',    '2026-02-15 14:00:00'),
(114, 114, '2026-03-06 10:00:00', 'Fleet Port Main Terminal',   @user_admin, 1, 'selesai',    '2026-03-05 16:30:00'),
(116, 116, '2026-08-17 11:00:00', 'Pama Water Truck Pit Base',  @user_admin, 1, 'selesai',    '2026-08-16 12:00:00'),
(117, 117, '2026-03-13 14:00:00', 'Power Systems Office Base',  @user_admin, 1, 'selesai',    '2026-03-12 13:00:00'),
(119, 119, '2026-08-22 10:00:00', 'Pit ROM Staging Yard',       @user_admin, 1, 'terjadwal', '2026-08-20 08:00:00'),
(120, 120, '2025-05-11 10:00:00', 'Port Logistik Bay KM 12',    @user_admin, 1, 'selesai',    '2025-05-10 10:00:00')
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `tanggal_uji` = VALUES(`tanggal_uji`), `lokasi_uji` = VALUES(`lokasi_uji`);


-- ────────────────────────────────────────────────────────────
-- STEP 4: INSERT HASIL UJI KELAYAKAN (INSPEKSI HASIL)
-- ────────────────────────────────────────────────────────────

INSERT INTO `uji_kelayakan`
(`id_uji`, `id_pengajuan`, `id_jadwal`, `tanggal_uji`, `hasil`, `nama_inspektor`, `nama_mekanik`, `perusahaan_mekanik`, `catatan_temuan`, `created_at`)
VALUES
(101, 101, 101, '2026-01-11 09:30:00', 'lulus',       'Ahmad Safety Inspector', 'Budi Mechanic', 'PT Pamapersada', 'Unit dalam kondisi sangat baik & lengkap standar safety tambang.', '2026-01-11 10:00:00'),
(102, 102, 102, '2026-01-13 10:30:00', 'lulus',       'Ahmad Safety Inspector', 'Eko Technician', 'PT Petrosea',   'Fitur keselamatan aktif, APAR & P3K terverifikasi di area non-mining.', '2026-01-13 11:00:00'),
(103, 103, 103, '2026-01-16 11:30:00', 'lulus',       'Rizal Inspector OHS',    'Deni Specialist','PT Vale',      'Scrubber gas buang & lampu UG berfungsi normal.', '2026-01-16 12:00:00'),
(104, 104, 104, '2025-07-21 10:00:00', 'lulus',       'Ahmad Safety Inspector', 'Agus Mechanic',   'PT UT',        'Buggymip & rollbar standar pit tambang terpasang.', '2025-07-21 10:30:00'),
(105, 105, 105, '2026-02-02 14:30:00', 'lulus',       'Rizal Inspector OHS',    'Fajar Tech',    'PT BUMA',      'Lulus inspeksi standar Non Mining LV.', '2026-02-02 15:00:00'),
(106, 106, 106, '2026-02-06 09:45:00', 'lulus',       'Ahmad Safety Inspector', 'Hadi Mechanic',   'PT Transcoal', 'Fire suppression system UG teruji sempurna.', '2026-02-06 10:15:00'),
(110, 110, 110, '2026-08-14 11:00:00', 'lulus',       'Rizal Inspector OHS',    'Joko Expert',   'PT Hexindo',   'Hidrolik & struktur boom dalam batas aman toleransi pit.', '2026-08-14 11:30:00'),
(111, 111, 111, '2026-08-11 13:45:00', 'tidak_lulus', 'Ahmad Safety Inspector', 'Budi Mechanic', 'PT Pamapersada', 'Temuan critical: Kampas rem aus & oli hydraulic leak.', '2026-08-11 14:00:00'),
(112, 112, 112, '2026-08-09 10:45:00', 'tidak_lulus', 'Rizal Inspector OHS',    'Deni Specialist','PT Vale',      'Steering cylinder UG bocor halus & tire pressure error.', '2026-08-09 11:15:00'),
(113, 113, 113, '2026-02-16 09:30:00', 'lulus',       'Ahmad Safety Inspector', 'Kusno Crane Spec','PT SSB',      'Overhead crane lock & outrigger terverifikasi.', '2026-02-16 10:00:00'),
(114, 114, 114, '2026-03-06 10:30:00', 'lulus',       'Rizal Inspector OHS',    'Lukman Mechanic','PT Fleet',     'Emergency exit window & seatbelt 3-point aman.', '2026-03-06 11:00:00'),
(116, 116, 116, '2026-08-17 11:30:00', 'lulus',       'Ahmad Safety Inspector', 'Budi Mechanic', 'PT Pamapersada', 'Pompa penyiram water cannon & nozzle berfungsi presisi.', '2026-08-17 12:00:00'),
(117, 117, 117, '2026-03-13 14:30:00', 'lulus',       'Rizal Inspector OHS',    'Miftah Electric','PT Power',     'Master switch & grounding strap dalam kondisi baik.', '2026-03-13 15:00:00'),
(120, 120, 120, '2025-05-11 10:30:00', 'lulus',       'Ahmad Safety Inspector', 'Fajar Tech',    'PT BUMA',      'Semua indikator mesin normal.', '2025-05-11 11:00:00')
ON DUPLICATE KEY UPDATE `hasil` = VALUES(`hasil`), `catatan_temuan` = VALUES(`catatan_temuan`);


-- ────────────────────────────────────────────────────────────
-- STEP 5: INSERT PERBAIKAN UNIT (UNTUK UNIT TIDAK LULUS)
-- ────────────────────────────────────────────────────────────

INSERT INTO `perbaikan_unit`
(`id_perbaikan`, `id_pengajuan`, `id_uji`, `tgl_max_perbaikan`, `tgl_selesai`, `id_verifikator`, `catatan_perbaikan`, `status`, `created_at`)
VALUES
(101, 111, 111, '2026-08-25', NULL,         NULL,        'Menunggu perbaikan kampas rem & penggantian seal hydraulic dari workshop Port.', 'menunggu', '2026-08-11 14:30:00'),
(102, 112, 112, '2026-08-20', '2026-08-18', @user_admin, 'Penggantian seal steering cylinder UG & kalibrasi sensor tekanan ban telah dilakukan.', 'diverifikasi', '2026-08-09 11:30:00')
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `catatan_perbaikan` = VALUES(`catatan_perbaikan`);


-- ────────────────────────────────────────────────────────────
-- STEP 6: INSERT STIKER RELEASE (DOKUMEN KELUAR)
-- ────────────────────────────────────────────────────────────

INSERT INTO `sticker_release`
(`id_sticker`, `id_pengajuan`, `nomor_sticker`, `tanggal_release`, `tgl_expired`, `created_at`)
VALUES
(101, 101, 'STK-2026-LV101', '2026-01-12 10:15:00', '2027-01-12', '2026-01-12 10:15:00'), -- Aktif (11 bln lagi)
(102, 102, 'STK-2026-LV102', '2026-01-14 11:30:00', '2026-10-14', '2026-01-14 11:30:00'), -- Aktif (2 bln lagi)
(103, 103, 'STK-2026-UG103', '2026-01-17 14:30:00', '2026-09-04', '2026-01-17 14:30:00'), -- Hampir Expired (15 hari lagi)
(104, 104, 'STK-2025-LV104', '2025-07-22 15:15:00', '2026-08-10', '2025-07-22 15:15:00'), -- Expired (10 hari lalu)
(106, 106, 'STK-2026-UG201', '2026-02-08 10:45:00', '2027-02-08', '2026-02-08 10:45:00'), -- Aktif
(113, 113, 'STK-2026-CR401', '2026-02-18 16:15:00', '2027-02-18', '2026-02-18 16:15:00'), -- Aktif
(114, 114, 'STK-2026-BS501', '2026-03-08 11:15:00', '2027-03-08', '2026-03-08 11:15:00'), -- Aktif
(117, 117, 'STK-2026-SP701', '2026-03-15 14:45:00', '2027-03-15', '2026-03-15 14:45:00'), -- Aktif
(120, 120, 'STK-2025-DT204', '2025-05-12 11:15:00', '2026-05-12', '2025-05-12 11:15:00')  -- Expired (3 bulan lalu)
ON DUPLICATE KEY UPDATE `nomor_sticker` = VALUES(`nomor_sticker`), `tgl_expired` = VALUES(`tgl_expired`);

SET FOREIGN_KEY_CHECKS = 1;
