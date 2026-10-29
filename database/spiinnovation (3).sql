-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 14, 2026 at 11:30 AM
-- Server version: 8.4.7
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spiinnovation`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

DROP TABLE IF EXISTS `attributes`;
CREATE TABLE IF NOT EXISTS `attributes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `name`, `created_at`) VALUES
(2, 'Microcontroller', '2026-01-12 06:56:48');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_values`
--

DROP TABLE IF EXISTS `attribute_values`;
CREATE TABLE IF NOT EXISTS `attribute_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `attribute_id` int NOT NULL,
  `value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `attribute_id` (`attribute_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_values`
--

INSERT INTO `attribute_values` (`id`, `attribute_id`, `value`, `created_at`) VALUES
(1, 2, '200', '2026-01-12 06:57:59');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `status`, `created_at`) VALUES
(1, 'Microcontroller', 1, '2026-01-12 12:07:21'),
(2, 'Sensor', 1, '2026-01-12 12:08:07');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `qty` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`product_id`),
  KEY `idx_cart_product` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=249 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `qty`, `created_at`) VALUES
(247, 10, 73, 1, '2026-05-14 10:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `status`, `created_at`) VALUES
(16, 'Displays', 1, '2026-02-02 07:01:45'),
(4, 'Microcontrollers', 1, '2026-01-15 07:03:04'),
(12, 'Sensors', 1, '2026-02-02 06:48:24'),
(13, 'Wireless', 1, '2026-02-02 06:51:55'),
(14, 'Power', 1, '2026-02-02 06:55:09'),
(15, 'Actuators', 1, '2026-02-02 06:58:37'),
(17, 'Development Boards', 1, '2026-05-04 08:17:44'),
(18, 'Modules', 1, '2026-05-04 08:17:44'),
(19, 'Communication Modules', 1, '2026-05-04 08:17:44'),
(20, 'Battery Components', 1, '2026-05-04 08:17:44'),
(21, 'Motors', 1, '2026-05-04 08:17:44'),
(22, 'Indicators', 1, '2026-05-04 08:17:44');

-- --------------------------------------------------------

--
-- Table structure for table `compare`
--

DROP TABLE IF EXISTS `compare`;
CREATE TABLE IF NOT EXISTS `compare` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compare`
--

INSERT INTO `compare` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(52, 10, 73, '2026-05-14 10:13:27'),
(51, 10, 1, '2026-05-14 05:08:23');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `created_at`) VALUES
(8, 'Rajnish', 'rajnish@gmail.com', '8009423206', 'Test', 'Hello', '2026-01-16 11:23:31'),
(7, 'Aditya pratap Vishwakarma', 'adityapratap7391@gmail.com', '07390916239', 'Placement Ke Regarding', 'hiiii', '2026-01-16 11:11:12'),
(9, 'Aditya pratap Vishwakarma', 'adityapratap05072005@gmail.com', '07390916220', 'Test', 'Test', '2026-05-06 13:39:11');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` enum('percent','flat') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'percent',
  `discount_value` decimal(10,2) DEFAULT '0.00',
  `product_id` int DEFAULT '0',
  `min_order_amount` decimal(10,2) DEFAULT '0.00',
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `title`, `code`, `discount_type`, `discount_value`, `product_id`, `min_order_amount`, `status`, `created_at`) VALUES
(2, 'asdfghj', 'save20', 'flat', 20.00, 0, 30000.00, 1, '2026-05-14 08:50:29');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `status`, `created_at`) VALUES
(1, 'Aditya pratap Vishwakarma', 'aditya@gmail.com', '07390916239', 'Ambedkarnagar', 1, '2026-01-12 12:39:11');

-- --------------------------------------------------------

--
-- Table structure for table `customer_addresses`
--

DROP TABLE IF EXISTS `customer_addresses`;
CREATE TABLE IF NOT EXISTS `customer_addresses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_customer_addresses_customer` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_addresses`
--

INSERT INTO `customer_addresses` (`id`, `customer_id`, `name`, `phone`, `city`, `state`, `pincode`, `type`, `is_default`, `created_at`) VALUES
(66, 1, 'Aditya pratap Vishwakarma', '07390916220', 'Lucknow', 'Uttar Pradesh', '226006', 'Work', 1, '2026-05-02 05:27:49'),
(68, 10, 'Surya', '8364578234', 'Chinhat', 'Uttar Pradesh', '226028', 'Home', 1, '2026-05-05 10:01:02'),
(67, 10, 'Aditya Kumar', '6389450032', 'Chinhat', 'Uttar Pradesh', '226028', 'Home', 0, '2026-05-05 09:41:16'),
(64, 10, 'Aditya Pratap Vishwakarma', '7390916220', 'Lucknow', 'Uttar Pradesh', '226006', 'Home', 0, '2026-03-21 12:37:55'),
(69, 11, 'Aditya pratap', '7390916220', 'Lucknow', 'Uttar Pradesh', '226006', 'Home', 1, '2026-05-14 09:49:58');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_api_settings`
--

DROP TABLE IF EXISTS `delivery_api_settings`;
CREATE TABLE IF NOT EXISTS `delivery_api_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `provider_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'India Post Pincode API',
  `api_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://api.postalpincode.in/pincode/{PINCODE}',
  `free_delivery_above` decimal(10,2) NOT NULL DEFAULT '999.00',
  `default_charge` decimal(10,2) NOT NULL DEFAULT '50.00',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_api_settings`
--

INSERT INTO `delivery_api_settings` (`id`, `provider_name`, `api_url`, `free_delivery_above`, `default_charge`, `status`, `created_at`) VALUES
(1, 'India Post Pincode API', 'https://api.postalpincode.in/pincode/{PINCODE}', 999.00, 50.00, 1, '2026-05-04 07:42:57');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_partners`
--

