-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 09, 2025 at 12:12 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rk_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachmentable`
--

CREATE TABLE `attachmentable` (
  `id` int(10) UNSIGNED NOT NULL,
  `attachmentable_type` varchar(255) NOT NULL,
  `attachmentable_id` int(10) UNSIGNED NOT NULL,
  `attachment_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `original_name` text NOT NULL,
  `mime` varchar(255) NOT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `size` bigint(20) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `path` text NOT NULL,
  `description` text DEFAULT NULL,
  `alt` text DEFAULT NULL,
  `hash` text DEFAULT NULL,
  `disk` varchar(255) NOT NULL DEFAULT 'public',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_details`
--

CREATE TABLE `bank_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `account_holder_name` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'Phone', 'Its for phone', '01K742RHC2K0W3STVJ91MTG2CV.png', 1, 0, '2025-10-09 03:22:21', '2025-10-09 04:13:56'),
(2, 'Electronics', 'Electronic devices and gadgets', 'electronics.jpg', 1, 0, '2025-10-09 03:24:41', '2025-10-09 04:13:57'),
(3, 'Fashion', 'Clothing and accessories', 'fashion.jpg', 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(4, 'Home & Kitchen', 'Home appliances and kitchen items', 'categories/01K744FHS93Z2YDWR20XE1496R.png', 1, 0, '2025-10-09 03:24:41', '2025-10-09 04:12:53'),
(5, 'Electronics', 'Electronic devices and gadgets', 'electronics.jpg', 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(6, 'Fashion', 'Clothing and accessories', 'fashion.jpg', 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(7, 'Home & Kitchen', 'Home appliances and kitchen items', 'home.jpg', 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `title`, `type`, `value`, `min_amount`, `max_discount`, `valid_from`, `valid_until`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME10', 'Welcome Offer', 'percentage', 10.00, 1000.00, 500.00, '2025-10-09', '2026-01-09', 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(2, 'FLAT500', 'Flat ₹500 Off', 'fixed', 500.00, 5000.00, 500.00, '2025-10-09', '2025-12-09', 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41');

-- --------------------------------------------------------

--
-- Table structure for table `help_support`
--

CREATE TABLE `help_support` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','resolved','closed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_10_09_043930_create_personal_access_tokens_table', 1),
(2, '2025_10_09_044715_create_users_table_ecommerce', 1),
(3, '2025_10_09_045742_create_user_devices_table', 1),
(4, '2025_10_09_045800_create_bank_details_table', 1),
(5, '2025_10_09_060000_create_categories_table', 1),
(6, '2025_10_09_060100_create_sub_categories_table', 1),
(7, '2025_10_09_070000_create_user_addresses_table', 1),
(8, '2025_10_09_080000_create_help_support_table', 1),
(9, '2025_10_09_083050_create_sessions_table', 1),
(10, '2025_10_09_083146_create_cache_table', 1),
(11, '2025_10_09_090000_create_products_table', 1),
(12, '2025_10_09_090100_create_product_colors_table', 1),
(13, '2025_10_09_090200_create_product_reviews_table', 1),
(14, '2025_10_09_090300_create_user_wishlist_table', 1),
(15, '2025_10_09_090400_create_user_cart_table', 1),
(16, '2025_10_09_100000_create_coupons_table', 1),
(17, '2025_10_09_110000_create_orders_table', 1),
(18, '2025_10_09_110100_create_order_items_table', 1),
(19, '2025_10_09_120000_create_notifications_table', 1),
(20, '2015_04_12_000000_create_orchid_users_table', 2),
(21, '2015_10_19_214424_create_orchid_roles_table', 2),
(22, '2015_10_19_214425_create_orchid_role_users_table', 2),
(23, '2016_08_07_125128_create_orchid_attachmentstable_table', 2),
(24, '2017_09_17_125801_create_notifications_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'general',
  `is_read` tinyint(4) NOT NULL DEFAULT 0,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_charges` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `order_status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `address_id` bigint(20) UNSIGNED NOT NULL,
  `coupon_code` varchar(255) DEFAULT NULL,
  `coupon_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estimated_delivery_date` date NOT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `subtotal`, `discount_amount`, `shipping_charges`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `address_id`, `coupon_code`, `coupon_discount`, `estimated_delivery_date`, `delivered_at`, `cancelled_at`, `cancellation_reason`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'ORD1760000081', 2, 150498.00, 0.00, 150.00, 150648.00, 'online', 'paid', 'delivered', 1, NULL, 0.00, '2025-10-16', '2025-10-07 03:24:41', NULL, NULL, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(2, 'ORD1760000145', 2, 150498.00, 0.00, 150.00, 150648.00, 'online', 'refunded', 'cancelled', 1, NULL, 0.00, '2025-10-16', '2025-10-07 03:25:45', NULL, NULL, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_color_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_title` varchar(255) NOT NULL,
  `color_name` varchar(255) DEFAULT NULL,
  `color_value` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `item_status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `refund_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_reviewed` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `user_id`, `seller_id`, `product_id`, `product_color_id`, `product_title`, `color_name`, `color_value`, `quantity`, `unit_price`, `total_price`, `item_status`, `cancelled_at`, `cancellation_reason`, `refund_amount`, `is_reviewed`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 3, 1, 1, 'iPhone 15 Pro Max', 'Natural Titanium', '#8B8B8B', 1, 149900.00, 149900.00, 'cancelled', NULL, NULL, 0.00, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(2, 2, 2, 3, 5, 13, 'Men\'s Cotton T-Shirt', 'Black', '#000000', 1, 599.00, 599.00, 'delivered', NULL, NULL, 0.00, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `min_quantity` int(11) NOT NULL DEFAULT 1,
  `description` text NOT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`images`)),
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating_average` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `name`, `price`, `discounted_price`, `min_quantity`, `description`, `images`, `specifications`, `category_id`, `subcategory_id`, `rating_average`, `rating_count`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 3, 'iPhone 15 Pro Max', 159900.00, 149900.00, 1, 'Latest iPhone with A17 Pro chip, titanium design, and advanced camera system', '[\"iphone-15-pro-1.jpg\",\"iphone-15-pro-2.jpg\",\"iphone-15-pro-3.jpg\"]', '{\"Display\":\"6.7-inch Super Retina XDR\",\"Processor\":\"A17 Pro chip\",\"Camera\":\"48MP Main + 12MP Ultra Wide\",\"Battery\":\"Up to 29 hours video playback\"}', 2, 1, 4.80, 245, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(2, 3, 'Samsung Galaxy S24 Ultra', 134999.00, 124999.00, 1, 'Premium Android flagship with S Pen, 200MP camera, and AI features', '[\"products\\/01K744S6K87XQX9QS4T0GGBG3D.png\",\"products\\/01K744S6K94S4KR8XFFN01CSBZ.png\",\"products\\/01K744S6K94S4KR8XFFN01CSC0.png\",\"products\\/01K744S6K94S4KR8XFFN01CSC1.png\",\"products\\/01K744S6KA3WPWQ8V4TA6D0N3X.png\",\"products\\/01K744S6KA3WPWQ8V4TA6D0N3Y.png\",\"products\\/01K744S6KA3WPWQ8V4TA6D0N3Z.png\"]', '{\"Display\":\"6.8-inch Dynamic AMOLED 2X\",\"Processor\":\"Snapdragon 8 Gen 3\",\"Camera\":\"200MP Main + 50MP Telephoto\",\"RAM\":\"12GB\"}', 2, 1, 4.70, 189, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:58:07'),
(3, 3, 'MacBook Pro 14\" M3', 199900.00, 189900.00, 1, 'Powerful laptop with M3 chip, stunning Liquid Retina XDR display', '[\"macbook-pro-1.jpg\",\"macbook-pro-2.jpg\"]', '{\"Processor\":\"Apple M3 chip\",\"Display\":\"14.2-inch Liquid Retina XDR\",\"RAM\":\"16GB unified memory\",\"Storage\":\"512GB SSD\"}', 2, 2, 4.90, 156, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(4, 3, 'Dell XPS 15', 149999.00, 139999.00, 1, 'Premium Windows laptop with InfinityEdge display and powerful performance', '[\"dell-xps-1.jpg\"]', '{\"Processor\":\"Intel Core i7-13700H\",\"Display\":\"15.6-inch FHD+\",\"RAM\":\"16GB DDR5\",\"Storage\":\"512GB NVMe SSD\"}', 2, 2, 4.60, 98, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(5, 3, 'Men\'s Cotton T-Shirt', 799.00, 599.00, 1, 'Premium quality cotton t-shirt, comfortable and stylish', '[\"tshirt-1.jpg\",\"tshirt-2.jpg\"]', '{\"Material\":\"100% Cotton\",\"Fit\":\"Regular Fit\",\"Care\":\"Machine Wash\"}', 3, 3, 4.30, 567, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(6, 3, 'iPhone 15 Pro Max', 159900.00, 149900.00, 1, 'Latest iPhone with A17 Pro chip, titanium design, and advanced camera system', '[\"iphone-15-pro-1.jpg\",\"iphone-15-pro-2.jpg\",\"iphone-15-pro-3.jpg\"]', '{\"Display\":\"6.7-inch Super Retina XDR\",\"Processor\":\"A17 Pro chip\",\"Camera\":\"48MP Main + 12MP Ultra Wide\",\"Battery\":\"Up to 29 hours video playback\"}', 5, 4, 4.80, 245, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(7, 3, 'Samsung Galaxy S24 Ultra', 134999.00, 124999.00, 1, 'Premium Android flagship with S Pen, 200MP camera, and AI features', '[\"samsung-s24-1.jpg\",\"samsung-s24-2.jpg\"]', '{\"Display\":\"6.8-inch Dynamic AMOLED 2X\",\"Processor\":\"Snapdragon 8 Gen 3\",\"Camera\":\"200MP Main + 50MP Telephoto\",\"RAM\":\"12GB\"}', 5, 4, 4.70, 189, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(8, 3, 'MacBook Pro 14\" M3', 199900.00, 189900.00, 1, 'Powerful laptop with M3 chip, stunning Liquid Retina XDR display', '[\"macbook-pro-1.jpg\",\"macbook-pro-2.jpg\"]', '{\"Processor\":\"Apple M3 chip\",\"Display\":\"14.2-inch Liquid Retina XDR\",\"RAM\":\"16GB unified memory\",\"Storage\":\"512GB SSD\"}', 5, 5, 4.90, 156, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(9, 3, 'Dell XPS 15', 149999.00, 139999.00, 1, 'Premium Windows laptop with InfinityEdge display and powerful performance', '[\"dell-xps-1.jpg\"]', '{\"Processor\":\"Intel Core i7-13700H\",\"Display\":\"15.6-inch FHD+\",\"RAM\":\"16GB DDR5\",\"Storage\":\"512GB NVMe SSD\"}', 5, 5, 4.60, 98, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(10, 3, 'Men\'s Cotton T-Shirt', 799.00, 599.00, 1, 'Premium quality cotton t-shirt, comfortable and stylish', '[\"tshirt-1.jpg\",\"tshirt-2.jpg\"]', '{\"Material\":\"100% Cotton\",\"Fit\":\"Regular Fit\",\"Care\":\"Machine Wash\"}', 6, 6, 4.30, 567, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(11, 3, 'iPhone 15 Pro Max', 159900.00, 149900.00, 1, 'Latest iPhone with A17 Pro chip, titanium design, and advanced camera system', '[\"products\\/01K74529JF47ESECC4MBTBSXQ6.png\"]', '{\"Display\":\"6.7-inch Super Retina XDR\",\"Processor\":\"A17 Pro chip\",\"Camera\":\"48MP Main + 12MP Ultra Wide\",\"Battery\":\"Up to 29 hours video playback\"}', 2, 1, 4.80, 245, 1, 0, '2025-10-09 03:25:45', '2025-10-09 04:13:53'),
(12, 3, 'Samsung Galaxy S24 Ultra', 134999.00, 124999.00, 1, 'Premium Android flagship with S Pen, 200MP camera, and AI features', '[\"samsung-s24-1.jpg\",\"samsung-s24-2.jpg\"]', '{\"Display\":\"6.8-inch Dynamic AMOLED 2X\",\"Processor\":\"Snapdragon 8 Gen 3\",\"Camera\":\"200MP Main + 50MP Telephoto\",\"RAM\":\"12GB\"}', 2, 1, 4.70, 189, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(13, 3, 'MacBook Pro 14\" M3', 199900.00, 189900.00, 1, 'Powerful laptop with M3 chip, stunning Liquid Retina XDR display', '[\"macbook-pro-1.jpg\",\"macbook-pro-2.jpg\"]', '{\"Processor\":\"Apple M3 chip\",\"Display\":\"14.2-inch Liquid Retina XDR\",\"RAM\":\"16GB unified memory\",\"Storage\":\"512GB SSD\"}', 2, 2, 4.90, 156, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(14, 3, 'Dell XPS 15', 149999.00, 139999.00, 1, 'Premium Windows laptop with InfinityEdge display and powerful performance', '[\"dell-xps-1.jpg\"]', '{\"Processor\":\"Intel Core i7-13700H\",\"Display\":\"15.6-inch FHD+\",\"RAM\":\"16GB DDR5\",\"Storage\":\"512GB NVMe SSD\"}', 2, 2, 4.60, 98, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(15, 3, 'Men\'s Cotton T-Shirt', 799.00, 599.00, 1, 'Premium quality cotton t-shirt, comfortable and stylish', '[\"tshirt-1.jpg\",\"tshirt-2.jpg\"]', '{\"Material\":\"100% Cotton\",\"Fit\":\"Regular Fit\",\"Care\":\"Machine Wash\"}', 3, 3, 4.30, 567, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `color_name` varchar(255) NOT NULL,
  `color_code` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`id`, `product_id`, `color_name`, `color_code`, `stock`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 1, 'Natural Titanium', '#8B8B8B', 50, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(2, 1, 'Blue Titanium', '#4A5568', 30, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(3, 1, 'Black Titanium', '#1A1A1A', 45, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(4, 2, 'Titanium Gray', '#6B7280', 40, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(5, 2, 'Titanium Black', '#000000', 35, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(6, 3, 'Space Gray', '#4A4A4A', 25, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(7, 3, 'Silver', '#C0C0C0', 20, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(8, 4, 'Platinum Silver', '#E5E5E5', 30, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(9, 5, 'Black', '#000000', 100, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(10, 5, 'White', '#FFFFFF', 120, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(11, 5, 'Navy Blue', '#000080', 80, 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(12, 6, 'Natural Titanium', '#8B8B8B', 50, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(13, 6, 'Blue Titanium', '#4A5568', 30, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(14, 6, 'Black Titanium', '#1A1A1A', 45, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(15, 7, 'Titanium Gray', '#6B7280', 40, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(16, 7, 'Titanium Black', '#000000', 35, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(17, 8, 'Space Gray', '#4A4A4A', 25, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(18, 8, 'Silver', '#C0C0C0', 20, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(19, 9, 'Platinum Silver', '#E5E5E5', 30, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(20, 10, 'Black', '#000000', 100, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(21, 10, 'White', '#FFFFFF', 120, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(22, 10, 'Navy Blue', '#000080', 80, 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(23, 11, 'Natural Titanium', '#8B8B8B', 50, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(24, 11, 'Blue Titanium', '#4A5568', 30, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(25, 11, 'Black Titanium', '#1A1A1A', 45, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(26, 12, 'Titanium Gray', '#6B7280', 40, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(27, 12, 'Titanium Black', '#000000', 35, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(28, 13, 'Space Gray', '#4A4A4A', 25, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(29, 13, 'Silver', '#C0C0C0', 20, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(30, 14, 'Platinum Silver', '#E5E5E5', 30, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(31, 15, 'Black', '#000000', 100, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(32, 15, 'White', '#FFFFFF', 120, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45'),
(33, 15, 'Navy Blue', '#000080', 80, 1, 0, '2025-10-09 03:25:45', '2025-10-09 03:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL COMMENT '1-5 stars',
  `review` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `user_id`, `product_id`, `rating`, `review`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 2, 7, 5, '.', 1, 0, '2025-10-09 03:58:50', '2025-10-09 03:58:50');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

CREATE TABLE `role_users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('qwpIWztEWGBl8Erso98G2DOshaC2OLgSmXq57fQL', 1, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoia3N6bFc0eE5KcHE1N1ZOUTA2TGF5SjJXenV2V3Ixb3FseWlCUnlDcyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDEvYWRtaW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkYXpMa1hSeWk4SXM4dmZFWTNqRTg3ZWhKd3RGMGpOZ0dqakM0UW9xcm1yOFRmMXo1WXlScEMiO3M6ODoiZmlsYW1lbnQiO2E6MDp7fXM6NjoidGFibGVzIjthOjM6e3M6NDg6IjU3OWVlMzBiMWRhODUzZWVmZjAzNzE2M2RlMTcwMDg4X3RvZ2dsZWRfY29sdW1ucyI7YToyOntzOjEwOiJjcmVhdGVkX2F0IjtiOjE7czoxMDoidXBkYXRlZF9hdCI7YjoxO31zOjQxOiJlMWNkNzU3NmYwMjI4NjM3YWQ2NjU1NGYzOTNmMTQzZV9wZXJfcGFnZSI7czozOiJhbGwiO3M6NDg6IjA5OTFiMWQyMjg2NGU3MmMwZTgzZWIwYTJjZDA3NzI3X3RvZ2dsZWRfY29sdW1ucyI7YToxOntzOjEwOiJjcmVhdGVkX2F0IjtiOjA7fX19', 1760004718);

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `description`, `image`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 2, 'Smartphones', 'Mobile phones and accessories', 'smartphones.jpg', 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(2, 2, 'Laptops', 'Laptops and notebooks', 'laptops.jpg', 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(3, 3, 'Men\'s Clothing', 'Clothing for men', 'mens-wear.jpg', 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(4, 5, 'Smartphones', 'Mobile phones and accessories', 'smartphones.jpg', 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(5, 5, 'Laptops', 'Laptops and notebooks', 'laptops.jpg', 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24'),
(6, 6, 'Men\'s Clothing', 'Clothing for men', 'mens-wear.jpg', 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `government_id` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('customer','seller') NOT NULL DEFAULT 'customer',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `otp` varchar(255) DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `profile_image`, `government_id`, `email_verified_at`, `phone_verified_at`, `password`, `user_type`, `status`, `is_verified`, `otp`, `otp_expires_at`, `created_at`, `updated_at`, `permissions`) VALUES
(1, 'Admin', 'admin@admin.com', '1234567890', NULL, NULL, '2025-10-09 03:18:01', NULL, '$2y$12$azLkXRyi8Is8vfEY3jE87ehJwtF0jNgGjjC4Qoqrmr8Tf1z5YyRpC', 'customer', 'active', 1, NULL, NULL, '2025-10-09 03:18:01', '2025-10-09 03:18:01', NULL),
(2, 'John Doe', 'john@example.com', '9876543210', 'profile/01K744VN9NVZKVV6T3K288NAVV.png', NULL, '2025-10-09 03:24:41', NULL, '$2y$12$yNaWDZQYugzx1PV0ee0J2ukE1w4Eol6yuadfmPMg4h5xMaMJdSlUK', 'customer', 'active', 1, NULL, NULL, '2025-10-09 03:24:41', '2025-10-09 03:59:28', NULL),
(3, 'Tech Store', 'seller@example.com', '9876543211', NULL, NULL, '2025-10-09 03:24:41', NULL, '$2y$12$zUTqvanG1Q2hkX4zd62ByuK5oUGUHd4RpQIpt2Wt5S60oaElFojdO', 'seller', 'active', 1, NULL, NULL, '2025-10-09 03:24:41', '2025-10-09 03:24:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `block_number` varchar(255) NOT NULL,
  `building_name` varchar(255) DEFAULT NULL,
  `area_street` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `full_name`, `block_number`, `building_name`, `area_street`, `state`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 2, 'John Doe', 'A-101', 'Green Valley Apartments', 'MG Road, Bangalore', 'Karnataka', 1, 0, '2025-10-09 03:24:41', '2025-10-09 03:24:41'),
(2, 2, 'John Doe', 'A-101', 'Green Valley Apartments', 'MG Road, Bangalore', 'Karnataka', 1, 0, '2025-10-09 03:25:24', '2025-10-09 03:25:24');

-- --------------------------------------------------------

--
-- Table structure for table `user_cart`
--

CREATE TABLE `user_cart` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_color_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_devices`
--

CREATE TABLE `user_devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `device_type` enum('A','I') NOT NULL COMMENT 'A = Android, I = iOS',
  `ip_address` varchar(50) DEFAULT NULL,
  `uuid` varchar(100) DEFAULT NULL,
  `os_version` varchar(100) DEFAULT NULL,
  `device_model` varchar(100) DEFAULT NULL,
  `app_version` varchar(50) DEFAULT NULL,
  `device_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_wishlist`
--

CREATE TABLE `user_wishlist` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `is_added` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachmentable`
--
ALTER TABLE `attachmentable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachmentable_attachmentable_type_attachmentable_id_index` (`attachmentable_type`,`attachmentable_id`),
  ADD KEY `attachmentable_attachment_id_foreign` (`attachment_id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_details_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`);

--
-- Indexes for table `help_support`
--
ALTER TABLE `help_support`
  ADD PRIMARY KEY (`id`),
  ADD KEY `help_support_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_address_id_foreign` (`address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_user_id_foreign` (`user_id`),
  ADD KEY `order_items_seller_id_foreign` (`seller_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_product_color_id_foreign` (`product_color_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_user_id_foreign` (`user_id`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_subcategory_id_foreign` (`subcategory_id`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_colors_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_reviews_user_id_foreign` (`user_id`),
  ADD KEY `product_reviews_product_id_foreign` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_users`
--
ALTER TABLE `role_users`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_users_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_categories_category_id_foreign` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_addresses_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_cart`
--
ALTER TABLE `user_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_cart_user_id_foreign` (`user_id`),
  ADD KEY `user_cart_product_id_foreign` (`product_id`),
  ADD KEY `user_cart_product_color_id_foreign` (`product_color_id`);

--
-- Indexes for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_devices_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_wishlist`
--
ALTER TABLE `user_wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_wishlist_user_id_product_id_unique` (`user_id`,`product_id`),
  ADD KEY `user_wishlist_product_id_foreign` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachmentable`
--
ALTER TABLE `attachmentable`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_details`
--
ALTER TABLE `bank_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `help_support`
--
ALTER TABLE `help_support`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_cart`
--
ALTER TABLE `user_cart`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_devices`
--
ALTER TABLE `user_devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_wishlist`
--
ALTER TABLE `user_wishlist`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachmentable`
--
ALTER TABLE `attachmentable`
  ADD CONSTRAINT `attachmentable_attachment_id_foreign` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD CONSTRAINT `bank_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `help_support`
--
ALTER TABLE `help_support`
  ADD CONSTRAINT `help_support_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `user_addresses` (`id`),
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_color_id_foreign` FOREIGN KEY (`product_color_id`) REFERENCES `product_colors` (`id`),
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `order_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `sub_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD CONSTRAINT `product_colors_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_users`
--
ALTER TABLE `role_users`
  ADD CONSTRAINT `role_users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD CONSTRAINT `sub_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_cart`
--
ALTER TABLE `user_cart`
  ADD CONSTRAINT `user_cart_product_color_id_foreign` FOREIGN KEY (`product_color_id`) REFERENCES `product_colors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_cart_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_cart_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD CONSTRAINT `user_devices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_wishlist`
--
ALTER TABLE `user_wishlist`
  ADD CONSTRAINT `user_wishlist_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_wishlist_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
