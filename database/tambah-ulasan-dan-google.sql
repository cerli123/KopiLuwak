-- =============================================================================
--  Untuk hosting TANPA akses SSH (tidak bisa menjalankan "php artisan migrate").
--  Jalankan SEKALI di phpMyAdmin (tab SQL) pada database toko Anda.
--  Hasilnya sama dengan migrasi:
--    2026_11_20_000001_add_google_to_users_table
--    2026_11_20_000002_create_ulasans_table
--  Jika Anda memakai "php artisan migrate", JANGAN jalankan file ini.
-- =============================================================================

ALTER TABLE `users`
  ADD COLUMN `google_id` VARCHAR(255) NULL AFTER `email`,
  ADD COLUMN `avatar` VARCHAR(500) NULL AFTER `google_id`,
  ADD COLUMN `punya_password` TINYINT(1) NOT NULL DEFAULT 1 AFTER `avatar`,
  ADD UNIQUE KEY `users_google_id_unique` (`google_id`);

CREATE TABLE IF NOT EXISTS `ulasans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `produk_id` BIGINT UNSIGNED NOT NULL,
  `pesanan_id` BIGINT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `komentar` TEXT NULL,
  `tampil` TINYINT(1) NOT NULL DEFAULT 1,
  `balasan_admin` TEXT NULL,
  `dibalas_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ulasans_pesanan_id_produk_id_unique` (`pesanan_id`, `produk_id`),
  KEY `ulasans_produk_id_tampil_index` (`produk_id`, `tampil`),
  KEY `ulasans_user_id_foreign` (`user_id`),
  CONSTRAINT `ulasans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ulasans_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ulasans_pesanan_id_foreign` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Catat ke tabel migrations agar "php artisan migrate" di kemudian hari tidak mengulang.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_20_000001_add_google_to_users_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_20_000002_create_ulasans_table', COALESCE(MAX(`batch`), 0) FROM `migrations`;
