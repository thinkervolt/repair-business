-- =============================================================================
-- repair-business :: production DB upgrade for the React SPA (Laravel 8 end).
-- 2026-09-19
--
-- The Blade → API+React upgrade adds ONE new table that live production
-- databases do not have: `personal_access_tokens` (Laravel Sanctum). It is what
-- stores the API login token for each user, so logins fail without it.
--
-- USAGE
--   Paste this whole file into the SQL runner of EACH production database
--   (Cloud SQL main + every customer's server DB), then click run.
--
-- SAFE TO RUN
--   - Idempotent: creates the table only if missing, and only registers the
--     migration in the `migrations` table if it is not already recorded.
--   - Does NOT touch any existing table, row, or data.
--
-- OPTIONAL ONLY IF SESSION_DRIVER=database (it is 'file' everywhere):
--   `sessions` table. See bottom of this file.
-- =============================================================================

-- 1) Create the Sanctum tokens table (if it does not already exist).
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Tell Laravel this migration already ran, so the next `php artisan migrate`
--    on a dev machine does not try to create the table again and crash.
--    (batch = highest existing batch + 1).
SET @batch := (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2019_12_14_000001_create_personal_access_tokens_table', @batch
WHERE NOT EXISTS (
    SELECT 1 FROM migrations
    WHERE migration = '2019_12_14_000001_create_personal_access_tokens_table'
);

-- =============================================================================
-- OPTIONAL (only if you ever set SESSION_DRIVER=database, not 'file'):
--
-- CREATE TABLE IF NOT EXISTS `sessions` (
--   `id` varchar(255) NOT NULL,
--   `user_id` bigint unsigned DEFAULT NULL,
--   `ip_address` varchar(45) DEFAULT NULL,
--   `user_agent` text,
--   `payload` text NOT NULL,
--   `last_activity` int NOT NULL,
--   PRIMARY KEY (`id`),
--   KEY `sessions_user_id_index` (`user_id`),
--   KEY `sessions_last_activity_index` (`last_activity`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- =============================================================================