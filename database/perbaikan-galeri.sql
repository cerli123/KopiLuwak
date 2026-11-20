-- ============================================================================
-- Perbaikan Galeri (CADANGAN bila tidak bisa menjalankan `php artisan migrate`)
-- Pakai lewat phpMyAdmin: pilih database -> tab SQL -> tempel -> Kirim.
-- Jalankan SEKALI SAJA. Bila muncul "Duplicate column name", berarti kolomnya
-- sudah ada (aman diabaikan).
-- ============================================================================

ALTER TABLE `galeris`
  ADD COLUMN `deskripsi` TEXT NULL AFTER `judul`,
  ADD COLUMN `kategori` VARCHAR(60) NULL AFTER `deskripsi`,
  ADD COLUMN `urutan` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `kategori`,
  ADD COLUMN `unggulan` TINYINT(1) NOT NULL DEFAULT 0 AFTER `urutan`;
