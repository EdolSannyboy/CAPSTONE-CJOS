-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 04:58 PM
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
-- Database: `canteen1`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `audit_ID` int(11) NOT NULL,
  `batch_ID` varchar(50) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_ID` int(11) DEFAULT NULL,
  `column_name` varchar(100) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `action_description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`audit_ID`, `batch_ID`, `table_name`, `record_ID`, `column_name`, `old_value`, `new_value`, `changed_by`, `user_type`, `action_description`, `created_at`) VALUES
(119, 'batch_689af4591a5ef', 'administrator', 1, 'firstname', 'Juan', 'Juans', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 15:59:21'),
(120, 'batch_689af4591a5ef', 'administrator', 1, 'middlename', 'Santos', 'Santoss', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 15:59:21'),
(121, 'batch_689af4591a5ef', 'administrator', 1, 'lastname', 'Dela Cruz', 'Dela Cruzs', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 15:59:21'),
(122, 'batch_689af4621384e', 'administrator', 1, 'firstname', 'Juans', 'Juan', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 15:59:30'),
(123, 'batch_689af4621384e', 'administrator', 1, 'middlename', 'Santoss', 'Santos', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 15:59:30'),
(124, 'batch_689af4621384e', 'administrator', 1, 'lastname', 'Dela Cruzs', 'Dela Cruz', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 15:59:30'),
(125, 'batch_689af5b39e47e', 'administrator', 1, 'firstname', 'Juan', 'Juans', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 16:05:07'),
(126, 'batch_689af5b39e47e', 'administrator', 1, 'middlename', 'Santos', 'Santoss', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 16:05:07'),
(127, 'batch_689af5b702411', 'administrator', 1, 'firstname', 'Juans', 'Juan', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 16:05:11'),
(128, 'batch_689af5b99937e', 'administrator', 1, 'middlename', 'Santoss', 'Santos', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 16:05:13'),
(129, 'batch_689af5f748c3f', 'administrator', 1, 'image', 'admin_1_juan_dela_cruz_689ad3e4834c4.jpg', 'admin_1_juan_dela_cruz_689af5f7476a7.jpg', 1, 'Administrator', 'Updated profile picture for Administrator with ID: 1', '2025-08-12 16:06:15'),
(130, 'batch_689af5fed7bf4', 'administrator', 1, 'image', 'admin_1_juan_dela_cruz_689af5f7476a7.jpg', 'admin_1_juan_dela_cruz_689af5fed3a24.jpg', 1, 'Administrator', 'Updated profile picture for Administrator with ID: 1', '2025-08-12 16:06:22'),
(131, 'batch_689af602caf83', 'administrator', 1, 'firstname', 'Juan', 'Juans', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 16:06:26'),
(132, 'batch_689af6052fa37', 'administrator', 1, 'firstname', 'Juans', 'Juan', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 16:06:29'),
(133, 'batch_689af6101f3ac', 'administrator', 1, 'password', '[ENCRYPTED]', '[ENCRYPTED]', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 16:06:40'),
(134, 'batch_689af62d06454', 'administrator', 1, 'password', '[ENCRYPTED]', '[ENCRYPTED]', 1, 'Administrator', 'Profile updated for Administrator ID 1', '2025-08-12 16:07:09'),
(135, 'batch_689b3c859dc5c', 'administrator', 6, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 6', '2025-08-12 21:07:17'),
(136, 'batch_689b3c8b5fbef', 'administrator', 9, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 9', '2025-08-12 21:07:23'),
(137, 'batch_689b3c8b61317', 'administrator', 8, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 8', '2025-08-12 21:07:23'),
(138, 'batch_689b8892b1428', 'canteen_staff', 1, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 1', '2025-08-13 02:31:46'),
(139, 'batch_689b8f5e01964', 'administrator', 11, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 11', '2025-08-13 03:00:46'),
(140, 'batch_689b8f5e0229e', 'administrator', 10, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 10', '2025-08-13 03:00:46'),
(141, 'batch_689b8f62d0035', 'canteen_staff', 3, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 3', '2025-08-13 03:00:50'),
(142, 'batch_689b8f62d1220', 'canteen_staff', 2, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 2', '2025-08-13 03:00:50'),
(143, 'batch_689b8f69b6b64', 'office_staff', 2, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 2', '2025-08-13 03:00:57'),
(144, 'batch_689b8f69b78e0', 'office_staff', 1, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 1', '2025-08-13 03:00:57'),
(145, 'batch_689c37cec8ed6', 'products', 1, 'product_name', NULL, 'dsad', 1, 'Administrator', 'Added new product: dsad', '2025-08-13 14:59:26'),
(146, 'batch_689c37cec8ed6', 'products', 1, 'category', NULL, 'Meal', 1, 'Administrator', 'Added new product: dsad', '2025-08-13 14:59:26'),
(147, 'batch_689c37cec8ed6', 'products', 1, 'description', NULL, 'dsada', 1, 'Administrator', 'Added new product: dsad', '2025-08-13 14:59:26'),
(148, 'batch_689c37cec8ed6', 'products', 1, 'price', NULL, '23', 1, 'Administrator', 'Added new product: dsad', '2025-08-13 14:59:26'),
(149, 'batch_689c37cec8ed6', 'products', 1, 'stock_quantity', NULL, '2', 1, 'Administrator', 'Added new product: dsad', '2025-08-13 14:59:26'),
(150, 'batch_689c37cec8ed6', 'products', 1, 'unit', NULL, 'Bottle', 1, 'Administrator', 'Added new product: dsad', '2025-08-13 14:59:26'),
(151, 'batch_689c37cec8ed6', 'products', 1, 'image', NULL, '1755068366.png', 1, 'Administrator', 'Added new product: dsad', '2025-08-13 14:59:26'),
(152, 'batch_689c37e9d39ef', 'products', 2, 'product_name', NULL, 'dsadds', 1, 'Administrator', 'Added new product: dsadds', '2025-08-13 14:59:53'),
(153, 'batch_689c37e9d39ef', 'products', 2, 'category', NULL, 'Meal', 1, 'Administrator', 'Added new product: dsadds', '2025-08-13 14:59:53'),
(154, 'batch_689c37e9d39ef', 'products', 2, 'description', NULL, 'dsada', 1, 'Administrator', 'Added new product: dsadds', '2025-08-13 14:59:53'),
(155, 'batch_689c37e9d39ef', 'products', 2, 'price', NULL, '23', 1, 'Administrator', 'Added new product: dsadds', '2025-08-13 14:59:53'),
(156, 'batch_689c37e9d39ef', 'products', 2, 'stock_quantity', NULL, '2', 1, 'Administrator', 'Added new product: dsadds', '2025-08-13 14:59:53'),
(157, 'batch_689c37e9d39ef', 'products', 2, 'unit', NULL, 'Bottle', 1, 'Administrator', 'Added new product: dsadds', '2025-08-13 14:59:53'),
(158, 'batch_689c37e9d39ef', 'products', 2, 'image', NULL, '1755068393.png', 1, 'Administrator', 'Added new product: dsadds', '2025-08-13 14:59:53'),
(159, 'batch_689c39a21b725', 'products', 3, 'product_name', NULL, 'da', 1, 'Administrator', 'Added new product: da', '2025-08-13 15:07:14'),
(160, 'batch_689c39a21b725', 'products', 3, 'category', NULL, 'Meal', 1, 'Administrator', 'Added new product: da', '2025-08-13 15:07:14'),
(161, 'batch_689c39a21b725', 'products', 3, 'description', NULL, 'dsada', 1, 'Administrator', 'Added new product: da', '2025-08-13 15:07:14'),
(162, 'batch_689c39a21b725', 'products', 3, 'price', NULL, '32', 1, 'Administrator', 'Added new product: da', '2025-08-13 15:07:14'),
(163, 'batch_689c39a21b725', 'products', 3, 'stock_quantity', NULL, '32', 1, 'Administrator', 'Added new product: da', '2025-08-13 15:07:14'),
(164, 'batch_689c39a21b725', 'products', 3, 'unit', NULL, 'Bottle', 1, 'Administrator', 'Added new product: da', '2025-08-13 15:07:14'),
(165, 'batch_689c39a21b725', 'products', 3, 'image', NULL, '1755068834.jpg', 1, 'Administrator', 'Added new product: da', '2025-08-13 15:07:14'),
(166, 'batch_689c3bb53b162', 'products', 3, 'product_name', 'da', 'dads', 1, 'Administrator', 'Updated product: dads', '2025-08-13 15:16:05'),
(167, 'batch_689c3bb53b162', 'products', 3, 'image', '1755068834.jpg', NULL, 1, 'Administrator', 'Updated product: dads', '2025-08-13 15:16:05'),
(168, 'batch_689c3d0c38bf9', 'products', 3, 'product_name', 'dads', 'dadsds', 1, 'Administrator', 'Updated product: dadsds', '2025-08-13 15:21:48'),
(169, 'batch_689c3d1587d7c', 'products', 3, 'image', '1755068834.jpg', '1755069717.jpg', 1, 'Administrator', 'Updated product: dadsds', '2025-08-13 15:21:57'),
(170, 'batch_689c3d58ef681', 'products', 1, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 1', '2025-08-13 15:23:04'),
(171, 'batch_689c3d58efedd', 'products', 2, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 2', '2025-08-13 15:23:04'),
(172, 'batch_689c3e7d8bb64', 'products', 16, 'price', '142.49', '15', 1, 'Administrator', 'Updated product: Banana Cue', '2025-08-13 15:27:57'),
(173, 'batch_689c3e7d8bb64', 'products', 16, 'unit', 'Cup', 'Piece', 1, 'Administrator', 'Updated product: Banana Cue', '2025-08-13 15:27:57'),
(174, 'batch_689c3e7d8bb64', 'products', 16, 'image', 'banana_cue.jpg', '1755070077.jpg', 1, 'Administrator', 'Updated product: Banana Cue', '2025-08-13 15:27:57'),
(175, 'batch_689c3ed6a55dd', 'products', 30, 'price', '24.53', '30', 1, 'Administrator', 'Updated product: Bangsilog', '2025-08-13 15:29:26'),
(176, 'batch_689c3ed6a55dd', 'products', 30, 'image', 'bangsilog.jpg', '1755070166.jpg', 1, 'Administrator', 'Updated product: Bangsilog', '2025-08-13 15:29:26'),
(177, 'batch_689c3eef502ee', 'products', 5, 'image', 'beef_steak.jpg', '1755070191.jpg', 1, 'Administrator', 'Updated product: Beef Steak', '2025-08-13 15:29:51'),
(178, 'batch_689c3f0696d80', 'products', 45, 'image', 'blt_sandwich.jpg', '1755070214.jpg', 1, 'Administrator', 'Updated product: BLT Sandwich', '2025-08-13 15:30:14'),
(179, 'batch_689c3f211993d', 'products', 21, 'image', 'bottled_water.jpg', '1755070241.jpg', 1, 'Administrator', 'Updated product: Bottled Water', '2025-08-13 15:30:41'),
(180, 'batch_689c3f3fbb40b', 'products', 38, 'image', 'brownie.jpg', '1755070271.jpg', 1, 'Administrator', 'Updated product: Brownie', '2025-08-13 15:31:11'),
(181, 'batch_689c3f604b223', 'products', 36, 'image', 'buko_pandan.jpg', '1755070304.jpg', 1, 'Administrator', 'Updated product: Buko Pandan', '2025-08-13 15:31:44'),
(182, 'batch_689c3f84809ac', 'products', 8, 'image', 'burger.jpg', '1755070340.jpg', 1, 'Administrator', 'Updated product: Burger', '2025-08-13 15:32:20'),
(183, 'batch_689c3fa0076a4', 'products', 49, 'image', 'caesar_salad.jpg', '1755070368.jpg', 1, 'Administrator', 'Updated product: Caesar Salad', '2025-08-13 15:32:48'),
(184, 'batch_689c3fb5d855f', 'products', 42, 'image', 'cheesecake.jpg', '1755070389.jpg', 1, 'Administrator', 'Updated product: Cheesecake', '2025-08-13 15:33:09'),
(185, 'batch_689c402a1b358', 'products', 4, 'image', 'chicken_adobo.jpg', '1755070506.jpg', 1, 'Administrator', 'Updated product: Chicken Adobo', '2025-08-13 15:35:06'),
(186, 'batch_689c404172aad', 'products', 48, 'image', 'veggie_salad.jpg', '1755070529.jpg', 1, 'Administrator', 'Updated product: Veggie Salad', '2025-08-13 15:35:29'),
(187, 'batch_689c405f11e1e', 'products', 17, 'image', 'turon.jpg', '1755070559.jpg', 1, 'Administrator', 'Updated product: Turon', '2025-08-13 15:35:59'),
(188, 'batch_689c407888dce', 'products', 29, 'image', 'tocilog.jpg', '1755070584.jpg', 1, 'Administrator', 'Updated product: Tocilog', '2025-08-13 15:36:24'),
(189, 'batch_689c408e8ce34', 'products', 27, 'image', 'tapsilog.jpg', '1755070606.jpg', 1, 'Administrator', 'Updated product: Tapsilog', '2025-08-13 15:36:46'),
(190, 'batch_689c409f7a962', 'products', 14, 'image', 'spring_roll.jpg', '1755070623.jpg', 1, 'Administrator', 'Updated product: Spring Roll', '2025-08-13 15:37:03'),
(191, 'batch_689c40a836cd1', 'products', 7, 'image', 'spaghetti.jpg', '1755070632.jpg', 1, 'Administrator', 'Updated product: Spaghetti', '2025-08-13 15:37:12'),
(192, 'batch_689c40b31a096', 'products', 20, 'image', 'soft_drink.jpg', '1755070643.jpg', 1, 'Administrator', 'Updated product: Soft Drink', '2025-08-13 15:37:23'),
(193, 'batch_689c40bba250f', 'products', 13, 'image', 'siopao.jpg', '1755070651.jpg', 1, 'Administrator', 'Updated product: Siopao', '2025-08-13 15:37:31'),
(194, 'batch_689c40c45ca0e', 'products', 12, 'image', 'siomai.jpg', '1755070660.jpg', 1, 'Administrator', 'Updated product: Siomai', '2025-08-13 15:37:40'),
(195, 'batch_689c40ce44bc0', 'products', 32, 'image', 'pork_bbq.jpg', '1755070670.jpg', 1, 'Administrator', 'Updated product: Pork BBQ', '2025-08-13 15:37:50'),
(196, 'batch_689c40d810128', 'products', 6, 'image', 'pancit_canton.jpg', '1755070680.jpg', 1, 'Administrator', 'Updated product: Pancit Canton', '2025-08-13 15:38:00'),
(197, 'batch_689c40e15368b', 'products', 41, 'image', 'muffin.jpg', '1755070689.jpg', 1, 'Administrator', 'Updated product: Muffin', '2025-08-13 15:38:09'),
(198, 'batch_689c40ef4aad2', 'products', 24, 'image', 'milk_tea.jpg', '1755070703.jpg', 1, 'Administrator', 'Updated product: Milk Tea', '2025-08-13 15:38:23'),
(199, 'batch_689c40f7cd12f', 'products', 52, 'image', 'mashed_potato.jpg', '1755070711.jpg', 1, 'Administrator', 'Updated product: Mashed Potato', '2025-08-13 15:38:31'),
(200, 'batch_689c410586c6d', 'products', 35, 'image', 'macaroni_salad.jpg', '1755070725.jpg', 1, 'Administrator', 'Updated product: Macaroni Salad', '2025-08-13 15:38:45'),
(201, 'batch_689c41146ba9d', 'products', 26, 'image', 'lumpia.jpg', '1755070740.jpg', 1, 'Administrator', 'Updated product: Lumpia', '2025-08-13 15:39:00'),
(202, 'batch_689c4120271c4', 'products', 28, 'image', 'longsilog.jpg', '1755070752.jpg', 1, 'Administrator', 'Updated product: Longsilog', '2025-08-13 15:39:12'),
(203, 'batch_689c4128c113f', 'products', 37, 'image', 'leche_flan.jpg', '1755070760.png', 1, 'Administrator', 'Updated product: Leche Flan', '2025-08-13 15:39:20'),
(204, 'batch_689c413230d0b', 'products', 34, 'image', 'lasagna.jpg', '1755070770.jpg', 1, 'Administrator', 'Updated product: Lasagna', '2025-08-13 15:39:30'),
(205, 'batch_689c413b3996b', 'products', 15, 'image', 'kwek-kwek.jpg', '1755070779.jpg', 1, 'Administrator', 'Updated product: Kwek-Kwek', '2025-08-13 15:39:39'),
(206, 'batch_689c41456ea5a', 'products', 19, 'image', 'iced_tea.jpg', '1755070789.jpg', 1, 'Administrator', 'Updated product: Iced Tea', '2025-08-13 15:39:49'),
(207, 'batch_689c415164293', 'products', 18, 'image', 'ice_cream.jpg', '1755070801.jpg', 1, 'Administrator', 'Updated product: Ice Cream', '2025-08-13 15:40:01'),
(208, 'batch_689c41597f7ed', 'products', 10, 'image', 'hotdog_sandwich.jpg', '1755070809.jpg', 1, 'Administrator', 'Updated product: Hotdog Sandwich', '2025-08-13 15:40:09'),
(209, 'batch_689c416332e16', 'products', 23, 'image', 'hot_coffee.jpg', '1755070819.jpg', 1, 'Administrator', 'Updated product: Hot Coffee', '2025-08-13 15:40:19'),
(210, 'batch_689c416f73404', 'products', 47, 'image', 'ham_sandwich.jpg', '1755070831.jpg', 1, 'Administrator', 'Updated product: Ham Sandwich', '2025-08-13 15:40:31'),
(211, 'batch_689c4177bb42d', 'products', 25, 'image', 'halo-halo.jpg', '1755070839.jpg', 1, 'Administrator', 'Updated product: Halo-Halo', '2025-08-13 15:40:39'),
(212, 'batch_689c4180262c7', 'products', 33, 'image', 'grilled_fish.jpg', '1755070848.jpg', 1, 'Administrator', 'Updated product: Grilled Fish', '2025-08-13 15:40:48'),
(213, 'batch_689c4188d6dbc', 'products', 53, 'image', 'garlic_bread.jpg', '1755070856.jpg', 1, 'Administrator', 'Updated product: Garlic Bread', '2025-08-13 15:40:56'),
(214, 'batch_689c4193b7465', 'products', 22, 'image', 'fruit_shake.jpg', '1755070867.jpg', 1, 'Administrator', 'Updated product: Fruit Shake', '2025-08-13 15:41:07'),
(215, 'batch_689c419ed81ef', 'products', 50, 'image', 'fruit_salad.jpg', '1755070878.jpg', 1, 'Administrator', 'Updated product: Fruit Salad', '2025-08-13 15:41:18'),
(216, 'batch_689c41a94ce9c', 'products', 9, 'image', 'fries.jpg', '1755070889.jpg', 1, 'Administrator', 'Updated product: Fries', '2025-08-13 15:41:29'),
(217, 'batch_689c41b40cf0f', 'products', 11, 'image', 'fish_fillet.jpg', '1755070900.jpg', 1, 'Administrator', 'Updated product: Fish Fillet', '2025-08-13 15:41:40'),
(218, 'batch_689c41c5dce1f', 'products', 46, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 46', '2025-08-13 15:41:57'),
(219, 'batch_689c41d3167de', 'products', 39, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 39', '2025-08-13 15:42:11'),
(220, 'batch_689c41d31766f', 'products', 40, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 40', '2025-08-13 15:42:11'),
(221, 'batch_689c41df731bc', 'products', 51, 'image', 'corn_on_the_cob.jpg', '1755070943.jpg', 1, 'Administrator', 'Updated product: Corn on the Cob', '2025-08-13 15:42:23'),
(222, 'batch_689c41e77e950', 'products', 31, 'image', 'chicken_bbq.jpg', '1755070951.jpg', 1, 'Administrator', 'Updated product: Chicken BBQ', '2025-08-13 15:42:31'),
(223, 'batch_689c41f11577d', 'products', 43, 'image', 'chocolate_cake.jpg', '1755070961.jpg', 1, 'Administrator', 'Updated product: Chocolate Cake', '2025-08-13 15:42:41'),
(224, 'batch_689c41fa84c4b', 'products', 44, 'image', 'club_sandwich.jpg', '1755070970.jpg', 1, 'Administrator', 'Updated product: Club Sandwich', '2025-08-13 15:42:50'),
(225, 'batch_689cf881c653b', 'administrator', 1, 'image', 'admin_1_juan_dela_cruz_689af5fed3a24.jpg', 'admin_1_juan_dela_cruz_689cf881c557c.jpg', 1, 'Administrator', 'Updated profile picture for Administrator with ID: 1', '2025-08-14 04:41:37'),
(226, 'batch_689cf8a974a9c', 'administrator', 1, 'image', 'admin_1_juan_dela_cruz_689cf881c557c.jpg', 'admin_1_juan_dela_cruz_689cf8a972f97.jpg', 1, 'Administrator', 'Updated profile picture for Administrator with ID: 1', '2025-08-14 04:42:17'),
(227, 'batch_689cf8de97b11', 'products', 16, 'image', '1755070077.jpg', 'Banana Cue.jpg', 1, 'Administrator', 'Updated product: Banana Cue', '2025-08-14 04:43:10'),
(228, 'batch_689cf8f786cf2', 'products', 54, 'product_name', NULL, 'wow', 1, 'Administrator', 'Added new product: wow', '2025-08-14 04:43:35'),
(229, 'batch_689cf8f786cf2', 'products', 54, 'category', NULL, 'Snack', 1, 'Administrator', 'Added new product: wow', '2025-08-14 04:43:35'),
(230, 'batch_689cf8f786cf2', 'products', 54, 'description', NULL, 'dsad', 1, 'Administrator', 'Added new product: wow', '2025-08-14 04:43:35'),
(231, 'batch_689cf8f786cf2', 'products', 54, 'price', NULL, '23', 1, 'Administrator', 'Added new product: wow', '2025-08-14 04:43:35'),
(232, 'batch_689cf8f786cf2', 'products', 54, 'stock_quantity', NULL, '32', 1, 'Administrator', 'Added new product: wow', '2025-08-14 04:43:35'),
(233, 'batch_689cf8f786cf2', 'products', 54, 'unit', NULL, 'Bottle', 1, 'Administrator', 'Added new product: wow', '2025-08-14 04:43:35'),
(234, 'batch_689cf8f786cf2', 'products', 54, 'image', NULL, 'wow.jpg', 1, 'Administrator', 'Added new product: wow', '2025-08-14 04:43:35'),
(235, 'batch_689d6ffb9d13f', 'canteen_staff', 4, 'firstname', 'Canteen', 'Canteens', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:11:23'),
(236, 'batch_689d700a7c3cb', 'canteen_staff', 4, 'middlename', '', 's', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:11:38'),
(237, 'batch_689d700a7c3cb', 'canteen_staff', 4, 'lastname', 'Staff', 'Staffs', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:11:38'),
(238, 'batch_689d700a7c3cb', 'canteen_staff', 4, 'suffix', '', 's', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:11:38'),
(239, 'batch_689d700a7c3cb', 'canteen_staff', 4, 'email', 'canteenstaff@gmail.com', 'canteenstaffs@gmail.com', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:11:38'),
(240, 'batch_689d700fabb17', 'canteen_staff', 4, 'firstname', 'Canteens', 'Canteen', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:11:43'),
(241, 'batch_689d700fabb17', 'canteen_staff', 4, 'middlename', 's', '', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:11:43'),
(242, 'batch_689d700fabb17', 'canteen_staff', 4, 'lastname', 'Staffs', 'Staff', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:11:43'),
(243, 'batch_689d700fabb17', 'canteen_staff', 4, 'suffix', 's', '', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:11:43'),
(244, 'batch_689d7013bc58a', 'canteen_staff', 4, 'gender', 'Male', 'Female', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:11:47'),
(245, 'batch_689d7021d8506', 'canteen_staff', 4, 'birthdate', '2025-08-05', '1996-03-14', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:12:01'),
(246, 'batch_689d70284f1bb', 'canteen_staff', 4, 'nationality', 'dada', 'Filipino', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:12:08'),
(247, 'batch_689d702ef1ec7', 'canteen_staff', 4, 'contact', '9509972086', '9509972022', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:12:14'),
(248, 'batch_689d7033e25be', 'canteen_staff', 4, 'email', 'canteenstaffs@gmail.com', 'canteenstaff@gmail.com', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:12:19'),
(249, 'batch_689d70469d598', 'canteen_staff', 4, 'password', '[ENCRYPTED]', '[ENCRYPTED]', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:12:38'),
(250, 'batch_689d7253addc8', 'canteen_staff', 4, 'image', 'avatar.png', 'canteen-staff_4_canteen_staff_689d7253ab4ad.jpg', 4, 'Canteen Staff', 'Updated profile picture for Canteen Staff with ID: 4', '2025-08-14 13:21:23'),
(251, 'batch_689d730387af7', 'canteen_staff', 4, 'password', '[ENCRYPTED]', '[ENCRYPTED]', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-14 13:24:19'),
(252, 'batch_689d95e6e620a', 'office_staff', 3, 'firstname', 'Erwin', 'Erwins', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 15:53:10'),
(253, 'batch_689d960336311', 'office_staff', 3, 'middlename', '', 's', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 15:53:39'),
(254, 'batch_689d960336311', 'office_staff', 3, 'lastname', 'Son', 'Sons', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 15:53:39'),
(255, 'batch_689d960336311', 'office_staff', 3, 'suffix', '', 's', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 15:53:39'),
(256, 'batch_689d960336311', 'office_staff', 3, 'gender', 'Male', 'Female', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 15:53:39'),
(257, 'batch_689d960336311', 'office_staff', 3, 'birthdate', '2025-08-05', '2000-02-09', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 15:53:39'),
(258, 'batch_689d960336311', 'office_staff', 3, 'nationality', 'dsa', 'dsasss', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 15:53:39'),
(259, 'batch_689d960336311', 'office_staff', 3, 'contact', '9509972087', '9509972083', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 15:53:39'),
(260, 'batch_689d960336311', 'office_staff', 3, 'email', 'officestaff@gmail.com', 'officestaffs@gmail.com', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 15:53:39'),
(261, 'batch_689d96093e6b8', 'office_staff', 3, 'email', 'officestaffs@gmail.com', 'officestaff@gmail.com', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 15:53:45'),
(262, 'batch_689d97c2801a7', 'office_staff', 3, 'password', '[ENCRYPTED]', '[ENCRYPTED]', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 16:01:06'),
(263, 'batch_689d97e43c15e', 'office_staff', 3, 'password', '[ENCRYPTED]', '[ENCRYPTED]', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 16:01:40'),
(264, 'batch_689d9d5638457', 'office_staff', 3, 'image', 'avatar.png', 'office-staff_3_erwins_sons_689d9d5636dfa.jpg', 3, 'Office Staff', 'Updated profile picture for Office Staff with ID: 3', '2025-08-14 16:24:54'),
(265, 'batch_689d9fa8845e6', 'office_staff', 3, 'firstname', 'Erwins', 'Erwin', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 16:34:48'),
(266, 'batch_689d9fa8845e6', 'office_staff', 3, 'middlename', 's', '', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 16:34:48'),
(267, 'batch_689d9fa8845e6', 'office_staff', 3, 'lastname', 'Sons', 'Son', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 16:34:48'),
(268, 'batch_689d9fa8845e6', 'office_staff', 3, 'suffix', 's', '', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 16:34:48'),
(269, 'batch_689d9fa8845e6', 'office_staff', 3, 'gender', 'Female', 'Male', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 16:34:48'),
(270, 'batch_689d9fa8845e6', 'office_staff', 3, 'nationality', 'dsasss', 'Filipino', 3, 'Office Staff', 'Profile updated for Office Staff ID 3', '2025-08-14 16:34:48'),
(271, 'batch_689efa8806f97', 'office_staff', 8, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 8', '2025-08-15 17:14:48'),
(272, 'batch_689efa88107ef', 'office_staff', 7, 'is_deleted', '0', 'deleted', 1, 'Administrator', 'Deleted record ID: 7', '2025-08-15 17:14:48'),
(273, 'batch_689efc43ca746', 'canteen_staff', 4, 'image', 'canteen-staff_4_canteen_staff_689d7253ab4ad.jpg', 'canteen-staff_4_canteen_staff_689efc43c7e92.jpg', 4, 'Canteen Staff', 'Updated profile picture for Canteen Staff with ID: 4', '2025-08-15 17:22:11'),
(274, 'batch_689efc467de08', 'canteen_staff', 4, 'firstname', 'Canteen', 'Canteenj', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-15 17:22:14'),
(275, 'batch_689f003f2426c', 'office_staff', 3, 'image', 'office-staff_3_erwins_sons_689d9d5636dfa.jpg', 'office-staff_3_erwin_son_689f003f220e4.jpg', 3, 'Office Staff', 'Updated profile picture for Office Staff with ID: 3', '2025-08-15 17:39:11'),
(276, 'batch_68a30965be7d0', 'canteen_staff', 4, 'firstname', 'Canteenj', 'Canteen', 4, 'Canteen Staff', 'Profile updated for Canteen Staff ID 4', '2025-08-18 19:07:17'),
(277, 'batch_68f73fd8c762a', 'administrator', 1, 'image', 'admin_1_juan_dela_cruz_689cf8a972f97.jpg', 'admin_1_juan_dela_cruz_68f73fd8c5e4b.png', 1, 'Administrator', 'Updated profile picture for Administrator with ID: 1', '2025-10-21 16:10:00'),
(278, 'batch_693826a16045f', 'tblofficetype', 1, 'office_type_name', '', 'College', 11, 'Administrator', 'Created office type ID 1', '2025-12-09 21:39:45'),
(279, 'batch_693826a66bff3', 'tblofficetype', 1, 'office_type_name', 'College', 'Colleges', 11, 'Administrator', 'Updated office type ID 1', '2025-12-09 21:39:50'),
(280, 'batch_693826e1cdc64', 'tbloffice', 1, 'office_type_id', '', '1', 11, 'Administrator', 'Created office ID 1', '2025-12-09 21:40:49'),
(281, 'batch_693826e1cdc64', 'tbloffice', 1, 'office_name', '', 'asd', 11, 'Administrator', 'Created office ID 1', '2025-12-09 21:40:49'),
(282, 'batch_693826e1cdc64', 'tbloffice', 1, 'office_email', '', 'asd@gmail.com', 11, 'Administrator', 'Created office ID 1', '2025-12-09 21:40:49'),
(283, 'batch_69394d0de9655', 'tblofficetype', 2, 'office_type_name', '', 'Administration', 11, 'Administrator', 'Created office type ID 2', '2025-12-10 18:35:57'),
(284, 'batch_69394d2895bd4', 'tbloffice', 2, 'office_type_id', '', '1', 11, 'Administrator', 'Created office ID 2', '2025-12-10 18:36:24'),
(285, 'batch_69394d2895bd4', 'tbloffice', 2, 'office_name', '', 'CITE', 11, 'Administrator', 'Created office ID 2', '2025-12-10 18:36:24'),
(286, 'batch_69394d2895bd4', 'tbloffice', 2, 'office_email', '', 'vallechristianmark@gmail.com', 11, 'Administrator', 'Created office ID 2', '2025-12-10 18:36:24'),
(287, 'batch_693c13c9e5fa4', 'tbloffice', 3, 'office_type_id', '', '2', 11, 'Administrator', 'Created office ID 3', '2025-12-12 21:08:25'),
(288, 'batch_693c13c9e5fa4', 'tbloffice', 3, 'office_name', '', 'Sample administration', 11, 'Administrator', 'Created office ID 3', '2025-12-12 21:08:25'),
(289, 'batch_693c13c9e5fa4', 'tbloffice', 3, 'office_email', '', 'asdasd@gmail.com', 11, 'Administrator', 'Created office ID 3', '2025-12-12 21:08:25'),
(290, 'batch_693d59e02df92', 'orders', 55, 'job_order_no', '', 'JO0003-12', 13, 'Canteen Staff', 'Created order #JO0003-12', '2025-12-13 20:19:44'),
(291, 'batch_693d59e02df92', 'orders', 55, 'office_id', '', '3', 13, 'Canteen Staff', 'Created order #JO0003-12', '2025-12-13 20:19:44'),
(292, 'batch_693d59e02df92', 'orders', 55, 'office_name', '', 'Sample administration', 13, 'Canteen Staff', 'Created order #JO0003-12', '2025-12-13 20:19:44'),
(293, 'batch_693d59e02df92', 'orders', 55, 'total_amount', '', '12', 13, 'Canteen Staff', 'Created order #JO0003-12', '2025-12-13 20:19:44'),
(294, 'batch_693d59e02df92', 'orders', 55, 'status', '', '4', 13, 'Canteen Staff', 'Created order #JO0003-12', '2025-12-13 20:19:44'),
(295, 'batch_693d59e0332be', 'tblitem', 8, 'stock_qty', '1', '0', 13, 'Canteen Staff', 'Stock deducted for order #JO0003-12', '2025-12-13 20:19:44'),
(296, 'batch_693d5a1a02acc', 'order_status', 55, 'status', 'On-going', 'On-going', 18, 'Canteen Manager', 'Updated order #JO0003-12 status from On-going to On-going', '2025-12-13 20:20:42'),
(297, 'batch_693e288c273c2', 'tbluser', 19, 'user_type', '', 'Administrator', 11, 'Administrator', 'Created new Administrator: dsa dsa', '2025-12-14 11:01:32'),
(298, 'batch_693e288c273c2', 'tbluser', 19, 'full_name', '', 'dsa dsa dsa jr', 11, 'Administrator', 'Created new Administrator: dsa dsa', '2025-12-14 11:01:32'),
(299, 'batch_693e288c273c2', 'tbluser', 19, 'email', '', 'vallechristianmark@gmail.com', 11, 'Administrator', 'Created new Administrator: dsa dsa', '2025-12-14 11:01:32'),
(300, 'batch_693e2acccf8f9', 'tbluser', 20, 'user_type', '', 'Administrator', 11, 'Administrator', 'Created new Administrator: asd asd', '2025-12-14 11:11:08'),
(301, 'batch_693e2acccf8f9', 'tbluser', 20, 'full_name', '', 'asd sd asd asd', 11, 'Administrator', 'Created new Administrator: asd asd', '2025-12-14 11:11:08'),
(302, 'batch_693e2acccf8f9', 'tbluser', 20, 'email', '', 'vallechristianmark@gmail.com', 11, 'Administrator', 'Created new Administrator: asd asd', '2025-12-14 11:11:08'),
(303, 'batch_693e2e6d9746f', 'orders', 56, 'job_order_no', '', 'JO0004-12', 20, 'Administrator', 'Created order #JO0004-12', '2025-12-14 11:26:37'),
(304, 'batch_693e2e6d9746f', 'orders', 56, 'office_id', '', '3', 20, 'Administrator', 'Created order #JO0004-12', '2025-12-14 11:26:37'),
(305, 'batch_693e2e6d9746f', 'orders', 56, 'office_name', '', 'Sample administration', 20, 'Administrator', 'Created order #JO0004-12', '2025-12-14 11:26:37'),
(306, 'batch_693e2e6d9746f', 'orders', 56, 'total_amount', '', '160', 20, 'Administrator', 'Created order #JO0004-12', '2025-12-14 11:26:37'),
(307, 'batch_693e2e6d9746f', 'orders', 56, 'status', '', '1', 20, 'Administrator', 'Created order #JO0004-12', '2025-12-14 11:26:37'),
(308, 'batch_693e2e6d9a6bb', 'tblitem', 1, 'stock_qty', '100', '98', 20, 'Administrator', 'Stock deducted for order #JO0004-12', '2025-12-14 11:26:37'),
(309, 'batch_693e38b1f0d20', 'order_status', 53, 'status', 'On-going', 'Completed', 11, 'Administrator', 'Updated order #JO0001-12 status from On-going to Completed', '2025-12-14 12:10:25'),
(310, 'batch_693e3b60d8926', 'tblitem', 8, 'item_name', 'asd', 'sanny', 11, 'Administrator', 'Updated item: sanny', '2025-12-14 12:21:52'),
(311, 'batch_693e3b60d8926', 'tblitem', 8, 'stock_qty', '0', '3', 11, 'Administrator', 'Updated item: sanny', '2025-12-14 12:21:52'),
(312, 'batch_693e3c35822bf', 'orders', 57, 'job_order_no', '', 'JO0005-12', 18, 'Canteen Manager', 'Created order #JO0005-12', '2025-12-14 12:25:25'),
(313, 'batch_693e3c35822bf', 'orders', 57, 'office_id', '', '1', 18, 'Canteen Manager', 'Created order #JO0005-12', '2025-12-14 12:25:25'),
(314, 'batch_693e3c35822bf', 'orders', 57, 'office_name', '', 'asd', 18, 'Canteen Manager', 'Created order #JO0005-12', '2025-12-14 12:25:25'),
(315, 'batch_693e3c35822bf', 'orders', 57, 'total_amount', '', '3930', 18, 'Canteen Manager', 'Created order #JO0005-12', '2025-12-14 12:25:25'),
(316, 'batch_693e3c35822bf', 'orders', 57, 'status', '', '1', 18, 'Canteen Manager', 'Created order #JO0005-12', '2025-12-14 12:25:25'),
(317, 'batch_693e3c35867a6', 'tblitem', 1, 'stock_qty', '198', '186', 18, 'Canteen Manager', 'Stock deducted for order #JO0005-12', '2025-12-14 12:25:25'),
(318, 'batch_693e3c3587c83', 'tblitem', 4, 'stock_qty', '50', '17', 18, 'Canteen Manager', 'Stock deducted for order #JO0005-12', '2025-12-14 12:25:25'),
(319, 'batch_693e3ca1f19d8', 'orders', 58, 'job_order_no', '', 'JO0006-12', 13, 'Canteen Staff', 'Created order #JO0006-12', '2025-12-14 12:27:13'),
(320, 'batch_693e3ca1f19d8', 'orders', 58, 'office_id', '', '2', 13, 'Canteen Staff', 'Created order #JO0006-12', '2025-12-14 12:27:13'),
(321, 'batch_693e3ca1f19d8', 'orders', 58, 'office_name', '', 'CITE', 13, 'Canteen Staff', 'Created order #JO0006-12', '2025-12-14 12:27:13'),
(322, 'batch_693e3ca1f19d8', 'orders', 58, 'total_amount', '', '24', 13, 'Canteen Staff', 'Created order #JO0006-12', '2025-12-14 12:27:14'),
(323, 'batch_693e3ca1f19d8', 'orders', 58, 'status', '', '4', 13, 'Canteen Staff', 'Created order #JO0006-12', '2025-12-14 12:27:14'),
(324, 'batch_693e3ca202785', 'tblitem', 8, 'stock_qty', '3', '1', 13, 'Canteen Staff', 'Stock deducted for order #JO0006-12', '2025-12-14 12:27:14'),
(325, 'batch_693e42820709b', 'orders', 59, 'job_order_no', '', 'JO0007-12', 13, 'Canteen Staff', 'Created order #JO0007-12', '2025-12-14 12:52:18'),
(326, 'batch_693e42820709b', 'orders', 59, 'office_id', '', '3', 13, 'Canteen Staff', 'Created order #JO0007-12', '2025-12-14 12:52:18'),
(327, 'batch_693e42820709b', 'orders', 59, 'office_name', '', 'Sample administration', 13, 'Canteen Staff', 'Created order #JO0007-12', '2025-12-14 12:52:18'),
(328, 'batch_693e42820709b', 'orders', 59, 'total_amount', '', '80', 13, 'Canteen Staff', 'Created order #JO0007-12', '2025-12-14 12:52:18'),
(329, 'batch_693e42820709b', 'orders', 59, 'status', '', '4', 13, 'Canteen Staff', 'Created order #JO0007-12', '2025-12-14 12:52:18'),
(330, 'batch_693e42820c30f', 'tblitem', 1, 'stock_qty', '186', '185', 13, 'Canteen Staff', 'Stock deducted for order #JO0007-12', '2025-12-14 12:52:18'),
(331, 'batch_693e59b2514e8', 'tbluser', 20, 'firstname', 'asd', 'asddd', 11, 'Administrator', 'Updated Administrator profile', '2025-12-14 14:31:14'),
(332, 'batch_693e59ba365a9', 'tbluser', 20, 'firstname', 'asddd', 'asd', 11, 'Administrator', 'Updated Administrator profile', '2025-12-14 14:31:22'),
(333, 'batch_693ffc07a2a82', 'orders', 60, 'job_order_no', '', 'JO0008-12', 13, 'Canteen Staff', 'Created order #JO0008-12', '2025-12-15 20:16:07'),
(334, 'batch_693ffc07a2a82', 'orders', 60, 'office_id', '', '2', 13, 'Canteen Staff', 'Created order #JO0008-12', '2025-12-15 20:16:07'),
(335, 'batch_693ffc07a2a82', 'orders', 60, 'office_name', '', 'CITE', 13, 'Canteen Staff', 'Created order #JO0008-12', '2025-12-15 20:16:07'),
(336, 'batch_693ffc07a2a82', 'orders', 60, 'total_amount', '', '250', 13, 'Canteen Staff', 'Created order #JO0008-12', '2025-12-15 20:16:07'),
(337, 'batch_693ffc07a2a82', 'orders', 60, 'status', '', '4', 13, 'Canteen Staff', 'Created order #JO0008-12', '2025-12-15 20:16:07'),
(338, 'batch_693ffc07add1e', 'tblitem', 1, 'stock_qty', '185', '183', 13, 'Canteen Staff', 'Stock deducted for order #JO0008-12', '2025-12-15 20:16:07'),
(339, 'batch_693ffc07ae97d', 'tblitem', 4, 'stock_qty', '17', '16', 13, 'Canteen Staff', 'Stock deducted for order #JO0008-12', '2025-12-15 20:16:07'),
(340, 'batch_693ffc1a8bb8d', 'order_status', 58, 'status', 'On-going', 'On-going', 11, 'Administrator', 'Updated order #JO0006-12 status from On-going to On-going', '2025-12-15 20:16:26'),
(341, 'batch_693ffd773aca0', 'order_status', 59, 'status', 'On-going', 'On-going', 11, 'Administrator', 'Updated order #JO0007-12 status from On-going to On-going', '2025-12-15 20:22:15'),
(342, 'batch_693ffe06658ba', 'tbluser', 18, 'password', '[ENCRYPTED]', '[ENCRYPTED]', 11, 'Administrator', 'Updated Canteen Manager profile and password', '2025-12-15 20:24:38'),
(343, 'batch_693ffe398e1d2', 'orders', 61, 'job_order_no', '', 'JO0009-12', 13, 'Canteen Staff', 'Created order #JO0009-12', '2025-12-15 20:25:29'),
(344, 'batch_693ffe398e1d2', 'orders', 61, 'office_id', '', '2', 13, 'Canteen Staff', 'Created order #JO0009-12', '2025-12-15 20:25:29'),
(345, 'batch_693ffe398e1d2', 'orders', 61, 'office_name', '', 'CITE', 13, 'Canteen Staff', 'Created order #JO0009-12', '2025-12-15 20:25:29'),
(346, 'batch_693ffe398e1d2', 'orders', 61, 'total_amount', '', '80', 13, 'Canteen Staff', 'Created order #JO0009-12', '2025-12-15 20:25:29'),
(347, 'batch_693ffe398e1d2', 'orders', 61, 'status', '', '4', 13, 'Canteen Staff', 'Created order #JO0009-12', '2025-12-15 20:25:29'),
(348, 'batch_693ffe3992164', 'tblitem', 1, 'stock_qty', '183', '182', 13, 'Canteen Staff', 'Stock deducted for order #JO0009-12', '2025-12-15 20:25:29'),
(349, 'batch_693ffe4c1eafa', 'order_status', 60, 'status', 'On-going', 'On-going', 18, 'Canteen Manager', 'Updated order #JO0008-12 status from On-going to On-going', '2025-12-15 20:25:48'),
(350, 'batch_6940028554080', 'orders', 62, 'job_order_no', '', 'JO0010-12', 18, 'Canteen Manager', 'Created order #JO0010-12', '2025-12-15 20:43:49'),
(351, 'batch_6940028554080', 'orders', 62, 'office_id', '', '2', 18, 'Canteen Manager', 'Created order #JO0010-12', '2025-12-15 20:43:49'),
(352, 'batch_6940028554080', 'orders', 62, 'office_name', '', 'CITE', 18, 'Canteen Manager', 'Created order #JO0010-12', '2025-12-15 20:43:49'),
(353, 'batch_6940028554080', 'orders', 62, 'total_amount', '', '182', 18, 'Canteen Manager', 'Created order #JO0010-12', '2025-12-15 20:43:49'),
(354, 'batch_6940028554080', 'orders', 62, 'status', '', '1', 18, 'Canteen Manager', 'Created order #JO0010-12', '2025-12-15 20:43:49'),
(355, 'batch_69400285584a5', 'tblitem', 1, 'stock_qty', '182', '181', 18, 'Canteen Manager', 'Stock deducted for order #JO0010-12', '2025-12-15 20:43:49'),
(356, 'batch_6940028559378', 'tblitem', 4, 'stock_qty', '16', '15', 18, 'Canteen Manager', 'Stock deducted for order #JO0010-12', '2025-12-15 20:43:49'),
(357, 'batch_694002855a1ce', 'tblitem', 8, 'stock_qty', '1', '0', 18, 'Canteen Manager', 'Stock deducted for order #JO0010-12', '2025-12-15 20:43:49'),
(358, 'batch_69400414095c8', 'orders', 63, 'job_order_no', '', 'JO0011-12', 18, 'Canteen Manager', 'Created order #JO0011-12', '2025-12-15 20:50:28'),
(359, 'batch_69400414095c8', 'orders', 63, 'office_id', '', '2', 18, 'Canteen Manager', 'Created order #JO0011-12', '2025-12-15 20:50:28'),
(360, 'batch_69400414095c8', 'orders', 63, 'office_name', '', 'CITE', 18, 'Canteen Manager', 'Created order #JO0011-12', '2025-12-15 20:50:28'),
(361, 'batch_69400414095c8', 'orders', 63, 'total_amount', '', '160', 18, 'Canteen Manager', 'Created order #JO0011-12', '2025-12-15 20:50:28'),
(362, 'batch_69400414095c8', 'orders', 63, 'status', '', '1', 18, 'Canteen Manager', 'Created order #JO0011-12', '2025-12-15 20:50:28'),
(363, 'batch_694004140d6b5', 'tblitem', 1, 'stock_qty', '181', '179', 18, 'Canteen Manager', 'Stock deducted for order #JO0011-12', '2025-12-15 20:50:28'),
(364, 'batch_6940043210115', 'order_status', 54, 'status', 'Approved', 'Completed', 18, 'Canteen Manager', 'Updated order #JO0002-12 status from Approved to Completed', '2025-12-15 20:50:58'),
(365, 'batch_6940046d24dac', 'orders', 64, 'job_order_no', '', 'JO0012-12', 11, 'Administrator', 'Created order #JO0012-12', '2025-12-15 20:51:57'),
(366, 'batch_6940046d24dac', 'orders', 64, 'office_id', '', '2', 11, 'Administrator', 'Created order #JO0012-12', '2025-12-15 20:51:57'),
(367, 'batch_6940046d24dac', 'orders', 64, 'office_name', '', 'CITE', 11, 'Administrator', 'Created order #JO0012-12', '2025-12-15 20:51:57'),
(368, 'batch_6940046d24dac', 'orders', 64, 'total_amount', '', '80', 11, 'Administrator', 'Created order #JO0012-12', '2025-12-15 20:51:57'),
(369, 'batch_6940046d24dac', 'orders', 64, 'status', '', '1', 11, 'Administrator', 'Created order #JO0012-12', '2025-12-15 20:51:57'),
(370, 'batch_6940046d29502', 'tblitem', 1, 'stock_qty', '179', '178', 11, 'Administrator', 'Stock deducted for order #JO0012-12', '2025-12-15 20:51:57'),
(371, 'batch_69400480307be', 'order_status', 61, 'status', 'On-going', 'On-going', 18, 'Canteen Manager', 'Updated order #JO0009-12 status from On-going to On-going', '2025-12-15 20:52:16'),
(372, 'batch_694004a050232', 'order_status', 56, 'status', 'On-going', 'Completed', 18, 'Canteen Manager', 'Updated order #JO0004-12 status from On-going to Completed', '2025-12-15 20:52:48'),
(373, 'batch_694004ad0d194', 'order_status', 58, 'status', 'Approved', 'Completed', 18, 'Canteen Manager', 'Updated order #JO0006-12 status from Approved to Completed', '2025-12-15 20:53:01'),
(374, 'batch_69400520a194b', 'orders', 65, 'job_order_no', '', 'JO0013-12', 11, 'Administrator', 'Created order #JO0013-12', '2025-12-15 20:54:56'),
(375, 'batch_69400520a194b', 'orders', 65, 'office_id', '', '2', 11, 'Administrator', 'Created order #JO0013-12', '2025-12-15 20:54:56'),
(376, 'batch_69400520a194b', 'orders', 65, 'office_name', '', 'CITE', 11, 'Administrator', 'Created order #JO0013-12', '2025-12-15 20:54:56'),
(377, 'batch_69400520a194b', 'orders', 65, 'total_amount', '', '260', 11, 'Administrator', 'Created order #JO0013-12', '2025-12-15 20:54:56'),
(378, 'batch_69400520a194b', 'orders', 65, 'status', '', '1', 11, 'Administrator', 'Created order #JO0013-12', '2025-12-15 20:54:56'),
(379, 'batch_69400520a4cc5', 'tblitem', 1, 'stock_qty', '178', '177', 11, 'Administrator', 'Stock deducted for order #JO0013-12', '2025-12-15 20:54:56'),
(380, 'batch_69400520a5942', 'tblitem', 4, 'stock_qty', '15', '13', 11, 'Administrator', 'Stock deducted for order #JO0013-12', '2025-12-15 20:54:56'),
(381, 'batch_69400652757a5', 'orders', 69, 'job_order_no', '', 'JO0014-12', 11, 'Administrator', 'Created order #JO0014-12', '2025-12-15 21:00:02'),
(382, 'batch_69400652757a5', 'orders', 69, 'office_id', '', NULL, 11, 'Administrator', 'Created order #JO0014-12', '2025-12-15 21:00:02'),
(383, 'batch_69400652757a5', 'orders', 69, 'office_name', '', 'asd@gmail.com', 11, 'Administrator', 'Created order #JO0014-12', '2025-12-15 21:00:02'),
(384, 'batch_69400652757a5', 'orders', 69, 'total_amount', '', '80', 11, 'Administrator', 'Created order #JO0014-12', '2025-12-15 21:00:02'),
(385, 'batch_69400652757a5', 'orders', 69, 'status', '', '1', 11, 'Administrator', 'Created order #JO0014-12', '2025-12-15 21:00:02'),
(386, 'batch_6940065278f2d', 'tblitem', 1, 'stock_qty', '177', '176', 11, 'Administrator', 'Stock deducted for order #JO0014-12', '2025-12-15 21:00:02'),
(387, 'batch_694007ec99333', 'tbluser', 20, 'firstname', 'asd', 'asddd', 11, 'Administrator', 'Updated Administrator profile', '2025-12-15 21:06:52'),
(388, 'batch_69412924e96e6', 'tblofficetype', 1, 'office_type_name', 'Colleges', 'SCITC', 11, 'Administrator', 'Updated office type ID 1', '2025-12-16 17:40:52'),
(389, 'batch_69412940315d9', 'tbloffice', 4, 'office_type_id', '', '1', 11, 'Administrator', 'Created office ID 4', '2025-12-16 17:41:20'),
(390, 'batch_69412940315d9', 'tbloffice', 4, 'office_name', '', 'COMSCI', 11, 'Administrator', 'Created office ID 4', '2025-12-16 17:41:20'),
(391, 'batch_69412940315d9', 'tbloffice', 4, 'office_email', '', 'asd@gmaill.com', 11, 'Administrator', 'Created office ID 4', '2025-12-16 17:41:20'),
(392, 'batch_6941298079833', 'tblofficetype', 3, 'office_type_name', '', 'Finace', 11, 'Administrator', 'Created office type ID 3', '2025-12-16 17:42:24'),
(393, 'batch_6941299ec8800', 'tbloffice', 5, 'office_type_id', '', '3', 11, 'Administrator', 'Created office ID 5', '2025-12-16 17:42:54'),
(394, 'batch_6941299ec8800', 'tbloffice', 5, 'office_name', '', 'CSM', 11, 'Administrator', 'Created office ID 5', '2025-12-16 17:42:54'),
(395, 'batch_6941299ec8800', 'tbloffice', 5, 'office_email', '', 'asjdhsajdh@gmail.com', 11, 'Administrator', 'Created office ID 5', '2025-12-16 17:42:54'),
(396, 'batch_69416f13b2021', 'tbloffice', 6, 'office_type_id', '', '2', 11, 'Administrator', 'Created office ID 6', '2025-12-16 22:39:15'),
(397, 'batch_69416f13b2021', 'tbloffice', 6, 'office_name', '', 'CITC', 11, 'Administrator', 'Created office ID 6', '2025-12-16 22:39:15'),
(398, 'batch_69416f13b2021', 'tbloffice', 6, 'office_email', '', 'vallechristianmark@gmail.com', 11, 'Administrator', 'Created office ID 6', '2025-12-16 22:39:15'),
(399, 'batch_69416f13b558f', 'tbl_office_under', 1, 'office_id', '', '6', 11, 'Administrator', 'Created sub office ID 1 for office ID 6', '2025-12-16 22:39:15'),
(400, 'batch_69416f13b558f', 'tbl_office_under', 1, 'office_under_name', '', 'IT', 11, 'Administrator', 'Created sub office ID 1 for office ID 6', '2025-12-16 22:39:15'),
(401, 'batch_69416f13b85e7', 'tbl_office_under', 2, 'office_id', '', '6', 11, 'Administrator', 'Created sub office ID 2 for office ID 6', '2025-12-16 22:39:15'),
(402, 'batch_69416f13b85e7', 'tbl_office_under', 2, 'office_under_name', '', 'COMSCI', 11, 'Administrator', 'Created sub office ID 2 for office ID 6', '2025-12-16 22:39:15'),
(403, 'batch_694176310d42b', 'tbl_office_under', 3, 'office_id', '', '6', 11, 'Administrator', 'Created sub office ID 3 for office ID 6', '2025-12-16 23:09:37'),
(404, 'batch_694176310d42b', 'tbl_office_under', 3, 'office_under_name', '', 'asd', 11, 'Administrator', 'Created sub office ID 3 for office ID 6', '2025-12-16 23:09:37'),
(405, 'batch_694177ff5fae7', 'orders', 70, 'job_order_no', '', 'JO0015-12', 13, 'Canteen Staff', 'Created order #JO0015-12', '2025-12-16 23:17:19'),
(406, 'batch_694177ff5fae7', 'orders', 70, 'office_id', '', '6', 13, 'Canteen Staff', 'Created order #JO0015-12', '2025-12-16 23:17:19'),
(407, 'batch_694177ff5fae7', 'orders', 70, 'office_name', '', 'CITC', 13, 'Canteen Staff', 'Created order #JO0015-12', '2025-12-16 23:17:19'),
(408, 'batch_694177ff5fae7', 'orders', 70, 'office_under_id', '', '3', 13, 'Canteen Staff', 'Created order #JO0015-12', '2025-12-16 23:17:19'),
(409, 'batch_694177ff5fae7', 'orders', 70, 'total_amount', '', '330', 13, 'Canteen Staff', 'Created order #JO0015-12', '2025-12-16 23:17:19'),
(410, 'batch_694177ff5fae7', 'orders', 70, 'status', '', '4', 13, 'Canteen Staff', 'Created order #JO0015-12', '2025-12-16 23:17:19'),
(411, 'batch_694177ff6a680', 'tblitem', 1, 'stock_qty', '176', '173', 13, 'Canteen Staff', 'Stock deducted for order #JO0015-12', '2025-12-16 23:17:19'),
(412, 'batch_694177ff6be1d', 'tblitem', 4, 'stock_qty', '13', '12', 13, 'Canteen Staff', 'Stock deducted for order #JO0015-12', '2025-12-16 23:17:19'),
(413, 'batch_69417960cf66c', 'order_status', 70, 'status', 'On-going', 'On-going', 11, 'Administrator', 'Updated order #JO0015-12 status from On-going to On-going', '2025-12-16 23:23:12'),
(414, 'batch_69417f4fc233d', 'tblofficetype', 1, 'office_type_name', 'SCITC', 'College', 11, 'Administrator', 'Updated office type ID 1', '2025-12-16 23:48:31'),
(415, 'batch_69418081b34c5', 'tbloffice', 3, 'office_email', 'asdasd@gmail.com', 'asd@ustp.edu.ph', 11, 'Administrator', 'Updated office ID 3', '2025-12-16 23:53:37'),
(416, 'batch_6941808b5ee33', 'tbloffice', 6, 'office_email', 'vallechristianmark@gmail.com', 'vallechristianmarkasd@ustp.edu.ph', 11, 'Administrator', 'Updated office ID 6', '2025-12-16 23:53:47');

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `admin_ID` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `birthdate` date NOT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_password_changed` tinyint(1) DEFAULT 0,
  `verification_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `canteen_staff`
--

CREATE TABLE `canteen_staff` (
  `canteen_staff_ID` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `birthdate` date NOT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_password_changed` tinyint(1) DEFAULT 0,
  `verification_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `attempt_ID` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`attempt_ID`, `email`, `attempts`, `last_attempt`) VALUES
(25, 'officestaff@gmail.com', 5, '2025-12-10 08:49:52'),
(26, 'admin@gmail.com', 2, '2025-12-10 08:49:26');

-- --------------------------------------------------------

--
-- Table structure for table `log_history`
--

CREATE TABLE `log_history` (
  `log_Id` int(11) NOT NULL,
  `user_ID` int(11) DEFAULT NULL,
  `uin` varchar(50) DEFAULT NULL,
  `login_datetime` datetime NOT NULL,
  `logout_datetime` datetime DEFAULT NULL,
  `logout_remarks` int(11) NOT NULL DEFAULT 0 COMMENT '0=Logged out successfully, 1=Unable to logout last login',
  `usertype` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `log_history`
--

INSERT INTO `log_history` (`log_Id`, `user_ID`, `uin`, `login_datetime`, `logout_datetime`, `logout_remarks`, `usertype`) VALUES
(1153, 11, '20251210091841ab1d0e14', '2025-12-10 09:18:41', NULL, 0, NULL),
(1154, 11, '2025121010431992914641', '2025-12-10 10:43:19', NULL, 0, NULL),
(1155, 11, '20251210111931581fcf6e', '2025-12-10 11:19:31', NULL, 0, NULL),
(1156, 11, '202512101322204a2c25c7', '2025-12-10 13:22:20', NULL, 0, NULL),
(1157, 11, '20251210150821df81e5c0', '2025-12-10 15:08:21', NULL, 0, NULL),
(1158, 11, '202512101528157cd2ba75', '2025-12-10 15:28:15', NULL, 0, NULL),
(1159, 12, '20251210160130641adc68', '2025-12-10 16:01:30', NULL, 0, NULL),
(1160, 11, '202512101835214cc773dd', '2025-12-10 18:35:21', NULL, 0, NULL),
(1161, 13, '202512101838521a65efdf', '2025-12-10 18:38:52', NULL, 0, NULL),
(1162, 11, '2025121212143208625dc7', '2025-12-12 12:14:32', '2025-12-12 12:35:24', 0, NULL),
(1163, 18, '2025121212253116068ba8', '2025-12-12 12:25:31', NULL, 0, NULL),
(1164, 18, '202512121227416f88f421', '2025-12-12 12:27:41', NULL, 0, NULL),
(1165, 13, '20251212123538d5f32cc7', '2025-12-12 12:35:38', NULL, 0, NULL),
(1166, 12, '20251212125548b348a51e', '2025-12-12 12:55:48', NULL, 0, NULL),
(1167, 18, '20251212130519471aaca9', '2025-12-12 13:05:19', NULL, 0, NULL),
(1168, 11, '20251212143355be8dd495', '2025-12-12 14:33:55', NULL, 0, NULL),
(1169, 13, '202512121434332cecd7fb', '2025-12-12 14:34:33', NULL, 0, NULL),
(1170, 11, '2025121219204816b5aa42', '2025-12-12 19:20:48', NULL, 0, NULL),
(1171, 11, '20251212192048a9ba0f27', '2025-12-12 19:20:48', NULL, 0, NULL),
(1172, 11, '20251212192048cdc0a43c', '2025-12-12 19:20:48', NULL, 0, NULL),
(1173, 11, '20251212192048be5bc2c1', '2025-12-12 19:20:48', NULL, 0, NULL),
(1174, 11, '20251212192049b3b79b0c', '2025-12-12 19:20:49', NULL, 0, NULL),
(1175, 18, '20251212203903f46f98ef', '2025-12-12 20:39:03', NULL, 0, NULL),
(1176, 13, '20251212203917461e8f59', '2025-12-12 20:39:17', '2025-12-12 20:49:18', 0, NULL),
(1177, 11, '20251212210759072c8bbd', '2025-12-12 21:07:59', NULL, 0, NULL),
(1178, 13, '20251212220758bcb4760d', '2025-12-12 22:07:58', NULL, 0, NULL),
(1179, 11, '20251213084458a9acde1f', '2025-12-13 08:44:58', NULL, 0, NULL),
(1180, 13, '20251213084520927c40d8', '2025-12-13 08:45:20', NULL, 0, NULL),
(1181, 13, '20251213095839449b5544', '2025-12-13 09:58:39', '2025-12-13 10:14:23', 0, NULL),
(1182, 18, '2025121310142805ce83d4', '2025-12-13 10:14:28', NULL, 0, NULL),
(1183, 18, '20251213103818c4f07a89', '2025-12-13 10:38:18', NULL, 0, NULL),
(1184, 13, '20251213130458b19b4a53', '2025-12-13 13:04:58', '2025-12-13 13:05:38', 0, NULL),
(1185, 13, '2025121313055138d3b55b', '2025-12-13 13:05:51', '2025-12-13 13:29:07', 0, NULL),
(1186, 11, '202512131312020419bbf5', '2025-12-13 13:12:02', NULL, 0, NULL),
(1187, 11, '20251213132916471692ea', '2025-12-13 13:29:16', '2025-12-13 13:31:35', 0, NULL),
(1188, 13, '202512131330195579fefd', '2025-12-13 13:30:19', '2025-12-13 13:30:43', 0, NULL),
(1189, 18, '2025121313304915469d79', '2025-12-13 13:30:49', NULL, 0, NULL),
(1190, 11, '202512131332128600241e', '2025-12-13 13:32:12', NULL, 0, NULL),
(1191, 11, '20251213190156333dd0ac', '2025-12-13 19:01:56', NULL, 0, NULL),
(1192, 13, '20251213191003312abd17', '2025-12-13 19:10:03', NULL, 0, NULL),
(1193, 13, '2025121320192625734a84', '2025-12-13 20:19:26', NULL, 0, NULL),
(1194, 18, '2025121320203613909d66', '2025-12-13 20:20:36', '2025-12-13 20:20:49', 0, NULL),
(1195, 11, '20251214110007adf1c692', '2025-12-14 11:00:07', NULL, 0, NULL),
(1196, 20, '2025121411134375a43a20', '2025-12-14 11:13:43', NULL, 0, NULL),
(1197, 11, '2025121411313098e58c0c', '2025-12-14 11:31:30', '2025-12-14 11:31:53', 0, NULL),
(1198, 11, '20251214113241f8ba457b', '2025-12-14 11:32:41', '2025-12-14 11:32:49', 0, NULL),
(1199, 20, '20251214113254a2a5d712', '2025-12-14 11:32:54', '2025-12-14 11:34:07', 0, NULL),
(1200, 11, '20251214114714d51ba4ed', '2025-12-14 11:47:14', NULL, 0, NULL),
(1201, 13, '202512141159485224ff07', '2025-12-14 11:59:48', '2025-12-14 12:01:48', 0, NULL),
(1202, 13, '20251214120250684d7839', '2025-12-14 12:02:50', '2025-12-14 12:22:53', 0, NULL),
(1203, 18, '2025121412234739362d1c', '2025-12-14 12:23:47', '2025-12-14 12:26:10', 0, NULL),
(1204, 13, '20251214122623ac31a629', '2025-12-14 12:26:23', '2025-12-14 12:28:24', 0, NULL),
(1205, 13, '20251214124839c397d5ef', '2025-12-14 12:48:39', '2025-12-14 12:56:17', 0, NULL),
(1206, 20, '202512141300009239587c', '2025-12-14 13:00:00', NULL, 0, NULL),
(1207, 11, '202512141342581de78232', '2025-12-14 13:42:58', NULL, 0, NULL),
(1208, 11, '202512141427570f8f308c', '2025-12-14 14:27:57', NULL, 0, NULL),
(1209, 11, '20251215141401b644f2af', '2025-12-15 14:14:01', NULL, 0, NULL),
(1210, 11, '202512151436461f2ad07c', '2025-12-15 14:36:46', NULL, 0, NULL),
(1211, 13, '20251215154856ed88f090', '2025-12-15 15:48:56', NULL, 0, NULL),
(1212, 11, '20251215195718f5e847f4', '2025-12-15 19:57:18', '2025-12-15 20:01:15', 0, NULL),
(1213, 13, '20251215200124ee045f4b', '2025-12-15 20:01:24', NULL, 0, NULL),
(1214, 13, '202512152001247f252653', '2025-12-15 20:01:24', '2025-12-15 20:24:13', 0, NULL),
(1215, 11, '202512152014149d64d2ce', '2025-12-15 20:14:14', '2025-12-15 20:23:53', 0, NULL),
(1216, 11, '202512152024227c9ed93c', '2025-12-15 20:24:22', '2025-12-15 20:24:55', 0, NULL),
(1217, 18, '20251215202443ff20dffc', '2025-12-15 20:24:43', '2025-12-15 20:55:11', 0, NULL),
(1218, 13, '20251215202505892c711e', '2025-12-15 20:25:05', NULL, 0, NULL),
(1219, 13, '202512152039104e817620', '2025-12-15 20:39:10', '2025-12-15 20:39:47', 0, NULL),
(1220, 11, '202512152045356aa0bed6', '2025-12-15 20:45:35', NULL, 0, NULL),
(1221, 11, '20251215205524e25d2359', '2025-12-15 20:55:24', '2025-12-15 21:00:31', 0, NULL),
(1222, 11, '20251216174024e1c22645', '2025-12-16 17:40:24', NULL, 0, NULL),
(1223, 13, '20251216182655a327fd8e', '2025-12-16 18:26:55', '2025-12-16 18:27:21', 0, NULL),
(1224, 13, '2025121618272720c087a6', '2025-12-16 18:27:27', '2025-12-16 18:33:20', 0, NULL),
(1225, 13, '2025121618332610babae8', '2025-12-16 18:33:26', NULL, 0, NULL),
(1226, 18, '20251216184050e587044e', '2025-12-16 18:40:50', NULL, 0, NULL),
(1227, 11, '20251216200056cbc6f614', '2025-12-16 20:00:56', NULL, 0, NULL),
(1228, 11, '20251216205920ccdcce9d', '2025-12-16 20:59:20', NULL, 0, NULL),
(1229, 11, '2025121623013452d706a0', '2025-12-16 23:01:34', NULL, 0, NULL),
(1230, 11, '202512162301346feb765b', '2025-12-16 23:01:34', NULL, 0, NULL),
(1231, 13, '20251216231021af48201b', '2025-12-16 23:10:21', '2025-12-16 23:27:35', 0, NULL),
(1232, 13, '20251216233453eb7a5785', '2025-12-16 23:34:53', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `job_order_no` varchar(255) DEFAULT NULL,
  `office_name` varchar(255) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `office_under_id` int(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `needed_datetime` varchar(255) NOT NULL,
  `event` text NOT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `job_order_no`, `office_name`, `office_id`, `office_under_id`, `email`, `needed_datetime`, `event`, `total_amount`, `created_at`, `updated_at`) VALUES
(53, 'JO0001-12', NULL, 3, NULL, NULL, '2025-12-15 10:19:00 | 2025-12-17 10:19:00 | 2025-12-18 10:19:00 | 2025-12-19 10:19:00 | 2025-12-26 10:19:00', 'asd', 80.00, '2025-12-13 02:19:34', '2025-12-13 02:19:34'),
(54, 'JO0002-12', 'Sample administration', 3, NULL, 'asdasd@gmail.com', '2025-12-15 07:25:00 | 2025-12-17 07:25:00', 'asd', 80.00, '2025-12-13 11:24:39', '2025-12-13 11:24:39'),
(55, 'JO0003-12', 'Sample administration', 3, NULL, 'asdasd@gmail.com', '2025-12-16 08:20:00 | 2025-12-17 08:20:00 | 2025-12-19 08:20:00', 'asd', 12.00, '2025-12-13 12:19:44', '2025-12-13 12:19:44'),
(56, 'JO0004-12', 'Sample administration', 3, NULL, 'asdasd@gmail.com', '2025-12-16 11:27:00 | 2025-12-17 11:27:00 | 2025-12-25 11:27:00 | 2025-12-26 11:27:00', 'asd', 160.00, '2025-12-14 03:26:37', '2025-12-14 03:26:37'),
(57, 'JO0005-12', 'asd', 1, NULL, 'asd@gmail.com', '2025-12-15 15:24:00 | 2025-12-19 15:24:00 | 2025-12-16 15:24:00 | 2025-12-17 15:24:00', 'sdsf', 3930.00, '2025-12-14 04:25:25', '2025-12-14 04:25:25'),
(58, 'JO0006-12', 'CITE', 2, NULL, 'vallechristianmark@gmail.com', '2025-12-22 14:26:00 | 2025-12-26 14:26:00 | 2025-12-23 14:26:00 | 2025-12-24 14:26:00 | 2025-12-25 14:26:00', 'sds', 24.00, '2025-12-14 04:27:13', '2025-12-14 04:27:13'),
(59, 'JO0007-12', 'Sample administration', 3, NULL, 'asdasd@gmail.com', '2025-12-15 12:52:00 | 2025-12-16 12:52:00 | 2025-12-19 12:52:00 | 2025-12-17 12:52:00', 'asd', 80.00, '2025-12-14 04:52:17', '2025-12-14 04:52:17'),
(60, 'JO0008-12', 'CITE', 2, NULL, 'vallechristianmark@gmail.com', '2025-12-22 08:15:00 | 2025-12-24 08:15:00 | 2025-12-23 08:15:00', 'asd', 250.00, '2025-12-15 12:16:07', '2025-12-15 12:16:07'),
(61, 'JO0009-12', 'CITE', 2, NULL, 'vallechristianmark@gmail.com', '2025-12-16 08:25:00', 'asd', 80.00, '2025-12-15 12:25:29', '2025-12-15 12:25:29'),
(62, 'JO0010-12', 'CITE', 2, NULL, 'vallechristianmark@gmail.com', '2025-12-16 08:43:00 | 2025-12-17 08:43:00', 'asd', 182.00, '2025-12-15 12:43:49', '2025-12-15 12:43:49'),
(63, 'JO0011-12', 'CITE', 2, NULL, 'vallechristianmark@gmail.com', '2025-12-17 08:50:00 | 2025-12-16 08:50:00 | 2025-12-18 08:50:00', 'asd', 160.00, '2025-12-15 12:50:27', '2025-12-15 12:50:27'),
(64, 'JO0012-12', 'CITE', 2, NULL, 'vallechristianmark@gmail.com', '2025-12-17 08:51:00', 'AS', 80.00, '2025-12-15 12:51:57', '2025-12-15 12:51:57'),
(65, 'JO0013-12', 'CITE', 2, NULL, 'vallechristianmark@gmail.com', '2025-12-16 08:54:00 | 2025-12-17 08:54:00 | 2025-12-18 08:54:00', 'ASD', 260.00, '2025-12-15 12:54:56', '2025-12-15 12:54:56'),
(69, 'JO0014-12', 'asd@gmail.com', NULL, NULL, 'asd@gmail.com', '2025-12-16 08:56:00', 'asd', 80.00, '2025-12-15 13:00:02', '2025-12-15 13:00:02'),
(70, 'JO0015-12', 'CITC', 6, 3, 'vallechristianmark@gmail.com', '2025-12-17 11:10:00 | 2025-12-18 11:10:00', 'asd', 330.00, '2025-12-16 15:17:19', '2025-12-16 15:17:19');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `items_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `total` decimal(10,0) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `items_id`, `quantity`, `total`, `remarks`, `created_at`, `updated_at`) VALUES
(63, 53, 1, 1, 80, 'asd', '2025-12-13 02:19:34', '2025-12-13 02:19:34'),
(64, 54, 1, 1, 80, '', '2025-12-13 11:24:39', '2025-12-13 11:24:39'),
(65, 55, 8, 1, 12, '', '2025-12-13 12:19:44', '2025-12-13 12:19:44'),
(66, 56, 1, 2, 160, 'asd', '2025-12-14 03:26:37', '2025-12-14 03:26:37'),
(67, 57, 1, 12, 960, 'dsa', '2025-12-14 04:25:25', '2025-12-14 04:25:25'),
(68, 57, 4, 33, 2970, '', '2025-12-14 04:25:25', '2025-12-14 04:25:25'),
(69, 58, 8, 2, 24, 'dsfds', '2025-12-14 04:27:13', '2025-12-14 04:27:13'),
(70, 59, 1, 1, 80, '', '2025-12-14 04:52:18', '2025-12-14 04:52:18'),
(71, 60, 1, 2, 160, '', '2025-12-15 12:16:07', '2025-12-15 12:16:07'),
(72, 60, 4, 1, 90, '', '2025-12-15 12:16:07', '2025-12-15 12:16:07'),
(73, 61, 1, 1, 80, '', '2025-12-15 12:25:29', '2025-12-15 12:25:29'),
(74, 62, 1, 1, 80, 'add one coke', '2025-12-15 12:43:49', '2025-12-15 12:43:49'),
(75, 62, 4, 1, 90, 'asd', '2025-12-15 12:43:49', '2025-12-15 12:43:49'),
(76, 62, 8, 1, 12, 'asd', '2025-12-15 12:43:49', '2025-12-15 12:43:49'),
(77, 63, 1, 2, 160, '', '2025-12-15 12:50:28', '2025-12-15 12:50:28'),
(78, 64, 1, 1, 80, '', '2025-12-15 12:51:57', '2025-12-15 12:51:57'),
(79, 65, 1, 1, 80, 'ASD', '2025-12-15 12:54:56', '2025-12-15 12:54:56'),
(80, 65, 4, 2, 180, 'ASD', '2025-12-15 12:54:56', '2025-12-15 12:54:56'),
(81, 69, 1, 1, 80, '', '2025-12-15 13:00:02', '2025-12-15 13:00:02'),
(82, 70, 1, 3, 240, '', '2025-12-16 15:17:19', '2025-12-16 15:17:19'),
(83, 70, 4, 1, 90, '', '2025-12-16 15:17:19', '2025-12-16 15:17:19');

-- --------------------------------------------------------

--
-- Table structure for table `order_reminders`
--

CREATE TABLE `order_reminders` (
  `reminder_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `job_order_no` varchar(255) NOT NULL,
  `office_email` varchar(100) NOT NULL,
  `message` text DEFAULT 'Please follow up on this order',
  `is_acknowledged` tinyint(1) DEFAULT 0,
  `acknowledged_by` int(11) DEFAULT NULL,
  `acknowledged_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_reminders`
--

INSERT INTO `order_reminders` (`reminder_id`, `order_id`, `job_order_no`, `office_email`, `message`, `is_acknowledged`, `acknowledged_by`, `acknowledged_at`, `created_at`) VALUES
(7, 53, 'JO0001-12', 'asdasd@gmail.com', 'Please follow up on this order - Customer reminder sent via tracking page', 1, 18, '2025-12-13 10:38:22', '2025-12-13 02:37:53'),
(8, 55, 'JO0003-12', 'asdasd@gmail.com', 'Please follow up on this order - Customer reminder sent via tracking page', 1, 13, '2025-12-13 20:21:52', '2025-12-13 12:21:04'),
(11, 54, 'JO0002-12', 'asdasd@gmail.com', 'Please follow up on this order - Customer reminder sent via tracking page', 1, 11, '2025-12-14 11:57:49', '2025-12-14 03:57:44'),
(12, 58, 'JO0006-12', 'vallechristianmark@gmail.com', 'Please follow up on this order - Customer reminder sent via tracking page', 1, 11, '2025-12-14 12:29:22', '2025-12-14 04:29:05'),
(13, 69, 'JO0014-12', 'asd@gmail.com', 'Please follow up on this order - Customer reminder sent via tracking page', 1, 11, '2025-12-15 21:01:08', '2025-12-15 13:00:57');

-- --------------------------------------------------------

--
-- Table structure for table `order_status`
--

CREATE TABLE `order_status` (
  `order_status_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status`
--

INSERT INTO `order_status` (`order_status_id`, `order_id`, `status_id`, `updated_at`) VALUES
(18, 53, 4, '2025-12-13 10:19:34'),
(19, 53, 1, '2025-12-13 10:19:49'),
(20, 54, 4, '2025-12-13 19:24:39'),
(21, 54, 5, '2025-12-13 19:37:45'),
(22, 54, 1, '2025-12-13 19:37:45'),
(23, 55, 4, '2025-12-13 20:19:44'),
(24, 55, 5, '2025-12-13 20:20:41'),
(25, 55, 1, '2025-12-13 20:20:41'),
(26, 56, 1, '2025-12-14 11:26:37'),
(27, 53, 3, '2025-12-14 12:10:23'),
(28, 57, 1, '2025-12-14 12:25:25'),
(29, 58, 4, '2025-12-14 12:27:13'),
(30, 59, 4, '2025-12-14 12:52:18'),
(31, 60, 4, '2025-12-15 20:16:07'),
(32, 58, 5, '2025-12-15 20:16:26'),
(33, 58, 1, '2025-12-15 20:16:26'),
(34, 59, 5, '2025-12-15 20:22:12'),
(35, 59, 1, '2025-12-15 20:22:12'),
(36, 61, 4, '2025-12-15 20:25:29'),
(37, 60, 5, '2025-12-15 20:25:45'),
(38, 60, 1, '2025-12-15 20:25:45'),
(39, 62, 1, '2025-12-15 20:43:49'),
(40, 63, 1, '2025-12-15 20:50:28'),
(41, 54, 3, '2025-12-15 20:50:54'),
(42, 64, 1, '2025-12-15 20:51:57'),
(43, 61, 5, '2025-12-15 20:52:12'),
(44, 61, 1, '2025-12-15 20:52:12'),
(45, 56, 3, '2025-12-15 20:52:45'),
(46, 58, 3, '2025-12-15 20:52:58'),
(47, 65, 1, '2025-12-15 20:54:56'),
(48, 69, 1, '2025-12-15 21:00:02'),
(49, 70, 4, '2025-12-16 23:17:19'),
(50, 70, 5, '2025-12-16 23:23:10'),
(51, 70, 1, '2025-12-16 23:23:10');

-- --------------------------------------------------------

--
-- Table structure for table `public_concerns`
--

CREATE TABLE `public_concerns` (
  `msg_ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(70) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `is_read` int(11) NOT NULL DEFAULT 0 COMMENT '0=Unread, 1=Read',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `public_concerns`
--

INSERT INTO `public_concerns` (`msg_ID`, `name`, `email`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'dsada', 'dsada@gmail.com', 'dsadadsa', 'dada', 0, '2025-08-13 10:02:10'),
(2, 'dsa', 'sonerdswin12@gmail.com', 'dsadadad', 'sada', 0, '2025-08-13 10:02:52'),
(3, 'fdsf', 'fdsfsfsd@gmail.com', 'dsfsdfd', 'fdsfs', 0, '2025-08-18 08:59:20');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `Id` int(11) NOT NULL,
  `system_name` varchar(255) NOT NULL,
  `about_us` text NOT NULL,
  `address` text NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `gallery` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0=Inactive, 1=Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`Id`, `system_name`, `about_us`, `address`, `email`, `contact`, `logo`, `gallery`, `status`, `created_at`) VALUES
(5, 'Canteen Job Order', 'Sample Address', 'Lapasan, Cagayan de Oro City, 9000, Philippines', 'ustpsample@gmail.com', '09317818551', '1755251082.png', '1755249565_0.jpg,1755249565_1.jpg,1755249565_2.jpg,1755249565_3.jpg,1755249565_4.jpg,1755249565_5.jpg,1755249565_6.jpg,1755249565_7.jpg', 1, '2024-09-27 10:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `tblitem`
--

CREATE TABLE `tblitem` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `item_unit_price` decimal(10,2) NOT NULL,
  `stock_qty` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) NOT NULL DEFAULT 10,
  `item_added_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblitem`
--

INSERT INTO `tblitem` (`item_id`, `item_name`, `item_unit_price`, `stock_qty`, `low_stock_threshold`, `item_added_by`) VALUES
(1, 'A.M Snacks', 80.00, 173, 6, 11),
(2, 'P.M Snacks', 80.00, 0, 10, 11),
(3, 'Snacks', 70.00, 0, 10, 11),
(4, 'Breakfast', 90.00, 12, 10, 11),
(5, 'Lunch', 100.00, 0, 10, 11),
(6, 'Dinner', 120.00, 0, 10, 11),
(7, 'sample', 20.00, 0, 10, 11),
(8, 'sanny', 12.00, 0, 10, 13);

-- --------------------------------------------------------

--
-- Table structure for table `tbloffice`
--

CREATE TABLE `tbloffice` (
  `office_id` int(11) NOT NULL,
  `office_type_id` int(11) NOT NULL,
  `office_name` varchar(150) NOT NULL,
  `office_email` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbloffice`
--

INSERT INTO `tbloffice` (`office_id`, `office_type_id`, `office_name`, `office_email`) VALUES
(1, 1, 'asd', 'asd@gmail.com'),
(2, 1, 'CITE', 'vallechristianmark@gmail.com'),
(3, 2, 'Sample administration', 'asd@ustp.edu.ph'),
(4, 1, 'COMSCI', 'asd@gmaill.com'),
(5, 3, 'CSM', 'asjdhsajdh@gmail.com'),
(6, 2, 'CITC', 'vallechristianmarkasd@ustp.edu.ph');

-- --------------------------------------------------------

--
-- Table structure for table `tblofficetype`
--

CREATE TABLE `tblofficetype` (
  `office_type_id` int(11) NOT NULL,
  `office_type_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblofficetype`
--

INSERT INTO `tblofficetype` (`office_type_id`, `office_type_name`) VALUES
(1, 'College'),
(2, 'Administration'),
(3, 'Finace');

-- --------------------------------------------------------

--
-- Table structure for table `tblstatus`
--

CREATE TABLE `tblstatus` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstatus`
--

INSERT INTO `tblstatus` (`status_id`, `status_name`) VALUES
(1, 'On-going'),
(2, 'Cancelled'),
(3, 'Completed'),
(4, 'Pending'),
(5, 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `tblstock_adjustment`
--

CREATE TABLE `tblstock_adjustment` (
  `adjustment_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `change_qty` int(11) NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstock_adjustment`
--

INSERT INTO `tblstock_adjustment` (`adjustment_id`, `item_id`, `change_qty`, `previous_stock`, `new_stock`, `created_by`, `created_at`) VALUES
(1, 1, 1, 1, 2, 13, '2025-12-13 05:21:18'),
(2, 1, -1, 2, 1, 13, '2025-12-13 05:21:25'),
(3, 1, 100, 0, 100, 20, '2025-12-14 03:26:18'),
(4, 1, 100, 98, 198, 11, '2025-12-14 04:21:02'),
(5, 4, 50, 0, 50, 11, '2025-12-14 04:22:14');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_password_changed` tinyint(1) DEFAULT 0,
  `verification_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `userlevel_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`user_id`, `firstname`, `middlename`, `lastname`, `suffix`, `gender`, `birthdate`, `nationality`, `contact`, `email`, `password`, `image`, `is_verified`, `is_password_changed`, `verification_code`, `created_at`, `userlevel_id`) VALUES
(11, 'Admin', '', 'User', '', 'Male', '1990-01-01', 'Filipino', '9509972084', 'admin@gmail.com', '$2y$10$o.CAPk/Okx41qL4DyUrpO.N9I7oiZsoyL4IQNOBSqyjOwvfSuXj1q', 'avatar.png', 1, 1, NULL, '2025-12-09 12:22:42', 1),
(12, 'Office', '', 'Staff', '', 'Male', '1990-01-01', 'Filipino', '9509972085', 'officestaff@gmail.com', '$2y$10$o.CAPk/Okx41qL4DyUrpO.N9I7oiZsoyL4IQNOBSqyjOwvfSuXj1q', 'avatar.png', 1, 1, NULL, '2025-12-09 12:22:42', 2),
(13, 'Canteen', '', 'Staff', '', 'Female', '1995-05-15', 'Filipino', '9509972086', 'canteenstaff@gmail.com', '$2y$10$o.CAPk/Okx41qL4DyUrpO.N9I7oiZsoyL4IQNOBSqyjOwvfSuXj1q', 'avatar.png', 1, 1, NULL, '2025-12-09 12:22:42', 2),
(17, 'dsa', 'dsa', 'dsa', 'dsa', 'Male', '0001-02-12', 'asd', '9541125589', 'asd@gmail.com', '$2y$10$OYcanf10IdpTffPW17kvQu.oMFuxqTKmA6dn9ag1HVdBUrUIGsVfO', 'avatar.png', 0, 0, NULL, '2025-12-09 13:24:02', 1),
(18, 'asd', 'asd', 'asd', 'Jr', 'Male', '1222-02-12', '12', '9533593321', 'canteenmanager@gmail.com', '$2y$10$HvM6BOkUvChQWJfIKRQxq.x4C9MeKt59kFCqe/HkhALXZD0PwjtQG', 'avatar.png', 1, 0, NULL, '2025-12-12 04:22:44', 3),
(20, 'asddd', 'sd', 'asd', 'asd', 'Male', '1222-12-12', '122', '9876543321', 'vallechristianmark@gmail.com', '$2y$10$D475DqrlwN26MZsmUE71cuAnTrvKUWC7fx48BqevVUBdYLTXjWjCq', 'avatar.png', 1, 1, NULL, '2025-12-14 03:11:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbluserlevel`
--

CREATE TABLE `tbluserlevel` (
  `userlevel_id` int(11) NOT NULL,
  `userlevel_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluserlevel`
--

INSERT INTO `tbluserlevel` (`userlevel_id`, `userlevel_name`) VALUES
(1, 'Admin'),
(2, 'Canteen Staff'),
(3, 'Canteen Manager');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_office_under`
--

CREATE TABLE `tbl_office_under` (
  `office_under_id` int(11) NOT NULL,
  `office_id` int(11) NOT NULL,
  `office_under_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_office_under`
--

INSERT INTO `tbl_office_under` (`office_under_id`, `office_id`, `office_under_name`) VALUES
(3, 6, 'asd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`audit_ID`);

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`admin_ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `canteen_staff`
--
ALTER TABLE `canteen_staff`
  ADD PRIMARY KEY (`canteen_staff_ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`attempt_ID`);

--
-- Indexes for table `log_history`
--
ALTER TABLE `log_history`
  ADD PRIMARY KEY (`log_Id`),
  ADD KEY `fk_loghistory_user` (`user_ID`),
  ADD KEY `idx_uin` (`uin`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `job_order_no` (`job_order_no`),
  ADD KEY `fk_orders_office` (`office_id`),
  ADD KEY `fk_orders_office_under` (`office_under_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `fk_order_items_item` (`items_id`);

--
-- Indexes for table `order_reminders`
--
ALTER TABLE `order_reminders`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `fk_reminders_order` (`order_id`),
  ADD KEY `idx_job_order_no` (`job_order_no`);

--
-- Indexes for table `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`order_status_id`),
  ADD KEY `fk_order_status_order` (`order_id`),
  ADD KEY `fk_order_status_status` (`status_id`);

--
-- Indexes for table `public_concerns`
--
ALTER TABLE `public_concerns`
  ADD PRIMARY KEY (`msg_ID`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblitem`
--
ALTER TABLE `tblitem`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `fk_item_added_by` (`item_added_by`);

--
-- Indexes for table `tbloffice`
--
ALTER TABLE `tbloffice`
  ADD PRIMARY KEY (`office_id`),
  ADD KEY `fk_office_type` (`office_type_id`);

--
-- Indexes for table `tblofficetype`
--
ALTER TABLE `tblofficetype`
  ADD PRIMARY KEY (`office_type_id`);

--
-- Indexes for table `tblstatus`
--
ALTER TABLE `tblstatus`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `tblstock_adjustment`
--
ALTER TABLE `tblstock_adjustment`
  ADD PRIMARY KEY (`adjustment_id`),
  ADD KEY `fk_adj_item` (`item_id`),
  ADD KEY `fk_adj_user` (`created_by`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_userlevel` (`userlevel_id`);

--
-- Indexes for table `tbluserlevel`
--
ALTER TABLE `tbluserlevel`
  ADD PRIMARY KEY (`userlevel_id`);

--
-- Indexes for table `tbl_office_under`
--
ALTER TABLE `tbl_office_under`
  ADD PRIMARY KEY (`office_under_id`),
  ADD KEY `fk_office_under_office` (`office_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `audit_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=417;

--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `canteen_staff`
--
ALTER TABLE `canteen_staff`
  MODIFY `canteen_staff_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `attempt_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `log_history`
--
ALTER TABLE `log_history`
  MODIFY `log_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1233;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `order_reminders`
--
ALTER TABLE `order_reminders`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_status`
--
ALTER TABLE `order_status`
  MODIFY `order_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `public_concerns`
--
ALTER TABLE `public_concerns`
  MODIFY `msg_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblitem`
--
ALTER TABLE `tblitem`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbloffice`
--
ALTER TABLE `tbloffice`
  MODIFY `office_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblofficetype`
--
ALTER TABLE `tblofficetype`
  MODIFY `office_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblstatus`
--
ALTER TABLE `tblstatus`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblstock_adjustment`
--
ALTER TABLE `tblstock_adjustment`
  MODIFY `adjustment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbluserlevel`
--
ALTER TABLE `tbluserlevel`
  MODIFY `userlevel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_office_under`
--
ALTER TABLE `tbl_office_under`
  MODIFY `office_under_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_history`
--
ALTER TABLE `log_history`
  ADD CONSTRAINT `fk_loghistory_user` FOREIGN KEY (`user_ID`) REFERENCES `tbluser` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_office` FOREIGN KEY (`office_id`) REFERENCES `tbloffice` (`office_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_office_under` FOREIGN KEY (`office_under_id`) REFERENCES `tbl_office_under` (`office_under_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_item` FOREIGN KEY (`items_id`) REFERENCES `tblitem` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_reminders`
--
ALTER TABLE `order_reminders`
  ADD CONSTRAINT `fk_reminders_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_status`
--
ALTER TABLE `order_status`
  ADD CONSTRAINT `fk_order_status_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_status_status` FOREIGN KEY (`status_id`) REFERENCES `tblstatus` (`status_id`);

--
-- Constraints for table `tblitem`
--
ALTER TABLE `tblitem`
  ADD CONSTRAINT `fk_item_added_by` FOREIGN KEY (`item_added_by`) REFERENCES `tbluser` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tbloffice`
--
ALTER TABLE `tbloffice`
  ADD CONSTRAINT `fk_office_type` FOREIGN KEY (`office_type_id`) REFERENCES `tblofficetype` (`office_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblstock_adjustment`
--
ALTER TABLE `tblstock_adjustment`
  ADD CONSTRAINT `fk_adj_item` FOREIGN KEY (`item_id`) REFERENCES `tblitem` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adj_user` FOREIGN KEY (`created_by`) REFERENCES `tbluser` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD CONSTRAINT `fk_userlevel` FOREIGN KEY (`userlevel_id`) REFERENCES `tbluserlevel` (`userlevel_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
