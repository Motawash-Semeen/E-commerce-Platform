-- ---------------------------------------------------------------------------
-- E-commerce Platform: database export (MySQL 8+ / MariaDB 10.4+)
--
-- The same data as `php artisan migrate --seed`, for importing with phpMyAdmin
-- or the mysql client instead of running the migrations.
--
-- Contents:
--   * Structure of every table the app uses (including Laravel's sessions,
--     cache and jobs tables)
--   * Data: 1 admin, 2 shop owners with a shop each, 3 categories,
--     6 subcategories, 10 catalog products, 11 shop listings (shop_product),
--     and the migrations table (so `php artisan migrate` has nothing left to run)
--   * Not included: rows from sessions, cache, jobs and password resets
--     (temporary runtime data)
--
-- Import:
--   1. CREATE DATABASE ecommerce_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
--   2. mysql -u root -p ecommerce_platform < database/export/ecommerce_platform.sql
--      (or in phpMyAdmin: select the database > Import)
--   3. Copy database/export/product-images/* into storage/app/public/products/
--      and run `php artisan storage:link`.
--
-- Logins (all with password "password", stored as bcrypt hashes; change them!):
--   admin@example.com (admin), owner1@example.com and owner2@example.com (shop owners)
-- ---------------------------------------------------------------------------

-- Read the file as UTF-8.
SET NAMES utf8mb4;
-- Timestamps below are written in UTC.
SET TIME_ZONE = '+00:00';
-- Tables are created in alphabetical order, so e.g. `products` refers to
-- `subcategories` before it exists; foreign key checks are switched off
-- during the import and back on at the end.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------------
-- Structure
-- ---------------------------------------------------------------------------

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `subcategory_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_subcategory_id_foreign` (`subcategory_id`),
  KEY `products_created_by_foreign` (`created_by`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `shop_product`;
CREATE TABLE `shop_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_product_shop_id_product_id_unique` (`shop_id`,`product_id`),
  KEY `shop_product_product_id_foreign` (`product_id`),
  CONSTRAINT `shop_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `shop_product_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `shops`;
CREATE TABLE `shops` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shops_owner_id_unique` (`owner_id`),
  UNIQUE KEY `shops_slug_unique` (`slug`),
  CONSTRAINT `shops_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `subcategories`;
CREATE TABLE `subcategories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subcategories_category_id_name_unique` (`category_id`,`name`),
  UNIQUE KEY `subcategories_slug_unique` (`slug`),
  CONSTRAINT `subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'shop_owner',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Data
-- ---------------------------------------------------------------------------

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (1,'Fashion','fashion','2026-11-10 01:55:49','2026-11-10 01:55:49');
INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (2,'Electronics','electronics','2026-11-10 01:55:50','2026-11-10 01:55:50');
INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (3,'Home & Kitchen','home-kitchen','2026-11-10 01:55:51','2026-11-10 01:55:51');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (1,1,'T-Shirts','t-shirts','2026-11-10 01:55:49','2026-11-10 01:55:49');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (2,1,'Jeans','jeans','2026-11-10 01:55:50','2026-11-10 01:55:50');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (3,2,'Smartphones','smartphones','2026-11-10 01:55:50','2026-11-10 01:55:50');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (4,2,'Headphones','headphones','2026-11-10 01:55:50','2026-11-10 01:55:50');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (5,3,'Cookware','cookware','2026-11-10 01:55:51','2026-11-10 01:55:51');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (6,3,'Lighting','lighting','2026-11-10 01:55:51','2026-11-10 01:55:51');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES (1,'Admin','admin@example.com',NULL,'$2y$12$Ai.BNyGtrdrto6LYgj3XmOygu9U1KlZ6MOrmoL2LihDwJxk8fjlxC','admin',1,NULL,'2026-11-10 01:55:48','2026-11-10 01:55:48');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES (2,'Nadia Islam','owner1@example.com',NULL,'$2y$12$431he6rPZMWIrzik8NUFHuWIVqsIPV9wtkfEt.w.ZNxmzDe8SolS6','shop_owner',1,NULL,'2026-11-10 01:55:48','2026-11-10 01:55:48');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES (3,'Tanvir Ahmed','owner2@example.com',NULL,'$2y$12$VwLilI6.3FRlzb6CfqvhvuQMPuGokf/lJ9Cl4d/d0/uK4jAQJIoDW','shop_owner',1,NULL,'2026-11-10 01:55:49','2026-11-10 01:55:49');
INSERT INTO `shops` (`id`, `owner_id`, `name`, `slug`, `description`, `logo`, `is_active`, `created_at`, `updated_at`) VALUES (1,2,'Urban Bazaar','urban-bazaar','Everyday fashion and smart home finds.\nFree gift wrapping on every order.',NULL,1,'2026-11-10 01:55:48','2026-11-10 01:55:48');
INSERT INTO `shops` (`id`, `owner_id`, `name`, `slug`, `description`, `logo`, `is_active`, `created_at`, `updated_at`) VALUES (2,3,'Corner Store','corner-store','Phones, audio and kitchen essentials at fair prices.',NULL,1,'2026-11-10 01:55:49','2026-11-10 01:55:49');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `created_by`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (1,1,1,1,'Classic Cotton Crew Tee','classic-cotton-crew-tee','Soft 100% cotton tee with a relaxed fit.\nPre-shrunk and machine washable.','products/fH4KK8GXG7i09fSBQhdWlRjmkNJ3ANS01SyFRvOp.jpg',1200.00,950.00,'2026-11-10 01:55:49','2026-11-10 01:55:49');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `created_by`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (2,1,1,2,'Striped Pocket Tee','striped-pocket-tee','Breton stripes with a chest pocket.\nMid-weight jersey that keeps its shape.','products/7fPeONVfwXkHzvJNS9zaFT78bEOPcsP0I0BSgDRk.jpg',1400.00,1100.00,'2026-11-10 01:55:49','2026-11-10 01:55:49');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `created_by`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (3,1,2,1,'Slim Fit Stretch Jeans','slim-fit-stretch-jeans','Dark indigo denim with 2% stretch.\nFive-pocket styling and a slim leg.','products/HRuqvv76pHktJObszsvyhlxZ2qfLMYmJ6D1ITa7R.jpg',3500.00,2800.00,'2026-11-10 01:55:50','2026-11-10 01:55:50');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `created_by`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (4,2,3,1,'Nova X 128GB Smartphone','nova-x-128gb-smartphone','6.5-inch AMOLED display, 50 MP camera.\nAll-day 5000 mAh battery with fast charging.','products/5F7jXYSzBTp39bfNjNv6Q6jb9lKeVWdhjWgbtwfa.jpg',42000.00,38500.00,'2026-11-10 01:55:50','2026-11-10 01:55:50');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `created_by`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (5,2,3,3,'Lite A5 64GB Smartphone','lite-a5-64gb-smartphone','Compact 6.1-inch screen and dual cameras.\nGreat value for everyday use.','products/RaAnlvbQBv2iCgHvxUIdi3mADrV1LN6mVOscCRsS.jpg',18000.00,15999.00,'2026-11-10 01:55:50','2026-11-10 01:55:50');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `created_by`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (6,2,4,1,'Wireless Noise-Cancelling Headphones','wireless-noise-cancelling-headphones','Active noise cancelling with 30-hour battery.\nFolds flat for travel.','products/oigri2QC4knkTutzWRACRI6ztWsGCDs0AfYoXMyp.jpg',12000.00,9500.00,'2026-11-10 01:55:50','2026-11-10 01:55:50');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `created_by`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (7,2,4,3,'True Wireless Earbuds','true-wireless-earbuds','Pocket-size charging case with 24 hours total playback.\nSplash resistant (IPX4).','products/aPPWrdIlUfxQodtcQ9ZFcne4gvl4rTZ4SuGpqWVX.jpg',4500.00,3600.00,'2026-11-10 01:55:50','2026-11-10 01:55:50');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `created_by`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (8,3,5,1,'Non-Stick Frying Pan 28cm','non-stick-frying-pan-28cm','Three-layer non-stick coating.\nWorks on gas, electric and induction.','products/D6Jsz5bcJE9kybvW1Zm5QUifISDPjMJtZTsJrEwX.jpg',2200.00,1750.00,'2026-11-10 01:55:51','2026-11-10 01:55:51');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `created_by`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (9,3,6,2,'LED Desk Lamp','led-desk-lamp','Five brightness levels and three colour temperatures.\nUSB charging port in the base.','products/aLluA991nj1scVQ4tFfMIgkXyUQeqLY26H9fxY6E.jpg',2800.00,2200.00,'2026-11-10 01:55:51','2026-11-10 01:55:51');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `created_by`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (10,3,6,1,'Rattan Pendant Light','rattan-pendant-light','Hand-woven rattan shade for a warm, natural look.\nFits standard E27 bulbs.','products/28Ik8MKSZohCsT94BWjCjJrEsy68fCrGHeQawfq0.jpg',4200.00,3900.00,'2026-11-10 01:55:51','2026-11-10 01:55:51');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (1,1,1,899.00,1200.00,'Our best-selling tee, now in five colours.\nBuy two and get free delivery.',NULL,1,'2026-11-10 01:55:51','2026-11-10 01:55:51');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (2,1,2,1050.00,1400.00,NULL,NULL,1,'2026-11-10 01:55:52','2026-11-10 01:55:52');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (3,1,3,2750.00,3500.00,NULL,NULL,1,'2026-11-10 01:55:52','2026-11-10 01:55:52');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (4,1,6,9200.00,12000.00,NULL,NULL,1,'2026-11-10 01:55:52','2026-11-10 01:55:52');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (5,1,9,2100.00,NULL,NULL,NULL,1,'2026-11-10 01:55:52','2026-11-10 01:55:52');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (6,2,1,990.00,NULL,NULL,NULL,1,'2026-11-10 01:55:52','2026-11-10 01:55:52');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (7,2,4,37999.00,42000.00,NULL,NULL,1,'2026-11-10 01:55:52','2026-11-10 01:55:52');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (8,2,5,15499.00,18000.00,NULL,NULL,1,'2026-11-10 01:55:52','2026-11-10 01:55:52');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (9,2,6,9800.00,12000.00,'Includes a hard travel case and a 1-year shop warranty.',NULL,1,'2026-11-10 01:55:52','2026-11-10 01:55:52');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (10,2,7,3500.00,4500.00,NULL,NULL,1,'2026-11-10 01:55:52','2026-11-10 01:55:52');
INSERT INTO `shop_product` (`id`, `shop_id`, `product_id`, `price`, `old_price`, `description`, `thumbnail`, `is_active`, `created_at`, `updated_at`) VALUES (11,2,8,1700.00,2200.00,NULL,NULL,0,'2026-11-10 01:55:52','2026-11-10 01:55:52');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_11_08_000001_create_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_11_08_000002_create_subcategories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_11_08_000003_create_products_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_11_09_000001_add_role_and_is_active_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_11_09_000002_create_shops_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_11_09_000003_create_shop_product_table',1);


SET FOREIGN_KEY_CHECKS = 1;
