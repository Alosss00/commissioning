-- ============================================================
-- MIGRATION v8 — Mekanik Info + Temuan + Perbaikan + N/A Fields
-- Copy dari per nomor spaya nda error 1 - 6
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ── 1. uji_kelayakan: tambah info mekanik + temuan ───────────────────
ALTER TABLE `uji_kelayakan`
ADD COLUMN  `nama_mekanik` VARCHAR(200) DEFAULT NULL
AFTER `id_mekanik_master`,
ADD COLUMN  `perusahaan_mekanik` VARCHAR(200) DEFAULT NULL
AFTER `nama_mekanik`;


ALTER TABLE `uji_kelayakan`
CHANGE COLUMN `catatan_umum` `catatan_temuan` TEXT DEFAULT NULL;

-- ── 2. Tabel perbaikan unit (untuk resubmit setelah tidak lulus) ──────
CREATE TABLE  `perbaikan_unit` (
`id_perbaikan` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`id_pengajuan` INT NOT NULL,
`id_uji` INT NOT NULL COMMENT 'Referensi hasil inspeksi tidak lulus',
`tgl_max_perbaikan` DATE NOT NULL COMMENT 'Deadline perbaikan unit',
`tgl_selesai` DATE DEFAULT NULL COMMENT 'Tanggal unit selesai diperbaiki',
`id_verifikator` INT UNSIGNED DEFAULT NULL COMMENT 'User inspektor yang memverifikasi perbaikan',
`catatan_perbaikan` TEXT DEFAULT NULL COMMENT 'Keterangan perbaikan yang dilakukan',
`status` ENUM('menunggu','selesai','diverifikasi') NOT NULL DEFAULT 'menunggu',
`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
`updated_at` DATETIME DEFAULT NULL,
PRIMARY KEY (`id_perbaikan`),
KEY `fk_pb_pengajuan` (`id_pengajuan`),
KEY `fk_pb_uji` (`id_uji`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Data perbaikan unit setelah tidak lulus inspeksi';

-- ── 3. Tabel lampiran perbaikan ───────────────────────────────────────
CREATE TABLE  `perbaikan_lampiran` (
`id_lampiran` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`id_perbaikan` INT UNSIGNED NOT NULL,
`file_path` VARCHAR(300) NOT NULL,
`jenis` VARCHAR(50) NOT NULL DEFAULT 'bukti_perbaikan',
`uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id_lampiran`),
CONSTRAINT `fk_pl_perbaikan`
FOREIGN KEY (`id_perbaikan`) REFERENCES `perbaikan_unit`(`id_perbaikan`)
ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 4. kendaraan: field N/A untuk kolom yang bisa tidak berlaku ───────
-- Solusi: simpan nilai literal 'N/A' di kolom VARCHAR
-- + flag is_na_* untuk kolom yang memang bisa N/A
ALTER TABLE `kendaraan`
ADD COLUMN  `is_na_no_polisi` TINYINT(1) NOT NULL DEFAULT 0 AFTER `no_polisi`,
ADD COLUMN  `is_na_nomor_mesin` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_unit_baru`,
ADD COLUMN  `is_na_nomor_unit` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_na_nomor_mesin`,
ADD COLUMN  `is_na_model_unit` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_na_nomor_unit`;

-- ── 5. pengajuan_uji: flag N/A untuk field yang bisa tidak berlaku ────
ALTER TABLE `pengajuan_uji`
ADD COLUMN  `is_na_nomor_mesin` TINYINT(1) NOT NULL DEFAULT 0 AFTER `nomor_mesin`,
ADD COLUMN  `is_na_nomor_polisi` TINYINT(1) NOT NULL DEFAULT 0 AFTER `nomor_rangka`;

-- ── 6. pengajuan_lampiran: expand ENUM untuk N/A compatibility ────────
ALTER TABLE `pengajuan_lampiran`
MODIFY COLUMN `jenis_lampiran`
ENUM(
'stnk','unit_depan','unit_belakang','unit_kiri','unit_kanan',
'maintenance_record','bukti_perbaikan'
) NOT NULL;

SET FOREIGN_KEY_CHECKS = 1;