-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 21, 2018 at 01:58 AM
-- Server version: 10.2.19-MariaDB
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `lumise_bugs`
--

DROP TABLE IF EXISTS `lumise_bugs`;
CREATE TABLE IF NOT EXISTS `lumise_bugs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `lumise` int(1) NOT NULL DEFAULT 1,
  `status` varchar(11) NOT NULL DEFAULT 'open',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `status` (`status`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_categories`
--

DROP TABLE IF EXISTS `lumise_categories`;
CREATE TABLE IF NOT EXISTS `lumise_categories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `upload` varchar(255) NOT NULL DEFAULT '',
  `thumbnail_url` varchar(255) NOT NULL DEFAULT '',
  `parent` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL DEFAULT 'cliparts',
  `active` int(1) NOT NULL DEFAULT 1,
  `order` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `parent` (`parent`),
  KEY `active` (`active`),
  KEY `order` (`order`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_categories_reference`
--

DROP TABLE IF EXISTS `lumise_categories_reference`;
CREATE TABLE IF NOT EXISTS `lumise_categories_reference` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `item_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `category_id` (`category_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_cliparts`
--

DROP TABLE IF EXISTS `lumise_cliparts`;
CREATE TABLE IF NOT EXISTS `lumise_cliparts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `upload` text NOT NULL DEFAULT '',
  `thumbnail_url` text NOT NULL DEFAULT '',
  `price` float NOT NULL DEFAULT 0,
  `featured` int(1) NOT NULL DEFAULT 0,
  `tags` varchar(255) NOT NULL DEFAULT '',
  `use_count` int(11) NOT NULL DEFAULT 0,
  `order` int(1) NOT NULL DEFAULT 0,
  `active` int(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `price` (`price`),
  KEY `active` (`active`),
  KEY `created` (`created`),
  KEY `order` (`order`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_designs`
--

DROP TABLE IF EXISTS `lumise_designs`;
CREATE TABLE IF NOT EXISTS `lumise_designs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL DEFAULT '',
  `aid` varchar(11) NOT NULL DEFAULT '',
  `pid` varchar(11) NOT NULL DEFAULT '',
  `screenshots` varchar(255) NOT NULL DEFAULT '',
  `categories` varchar(255) NOT NULL DEFAULT '',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `data` varchar(255) NOT NULL DEFAULT '',
  `data_sharing` varchar(255) NOT NULL DEFAULT '',
  `share_token` int(11) NOT NULL DEFAULT 0,
  `sharing` int(1) NOT NULL DEFAULT 0,
  `active` int(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `aid` (`aid`),
  KEY `pid` (`pid`),
  KEY `active` (`active`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_fonts`
--

DROP TABLE IF EXISTS `lumise_fonts`;
CREATE TABLE IF NOT EXISTS `lumise_fonts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `upload` varchar(255) NOT NULL,
  `upload_ttf` varchar(255) NOT NULL DEFAULT '',
  `active` int(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `active` (`active`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_guests`
--

DROP TABLE IF EXISTS `lumise_guests`;
CREATE TABLE IF NOT EXISTS `lumise_guests` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `zipcode` varchar(20) NOT NULL DEFAULT '',
  `city` varchar(20) NOT NULL DEFAULT '',
  `country` varchar(20) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `phone` (`phone`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_languages`
--

DROP TABLE IF EXISTS `lumise_languages`;
CREATE TABLE IF NOT EXISTS `lumise_languages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `original_text` text NOT NULL,
  `lang` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_orders`
--

DROP TABLE IF EXISTS `lumise_orders`;
CREATE TABLE IF NOT EXISTS `lumise_orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `total` float NOT NULL DEFAULT 0,
  `currency` varchar(10) NOT NULL DEFAULT '',
  `payment` varchar(255) NOT NULL DEFAULT '',
  `txn_id` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `status` varchar(11) NOT NULL DEFAULT 'pending',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `user_id` (`user_id`),
  KEY `updated` (`updated`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_order_products`
--

DROP TABLE IF EXISTS `lumise_order_products`;
CREATE TABLE IF NOT EXISTS `lumise_order_products` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Id',
  `order_id` varchar(50) NOT NULL DEFAULT '0' COMMENT 'Order id',
  `product_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Order id',
  `product_base` bigint(20) NOT NULL DEFAULT 0,
  `product_price` text NOT NULL COMMENT 'Product Price',
  `qty` int(11) NOT NULL DEFAULT 0,
  `data` longtext NOT NULL COMMENT 'Product Data',
  `design` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `screenshots` text NOT NULL,
  `print_files` text NOT NULL,
  `custom` int(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created At',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `product_id` (`product_id`),
  KEY `product_base` (`product_base`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_printings`
--

DROP TABLE IF EXISTS `lumise_printings`;
CREATE TABLE IF NOT EXISTS `lumise_printings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `active` int(1) NOT NULL DEFAULT 1,
  `calculate` longtext NOT NULL DEFAULT '',
  `thumbnail` varchar(255) NOT NULL DEFAULT '',
  `upload` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `active` (`active`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_products`
--

DROP TABLE IF EXISTS `lumise_products`;
CREATE TABLE IF NOT EXISTS `lumise_products` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `product` int(11) NOT NULL DEFAULT 0,
  `thumbnail` varchar(255) NOT NULL DEFAULT '',
  `thumbnail_url` varchar(255) NOT NULL DEFAULT '',
  `template` varchar(11) NOT NULL DEFAULT '',
  `description` text NOT NULL DEFAULT '',
  `stages` text NOT NULL,
  `variations` text NOT NULL DEFAULT '',
  `attributes` text NOT NULL DEFAULT '',
  `printings` text NOT NULL DEFAULT '',
  `order` int(11) NOT NULL DEFAULT 0,
  `active` int(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `price` (`price`),
  KEY `product` (`product`),
  KEY `active` (`active`),
  KEY `order` (`order`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

--
-- Table structure for table `lumise_settings`
--

DROP TABLE IF EXISTS `lumise_settings`;
CREATE TABLE IF NOT EXISTS `lumise_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_shapes`
--

DROP TABLE IF EXISTS `lumise_shapes`;
CREATE TABLE IF NOT EXISTS `lumise_shapes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `active` int(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_shares`
--

DROP TABLE IF EXISTS `lumise_shares`;
CREATE TABLE IF NOT EXISTS `lumise_shares` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `aid` varchar(255) NOT NULL DEFAULT '',
  `share_id` varchar(255) NOT NULL,
  `product` int(11) NOT NULL DEFAULT 0,
  `product_cms` int(11) NOT NULL DEFAULT 0,
  `view` int(11) NOT NULL DEFAULT 0,
  `active` int(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `product` (`product`),
  KEY `product_cms` (`product_cms`),
  KEY `active` (`active`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_tags`
--

DROP TABLE IF EXISTS `lumise_tags`;
CREATE TABLE IF NOT EXISTS `lumise_tags` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_tags_reference`
--

DROP TABLE IF EXISTS `lumise_tags_reference`;
CREATE TABLE IF NOT EXISTS `lumise_tags_reference` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL DEFAULT 0,
  `item_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lumise_templates`
--

DROP TABLE IF EXISTS `lumise_templates`;
CREATE TABLE IF NOT EXISTS `lumise_templates` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `author` varchar(255) NOT NULL DEFAULT '',
  `screenshot` varchar(255) NOT NULL,
  `upload` varchar(255) NOT NULL,
  `featured` int(1) NOT NULL DEFAULT 0,
  `active` int(1) NOT NULL DEFAULT 1,
  `tags` varchar(255) NOT NULL DEFAULT '',
  `order` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `created` (`created`),
  KEY `price` (`price`),
  KEY `featured` (`featured`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8mb4;

COMMIT;