DROP TABLE IF EXISTS `delivery_partners`;
CREATE TABLE IF NOT EXISTS `delivery_partners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE IF NOT EXISTS `login` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `role_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_id` (`login_id`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `name`, `login_id`, `password`, `mobile`, `role`, `role_id`, `status`, `created_at`) VALUES
(1, 'Super Admin', 'admin@gmail.com', '$2y$10$P685OrF.0h28BC6c7usDpOByU1rbkGXpx6.DMwqzYtmRNZW.NWzge', NULL, 'admin', 1, 1, '2026-05-06 09:07:13'),
(2, 'Aditya pratap Vishwakarma', 'adityapratap7391@gmail.com', '$2y$10$hwDlc3LR7o6pCJVYhDXpdOueVS0ZtGANaCtlaizvmf.Fu8a/psIvK', '7390916220', 'user', NULL, 1, '2026-05-06 09:07:13'),
(14, 'adityapratap', 'webdeveloper8009@gmail.com', '$2y$10$Gg1pdMSRKnB.N91koRQqC..v6tSW8gjCXa8lGOXKgZn0fK3FFAFPO', '8009423206', 'user', NULL, 1, '2026-05-06 09:07:13'),
(15, 'Sanjay Kumar', 'sk2860@gmail.com', '$2y$10$s/98I1fkjn7PDvFTBvBFEOaG9rOowyIHNbSxQjJOsgTilinJHVnGq', '8604678897', 'admin', 3, 1, '2026-05-06 09:14:18');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_no` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `courier_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_partner` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `customer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `delivery_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('Pending','Paid','Failed','Refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `order_status` enum('Pending','Confirmed','Packed','Shipped','Delivered','Cancelled','Returned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `address_id` int NOT NULL,
  `user_id` int NOT NULL,
  `razorpay_order_id` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_payment_id` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_signature` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_by` enum('Customer','Admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancel_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `stock_restored` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_orders_user_status` (`user_id`,`order_status`),
  KEY `idx_orders_address` (`address_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_no`, `invoice_no`, `tracking_no`, `courier_name`, `delivery_partner`, `expected_delivery_date`, `delivered_at`, `customer_name`, `customer_email`, `customer_phone`, `total_amount`, `tax_amount`, `delivery_charge`, `payment_method`, `payment_status`, `order_status`, `created_at`, `address_id`, `user_id`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `cancelled_by`, `cancel_reason`, `cancelled_at`, `stock_restored`) VALUES
(1, 'ORD20260512112351708', 'INV202605123439', 'ORD20260512112351708', '', NULL, NULL, '2026-05-12 16:54:48', 'Aditya pratap Vishwakarma', 'adityapratap7391@gmail.com', '7390916220', 683.96, 104.33, 0.00, 'COD', 'Paid', 'Delivered', '2026-05-12 11:23:51', 64, 10, '', '', '', NULL, NULL, NULL, 0),
(2, 'ORD20260512134339173', 'INV202605121243', 'ORD20260512134339173', NULL, NULL, NULL, NULL, 'Aditya pratap Vishwakarma', 'adityapratap7391@gmail.com', '7390916220', 129.99, 19.83, 0.00, 'COD', 'Pending', 'Pending', '2026-05-12 13:43:39', 68, 10, '', '', '', NULL, NULL, NULL, 0),
(3, 'ORD20260512134815268', 'INV202605124431', 'ORD20260512134815268', NULL, NULL, NULL, NULL, 'Aditya pratap Vishwakarma', 'adityapratap7391@gmail.com', '7390916220', 129.99, 19.83, 0.00, 'COD', 'Pending', 'Pending', '2026-05-12 13:48:15', 68, 10, '', '', '', NULL, NULL, NULL, 0),
(4, 'ORD20260514045403776', 'INV202605141079', 'ORD20260514045403776', NULL, NULL, NULL, NULL, 'Aditya pratap Vishwakarma', 'adityapratap7391@gmail.com', '7390916220', 129.99, 19.83, 0.00, 'COD', 'Pending', 'Pending', '2026-05-14 04:54:03', 68, 10, '', '', '', NULL, NULL, NULL, 0),
(5, 'ORD20260514062618385', 'INV202605147409', 'ORD20260514062618385', NULL, NULL, NULL, NULL, 'Aditya pratap Vishwakarma', 'adityapratap7391@gmail.com', '7390916220', 3850.32, 587.34, 0.00, 'COD', 'Pending', 'Pending', '2026-05-14 06:26:18', 68, 10, '', '', '', NULL, NULL, NULL, 0),
(6, 'ORD20260514063342615', '', 'ORD20260514063342615', '', NULL, NULL, '2026-05-14 12:05:25', 'Aditya pratap Vishwakarma', 'adityapratap7391@gmail.com', '7390916220', 4813.20, 734.22, 0.00, 'ONLINE', 'Paid', 'Delivered', '2026-05-14 06:33:42', 68, 10, 'order_Sp99QruwBezxoX', 'pay_Sp99YXctgrZFYI', '424a8e98b8f8b6cce096c69bb5a6368bf9d32899285bcb754b8b3b84416f0a68', NULL, NULL, NULL, 0),
(7, 'ORD20260514095011236', 'INV202605145798', 'ORD20260514095011236', NULL, NULL, NULL, NULL, 'adityapratap', 'webdeveloper8009@gmail.com', '8009423206', 143.23, 21.85, 0.00, 'COD', 'Pending', 'Pending', '2026-05-14 09:50:11', 69, 11, '', '', '', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `qty` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `idx_order_items_product` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=180 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `price`, `tax_percentage`, `tax_amount`, `qty`) VALUES
(13, 15, 69, 200.00, 0.00, 0.00, 1),
(32, 28, 92, 3999.00, 0.00, 0.00, 1),
(30, 26, 99, 89.00, 0.00, 0.00, 1),
(29, 25, 92, 3999.00, 0.00, 0.00, 1),
(18, 22, 72, 299.00, 0.00, 0.00, 2),
(19, 22, 74, 2375.00, 0.00, 0.00, 1),
(20, 22, 87, 6173.00, 0.00, 0.00, 1),
(21, 22, 98, 0.00, 0.00, 0.00, 1),
(22, 23, 96, 129.00, 0.00, 0.00, 1),
(23, 24, 92, 3999.00, 0.00, 0.00, 1),
(24, 24, 93, 260.00, 0.00, 0.00, 1),
(25, 24, 94, 690.00, 0.00, 0.00, 1),
(26, 24, 95, 211.00, 0.00, 0.00, 1),
(27, 24, 96, 129.00, 0.00, 0.00, 1),
(28, 24, 97, 139.00, 0.00, 0.00, 1),
(33, 28, 93, 260.00, 0.00, 0.00, 1),
(34, 28, 94, 690.00, 0.00, 0.00, 1),
(35, 28, 99, 89.00, 0.00, 0.00, 1),
(36, 29, 99, 89.00, 0.00, 0.00, 1),
(37, 30, 99, 89.00, 0.00, 0.00, 1),
(38, 31, 95, 211.00, 0.00, 0.00, 1),
(39, 31, 96, 129.00, 0.00, 0.00, 1),
(40, 31, 97, 139.00, 0.00, 0.00, 1),
(41, 31, 99, 89.00, 0.00, 0.00, 1),
(42, 31, 102, 200.00, 0.00, 0.00, 1),
(43, 32, 79, 430.00, 0.00, 0.00, 1),
(44, 32, 80, 449.00, 0.00, 0.00, 1),
(45, 32, 82, 8780.00, 0.00, 0.00, 1),
(46, 32, 83, 2190.00, 0.00, 0.00, 1),
(47, 32, 87, 6173.00, 0.00, 0.00, 1),
(48, 32, 89, 289.00, 0.00, 0.00, 1),
(49, 32, 90, 999.00, 0.00, 0.00, 1),
(50, 32, 91, 4999.00, 0.00, 0.00, 1),
(51, 33, 92, 4718.82, 18.00, 6478.38, 9),
(52, 34, 74, 2802.50, 18.00, 427.50, 1),
(53, 34, 103, 236.00, 18.00, 36.00, 1),
(54, 35, 95, 248.98, 18.00, 37.98, 1),
(55, 36, 99, 130.00, 20.00, 22.00, 1),
(56, 37, 91, 6017.00, 20.00, 1018.00, 1),
(57, 37, 102, 241.00, 20.00, 41.00, 1),
(58, 38, 104, 130.00, 20.00, 22.00, 1),
(59, 39, 104, 130.00, 20.00, 22.00, 1),
(60, 40, 94, 831.00, 20.00, 141.00, 1),
(61, 40, 95, 254.00, 20.00, 43.00, 1),
(62, 40, 96, 156.00, 20.00, 27.00, 1),
(63, 40, 99, 130.00, 20.00, 22.00, 1),
(64, 40, 102, 241.00, 20.00, 41.00, 1),
(65, 40, 103, 482.00, 20.00, 164.00, 2),
(66, 40, 104, 130.00, 20.00, 22.00, 1),
(67, 41, 90, 1203.00, 20.00, 204.00, 1),
(68, 41, 91, 6017.00, 20.00, 1018.00, 1),
(69, 41, 92, 4814.00, 20.00, 815.00, 1),
(70, 41, 93, 313.00, 20.00, 53.00, 1),
(71, 41, 103, 482.00, 20.00, 82.00, 1),
(72, 41, 104, 130.00, 20.00, 22.00, 1),
(73, 41, 106, 482.00, 20.00, 82.00, 1),
(74, 42, 96, 156.00, 20.00, 27.00, 1),
(75, 43, 104, 130.00, 20.00, 22.00, 1),
(76, 44, 91, 6017.00, 20.00, 1018.00, 1),
(77, 44, 92, 4814.00, 20.00, 815.00, 1),
(78, 44, 94, 831.00, 20.00, 141.00, 1),
(79, 44, 95, 254.00, 20.00, 43.00, 1),
(80, 44, 96, 156.00, 20.00, 27.00, 1),
(81, 45, 103, 482.00, 20.00, 82.00, 1),
(82, 46, 102, 241.00, 20.00, 41.00, 1),
(83, 46, 103, 482.00, 20.00, 82.00, 1),
(84, 46, 104, 130.00, 20.00, 22.00, 1),
(85, 47, 94, 831.00, 20.00, 141.00, 1),
(86, 47, 95, 254.00, 20.00, 43.00, 1),
(87, 47, 96, 156.00, 20.00, 27.00, 1),
(88, 47, 99, 130.00, 20.00, 22.00, 1),
(89, 47, 102, 241.00, 20.00, 41.00, 1),
(90, 47, 103, 482.00, 20.00, 82.00, 1),
(91, 47, 104, 130.00, 20.00, 22.00, 1),
(92, 47, 106, 482.00, 20.00, 82.00, 1),
(93, 48, 94, 831.00, 20.00, 141.00, 1),
(94, 48, 95, 254.00, 20.00, 43.00, 1),
(95, 48, 96, 156.00, 20.00, 27.00, 1),
(96, 49, 103, 482.00, 20.00, 82.00, 1),
(97, 49, 104, 130.00, 20.00, 22.00, 1),
(98, 50, 103, 482.00, 20.00, 82.00, 1),
(99, 50, 104, 130.00, 20.00, 22.00, 1),
(100, 51, 102, 241.00, 20.00, 41.00, 1),
(101, 51, 103, 482.00, 20.00, 82.00, 1),
(102, 51, 104, 130.00, 20.00, 22.00, 1),
(103, 51, 106, 482.00, 20.00, 82.00, 1),
(104, 52, 96, 156.00, 20.00, 27.00, 1),
(105, 53, 90, 1203.00, 20.00, 204.00, 1),
(106, 53, 91, 6017.00, 20.00, 1018.00, 1),
(107, 54, 82, 12432.48, 18.00, 1896.48, 1),
(108, 54, 83, 3101.04, 18.00, 473.04, 1),
(109, 54, 87, 8740.97, 18.00, 1333.37, 1),
(110, 54, 89, 409.22, 18.00, 62.42, 1),
(111, 55, 94, 831.00, 20.00, 141.00, 1),
(112, 55, 92, 4814.00, 20.00, 815.00, 1),
(113, 55, 82, 10568.00, 20.00, 1788.00, 1),
(114, 55, 83, 2636.00, 20.00, 446.00, 1),
(115, 55, 91, 6017.00, 20.00, 1018.00, 1),
(116, 55, 90, 1203.00, 20.00, 204.00, 1),
(117, 55, 87, 7430.00, 20.00, 2514.00, 2),
(118, 55, 89, 348.00, 20.00, 59.00, 1),
(119, 55, 106, 482.00, 20.00, 82.00, 1),
(120, 56, 102, 241.00, 20.00, 41.00, 1),
(121, 56, 104, 130.00, 20.00, 22.00, 1),
(122, 56, 106, 482.00, 20.00, 82.00, 1),
(123, 57, 94, 831.00, 20.00, 141.00, 1),
(124, 57, 95, 254.00, 20.00, 43.00, 1),
(125, 57, 96, 156.00, 20.00, 27.00, 1),
(126, 57, 99, 130.00, 20.00, 22.00, 1),
(127, 58, 102, 241.00, 20.00, 41.00, 1),
(128, 58, 104, 130.00, 20.00, 22.00, 1),
(129, 58, 106, 482.00, 20.00, 328.00, 4),
(130, 59, 106, 482.00, 20.00, 82.00, 1),
(131, 60, 102, 241.00, 20.00, 41.00, 1),
(132, 60, 104, 130.00, 20.00, 22.00, 1),
(133, 61, 94, 831.00, 20.00, 141.00, 1),
(134, 61, 95, 254.00, 20.00, 43.00, 1),
(135, 61, 96, 156.00, 20.00, 27.00, 1),
(136, 62, 82, 10568.00, 20.00, 1788.00, 1),
(137, 62, 83, 2636.00, 20.00, 446.00, 1),
(138, 62, 87, 7430.00, 20.00, 1257.00, 1),
(139, 62, 89, 348.00, 20.00, 236.00, 4),
(140, 63, 82, 10568.00, 20.00, 1788.00, 1),
(141, 63, 83, 2636.00, 20.00, 446.00, 1),
(142, 63, 87, 7430.00, 20.00, 1257.00, 1),
(143, 63, 89, 348.00, 20.00, 59.00, 1),
(144, 64, 94, 831.00, 20.00, 141.00, 1),
(145, 64, 95, 254.00, 20.00, 43.00, 1),
(146, 64, 96, 156.00, 20.00, 27.00, 1),
(147, 64, 102, 241.00, 20.00, 41.00, 1),
(148, 64, 104, 130.00, 20.00, 22.00, 1),
(149, 65, 102, 241.00, 20.00, 41.00, 1),
(150, 65, 104, 130.00, 20.00, 22.00, 1),
(151, 66, 102, 241.00, 20.00, 41.00, 1),
(152, 67, 102, 241.00, 20.00, 41.00, 1),
(153, 68, 104, 130.00, 20.00, 22.00, 1),
(154, 69, 102, 241.00, 20.00, 41.00, 1),
(155, 70, 102, 241.00, 20.00, 82.00, 2),
(156, 71, 99, 130.00, 20.00, 22.00, 1),
(157, 72, 106, 482.00, 20.00, 82.00, 1),
(158, 73, 103, 482.00, 20.00, 82.00, 1),
(159, 73, 104, 130.00, 20.00, 22.00, 1),
(160, 73, 106, 482.00, 20.00, 82.00, 1),
(161, 73, 107, 121.00, 20.00, 21.00, 1),
(162, 74, 106, 482.00, 20.00, 82.00, 1),
(163, 75, 107, 121.00, 20.00, 21.00, 1),
(164, 76, 107, 121.00, 20.00, 21.00, 1),
(165, 77, 103, 386.00, 18.00, 58.88, 1),
(166, 78, 107, 120.36, 18.00, 18.36, 1),
(167, 79, 107, 120.36, 18.00, 18.36, 1),
(168, 80, 107, 120.36, 18.00, 18.36, 1),
(171, 1, 104, 129.60, 18.00, 19.77, 1),
(170, 82, 107, 120.36, 18.00, 18.36, 1),
(172, 1, 106, 434.00, 18.00, 66.20, 1),
(173, 1, 107, 120.36, 18.00, 18.36, 1),
(174, 2, 1, 129.99, 18.00, 19.83, 1),
(175, 3, 1, 129.99, 18.00, 19.83, 1),
(176, 4, 1, 129.99, 18.00, 19.83, 1),
(177, 5, 4, 3850.32, 18.00, 587.34, 1),
(178, 6, 9, 4813.20, 18.00, 734.22, 1),
(179, 7, 74, 143.23, 18.00, 21.85, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

DROP TABLE IF EXISTS `order_status_history`;
CREATE TABLE IF NOT EXISTS `order_status_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_order_status` (`order_id`,`status`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=168 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `order_id`, `status`, `note`, `created_at`) VALUES
(1, 22, 'Pending', 'Order placed successfully', '2026-05-02 06:38:15'),
(2, 22, 'Confirmed', 'Hii, this is Your Order', '2026-05-02 06:53:12'),
(3, 22, 'Cancelled', '', '2026-05-02 06:55:16'),
(4, 22, 'Packed', '', '2026-05-02 06:56:22'),
(5, 22, 'Returned', '', '2026-05-02 06:56:33'),
(8, 22, 'Shipped', '', '2026-05-02 06:57:05'),
(22, 23, 'Cancelled', 'Cancelled by customer: Cancelled from my orders', '2026-05-02 07:50:25'),
(20, 23, 'Pending', 'Order placed successfully', '2026-05-02 07:19:41'),
(21, 24, 'Pending', 'Order placed successfully', '2026-05-02 07:20:40'),
(23, 24, 'Cancelled', 'Cancelled by customer: Cancelled from my orders', '2026-05-02 07:52:12'),
(24, 25, 'Pending', 'Order placed successfully', '2026-05-02 08:21:47'),
(25, 26, 'Pending', 'Order placed successfully', '2026-05-02 08:38:43'),
(38, 16, 'Pending', 'Order placed successfully', '2026-03-21 12:59:11'),
(27, 26, 'Confirmed', 'Order confirmed by admin', '2026-05-02 08:42:45'),
(28, 26, 'Packed', 'Order packed', '2026-05-02 08:42:45'),
(29, 26, 'Shipped', 'Order shipped', '2026-05-02 08:42:45'),
(30, 26, 'Delivered', 'Order delivered', '2026-05-02 08:42:45'),
(39, 17, 'Pending', 'Order placed successfully', '2026-03-21 12:59:36'),
(37, 28, 'Confirmed', 'Order confirmed by admin', '2026-05-02 09:46:38'),
(35, 28, 'Pending', 'Order placed successfully', '2026-05-02 08:48:05'),
(40, 18, 'Pending', 'Order placed successfully', '2026-03-21 12:59:41'),
(41, 19, 'Pending', 'Order placed successfully', '2026-03-21 12:59:45'),
(42, 16, 'Delivered', 'Order delivered', '2026-03-21 12:59:11'),
(43, 17, 'Delivered', 'Order delivered', '2026-03-21 12:59:36'),
(44, 18, 'Delivered', 'Order delivered', '2026-03-21 12:59:41'),
(45, 19, 'Delivered', 'Order delivered', '2026-03-21 12:59:45'),
(46, 29, 'Pending', 'Order placed successfully', '2026-05-02 10:05:08'),
(47, 30, 'Pending', 'Order placed successfully', '2026-05-02 10:10:34'),
(48, 31, 'Pending', 'Order placed successfully', '2026-05-02 12:14:24'),
(49, 32, 'Pending', 'Order placed successfully', '2026-05-02 13:20:21'),
(50, 33, 'Pending', 'Order placed successfully', '2026-05-06 06:21:15'),
(51, 33, 'Cancelled', 'Cancelled by customer: Cancelled from profile page', '2026-05-06 06:23:50'),
(52, 34, 'Pending', 'Order placed successfully', '2026-05-08 05:04:23'),
(53, 35, 'Pending', 'Order placed successfully', '2026-05-08 05:10:44'),
(54, 34, 'Confirmed', 'Order confirmed by admin', '2026-05-08 05:18:15'),
(55, 36, 'Pending', 'Order placed successfully', '2026-05-09 08:01:30'),
(56, 37, 'Pending', 'Order placed successfully', '2026-05-09 09:01:26'),
(57, 37, 'Confirmed', 'Order confirmed by admin', '2026-05-09 09:10:16'),
(58, 37, 'Packed', 'Order packed', '2026-05-09 09:10:16'),
(59, 37, 'Shipped', 'Order shipped', '2026-05-09 09:10:16'),
(60, 37, 'Delivered', 'Order delivered', '2026-05-09 09:10:16'),
(61, 38, 'Pending', 'Order placed successfully', '2026-05-09 09:35:47'),
(62, 38, 'Cancelled', 'Cancelled by customer: Cancelled from latest order page', '2026-05-09 09:56:29'),
(63, 35, 'Cancelled', 'Cancelled by customer: this is not valid', '2026-05-09 10:12:39'),
(64, 39, 'Pending', 'Order placed successfully', '2026-05-09 10:13:58'),
(65, 40, 'Pending', 'Order placed successfully', '2026-05-09 10:21:58'),
(66, 41, 'Pending', 'Order placed successfully', '2026-05-09 10:29:10'),
(67, 42, 'Pending', 'Order placed successfully', '2026-05-09 10:34:30'),
(68, 43, 'Pending', 'Order placed successfully', '2026-05-09 10:35:42'),
(69, 44, 'Pending', 'Order placed successfully', '2026-05-09 10:40:17'),
(70, 45, 'Pending', 'Order placed successfully', '2026-05-09 10:41:47'),
(71, 46, 'Pending', 'Order placed successfully', '2026-05-09 10:44:40'),
(72, 47, 'Pending', 'Order placed successfully', '2026-05-09 11:17:42'),
(73, 47, 'Confirmed', 'Order confirmed by admin', '2026-05-09 11:18:18'),
(74, 47, 'Packed', 'Order packed', '2026-05-09 11:18:18'),
(75, 47, 'Shipped', 'Order shipped', '2026-05-09 11:18:18'),
(76, 47, 'Delivered', 'Order delivered', '2026-05-09 11:18:36'),
(77, 48, 'Pending', 'Order placed successfully', '2026-05-09 11:41:47'),
(78, 48, 'Confirmed', 'Order confirmed by admin', '2026-05-09 11:43:47'),
(79, 48, 'Packed', 'Order packed', '2026-05-09 11:43:47'),
(80, 48, 'Shipped', 'Order shipped', '2026-05-09 11:43:47'),
(81, 48, 'Delivered', 'Order delivered', '2026-05-09 11:43:47'),
(82, 49, 'Pending', 'Order placed successfully', '2026-05-09 11:46:13'),
(83, 49, 'Confirmed', 'Order confirmed by admin', '2026-05-09 11:46:48'),
(84, 49, 'Packed', 'Order packed', '2026-05-09 11:46:48'),
(85, 49, 'Shipped', 'Order shipped', '2026-05-09 11:46:48'),
(86, 49, 'Delivered', 'Order delivered', '2026-05-09 11:46:48'),
(87, 46, 'Confirmed', 'Order confirmed by admin', '2026-05-09 12:07:16'),
(88, 46, 'Packed', 'Order packed', '2026-05-09 12:07:16'),
(89, 46, 'Shipped', 'Order shipped', '2026-05-09 12:07:16'),
(90, 46, 'Delivered', 'Order delivered', '2026-05-09 12:07:16'),
(91, 50, 'Pending', 'Order placed successfully', '2026-05-09 13:03:40'),
(92, 51, 'Pending', 'Order placed successfully', '2026-05-09 13:15:10'),
(93, 44, 'Confirmed', 'Order confirmed by admin', '2026-05-09 13:31:22'),
(94, 44, 'Packed', 'Order packed', '2026-05-09 13:31:22'),
(95, 44, 'Shipped', 'Order shipped', '2026-05-09 13:31:22'),
(96, 44, 'Delivered', 'Order delivered', '2026-05-09 13:31:22'),
(97, 50, 'Confirmed', 'Order confirmed by admin', '2026-05-09 13:31:50'),
(98, 50, 'Packed', 'Order packed', '2026-05-09 13:31:50'),
(99, 50, 'Shipped', 'Order shipped', '2026-05-09 13:31:50'),
(100, 50, 'Delivered', 'Order delivered', '2026-05-09 13:31:50'),
(101, 52, 'Pending', 'Order placed successfully', '2026-05-09 13:56:32'),
(102, 53, 'Pending', 'Order placed successfully', '2026-05-09 13:59:47'),
(103, 53, 'Confirmed', 'Order confirmed by admin', '2026-05-09 14:00:31'),
(104, 53, 'Packed', 'Order packed', '2026-05-09 14:00:31'),
(105, 53, 'Shipped', 'Order shipped', '2026-05-09 14:00:31'),
(106, 53, 'Delivered', 'Order delivered', '2026-05-09 14:00:31'),
(107, 34, 'Packed', 'Order packed', '2026-05-09 14:06:12'),
(108, 34, 'Shipped', 'Order shipped', '2026-05-09 14:06:12'),
(109, 34, 'Delivered', 'Order delivered', '2026-05-09 14:06:12'),
(110, 52, 'Confirmed', 'Order confirmed by admin', '2026-05-09 14:07:12'),
(111, 52, 'Packed', 'Order packed', '2026-05-09 14:07:12'),
(112, 52, 'Shipped', 'Order shipped', '2026-05-09 14:07:12'),
(113, 52, 'Delivered', 'Order delivered', '2026-05-09 14:07:12'),
(114, 54, 'Pending', 'Order placed successfully', '2026-05-12 05:34:20'),
(115, 55, 'Pending', 'Order placed successfully', '2026-05-12 05:47:59'),
(116, 55, 'Confirmed', 'Order confirmed by admin', '2026-05-12 05:48:24'),
(117, 55, 'Packed', 'Order packed', '2026-05-12 05:48:24'),
(118, 55, 'Shipped', 'Order shipped', '2026-05-12 05:48:24'),
(119, 55, 'Delivered', 'Order delivered', '2026-05-12 05:48:24'),
(120, 56, 'Pending', 'Order placed successfully', '2026-05-12 05:50:38'),
(121, 56, 'Confirmed', 'Order confirmed by admin', '2026-05-12 05:53:45'),
(122, 56, 'Packed', 'Order packed', '2026-05-12 05:53:45'),
(123, 56, 'Shipped', 'Order shipped', '2026-05-12 05:53:45'),
(124, 56, 'Delivered', 'Order delivered', '2026-05-12 05:53:45'),
(125, 57, 'Pending', 'Order placed successfully', '2026-05-12 05:55:14'),
(126, 58, 'Pending', 'Order placed successfully', '2026-05-12 05:57:32'),
(127, 59, 'Pending', 'Order placed successfully', '2026-05-12 05:58:15'),
(128, 60, 'Pending', 'Order placed successfully', '2026-05-12 05:59:33'),
(129, 61, 'Pending', 'Order placed successfully', '2026-05-12 06:03:00'),
(130, 62, 'Pending', 'Order placed successfully', '2026-05-12 06:04:17'),
(131, 63, 'Pending', 'Order placed successfully', '2026-05-12 06:05:04'),
(132, 64, 'Pending', 'Order placed successfully', '2026-05-12 06:08:55'),
(133, 65, 'Pending', 'Order placed successfully', '2026-05-12 06:15:18'),
(134, 66, 'Pending', 'Order placed successfully', '2026-05-12 06:41:25'),
(135, 67, 'Pending', 'Order placed successfully', '2026-05-12 06:42:00'),
(136, 68, 'Pending', 'Order placed successfully', '2026-05-12 06:51:13'),
(137, 69, 'Pending', 'Order placed successfully', '2026-05-12 07:00:39'),
(138, 70, 'Pending', 'Order placed successfully', '2026-05-12 07:02:14'),
(139, 71, 'Pending', 'Order placed successfully', '2026-05-12 07:12:31'),
(140, 72, 'Pending', 'Order placed successfully', '2026-05-12 07:18:56'),
(141, 73, 'Pending', 'Order placed successfully', '2026-05-12 09:51:11'),
(142, 74, 'Pending', 'Order placed successfully', '2026-05-12 10:01:24'),
(143, 75, 'Pending', 'Order placed successfully', '2026-05-12 10:02:00'),
(144, 76, 'Pending', 'Order placed successfully', '2026-05-12 10:08:01'),
(145, 77, 'Pending', 'Order placed successfully', '2026-05-12 10:11:06'),
(146, 78, 'Pending', 'Order placed successfully', '2026-05-12 10:11:38'),
(147, 79, 'Pending', 'Order placed successfully', '2026-05-12 10:12:13'),
(148, 80, 'Pending', 'Order placed successfully', '2026-05-12 10:15:56'),
(152, 80, 'Cancelled', 'Cancelled by customer: Cancelled from previous orders page', '2026-05-12 11:19:00'),
(150, 82, 'Pending', 'Order placed successfully', '2026-05-12 10:20:07'),
(153, 1, 'Pending', 'Order placed successfully', '2026-05-12 11:23:51'),
(154, 1, 'Confirmed', 'Order confirmed by admin', '2026-05-12 11:24:48'),
(155, 1, 'Packed', 'Order packed', '2026-05-12 11:24:48'),
(156, 1, 'Shipped', 'Order shipped', '2026-05-12 11:24:48'),
(157, 1, 'Delivered', 'Order delivered', '2026-05-12 11:24:48'),
(158, 2, 'Pending', 'Order placed successfully', '2026-05-12 13:43:39'),
(159, 3, 'Pending', 'Order placed successfully', '2026-05-12 13:48:15'),
(160, 4, 'Pending', 'Order placed successfully', '2026-05-14 04:54:03'),
(161, 5, 'Pending', 'Order placed successfully', '2026-05-14 06:26:18'),
(162, 6, 'Pending', 'Order placed successfully', '2026-05-14 06:33:42'),
(163, 6, 'Confirmed', 'Order confirmed by admin', '2026-05-14 06:35:25'),
(164, 6, 'Packed', 'Order packed', '2026-05-14 06:35:25'),
(165, 6, 'Shipped', 'Order shipped', '2026-05-14 06:35:25'),
(166, 6, 'Delivered', 'Order delivered', '2026-05-14 06:35:25'),
(167, 7, 'Pending', 'Order placed successfully', '2026-05-14 09:50:11');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `method_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `method_name`, `status`, `created_at`) VALUES
(1, 'COD', 1, '2026-01-15 04:49:42'),
(2, 'Online', 1, '2026-05-02 06:24:37');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `sub_category_id` int DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seller` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `default_tax_percentage` decimal(5,2) NOT NULL DEFAULT '2.00',
  `price` decimal(10,2) DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `tax_percentage` decimal(5,2) DEFAULT '0.00',
  `delivery_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rating` decimal(2,1) DEFAULT NULL,
  `ratings_count` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bought` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `main_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `sub_category_id`, `title`, `store`, `seller`, `base_price`, `default_tax_percentage`, `price`, `old_price`, `discount`, `tax_percentage`, `delivery_charge`, `rating`, `ratings_count`, `bought`, `stock`, `main_image`, `image1`, `image2`, `image3`, `image4`, `status`, `created_at`) VALUES
(2, 17, 32, 'UNO R3 SMD Board', 'Softpro India', 'Softpro India', 349.00, 2.00, 420.06, 420.06, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778738882_main_image_7223.png', '1778738882_image1_8351.png', '1778738882_image2_6608.png', '1778738882_image3_3457.png', '1778738882_image4_1718.png', 1, '2026-05-14 06:08:02'),
(3, 17, 32, 'Arduino Uno', 'SPI Store', 'SIPL', 2375.00, 2.00, 2858.55, 2858.55, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778738973_main_image_2421.png', '1778738973_image1_8528.png', '1778738973_image2_8668.png', '1778738973_image3_5310.png', '1778738973_image4_2051.png', 1, '2026-05-14 06:09:33'),
(4, 17, 32, 'Arduino Mega', 'SPI Store', 'SIPL', 3199.00, 2.00, 3850.32, 3850.32, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 99, '1778739054_main_image_5038.png', '1778739054_image1_2544.png', '1778739054_image2_7049.png', '1778739054_image3_9839.png', '1778739054_image4_2802.png', 1, '2026-05-14 06:10:18'),
(5, 17, 32, 'Arduino Nano', 'SPI Store', 'SIPL', 299.00, 2.00, 359.88, 359.88, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778739202_main_image_3418.png', '1778739202_image1_9309.png', '1778739202_image2_5706.png', '1778739202_image3_5883.png', '1778739202_image4_6368.png', 1, '2026-05-14 06:13:22'),
(6, 17, 32, 'Arduino Pro Mini', 'SPI Store', 'SIPL', 315.00, 2.00, 321.30, 321.30, 0.00, 0.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778739266_main_image_1448.png', '1778739266_image1_4417.png', '1778739266_image2_4330.png', '1778739266_image3_3777.png', '1778739266_image4_7931.png', 1, '2026-05-14 06:14:26'),
(7, 17, 35, 'ESP8266', 'SPI Store', 'SIPL', 430.00, 2.00, 517.55, 517.55, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778739476_main_image_5065.png', '1778739476_image1_5618.png', '1778739476_image2_8502.png', '1778739476_image3_7957.png', '1778739476_image4_6116.png', 1, '2026-05-14 06:17:56'),
(8, 17, 35, 'ESP32', 'SPI Store', 'SIPL', 449.00, 2.00, 540.42, 540.42, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778739573_main_image_2320.png', '1778739573_image1_7284.png', '1778739573_image2_5799.png', '1778739573_image3_6474.png', '1778739573_image4_4980.png', 1, '2026-05-14 06:19:33'),
(9, 17, 58, 'Raspberry Pi 3 Model B', 'SPI Store', 'SIPL', 3999.00, 2.00, 4813.20, 4813.20, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 99, '1778739669_main_image_8737.png', '1778739669_image1_9863.png', '1778739669_image2_6574.png', '1778739669_image3_7362.png', '1778739669_image4_8583.png', 1, '2026-05-14 06:21:09'),
(10, 17, 58, 'Raspberry Pi 4 Model B', 'SPI Store', 'SIPL', 4999.00, 2.00, 6016.80, 6016.80, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778740829_main_image_9928.png', '1778740829_image1_9076.png', '1778740829_image2_9120.png', '1778740829_image3_9726.png', '1778740829_image4_8390.png', 1, '2026-05-14 06:40:29'),
(11, 17, 58, 'Raspberry Pi  5', 'SPI Store', 'SIPL', 8780.00, 2.00, 10567.61, 10567.61, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778740898_main_image_6015.png', '1778740898_image1_4855.png', '1778740898_image2_1424.png', '1778740898_image3_4047.png', '1778740898_image4_6725.png', 1, '2026-05-14 06:41:38'),
(12, 17, 59, 'STEM32 Board', 'SPI Store', 'SIPL', 2190.00, 2.00, 2635.88, 2635.88, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778740985_main_image_7359.png', '1778740985_image1_1296.png', '1778740985_image2_6493.png', '1778740985_image3_9814.png', '1778740985_image4_5050.png', 1, '2026-05-14 06:43:05'),
(13, 17, 62, 'BeagleBone Black', 'SPI Store', 'SIPL', 6173.00, 2.00, 7429.82, 7429.82, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778741174_main_image_9156.png', '1778741174_image1_4318.png', '1778741174_image2_9017.png', '1778741174_image3_2147.png', '1778741174_image4_8287.png', 1, '2026-05-14 06:46:14'),
(14, 4, 60, 'PIC Microcontroller', 'SPI Store', 'SIPL', 999.00, 2.00, 1202.40, 1202.40, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778741258_main_image_8554.png', '1778741258_image1_8017.png', '1778741258_image2_5301.png', '1778741258_image3_4729.png', '1778741258_image4_5446.png', 1, '2026-05-14 06:47:38'),
(15, 4, 61, 'AT tiny85', 'SPI Store', 'SIPL', 289.00, 2.00, 347.84, 347.84, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778741321_main_image_1413.png', '1778741321_image1_8287.png', '1778741321_image2_5007.png', '1778741321_image3_5142.png', '1778741321_image4_9520.png', 1, '2026-05-14 06:48:41'),
(16, 12, 63, 'DHT11', 'SPI Store', 'SIPL', 260.00, 2.00, 312.94, 312.94, 0.00, 18.00, 0.00, 5.0, '10', '20+', 100, '1778741852_main_image_1631.png', '1778741852_image1_4919.png', '1778741852_image2_3063.png', '1778741852_image3_2775.png', '1778741852_image4_2916.png', 1, '2026-05-14 06:57:32'),
(17, 12, 63, 'DHT22', 'SPI Store', 'SIPL', 690.00, 2.00, 830.48, 830.48, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778742014_main_image_1660.png', '1778742486_image1_8802.png', '1778742014_image2_1104.png', '1778742014_image3_4550.png', '1778742014_image4_7072.png', 1, '2026-05-14 07:00:14'),
(18, 12, 63, 'DS18B20', 'SPI Store', 'SIPL', 211.00, 2.00, 253.96, 253.96, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778742135_main_image_3575.png', '1778742135_image1_1592.png', '1778742135_image2_7140.png', '1778742135_image3_2238.png', '1778742135_image4_3366.png', 1, '2026-05-14 07:02:15'),
(19, 12, 64, 'PIR', 'SPI Store', 'SIPL', 129.00, 2.00, 155.26, 155.26, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778742222_main_image_9887.png', '1778742222_image1_1600.png', '1778742222_image2_4385.png', '1778742222_image3_1931.png', '1778742222_image4_2520.png', 1, '2026-05-14 07:03:42'),
(20, 12, 64, 'Ultrasonic HC-SR04', 'SPI Store', 'SIPL', 139.00, 2.00, 167.30, 167.30, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778742302_main_image_1510.png', '1778742302_image1_2094.png', '1778742302_image2_1142.png', '1778742302_image3_9734.png', '1778742302_image4_1766.png', 1, '2026-05-14 07:05:02'),
(21, 12, 65, 'MQ-2', 'SPI Store', 'SIPL', 10.00, 2.00, 12.04, 12.04, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778742465_main_image_5666.png', '1778742465_image1_3732.png', '1778742465_image2_5480.png', '1778742465_image3_6689.png', '1778742465_image4_2319.png', 1, '2026-05-14 07:06:42'),
(22, 12, 66, 'IR', 'SPI Store', 'SIPL', 89.00, 2.00, 107.12, 107.12, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778742557_main_image_4327.png', '1778742557_image1_4607.png', '1778742557_image2_3865.png', '1778742557_image3_9507.png', '1778742557_image4_3893.png', 1, '2026-05-14 07:09:17'),
(23, 12, 66, 'LDR', 'SPI Store', 'SIPL', 189.00, 2.00, 227.48, 227.48, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778742637_main_image_4787.png', '1778742637_image1_7548.png', '1778742637_image2_4670.png', '1778742637_image3_4545.png', '1778742637_image4_5635.png', 1, '2026-05-14 07:10:37'),
(24, 12, 67, 'MPU6050', 'SPI Store', 'SIPL', 299.00, 2.00, 359.88, 359.88, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778742718_main_image_2622.png', '1778742718_image1_9063.png', '1778742718_image2_7452.png', '1778742718_image3_2307.png', '1778742718_image4_7870.png', 1, '2026-05-14 07:11:58'),
(25, 12, 67, 'ADXL345', 'SPI Store', 'SIPL', 399.00, 2.00, 480.24, 480.24, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778742779_main_image_8402.png', '1778742779_image1_4161.png', '1778742779_image2_6225.png', '1778742779_image3_5422.png', '1778742779_image4_5012.png', 1, '2026-05-14 07:12:59'),
(26, 12, 68, 'KY-038', 'SPI Store', 'SIPL', 199.00, 2.00, 239.52, 239.52, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778742909_main_image_2639.png', '1778742909_image1_2988.png', '1778742909_image2_5820.png', '1778742909_image3_9594.png', '1778742909_image4_2591.png', 1, '2026-05-14 07:15:09'),
(27, 12, 68, 'SW-420', 'SPI Store', 'SIPL', 144.00, 2.00, 173.32, 173.32, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778743003_main_image_6112.png', '1778743003_image1_5529.png', '1778743003_image2_5887.png', '1778743003_image3_3061.png', '1778743003_image4_5055.png', 1, '2026-05-14 07:16:43'),
(28, 12, 69, 'Fingerprint sensor', 'SPI Store', 'SIPL', 1079.00, 2.00, 1298.68, 1298.68, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778743075_main_image_5242.png', '1778743075_image1_3642.png', '1778743075_image2_9006.png', '1778743075_image3_1837.png', '1778743075_image4_1918.png', 1, '2026-05-14 07:17:55'),
(29, 12, 65, 'MQ-135', 'SPI Store', 'SIPL', 10.00, 2.00, 12.04, 12.04, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778743298_main_image_8109.png', '1778743380_image1_4048.png', '1778743298_image2_9209.png', '1778743298_image3_4892.png', '1778743298_image4_3412.png', 1, '2026-05-14 07:21:38'),
(30, 19, 70, 'NRF24L01', 'SPI Store', 'SIPL', 350.00, 2.00, 421.26, 421.26, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778743545_main_image_8767.png', '1778743545_image1_4141.png', '1778743545_image2_4522.png', '1778743545_image3_7313.png', '1778743545_image4_2422.png', 1, '2026-05-14 07:25:45'),
(31, 19, 70, '433MHz', 'SPI Store', 'SIPL', 99.00, 2.00, 119.16, 119.16, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778743622_main_image_8570.png', '1778743622_image1_1176.png', '1778743622_image2_6293.png', '1778743622_image3_4282.png', '1778743622_image4_9833.png', 1, '2026-05-14 07:27:02'),
(32, 19, 71, 'HC-05', 'SPI Store', 'SIPL', 345.00, 2.00, 415.24, 415.24, 0.00, 18.00, 0.00, 4.0, '10', '20+', 100, '1778743707_main_image_6731.png', '1778743707_image1_6880.png', '1778743707_image2_4347.png', '1778743707_image3_8020.png', '1778743707_image4_5001.png', 1, '2026-05-14 07:28:27'),
(33, 19, 71, 'HC-06', 'SPI Store', 'SIPL', 469.00, 2.00, 564.49, 564.49, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778743765_main_image_4814.png', '1778743765_image1_2636.png', '1778743765_image2_6278.png', '1778743765_image3_7018.png', '1778743765_image4_7000.png', 1, '2026-05-14 07:29:25'),
(34, 19, 72, 'SIM800L', 'SPI Store', 'SIPL', 625.00, 2.00, 752.25, 752.25, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778743829_main_image_1345.png', '1778743829_image1_9395.png', '1778743829_image2_1359.png', '1778743829_image3_2090.png', '1778743829_image4_8975.png', 1, '2026-05-14 07:30:29'),
(35, 19, 72, 'SIM900A', 'SPI Store', 'SIPL', 725.00, 2.00, 872.61, 872.61, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778743892_main_image_1171.png', '1778743892_image1_5913.png', '1778743892_image2_7530.png', '1778743892_image3_8217.png', '1778743892_image4_3046.png', 1, '2026-05-14 07:31:32'),
(36, 19, 73, 'SX1278', 'SPI Store', 'SIPL', 595.00, 2.00, 716.14, 716.14, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778743945_main_image_6590.png', '1778743945_image1_8523.png', '1778743945_image2_3275.png', '1778743945_image3_9137.png', '1778743945_image4_2798.png', 1, '2026-05-14 07:32:25'),
(37, 19, 74, 'CC2530', 'SPI Store', 'SIPL', 1539.00, 2.00, 1852.34, 1852.34, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778744001_main_image_5475.png', '1778744001_image1_3550.png', '1778744001_image2_4146.png', '1778744001_image3_6525.png', '1778744001_image4_8975.png', 1, '2026-05-14 07:33:21'),
(38, 19, 74, 'XBee', 'SPI Store', 'SIPL', 325.00, 2.00, 391.17, 391.17, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778744073_main_image_1905.png', '1778744073_image1_4566.png', '1778744073_image2_3064.png', '1778744073_image3_6827.png', '1778744073_image4_1120.png', 1, '2026-05-14 07:34:33'),
(39, 19, 75, 'NEO-6M', 'SPI Store', 'SIPL', 517.00, 2.00, 622.26, 622.26, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778744141_main_image_1927.png', '1778744141_image1_7453.png', '1778744141_image2_7981.png', '1778744141_image3_4110.png', '1778744141_image4_8195.png', 1, '2026-05-14 07:35:41'),
(40, 20, 76, '18650', 'SPI Store', 'SIPL', 495.00, 2.00, 595.78, 595.78, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778744265_main_image_2727.png', '1778744265_image1_1540.png', '1778744265_image2_4164.png', '1778744265_image3_7817.png', '1778744265_image4_2838.png', 1, '2026-05-14 07:37:45'),
(41, 20, 76, 'Li-po Battery', 'SPI Store', 'SIPL', 388.00, 2.00, 467.00, 467.00, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778744351_main_image_3371.png', '1778744351_image1_9191.png', '1778744351_image2_6562.png', '1778744351_image3_5194.png', '1778744351_image4_5721.png', 1, '2026-05-14 07:39:11'),
(42, 20, 76, '12v Li-po Battery', 'SPI Store', 'SIPL', 1499.00, 2.00, 1804.20, 1804.20, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778744428_main_image_8510.png', '1778744428_image1_1097.png', '1778744428_image2_9750.png', '1778744428_image3_9629.png', '1778744428_image4_9364.png', 1, '2026-05-14 07:40:28'),
(43, 20, 77, 'BMS', 'SPI Store', 'SIPL', 199.00, 2.00, 239.52, 239.52, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778744493_main_image_3344.png', '1778744493_image1_4790.png', '1778744493_image2_7260.png', '1778744493_image3_9920.png', '1778744493_image4_2911.png', 1, '2026-05-14 07:41:33'),
(44, 14, 78, 'LM7805', 'SPI Store', 'SIPL', 49.00, 2.00, 58.98, 58.98, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778744567_main_image_5364.png', '1778744567_image1_8438.png', '1778744567_image2_5917.png', '1778744567_image3_6173.png', '1778744567_image4_5483.png', 1, '2026-05-14 07:42:47'),
(45, 14, 78, 'LM317', 'SPI Store', 'SIPL', 44.00, 2.00, 52.96, 52.96, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778744629_main_image_4101.png', '1778744629_image1_2184.png', '1778744629_image2_5284.png', '1778744629_image3_1065.png', '1778744629_image4_4174.png', 1, '2026-05-14 07:43:49'),
(46, 14, 78, 'AMS1117', 'SPI Store', 'SIPL', 70.00, 2.00, 84.25, 84.25, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778744748_main_image_7408.png', '1778744748_image1_8983.png', '1778744748_image2_2738.png', '1778744748_image3_2364.png', '1778744748_image4_4009.png', 1, '2026-05-14 07:45:48'),
(47, 14, 79, 'XL4015 Buck', 'SPI Store', 'SIPL', 235.00, 2.00, 282.85, 282.85, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778744816_main_image_3786.png', '1778744816_image1_4281.png', '1778744816_image2_7453.png', '1778744816_image3_8790.png', '1778744816_image4_6289.png', 1, '2026-05-14 07:46:56'),
(48, 14, 80, 'Solar panel mini', 'SPI Store', 'SIPL', 179.00, 2.00, 215.44, 215.44, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778744879_main_image_9447.png', '1778744879_image1_7090.png', '1778744879_image2_4175.png', '1778744879_image3_4957.png', '1778744879_image4_5353.png', 1, '2026-05-14 07:47:59'),
(49, 20, 81, 'TP4056', 'SPI Store', 'SIPL', 219.00, 2.00, 263.59, 263.59, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778744961_main_image_4977.png', '1778744961_image1_5456.png', '1778744961_image2_3445.png', '1778744961_image3_8083.png', '1778744961_image4_8487.png', 1, '2026-05-14 07:49:21'),
(50, 20, 81, 'Li-ion Charger', 'SPI Store', 'SIPL', 249.00, 2.00, 299.70, 299.70, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778745028_main_image_5659.png', '1778745028_image1_2966.png', '1778745028_image2_1576.png', '1778745028_image3_4399.png', '1778745028_image4_2409.png', 1, '2026-05-14 07:50:28'),
(51, 20, 81, 'Lithium Battery holder 4 Slots', 'SPI Store', 'SIPL', 149.00, 2.00, 179.34, 179.34, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778745102_main_image_1317.png', '1778745102_image1_1602.png', '1778745102_image2_9510.png', '1778745102_image3_7466.png', '1778745102_image4_7933.png', 1, '2026-05-14 07:51:42'),
(52, 21, 82, '6V DC Motor', 'SPI Store', 'SIPL', 93.00, 2.00, 111.93, 111.93, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778745259_main_image_3595.png', '1778745259_image1_1133.png', '1778745259_image2_2134.png', '1778745259_image3_1832.png', '1778745259_image4_6149.png', 1, '2026-05-14 07:54:19'),
(54, 21, 82, '12V DC Motor', 'SPI Store', 'SIPL', 149.00, 2.00, 179.34, 179.34, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778745325_main_image_3221.png', '1778745325_image1_7143.png', '1778745325_image2_2174.png', '1778745325_image3_2692.png', '1778745325_image4_8919.png', 1, '2026-05-14 07:55:25'),
(55, 21, 82, '24V DC Motor', 'SPI Store', 'SIPL', 2819.00, 2.00, 3392.95, 3392.95, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778745459_main_image_9645.png', '1778745459_image1_1895.png', '1778745459_image2_1489.png', '1778745459_image3_7610.png', '1778745459_image4_3840.png', 1, '2026-05-14 07:57:39'),
(56, 21, 82, '6V DC Gear Motor', 'SPI Store', 'SIPL', 280.00, 2.00, 337.01, 337.01, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778745543_main_image_2447.png', '1778745543_image1_7110.png', '1778745543_image2_9925.png', '1778745543_image3_7553.png', '1778745543_image4_7286.png', 1, '2026-05-14 07:59:03'),
(57, 21, 82, '12V DC Gear Motor', 'SPI Store', 'SIPL', 492.00, 2.00, 592.17, 592.17, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778745617_main_image_6674.png', '1778745617_image1_3792.png', '1778745617_image2_8319.png', '1778745617_image3_8865.png', '1778745617_image4_7824.png', 1, '2026-05-14 08:00:17'),
(58, 21, 82, '12V DC Gear Motor 100rpm', 'SPI Store', 'SIPL', 492.00, 2.00, 592.17, 592.17, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778745687_main_image_8742.png', '1778745687_image1_6376.png', '1778745687_image2_9079.png', '1778745687_image3_5635.png', '1778745687_image4_6719.png', 1, '2026-05-14 08:01:27'),
(59, 21, 82, 'DC BO Gear Motor', 'SPI Store', 'SIPL', 299.00, 2.00, 359.88, 359.88, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778745749_main_image_9539.png', '1778745749_image1_4634.png', '1778745749_image2_8054.png', '1778745749_image3_3635.png', '1778745749_image4_7497.png', 1, '2026-05-14 08:02:29'),
(60, 21, 82, '24V DC Gear Motor', 'SPI Store', 'SIPL', 1495.00, 2.00, 1799.38, 1799.38, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778745820_main_image_5147.png', '1778745820_image1_5014.png', '1778745820_image2_7500.png', '1778745820_image3_5230.png', '1778745820_image4_2241.png', 1, '2026-05-14 08:03:40'),
(61, 21, 83, 'NEMA17 STEPPER MOTOR', 'SPI Store', 'SIPL', 795.00, 2.00, 956.86, 956.86, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778745893_main_image_8002.png', '1778745893_image1_1837.png', '1778745893_image2_4622.png', '1778745893_image3_9908.png', '1778745893_image4_3154.png', 1, '2026-05-14 08:04:54'),
(62, 21, 83, 'NEMA23 STEPPER  MOTOR', 'SPI Store', 'SIPL', 4799.00, 2.00, 5776.08, 5776.08, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778745954_main_image_4585.png', '1778745954_image1_6679.png', '1778745954_image2_9781.png', '1778745954_image3_1453.png', '1778745954_image4_8687.png', 1, '2026-05-14 08:05:54'),
(63, 21, 84, 'SG90 Micro Servo Motor', 'SPI Store', 'SIPL', 270.00, 2.00, 324.97, 324.97, 0.00, 18.00, 0.00, 4.0, '10', '20+', 100, '1778746019_main_image_9557.png', '1778746019_image1_8989.png', '1778746019_image2_7180.png', '1778746019_image3_3090.png', '1778746019_image4_9665.png', 1, '2026-05-14 08:06:59'),
(64, 21, 84, 'MG995 Servo Motor', 'SPI Store', 'SIPL', 620.00, 2.00, 746.23, 746.23, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778746099_main_image_8539.png', '1778746099_image1_6162.png', '1778746099_image2_8279.png', '1778746099_image3_5165.png', '1778746099_image4_9036.png', 1, '2026-05-14 08:08:19'),
(65, 15, 85, 'Linear Actuators', 'SPI Store', 'SIPL', 4905.00, 2.00, 5903.66, 5903.66, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778746180_main_image_4401.png', '1778746180_image1_5026.png', '1778746180_image2_1860.png', '1778746180_image3_8525.png', '1778746180_image4_8937.png', 1, '2026-05-14 08:08:46'),
(66, 15, 86, 'Solenoid Valves', 'SPI Store', 'SIPL', 190.00, 2.00, 228.68, 228.68, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778746290_main_image_4267.png', '1778746290_image1_8811.png', '1778746290_image2_7952.png', '1778746290_image3_8916.png', '1778746290_image4_9702.png', 1, '2026-05-14 08:10:31'),
(67, 21, 87, 'L298N Motor Driver', 'SPI Store', 'SIPL', 189.00, 2.00, 227.48, 227.48, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778746377_main_image_7476.png', '1778746377_image1_5354.png', '1778746377_image2_5175.png', '1778746377_image3_3783.png', '1778746377_image4_8801.png', 1, '2026-05-14 08:12:57'),
(68, 21, 87, 'DRV8825 Stepper Motor Driver', 'SPI Store', 'SIPL', 161.00, 2.00, 193.78, 193.78, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778746451_main_image_7944.png', '1778746451_image1_6545.png', '1778746451_image2_2201.png', '1778746451_image3_4518.png', '1778746451_image4_4657.png', 1, '2026-05-14 08:14:11'),
(69, 22, 88, 'WS2812', 'SPI Store', 'SIPL', 1348.00, 2.00, 1622.45, 1622.45, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778746573_main_image_9691.png', '1778746573_image1_8560.png', '1778746573_image2_2052.png', '1778746573_image3_8034.png', '1778746573_image4_9725.png', 1, '2026-05-14 08:16:13'),
(70, 22, 88, '16x2 LCD', 'SPI Store', 'SIPL', 219.00, 2.00, 263.59, 263.59, 0.00, 18.00, 10.00, 4.0, '10', '20+ bought in past month', 100, '1778746682_main_image_6321.png', '1778746682_image1_7747.png', '1778746682_image2_2985.png', '1778746682_image3_9531.png', '1778746682_image4_8478.png', 1, '2026-05-14 08:18:02'),
(71, 22, 88, '20x4LCD', 'SPI Store', 'SIPL', 259.00, 2.00, 311.73, 311.73, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778746752_main_image_2681.png', '1778746752_image1_2921.png', '1778746752_image2_1034.png', '1778746752_image3_3860.png', '1778746752_image4_5396.png', 1, '2026-05-14 08:19:12'),
(72, 22, 88, '0.96 OLED LCD', 'SPI Store', 'SIPL', 229.00, 2.00, 275.62, 275.62, 0.00, 18.00, 0.00, 4.0, '10', '20+ bought in past month', 100, '1778746824_main_image_7436.png', '1778746824_image1_4423.png', '1778746824_image2_9295.png', '1778746824_image3_7056.png', '1778746824_image4_5431.png', 1, '2026-05-14 08:19:33'),
(73, 16, 89, 'TFT', 'SPI Store', 'SIPL', 595.00, 2.00, 716.14, 716.14, 0.00, 18.00, 0.00, 5.0, '10', '20+ bought in past month', 100, '1778746891_main_image_5782.png', '1778746891_image1_6949.png', '1778746891_image2_5606.png', '1778746891_image3_6231.png', '1778746891_image4_9343.png', 1, '2026-05-14 08:21:31'),
(74, 16, 90, '7-Segment Displays', 'SPI Store', 'SIPL', 119.00, 2.00, 143.23, 143.23, 0.00, 18.00, 0.00, 4.5, '2', '20+ bought in past month', 99, '1778746949_main_image_6597.png', '1778746949_image1_5292.png', '1778746949_image2_2748.png', '1778746949_image3_5896.png', '1778746949_image4_3440.png', 1, '2026-05-14 08:22:29');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `customer_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` tinyint NOT NULL DEFAULT '5',
  `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `user_id`, `customer_name`, `rating`, `review`, `status`, `created_at`) VALUES
(1, 74, 10, 'Aditya pratap Vishwakarma', 5, 'This is Very Good', 1, '2026-05-14 08:30:29'),
(3, 74, 11, 'adityapratap', 4, 'good ✔', 1, '2026-05-14 09:05:52');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_type` enum('mini','major') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mini',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `project_type`, `description`, `image`, `status`, `created_at`) VALUES
(1, 'Smart Dustbin with Capacity Monitor', 'mini', 'The Smart Dustbin with Capacity Monitor is an innovative waste management system designed to improve cleanliness, efficiency, and hygiene in homes, offices, schools, and public places. This smart dustbin uses sensors to monitor the garbage level inside the bin and provides real-time information about its capacity status.\r\n\r\nThe system automatically detects whether the dustbin is empty, partially filled, or full. Once the dustbin reaches its maximum capacity, it can send alerts or notifications to the concerned authority or display a warning indication, helping ensure timely waste collection and preventing overflow.\r\n\r\nThe project is built using sensors, a microcontroller, and a monitoring system that enhances waste management processes while reducing manual checking efforts. It promotes a cleaner environment, better resource management, and supports smart city initiatives.', '1778736427_smart_Dustbin.jpg', 1, '2026-05-14 05:27:07'),
(2, 'Floor Cleaner Robot', 'mini', 'The Floor Cleaner Robot is an automated smart cleaning system designed to clean floor surfaces efficiently with minimal human effort. This robot helps maintain cleanliness in homes, offices, hospitals, and commercial spaces by automatically moving across the floor and removing dust, dirt, and small waste particles.\r\n\r\nThe robot is equipped with motors, cleaning brushes, and sensors that allow it to navigate different areas while avoiding obstacles. It can perform dry or wet cleaning operations depending on the design and functionality. The automated cleaning mechanism saves time, reduces manual labor, and improves cleaning efficiency.\r\n\r\nUsing embedded systems and smart control technology, the Floor Cleaner Robot provides an intelligent and cost-effective solution for modern cleaning needs. It is especially useful for maintaining hygiene in large indoor environments.', '1778736521_Floor_Cleaner.jpg', 1, '2026-05-14 05:28:41'),
(3, 'Hand Gesture Home Automation With IOT', 'mini', 'The Hand Gesture Home Automation with IoT project is an advanced smart home system that enables users to control electrical appliances through simple hand gestures without physically touching switches. The system uses gesture recognition technology along with IoT connectivity to provide a modern, contactless, and intelligent home automation experience.\r\n\r\nIn this project, sensors detect hand movements and send signals to a microcontroller, which controls devices such as lights, fans, and other household appliances. The IoT integration allows users to monitor and control appliances remotely using a smartphone or internet-based application from anywhere.\r\n\r\nThis system improves convenience, enhances energy efficiency, and supports smart living by reducing manual effort. It is especially beneficial for elderly and physically challenged individuals by offering an easy and touch-free method of controlling home appliances.', '1778736624_HandGesture.jpg', 1, '2026-05-14 05:30:24'),
(4, 'Smart Gloves for Speechless People', 'mini', 'The Smart Gloves for Speechless People project is an assistive communication system designed to help speech-impaired individuals communicate more effectively. The glove is equipped with sensors that detect hand and finger movements and convert them into understandable text or voice output.\r\n\r\nWhen the user performs specific hand gestures or sign language movements, the sensors capture the motion and send the data to a microcontroller. The system then processes the gesture and displays the corresponding message on an LCD screen or converts it into speech using a speaker module.\r\n\r\nThis project provides an easy, portable, and user-friendly communication solution for people who face difficulties in verbal communication. It helps bridge the communication gap between speechless individuals and others, improving accessibility and social interaction.', '1778736709_SmartGloves.jpg', 1, '2026-05-14 05:31:49'),
(5, '5 Smart parking system', 'mini', 'The Smart Parking System is an intelligent vehicle parking management solution designed to reduce parking difficulties and improve space utilization in parking areas. This system uses sensors and automated monitoring technology to detect available parking slots and provide real-time parking information to users.\r\n\r\nThe system automatically monitors vehicle entry, exit, and parking slot occupancy. When a parking space becomes available, the information is displayed through an LCD display, mobile application, or IoT dashboard, helping drivers quickly find vacant slots and reducing traffic congestion inside parking areas.\r\n\r\nThis project enhances parking efficiency, saves time and fuel, and supports smart city infrastructure by automating traditional parking management processes.', '1778736823_carparking.jpg', 1, '2026-05-14 05:33:43'),
(6, 'Face Following Robot', 'mini', 'The Face Following Robot is an intelligent robotic system designed to detect and follow a human face automatically using computer vision and sensor technologies. The robot uses a camera module and image processing techniques to recognize a person’s face and continuously track their movement in real time.\r\n\r\nWhen the camera detects a face, the robot processes the image data and moves in the direction of the detected person. The system is capable of maintaining a certain distance while following the target smoothly and accurately. This project combines robotics, artificial intelligence, and automation to create a smart interactive system.\r\n\r\nThe Face Following Robot can be used in security systems, personal assistance, smart surveillance, and human-following applications, making it a useful and innovative robotic solution.', '1778736880_facefollowing.jpg', 1, '2026-05-14 05:34:40'),
(7, 'Smart Whiteboard Cleaner with Period Notification', 'mini', 'The Smart Whiteboard Cleaner with Period Notification is an automated classroom assistance system designed to clean whiteboards efficiently while also providing class period alerts and notifications. This smart system helps maintain a clean writing surface and supports better classroom management in schools, colleges, and training institutes.\r\n\r\nThe project uses a motorized cleaning mechanism to automatically erase the whiteboard after use or at scheduled intervals. In addition, the system includes a period notification feature that alerts teachers and students about class timings using a buzzer, display, or digital notification system.\r\n\r\nThis project reduces manual effort, saves time, and improves classroom hygiene and organization by combining automation with smart scheduling technology.', '1778736927_Smart_Whiteboard_Cleaner_with_Period_Notification_.jpg', 1, '2026-05-14 05:35:27'),
(8, 'Smart Scarecrow with IoT', 'mini', 'The Smart Scarecrow with IoT is an intelligent agricultural protection system designed to safeguard crops from birds and animals using automation and internet-based monitoring technology. Unlike traditional scarecrows, this smart system uses sensors and IoT technology to detect movement in farming areas and automatically activate deterrent mechanisms such as sound, lights, or alarms.\r\n\r\nThe system continuously monitors the field and sends real-time updates or alerts to farmers through a mobile application or IoT platform. This helps farmers monitor crop safety remotely and reduces crop damage caused by animals and birds.\r\n\r\nThe Smart Scarecrow improves farming efficiency, minimizes manual supervision, and supports modern smart agriculture practices by integrating automation and remote monitoring capabilities.', '1778736968_smart_scarecrow.jpg', 1, '2026-05-14 05:36:08'),
(9, 'Smart shoes for girls safety', 'mini', 'The Smart Shoes for Girls Safety project is a wearable safety device designed to enhance personal security for women and girls during emergencies. These smart shoes are equipped with sensors, GPS tracking, and communication modules that help send alerts and location information to family members, guardians, or emergency contacts when the user is in danger.\r\n\r\nThe system can be activated through a hidden switch, pressure sensor, or emergency trigger integrated into the shoes. Once activated, the device sends the user’s live location and emergency alert message through GSM or IoT technology. Some versions may also include buzzer alarms or shock mechanisms for additional protection.\r\n\r\nThis project aims to provide a reliable, portable, and easy-to-use safety solution that improves confidence and personal security in public places.', '1778737035_smartshoes.png', 1, '2026-05-14 05:37:15'),
(10, 'Multifunctional robot with AI', 'mini', 'The Multifunctional Robot with AI is an advanced intelligent robotic system designed to perform multiple tasks using artificial intelligence and automation technologies. This robot is capable of interacting with users, responding to commands, navigating environments, and performing various operations such as object detection, obstacle avoidance, voice interaction, and smart assistance.\r\n\r\nThe system uses AI algorithms, sensors, cameras, and microcontrollers to analyze its surroundings and make intelligent decisions in real time. Depending on the project design, the robot can be controlled through voice commands, mobile applications, or autonomous programming.\r\n\r\nThis project demonstrates the integration of robotics, machine learning, and IoT technologies to create a smart and versatile robotic assistant for modern applications.', '1778737102_multifunrobo.jpg', 1, '2026-05-14 05:38:22');

-- --------------------------------------------------------

--
-- Table structure for table `review_reactions`
--

DROP TABLE IF EXISTS `review_reactions`;
CREATE TABLE IF NOT EXISTS `review_reactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `reaction` enum('like','dislike') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_reactions`
--

INSERT INTO `review_reactions` (`id`, `review_id`, `user_id`, `reaction`, `created_at`) VALUES
(1, 3, 11, 'like', '2026-05-14 09:49:02'),
(2, 1, 11, 'like', '2026-05-14 09:49:05');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` json NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `permissions`, `status`, `created_at`) VALUES
(1, 'Super Admin', '[\"products\", \"orders\", \"customers\", \"payments\"]', 'active', '2026-01-12 13:26:39'),
(2, 'Product Manager', '[\"products\", \"orders\", \"customers\"]', 'active', '2026-01-12 13:27:03'),
(3, 'Admin', '[\"reports\", \"categories\", \"coupons\"]', 'active', '2026-05-06 09:15:31');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_charges`
--

DROP TABLE IF EXISTS `shipping_charges`;
CREATE TABLE IF NOT EXISTS `shipping_charges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `zone_id` int DEFAULT NULL,
  `min_weight` decimal(10,2) DEFAULT NULL,
  `max_weight` decimal(10,2) DEFAULT NULL,
  `charge` decimal(10,2) DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_zones`
--

DROP TABLE IF EXISTS `shipping_zones`;
CREATE TABLE IF NOT EXISTS `shipping_zones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `zone_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `states` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_history`
--

DROP TABLE IF EXISTS `stock_history`;
CREATE TABLE IF NOT EXISTS `stock_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `old_stock` int DEFAULT NULL,
  `new_stock` int DEFAULT NULL,
  `changed_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_history`
--

INSERT INTO `stock_history` (`id`, `product_id`, `old_stock`, `new_stock`, `changed_by`, `created_at`) VALUES
(1, 20, 450, 0, 'Admin', '2026-01-12 11:58:26'),
(2, 20, 0, 2, 'Admin', '2026-01-12 11:58:47'),
(3, 18, 900, 0, 'Admin', '2026-01-12 11:59:11'),
(4, 22, 0, 1, 'Admin', '2026-01-12 13:16:43'),
(5, 22, 1, 10000, 'Admin', '2026-01-12 13:17:03'),
(6, 20, 2, 0, 'Admin', '2026-01-12 13:18:03'),
(7, 22, 10000, 100, 'Admin', '2026-01-12 17:14:11'),
(8, 100, 100, 10, 'Admin', '2026-02-08 13:18:17'),
(9, 102, 97, 97, 'Admin', '2026-05-06 10:43:42'),
(10, 103, 10, 9, 'Admin', '2026-05-06 15:23:21'),
(11, 103, 9, 2, 'Admin', '2026-05-06 15:23:30'),
(12, 103, 2, 10, 'Admin', '2026-05-06 15:23:46');

-- --------------------------------------------------------

--
-- Table structure for table `stock_logs`
--

DROP TABLE IF EXISTS `stock_logs`;
CREATE TABLE IF NOT EXISTS `stock_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `variant_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `type` enum('IN','OUT') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

DROP TABLE IF EXISTS `sub_categories`;
CREATE TABLE IF NOT EXISTS `sub_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `status`, `created_at`) VALUES
(10, 6, 'Actuators', 1, '2026-01-15 07:05:35'),
(9, 5, '433MHz', 1, '2026-01-15 07:04:49'),
(11, 6, 'Motors', 1, '2026-01-15 07:05:46'),
(12, 10, 'Battery', 1, '2026-01-15 07:07:17'),
(13, 9, 'Major', 1, '2026-01-15 07:07:33'),
(14, 9, 'Minor', 1, '2026-01-15 07:07:41'),
(15, 8, 'Passive Components', 1, '2026-01-15 07:08:01'),
(16, 7, 'Elx Tool', 1, '2026-01-15 07:08:15'),
(17, 5, 'CC2530', 1, '2026-01-16 05:19:03'),
(18, 5, 'HC-05', 1, '2026-01-16 05:19:17'),
(19, 5, 'HC-06', 1, '2026-01-16 05:19:26'),
(20, 5, 'NEO-6M', 1, '2026-01-16 05:19:51'),
(21, 5, 'NRF24L01', 1, '2026-01-16 05:20:08'),
(22, 5, 'SIM800L', 1, '2026-01-16 05:20:28'),
(23, 5, 'SIM900A', 1, '2026-01-16 05:20:40'),
(24, 5, 'SX1278', 1, '2026-01-16 05:20:58'),
(25, 5, 'XBee', 1, '2026-01-16 05:21:09'),
(26, 11, '0.96 OLED LCD', 1, '2026-01-16 05:24:40'),
(27, 11, '7-Segment Displays ', 1, '2026-01-16 05:25:03'),
(28, 11, '16x2 LCD ', 1, '2026-01-16 05:25:57'),
(29, 11, '20x4 LCD', 1, '2026-01-16 05:26:13'),
(30, 11, 'TFT Display  ', 1, '2026-01-16 05:26:27'),
(31, 11, 'WS2812 RGB LEDS', 1, '2026-01-16 05:26:38'),
(32, 17, 'Arduino Boards', 1, '2026-01-16 05:38:55'),
(35, 17, 'ESP8266 & ESP32', 1, '2026-01-16 05:40:03'),
(39, 6, '6V DC Gear Motor ', 1, '2026-01-16 05:51:28'),
(40, 6, '6V DC Motor ', 1, '2026-01-16 05:51:44'),
(41, 6, '12V DC Gear Motor ', 1, '2026-01-16 05:52:02'),
(42, 6, '12V DC Gear Motor 100rpm ', 1, '2026-01-16 05:52:18'),
(43, 6, '12V DC Motor ', 1, '2026-01-16 05:52:40'),
(44, 6, '24V DC Gear Motor ', 1, '2026-01-16 05:52:56'),
(45, 6, '24V DC Motor ', 1, '2026-01-16 05:53:10'),
(46, 6, 'DC BO Motor', 1, '2026-01-16 05:53:29'),
(47, 6, 'DRV8825 Stepper Motor Driver ', 1, '2026-01-16 05:53:53'),
(48, 6, 'L298N Motor Driver', 1, '2026-01-16 05:54:33'),
(49, 6, 'Linear Actuators ', 1, '2026-01-16 05:54:51'),
(50, 6, 'MG995 Servo Motor ', 1, '2026-01-16 05:55:04'),
(51, 6, 'NEMA17 Stepper motor ', 1, '2026-01-16 05:55:16'),
(52, 6, 'NEMA23 STEPPER MOTOR ', 1, '2026-01-16 05:55:29'),
(53, 6, 'SG90 Micro Servo Motor', 1, '2026-01-16 05:55:42'),
(54, 6, 'Solenoid_Valves', 1, '2026-01-16 05:55:58'),
(58, 17, 'Raspberry Pi', 1, '2026-02-02 06:46:37'),
(59, 17, 'STM32 Boards', 1, '2026-02-02 06:46:56'),
(60, 4, 'PIC Microcontrollers', 1, '2026-02-02 06:47:32'),
(61, 4, 'ATtiny85', 1, '2026-02-02 06:47:50'),
(62, 17, 'BeagleBone Black', 1, '2026-02-02 06:48:08'),
(63, 12, 'Temperature Sensors', 1, '2026-02-02 06:48:50'),
(64, 12, 'Motion Sensors', 1, '2026-02-02 06:49:15'),
(65, 12, 'Gas Sensors', 1, '2026-02-02 06:49:42'),
(66, 12, 'Proximity Sensors', 1, '2026-02-02 06:49:58'),
(67, 12, 'Accelerometer Sensors', 1, '2026-02-02 06:50:15'),
(68, 12, 'Sound Sensors', 1, '2026-02-02 06:50:43'),
(69, 12, 'Fingerprint Scanner', 1, '2026-02-02 06:51:39'),
(70, 19, 'RF Modules', 1, '2026-02-02 06:52:24'),
(71, 19, 'Bluetooth Modules', 1, '2026-02-02 06:52:50'),
(72, 19, 'GSM/GPRS Modules', 1, '2026-02-02 06:53:10'),
(73, 19, 'LoRa Modules', 1, '2026-02-02 06:53:33'),
(74, 19, 'Zigbee Modules', 1, '2026-02-02 06:53:54'),
(75, 19, 'GPS Modules', 1, '2026-02-02 06:54:23'),
(76, 20, 'Lithium-Ion Batteries', 1, '2026-02-02 06:55:47'),
(77, 20, 'Battery Management Systems', 1, '2026-02-02 06:56:07'),
(78, 14, 'Voltage Regulators', 1, '2026-02-02 06:56:41'),
(79, 14, 'DC-DC Converters', 1, '2026-02-02 06:57:08'),
(80, 14, 'Solar Panels', 1, '2026-02-02 06:57:32'),
(81, 20, 'Battery Chargers', 1, '2026-02-02 06:57:58'),
(82, 21, 'DC Gear Motors', 1, '2026-02-02 06:59:29'),
(83, 21, 'Stepper Motors', 1, '2026-02-02 06:59:51'),
(84, 21, 'Servo Motors', 1, '2026-02-02 07:00:27'),
(85, 15, 'Linear Actuators', 1, '2026-02-02 07:00:45'),
(86, 15, 'Solenoid Valves', 1, '2026-02-02 07:01:07'),
(87, 21, 'Motor Drivers', 1, '2026-02-02 07:01:20'),
(88, 22, 'LED Indicators', 1, '2026-02-02 07:02:15'),
(89, 16, 'Touchscreen Displays', 1, '2026-02-02 07:02:33'),
(90, 16, '7-Segment Displays', 1, '2026-02-02 07:02:51');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_products`
--

DROP TABLE IF EXISTS `supplier_products`;
CREATE TABLE IF NOT EXISTS `supplier_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `product_id` int NOT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_products`
--

INSERT INTO `supplier_products` (`id`, `supplier_id`, `product_id`, `cost_price`, `created_at`) VALUES
(1, 1, 20, 2000.00, '2026-01-12 08:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_purchases`
--

DROP TABLE IF EXISTS `supplier_purchases`;
CREATE TABLE IF NOT EXISTS `supplier_purchases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `invoice_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `technology_companies`
--

DROP TABLE IF EXISTS `technology_companies`;
CREATE TABLE IF NOT EXISTS `technology_companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `technology_id` int NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `technology_features`
--

DROP TABLE IF EXISTS `technology_features`;
CREATE TABLE IF NOT EXISTS `technology_features` (
  `id` int NOT NULL AUTO_INCREMENT,
  `technology_id` int NOT NULL,
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `technology_modules`
--

DROP TABLE IF EXISTS `technology_modules`;
CREATE TABLE IF NOT EXISTS `technology_modules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `phase_id` int NOT NULL,
  `module_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=186 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `technology_modules`
--

INSERT INTO `technology_modules` (`id`, `phase_id`, `module_title`, `sort_order`, `status`) VALUES
(1, 1, 'Day 1', 1, 1),
(2, 2, 'Day 1 – Program Orientation & Embedded Overview', 1, 1),
(3, 2, 'Day 2 – Personality Development (PD-I)', 2, 1),
(4, 3, 'Day 1 – Program Orientation & Embedded Overview', 1, 1),
(5, 3, 'Day 2 – Personality Development (PD-I)', 2, 1),
(6, 3, 'Day 3: Basic Computer Fundamentals', 3, 1),
(7, 3, 'Day 4: Software & Embedded Tools Introduction', 4, 1),
(8, 3, 'Day 5: PD-II & Technical Skills\",\"topics', 5, 1),
(9, 4, 'Day 6: Electronics Fundamentals', 1, 1),
(10, 5, 'Day 1 – Program Orientation & Embedded Overview', 1, 1),
(11, 5, 'Day 2 – Personality Development (PD-I)', 2, 1),
(12, 5, 'Day 3: Basic Computer Fundamentals', 3, 1),
(13, 5, 'Day 4: Software & Embedded Tools Introduction', 4, 1),
(14, 5, 'Day 5: PD-II & Technical Skills\",\"topics', 5, 1),
(15, 6, 'Day 6: Electronics Fundamentals', 1, 1),
(16, 7, 'Day 1 – Program Orientation & Embedded Overview', 1, 1),
(17, 7, 'Day 2 – Personality Development (PD-I)', 2, 1),
(18, 7, 'Day 3: Basic Computer Fundamentals', 3, 1),
(19, 7, 'Day 4: Software & Embedded Tools Introduction', 4, 1),
(20, 7, 'Day 5: PD-II & Technical Skills\",\"topics', 5, 1),
(21, 8, 'Day 6: Electronics Fundamentals', 1, 1),
(22, 9, 'Day 1 – Program Orientation & Embedded Overview', 1, 1),
(23, 9, 'Day 2 – Personality Development (PD-I)', 2, 1),
(24, 9, 'Day 3: Basic Computer Fundamentals', 3, 1),
(25, 9, 'Day 4: Software & Embedded Tools Introduction', 4, 1),
(26, 9, 'Day 5: PD-II & Technical Skills\",\"topics', 5, 1),
(27, 10, 'Day 6: Electronics Fundamentals', 1, 1),
(28, 10, 'Day 7: Digital Electronics Basics', 2, 1),
(29, 10, 'Day 8: Embedded System Architecture', 3, 1),
(30, 10, 'Day 9: Microcontroller Fundamentals', 4, 1),
(31, 11, 'Day 1 – Program Orientation & Embedded Overview', 1, 1),
(32, 11, 'Day 2 – Personality Development (PD-I)', 2, 1),
(33, 11, 'Day 3: Basic Computer Fundamentals', 3, 1),
(34, 11, 'Day 4: Software & Embedded Tools Introduction', 4, 1),
(35, 11, 'Day 5: PD-II & Technical Skills\",\"topics', 5, 1),
(36, 12, 'Day 6: Electronics Fundamentals', 1, 1),
(37, 12, 'Day 7: Digital Electronics Basics', 2, 1),
(38, 12, 'Day 8: Embedded System Architecture', 3, 1),
(39, 12, 'Day 9: Microcontroller Fundamentals', 4, 1),
(40, 12, 'Day 10: Embedded Development Cycle', 5, 1),
(56, 16, 'Day 6 – Electronics Fundamentals', 1, 1),
(55, 15, 'Day 5 – PD-II & Technical Skills', 5, 1),
(54, 15, 'Day 4 – Software & Embedded Tools Introduction', 4, 1),
(53, 15, 'Day 3 – Basic Computer Fundamentals', 3, 1),
(52, 15, 'Day 2 – Personality Development (PD-I)', 2, 1),
(51, 15, 'Day 1 – Program Orientation & Embedded Overview', 1, 1),
(57, 16, 'Day 7 – Digital Electronics Basics', 2, 1),
(58, 16, 'Day 8 – Embedded System Architecture', 3, 1),
(59, 16, 'Day 9 – Microcontroller Fundamentals', 4, 1),
(60, 16, 'Day 10 – Embedded Development Cycle', 5, 1),
(61, 17, 'Day 11 – Introduction to C for Embedded', 1, 1),
(62, 17, 'Day 12 – Operators & Control Statements', 2, 1),
(63, 17, 'Day 13 – Functions & Arrays', 3, 1),
(64, 17, 'Day 14 – Pointers & Memory', 4, 1),
(65, 17, 'Day 15 – Bitwise Operations', 5, 1),
(66, 17, 'Day 16 – Embedded C Optimization', 6, 1),
(67, 17, 'Day 17 – Mini Project-1 (C Based)', 7, 1),
(68, 18, 'Day 18 – Embedded Simulation Tools', 1, 1),
(69, 18, 'Day 19 – Microcontroller Simulation', 2, 1),
(70, 18, 'Day 20 – Circuit Design & Testing', 3, 1),
(71, 18, 'Day 21 – Debugging Using Simulator', 4, 1),
(72, 18, 'Day 22 – Mini Project-2 (Simulation Based)', 5, 1),
(73, 19, 'Day 23 – Arduino Hardware Architecture', 1, 1),
(74, 19, 'Day 24 – Arduino Embedded Programming', 2, 1),
(75, 19, 'Day 25 – LED & Buzzer Interfacing', 3, 1),
(76, 19, 'Day 26 – Switch & Relay Interfacing', 4, 1),
(77, 19, 'Day 27 – Motor Interfacing', 5, 1),
(78, 19, 'Day 28 – Servo Motor Control', 6, 1),
(79, 19, 'Day 29 – Sensor Interfacing (Basic)', 7, 1),
(80, 19, 'Day 30 – Mini Project-3 (Arduino Based)', 8, 1),
(81, 20, 'Day 31 – LCD & Display Interfacing', 1, 1),
(82, 20, 'Day 32 – Keypad & Input Devices', 2, 1),
(83, 20, 'Day 33 – Timers & Delays', 3, 1),
(84, 20, 'Day 34 – Interrupts', 4, 1),
(85, 20, 'Day 35 – Mini Project-4', 5, 1),
(86, 21, 'Day 36 – Introduction to ARM Architecture', 1, 1),
(87, 21, 'Day 37 – STM32 Hardware Architecture', 2, 1),
(88, 21, 'Day 38 – STM32 Programming Environment', 3, 1),
(89, 21, 'Day 39 – GPIO & Peripheral Programming', 4, 1),
(90, 21, 'Day 40 – Mini Project-5 (STM32 Based)', 5, 1),
(91, 22, 'Day 41 – Major Project Planning', 1, 1),
(92, 22, 'Day 42 – Major Project Implementation-I', 2, 1),
(93, 22, 'Day 43 – Major Project Implementation-II', 3, 1),
(94, 22, 'Day 44 – Project Documentation & Presentation', 4, 1),
(95, 22, 'Day 45 – Final Demonstration & Evaluation', 5, 1),
(96, 23, 'Day 1 – Program Orientation & IoT Fundamentals', 1, 1),
(97, 23, 'Day 2 – Personality Development (PD-I)', 2, 1),
(98, 23, 'Day 3 – Basic Computer Fundamentals', 3, 1),
(99, 23, 'Day 4 – Programming Logic & Flow', 4, 1),
(100, 23, 'Day 5 – PD-II & Technical Mindset', 5, 1),
(101, 24, 'Day 6 – Electronics Basics for IoT', 1, 1),
(102, 24, 'Day 7 – Sensors & Actuators Overview', 2, 1),
(103, 24, 'Day 8 – Digital & Analog Sensors', 3, 1),
(104, 24, 'Day 9 – Environmental & Motion Sensors', 4, 1),
(105, 24, 'Day 10 – Practical Task Day', 5, 1),
(106, 25, 'Day 11 – Introduction to MicroPython', 1, 1),
(107, 25, 'Day 12 – Python Basics for IoT', 2, 1),
(108, 25, 'Day 13 – Loops & Functions', 3, 1),
(109, 25, 'Day 14 – File Handling & Libraries', 4, 1),
(110, 25, 'Day 15 – Programming Task & Practice', 5, 1),
(111, 26, 'Day 16 – ESP8266 Hardware Overview', 1, 1),
(112, 26, 'Day 17 – ESP8266 MicroPython Setup', 2, 1),
(113, 26, 'Day 18 – GPIO Programming', 3, 1),
(114, 26, 'Day 19 – Sensor Interfacing', 4, 1),
(115, 26, 'Day 20 – Wi-Fi Programming', 5, 1),
(116, 26, 'Day 21 – Web Server & Data Display', 6, 1),
(117, 26, 'Day 22 – Mini Project-1 (ESP8266)', 7, 1),
(118, 27, 'Day 23 – ESP32 Hardware Overview', 1, 1),
(119, 27, 'Day 24 – ESP32 MicroPython Setup', 2, 1),
(120, 27, 'Day 25 – Digital & PWM Control', 3, 1),
(121, 27, 'Day 26 – Advanced ESP32 Features', 4, 1),
(122, 27, 'Day 27 – Multi-Sensor Integration', 5, 1),
(123, 27, 'Day 28 – Wi-Fi & Bluetooth Programming', 6, 1),
(124, 27, 'Day 29 – Mini Project-2 (ESP32)', 7, 1),
(125, 27, 'Day 30 – Optimization & Review', 8, 1),
(126, 28, 'Day 31 – IoT Cloud Fundamentals', 1, 1),
(127, 28, 'Day 32 – Cloud Data Upload', 2, 1),
(128, 28, 'Day 33 – Dashboard Design', 3, 1),
(129, 28, 'Day 34 – Remote Monitoring & Control', 4, 1),
(130, 28, 'Day 35 – Mini Project-3 (Cloud Based)', 5, 1),
(131, 29, 'Day 36 – Raspberry Pi Hardware Overview', 1, 1),
(132, 29, 'Day 37 – MicroPython & Python on Raspberry Pi', 2, 1),
(133, 29, 'Day 38 – Sensor & Module Interfacing', 3, 1),
(134, 29, 'Day 39 – Industrial IoT (IIoT) Concepts', 4, 1),
(135, 29, 'Day 40 – Mini Project-4 (IIoT Based)', 5, 1),
(136, 30, 'Day 41 – Major Project Planning', 1, 1),
(137, 30, 'Day 42 – Major Project Implementation-I', 2, 1),
(138, 30, 'Day 43 – Major Project Implementation-II', 3, 1),
(139, 30, 'Day 44 – Documentation & Presentation', 4, 1),
(140, 30, 'Day 45 – Final Demonstration & Certification', 5, 1),
(141, 31, 'Day 1 – Program Orientation & Robotics Overview', 1, 1),
(142, 31, 'Day 2 – Personality Development (PD-I)', 2, 1),
(143, 31, 'Day 3 – Basic Computer Fundamentals', 3, 1),
(144, 31, 'Day 4 – Programming Logic & Flow', 4, 1),
(145, 31, 'Day 5 – PD-II & Engineering Mindset', 5, 1),
(146, 32, 'Day 6 – Introduction to Robotics Systems', 1, 1),
(147, 32, 'Day 7 – Types of Robots', 2, 1),
(148, 32, 'Day 8 – Coordinate Systems & Motion Basics', 3, 1),
(149, 32, 'Day 9 – Robot Components Overview', 4, 1),
(150, 32, 'Day 10 – Task & Practice Day', 5, 1),
(151, 33, 'Day 11 – Python Introduction', 1, 1),
(152, 33, 'Day 12 – Control Statements', 2, 1),
(153, 33, 'Day 13 – Functions & Modular Coding', 3, 1),
(154, 33, 'Day 14 – Python Libraries for Robotics', 4, 1),
(155, 33, 'Day 15 – File Handling & Debugging', 5, 1),
(156, 33, 'Day 16 – Python Tasks for Robotics', 6, 1),
(157, 33, 'Day 17 – Mini Project-1 (Python Based)', 7, 1),
(158, 34, 'Day 18 – Raspberry Pi Hardware Overview', 1, 1),
(159, 34, 'Day 19 – Linux for Robotics', 2, 1),
(160, 34, 'Day 20 – GPIO Programming with Python', 3, 1),
(161, 34, 'Day 21 – Sensor Interfacing', 4, 1),
(162, 34, 'Day 22 – Mini Project-2 (Pi Based Robot Control)', 5, 1),
(163, 35, 'Day 23 – Motors in Robotics', 1, 1),
(164, 35, 'Day 24 – Motor Control Techniques', 2, 1),
(165, 35, 'Day 25 – Sensors for Robotics', 3, 1),
(166, 35, 'Day 26 – Sensor Fusion Basics', 4, 1),
(167, 35, 'Day 27 – Mini Project-3 (Sensor-Based Robot)', 5, 1),
(168, 36, 'Day 28 – Introduction to ROS', 1, 1),
(169, 36, 'Day 29 – ROS Installation & Setup', 2, 1),
(170, 36, 'Day 30 – ROS Nodes & Topics', 3, 1),
(171, 36, 'Day 31 – ROS Messages & Services', 4, 1),
(172, 36, 'Day 32 – Robot Control with ROS', 5, 1),
(173, 36, 'Day 33 – Mini Project-4 (ROS Based Robot)', 6, 1),
(174, 37, 'Day 34 – Introduction to Gazebo', 1, 1),
(175, 37, 'Day 35 – Robot Modeling in Gazebo', 2, 1),
(176, 37, 'Day 36 – Sensor Simulation', 3, 1),
(177, 37, 'Day 37 – Robot Navigation in Simulation', 4, 1),
(178, 37, 'Day 38 – Mini Project-5 (Simulated Robot)', 5, 1),
(179, 38, 'Day 39 – Introduction to CAD for Robotics', 1, 1),
(180, 38, 'Day 40 – Robot Chassis Design', 2, 1),
(181, 38, 'Day 41 – Design Integration', 3, 1),
(182, 39, 'Day 42 – Major Project Planning', 1, 1),
(183, 39, 'Day 43 – Major Project Implementation-I', 2, 1),
(184, 39, 'Day 44 – Major Project Implementation-II', 3, 1),
(185, 39, 'Day 45 – Final Demonstration & Evaluation', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `technology_phases`
--

DROP TABLE IF EXISTS `technology_phases`;
CREATE TABLE IF NOT EXISTS `technology_phases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `technology_id` int NOT NULL,
  `phase_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `technology_phases`
--

INSERT INTO `technology_phases` (`id`, `technology_id`, `phase_title`, `sort_order`, `status`) VALUES
(16, 6, 'PHASE 2: ELECTRONICS & EMBEDDED FOUNDATIONS (Day 6–10)', 2, 1),
(15, 6, 'PHASE 1: ORIENTATION, PD & COMPUTER BASICS (Day 1–5)', 1, 1),
(17, 6, 'PHASE 3: EMBEDDED C PROGRAMMING (Day 11–17)', 3, 1),
(18, 6, 'PHASE 4: EMBEDDED SIMULATORS & TOOLS (Day 18–22)', 4, 1),
(19, 6, 'PHASE 5: ARDUINO PLATFORM (EMBEDDED USE) (Day 23–30)', 5, 1),
(20, 6, 'PHASE 6: ADVANCED EMBEDDED INTERFACING (Day 31–35)', 6, 1),
(21, 6, 'PHASE 7: ARM & STM32 MICROCONTROLLER (Day 36–40)', 7, 1),
(22, 6, 'PHASE 8: MAJOR PROJECT & PROFESSIONAL SKILLS (Day 41–45)', 8, 1),
(23, 7, 'PHASE 1: ORIENTATION, PD & COMPUTING BASICS (Day 1–5)', 1, 1),
(24, 7, 'PHASE 2: ELECTRONICS & SENSOR FOUNDATION (Day 6–10)', 2, 1),
(25, 7, 'PHASE 3: MICROPYTHON PROGRAMMING BASICS (Day 11–15)', 3, 1),
(26, 7, 'PHASE 4: ESP8266 WITH MICROPYTHON (Day 16–22)', 4, 1),
(27, 7, 'PHASE 5: ESP32 WITH MICROPYTHON (Day 23–30)', 5, 1),
(28, 7, 'PHASE 6: IOT CLOUD PLATFORMS (Day 31–35)', 6, 1),
(29, 7, 'PHASE 7: RASPBERRY PI WITH MICROPYTHON & IIOT (Day 36–40)', 7, 1),
(30, 7, 'PHASE 8: MAJOR PROJECT & PROFESSIONAL SKILLS (Day 41–45)', 8, 1),
(31, 8, 'PHASE 1: ORIENTATION, PD & COMPUTING BASICS (Day 1–5)', 1, 1),
(32, 8, 'PHASE 2: ROBOTICS FUNDAMENTALS (Day 6–10)', 2, 1),
(33, 8, 'PHASE 3: PYTHON FOR ROBOTICS (Day 11–17)', 3, 1),
(34, 8, 'PHASE 4: RASPBERRY PI FOR ROBOTICS (Day 18–22)', 4, 1),
(35, 8, 'PHASE 5: ROBOTICS SENSORS & ACTUATORS (Day 23–27)', 5, 1),
(36, 8, 'PHASE 6: ROBOT OPERATING SYSTEM (ROS) (Day 28–33)', 6, 1),
(37, 8, 'PHASE 7: SIMULATION WITH GAZEBO (Day 34–38)', 7, 1),
(38, 8, 'PHASE 8: CAD & ROBOT DESIGN (Day 39–41)', 8, 1),
(39, 8, 'PHASE 9: MAJOR ROBOTICS PROJECT (Day 42–45)', 9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `technology_topics`
--

DROP TABLE IF EXISTS `technology_topics`;
CREATE TABLE IF NOT EXISTS `technology_topics` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_id` int NOT NULL,
  `topic_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=444 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `technology_topics`
--

INSERT INTO `technology_topics` (`id`, `module_id`, `topic_text`, `sort_order`, `status`) VALUES
(1, 1, 'Training structure & evaluation method', 1, 1),
(2, 1, 'Introduction to Embedded Systems', 2, 1),
(3, 1, 'Real-world embedded applications', 3, 1),
(4, 2, 'Training structure & evaluation method', 1, 1),
(5, 2, 'Introduction to Embedded Systems', 2, 1),
(6, 2, 'Real-world embedded applications', 3, 1),
(7, 3, 'Communication skills for engineers', 1, 1),
(8, 3, 'Technical discipline & lab ethics', 2, 1),
(9, 3, 'Presentation basics', 3, 1),
(10, 4, 'Training structure & evaluation method', 1, 1),
(11, 4, 'Introduction to Embedded Systems', 2, 1),
(12, 4, 'Real-world embedded applications', 3, 1),
(13, 5, 'Communication skills for engineers', 1, 1),
(14, 5, 'Technical discipline & lab ethics', 2, 1),
(15, 5, 'Presentation basics', 3, 1),
(16, 6, 'Computer hardware overview', 1, 1),
(17, 6, 'Operating systems basics', 2, 1),
(18, 6, 'File & folder management', 3, 1),
(19, 7, 'Editors, compilers & IDEs', 1, 1),
(20, 7, 'Embedded workflow overview', 2, 1),
(21, 7, 'Introduction to simulators', 3, 1),
(22, 8, 'Logical thinking', 1, 1),
(23, 8, 'Problem-solving techniques', 2, 1),
(24, 8, 'Task-based learning approach', 3, 1),
(25, 9, 'Voltage, current & resistance', 1, 1),
(26, 9, 'Ohm’s law', 2, 1),
(27, 9, 'Passive components', 3, 1),
(28, 10, 'Training structure & evaluation method', 1, 1),
(29, 10, 'Introduction to Embedded Systems', 2, 1),
(30, 10, 'Real-world embedded applications', 3, 1),
(31, 11, 'Communication skills for engineers', 1, 1),
(32, 11, 'Technical discipline & lab ethics', 2, 1),
(33, 11, 'Presentation basics', 3, 1),
(34, 12, 'Computer hardware overview', 1, 1),
(35, 12, 'Operating systems basics', 2, 1),
(36, 12, 'File & folder management', 3, 1),
(37, 13, 'Editors, compilers & IDEs', 1, 1),
(38, 13, 'Embedded workflow overview', 2, 1),
(39, 13, 'Introduction to simulators', 3, 1),
(40, 14, 'Logical thinking', 1, 1),
(41, 14, 'Problem-solving techniques', 2, 1),
(42, 14, 'Task-based learning approach', 3, 1),
(43, 15, 'Voltage, current & resistance', 1, 1),
(44, 15, 'Ohm’s law', 2, 1),
(45, 15, 'Passive components', 3, 1),
(46, 16, 'Training structure & evaluation method', 1, 1),
(47, 16, 'Introduction to Embedded Systems', 2, 1),
(48, 16, 'Real-world embedded applications', 3, 1),
(49, 17, 'Communication skills for engineers', 1, 1),
(50, 17, 'Technical discipline & lab ethics', 2, 1),
(51, 17, 'Presentation basics', 3, 1),
(52, 18, 'Computer hardware overview', 1, 1),
(53, 18, 'Operating systems basics', 2, 1),
(54, 18, 'File & folder management', 3, 1),
(55, 19, 'Editors, compilers & IDEs', 1, 1),
(56, 19, 'Embedded workflow overview', 2, 1),
(57, 19, 'Introduction to simulators', 3, 1),
(58, 20, 'Logical thinking', 1, 1),
(59, 20, 'Problem-solving techniques', 2, 1),
(60, 20, 'Task-based learning approach', 3, 1),
(61, 21, 'Voltage, current & resistance', 1, 1),
(62, 21, 'Ohm’s law', 2, 1),
(63, 21, 'Passive components', 3, 1),
(64, 22, 'Training structure & evaluation method', 1, 1),
(65, 22, 'Introduction to Embedded Systems', 2, 1),
(66, 22, 'Real-world embedded applications', 3, 1),
(67, 23, 'Communication skills for engineers', 1, 1),
(68, 23, 'Technical discipline & lab ethics', 2, 1),
(69, 23, 'Presentation basics', 3, 1),
(70, 24, 'Computer hardware overview', 1, 1),
(71, 24, 'Operating systems basics', 2, 1),
(72, 24, 'File & folder management', 3, 1),
(73, 25, 'Editors, compilers & IDEs', 1, 1),
(74, 25, 'Embedded workflow overview', 2, 1),
(75, 25, 'Introduction to simulators', 3, 1),
(76, 26, 'Logical thinking', 1, 1),
(77, 26, 'Problem-solving techniques', 2, 1),
(78, 26, 'Task-based learning approach', 3, 1),
(79, 27, 'Voltage, current & resistance', 1, 1),
(80, 27, 'Ohm’s law', 2, 1),
(81, 27, 'Passive components', 3, 1),
(82, 28, 'Number systems', 1, 1),
(83, 28, 'Logic gates', 2, 1),
(84, 28, 'Truth tables', 3, 1),
(85, 29, 'Embedded hardware blocks', 1, 1),
(86, 29, 'Memory types', 2, 1),
(87, 29, 'Clock & reset', 3, 1),
(88, 30, '8051, AVR & ARM overview', 1, 1),
(89, 30, 'Pin functions', 2, 1),
(90, 30, 'Internal peripherals', 3, 1),
(91, 31, 'Training structure & evaluation method', 1, 1),
(92, 31, 'Introduction to Embedded Systems', 2, 1),
(93, 31, 'Real-world embedded applications', 3, 1),
(94, 32, 'Communication skills for engineers', 1, 1),
(95, 32, 'Technical discipline & lab ethics', 2, 1),
(96, 32, 'Presentation basics', 3, 1),
(97, 33, 'Computer hardware overview', 1, 1),
(98, 33, 'Operating systems basics', 2, 1),
(99, 33, 'File & folder management', 3, 1),
(100, 34, 'Editors, compilers & IDEs', 1, 1),
(101, 34, 'Embedded workflow overview', 2, 1),
(102, 34, 'Introduction to simulators', 3, 1),
(103, 35, 'Logical thinking', 1, 1),
(104, 35, 'Problem-solving techniques', 2, 1),
(105, 35, 'Task-based learning approach', 3, 1),
(106, 36, 'Voltage, current & resistance', 1, 1),
(107, 36, 'Ohm’s law', 2, 1),
(108, 36, 'Passive components', 3, 1),
(109, 37, 'Number systems', 1, 1),
(110, 37, 'Logic gates', 2, 1),
(111, 37, 'Truth tables', 3, 1),
(112, 38, 'Embedded hardware blocks', 1, 1),
(113, 38, 'Memory types', 2, 1),
(114, 38, 'Clock & reset', 3, 1),
(115, 39, '8051, AVR & ARM overview', 1, 1),
(116, 39, 'Pin functions', 2, 1),
(117, 39, 'Internal peripherals', 3, 1),
(118, 40, 'Flowchart & algorithm', 1, 1),
(119, 40, 'Embedded coding process', 2, 1),
(120, 40, 'Simple logic design', 3, 1),
(170, 57, 'Logic gates', 2, 1),
(169, 57, 'Number systems', 1, 1),
(168, 56, 'Passive components', 3, 1),
(167, 56, 'Ohm’s law', 2, 1),
(166, 56, 'Voltage, current & resistance', 1, 1),
(165, 55, 'Task-based learning approach', 3, 1),
(164, 55, 'Problem-solving techniques', 2, 1),
(163, 55, 'Logical thinking', 1, 1),
(162, 54, 'Introduction to simulators', 3, 1),
(161, 54, 'Embedded workflow overview', 2, 1),
(160, 54, 'Editors, compilers & IDEs', 1, 1),
(159, 53, 'File & folder management', 3, 1),
(158, 53, 'Operating systems basics', 2, 1),
(157, 53, 'Computer hardware overview', 1, 1),
(156, 52, 'Presentation basics', 3, 1),
(155, 52, 'Technical discipline & lab ethics', 2, 1),
(154, 52, 'Communication skills for engineers', 1, 1),
(153, 51, 'Real-world embedded applications', 3, 1),
(152, 51, 'Introduction to Embedded Systems', 2, 1),
(151, 51, 'Training structure & evaluation method', 1, 1),
(171, 57, 'Truth tables', 3, 1),
(172, 58, 'Embedded hardware blocks', 1, 1),
(173, 58, 'Memory types', 2, 1),
(174, 58, 'Clock & reset', 3, 1),
(175, 59, '8051, AVR & ARM overview', 1, 1),
(176, 59, 'Pin functions', 2, 1),
(177, 59, 'Internal peripherals', 3, 1),
(178, 60, 'Flowchart & algorithm', 1, 1),
(179, 60, 'Embedded coding process', 2, 1),
(180, 60, 'Task-1: Simple logic design', 3, 1),
(181, 61, 'C structure', 1, 1),
(182, 61, 'Data types & variables', 2, 1),
(183, 62, 'Conditional statements', 1, 1),
(184, 62, 'Looping concepts', 2, 1),
(185, 62, 'Task-2: Logic-based programs', 3, 1),
(186, 63, 'Function creation', 1, 1),
(187, 63, 'Array handling', 2, 1),
(188, 64, 'Pointer basics', 1, 1),
(189, 64, 'Memory addressing', 2, 1),
(190, 65, 'Bit masking', 1, 1),
(191, 65, 'Register-level logic', 2, 1),
(192, 66, 'Efficient coding', 1, 1),
(193, 66, 'Memory optimization', 2, 1),
(194, 67, 'Embedded logic mini project', 1, 1),
(195, 67, 'Code testing & review', 2, 1),
(196, 68, 'Proteus software overview', 1, 1),
(197, 68, 'Circuit simulation basics', 2, 1),
(198, 69, 'Simulating microcontrollers', 1, 1),
(199, 69, 'Loading hex files', 2, 1),
(200, 70, 'Schematic design', 1, 1),
(201, 70, 'Error detection', 2, 1),
(202, 71, 'Breakpoints', 1, 1),
(203, 71, 'Real-time variable monitoring', 2, 1),
(204, 72, 'Simulator-based embedded project', 1, 1),
(205, 72, 'Verification & analysis', 2, 1),
(206, 73, 'Microcontroller on Arduino', 1, 1),
(207, 73, 'Pin configuration', 2, 1),
(208, 74, 'Arduino sketch structure', 1, 1),
(209, 74, 'Digital I/O control', 2, 1),
(210, 75, 'Output device control', 1, 1),
(211, 75, 'Task-3: Pattern generation', 2, 1),
(212, 76, 'Input handling', 1, 1),
(213, 76, 'Debouncing logic', 2, 1),
(214, 77, 'DC motor', 1, 1),
(215, 77, 'Motor driver ICs', 2, 1),
(216, 78, 'PWM fundamentals', 1, 1),
(217, 78, 'Servo positioning', 2, 1),
(218, 79, 'IR sensor', 1, 1),
(219, 79, 'Ultrasonic sensor', 2, 1),
(220, 80, 'Sensor-based embedded project', 1, 1),
(221, 80, 'Hardware testing', 2, 1),
(222, 81, '16×2 LCD', 1, 1),
(223, 81, 'Display formatting', 2, 1),
(224, 82, 'Matrix keypad', 1, 1),
(225, 82, 'User input logic', 2, 1),
(226, 83, 'Timer concepts', 1, 1),
(227, 83, 'Accurate delay generation', 2, 1),
(228, 84, 'External interrupts', 1, 1),
(229, 84, 'Priority handling', 2, 1),
(230, 85, 'Interrupt-based embedded project', 1, 1),
(231, 85, 'Performance testing', 2, 1),
(232, 86, 'ARM core basics', 1, 1),
(233, 86, 'STM32 family overview', 2, 1),
(234, 87, 'GPIO registers', 1, 1),
(235, 87, 'Clock system', 2, 1),
(236, 88, 'STM32CubeIDE', 1, 1),
(237, 88, 'Project creation', 2, 1),
(238, 89, 'LED, switch interfacing', 1, 1),
(239, 89, 'Register-level programming', 2, 1),
(240, 90, 'STM32 embedded application', 1, 1),
(241, 90, 'Testing & validation', 2, 1),
(242, 91, 'Project selection', 1, 1),
(243, 91, 'Block diagram & flowchart', 2, 1),
(244, 92, 'Hardware assembly', 1, 1),
(245, 92, 'Embedded coding', 2, 1),
(246, 93, 'Debugging', 1, 1),
(247, 93, 'Optimization', 2, 1),
(248, 94, 'Report preparation', 1, 1),
(249, 94, 'Technical presentation', 2, 1),
(250, 95, 'Major project demo', 1, 1),
(251, 95, 'Assessment & certification', 2, 1),
(252, 96, 'Training structure & evaluation', 1, 1),
(253, 96, 'What is IoT & IIoT', 2, 1),
(254, 96, 'Real-world IoT applications', 3, 1),
(255, 97, 'Communication skills', 1, 1),
(256, 97, 'Technical confidence', 2, 1),
(257, 97, 'Professional lab discipline', 3, 1),
(258, 98, 'Computer hardware & software', 1, 1),
(259, 98, 'Operating system basics', 2, 1),
(260, 98, 'File & folder management', 3, 1),
(261, 99, 'Algorithm basics', 1, 1),
(262, 99, 'Flowcharts', 2, 1),
(263, 99, 'Logical problem solving', 3, 1),
(264, 100, 'Teamwork & leadership', 1, 1),
(265, 100, 'Project-based learning approach', 2, 1),
(266, 100, 'Task-oriented thinking', 3, 1),
(267, 101, 'Voltage, current & resistance', 1, 1),
(268, 101, 'Power supply fundamentals', 2, 1),
(269, 102, 'Types of sensors', 1, 1),
(270, 102, 'Actuators used in IoT systems', 2, 1),
(271, 103, 'IR sensor', 1, 1),
(272, 103, 'LDR & potentiometer', 2, 1),
(273, 104, 'Temperature & humidity sensors', 1, 1),
(274, 104, 'Gas & PIR sensors', 2, 1),
(275, 105, 'Sensor testing tasks', 1, 1),
(276, 105, 'Sensor behavior analysis', 2, 1),
(277, 106, 'What is MicroPython', 1, 1),
(278, 106, 'Advantages in IoT', 2, 1),
(279, 106, 'Supported boards overview', 3, 1),
(280, 107, 'Variables & data types', 1, 1),
(281, 107, 'Conditional statements', 2, 1),
(282, 108, 'for & while loops', 1, 1),
(283, 108, 'Functions & modular code', 2, 1),
(284, 109, 'Using MicroPython libraries', 1, 1),
(285, 109, 'Code organization', 2, 1),
(286, 110, 'Logic-based Python tasks', 1, 1),
(287, 110, 'Code optimization practice', 2, 1),
(288, 111, 'ESP8266 features', 1, 1),
(289, 111, 'Pin configuration', 2, 1),
(290, 112, 'Firmware flashing', 1, 1),
(291, 112, 'IDE & REPL usage', 2, 1),
(292, 113, 'Digital input & output', 1, 1),
(293, 113, 'LED & relay control', 2, 1),
(294, 114, 'Reading sensor values', 1, 1),
(295, 114, 'Data calibration', 2, 1),
(296, 115, 'Station & Access Point mode', 1, 1),
(297, 115, 'Network connection', 2, 1),
(298, 116, 'Creating web interface', 1, 1),
(299, 116, 'Live sensor data display', 2, 1),
(300, 117, 'Wi-Fi-based monitoring system', 1, 1),
(301, 117, 'Testing & validation', 2, 1),
(302, 118, 'ESP32 features', 1, 1),
(303, 118, 'GPIO & power management', 2, 1),
(304, 119, 'Firmware installation', 1, 1),
(305, 119, 'Development workflow', 2, 1),
(306, 120, 'GPIO programming', 1, 1),
(307, 120, 'PWM output control', 2, 1),
(308, 121, 'Timers', 1, 1),
(309, 121, 'Interrupt handling', 2, 1),
(310, 122, 'Handling multiple sensors', 1, 1),
(311, 122, 'Data aggregation', 2, 1),
(312, 123, 'Wireless communication basics', 1, 1),
(313, 123, 'Device interaction', 2, 1),
(314, 124, 'Smart control system', 1, 1),
(315, 124, 'Performance analysis', 2, 1),
(316, 125, 'Code optimization', 1, 1),
(317, 125, 'Power efficiency techniques', 2, 1),
(318, 126, 'Cloud architecture', 1, 1),
(319, 126, 'Device-to-cloud communication', 2, 1),
(320, 127, 'Sending sensor data', 1, 1),
(321, 127, 'API & authentication basics', 2, 1),
(322, 128, 'Data visualization', 1, 1),
(323, 128, 'Charts & widgets', 2, 1),
(324, 129, 'Cloud-based ON/OFF', 1, 1),
(325, 129, 'Rule-based automation', 2, 1),
(326, 130, 'Cloud-connected IoT system', 1, 1),
(327, 130, 'Validation & testing', 2, 1),
(328, 131, 'Board architecture', 1, 1),
(329, 131, 'Operating system setup', 2, 1),
(330, 132, 'Python vs MicroPython usage', 1, 1),
(331, 132, 'GPIO programming', 2, 1),
(332, 133, 'Multiple sensor handling', 1, 1),
(333, 133, 'Data processing', 2, 1),
(334, 134, 'Industrial monitoring systems', 1, 1),
(335, 134, 'Data-driven decision making', 2, 1),
(336, 135, 'Industrial monitoring application', 1, 1),
(337, 135, 'Testing & analysis', 2, 1),
(338, 136, 'Problem identification', 1, 1),
(339, 136, 'Block diagram & flowchart', 2, 1),
(340, 137, 'Hardware integration', 1, 1),
(341, 137, 'MicroPython coding', 2, 1),
(342, 138, 'Cloud integration', 1, 1),
(343, 138, 'Debugging & optimization', 2, 1),
(344, 139, 'Project report writing', 1, 1),
(345, 139, 'Technical presentation', 2, 1),
(346, 140, 'Major project demo', 1, 1),
(347, 140, 'Evaluation & certification', 2, 1),
(348, 141, 'Training structure & evaluation', 1, 1),
(349, 141, 'What is Robotics', 2, 1),
(350, 141, 'Types of robots & applications', 3, 1),
(351, 142, 'Communication skills', 1, 1),
(352, 142, 'Technical confidence', 2, 1),
(353, 142, 'Lab discipline & teamwork', 3, 1),
(354, 143, 'Computer hardware & software', 1, 1),
(355, 143, 'Linux & Windows overview', 2, 1),
(356, 143, 'File & directory management', 3, 1),
(357, 144, 'Algorithms', 1, 1),
(358, 144, 'Flowcharts', 2, 1),
(359, 144, 'Problem-solving mindset', 3, 1),
(360, 145, 'Critical thinking', 1, 1),
(361, 145, 'Project-based learning', 2, 1),
(362, 145, 'Professional ethics', 3, 1),
(363, 146, 'Robot structure', 1, 1),
(364, 146, 'Sensors, actuators & controller roles', 2, 1),
(365, 147, 'Mobile robots', 1, 1),
(366, 147, 'Industrial robots', 2, 1),
(367, 147, 'Service robots', 3, 1),
(368, 148, 'Linear & angular motion', 1, 1),
(369, 148, 'Robot movement principles', 2, 1),
(370, 149, 'Motors & wheels', 1, 1),
(371, 149, 'Sensors used in robotics', 2, 1),
(372, 150, 'Robot block diagram creation', 1, 1),
(373, 150, 'System understanding task', 2, 1),
(374, 151, 'Python syntax', 1, 1),
(375, 151, 'Variables & data types', 2, 1),
(376, 152, 'Conditional logic', 1, 1),
(377, 152, 'Loops for robotics logic', 2, 1),
(378, 153, 'Functions', 1, 1),
(379, 153, 'Code structuring', 2, 1),
(380, 154, 'NumPy basics', 1, 1),
(381, 154, 'Time & math libraries', 2, 1),
(382, 155, 'Reading & writing files', 1, 1),
(383, 155, 'Debugging techniques', 2, 1),
(384, 156, 'Logic-based robotic problems', 1, 1),
(385, 156, 'Movement algorithms', 2, 1),
(386, 157, 'Robot logic simulation', 1, 1),
(387, 157, 'Code testing & review', 2, 1),
(388, 158, 'Pi board architecture', 1, 1),
(389, 158, 'GPIO concept', 2, 1),
(390, 159, 'Raspberry Pi OS', 1, 1),
(391, 159, 'Terminal commands', 2, 1),
(392, 160, 'Digital input & output', 1, 1),
(393, 160, 'Motor control basics', 2, 1),
(394, 161, 'Distance & motion sensors', 1, 1),
(395, 161, 'Sensor data processing', 2, 1),
(396, 162, 'Sensor-controlled robot', 1, 1),
(397, 162, 'Testing & validation', 2, 1),
(398, 163, 'DC motors', 1, 1),
(399, 163, 'Servo motors', 2, 1),
(400, 164, 'Speed control', 1, 1),
(401, 164, 'Direction control', 2, 1),
(402, 165, 'Ultrasonic sensors', 1, 1),
(403, 165, 'IR sensors', 2, 1),
(404, 166, 'Using multiple sensors', 1, 1),
(405, 166, 'Decision logic', 2, 1),
(406, 167, 'Obstacle-avoiding robot logic', 1, 1),
(407, 167, 'Performance analysis', 2, 1),
(408, 168, 'What is ROS', 1, 1),
(409, 168, 'ROS architecture', 2, 1),
(410, 169, 'ROS on Raspberry Pi / PC', 1, 1),
(411, 169, 'Workspace creation', 2, 1),
(412, 170, 'Nodes', 1, 1),
(413, 170, 'Publishers & subscribers', 2, 1),
(414, 171, 'Message types', 1, 1),
(415, 171, 'Service communication', 2, 1),
(416, 172, 'Movement commands', 1, 1),
(417, 172, 'Sensor data handling', 2, 1),
(418, 173, 'ROS-controlled mobile robot', 1, 1),
(419, 173, 'Testing & debugging', 2, 1),
(420, 174, 'Simulation concept', 1, 1),
(421, 174, 'Gazebo environment', 2, 1),
(422, 175, 'Robot structure', 1, 1),
(423, 175, 'Links & joints', 2, 1),
(424, 176, 'Simulated sensors', 1, 1),
(425, 176, 'Data visualization', 2, 1),
(426, 177, 'Robot movement', 1, 1),
(427, 177, 'Obstacle interaction', 2, 1),
(428, 178, 'Virtual robot simulation', 1, 1),
(429, 178, 'Performance testing', 2, 1),
(430, 179, 'CAD tools overview', 1, 1),
(431, 179, 'Mechanical design basics', 2, 1),
(432, 180, '2D & 3D modeling', 1, 1),
(433, 180, 'Assembly concepts', 2, 1),
(434, 181, 'CAD to simulation workflow', 1, 1),
(435, 181, 'Design optimization', 2, 1),
(436, 182, 'Problem selection', 1, 1),
(437, 182, 'System architecture', 2, 1),
(438, 183, 'Hardware & software integration', 1, 1),
(439, 183, 'Python & ROS coding', 2, 1),
(440, 184, 'Testing & debugging', 1, 1),
(441, 184, 'Optimization', 2, 1),
(442, 185, 'Project demonstration', 1, 1),
(443, 185, 'Assessment & certification', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `training_page_content`
--

DROP TABLE IF EXISTS `training_page_content`;
CREATE TABLE IF NOT EXISTS `training_page_content` (
  `id` int NOT NULL AUTO_INCREMENT,
  `heading` varchar(255) NOT NULL,
  `sub_heading` varchar(255) DEFAULT NULL,
  `description` text,
  `hero_image` varchar(255) DEFAULT NULL,
  `training_contact_title` varchar(120) DEFAULT 'Course Counsellor',
  `training_contact_phone` varchar(30) DEFAULT NULL,
  `technical_contact_name` varchar(120) DEFAULT NULL,
  `technical_contact_phone` varchar(30) DEFAULT NULL,
  `know_more_link` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `training_page_content`
--

INSERT INTO `training_page_content` (`id`, `heading`, `sub_heading`, `description`, `hero_image`, `training_contact_title`, `training_contact_phone`, `technical_contact_name`, `technical_contact_phone`, `know_more_link`, `status`, `updated_at`) VALUES
(1, 'Summer Training / Internship in Lucknow', 'Industry-oriented practical learning with projects', 'We provide B.Tech training and internship programs in Embedded Systems, IoT, Robotics, Drone Technology and Automation. The program focuses on daily practicals, real-time projects, assignments, revision sessions and certification-oriented learning.', '', 'Course Counsellor', '7080102007', 'Er. Akash Maurya', '7080102008', 'https://www.trainingatsoftpro.com/', 1, '2026-05-05 07:17:45');

-- --------------------------------------------------------

--
-- Table structure for table `training_page_settings`
--

DROP TABLE IF EXISTS `training_page_settings`;
CREATE TABLE IF NOT EXISTS `training_page_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_title` varchar(255) DEFAULT 'TRAINING/ <span class=''pink''>INTERNSHIP</span>',
  `breadcrumb` varchar(100) DEFAULT 'Training/Internship',
  `left_heading` varchar(255) DEFAULT NULL,
  `left_description` text,
  `about_heading` varchar(255) DEFAULT NULL,
  `about_description` text,
  `hero_image` varchar(255) DEFAULT NULL,
  `counsellor_title` varchar(150) DEFAULT NULL,
  `counsellor_phone` varchar(30) DEFAULT NULL,
  `technical_name` varchar(150) DEFAULT NULL,
  `technical_phone` varchar(30) DEFAULT NULL,
  `know_more_link` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training_points`
--

DROP TABLE IF EXISTS `training_points`;
CREATE TABLE IF NOT EXISTS `training_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `point_type` enum('why','eligibility') NOT NULL DEFAULT 'why',
  `point_text` varchar(500) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `training_points`
--

INSERT INTO `training_points` (`id`, `point_type`, `point_text`, `sort_order`, `status`, `created_at`) VALUES
(1, 'why', '39 Practicals, 23 Minor Projects & 4 Major Projects within 45 days', 1, 1, '2026-05-05 07:17:45'),
(2, 'why', 'Professionally designed syllabus for Basic & Advance Embedded Systems', 2, 1, '2026-05-05 07:17:45'),
(3, 'why', 'Daily theory + practical sessions with assignment review', 3, 1, '2026-05-05 07:17:45'),
(4, 'why', 'Regular revision, doubt clearing and project development time', 4, 1, '2026-05-05 07:17:45'),
(5, 'eligibility', 'Diploma (EC)', 1, 1, '2026-05-05 07:17:45'),
(6, 'eligibility', 'Diploma (EE)', 2, 1, '2026-05-05 07:17:45'),
(7, 'eligibility', 'B.Tech (EC)', 3, 1, '2026-05-05 07:17:45'),
(8, 'eligibility', 'B.Tech (EE)', 4, 1, '2026-05-05 07:17:45');

-- --------------------------------------------------------

--
-- Table structure for table `training_syllabus`
--

DROP TABLE IF EXISTS `training_syllabus`;
CREATE TABLE IF NOT EXISTS `training_syllabus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_title` varchar(255) NOT NULL,
  `module_content` text NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `training_syllabus`
--

INSERT INTO `training_syllabus` (`id`, `module_title`, `module_content`, `sort_order`, `status`, `created_at`) VALUES
(1, 'Module 1: Basics of Electronics & Embedded C', 'Electronics fundamentals, C programming basics, microcontroller architecture, GPIO handling and practical lab setup.', 1, 1, '2026-05-05 07:17:45'),
(2, 'Module 2: Arduino, Sensors & Interfacing', 'Arduino board programming, sensor interfacing, display modules, motor driver, relay control and mini practicals.', 2, 1, '2026-05-05 07:17:45'),
(3, 'Module 3: IoT & Cloud Based Projects', 'IoT architecture, Wi-Fi modules, data monitoring, cloud dashboard integration and automation use cases.', 3, 1, '2026-05-05 07:17:45'),
(4, 'Module 4: Minor & Major Project Development', 'Project selection, circuit implementation, coding, testing, debugging, documentation and final presentation.', 4, 1, '2026-05-05 07:17:45');

-- --------------------------------------------------------

--
-- Table structure for table `training_technologies`
--

DROP TABLE IF EXISTS `training_technologies`;
CREATE TABLE IF NOT EXISTS `training_technologies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT '#',
  `short_text` varchar(255) DEFAULT 'Certification',
  `detail_title` varchar(255) DEFAULT NULL,
  `detail_image` varchar(255) DEFAULT NULL,
  `detail_description` text,
  `batch_text` varchar(255) DEFAULT 'New Batch Starting Soon – Enroll Today!',
  `rating` tinyint NOT NULL DEFAULT '5',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `training_technologies`
--

INSERT INTO `training_technologies` (`id`, `title`, `slug`, `image`, `link`, `short_text`, `detail_title`, `detail_image`, `detail_description`, `batch_text`, `rating`, `sort_order`, `status`, `created_at`) VALUES
(6, 'Embedded Systems & Programming', 'embedded-system', 'uploads/technologies/1777984843_4750.png', 'technology.php?slug=embedded-system', 'Certification', 'Embedded Systems & Programming', 'uploads/technologies/1777984843_5448.png', 'Embedded Systems & Programming focuses on building a strong link\r\n        between hardware and software. It covers microcontrollers, sensors, and real-time\r\n        systems with hands-on coding practice. You will learn low-level programming using\r\n        C/C++ for system control and optimization. The course includes practical projects\r\n        to develop real-world embedded applications. It provides a solid foundation for\r\n        careers in electronics, IoT, and automation.', 'New Batch Starting Soon – Enroll Today!', 4, 0, 1, '2026-05-05 11:08:42'),
(7, 'Internet of Things (IoT) & Industrial IoT (IIoT)', 'iot', 'uploads/technologies/1777985649_1996.jpeg', 'technology.php?slug=iot', 'Certification', 'Internet of Things (IoT) & Industrial IoT (IIoT)', 'uploads/technologies/1777985649_3435.png', 'Embedded Systems & Programming\r\nEmbedded Systems & Programming focuses on building a strong link\r\nbetween hardware and software. It covers microcontrollers, sensors, and real-time\r\nsystems with hands-on coding practice. You will learn low-level programming using\r\nC/C++ for system control and optimization. The course includes practical projects\r\nto develop real-world embedded applications. It provides a solid foundation for\r\ncareers in electronics, IoT, and automation.\r\n\r\nNew Batch Starting Soon – Enroll Today!', 'New Batch Starting Soon – Enroll Today!', 5, 0, 1, '2026-05-05 12:54:09'),
(8, 'Robotics & Automation', 'robotics', 'uploads/technologies/1777985870_6573.png', 'technology.php?slug=robotics', 'Certification', 'Robotics & Automation', 'uploads/technologies/1777985870_3071.png', 'Internet of Things (IoT) & Industrial IoT (IIoT)\r\nEmbedded Systems & Programming\r\nEmbedded Systems & Programming focuses on building a strong link\r\nbetween hardware and software. It covers microcontrollers, sensors, and real-time\r\nsystems with hands-on coding practice. You will learn low-level programming using\r\nC/C++ for system control and optimization. The course includes practical projects\r\nto develop real-world embedded applications. It provides a solid foundation for\r\ncareers in electronics, IoT, and automation.\r\n\r\nNew Batch Starting Soon – Enroll Today!', 'New Batch Starting Soon – Enroll Today!', 5, 0, 1, '2026-05-05 12:57:50');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `order_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `transaction_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_order_id` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_signature` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('Pending','Paid','Failed','Refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_transactions_order` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `order_id`, `order_no`, `payment_method`, `amount`, `transaction_id`, `razorpay_order_id`, `razorpay_signature`, `payment_status`, `created_at`) VALUES
(1, 25, 'ORD20260502082147562', 'COD', 4049.00, '', NULL, NULL, 'Pending', '2026-05-02 08:21:47'),
(2, 26, 'ORD20260502083842214', 'COD', 139.00, '', NULL, NULL, 'Pending', '2026-05-02 08:38:43'),
(3, 27, 'ORD20260502084210796', 'COD', 139.00, '', NULL, NULL, 'Pending', '2026-05-02 08:42:10'),
(4, 28, 'ORD20260502084805261', 'COD', 5088.00, '', NULL, NULL, 'Pending', '2026-05-02 08:48:05'),
(5, 29, 'ORD20260502100508329', 'COD', 139.00, '', NULL, NULL, 'Pending', '2026-05-02 10:05:08'),
(6, 30, 'ORD20260502101034374', 'COD', 139.00, '', NULL, NULL, 'Pending', '2026-05-02 10:10:34'),
(7, 31, 'ORD20260502121424108', 'COD', 818.00, '', NULL, NULL, 'Pending', '2026-05-02 12:14:24'),
(8, 32, 'ORD20260502132021446', 'COD', 24359.00, '', '', '', 'Pending', '2026-05-02 13:20:21'),
(9, 33, 'ORD20260506062115753', 'ONLINE', 42469.38, 'pay_SlyfBwUMqtItA0', 'order_Slye0ZbPSajN76', '16f3a455b583ec4a233c309036a6e71aedef7c0762d1a2d6c5e7ef05bffcd9c0', 'Refunded', '2026-05-06 06:21:15'),
(10, 34, 'ORD20260508050423818', 'ONLINE', 3038.50, 'pay_SmkQLwNkBsLoDc', 'order_SmkMIaFRvSActx', 'cc1d6de5e228e86db53e7ef9b49d102de1a96a6b078bc952782625486c8db354', 'Paid', '2026-05-08 05:04:23'),
(11, 35, 'ORD20260508051044194', 'ONLINE', 298.98, 'pay_SmkX6w2gmbObn0', 'order_SmkW9YurhL5pWX', '3557542b03a1020167449485eb4fc4a40c701f2d8e80e4b0feb93bf6c6cc7915', 'Refunded', '2026-05-08 05:10:44'),
(12, 36, 'ORD20260509080130578', 'ONLINE', 130.00, 'pay_SnByhH8LKsjkQl', 'order_SnByXW8olOVW67', '3ce759295526bf2f08143302946f45acb5b3994c3181ebd80b796a8e5393bf77', 'Paid', '2026-05-09 08:01:30'),
(13, 37, 'ORD20260509090126960', 'ONLINE', 6258.00, 'pay_SnCzyV7EwwmVU2', 'order_SnCzRmBdr24LBo', '1e05af21ff684f6f7ce68ded09233787de810583ed810f5bf887bd3b43ce0c52', 'Paid', '2026-05-09 09:01:26'),
(14, 38, 'ORD20260509093547689', 'COD', 130.00, '', '', '', 'Pending', '2026-05-09 09:35:47'),
(15, 39, 'ORD20260509101358751', 'ONLINE', 130.00, 'pay_SnEEXihFSrsUSf', 'order_SnEEOhr93av13P', '83265e339a733f4f54cb3d2f0d8ce48dc07c9e3226059bc0af6f7dae7eb8f83b', 'Paid', '2026-05-09 10:13:58'),
(16, 40, 'ORD20260509102158215', 'ONLINE', 2706.00, 'pay_SnEN5ruI4MbBWo', 'order_SnEMxz6JuRamz3', 'dcdd8a46c5d25165a1bc88f58110a84df84e49fa840ff290a6bdccd347dec72d', 'Paid', '2026-05-09 10:21:58'),
(17, 41, 'ORD20260509102910495', 'ONLINE', 13441.00, 'pay_SnEUgzQsC9eShd', 'order_SnEUWVta33JbIR', '260bf73a8c607fa0e52808e99dab3174b955c440a1006800c5397bd0a69783c2', 'Paid', '2026-05-09 10:29:10'),
(18, 42, 'ORD20260509103430946', 'ONLINE', 156.00, 'pay_SnEaKwBSkZJ6dg', 'order_SnEaB8UFmpfkxN', '1115667dbdf456f5d7ef89bea110f408418ed8ff251dcf69f32b9b130c02171a', 'Paid', '2026-05-09 10:34:30'),
(19, 43, 'ORD20260509103542613', 'COD', 130.00, '', '', '', 'Pending', '2026-05-09 10:35:42'),
(20, 44, 'ORD20260509104017735', 'COD', 12072.00, '', '', '', 'Paid', '2026-05-09 10:40:17'),
(21, 45, 'ORD20260509104147580', 'ONLINE', 482.00, 'pay_SnEi1oam9oTeHu', 'order_SnEhqaLnwaMvya', '09fc1191c07a28695bfd465734c68c871c4e96d1e5d46e8fd8ede81f60d248b1', 'Paid', '2026-05-09 10:41:47'),
(22, 46, 'ORD20260509104440204', 'COD', 853.00, '', '', '', 'Paid', '2026-05-09 10:44:40'),
(23, 47, 'ORD20260509111742363', 'ONLINE', 2706.00, 'pay_SnFJySPbogKfQQ', 'order_SnFJqPL7eGqB0z', 'f441be76ac7f7e3e9e5b2b4c58b2a22d044abe50cad04dfa15bfb82e30fe304b', 'Paid', '2026-05-09 11:17:42'),
(24, 48, 'ORD20260509114147792', 'COD', 1241.00, '', '', '', 'Paid', '2026-05-09 11:41:47'),
(25, 49, 'ORD20260509114613400', 'ONLINE', 612.00, 'pay_SnFo4JXWPPWRrQ', 'order_SnFnxpKIGt65Av', '8227f9ccb7a7c73b485abedd03768338649b188013f5cc5b276c6867deb33b37', 'Paid', '2026-05-09 11:46:13'),
(26, 50, 'ORD20260509130340406', 'COD', 612.00, '', '', '', 'Paid', '2026-05-09 13:03:40'),
(27, 51, 'ORD20260509131510913', 'ONLINE', 1335.00, 'pay_SnHK01ZiuF78dr', 'order_SnHJjs5HOPfnPt', '181591e57595de4d8c2bb18b773fbccd3e9a71baa5199d12554d18dfff9d65dd', 'Paid', '2026-05-09 13:15:10'),
(28, 52, 'ORD20260509135632237', 'COD', 156.00, '', '', '', 'Paid', '2026-05-09 13:56:32'),
(29, 53, 'ORD20260509135947235', 'COD', 7220.00, '', '', '', 'Paid', '2026-05-09 13:59:47'),
(30, 54, 'ORD20260512053420431', 'COD', 24683.71, '', '', '', 'Pending', '2026-05-12 05:34:20'),
(31, 55, 'ORD20260512054759910', 'COD', 41759.00, '', '', '', 'Paid', '2026-05-12 05:47:59'),
(32, 56, 'ORD20260512055038872', 'COD', 853.00, '', '', '', 'Paid', '2026-05-12 05:50:38'),
(33, 57, 'ORD20260512055514147', 'COD', 1371.00, '', '', '', 'Pending', '2026-05-12 05:55:14'),
(34, 58, 'ORD20260512055732623', 'COD', 2299.00, '', '', '', 'Pending', '2026-05-12 05:57:32'),
(35, 59, 'ORD20260512055815227', 'COD', 482.00, '', '', '', 'Pending', '2026-05-12 05:58:15'),
(36, 60, 'ORD20260512055933962', 'COD', 371.00, '', '', '', 'Pending', '2026-05-12 05:59:33'),
(37, 61, 'ORD20260512060300738', 'COD', 1241.00, '', '', '', 'Pending', '2026-05-12 06:03:00'),
(38, 62, 'ORD20260512060417129', 'COD', 22026.00, '', '', '', 'Pending', '2026-05-12 06:04:17'),
(39, 63, 'ORD20260512060504190', 'COD', 20982.00, '', '', '', 'Pending', '2026-05-12 06:05:04'),
(40, 64, 'ORD20260512060855526', 'COD', 1612.00, '', '', '', 'Pending', '2026-05-12 06:08:55'),
(41, 65, 'ORD20260512061518725', 'COD', 371.00, '', '', '', 'Pending', '2026-05-12 06:15:18'),
(42, 66, 'ORD20260512064125642', 'COD', 241.00, '', '', '', 'Pending', '2026-05-12 06:41:25'),
(43, 67, 'ORD20260512064200202', 'COD', 241.00, '', '', '', 'Pending', '2026-05-12 06:42:00'),
(44, 68, 'ORD20260512065113452', 'COD', 130.00, '', '', '', 'Pending', '2026-05-12 06:51:13'),
(45, 69, 'ORD20260512070039182', 'COD', 241.00, '', '', '', 'Pending', '2026-05-12 07:00:39'),
(46, 70, 'ORD20260512070214410', 'COD', 482.00, '', '', '', 'Pending', '2026-05-12 07:02:14'),
(47, 71, 'ORD20260512071231855', 'COD', 130.00, '', '', '', 'Pending', '2026-05-12 07:12:31'),
(48, 72, 'ORD20260512071856534', 'COD', 482.00, '', '', '', 'Pending', '2026-05-12 07:18:56'),
(49, 73, 'ORD20260512095111319', 'COD', 1215.00, '', '', '', 'Pending', '2026-05-12 09:51:11'),
(50, 74, 'ORD20260512100124755', 'COD', 482.00, '', '', '', 'Pending', '2026-05-12 10:01:24'),
(51, 75, 'ORD20260512100200161', 'COD', 121.00, '', '', '', 'Pending', '2026-05-12 10:02:00'),
(52, 76, 'ORD20260512100801309', 'COD', 121.00, '', '', '', 'Pending', '2026-05-12 10:08:01'),
(53, 77, 'ORD20260512101106600', 'COD', 386.00, '', '', '', 'Pending', '2026-05-12 10:11:06'),
(54, 78, 'ORD20260512101138896', 'COD', 121.00, '', '', '', 'Pending', '2026-05-12 10:11:38'),
(55, 79, 'ORD20260512101213842', 'COD', 121.00, '', '', '', 'Pending', '2026-05-12 10:12:13'),
(56, 80, 'ORD20260512101556525', 'COD', 121.00, '', '', '', 'Pending', '2026-05-12 10:15:56'),
(57, 81, 'ORD20260512101838520', 'COD', 0.00, '', '', '', 'Pending', '2026-05-12 10:18:38'),
(58, 82, 'ORD20260512102007416', 'COD', 120.36, '', '', '', 'Pending', '2026-05-12 10:20:07'),
(59, 1, 'ORD20260512112351708', 'COD', 683.96, '', '', '', 'Paid', '2026-05-12 11:23:51'),
(60, 2, 'ORD20260512134339173', 'COD', 129.99, '', '', '', 'Pending', '2026-05-12 13:43:39'),
(61, 3, 'ORD20260512134815268', 'COD', 129.99, '', '', '', 'Pending', '2026-05-12 13:48:15'),
(62, 4, 'ORD20260514045403776', 'COD', 129.99, '', '', '', 'Pending', '2026-05-14 04:54:03'),
(63, 5, 'ORD20260514062618385', 'COD', 3850.32, '', '', '', 'Pending', '2026-05-14 06:26:18'),
(64, 6, 'ORD20260514063342615', 'ONLINE', 4813.20, 'pay_Sp99YXctgrZFYI', 'order_Sp99QruwBezxoX', '424a8e98b8f8b6cce096c69bb5a6368bf9d32899285bcb754b8b3b84416f0a68', 'Paid', '2026-05-14 06:33:42'),
(65, 7, 'ORD20260514095011236', 'COD', 143.23, '', '', '', 'Pending', '2026-05-14 09:50:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `mobile` (`mobile`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `mobile`, `image`, `country`, `address`, `payment_details`) VALUES
(10, 'Aditya Pratap', 'adityapratap7391@gmail.com', '7390916220', 'uploads/profile/user_10_1777968736.jpeg', NULL, NULL, NULL),
(11, 'adityapratap', 'webdeveloper8009@gmail.com', '8009423206', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `variants`
--

DROP TABLE IF EXISTS `variants`;
CREATE TABLE IF NOT EXISTS `variants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `variant_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock` int DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variants`
--

INSERT INTO `variants` (`id`, `product_id`, `variant_name`, `stock`, `price`, `created_at`) VALUES
(1, 20, 'Micro', 0, 0.00, '2026-01-12 06:59:18');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`product_id`),
  UNIQUE KEY `unique_user_product` (`user_id`,`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(93, 10, 96, '2026-05-02 12:13:17'),
(92, 10, 97, '2026-05-02 12:13:14'),
(91, 10, 102, '2026-05-02 12:13:11'),
(90, 10, 99, '2026-05-02 12:13:09'),
(95, 10, 90, '2026-05-02 13:15:58'),
(94, 10, 91, '2026-05-02 12:56:46'),
(96, 10, 103, '2026-05-09 06:42:13'),
(100, 10, 73, '2026-05-14 10:13:24'),
(98, 10, 107, '2026-05-12 10:39:56'),
(99, 10, 1, '2026-05-12 13:21:44'),
(101, 10, 72, '2026-05-14 10:13:29');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------
-- Admin Activity Logs
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int DEFAULT NULL,
  `admin_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_id` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_type` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_id` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `details` longtext COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_action` (`action`),
  KEY `idx_module` (`module`),
  KEY `idx_record` (`record_type`,`record_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
