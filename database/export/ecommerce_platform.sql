-- ---------------------------------------------------------------------------
-- E-commerce Platform: database export (MySQL 8+ / MariaDB 10.4+)
--
-- Contents:
--   * Structure of all 13 tables the app uses (including Laravel's sessions,
--     cache and jobs tables)
--   * Data: 1 admin, 3 categories, 6 subcategories, 14 products (no shops or shop owners yet), and the
--     migrations table (so `php artisan migrate` has nothing left to run)
--   * Not included: rows from sessions, cache and jobs (temporary runtime data)
--
-- Import:
--   1. CREATE DATABASE ecommerce_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
--   2. mysql -u root -p ecommerce_platform < database/export/ecommerce_platform.sql
--      (or in phpMyAdmin: select the database > Import)
--   3. Copy database/export/product-images/* into storage/app/public/products/
--      and run `php artisan storage:link`.
--
-- Admin login: admin@example.com / password (role admin; stored as a bcrypt hash; change it!)
-- ---------------------------------------------------------------------------

-- Read the file as UTF-8 (keeps names like "Men's Fashion" intact).
SET NAMES utf8mb4;
-- Timestamps below are written in UTC.
SET TIME_ZONE = '+00:00';
-- Tables are created in alphabetical order, so `products` refers to
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `name` varchar(150) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_subcategory_id_foreign` (`subcategory_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
DROP TABLE IF EXISTS `shops`;
CREATE TABLE `shops` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shops_user_id_unique` (`user_id`),
  UNIQUE KEY `shops_slug_unique` (`slug`),
  CONSTRAINT `shops_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Data
-- ---------------------------------------------------------------------------

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_11_08_000001_create_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_11_08_000002_create_subcategories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_11_08_000003_create_products_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_11_09_000001_add_role_and_is_active_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_11_09_000002_create_shops_table',3);

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES (1,'Admin','admin@example.com',NULL,'$2y$12$W72f./4Y8T6GB67rx5fMrOQJNfgFYPChUWmTF52BJ7rkn6tKXBBRG','admin',1,NULL,'2026-11-07 23:46:08','2026-11-08 12:17:39');

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (1,'Men\'s Fashion','mens-fashion','2026-11-07 23:46:09','2026-11-07 23:46:09');
INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (2,'Electronics','electronics','2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (3,'Home & Kitchen','home-kitchen','2026-11-07 23:46:11','2026-11-07 23:46:11');

INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (1,1,'T-Shirts','t-shirts','2026-11-07 23:46:09','2026-11-07 23:46:09');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (2,1,'Jeans','jeans','2026-11-07 23:46:10','2026-11-07 23:46:10');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (3,2,'Smartphones','smartphones','2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (4,2,'Headphones','headphones','2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (5,3,'Cookware','cookware','2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (6,3,'Lighting','lighting','2026-11-07 23:46:11','2026-11-07 23:46:11');

INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (1,1,1,'Classic Cotton Crew Tee','classic-cotton-crew-tee','Soft 100% cotton tee with a relaxed fit.\nPre-shrunk and machine washable.','products/jw1FCb7yXW8nh6V6we0QJq98Ns31c5U55YWImApC.jpg',1200.00,950.00,'2026-11-07 23:46:10','2026-11-07 23:46:10');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (2,1,1,'Striped Pocket Tee','striped-pocket-tee','Breton stripes with a chest pocket.\nMid-weight jersey that keeps its shape.','products/4pzm0B1G7s448pjlBSHRNDzpo8Ugm0STFDVoAJez.jpg',1400.00,1100.00,'2026-11-07 23:46:10','2026-11-07 23:46:10');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (3,1,1,'Performance Sports Tee','performance-sports-tee','Quick-dry fabric that pulls sweat away.\nFlat seams to stop chafing.','products/bwONAFbdhkT7rZ52OZvl9drJPs0QUQgZiVDbk3Hu.jpg',1600.00,1600.00,'2026-11-07 23:46:10','2026-11-07 23:46:10');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (4,1,2,'Slim Fit Stretch Jeans','slim-fit-stretch-jeans','Dark indigo denim with 2% stretch.\nFive-pocket styling and a slim leg.','products/dlsr9Rl6ET6tv1SOGe6xQakF0NOq4Ha2iuOpTkyo.jpg',3500.00,2800.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (5,1,2,'Relaxed Straight Jeans','relaxed-straight-jeans','Classic straight leg with room through the thigh.\nStone-washed for a lived-in look.','products/HohE4Q7Yd35ZmwoiQT9hohFqDlnIcSOXP2iGIl4j.jpg',3200.00,2900.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (6,2,3,'Nova X 128GB Smartphone','nova-x-128gb-smartphone','6.5-inch AMOLED display, 50 MP camera.\nAll-day 5000 mAh battery with fast charging.','products/DKYjR0Q9EkTmxtSjr4E7sppSc99h9l4VQfzX2Q7q.jpg',42000.00,38500.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (7,2,3,'Lite A5 64GB Smartphone','lite-a5-64gb-smartphone','Compact 6.1-inch screen and dual cameras.\nGreat value for everyday use.','products/LGJhFP2RSxw1SYOXg7xO19ncOUWdah88FxI53J21.jpg',18000.00,15999.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (8,2,4,'Wireless Noise-Cancelling Headphones','wireless-noise-cancelling-headphones','Active noise cancelling with 30-hour battery.\nFolds flat for travel.','products/xstOnjxLRGpLZtWUNwOQLYSrAJfSHhWM7fzqb5dc.jpg',12000.00,9500.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (9,2,4,'True Wireless Earbuds','true-wireless-earbuds','Pocket-size charging case with 24 hours total playback.\nSplash resistant (IPX4).','products/KZL2BPtdpvzrVtLkDF4eGFHci3wleA0slINKp6GT.jpg',4500.00,3600.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (10,2,4,'Wired Studio Headphones','wired-studio-headphones','Closed-back design for accurate monitoring.\nDetachable 3 m cable.','products/yuDuzTeWvmx9gSlPd8QeQg6nipmtoxxgjQXmOV3o.jpg',6000.00,5400.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (11,3,5,'Non-Stick Frying Pan 28cm','non-stick-frying-pan-28cm','Three-layer non-stick coating.\nWorks on gas, electric and induction.','products/Uo4LMU8xFztUXYw6r0d3jDgkNsEieVpzswMwStbK.jpg',2200.00,1750.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (12,3,5,'Stainless Steel Saucepan Set','stainless-steel-saucepan-set','Three saucepans with glass lids.\nDishwasher safe.','products/NcOeNYwjBzLr0qUCza6jdgKclcqentCv7s81iyfe.jpg',5500.00,4600.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (13,3,6,'LED Desk Lamp','led-desk-lamp','Five brightness levels and three colour temperatures.\nUSB charging port in the base.','products/gFAWD47N9K3Elts1Gayw5zzOUlKqIHfVMhRmXUya.jpg',2800.00,2200.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price`, `created_at`, `updated_at`) VALUES (14,3,6,'Rattan Pendant Light','rattan-pendant-light','Hand-woven rattan shade for a warm, natural look.\nFits standard E27 bulbs.','products/GgzJC5xUJF3SQjtgYKJyySAZKVxKHyofrukkEBLI.jpg',4200.00,3900.00,'2026-11-07 23:46:11','2026-11-07 23:46:11');


SET FOREIGN_KEY_CHECKS = 1;
