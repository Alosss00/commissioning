-- =============================================================================
-- Migration: jenis_kendaraan string → FK id_tipe_kendaraan
-- Tujuan   : Normalisasi 3 tabel (kendaraan, checklist_template,
--             mekanik_tipe_kendaraan) agar pakai FK ke tipe_kendaraan
-- Caller   : Jalankan manual via phpMyAdmin / CLI mysql
-- Dependen : Tabel tipe_kendaraan (sudah ada), data existing valid
-- Side effect:
--   - UPDATE tipe_kendaraan.nama_tipe: rename 'Equipment Support (...)' → 'Equipment Support'
--   - INSERT tipe_kendaraan: tambah 'Water Truck'
--   - ALTER + DROP kolom jenis_kendaraan/jenis_unit lama
--   - ADD kolom id_tipe_kendaraan (FK) di 3 tabel
--   - UPDATE data existing via JOIN nama_tipe
--   - ADD INDEX & FK CONSTRAINT
-- Cara rollback: jalankan migration_tipe_kendaraan_rollback.sql
-- =============================================================================
 
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
 
-- ─────────────────────────────────────────────────────────────────────────────
-- STEP 1: Normalkan nama di tipe_kendaraan
--   - Rename 'Equipment Support (...)' → 'Equipment Support'
--     (lebih pendek, konsisten dengan data di checklist_template & mekanik)
--   - Tambah 'Water Truck' yang ada di mekanik_tipe_kendaraan tapi belum ada
-- ─────────────────────────────────────────────────────────────────────────────
 
UPDATE tipe_kendaraan
SET    nama_tipe = 'Equipment Support'
WHERE  nama_tipe = 'Equipment Support (Genset/Compressor/Lighting/Pump)';
 
-- Tambah Water Truck jika belum ada (idempotent)
INSERT INTO tipe_kendaraan (nama_tipe, kode_tipe, is_active)
SELECT 'Water Truck', 'WT', 1
WHERE NOT EXISTS (
    SELECT 1 FROM tipe_kendaraan WHERE nama_tipe = 'Water Truck'
);
 
-- ─────────────────────────────────────────────────────────────────────────────
-- STEP 2: Tabel KENDARAAN
--   Tambah kolom id_tipe_kendaraan, isi dari JOIN, lalu drop jenis_kendaraan
-- ─────────────────────────────────────────────────────────────────────────────
 
ALTER TABLE kendaraan
    ADD COLUMN id_tipe_kendaraan INT NULL COMMENT 'FK → tipe_kendaraan.id_tipe_kendaraan'
    AFTER nomor_unit;
 
-- Update FK dari nama existing — 1 pass UPDATE JOIN (efisien, no loop)
UPDATE kendaraan k
JOIN   tipe_kendaraan t ON t.nama_tipe = k.jenis_kendaraan
SET    k.id_tipe_kendaraan = t.id_tipe_kendaraan;
 
-- Validasi: pastikan tidak ada NULL yang tertinggal (jika ada, query di bawah kasih info)
-- SELECT id_kendaraan, jenis_kendaraan FROM kendaraan WHERE id_tipe_kendaraan IS NULL;
 
ALTER TABLE kendaraan
    DROP COLUMN jenis_kendaraan;
 
ALTER TABLE kendaraan
    ADD INDEX idx_kendaraan_tipe (id_tipe_kendaraan),
    ADD CONSTRAINT fk_kendaraan_tipe
        FOREIGN KEY (id_tipe_kendaraan)
        REFERENCES tipe_kendaraan (id_tipe_kendaraan)
        ON UPDATE CASCADE
        ON DELETE RESTRICT;
 
-- ─────────────────────────────────────────────────────────────────────────────
-- STEP 3: Tabel CHECKLIST_TEMPLATE
--   Tambah id_tipe_kendaraan, isi dari jenis_unit, drop jenis_unit
-- ─────────────────────────────────────────────────────────────────────────────
 
ALTER TABLE checklist_template
    ADD COLUMN id_tipe_kendaraan INT UNSIGNED NULL COMMENT 'FK → tipe_kendaraan'
    AFTER kode;
 
UPDATE checklist_template ct
JOIN   tipe_kendaraan t ON t.nama_tipe = ct.jenis_unit
SET    ct.id_tipe_kendaraan = t.id_tipe_kendaraan;
 
ALTER TABLE checklist_template
    DROP COLUMN jenis_unit;
 
ALTER TABLE checklist_template
    ADD UNIQUE KEY uk_template_tipe (id_tipe_kendaraan),
    ADD CONSTRAINT fk_template_tipe
        FOREIGN KEY (id_tipe_kendaraan)
        REFERENCES tipe_kendaraan (id_tipe_kendaraan)
        ON UPDATE CASCADE
        ON DELETE RESTRICT;
 
-- ─────────────────────────────────────────────────────────────────────────────
-- STEP 4: Tabel MEKANIK_TIPE_KENDARAAN
--   Tambah id_tipe_kendaraan, isi dari jenis_kendaraan, drop kolom lama
--   Unique key lama (id_mekanik, jenis_kendaraan) diganti (id_mekanik, id_tipe_kendaraan)
-- ─────────────────────────────────────────────────────────────────────────────
 
ALTER TABLE mekanik_tipe_kendaraan
    ADD COLUMN id_tipe_kendaraan INT UNSIGNED NULL COMMENT 'FK → tipe_kendaraan'
    AFTER id_mekanik;
 
UPDATE mekanik_tipe_kendaraan mtk
JOIN   tipe_kendaraan t ON t.nama_tipe = mtk.jenis_kendaraan
SET    mtk.id_tipe_kendaraan = t.id_tipe_kendaraan;
 
-- Drop unique key lama sebelum drop kolom
ALTER TABLE mekanik_tipe_kendaraan
    DROP INDEX uq_mekanik_jenis;
 
ALTER TABLE mekanik_tipe_kendaraan
    DROP COLUMN jenis_kendaraan;
 
ALTER TABLE mekanik_tipe_kendaraan
    ADD UNIQUE KEY uq_mekanik_tipe (id_mekanik, id_tipe_kendaraan),
    ADD INDEX idx_mtk_tipe (id_tipe_kendaraan),
    ADD CONSTRAINT fk_mtk_tipe
        FOREIGN KEY (id_tipe_kendaraan)
        REFERENCES tipe_kendaraan (id_tipe_kendaraan)
        ON UPDATE CASCADE
        ON DELETE CASCADE;
 
-- ─────────────────────────────────────────────────────────────────────────────
-- STEP 5: Selesai
-- ─────────────────────────────────────────────────────────────────────────────
 
COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
 
-- Verifikasi cepat setelah commit:
-- SELECT COUNT(*) FROM kendaraan           WHERE id_tipe_kendaraan IS NULL;
-- SELECT COUNT(*) FROM checklist_template  WHERE id_tipe_kendaraan IS NULL;
-- SELECT COUNT(*) FROM mekanik_tipe_kendaraan WHERE id_tipe_kendaraan IS NULL;