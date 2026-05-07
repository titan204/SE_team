-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 05, 2026 at 03:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30
CREATE DATABASE hotel_management;
USE hotel_management;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(150) NOT NULL,
  `asset_type` enum('hvac','elevator','plumbing','electrical','equipment','other') NOT NULL DEFAULT 'other',
  `status` enum('operational','under_maintenance','decommissioned') NOT NULL DEFAULT 'operational'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `name`, `location`, `asset_type`, `status`) VALUES
(1, 'Main HVAC Unit ΓÇö Floor 1', 'Floor 1 Plant Room', 'hvac', 'operational'),
(2, 'Main HVAC Unit ΓÇö Floor 2', 'Floor 2 Plant Room', 'hvac', 'operational'),
(3, 'Main HVAC Unit ΓÇö Floor 3', 'Floor 3 Plant Room', 'hvac', 'under_maintenance'),
(4, 'Passenger Elevator 1', 'Lobby', 'elevator', 'operational'),
(5, 'Passenger Elevator 2', 'North Wing', 'elevator', 'operational'),
(6, 'Boiler ΓÇö Hot Water System', 'Basement', 'plumbing', 'operational'),
(7, 'Main Electrical Panel', 'Basement', 'electrical', 'operational'),
(8, 'Pool Pump System', 'Pool Area', 'equipment', 'operational');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(10) UNSIGNED DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action`, `target_type`, `target_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'price_override', 'folio', 4, '2500.00', '2550.00', NULL, NULL, '2026-05-01 21:45:43'),
(2, 1, 'status_change', 'reservation', 8, 'confirmed', 'cancelled', NULL, NULL, '2026-05-01 21:45:43'),
(3, 3, 'check_in', 'reservation', 1, 'confirmed', 'checked_in', NULL, NULL, '2026-05-01 21:45:43'),
(4, 3, 'check_in', 'reservation', 9, 'confirmed', 'checked_in', NULL, NULL, '2026-05-01 21:45:43'),
(5, 4, 'check_out', 'reservation', 2, 'checked_in', 'checked_out', NULL, NULL, '2026-05-01 21:45:43'),
(6, 3, 'check_out', 'reservation', 3, 'checked_in', 'checked_out', NULL, NULL, '2026-05-01 21:45:43'),
(7, 4, 'check_out', 'reservation', 4, 'checked_in', 'checked_out', NULL, NULL, '2026-05-01 21:45:43'),
(8, 3, 'check_out', 'reservation', 9, 'checked_in', 'checked_out', NULL, NULL, '2026-05-01 21:45:43'),
(9, 1, 'room_status', 'room', 7, 'available', 'out_of_order', NULL, NULL, '2026-05-01 21:45:43'),
(10, 2, 'loyalty_upgrade', 'guest', 4, 'gold', 'platinum', NULL, NULL, '2026-05-01 21:45:43'),
(11, 2, 'vip_flag', 'guest', 1, '0', '1', NULL, NULL, '2026-05-01 21:45:43'),
(12, 4, 'no_show', 'reservation', 10, 'confirmed', 'no_show', NULL, NULL, '2026-05-01 21:45:43'),
(13, 1, 'price_override', 'folio', 4, '2500.00', '2550.00', NULL, NULL, '2026-05-01 21:53:46'),
(14, 1, 'status_change', 'reservation', 8, 'confirmed', 'cancelled', NULL, NULL, '2026-05-01 21:53:46'),
(15, 3, 'check_in', 'reservation', 1, 'confirmed', 'checked_in', NULL, NULL, '2026-05-01 21:53:46'),
(16, 3, 'check_in', 'reservation', 9, 'confirmed', 'checked_in', NULL, NULL, '2026-05-01 21:53:46'),
(17, 4, 'check_out', 'reservation', 2, 'checked_in', 'checked_out', NULL, NULL, '2026-05-01 21:53:46'),
(18, 3, 'check_out', 'reservation', 3, 'checked_in', 'checked_out', NULL, NULL, '2026-05-01 21:53:46'),
(19, 4, 'check_out', 'reservation', 4, 'checked_in', 'checked_out', NULL, NULL, '2026-05-01 21:53:46'),
(20, 3, 'check_out', 'reservation', 9, 'checked_in', 'checked_out', NULL, NULL, '2026-05-01 21:53:46'),
(21, 1, 'room_status', 'room', 7, 'available', 'out_of_order', NULL, NULL, '2026-05-01 21:53:46'),
(22, 2, 'loyalty_upgrade', 'guest', 4, 'gold', 'platinum', NULL, NULL, '2026-05-01 21:53:46'),
(23, 2, 'vip_flag', 'guest', 1, '0', '1', NULL, NULL, '2026-05-01 21:53:46'),
(24, 4, 'no_show', 'reservation', 10, 'confirmed', 'no_show', NULL, NULL, '2026-05-01 21:53:46'),
(25, 6, 'housekeeping.task.done', 'housekeeping_task', 11, NULL, NULL, NULL, NULL, '2026-05-01 23:36:40'),
(26, 9, 'check_in', 'reservation', 27, 'confirmed', 'checked_in', NULL, NULL, '2026-05-03 00:29:41'),
(27, 8, 'check_in', 'reservation', 28, 'confirmed', 'checked_in', NULL, NULL, '2026-05-03 00:51:29'),
(28, 8, 'room_upgrade', 'reservation', 28, '10', '15', NULL, NULL, '2026-05-03 00:51:45'),
(29, 8, 'room_upgrade', 'reservation', 28, '15', '20', NULL, NULL, '2026-05-03 00:52:07'),
(30, 10, 'check_in', 'reservation', 30, 'confirmed', 'checked_in', NULL, NULL, '2026-05-03 20:47:59'),
(31, 3, 'payment_flag', 'reservation', 5, 'incomplete', 'Member Carlos Rodriguez: No default payment method on file for guest #3', NULL, NULL, '2026-05-03 20:59:44'),
(32, 3, 'payment_flag_coordinator_notified', 'reservation', 5, 'incomplete', 'Coordinator notification queued: Carlos Rodriguez on reservation #5 has incomplete payment info (No default payment method on file for guest #3).', NULL, NULL, '2026-05-03 20:59:44'),
(33, 3, 'group_invoice_coordinator_notified', 'group_reservation', 1, '1', 'Consolidated group invoice #1 notification queued for coordinator John Smith (john.smith@gmail.com).', NULL, NULL, '2026-05-03 21:01:02'),
(34, 3, 'individual_invoice_member_notified', 'reservation', 6, '2', 'Individual invoice #2 notification queued for Yuki Tanaka (yuki.tanaka@gmail.com, guest #4) in group #1.', NULL, NULL, '2026-05-03 21:01:02'),
(35, 3, 'group_invoice_finalized', 'group_reservation', 1, 'draft', 'finalized', NULL, NULL, '2026-05-03 21:01:02'),
(36, 3, 'group_invoice_coordinator_notified', 'group_reservation', 1, '3', 'Consolidated group invoice #3 notification queued for coordinator John Smith (john.smith@gmail.com).', NULL, NULL, '2026-05-03 21:01:40'),
(37, 3, 'group_invoice_coordinator_notified', 'group_reservation', 1, '4', 'Consolidated group invoice #4 notification queued for coordinator John Smith (john.smith@gmail.com).', NULL, NULL, '2026-05-03 21:03:52'),
(38, 3, 'group_invoice_coordinator_notified', 'group_reservation', 2, '6', 'Consolidated group invoice #6 notification queued for coordinator John Smith (john.smith@gmail.com).', NULL, NULL, '2026-05-03 21:06:55'),
(39, 3, 'group_invoice_finalized', 'group_reservation', 2, 'draft', 'finalized', NULL, NULL, '2026-05-03 21:06:55'),
(40, 1, 'login', 'user', 1, NULL, NULL, '197.32.14.5', 'Mozilla/5.0 (Windows NT 10.0) Chrome/123.0', '2026-04-06 06:01:22'),
(41, 3, 'reservation_created', 'reservation', 1, NULL, '{\"room\":\"102\",\"guest\":\"John Smith\"}', '197.32.14.5', 'Mozilla/5.0 (Windows NT 10.0) Chrome/123.0', '2026-04-06 06:15:44'),
(42, 4, 'reservation_created', 'reservation', 2, NULL, '{\"room\":\"201\",\"guest\":\"Emma Wilson\"}', '197.32.14.8', 'Mozilla/5.0 (Windows NT 10.0) Chrome/123.0', '2026-04-07 07:22:10'),
(43, 2, 'room_status_changed', 'room', 3, 'available', 'occupied', '197.32.14.5', 'Mozilla/5.0 (Windows NT 10.0) Chrome/123.0', '2026-04-08 12:05:33'),
(44, 3, 'check_in', 'reservation', 2, NULL, 'checked_in', '197.32.14.5', 'Mozilla/5.0 (Windows NT 10.0) Chrome/123.0', '2026-04-09 11:15:00'),
(45, 1, 'user_role_updated', 'user', 6, 'front_desk', 'revenue_manager', '10.0.0.1', 'Mozilla/5.0 (Macintosh) Chrome/123.0', '2026-04-10 07:00:00'),
(46, 2, 'payment_recorded', 'payment', 40, NULL, '{\"amount\":2150,\"method\":\"credit_card\"}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-04-11 06:20:00'),
(47, 3, 'check_out', 'reservation', 2, 'checked_in', 'checked_out', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-12 09:45:00'),
(48, 4, 'reservation_cancelled', 'reservation', 8, 'confirmed', 'cancelled', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-04-13 08:30:00'),
(49, 1, 'login', 'user', 1, NULL, NULL, '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-14 05:58:11'),
(50, 2, 'maintenance_order_created', 'maintenance_orders', 1, NULL, '{\"room\":\"302\",\"issue\":\"AC unit\",\"priority\":\"high\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-15 07:00:00'),
(51, 3, 'payment_recorded', 'payment', 41, NULL, '{\"amount\":1500,\"method\":\"credit_card\"}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-04-16 06:10:00'),
(52, 5, 'housekeeping_task_updated', 'housekeeping_tasks', 3, 'pending', 'in_progress', '192.168.1.10', 'Mozilla/5.0 Chrome/123.0', '2026-04-16 07:30:00'),
(53, 5, 'housekeeping_task_updated', 'housekeeping_tasks', 3, 'in_progress', 'done', '192.168.1.10', 'Mozilla/5.0 Chrome/123.0', '2026-04-16 09:00:00'),
(54, 4, 'reservation_created', 'reservation', 3, NULL, '{\"room\":\"202\",\"guest\":\"Priya Sharma\"}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-04-17 08:00:00'),
(55, 3, 'check_in', 'reservation', 3, NULL, 'checked_in', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-17 11:00:00'),
(56, 2, 'payment_recorded', 'payment', 43, NULL, '{\"amount\":1200,\"method\":\"cash\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-18 06:00:00'),
(57, 1, 'login', 'user', 1, NULL, NULL, '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-19 05:55:00'),
(58, 3, 'reservation_created', 'reservation', 4, NULL, '{\"room\":\"203\",\"guest\":\"Pierre Dubois\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-19 07:00:00'),
(59, 6, 'report_viewed', 'report', NULL, NULL, '{\"type\":\"revenue\",\"period\":\"weekly\"}', '10.0.0.1', 'Mozilla/5.0 Chrome/123.0', '2026-04-19 08:30:00'),
(60, 4, 'check_in', 'reservation', 4, NULL, 'checked_in', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-04-20 14:00:00'),
(61, 5, 'housekeeping_task_updated', 'housekeeping_tasks', 5, 'pending', 'done', '192.168.1.10', 'Mozilla/5.0 Chrome/123.0', '2026-04-21 05:10:00'),
(62, 3, 'payment_recorded', 'payment', 44, NULL, '{\"amount\":1350,\"method\":\"credit_card\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-22 06:15:00'),
(63, 3, 'check_out', 'reservation', 3, 'checked_in', 'checked_out', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-23 08:30:00'),
(64, 2, 'payment_recorded', 'payment', 45, NULL, '{\"amount\":3000,\"method\":\"credit_card\"}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-04-24 04:00:00'),
(65, 4, 'check_out', 'reservation', 4, 'checked_in', 'checked_out', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-04-25 09:30:00'),
(66, 1, 'login', 'user', 1, NULL, NULL, '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-26 05:02:00'),
(67, 6, 'report_viewed', 'report', NULL, NULL, '{\"type\":\"occupancy\",\"period\":\"monthly\"}', '10.0.0.1', 'Mozilla/5.0 Chrome/123.0', '2026-04-26 07:00:00'),
(68, 3, 'reservation_created', 'reservation', 30, NULL, '{\"room\":\"104\",\"guest\":\"Carlos Rodriguez\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-27 06:00:00'),
(69, 3, 'reservation_created', 'reservation', 31, NULL, '{\"room\":\"105\",\"guest\":\"Aisha Al-Rashid\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-27 06:15:00'),
(70, 2, 'found_item_logged', 'found_items', 1, NULL, '{\"item\":\"Brown leather wallet\",\"location\":\"room 201\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-04-28 11:00:00'),
(71, 3, 'payment_recorded', 'payment', 48, NULL, '{\"amount\":2000,\"method\":\"bank_transfer\"}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-04-29 05:45:00'),
(72, 4, 'check_in', 'reservation', 24, NULL, 'checked_in', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-04-29 11:00:00'),
(73, 5, 'housekeeping_task_updated', 'housekeeping_tasks', 7, 'pending', 'done', '192.168.1.10', 'Mozilla/5.0 Chrome/123.0', '2026-04-30 01:30:00'),
(74, 3, 'check_in', 'reservation', 25, NULL, 'checked_in', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-01 10:30:00'),
(75, 2, 'payment_recorded', 'payment', 50, NULL, '{\"amount\":500,\"method\":\"credit_card\"}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-05-01 03:00:00'),
(76, 1, 'login', 'user', 1, NULL, NULL, '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-02 04:58:00'),
(77, 6, 'report_viewed', 'report', NULL, NULL, '{\"type\":\"audit_log\",\"period\":\"last_30_days\"}', '10.0.0.1', 'Mozilla/5.0 Chrome/123.0', '2026-05-02 06:00:00'),
(78, 3, 'work_order_created', 'work_orders', 3, NULL, '{\"room\":\"202\",\"issue\":\"dripping tap\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-02 07:00:00'),
(79, 5, 'housekeeping_task_updated', 'housekeeping_tasks', 2, 'pending', 'in_progress', '192.168.1.10', 'Mozilla/5.0 Chrome/123.0', '2026-05-02 03:30:00'),
(80, 2, 'found_item_logged', 'found_items', 2, NULL, '{\"item\":\"iPhone 15 Pro\",\"location\":\"room 102\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-03 08:00:00'),
(81, 3, 'reservation_created', 'reservation', 32, NULL, '{\"room\":\"204\",\"guest\":\"David Chen\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-03 06:00:00'),
(82, 3, 'reservation_created', 'reservation', 33, NULL, '{\"room\":\"106\",\"guest\":\"Emma Wilson\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-03 06:30:00'),
(83, 4, 'reservation_created', 'reservation', 34, NULL, '{\"room\":\"205\",\"guest\":\"Pierre Dubois\"}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-05-03 07:00:00'),
(84, 1, 'work_order_status_changed', 'work_orders', 1, 'open', 'in_progress', '10.0.0.1', 'Mozilla/5.0 Chrome/123.0', '2026-05-03 06:00:00'),
(85, 2, 'payment_recorded', 'payment', 56, NULL, '{\"amount\":500,\"method\":\"credit_card\"}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-05-04 04:00:00'),
(86, 3, 'billing_dispute_raised', 'billing_disputes', 1, NULL, '{\"reservation\":4,\"amount\":50,\"reason\":\"late checkout not communicated\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-04 11:00:00'),
(87, 4, 'lost_item_report_created', 'lost_item_reports', 3, NULL, '{\"guest_id\":1,\"item\":\"iPhone black Pro\"}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-05-04 12:30:00'),
(88, 1, 'login', 'user', 1, NULL, NULL, '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-05 04:55:00'),
(89, 3, 'check_in', 'reservation', 30, NULL, 'checked_in', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-05 11:00:00'),
(90, 3, 'check_in', 'reservation', 31, NULL, 'checked_in', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-05 12:30:00'),
(91, 4, 'payment_recorded', 'payment', 70, NULL, '{\"amount\":300,\"method\":\"credit_card\",\"folio\":20}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-05-05 11:05:00'),
(92, 4, 'payment_recorded', 'payment', 71, NULL, '{\"amount\":400,\"method\":\"credit_card\",\"folio\":21}', '197.32.14.8', 'Mozilla/5.0 Chrome/123.0', '2026-05-05 12:35:00'),
(93, 2, 'work_order_created', 'work_orders', 9, NULL, '{\"room\":\"201\",\"issue\":\"ceiling light flickering\"}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-05 06:30:00'),
(94, 6, 'report_viewed', 'report', NULL, NULL, '{\"type\":\"revenue\",\"period\":\"daily\"}', '10.0.0.1', 'Mozilla/5.0 Chrome/123.0', '2026-05-05 05:30:00'),
(95, 3, 'low_stock_alert_escalated', 'low_stock_alerts', 1, NULL, '{\"item\":\"All-Purpose Cleaner\",\"stock\":3,\"threshold\":8}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-05 07:00:00'),
(96, 2, 'reservation_created', 'reservation', 36, NULL, '{\"room\":\"203\",\"guest\":\"Yuki Tanaka\",\"vip\":true}', '197.32.14.5', 'Mozilla/5.0 Chrome/123.0', '2026-05-05 10:00:00'),
(97, 1, 'login', 'user', 2, NULL, NULL, '197.32.14.9', 'Mozilla/5.0 (Windows NT 10.0) Firefox/124.0', '2026-05-05 05:10:00'),
(98, 6, 'report_viewed', 'report', NULL, NULL, '{\"type\":\"audit_log\",\"period\":\"today\"}', '10.0.0.1', 'Mozilla/5.0 Chrome/123.0', '2026-05-05 13:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `billing_adjustments`
--

CREATE TABLE `billing_adjustments` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `type` enum('discount','surcharge','loyalty_redemption') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `applied_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing_adjustments`
--

INSERT INTO `billing_adjustments` (`id`, `reservation_id`, `type`, `value`, `applied_by_user_id`, `reason`, `created_at`) VALUES
(1, 7, 'discount', 10.00, 3, 'by love', '2026-05-03 20:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `billing_disputes`
--

CREATE TABLE `billing_disputes` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `raised_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('open','resolved') NOT NULL DEFAULT 'open',
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing_items`
--

CREATE TABLE `billing_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `item_type` enum('room_rate','minibar','external_service','manual','other') NOT NULL DEFAULT 'manual',
  `description` varchar(255) NOT NULL DEFAULT '',
  `amount` decimal(10,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `added_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_voided` tinyint(1) NOT NULL DEFAULT 0,
  `void_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing_items`
--

INSERT INTO `billing_items` (`id`, `reservation_id`, `item_type`, `description`, `amount`, `quantity`, `added_by_user_id`, `added_at`, `is_voided`, `void_reason`) VALUES
(1, 1, 'minibar', 'Minibar ΓÇö Water, Juice, Snacks', 25.00, 1, 1, '2026-05-01 21:45:44', 0, NULL),
(2, 1, 'external_service', 'Spa Session ΓÇö 60 min', 80.00, 1, 1, '2026-05-01 21:45:44', 0, NULL),
(3, 2, 'minibar', 'Minibar ΓÇö Beer x2', 18.00, 2, 1, '2026-05-01 21:45:44', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `billing_retry_queue`
--

CREATE TABLE `billing_retry_queue` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing_split_log`
--

CREATE TABLE `billing_split_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `split_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `members_split` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`members_split`)),
  `original_consolidated_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `corporate_accounts`
--

CREATE TABLE `corporate_accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `contracted_rate` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `corporate_accounts`
--

INSERT INTO `corporate_accounts` (`id`, `company_name`, `contact_email`, `contact_phone`, `contracted_rate`, `created_at`) VALUES
(1, 'TechCorp International', 'travel@techcorp.com', '+1-800-555-0201', 15.00, '2026-05-01 21:45:43'),
(2, 'Global Consulting Group', 'bookings@gcg.com', '+44-800-555-0202', 10.00, '2026-05-01 21:45:43'),
(3, 'Emirates Business Hub', 'hotels@emirateshub.ae', '+971-4-800-0203', 20.00, '2026-05-01 21:45:43'),
(4, 'TechCorp International', 'travel@techcorp.com', '+1-800-555-0201', 15.00, '2026-05-01 21:53:46'),
(5, 'Global Consulting Group', 'bookings@gcg.com', '+44-800-555-0202', 10.00, '2026-05-01 21:53:46'),
(6, 'Emirates Business Hub', 'hotels@emirateshub.ae', '+971-4-800-0203', 20.00, '2026-05-01 21:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `corrective_tasks`
--

CREATE TABLE `corrective_tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `qa_inspection_id` int(10) UNSIGNED NOT NULL,
  `assigned_to_user_id` int(10) UNSIGNED DEFAULT NULL,
  `task_description` text NOT NULL,
  `due_by` datetime DEFAULT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_flags`
--

CREATE TABLE `emergency_flags` (
  `id` int(10) UNSIGNED NOT NULL,
  `work_order_id` int(10) UNSIGNED NOT NULL,
  `severity` enum('low','medium','high','safety_critical') NOT NULL,
  `is_safety_critical` tinyint(1) NOT NULL DEFAULT 0,
  `property_alert_triggered` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `external_services`
--

CREATE TABLE `external_services` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `external_services`
--

INSERT INTO `external_services` (`id`, `name`, `service_type`, `description`, `created_at`) VALUES
(1, 'Grand Spa & Wellness', 'spa', 'Full-service spa: massages, facials, hydrotherapy', '2026-05-01 21:45:43'),
(2, 'The Gourmet Kitchen', 'restaurant', 'Fine dining with international and local cuisine', '2026-05-01 21:45:43'),
(3, 'Airport Luxury Transfers', 'transport', 'Premium car service to/from all major airports', '2026-05-01 21:45:43'),
(4, 'City Cultural Tours', 'tour', 'Guided half-day and full-day city sightseeing tours', '2026-05-01 21:45:43'),
(5, 'Business Centre Services', 'business', 'Printing, scanning, secretarial, and meeting room hire', '2026-05-01 21:45:43'),
(6, 'Grand Spa & Wellness', 'spa', 'Full-service spa: massages, facials, hydrotherapy', '2026-05-01 21:53:46'),
(7, 'The Gourmet Kitchen', 'restaurant', 'Fine dining with international and local cuisine', '2026-05-01 21:53:46'),
(8, 'Airport Luxury Transfers', 'transport', 'Premium car service to/from all major airports', '2026-05-01 21:53:46'),
(9, 'City Cultural Tours', 'tour', 'Guided half-day and full-day city sightseeing tours', '2026-05-01 21:53:46'),
(10, 'Business Centre Services', 'business', 'Printing, scanning, secretarial, and meeting room hire', '2026-05-01 21:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL CHECK (`rating` between 1 and 5),
  `comments` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `overall_score` tinyint(3) UNSIGNED DEFAULT NULL,
  `flagged_for_qa` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `reservation_id`, `guest_id`, `rating`, `comments`, `submitted_at`, `overall_score`, `flagged_for_qa`) VALUES
(1, 2, 2, 4, 'Great stay overall. Room was clean and the front desk staff were very helpful. WiFi could be faster.', '2026-05-01 21:45:43', NULL, 0),
(2, 3, 7, 5, 'Absolutely wonderful experience. The spa was exceptional and the room was spotless. Will return!', '2026-05-01 21:45:43', NULL, 0),
(3, 4, 6, 3, 'Room and food were good, but the late checkout fee was unexpected and not communicated upfront.', '2026-05-01 21:45:43', NULL, 0),
(4, 9, 1, 5, 'The anniversary surprise exceeded all expectations. Suite was magnificent. Cannot wait to come back.', '2026-05-01 21:45:43', NULL, 0),
(5, 2, 2, 4, 'Great stay overall. Room was clean and the front desk staff were very helpful. WiFi could be faster.', '2026-05-01 21:53:46', NULL, 0),
(6, 3, 7, 5, 'Absolutely wonderful experience. The spa was exceptional and the room was spotless. Will return!', '2026-05-01 21:53:46', NULL, 0),
(7, 4, 6, 3, 'Room and food were good, but the late checkout fee was unexpected and not communicated upfront.', '2026-05-01 21:53:46', NULL, 0),
(8, 9, 1, 5, 'The anniversary surprise exceeded all expectations. Suite was magnificent. Cannot wait to come back.', '2026-05-01 21:53:46', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `final_invoices`
--

CREATE TABLE `final_invoices` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_finalized` tinyint(1) NOT NULL DEFAULT 0,
  `issued_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `folios`
--

CREATE TABLE `folios` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_due` decimal(12,2) GENERATED ALWAYS AS (`total_amount` - `amount_paid`) STORED,
  `status` enum('open','settled','refunded') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `folios`
--

INSERT INTO `folios` (`id`, `reservation_id`, `total_amount`, `amount_paid`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2085.00, 1250.00, 'open', '2026-05-01 21:45:43', '2026-05-02 00:34:23'),
(2, 2, 2150.00, 2150.00, 'settled', '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(3, 3, 3350.00, 3350.00, 'settled', '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(4, 4, 2550.00, 2550.00, 'settled', '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(5, 5, 2000.00, 500.00, 'open', '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(6, 6, 9000.00, 6700.00, 'open', '2026-05-01 21:45:43', '2026-05-02 00:34:23'),
(7, 8, 1500.00, 800.00, 'open', '2026-05-01 21:45:43', '2026-05-02 00:34:23'),
(8, 9, 7850.00, 7850.00, 'settled', '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(9, 10, 1000.00, 500.00, 'open', '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(11, 21, 1800.00, 500.00, 'open', '2026-05-02 01:07:51', '2026-05-02 01:07:51'),
(12, 22, 1400.00, 400.00, 'open', '2026-05-02 01:07:51', '2026-05-02 01:07:51'),
(13, 23, 1200.00, 300.00, 'open', '2026-05-02 01:07:51', '2026-05-02 01:07:51'),
(14, 24, 1600.00, 300.00, 'open', '2026-05-02 01:07:51', '2026-05-02 01:07:51'),
(15, 25, 1200.00, 250.00, 'open', '2026-05-02 01:07:51', '2026-05-02 01:07:51'),
(16, 26, 500.00, 0.00, 'open', '2026-05-03 00:09:52', '2026-05-03 00:09:52'),
(17, 27, 7500.00, 0.00, 'open', '2026-05-03 00:19:22', '2026-05-03 00:19:22'),
(18, 28, 4500.00, 900.00, 'open', '2026-05-03 00:50:01', '2026-05-03 00:50:37'),
(19, 29, 34500.00, 0.00, 'open', '2026-05-03 00:52:50', '2026-05-03 00:52:50'),
(20, 30, 4500.00, 900.00, 'open', '2026-05-03 20:46:59', '2026-05-03 20:47:25'),
(21, 31, 2000.00, 400.00, 'open', '2026-05-05 12:30:00', '2026-05-05 12:30:00'),
(22, 32, 3200.00, 640.00, 'open', '2026-05-04 06:00:00', '2026-05-04 06:00:00'),
(23, 33, 1500.00, 300.00, 'open', '2026-05-04 07:00:00', '2026-05-04 07:00:00'),
(24, 34, 3200.00, 640.00, 'open', '2026-05-04 08:00:00', '2026-05-04 08:00:00'),
(25, 35, 2400.00, 480.00, 'open', '2026-05-04 09:00:00', '2026-05-04 09:00:00'),
(26, 36, 3200.00, 640.00, 'open', '2026-05-04 10:00:00', '2026-05-04 10:00:00'),
(27, 37, 1500.00, 300.00, 'open', '2026-05-04 11:00:00', '2026-05-04 11:00:00'),
(28, 38, 3200.00, 640.00, 'open', '2026-05-04 12:00:00', '2026-05-04 12:00:00'),
(29, 39, 2400.00, 480.00, 'open', '2026-05-04 13:00:00', '2026-05-04 13:00:00'),
(30, 40, 2000.00, 400.00, 'open', '2026-05-04 14:00:00', '2026-05-04 14:00:00'),
(31, 41, 3200.00, 640.00, 'open', '2026-05-05 05:00:00', '2026-05-05 05:00:00'),
(32, 42, 1500.00, 300.00, 'open', '2026-05-05 05:30:00', '2026-05-05 05:30:00'),
(33, 43, 3200.00, 640.00, 'open', '2026-05-05 06:00:00', '2026-05-05 06:00:00'),
(34, 44, 2000.00, 400.00, 'open', '2026-05-05 06:30:00', '2026-05-05 06:30:00'),
(35, 45, 3200.00, 640.00, 'open', '2026-05-05 07:00:00', '2026-05-05 07:00:00'),
(36, 46, 3200.00, 640.00, 'open', '2026-05-05 07:30:00', '2026-05-05 07:30:00'),
(37, 47, 2000.00, 400.00, 'open', '2026-05-05 08:00:00', '2026-05-05 08:00:00'),
(38, 48, 3200.00, 640.00, 'open', '2026-05-05 08:30:00', '2026-05-05 08:30:00'),
(39, 49, 3200.00, 640.00, 'open', '2026-05-05 09:00:00', '2026-05-05 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `folio_charges`
--

CREATE TABLE `folio_charges` (
  `id` int(10) UNSIGNED NOT NULL,
  `folio_id` int(10) UNSIGNED NOT NULL,
  `charge_type` enum('room_rate','service','minibar','spa','restaurant','penalty','tax','other') NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `posted_by` int(10) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `folio_charges`
--

INSERT INTO `folio_charges` (`id`, `folio_id`, `charge_type`, `description`, `amount`, `posted_by`, `posted_at`) VALUES
(1, 1, 'room_rate', 'Room 102 ΓÇô Standard ├ù 4 nights', 2000.00, 3, '2026-05-01 21:45:43'),
(2, 1, 'minibar', 'Minibar consumption ΓÇô 2026-04-26', 65.00, 5, '2026-05-01 21:45:43'),
(3, 1, 'service', 'Extra pillow set + turndown service', 20.00, 6, '2026-05-01 21:45:43'),
(4, 2, 'room_rate', 'Room 201 ΓÇô Standard ├ù 4 nights', 2000.00, 4, '2026-05-01 21:45:43'),
(5, 2, 'restaurant', 'In-room dining ΓÇô 2026-04-24', 150.00, 4, '2026-05-01 21:45:43'),
(6, 3, 'room_rate', 'Room 202 ΓÇô Deluxe ├ù 4 nights', 3200.00, 3, '2026-05-01 21:45:43'),
(7, 3, 'spa', 'Aromatherapy massage ΓÇô 2026-04-25', 150.00, 3, '2026-05-01 21:45:43'),
(8, 4, 'room_rate', 'Room 203 ΓÇô Deluxe ├ù 3 nights', 2400.00, 4, '2026-05-01 21:45:43'),
(9, 4, 'restaurant', 'Restaurant dinner ΓÇô 2026-04-25', 100.00, 4, '2026-05-01 21:45:43'),
(10, 4, 'penalty', 'Late check-out fee (2 hrs past policy)', 50.00, 1, '2026-05-01 21:45:43'),
(11, 5, 'room_rate', 'Room 101 ΓÇô Standard ├ù 4 nights (pre-auth)', 2000.00, 3, '2026-05-01 21:45:43'),
(12, 6, 'room_rate', 'Room 301 ΓÇô Suite ├ù 6 nights (pre-auth)', 9000.00, 4, '2026-05-01 21:45:43'),
(13, 8, 'room_rate', 'Room 301 ΓÇô Suite ├ù 5 nights', 7500.00, 3, '2026-05-01 21:45:43'),
(14, 8, 'spa', 'Couples massage ΓÇô 2026-03-12', 200.00, 3, '2026-05-01 21:45:43'),
(15, 8, 'restaurant', 'Anniversary dinner ΓÇô 2026-03-14', 150.00, 3, '2026-05-01 21:45:43'),
(16, 9, 'room_rate', 'No-show fee ΓÇô deposit forfeited', 500.00, 1, '2026-05-01 21:45:43'),
(17, 1, 'room_rate', 'Room 102 ΓÇô Standard ├ù 4 nights', 2000.00, 3, '2026-05-01 21:53:46'),
(18, 1, 'minibar', 'Minibar consumption ΓÇô 2026-04-26', 65.00, 5, '2026-05-01 21:53:46'),
(19, 1, 'service', 'Extra pillow set + turndown service', 20.00, 6, '2026-05-01 21:53:46'),
(20, 2, 'room_rate', 'Room 201 ΓÇô Standard ├ù 4 nights', 2000.00, 4, '2026-05-01 21:53:46'),
(21, 2, 'restaurant', 'In-room dining ΓÇô 2026-04-24', 150.00, 4, '2026-05-01 21:53:46'),
(22, 3, 'room_rate', 'Room 202 ΓÇô Deluxe ├ù 4 nights', 3200.00, 3, '2026-05-01 21:53:46'),
(23, 3, 'spa', 'Aromatherapy massage ΓÇô 2026-04-25', 150.00, 3, '2026-05-01 21:53:46'),
(24, 4, 'room_rate', 'Room 203 ΓÇô Deluxe ├ù 3 nights', 2400.00, 4, '2026-05-01 21:53:46'),
(25, 4, 'restaurant', 'Restaurant dinner ΓÇô 2026-04-25', 100.00, 4, '2026-05-01 21:53:46'),
(26, 4, 'penalty', 'Late check-out fee (2 hrs past policy)', 50.00, 1, '2026-05-01 21:53:46'),
(27, 5, 'room_rate', 'Room 101 ΓÇô Standard ├ù 4 nights (pre-auth)', 2000.00, 3, '2026-05-01 21:53:46'),
(28, 6, 'room_rate', 'Room 301 ΓÇô Suite ├ù 6 nights (pre-auth)', 9000.00, 4, '2026-05-01 21:53:46'),
(29, 8, 'room_rate', 'Room 301 ΓÇô Suite ├ù 5 nights', 7500.00, 3, '2026-05-01 21:53:46'),
(30, 8, 'spa', 'Couples massage ΓÇô 2026-03-12', 200.00, 3, '2026-05-01 21:53:46'),
(31, 8, 'restaurant', 'Anniversary dinner ΓÇô 2026-03-14', 150.00, 3, '2026-05-01 21:53:46'),
(32, 9, 'room_rate', 'No-show fee ΓÇô deposit forfeited', 500.00, 1, '2026-05-01 21:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `found_items`
--

CREATE TABLE `found_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `lf_reference` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `location_type` enum('room','public') NOT NULL DEFAULT 'room',
  `room_number` varchar(10) DEFAULT NULL,
  `public_area` enum('lobby','pool','restaurant','elevator','parking','other') DEFAULT NULL,
  `condition` enum('good','damaged','fragile') NOT NULL DEFAULT 'good',
  `photo_url` varchar(500) DEFAULT NULL,
  `is_high_value` tinyint(1) NOT NULL DEFAULT 0,
  `escalated_to_security` tinyint(1) NOT NULL DEFAULT 0,
  `found_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `found_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('stored','matched','claimed','shipped','returned','disposed') NOT NULL DEFAULT 'stored'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `front_desk_queue`
--

CREATE TABLE `front_desk_queue` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `guest_name` varchar(200) NOT NULL DEFAULT '',
  `reason` varchar(100) NOT NULL DEFAULT 'no_email_manual_delivery',
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_reservation_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `billing_type` enum('group','individual') NOT NULL DEFAULT 'group'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`id`, `group_reservation_id`, `reservation_id`, `billing_type`) VALUES
(1, 1, 5, 'group'),
(2, 1, 6, 'individual');

-- --------------------------------------------------------

--
-- Table structure for table `group_reservations`
--

CREATE TABLE `group_reservations` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_name` varchar(200) NOT NULL,
  `coordinator_guest_id` int(10) UNSIGNED NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_reservations`
--

INSERT INTO `group_reservations` (`id`, `group_name`, `coordinator_guest_id`, `discount_percentage`, `created_at`) VALUES
(1, 'TechCorp Conference Group', 1, 10.00, '2026-05-01 21:45:43'),
(2, 'TechCorp Conference Group', 1, 10.00, '2026-05-01 21:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `nationality` varchar(80) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `loyalty_tier` enum('standard','silver','gold','platinum') NOT NULL DEFAULT 'standard',
  `lifetime_nights` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `lifetime_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_blacklisted` tinyint(1) NOT NULL DEFAULT 0,
  `blacklist_reason` text DEFAULT NULL,
  `is_vip` tinyint(1) NOT NULL DEFAULT 0,
  `gdpr_anonymized` tinyint(1) NOT NULL DEFAULT 0,
  `referred_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `loyalty_points` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `national_id`, `nationality`, `date_of_birth`, `loyalty_tier`, `lifetime_nights`, `lifetime_value`, `is_blacklisted`, `blacklist_reason`, `is_vip`, `gdpr_anonymized`, `referred_by`, `created_at`, `updated_at`, `loyalty_points`) VALUES
(1, 'John Smith', 'john.smith@gmail.com', '+1-212-555-0101', 'US123456', 'American', '1985-03-14', 'gold', 42, 35000.00, 0, NULL, 1, 0, NULL, '2026-05-01 21:45:43', '2026-05-01 21:45:44', 1500),
(2, 'Emma Wilson', 'emma.wilson@gmail.com', '+44-207-946-0102', 'UK789012', 'British', '1990-07-22', 'silver', 18, 9500.00, 0, NULL, 0, 0, NULL, '2026-05-01 21:45:43', '2026-05-01 21:45:44', 800),
(3, 'Carlos Rodriguez', 'carlos.rodriguez@gmail.com', '+34-91-555-0103', 'ES345678', 'Spanish', '1978-11-05', 'standard', 5, 2500.00, 0, NULL, 0, 0, 1, '2026-05-01 21:45:43', '2026-05-01 21:45:43', 0),
(4, 'Yuki Tanaka', 'yuki.tanaka@gmail.com', '+81-3-5555-0104', 'JP901234', 'Japanese', '1992-01-30', 'platinum', 95, 95000.00, 0, NULL, 1, 0, NULL, '2026-05-01 21:45:43', '2026-05-01 21:45:44', 250),
(5, 'Aisha Al-Rashid', 'aisha.alrashid@gmail.com', '+971-4-555-0105', 'AE567890', 'Emirati', '1988-09-18', 'silver', 22, 12000.00, 0, NULL, 0, 0, NULL, '2026-05-01 21:45:43', '2026-05-01 21:45:43', 0),
(6, 'Pierre Dubois', 'pierre.dubois@gmail.com', '+33-1-5555-0106', 'FR123789', 'French', '1975-05-25', 'gold', 55, 48000.00, 0, NULL, 0, 0, NULL, '2026-05-01 21:45:43', '2026-05-01 21:45:43', 0),
(7, 'Priya Sharma', 'priya.sharma@gmail.com', '+91-98-5555-0107', 'IN456012', 'Indian', '1995-12-10', 'standard', 8, 3200.00, 0, NULL, 0, 0, 2, '2026-05-01 21:45:43', '2026-05-01 21:45:43', 0),
(8, 'David Chen', 'david.chen@gmail.com', '+1-650-555-0108', 'US789345', 'American', '1982-06-03', 'gold', 38, 30000.00, 0, NULL, 0, 0, NULL, '2026-05-01 21:45:43', '2026-05-01 21:45:43', 0);

-- --------------------------------------------------------

--
-- Table structure for table `guest_corporate`
--

CREATE TABLE `guest_corporate` (
  `guest_id` int(10) UNSIGNED NOT NULL,
  `corporate_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_corporate`
--

INSERT INTO `guest_corporate` (`guest_id`, `corporate_id`) VALUES
(1, 1),
(4, 1),
(5, 3),
(6, 2);

-- --------------------------------------------------------

--
-- Table structure for table `guest_preferences`
--

CREATE TABLE `guest_preferences` (
  `id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `pref_key` varchar(100) NOT NULL,
  `pref_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_preferences`
--

INSERT INTO `guest_preferences` (`id`, `guest_id`, `pref_key`, `pref_value`) VALUES
(1, 1, 'pillow_type', 'firm'),
(2, 1, 'floor_preference', 'high'),
(3, 1, 'dietary', 'no pork'),
(4, 2, 'pillow_type', 'soft'),
(5, 2, 'room_temperature', 'cool'),
(6, 3, 'dietary', 'vegetarian'),
(7, 3, 'floor_preference', 'low'),
(8, 4, 'pillow_type', 'memory_foam'),
(9, 4, 'room_temperature', 'warm'),
(10, 4, 'newspaper', 'Financial Times'),
(11, 4, 'amenities', 'extra towels,fruit basket'),
(12, 5, 'dietary', 'halal'),
(13, 5, 'pillow_type', 'soft'),
(14, 6, 'newspaper', 'Le Monde'),
(15, 6, 'dietary', 'no shellfish'),
(16, 7, 'dietary', 'vegan'),
(17, 7, 'room_temperature', 'cool'),
(18, 8, 'pillow_type', 'firm'),
(19, 8, 'floor_preference', 'high');

-- --------------------------------------------------------

--
-- Table structure for table `housekeeper_performance`
--

CREATE TABLE `housekeeper_performance` (
  `id` int(10) UNSIGNED NOT NULL,
  `housekeeper_id` int(10) UNSIGNED NOT NULL,
  `avg_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_inspections` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `trend` enum('improving','stable','declining') NOT NULL DEFAULT 'stable',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `housekeeper_performance`
--

INSERT INTO `housekeeper_performance` (`id`, `housekeeper_id`, `avg_score`, `total_inspections`, `trend`, `updated_at`) VALUES
(1, 5, 88.50, 12, 'stable', '2026-05-02 00:45:45'),
(2, 6, 91.25, 15, 'improving', '2026-05-02 00:45:45'),
(3, 7, 85.00, 9, 'stable', '2026-05-02 00:45:45');

-- --------------------------------------------------------

--
-- Table structure for table `housekeeping_tasks`
--

CREATE TABLE `housekeeping_tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_id` int(10) UNSIGNED NOT NULL,
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `task_type` enum('cleaning','turndown','inspection','deep_clean','minibar_check') NOT NULL DEFAULT 'cleaning',
  `status` enum('pending','in_progress','done','skipped') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `quality_score` tinyint(3) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `housekeeping_tasks`
--

INSERT INTO `housekeeping_tasks` (`id`, `room_id`, `assigned_to`, `task_type`, `status`, `notes`, `quality_score`, `created_at`, `updated_at`, `completed_at`) VALUES
(1, 3, 5, 'cleaning', 'pending', 'Post-checkout full clean, change all linen', NULL, '2026-05-02 04:15:00', '2026-05-02 04:15:00', NULL),
(2, 4, 6, 'cleaning', 'in_progress', 'Deep clean + restock minibar and amenities', NULL, '2026-05-02 04:30:00', '2026-05-02 06:45:00', NULL),
(3, 5, 5, 'cleaning', 'done', 'Clean completed after checkout', 87, '2026-05-02 03:00:00', '2026-05-02 05:20:00', '2026-05-02 05:20:00'),
(4, 5, 7, 'inspection', 'pending', 'Supervisor sign-off required before re-let', NULL, '2026-05-02 05:25:00', '2026-05-02 05:25:00', NULL),
(5, 2, 6, 'turndown', 'done', 'VIP guest ├╗ chocolates and extra towels', 88, '2026-05-02 05:00:00', '2026-05-02 07:10:00', '2026-05-02 07:10:00'),
(6, 6, 7, 'minibar_check', 'done', 'Fully stocked, champagne chilled for Yuki', 72, '2026-05-02 03:30:00', '2026-05-02 04:55:00', '2026-05-02 04:55:00'),
(7, 1, 5, 'inspection', 'done', 'Room cleared and ready for check-in', 91, '2026-05-02 03:00:00', '2026-05-02 04:30:00', '2026-05-02 04:30:00'),
(8, 8, 6, 'cleaning', 'done', 'Routine clean, ready for next guest', 68, '2026-05-02 04:00:00', '2026-05-02 06:00:00', '2026-05-02 06:00:00'),
(9, 3, 7, 'deep_clean', 'pending', 'Full mattress flip and carpet steam clean', NULL, '2026-05-02 06:00:00', '2026-05-02 06:00:00', NULL),
(19, 2, 5, 'turndown', 'pending', 'Evening turndown ├╗ VIP guest, mints and rose petals requested', NULL, '2026-05-02 11:00:00', '2026-05-02 11:00:00', NULL),
(20, 10, 5, 'cleaning', 'done', 'Check-in clean for res 30. Vegetarian welcome plate placed.', 88, '2026-05-05 04:00:00', '2026-05-05 06:30:00', '2026-05-05 06:30:00'),
(21, 11, 6, 'cleaning', 'done', 'Check-in clean for res 31. Prayer mat positioned.', 91, '2026-05-05 04:30:00', '2026-05-05 06:45:00', '2026-05-05 06:45:00'),
(22, 10, 5, 'turndown', 'done', 'Evening turndown res 30.', 85, '2026-05-06 14:00:00', '2026-05-06 15:15:00', '2026-05-06 15:15:00'),
(23, 15, 6, 'cleaning', 'done', 'Pre-arrival clean for res 32. Business setup arranged.', 90, '2026-05-06 07:00:00', '2026-05-06 09:00:00', '2026-05-06 09:00:00'),
(24, 12, 7, 'cleaning', 'done', 'Pre-arrival clean for res 33. Anniversary rose petals placed.', 92, '2026-05-06 07:30:00', '2026-05-06 09:30:00', '2026-05-06 09:30:00'),
(25, 16, 5, 'cleaning', 'done', 'Pre-arrival clean for res 34. Champagne chilled.', 89, '2026-05-07 07:00:00', '2026-05-07 09:00:00', '2026-05-07 09:00:00'),
(26, 17, 6, 'cleaning', 'done', 'Pre-arrival clean for res 35. Yoga mat placed.', 87, '2026-05-07 07:30:00', '2026-05-07 09:30:00', '2026-05-07 09:30:00'),
(27, 10, 7, 'deep_clean', 'done', 'Post-checkout deep clean res 30 departure.', 84, '2026-05-08 09:00:00', '2026-05-08 11:30:00', '2026-05-08 11:30:00'),
(28, 5, 5, 'cleaning', 'done', 'VIP prep res 36. Fruit basket + champagne + platinum amenities.', 95, '2026-05-08 07:00:00', '2026-05-08 09:30:00', '2026-05-08 09:30:00'),
(29, 13, 6, 'cleaning', 'done', 'Pre-arrival clean res 37. Extra pillows set.', 88, '2026-05-08 07:30:00', '2026-05-08 09:00:00', '2026-05-08 09:00:00'),
(30, 18, 7, 'cleaning', 'done', 'Pre-arrival clean res 38. Quiet setup, DND sign placed.', 86, '2026-05-09 07:00:00', '2026-05-09 09:00:00', '2026-05-09 09:00:00'),
(31, 12, 5, 'turndown', 'done', 'Turndown res 33.', 83, '2026-05-10 14:00:00', '2026-05-10 15:00:00', '2026-05-10 15:00:00'),
(32, 19, 6, 'cleaning', 'in_progress', 'Pre-arrival clean res 39. Baby cot being set up.', NULL, '2026-05-10 07:00:00', '2026-05-10 07:00:00', NULL),
(33, 10, 7, 'cleaning', 'pending', 'Post-checkout clean res 30, then turnover for res 40.', NULL, '2026-05-11 04:00:00', '2026-05-11 04:00:00', NULL),
(34, 15, 5, 'turndown', 'pending', 'Turndown res 32.', NULL, '2026-05-11 14:00:00', '2026-05-11 14:00:00', NULL),
(35, 15, 6, 'cleaning', 'pending', 'Post-checkout clean res 32 ÔåÆ turnover for res 41.', NULL, '2026-05-12 04:00:00', '2026-05-12 04:00:00', NULL),
(36, 11, 7, 'cleaning', 'pending', 'Post-checkout clean res 31 ÔåÆ turnover for res 42.', NULL, '2026-05-12 04:30:00', '2026-05-12 04:30:00', NULL),
(37, 16, 5, 'cleaning', 'pending', 'Post-checkout clean res 34 ÔåÆ turnover for res 43.', NULL, '2026-05-13 04:00:00', '2026-05-13 04:00:00', NULL),
(38, 12, 6, 'cleaning', 'pending', 'Post-checkout clean res 33 ÔåÆ turnover for res 44.', NULL, '2026-05-14 04:00:00', '2026-05-14 04:00:00', NULL),
(39, 17, 7, 'cleaning', 'pending', 'Post-checkout clean res 35 ÔåÆ turnover for res 45.', NULL, '2026-05-15 04:00:00', '2026-05-15 04:00:00', NULL),
(40, 18, 5, 'cleaning', 'pending', 'Post-checkout clean res 38 ÔåÆ turnover for res 46.', NULL, '2026-05-16 04:00:00', '2026-05-16 04:00:00', NULL),
(41, 13, 6, 'cleaning', 'pending', 'Post-checkout clean res 37 ÔåÆ turnover for res 47.', NULL, '2026-05-17 04:00:00', '2026-05-17 04:00:00', NULL),
(42, 19, 7, 'cleaning', 'pending', 'Post-checkout clean res 39 ÔåÆ turnover for res 48.', NULL, '2026-05-18 04:00:00', '2026-05-18 04:00:00', NULL),
(43, 5, 5, 'cleaning', 'pending', 'Post-checkout clean res 36 ÔåÆ turnover for res 49.', NULL, '2026-05-19 04:00:00', '2026-05-19 04:00:00', NULL),
(44, 10, 5, 'cleaning', 'done', 'Check-in clean res 30. Vegetarian welcome plate placed.', 88, '2026-05-05 04:00:00', '2026-05-05 06:30:00', '2026-05-05 06:30:00'),
(45, 11, 6, 'cleaning', 'done', 'Check-in clean res 31. Prayer mat positioned.', 91, '2026-05-05 04:30:00', '2026-05-05 06:45:00', '2026-05-05 06:45:00'),
(46, 15, 6, 'cleaning', 'done', 'Pre-arrival clean res 32. Business setup.', 90, '2026-05-06 07:00:00', '2026-05-06 09:00:00', '2026-05-06 09:00:00'),
(47, 12, 7, 'cleaning', 'done', 'Pre-arrival clean res 33. Anniversary rose petals.', 92, '2026-05-06 07:30:00', '2026-05-06 09:30:00', '2026-05-06 09:30:00'),
(48, 16, 5, 'cleaning', 'done', 'Pre-arrival clean res 34. Champagne chilled.', 89, '2026-05-07 07:00:00', '2026-05-07 09:00:00', '2026-05-07 09:00:00'),
(49, 17, 6, 'cleaning', 'done', 'Pre-arrival clean res 35. Yoga mat placed.', 87, '2026-05-07 07:30:00', '2026-05-07 09:30:00', '2026-05-07 09:30:00'),
(50, 10, 7, 'deep_clean', 'done', 'Post-checkout res 30. Deep clean completed.', 84, '2026-05-08 09:00:00', '2026-05-08 11:30:00', '2026-05-08 11:30:00'),
(51, 5, 5, 'cleaning', 'done', 'VIP prep res 36. Platinum amenities set.', 95, '2026-05-08 07:00:00', '2026-05-08 09:30:00', '2026-05-08 09:30:00'),
(52, 13, 6, 'cleaning', 'done', 'Pre-arrival clean res 37. Extra pillows.', 88, '2026-05-08 07:30:00', '2026-05-08 09:00:00', '2026-05-08 09:00:00'),
(53, 18, 7, 'cleaning', 'done', 'Pre-arrival clean res 38. DND sign placed.', 86, '2026-05-09 07:00:00', '2026-05-09 09:00:00', '2026-05-09 09:00:00'),
(54, 19, 6, 'cleaning', 'in_progress', 'Pre-arrival clean res 39. Baby cot setup.', NULL, '2026-05-10 07:00:00', '2026-05-10 07:00:00', NULL),
(55, 10, 7, 'cleaning', 'pending', 'Turnover res 30 checkout ÔÇö prep for res 40.', NULL, '2026-05-11 04:00:00', '2026-05-11 04:00:00', NULL),
(56, 15, 5, 'cleaning', 'pending', 'Post-checkout res 32 ÔÇö prep for res 41.', NULL, '2026-05-12 04:00:00', '2026-05-12 04:00:00', NULL),
(57, 11, 6, 'cleaning', 'pending', 'Post-checkout res 31 ÔÇö prep for res 42.', NULL, '2026-05-12 04:30:00', '2026-05-12 04:30:00', NULL),
(58, 16, 5, 'cleaning', 'pending', 'Post-checkout res 34 ÔÇö prep for res 43.', NULL, '2026-05-13 04:00:00', '2026-05-13 04:00:00', NULL),
(59, 12, 6, 'cleaning', 'pending', 'Post-checkout res 33 ÔÇö prep for res 44.', NULL, '2026-05-14 04:00:00', '2026-05-14 04:00:00', NULL),
(60, 17, 7, 'cleaning', 'pending', 'Post-checkout res 35 ÔÇö prep for res 45.', NULL, '2026-05-15 04:00:00', '2026-05-15 04:00:00', NULL),
(61, 18, 5, 'cleaning', 'pending', 'Post-checkout res 38 ÔÇö prep for res 46.', NULL, '2026-05-16 04:00:00', '2026-05-16 04:00:00', NULL),
(62, 13, 6, 'cleaning', 'pending', 'Post-checkout res 37 ÔÇö prep for res 47.', NULL, '2026-05-17 04:00:00', '2026-05-17 04:00:00', NULL),
(63, 19, 7, 'cleaning', 'pending', 'Post-checkout res 39 ÔÇö prep for res 48.', NULL, '2026-05-18 04:00:00', '2026-05-18 04:00:00', NULL),
(64, 5, 5, 'cleaning', 'pending', 'Post-checkout res 36 ÔÇö prep for res 49.', NULL, '2026-05-19 04:00:00', '2026-05-19 04:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_change_log`
--

CREATE TABLE `inventory_change_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_type_id` int(10) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `old_virtual_max` int(11) NOT NULL DEFAULT 0,
  `new_virtual_max` int(11) NOT NULL DEFAULT 0,
  `changed_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `invoice_type` enum('group','individual') NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','finalized','paid','void') NOT NULL DEFAULT 'draft',
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `group_id`, `reservation_id`, `invoice_type`, `total_amount`, `tax_amount`, `discount_amount`, `status`, `generated_at`) VALUES
(1, 1, NULL, 'group', 16200.00, 0.00, 1800.00, 'finalized', '2026-05-03 21:00:19'),
(2, 1, 6, 'individual', 18000.00, 0.00, 0.00, 'finalized', '2026-05-03 21:01:02'),
(3, 1, NULL, 'group', 16200.00, 0.00, 1800.00, 'finalized', '2026-05-03 21:01:36'),
(4, 1, NULL, 'group', 16200.00, 0.00, 1800.00, 'finalized', '2026-05-03 21:03:43'),
(5, 1, NULL, 'group', 16200.00, 0.00, 1800.00, 'draft', '2026-05-03 21:06:27'),
(6, 2, NULL, 'group', 0.00, 0.00, 0.00, 'finalized', '2026-05-03 21:06:46');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `amount` decimal(10,2) NOT NULL,
  `item_type` enum('room_rate','service','minibar','tax','discount','other') NOT NULL DEFAULT 'other',
  `reservation_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_returns`
--

CREATE TABLE `item_returns` (
  `id` int(10) UNSIGNED NOT NULL,
  `found_item_id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `return_method` enum('pickup','courier') NOT NULL DEFAULT 'pickup',
  `return_address` text DEFAULT NULL,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `returned_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lost_and_found`
--

CREATE TABLE `lost_and_found` (
  `id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED DEFAULT NULL,
  `room_id` int(10) UNSIGNED DEFAULT NULL,
  `found_by` int(10) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('found','claimed','donated','discarded') NOT NULL DEFAULT 'found',
  `found_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lost_and_found`
--

INSERT INTO `lost_and_found` (`id`, `guest_id`, `room_id`, `found_by`, `description`, `status`, `found_at`) VALUES
(1, 2, 3, 5, 'Black leather wallet found under bed after checkout. Contains credit cards and cash.', 'found', '2026-05-01 21:45:43'),
(2, 1, 6, 6, 'Gold wristwatch left on bathroom shelf after March stay. Guest notified.', 'claimed', '2026-05-01 21:45:43'),
(3, NULL, 4, 5, 'Blue umbrella found in wardrobe. No guest could be identified.', 'donated', '2026-05-01 21:45:43'),
(4, 7, 4, 6, 'USB-C phone charger on bedside table after checkout.', 'found', '2026-05-01 21:45:43'),
(5, 6, 5, 7, 'Designer sunglasses case (Gucci) left in room safe.', 'found', '2026-05-01 21:45:43'),
(6, 2, 3, 5, 'Black leather wallet found under bed after checkout. Contains credit cards and cash.', 'found', '2026-05-01 21:53:46'),
(7, 1, 6, 6, 'Gold wristwatch left on bathroom shelf after March stay. Guest notified.', 'claimed', '2026-05-01 21:53:46'),
(8, NULL, 4, 5, 'Blue umbrella found in wardrobe. No guest could be identified.', 'donated', '2026-05-01 21:53:46'),
(9, 7, 4, 6, 'USB-C phone charger on bedside table after checkout.', 'found', '2026-05-01 21:53:46'),
(10, 6, 5, 7, 'Designer sunglasses case (Gucci) left in room safe.', 'found', '2026-05-01 21:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `lost_item_reports`
--

CREATE TABLE `lost_item_reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `lost_date` date DEFAULT NULL,
  `matched_found_item_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('open','matched','closed') NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `low_stock_alerts`
--

CREATE TABLE `low_stock_alerts` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `location` varchar(50) NOT NULL,
  `current_stock` int(11) NOT NULL,
  `min_threshold` int(11) NOT NULL,
  `status` enum('active','acknowledged','resolved') NOT NULL DEFAULT 'active',
  `acknowledged_by` int(10) UNSIGNED DEFAULT NULL,
  `acknowledged_at` datetime DEFAULT NULL,
  `escalated` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_orders`
--

CREATE TABLE `maintenance_orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_id` int(10) UNSIGNED NOT NULL,
  `reported_by` int(10) UNSIGNED DEFAULT NULL,
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','escalated') NOT NULL DEFAULT 'open',
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_orders`
--

INSERT INTO `maintenance_orders` (`id`, `room_id`, `reported_by`, `assigned_to`, `description`, `priority`, `status`, `resolved_at`, `created_at`, `updated_at`) VALUES
(1, 7, 3, NULL, 'AC unit not cooling and making grinding noise. Room taken out of service pending repair.', 'high', 'in_progress', NULL, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(2, 4, 5, NULL, 'Bathroom basin tap dripping. Needs washer replacement.', 'low', 'open', NULL, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(3, 2, 3, NULL, 'Guest reported TV remote unresponsive. Batteries replaced, issue resolved.', 'low', 'resolved', '2026-04-26 10:00:00', '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(4, 6, 4, NULL, 'Suite balcony sliding door lock is stiff. Lubrication and adjustment required before May 1.', 'medium', 'open', NULL, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(5, 3, 6, NULL, 'Bathroom ceiling light flickering. Likely loose fitting or blown bulb.', 'low', 'open', NULL, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(6, 7, 3, NULL, 'AC unit not cooling and making grinding noise. Room taken out of service pending repair.', 'high', 'in_progress', NULL, '2026-05-01 21:53:46', '2026-05-01 21:53:46'),
(7, 4, 5, NULL, 'Bathroom basin tap dripping. Needs washer replacement.', 'low', 'open', NULL, '2026-05-01 21:53:46', '2026-05-01 21:53:46'),
(8, 2, 3, NULL, 'Guest reported TV remote unresponsive. Batteries replaced, issue resolved.', 'low', 'resolved', '2026-04-26 10:00:00', '2026-05-01 21:53:46', '2026-05-01 21:53:46'),
(9, 6, 4, NULL, 'Suite balcony sliding door lock is stiff. Lubrication and adjustment required before May 1.', 'medium', 'open', NULL, '2026-05-01 21:53:46', '2026-05-01 21:53:46'),
(10, 3, 6, NULL, 'Bathroom ceiling light flickering. Likely loose fitting or blown bulb.', 'low', 'open', NULL, '2026-05-01 21:53:46', '2026-05-01 21:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `minibar_inventory`
--

CREATE TABLE `minibar_inventory` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `current_stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_restocked_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `minibar_inventory`
--

INSERT INTO `minibar_inventory` (`id`, `room_id`, `item_id`, `current_stock`, `last_restocked_at`) VALUES
(1, 2, 1, 4, '2026-05-02 00:45:44'),
(2, 2, 2, 2, '2026-05-02 00:45:44'),
(3, 2, 3, 3, '2026-05-02 00:45:44'),
(4, 2, 4, 4, '2026-05-02 00:45:44'),
(5, 2, 5, 2, '2026-05-02 00:45:44'),
(6, 2, 6, 2, '2026-05-02 00:45:44'),
(7, 2, 7, 3, '2026-05-02 00:45:44'),
(8, 2, 8, 2, '2026-05-02 00:45:44'),
(9, 2, 9, 1, '2026-05-02 00:45:44'),
(10, 2, 10, 2, '2026-05-02 00:45:44'),
(11, 4, 1, 4, '2026-05-02 00:45:44'),
(12, 4, 2, 2, '2026-05-02 00:45:44'),
(13, 4, 3, 3, '2026-05-02 00:45:44'),
(14, 4, 4, 4, '2026-05-02 00:45:44'),
(15, 4, 5, 2, '2026-05-02 00:45:44'),
(16, 4, 6, 2, '2026-05-02 00:45:44'),
(17, 4, 7, 3, '2026-05-02 00:45:44'),
(18, 4, 8, 2, '2026-05-02 00:45:44'),
(19, 4, 9, 1, '2026-05-02 00:45:44'),
(20, 4, 10, 2, '2026-05-02 00:45:44'),
(21, 6, 1, 6, '2026-05-02 00:45:44'),
(22, 6, 2, 4, '2026-05-02 00:45:44'),
(23, 6, 3, 4, '2026-05-02 00:45:44'),
(24, 6, 4, 6, '2026-05-02 00:45:44'),
(25, 6, 5, 4, '2026-05-02 00:45:44'),
(26, 6, 6, 3, '2026-05-02 00:45:44'),
(27, 6, 7, 4, '2026-05-02 00:45:44'),
(28, 6, 8, 3, '2026-05-02 00:45:44'),
(29, 6, 9, 2, '2026-05-02 00:45:44'),
(30, 6, 10, 4, '2026-05-02 00:45:44');

-- --------------------------------------------------------

--
-- Table structure for table `minibar_items`
--

CREATE TABLE `minibar_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `reorder_threshold` int(10) UNSIGNED NOT NULL DEFAULT 2,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `minibar_items`
--

INSERT INTO `minibar_items` (`id`, `name`, `sku`, `price`, `reorder_threshold`, `is_active`) VALUES
(1, 'Still Water 500ml', 'MB-WATER-500', 3.00, 3, 1),
(2, 'Sparkling Water 500ml', 'MB-SPARK-500', 3.50, 3, 1),
(3, 'Orange Juice 330ml', 'MB-OJ-330', 4.50, 2, 1),
(4, 'Cola 330ml', 'MB-COLA-330', 4.00, 3, 1),
(5, 'Beer (Local) 330ml', 'MB-BEER-330', 8.00, 2, 1),
(6, 'Mixed Nuts 50g', 'MB-NUTS-050', 6.00, 2, 1),
(7, 'Chocolate Bar', 'MB-CHOC-001', 5.00, 2, 1),
(8, 'Chips 40g', 'MB-CHIP-040', 4.50, 2, 1),
(9, 'Sparkling Wine 200ml', 'MB-WINE-200', 18.00, 1, 1),
(10, 'Mineral Water 1L', 'MB-WATER-1L', 5.00, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `minibar_logs`
--

CREATE TABLE `minibar_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `housekeeper_id` int(10) UNSIGNED DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `logged_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `folio_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('cash','credit_card','debit_card','bank_transfer','online') NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `processed_by` int(10) UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `folio_id`, `amount`, `method`, `reference`, `processed_by`, `processed_at`) VALUES
(40, 2, 2150.00, 'credit_card', NULL, NULL, '2026-04-22 08:15:00'),
(41, 3, 1500.00, 'credit_card', NULL, NULL, '2026-04-23 12:30:00'),
(42, 3, 1850.00, 'credit_card', NULL, NULL, '2026-04-24 08:00:00'),
(43, 4, 1200.00, 'cash', NULL, NULL, '2026-04-25 06:45:00'),
(44, 4, 1350.00, 'credit_card', NULL, NULL, '2026-04-25 13:20:00'),
(45, 8, 3000.00, 'credit_card', NULL, NULL, '2026-04-26 07:00:00'),
(46, 8, 2500.00, 'credit_card', NULL, NULL, '2026-04-27 10:15:00'),
(47, 8, 2350.00, 'bank_transfer', NULL, NULL, '2026-04-28 06:30:00'),
(48, 6, 2000.00, 'bank_transfer', NULL, NULL, '2026-04-29 08:45:00'),
(49, 5, 500.00, 'online', NULL, NULL, '2026-04-30 11:00:00'),
(50, 9, 500.00, 'credit_card', NULL, NULL, '2026-05-01 06:00:00'),
(51, 1, 500.00, 'credit_card', NULL, NULL, '2026-05-01 07:30:00'),
(52, 6, 3500.00, 'credit_card', NULL, NULL, '2026-05-01 11:00:00'),
(53, 7, 800.00, 'cash', NULL, NULL, '2026-05-01 12:45:00'),
(54, 1, 750.00, 'credit_card', NULL, NULL, '2026-05-02 06:15:00'),
(55, 6, 1200.00, 'credit_card', NULL, NULL, '2026-05-02 08:30:00'),
(56, 11, 500.00, 'credit_card', NULL, NULL, '2026-05-02 01:07:51'),
(57, 12, 400.00, 'credit_card', NULL, NULL, '2026-05-02 01:07:51'),
(58, 13, 300.00, 'credit_card', NULL, NULL, '2026-05-02 01:07:51'),
(59, 14, 300.00, 'credit_card', NULL, NULL, '2026-05-02 01:07:51'),
(60, 15, 250.00, 'credit_card', NULL, NULL, '2026-05-02 01:07:51'),
(61, 1, 835.00, 'credit_card', 'REF-TODAY-001', 3, '2026-05-04 22:13:04'),
(62, 7, 700.00, 'cash', 'REF-TODAY-002', 3, '2026-05-04 22:13:04'),
(63, 17, 450.00, 'credit_card', 'REF-TODAY-003', 4, '2026-05-04 22:13:04'),
(70, 20, 300.00, 'credit_card', 'DEP-30-0505', 3, '2026-05-05 11:05:00'),
(71, 21, 400.00, 'credit_card', 'DEP-31-0505', 4, '2026-05-05 12:35:00'),
(72, 22, 640.00, 'credit_card', 'DEP-32-0506', 3, '2026-05-06 10:00:00'),
(73, 23, 300.00, 'cash', 'DEP-33-0506', 4, '2026-05-06 11:30:00'),
(74, 24, 640.00, 'credit_card', 'DEP-34-0507', 3, '2026-05-07 10:00:00'),
(75, 25, 480.00, 'debit_card', 'DEP-35-0507', 4, '2026-05-07 11:00:00'),
(76, 26, 640.00, 'credit_card', 'DEP-36-0508', 3, '2026-05-08 10:30:00'),
(77, 27, 300.00, 'cash', 'DEP-37-0508', 4, '2026-05-08 11:00:00'),
(78, 20, 1200.00, 'credit_card', 'SET-30-0508', 3, '2026-05-08 08:30:00'),
(79, 28, 640.00, 'credit_card', 'DEP-38-0509', 3, '2026-05-09 10:00:00'),
(80, 23, 1200.00, 'cash', 'SET-33-0509', 4, '2026-05-09 08:00:00'),
(81, 20, 1200.00, 'credit_card', 'SET-30-0511', 3, '2026-05-08 08:30:00'),
(82, 22, 2560.00, 'credit_card', 'SET-32-0510', 3, '2026-05-10 08:30:00'),
(83, 25, 1920.00, 'credit_card', 'SET-35-0510', 4, '2026-05-10 09:30:00'),
(84, 30, 400.00, 'credit_card', 'DEP-40-0511', 3, '2026-05-11 10:00:00'),
(85, 21, 1600.00, 'credit_card', 'SET-31-0511', 4, '2026-05-11 08:00:00'),
(86, 27, 1200.00, 'credit_card', 'SET-37-0511', 3, '2026-05-11 09:00:00'),
(87, 31, 640.00, 'credit_card', 'DEP-41-0512', 4, '2026-05-12 10:00:00'),
(88, 32, 300.00, 'cash', 'DEP-42-0512', 3, '2026-05-12 10:30:00'),
(89, 24, 1500.00, 'credit_card', 'PAR-34-0512', 4, '2026-05-12 07:00:00'),
(90, 33, 640.00, 'debit_card', 'DEP-43-0513', 4, '2026-05-13 10:00:00'),
(91, 28, 2560.00, 'credit_card', 'SET-38-0513', 3, '2026-05-13 08:00:00'),
(92, 29, 1920.00, 'credit_card', 'SET-39-0513', 4, '2026-05-13 09:00:00'),
(93, 34, 400.00, 'credit_card', 'DEP-44-0514', 3, '2026-05-14 10:00:00'),
(94, 24, 1060.00, 'credit_card', 'SET-34-0514', 4, '2026-05-14 08:00:00'),
(95, 35, 640.00, 'credit_card', 'DEP-45-0515', 3, '2026-05-15 10:00:00'),
(96, 30, 1600.00, 'bank_transfer', 'SET-40-0515', 4, '2026-05-15 08:00:00'),
(97, 32, 1200.00, 'credit_card', 'SET-42-0515', 3, '2026-05-15 09:00:00'),
(98, 36, 640.00, 'credit_card', 'DEP-46-0516', 4, '2026-05-16 10:00:00'),
(99, 26, 1500.00, 'credit_card', 'PAR-36-0516', 3, '2026-05-16 07:00:00'),
(100, 37, 400.00, 'credit_card', 'DEP-47-0517', 4, '2026-05-17 10:00:00'),
(101, 26, 1060.00, 'credit_card', 'SET-36-0517', 3, '2026-05-17 08:00:00'),
(102, 38, 640.00, 'credit_card', 'DEP-48-0518', 3, '2026-05-18 10:00:00'),
(103, 31, 2560.00, 'credit_card', 'SET-41-0518', 4, '2026-05-18 08:00:00'),
(104, 33, 2560.00, 'credit_card', 'SET-43-0518', 3, '2026-05-18 09:00:00'),
(105, 39, 640.00, 'debit_card', 'DEP-49-0519', 4, '2026-05-19 10:00:00'),
(106, 34, 1600.00, 'credit_card', 'SET-44-0519', 3, '2026-05-19 08:00:00'),
(107, 35, 2560.00, 'credit_card', 'SET-45-0520', 4, '2026-05-20 08:00:00'),
(108, 36, 640.00, 'credit_card', 'BAL-46-0520', 3, '2026-05-20 09:00:00'),
(109, 37, 1600.00, 'cash', 'SET-47-0521', 4, '2026-05-21 08:00:00'),
(110, 38, 2560.00, 'credit_card', 'SET-48-0522', 3, '2026-05-22 08:00:00'),
(111, 39, 2560.00, 'credit_card', 'SET-49-0523', 4, '2026-05-23 08:00:00'),
(112, 5, 500.00, 'credit_card', 'PAY-MAY03-001', 3, '2026-05-03 06:00:00'),
(113, 11, 450.00, 'online', 'PAY-MAY03-002', 4, '2026-05-03 08:30:00'),
(114, 16, 380.00, 'cash', 'PAY-MAY03-003', 3, '2026-05-03 11:00:00'),
(115, 12, 600.00, 'credit_card', 'PAY-MAY04-001', 3, '2026-05-04 05:30:00'),
(116, 13, 420.00, 'bank_transfer', 'PAY-MAY04-002', 4, '2026-05-04 09:00:00'),
(117, 14, 350.00, 'credit_card', 'PAY-MAY04-003', 3, '2026-05-04 13:00:00'),
(118, 1, 1005.78, 'credit_card', 'HIST-MAR-18656', NULL, '2026-03-23 09:00:00'),
(119, 2, 580.89, 'cash', 'HIST-MAR-50328', NULL, '2026-03-27 08:29:00'),
(120, 3, 1122.88, 'bank_transfer', 'HIST-MAR-52569', NULL, '2026-03-30 09:29:00'),
(121, 4, 931.90, 'bank_transfer', 'HIST-MAR-96558', NULL, '2026-03-26 11:52:00'),
(122, 5, 1230.16, 'credit_card', 'HIST-MAR-54225', NULL, '2026-03-27 12:52:00'),
(123, 6, 1017.58, 'cash', 'HIST-MAR-35087', NULL, '2026-03-23 13:49:00'),
(125, 1, 988.69, 'bank_transfer', 'HIST-MAR-72286', NULL, '2026-03-29 13:01:00'),
(126, 2, 789.43, 'online', 'HIST-MAR-74282', NULL, '2026-03-28 10:40:00'),
(127, 3, 991.41, 'credit_card', 'HIST-MAR-22111', NULL, '2026-03-27 07:44:00'),
(128, 4, 1388.03, 'online', 'HIST-MAR-39524', NULL, '2026-03-29 08:20:00'),
(129, 5, 912.21, 'cash', 'HIST-MAR-75969', NULL, '2026-03-26 16:19:00'),
(132, 1, 2084.65, 'debit_card', 'HIST-APR-50329', NULL, '2026-04-06 13:59:00'),
(133, 2, 842.99, 'online', 'HIST-APR-37366', NULL, '2026-04-04 06:31:00'),
(134, 3, 1310.26, 'bank_transfer', 'HIST-APR-99155', NULL, '2026-04-06 16:00:00'),
(135, 4, 1797.50, 'debit_card', 'HIST-APR-78658', NULL, '2026-04-01 06:51:00'),
(136, 5, 983.90, 'bank_transfer', 'HIST-APR-13960', NULL, '2026-04-06 11:21:00'),
(137, 6, 1434.45, 'debit_card', 'HIST-APR-20295', NULL, '2026-04-06 12:35:00'),
(138, 7, 1385.45, 'bank_transfer', 'HIST-APR-85267', NULL, '2026-04-02 12:43:00'),
(139, 8, 969.72, 'cash', 'HIST-APR-81596', NULL, '2026-04-04 15:56:00'),
(140, 9, 1217.88, 'cash', 'HIST-APR-71335', NULL, '2026-04-06 10:09:00'),
(141, 11, 1745.00, 'online', 'HIST-APR-91777', NULL, '2026-04-04 06:32:00'),
(142, 12, 1519.11, 'credit_card', 'HIST-APR-46436', NULL, '2026-04-03 13:53:00'),
(143, 13, 1743.80, 'bank_transfer', 'HIST-APR-78397', NULL, '2026-04-04 08:36:00'),
(144, 14, 1127.32, 'online', 'HIST-APR-67401', NULL, '2026-04-03 18:46:00'),
(145, 15, 2136.07, 'credit_card', 'HIST-APR-49222', NULL, '2026-04-03 07:03:00'),
(146, 16, 2260.25, 'bank_transfer', 'HIST-APR-71234', NULL, '2026-04-02 07:55:00'),
(147, 1, 1237.83, 'debit_card', 'HIST-APR-97856', NULL, '2026-04-09 07:00:00'),
(148, 2, 1963.36, 'cash', 'HIST-APR-87119', NULL, '2026-04-12 13:28:00'),
(149, 3, 1908.18, 'debit_card', 'HIST-APR-20578', NULL, '2026-04-12 16:29:00'),
(150, 4, 850.56, 'bank_transfer', 'HIST-APR-33581', NULL, '2026-04-11 09:39:00'),
(151, 5, 1416.42, 'bank_transfer', 'HIST-APR-18840', NULL, '2026-04-13 14:14:00'),
(152, 6, 1343.86, 'bank_transfer', 'HIST-APR-40144', NULL, '2026-04-08 12:02:00'),
(153, 7, 2395.61, 'cash', 'HIST-APR-91935', NULL, '2026-04-09 14:29:00'),
(154, 8, 1669.49, 'bank_transfer', 'HIST-APR-28415', NULL, '2026-04-09 12:56:00'),
(155, 9, 969.08, 'bank_transfer', 'HIST-APR-84768', NULL, '2026-04-11 13:01:00'),
(156, 11, 1544.29, 'cash', 'HIST-APR-87222', NULL, '2026-04-09 12:52:00'),
(157, 12, 2379.88, 'debit_card', 'HIST-APR-48649', NULL, '2026-04-10 13:51:00'),
(158, 13, 1763.54, 'cash', 'HIST-APR-73467', NULL, '2026-04-10 13:01:00'),
(159, 14, 1294.48, 'credit_card', 'HIST-APR-21877', NULL, '2026-04-10 16:49:00'),
(160, 15, 1954.78, 'debit_card', 'HIST-APR-48608', NULL, '2026-04-08 12:02:00'),
(161, 16, 2139.63, 'credit_card', 'HIST-APR-22444', NULL, '2026-04-09 07:48:00'),
(162, 1, 2082.02, 'debit_card', 'HIST-APR-75950', NULL, '2026-04-16 14:51:00'),
(163, 2, 1440.19, 'online', 'HIST-APR-48251', NULL, '2026-04-18 11:38:00'),
(164, 3, 2657.72, 'credit_card', 'HIST-APR-94362', NULL, '2026-04-19 08:38:00'),
(165, 4, 2020.48, 'online', 'HIST-APR-68505', NULL, '2026-04-18 10:48:00'),
(166, 5, 2904.96, 'debit_card', 'HIST-APR-95920', NULL, '2026-04-19 12:36:00'),
(167, 6, 1747.85, 'debit_card', 'HIST-APR-49450', NULL, '2026-04-15 11:47:00'),
(168, 7, 2162.87, 'online', 'HIST-APR-74207', NULL, '2026-04-17 18:37:00'),
(169, 8, 1219.75, 'credit_card', 'HIST-APR-82822', NULL, '2026-04-19 14:49:00'),
(170, 9, 1216.35, 'debit_card', 'HIST-APR-71939', NULL, '2026-04-15 10:36:00'),
(171, 11, 2759.09, 'debit_card', 'HIST-APR-60728', NULL, '2026-04-20 15:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `gateway_token` varchar(255) NOT NULL,
  `card_last4` char(4) NOT NULL,
  `card_brand` varchar(20) NOT NULL DEFAULT '',
  `expiry_month` tinyint(3) UNSIGNED NOT NULL,
  `expiry_year` smallint(5) UNSIGNED NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `guest_id`, `gateway_token`, `card_last4`, `card_brand`, `expiry_month`, `expiry_year`, `is_default`, `created_at`) VALUES
(1, 1, 'tok_john_smith_4242', '4242', 'visa', 12, 2027, 1, '2026-05-01 21:45:44'),
(2, 2, 'tok_emma_wilson_1234', '1234', 'mastercard', 6, 2026, 1, '2026-05-01 21:45:44'),
(3, 4, 'tok_yuki_tanaka_5678', '5678', 'visa', 3, 2028, 1, '2026-05-01 21:45:44');

-- --------------------------------------------------------

--
-- Table structure for table `payment_retry_queue`
--

CREATE TABLE `payment_retry_queue` (
  `id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) NOT NULL DEFAULT '',
  `idempotency_key` varchar(255) NOT NULL,
  `attempt_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `next_retry_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_debts`
--

CREATE TABLE `pending_debts` (
  `id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `preventative_schedules`
--

CREATE TABLE `preventative_schedules` (
  `id` int(10) UNSIGNED NOT NULL,
  `work_order_id` int(10) UNSIGNED NOT NULL,
  `asset_id` int(10) UNSIGNED DEFAULT NULL,
  `room_id` int(10) UNSIGNED DEFAULT NULL,
  `maintenance_type` enum('hvac','elevator','plumbing','electrical','deep_cleaning','other') NOT NULL,
  `scheduled_date` date NOT NULL,
  `estimated_minutes` int(10) UNSIGNED NOT NULL DEFAULT 60,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `recurrence_frequency` enum('weekly','monthly','quarterly','yearly') DEFAULT NULL,
  `next_due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property_wide_alerts`
--

CREATE TABLE `property_wide_alerts` (
  `id` int(10) UNSIGNED NOT NULL,
  `alert_type` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `triggered_by_work_order_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','resolved') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qa_inspections`
--

CREATE TABLE `qa_inspections` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_id` int(10) UNSIGNED NOT NULL,
  `inspector_id` int(10) UNSIGNED NOT NULL,
  `inspection_date` date NOT NULL,
  `overall_result` enum('pass','fail','corrective_action') NOT NULL,
  `checklist_scores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`checklist_scores`)),
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quality_scores`
--

CREATE TABLE `quality_scores` (
  `id` int(10) UNSIGNED NOT NULL,
  `inspection_id` int(10) UNSIGNED NOT NULL,
  `housekeeper_id` int(10) UNSIGNED NOT NULL,
  `room_id` int(10) UNSIGNED NOT NULL,
  `cleanliness` tinyint(3) UNSIGNED NOT NULL CHECK (`cleanliness` between 0 and 100),
  `presentation` tinyint(3) UNSIGNED NOT NULL CHECK (`presentation` between 0 and 100),
  `completeness` tinyint(3) UNSIGNED NOT NULL CHECK (`completeness` between 0 and 100),
  `speed` tinyint(3) UNSIGNED NOT NULL CHECK (`speed` between 0 and 100),
  `overall_score` decimal(5,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `photo_urls` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photo_urls`)),
  `submitted_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `is_disputed` tinyint(1) NOT NULL DEFAULT 0,
  `dispute_resolution` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `replacement_review_flags`
--

CREATE TABLE `replacement_review_flags` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_id` int(10) UNSIGNED DEFAULT NULL,
  `asset_id` int(10) UNSIGNED DEFAULT NULL,
  `emergency_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `flagged_at` datetime NOT NULL DEFAULT current_timestamp(),
  `reviewed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `room_id` int(10) UNSIGNED NOT NULL,
  `assigned_by` int(10) UNSIGNED DEFAULT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `actual_check_in` datetime DEFAULT NULL,
  `actual_check_out` datetime DEFAULT NULL,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled','no_show') NOT NULL DEFAULT 'pending',
  `adults` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `children` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `special_requests` text DEFAULT NULL,
  `deposit_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deposit_paid` tinyint(1) NOT NULL DEFAULT 0,
  `is_group` tinyint(1) NOT NULL DEFAULT 0,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `guest_id`, `room_id`, `assigned_by`, `check_in_date`, `check_out_date`, `actual_check_in`, `actual_check_out`, `status`, `adults`, `children`, `special_requests`, `deposit_amount`, `deposit_paid`, `is_group`, `group_id`, `total_price`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 3, '2026-04-25', '2026-04-29', '2026-04-25 14:00:00', '2026-04-29 11:00:00', 'checked_out', 2, 0, 'Extra pillows, high floor preference', 500.00, 1, 0, NULL, 2000.00, '2026-05-01 21:45:43', '2026-05-02 01:16:35'),
(2, 2, 3, 4, '2026-04-22', '2026-04-26', '2026-04-22 15:10:00', '2026-04-26 11:45:00', 'checked_out', 1, 0, NULL, 500.00, 1, 0, NULL, 2000.00, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(3, 7, 4, 3, '2026-04-23', '2026-04-27', '2026-04-23 13:00:00', '2026-04-27 10:30:00', 'checked_out', 2, 1, 'Baby cot needed, vegan breakfast', 800.00, 1, 0, NULL, 3200.00, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(4, 6, 5, 4, '2026-04-24', '2026-04-27', '2026-04-24 16:00:00', '2026-04-27 12:30:00', 'checked_out', 1, 0, 'Late check-out requested', 800.00, 1, 0, NULL, 2400.00, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(5, 3, 1, 3, '2026-04-28', '2026-05-02', NULL, NULL, 'confirmed', 2, 0, 'Vegetarian welcome plate', 500.00, 1, 0, NULL, 2000.00, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(6, 4, 6, 4, '2026-05-03', '2026-05-07', '2026-05-01 14:00:00', NULL, 'confirmed', 2, 0, 'VIP guest - platinum member', 2000.00, 1, 1, 1, 9000.00, '2026-05-01 21:45:43', '2026-05-03 01:02:31'),
(7, 5, 8, NULL, '2026-05-03', '2026-05-06', NULL, NULL, 'confirmed', 1, 0, 'Halal dining options, prayer mat in room', 0.00, 0, 0, NULL, 1000.00, '2026-05-01 21:45:43', '2026-05-03 01:02:31'),
(8, 8, 1, 3, '2026-05-03', '2026-05-05', NULL, NULL, 'cancelled', 2, 0, 'VIP guest, late arrival', 500.00, 0, 0, NULL, 1500.00, '2026-05-01 21:45:43', '2026-05-03 01:02:04'),
(9, 1, 6, 3, '2026-03-10', '2026-03-15', '2026-03-10 14:00:00', '2026-03-15 12:00:00', 'checked_out', 2, 0, 'Anniversary celebration ΓÇô roses and champagne in room', 2000.00, 1, 0, NULL, 7500.00, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(10, 4, 8, 4, '2026-02-20', '2026-02-22', NULL, NULL, 'no_show', 2, 0, NULL, 500.00, 1, 0, NULL, 1000.00, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(21, 2, 3, 1, '2026-05-03', '2026-05-08', NULL, NULL, 'confirmed', 2, 0, 'VIP - anniversary stay', 500.00, 1, 0, NULL, 1800.00, '2026-05-02 01:07:14', '2026-05-03 01:02:31'),
(22, 5, 8, 1, '2026-05-02', '2026-05-06', NULL, NULL, 'confirmed', 1, 0, 'VIP guest ├╗ Halal meals, prayer mat, late checkout requested', 400.00, 1, 0, NULL, 1400.00, '2026-05-02 01:07:14', '2026-05-02 01:07:14'),
(23, 8, 6, 1, '2026-05-03', '2026-05-06', NULL, NULL, 'confirmed', 2, 1, 'Extra bed for child, airport pickup requested', 300.00, 1, 0, NULL, 1200.00, '2026-05-02 01:07:14', '2026-05-03 01:02:31'),
(24, 1, 2, 1, '2026-04-29', '2026-05-03', '2026-04-28 14:00:00', NULL, 'checked_in', 2, 0, 'Extra pillows, high floor preference', 300.00, 1, 0, NULL, 1600.00, '2026-05-02 01:07:14', '2026-05-03 01:02:04'),
(25, 7, 4, 1, '2026-04-30', '2026-05-03', '2026-04-29 15:30:00', NULL, 'checked_in', 1, 0, 'Late checkout ├╗ conference attendee', 250.00, 1, 0, NULL, 1200.00, '2026-05-02 01:07:14', '2026-05-03 01:02:04'),
(26, 2, 1, NULL, '2026-05-05', '2026-05-06', NULL, NULL, 'pending', 1, 0, '', 0.00, 0, 1, 26, 500.00, '2026-05-03 00:09:52', '2026-05-03 00:09:52'),
(27, 2, 1, NULL, '2026-05-05', '2026-05-20', '2026-05-03 03:29:41', NULL, 'checked_in', 1, 0, '', 1500.00, 1, 0, NULL, 7500.00, '2026-05-03 00:19:22', '2026-05-03 00:29:41'),
(28, 1, 20, NULL, '2026-05-14', '2026-05-23', '2026-05-03 03:51:29', NULL, 'checked_in', 1, 0, '', 900.00, 1, 0, NULL, 4500.00, '2026-05-03 00:50:01', '2026-05-03 00:52:07'),
(29, 1, 21, NULL, '2026-05-06', '2026-05-29', NULL, NULL, 'pending', 1, 0, '', 0.00, 0, 0, NULL, 34500.00, '2026-05-03 00:52:50', '2026-05-03 00:52:50'),
(30, 3, 10, NULL, '2026-05-06', '2026-05-15', '2026-05-03 23:47:59', NULL, 'checked_in', 1, 0, '', 900.00, 1, 1, 30, 4500.00, '2026-05-03 20:46:59', '2026-05-03 20:47:59'),
(31, 5, 11, 4, '2026-05-05', '2026-05-09', '2026-05-05 15:30:00', NULL, 'checked_in', 2, 0, 'Halal dining, prayer mat', 400.00, 1, 0, NULL, 2000.00, '2026-05-03 08:00:00', '2026-05-05 12:30:00'),
(32, 8, 15, 3, '2026-05-06', '2026-05-10', NULL, NULL, 'confirmed', 1, 0, 'Business stay, early check-in', 640.00, 1, 0, NULL, 3200.00, '2026-05-04 06:00:00', '2026-05-04 06:00:00'),
(33, 2, 12, 4, '2026-05-06', '2026-05-09', NULL, NULL, 'confirmed', 2, 0, 'Anniversary dinner arrangement', 300.00, 1, 0, NULL, 1500.00, '2026-05-04 07:00:00', '2026-05-04 07:00:00'),
(34, 6, 16, 3, '2026-05-07', '2026-05-11', NULL, NULL, 'confirmed', 2, 0, 'Champagne and flowers on arrival', 640.00, 1, 0, NULL, 3200.00, '2026-05-04 08:00:00', '2026-05-04 08:00:00'),
(35, 7, 17, 4, '2026-05-07', '2026-05-10', NULL, NULL, 'confirmed', 1, 0, 'Vegan breakfast, yoga mat', 480.00, 1, 0, NULL, 2400.00, '2026-05-04 09:00:00', '2026-05-04 09:00:00'),
(36, 4, 5, 3, '2026-05-08', '2026-05-12', NULL, NULL, 'confirmed', 2, 0, 'VIP platinum - fruit basket, champagne', 640.00, 1, 0, NULL, 3200.00, '2026-05-04 10:00:00', '2026-05-04 10:00:00'),
(37, 1, 13, 4, '2026-05-08', '2026-05-11', NULL, NULL, 'confirmed', 2, 0, 'High floor, extra pillows', 300.00, 1, 0, NULL, 1500.00, '2026-05-04 11:00:00', '2026-05-04 11:00:00'),
(38, 3, 18, 3, '2026-05-09', '2026-05-13', NULL, NULL, 'confirmed', 1, 0, 'Quiet room, no early housekeeping', 640.00, 1, 0, NULL, 3200.00, '2026-05-04 12:00:00', '2026-05-04 12:00:00'),
(39, 8, 19, 4, '2026-05-10', '2026-05-13', NULL, NULL, 'confirmed', 2, 1, 'Extra bed for child, baby cot', 480.00, 1, 0, NULL, 2400.00, '2026-05-04 13:00:00', '2026-05-04 13:00:00'),
(40, 5, 10, 3, '2026-05-11', '2026-05-15', NULL, NULL, 'confirmed', 1, 0, 'Halal meals throughout stay', 400.00, 1, 0, NULL, 2000.00, '2026-05-04 14:00:00', '2026-05-04 14:00:00'),
(41, 2, 15, 4, '2026-05-12', '2026-05-16', NULL, NULL, 'confirmed', 2, 0, 'Garden view preferred', 640.00, 1, 0, NULL, 3200.00, '2026-05-05 05:00:00', '2026-05-05 05:00:00'),
(42, 6, 11, 3, '2026-05-12', '2026-05-15', NULL, NULL, 'confirmed', 1, 0, 'Late checkout requested', 300.00, 1, 0, NULL, 1500.00, '2026-05-05 05:30:00', '2026-05-05 05:30:00'),
(43, 7, 16, 4, '2026-05-13', '2026-05-17', NULL, NULL, 'confirmed', 2, 0, 'Vegan breakfast, afternoon tea', 640.00, 1, 0, NULL, 3200.00, '2026-05-05 06:00:00', '2026-05-05 06:00:00'),
(44, 4, 12, 3, '2026-05-14', '2026-05-18', NULL, NULL, 'confirmed', 2, 0, 'VIP platinum - premium amenities', 400.00, 1, 0, NULL, 2000.00, '2026-05-05 06:30:00', '2026-05-05 06:30:00'),
(45, 1, 17, 4, '2026-05-15', '2026-05-19', NULL, NULL, 'confirmed', 2, 0, 'High floor, late arrival ~11pm', 640.00, 1, 0, NULL, 3200.00, '2026-05-05 07:00:00', '2026-05-05 07:00:00'),
(46, 3, 18, 3, '2026-05-16', '2026-05-20', NULL, NULL, 'confirmed', 1, 0, 'Business trip, daily newspaper', 640.00, 1, 0, NULL, 3200.00, '2026-05-05 07:30:00', '2026-05-05 07:30:00'),
(47, 8, 13, 4, '2026-05-17', '2026-05-21', NULL, NULL, 'confirmed', 2, 0, 'Airport pickup requested', 400.00, 1, 0, NULL, 2000.00, '2026-05-05 08:00:00', '2026-05-05 08:00:00'),
(48, 5, 19, 3, '2026-05-18', '2026-05-22', NULL, NULL, 'confirmed', 2, 0, 'Halal meals, prayer direction card', 640.00, 1, 0, NULL, 3200.00, '2026-05-05 08:30:00', '2026-05-05 08:30:00'),
(49, 2, 5, 4, '2026-05-19', '2026-05-23', NULL, NULL, 'confirmed', 1, 0, 'Blackout curtains, no disturbance', 640.00, 1, 0, NULL, 3200.00, '2026-05-05 09:00:00', '2026-05-05 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `restocking_requisitions`
--

CREATE TABLE `restocking_requisitions` (
  `id` int(10) UNSIGNED NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `requested_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`) VALUES
(1, 'manager', '2026-05-01 21:45:43'),
(2, 'front_desk', '2026-05-01 21:45:43'),
(3, 'housekeeper', '2026-05-01 21:45:43'),
(4, 'guest', '2026-05-01 21:45:43'),
(5, 'revenue_manager', '2026-05-01 21:45:44');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_type_id` int(10) UNSIGNED NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `floor` tinyint(3) UNSIGNED NOT NULL,
  `status` enum('available','occupied','dirty','cleaning','inspecting','out_of_order') NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_type_id`, `room_number`, `floor`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '101', 1, 'occupied', 'Ground floor, near lobby', '2026-05-01 21:45:43', '2026-05-03 00:29:41'),
(2, 1, '102', 1, 'occupied', 'Guest currently checked in', '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(3, 2, '201', 2, 'occupied', 'Checkout completed, awaiting housekeeping', '2026-05-01 21:45:43', '2026-05-02 01:07:21'),
(4, 2, '202', 2, 'dirty', 'Housekeeping in progress', '2026-05-01 21:45:43', '2026-05-02 01:07:21'),
(5, 2, '203', 2, 'inspecting', 'Cleaning done, supervisor review pending', '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(6, 3, '301', 3, 'occupied', 'Premium corner suite', '2026-05-01 21:45:43', '2026-05-02 01:07:21'),
(7, 3, '302', 3, 'out_of_order', 'AC unit under repair', '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(8, 1, '103', 1, 'occupied', 'Near elevator', '2026-05-01 21:45:43', '2026-05-02 01:07:21'),
(10, 1, '104', 1, 'occupied', 'Standard room with garden view', '2026-05-02 22:55:52', '2026-05-03 20:47:59'),
(11, 1, '105', 1, 'available', 'Standard room with garden view', '2026-05-02 22:55:52', '2026-05-02 22:55:52'),
(12, 1, '106', 1, 'available', 'Standard room with city view', '2026-05-02 22:55:52', '2026-05-02 22:55:52'),
(13, 1, '107', 1, 'available', 'Standard room with city view', '2026-05-02 22:55:52', '2026-05-02 22:55:52'),
(14, 1, '108', 1, 'cleaning', 'Standard room - housekeeping in progress', '2026-05-02 22:55:52', '2026-05-02 22:55:52'),
(15, 2, '204', 2, 'available', 'Deluxe room with balcony', '2026-05-02 22:55:52', '2026-05-03 00:52:07'),
(16, 2, '205', 2, 'available', 'Deluxe room with balcony', '2026-05-02 22:55:52', '2026-05-02 22:55:52'),
(17, 2, '206', 2, 'available', 'Deluxe room with pool view', '2026-05-02 22:55:52', '2026-05-02 22:55:52'),
(18, 2, '207', 2, 'available', 'Deluxe room with pool view', '2026-05-02 22:55:52', '2026-05-02 22:55:52'),
(19, 2, '208', 2, 'available', 'Deluxe corner room', '2026-05-02 22:55:52', '2026-05-02 22:55:52'),
(20, 3, '303', 3, 'occupied', 'Junior Suite with lounge area', '2026-05-02 22:55:52', '2026-05-03 00:52:07'),
(21, 3, '304', 3, 'available', 'Junior Suite with lounge area', '2026-05-02 22:55:52', '2026-05-02 22:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `capacity` tinyint(3) UNSIGNED NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `description`, `base_price`, `capacity`) VALUES
(1, 'Standard', 'Comfortable room with essential amenities for a pleasant stay.', 500.00, 2),
(2, 'Deluxe', 'Spacious room with premium amenities and city view.', 800.00, 3),
(3, 'Suite', 'Luxury suite with separate living area, premium furnishings and minibar.', 1500.00, 4);

-- --------------------------------------------------------

--
-- Table structure for table `security_alerts`
--

CREATE TABLE `security_alerts` (
  `id` int(10) UNSIGNED NOT NULL,
  `alert_type` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `related_found_item_id` int(10) UNSIGNED DEFAULT NULL,
  `priority` enum('normal','urgent') NOT NULL DEFAULT 'normal',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_bookings`
--

CREATE TABLE `service_bookings` (
  `id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `service_id` int(10) UNSIGNED NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_bookings`
--

INSERT INTO `service_bookings` (`id`, `guest_id`, `service_id`, `booking_date`, `booking_time`, `status`, `created_at`) VALUES
(1, 1, 1, '2026-04-28', '10:00:00', 'confirmed', '2026-05-01 21:45:43'),
(2, 1, 3, '2026-04-29', '12:00:00', 'confirmed', '2026-05-01 21:45:43'),
(3, 4, 1, '2026-05-03', '11:00:00', 'confirmed', '2026-05-01 21:45:43'),
(4, 4, 2, '2026-05-02', '19:30:00', 'confirmed', '2026-05-01 21:45:43'),
(5, 5, 3, '2026-05-10', '08:00:00', 'pending', '2026-05-01 21:45:43'),
(6, 2, 4, '2026-04-23', '14:00:00', 'confirmed', '2026-05-01 21:45:43'),
(7, 3, 5, '2026-04-29', '09:00:00', 'pending', '2026-05-01 21:45:43'),
(8, 6, 2, '2026-04-25', '20:00:00', 'confirmed', '2026-05-01 21:45:43'),
(9, 1, 1, '2026-04-28', '10:00:00', 'confirmed', '2026-05-01 21:53:46'),
(10, 1, 3, '2026-04-29', '12:00:00', 'confirmed', '2026-05-01 21:53:46'),
(11, 4, 1, '2026-05-03', '11:00:00', 'confirmed', '2026-05-01 21:53:46'),
(12, 4, 2, '2026-05-02', '19:30:00', 'confirmed', '2026-05-01 21:53:46'),
(13, 5, 3, '2026-05-10', '08:00:00', 'pending', '2026-05-01 21:53:46'),
(14, 2, 4, '2026-04-23', '14:00:00', 'confirmed', '2026-05-01 21:53:46'),
(15, 3, 5, '2026-04-29', '09:00:00', 'pending', '2026-05-01 21:53:46'),
(16, 6, 2, '2026-04-25', '20:00:00', 'confirmed', '2026-05-01 21:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `supply_inventory`
--

CREATE TABLE `supply_inventory` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `location` enum('housekeeping_store','floor1','floor2','floor3','laundry','kitchen','general') NOT NULL DEFAULT 'general',
  `current_stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supply_inventory`
--

INSERT INTO `supply_inventory` (`id`, `item_id`, `location`, `current_stock`, `last_updated`) VALUES
(1, 1, 'housekeeping_store', 45, '2026-05-02 00:45:45'),
(2, 2, 'housekeeping_store', 60, '2026-05-02 00:45:45'),
(3, 3, 'housekeeping_store', 18, '2026-05-02 00:45:45'),
(4, 4, 'housekeeping_store', 22, '2026-05-02 00:45:45'),
(5, 5, 'housekeeping_store', 80, '2026-05-02 00:45:45'),
(6, 6, 'housekeeping_store', 75, '2026-05-02 00:45:45'),
(7, 7, 'housekeeping_store', 120, '2026-05-02 00:45:45'),
(8, 8, 'housekeeping_store', 95, '2026-05-02 00:45:45'),
(9, 9, 'housekeeping_store', 12, '2026-05-02 00:45:45'),
(10, 10, 'housekeeping_store', 3, '2026-05-02 00:45:45');

-- --------------------------------------------------------

--
-- Table structure for table `supply_items`
--

CREATE TABLE `supply_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` enum('cleaning','linen','amenity','minibar','other') NOT NULL DEFAULT 'other',
  `min_threshold` int(10) UNSIGNED NOT NULL DEFAULT 5,
  `unit` varchar(30) NOT NULL DEFAULT 'units',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supply_items`
--

INSERT INTO `supply_items` (`id`, `name`, `category`, `min_threshold`, `unit`, `is_active`) VALUES
(1, 'Bath Towels', 'linen', 20, 'pieces', 1),
(2, 'Hand Towels', 'linen', 30, 'pieces', 1),
(3, 'Bed Sheets (King)', 'linen', 10, 'sets', 1),
(4, 'Bed Sheets (Queen)', 'linen', 15, 'sets', 1),
(5, 'Shampoo Bottles', 'amenity', 50, 'bottles', 1),
(6, 'Conditioner Bottles', 'amenity', 50, 'bottles', 1),
(7, 'Soap Bars', 'amenity', 100, 'bars', 1),
(8, 'Toilet Rolls', 'amenity', 100, 'rolls', 1),
(9, 'All-Purpose Cleaner', 'cleaning', 10, 'litres', 1),
(10, 'Disinfectant Spray', 'cleaning', 15, 'cans', 1);

-- --------------------------------------------------------

--
-- Table structure for table `temp_sql`
--

CREATE TABLE `temp_sql` (
  `line` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temp_sql`
--

INSERT INTO `temp_sql` (`line`) VALUES
('-- ============================================================'),
('--  20-DAY FORWARD COVERAGE  (2026-05-05 → 2026-05-24)'),
('--  Rooms used: 10,11,12,13,15,16,17,18,19,5 (all available)'),
('--  Guests: 1-8 (read-only references)'),
('-- ============================================================'),
(''),
('-- ── RESERVATIONS ─────────────────────────────────────────'),
('INSERT IGNORE INTO `reservations`'),
(' (id,guest_id,room_id,assigned_by,check_in_date,check_out_date,'),
('  actual_check_in,actual_check_out,status,adults,children,'),
('  special_requests,deposit_amount,deposit_paid,is_group,group_id,'),
('  total_price,created_at,updated_at)'),
('VALUES'),
('(30,3,10,3,\'2026-05-05\',\'2026-05-08\',\'2026-05-05 14:00:00\',NULL,\'checked_in\',1,0,\'Vegetarian meals, quiet room\',300.00,1,0,NULL,1500.00,\'2026-05-03 10:00:00\',\'2026-05-05 14:00:00\'),'),
('(31,5,11,4,\'2026-05-05\',\'2026-05-09\',\'2026-05-05 15:30:00\',NULL,\'checked_in\',2,0,\'Halal dining, prayer mat\',400.00,1,0,NULL,2000.00,\'2026-05-03 11:00:00\',\'2026-05-05 15:30:00\'),'),
('(32,8,15,3,\'2026-05-06\',\'2026-05-10\',NULL,NULL,\'confirmed\',1,0,\'Business stay, early check-in\',640.00,1,0,NULL,3200.00,\'2026-05-04 09:00:00\',\'2026-05-04 09:00:00\'),'),
('(33,2,12,4,\'2026-05-06\',\'2026-05-09\',NULL,NULL,\'confirmed\',2,0,\'Anniversary dinner arrangement\',300.00,1,0,NULL,1500.00,\'2026-05-04 10:00:00\',\'2026-05-04 10:00:00\'),'),
('(34,6,16,3,\'2026-05-07\',\'2026-05-11\',NULL,NULL,\'confirmed\',2,0,\'Champagne and flowers on arrival\',640.00,1,0,NULL,3200.00,\'2026-05-04 11:00:00\',\'2026-05-04 11:00:00\'),'),
('(35,7,17,4,\'2026-05-07\',\'2026-05-10\',NULL,NULL,\'confirmed\',1,0,\'Vegan breakfast, yoga mat\',480.00,1,0,NULL,2400.00,\'2026-05-04 12:00:00\',\'2026-05-04 12:00:00\'),'),
('(36,4,5, 3,\'2026-05-08\',\'2026-05-12\',NULL,NULL,\'confirmed\',2,0,\'VIP platinum - fruit basket, champagne\',640.00,1,0,NULL,3200.00,\'2026-05-04 13:00:00\',\'2026-05-04 13:00:00\'),'),
('(37,1,13,4,\'2026-05-08\',\'2026-05-11\',NULL,NULL,\'confirmed\',2,0,\'High floor, extra pillows\',300.00,1,0,NULL,1500.00,\'2026-05-04 14:00:00\',\'2026-05-04 14:00:00\'),'),
('(38,3,18,3,\'2026-05-09\',\'2026-05-13\',NULL,NULL,\'confirmed\',1,0,\'Quiet room, no early housekeeping\',640.00,1,0,NULL,3200.00,\'2026-05-04 15:00:00\',\'2026-05-04 15:00:00\'),'),
('(39,8,19,4,\'2026-05-10\',\'2026-05-13\',NULL,NULL,\'confirmed\',2,1,\'Extra bed for child, baby cot\',480.00,1,0,NULL,2400.00,\'2026-05-04 16:00:00\',\'2026-05-04 16:00:00\'),'),
('(40,5,10,3,\'2026-05-11\',\'2026-05-15\',NULL,NULL,\'confirmed\',1,0,\'Halal meals throughout stay\',400.00,1,0,NULL,2000.00,\'2026-05-04 17:00:00\',\'2026-05-04 17:00:00\'),'),
('(41,2,15,4,\'2026-05-12\',\'2026-05-16\',NULL,NULL,\'confirmed\',2,0,\'Garden view preferred\',640.00,1,0,NULL,3200.00,\'2026-05-05 08:00:00\',\'2026-05-05 08:00:00\'),'),
('(42,6,11,3,\'2026-05-12\',\'2026-05-15\',NULL,NULL,\'confirmed\',1,0,\'Late checkout requested\',300.00,1,0,NULL,1500.00,\'2026-05-05 08:30:00\',\'2026-05-05 08:30:00\'),'),
('(43,7,16,4,\'2026-05-13\',\'2026-05-17\',NULL,NULL,\'confirmed\',2,0,\'Vegan breakfast, afternoon tea\',640.00,1,0,NULL,3200.00,\'2026-05-05 09:00:00\',\'2026-05-05 09:00:00\'),'),
('(44,4,12,3,\'2026-05-14\',\'2026-05-18\',NULL,NULL,\'confirmed\',2,0,\'VIP platinum - premium amenities\',400.00,1,0,NULL,2000.00,\'2026-05-05 09:30:00\',\'2026-05-05 09:30:00\'),'),
('(45,1,17,4,\'2026-05-15\',\'2026-05-19\',NULL,NULL,\'confirmed\',2,0,\'High floor, late arrival ~11pm\',640.00,1,0,NULL,3200.00,\'2026-05-05 10:00:00\',\'2026-05-05 10:00:00\'),'),
('(46,3,18,3,\'2026-05-16\',\'2026-05-20\',NULL,NULL,\'confirmed\',1,0,\'Business trip, daily newspaper\',640.00,1,0,NULL,3200.00,\'2026-05-05 10:30:00\',\'2026-05-05 10:30:00\'),'),
('(47,8,13,4,\'2026-05-17\',\'2026-05-21\',NULL,NULL,\'confirmed\',2,0,\'Airport pickup requested\',400.00,1,0,NULL,2000.00,\'2026-05-05 11:00:00\',\'2026-05-05 11:00:00\'),'),
('(48,5,19,3,\'2026-05-18\',\'2026-05-22\',NULL,NULL,\'confirmed\',2,0,\'Halal meals, prayer direction card\',640.00,1,0,NULL,3200.00,\'2026-05-05 11:30:00\',\'2026-05-05 11:30:00\'),'),
('(49,2,5, 4,\'2026-05-19\',\'2026-05-23\',NULL,NULL,\'confirmed\',1,0,\'Blackout curtains, no disturbance\',640.00,1,0,NULL,3200.00,\'2026-05-05 12:00:00\',\'2026-05-05 12:00:00\');'),
(''),
('-- ── FOLIOS for reservations 30-49 ────────────────────────'),
('INSERT IGNORE INTO `folios`'),
(' (id,reservation_id,total_charges,tax_amount,discount_amount,amount_paid,balance_due,status,notes,created_at,updated_at)'),
('VALUES'),
('(20,30,1500.00,150.00,0.00, 300.00,1200.00,\'open\',NULL,\'2026-05-05 14:00:00\',\'2026-05-05 14:00:00\'),'),
('(21,31,2000.00,200.00,0.00, 400.00,1600.00,\'open\',NULL,\'2026-05-05 15:30:00\',\'2026-05-05 15:30:00\'),'),
('(22,32,3200.00,320.00,0.00, 640.00,2560.00,\'open\',NULL,\'2026-05-04 09:00:00\',\'2026-05-04 09:00:00\'),'),
('(23,33,1500.00,150.00,0.00, 300.00,1200.00,\'open\',NULL,\'2026-05-04 10:00:00\',\'2026-05-04 10:00:00\'),'),
('(24,34,3200.00,320.00,0.00, 640.00,2560.00,\'open\',NULL,\'2026-05-04 11:00:00\',\'2026-05-04 11:00:00\'),'),
('(25,35,2400.00,240.00,0.00, 480.00,1920.00,\'open\',NULL,\'2026-05-04 12:00:00\',\'2026-05-04 12:00:00\'),'),
('(26,36,3200.00,320.00,0.00, 640.00,2560.00,\'open\',NULL,\'2026-05-04 13:00:00\',\'2026-05-04 13:00:00\'),'),
('(27,37,1500.00,150.00,0.00, 300.00,1200.00,\'open\',NULL,\'2026-05-04 14:00:00\',\'2026-05-04 14:00:00\'),'),
('(28,38,3200.00,320.00,0.00, 640.00,2560.00,\'open\',NULL,\'2026-05-04 15:00:00\',\'2026-05-04 15:00:00\'),'),
('(29,39,2400.00,240.00,0.00, 480.00,1920.00,\'open\',NULL,\'2026-05-04 16:00:00\',\'2026-05-04 16:00:00\'),'),
('(30,40,2000.00,200.00,0.00, 400.00,1600.00,\'open\',NULL,\'2026-05-04 17:00:00\',\'2026-05-04 17:00:00\'),'),
('(31,41,3200.00,320.00,0.00, 640.00,2560.00,\'open\',NULL,\'2026-05-05 08:00:00\',\'2026-05-05 08:00:00\'),'),
('(32,42,1500.00,150.00,0.00, 300.00,1200.00,\'open\',NULL,\'2026-05-05 08:30:00\',\'2026-05-05 08:30:00\'),'),
('(33,43,3200.00,320.00,0.00, 640.00,2560.00,\'open\',NULL,\'2026-05-05 09:00:00\',\'2026-05-05 09:00:00\'),'),
('(34,44,2000.00,200.00,0.00, 400.00,1600.00,\'open\',NULL,\'2026-05-05 09:30:00\',\'2026-05-05 09:30:00\'),'),
('(35,45,3200.00,320.00,0.00, 640.00,2560.00,\'open\',NULL,\'2026-05-05 10:00:00\',\'2026-05-05 10:00:00\'),'),
('(36,46,3200.00,320.00,0.00, 640.00,2560.00,\'open\',NULL,\'2026-05-05 10:30:00\',\'2026-05-05 10:30:00\'),'),
('(37,47,2000.00,200.00,0.00, 400.00,1600.00,\'open\',NULL,\'2026-05-05 11:00:00\',\'2026-05-05 11:00:00\'),'),
('(38,48,3200.00,320.00,0.00, 640.00,2560.00,\'open\',NULL,\'2026-05-05 11:30:00\',\'2026-05-05 11:30:00\'),'),
('(39,49,3200.00,320.00,0.00, 640.00,2560.00,\'open\',NULL,\'2026-05-05 12:00:00\',\'2026-05-05 12:00:00\');'),
(''),
('-- ── PAYMENTS — one deposit per check-in day ───────────────'),
('-- Each row: (folio_id, amount, method, reference, processed_by, processed_at)'),
('INSERT IGNORE INTO `payments`'),
(' (id,folio_id,amount,method,reference,processed_by,processed_at)'),
('VALUES'),
('-- May 5: res 30+31 deposits'),
('(70,20, 300.00,\'credit_card\',\'DEP-30-0505\',3,\'2026-05-05 14:05:00\'),'),
('(71,21, 400.00,\'credit_card\',\'DEP-31-0505\',4,\'2026-05-05 15:35:00\'),'),
('-- May 6: res 32+33 deposits'),
('(72,22, 640.00,\'credit_card\',\'DEP-32-0506\',3,\'2026-05-06 13:00:00\'),'),
('(73,23, 300.00,\'cash\',       \'DEP-33-0506\',4,\'2026-05-06 14:30:00\'),'),
('-- May 7: res 34+35 deposits'),
('(74,24, 640.00,\'credit_card\',\'DEP-34-0507\',3,\'2026-05-07 13:00:00\'),'),
('(75,25, 480.00,\'debit_card\', \'DEP-35-0507\',4,\'2026-05-07 14:00:00\'),'),
('-- May 8: res 36+37 deposits'),
('(76,26, 640.00,\'credit_card\',\'DEP-36-0508\',3,\'2026-05-08 13:30:00\'),'),
('(77,27, 300.00,\'cash\',       \'DEP-37-0508\',4,\'2026-05-08 14:00:00\'),'),
('-- May 9: res 38 deposit'),
('(78,28, 640.00,\'credit_card\',\'DEP-38-0509\',3,\'2026-05-09 13:00:00\'),'),
('-- May 10: res 39 deposit'),
('(79,29, 480.00,\'bank_transfer\',\'DEP-39-0510\',4,\'2026-05-10 12:00:00\'),'),
('-- May 11: res 40 deposit + res 30 settlement (checkout)'),
('(80,30, 400.00,\'credit_card\',\'DEP-40-0511\',3,\'2026-05-11 13:00:00\'),'),
('(81,20,1200.00,\'credit_card\',\'SET-30-0511\',3,\'2026-05-08 11:30:00\'),'),
('-- May 12: res 41+42 deposits + res 31 settlement'),
('(82,31, 640.00,\'credit_card\',\'DEP-41-0512\',4,\'2026-05-12 13:00:00\'),'),
('(83,32, 300.00,\'cash\',       \'DEP-42-0512\',3,\'2026-05-12 13:30:00\'),'),
('(84,21,1600.00,\'credit_card\',\'SET-31-0512\',4,\'2026-05-09 11:00:00\'),'),
('-- May 13: res 43 deposit + res 32+39 settlements'),
('(85,33, 640.00,\'debit_card\', \'DEP-43-0513\',4,\'2026-05-13 13:00:00\'),'),
('(86,22,2560.00,\'credit_card\',\'SET-32-0513\',3,\'2026-05-10 11:30:00\'),'),
('(87,29,1920.00,\'credit_card\',\'SET-39-0513\',4,\'2026-05-13 11:00:00\'),'),
('-- May 14: res 44 deposit + res 33+35+37 settlements'),
('(88,34, 400.00,\'credit_card\',\'DEP-44-0514\',3,\'2026-05-14 13:00:00\'),'),
('(89,23,1200.00,\'cash\',       \'SET-33-0514\',4,\'2026-05-09 11:30:00\'),'),
('(90,25,1920.00,\'credit_card\',\'SET-35-0514\',3,\'2026-05-10 11:00:00\'),'),
('(91,27,1200.00,\'credit_card\',\'SET-37-0514\',4,\'2026-05-11 11:30:00\'),'),
('-- May 15: res 45 deposit + res 34+40 settlements'),
('(92,35, 640.00,\'credit_card\',\'DEP-45-0515\',3,\'2026-05-15 13:00:00\'),'),
('(93,24,2560.00,\'credit_card\',\'SET-34-0515\',4,\'2026-05-11 12:00:00\'),'),
('(94,30,1600.00,\'bank_transfer\',\'SET-40-0515\',3,\'2026-05-15 11:00:00\'),'),
('-- May 16: res 46 deposit + res 34 balance + res 42 settlement'),
('(95,36, 640.00,\'credit_card\',\'DEP-46-0516\',4,\'2026-05-16 13:00:00\'),'),
('(96,32,1200.00,\'credit_card\',\'SET-42-0516\',3,\'2026-05-15 11:30:00\'),'),
('-- May 17: res 47 deposit + res 36+38+43 partial'),
('(97,37, 400.00,\'credit_card\',\'DEP-47-0517\',4,\'2026-05-17 13:00:00\'),'),
('(98,26,2560.00,\'credit_card\',\'SET-36-0517\',3,\'2026-05-12 11:00:00\'),'),
('(99,28,2560.00,\'credit_card\',\'SET-38-0517\',4,\'2026-05-13 12:00:00\'),'),
('-- May 18: res 48 deposit'),
('(100,38, 640.00,\'credit_card\',\'DEP-48-0518\',3,\'2026-05-18 13:00:00\'),'),
('-- May 19: res 49 deposit + res 40+41+44 partial'),
('(101,39, 640.00,\'debit_card\', \'DEP-49-0519\',4,\'2026-05-19 13:00:00\'),'),
('(102,33,2560.00,\'credit_card\',\'SET-43-0519\',3,\'2026-05-17 11:00:00\'),'),
('-- May 20: res 46 settlement'),
('(103,36,2560.00,\'credit_card\',\'SET-46-0520\',4,\'2026-05-20 11:00:00\'),'),
('-- May 21: res 47 settlement'),
('(104,37,1600.00,\'cash\',       \'SET-47-0521\',3,\'2026-05-21 11:00:00\'),'),
('-- May 22: res 41+48 settlements'),
('(105,31,2560.00,\'credit_card\',\'SET-41-0522\',4,\'2026-05-16 11:30:00\'),'),
('(106,38,2560.00,\'credit_card\',\'SET-48-0522\',3,\'2026-05-22 11:00:00\'),'),
('-- May 23: res 49 settlement'),
('(107,39,2560.00,\'credit_card\',\'SET-49-0523\',4,\'2026-05-23 11:00:00\'),'),
('-- May 24: res 45 settlement'),
('(108,35,2560.00,\'bank_transfer\',\'SET-45-0524\',3,\'2026-05-19 11:30:00\');'),
(''),
('-- ── WORK ORDERS (fills empty work_orders table) ───────────'),
('INSERT IGNORE INTO `work_orders`'),
(' (id,type,room_id,asset_id,description,priority,status,'),
('  assigned_to_user_id,created_by_user_id,work_performed,'),
('  parts_used,time_spent_minutes,supervisor_id,created_at,completed_at,closed_at)'),
('VALUES'),
('(1,\'emergency\',7,NULL,\'AC unit grinding noise and no cooling — Room 302 taken OOO\',\'high\',\'in_progress\',3,2,NULL,NULL,NULL,1,\'2026-05-03 09:00:00\',NULL,NULL),'),
('(2,\'preventative\',NULL,1,\'Quarterly HVAC filter replacement — all floors\',\'normal\',\'completed\',3,1,\'Replaced filters on floors 1-3. All units operational.\',NULL,180,1,\'2026-05-01 08:00:00\',\'2026-05-01 11:00:00\',NULL),'),
('(3,\'emergency\',4,NULL,\'Bathroom tap dripping — Room 202. Needs washer replacement\',\'low\',\'open\',3,2,NULL,NULL,NULL,NULL,\'2026-05-04 10:00:00\',NULL,NULL),'),
('(4,\'preventative\',NULL,8,\'Monthly elevator inspection and lubrication — Elevator B\',\'normal\',\'in_progress\',3,1,NULL,NULL,NULL,1,\'2026-05-03 07:00:00\',NULL,NULL),'),
('(5,\'emergency\',NULL,8,\'Elevator B door sensor malfunction — stuck open\',\'high\',\'open\',3,2,NULL,NULL,NULL,1,\'2026-05-04 14:00:00\',NULL,NULL),'),
('(6,\'preventative\',5,NULL,\'Deep clean and carpet steam — Room 203 post inspection\',\'normal\',\'completed\',5,6,\'Full deep clean completed. Carpet steamed. Room cleared for occupancy.\',NULL,120,1,\'2026-05-04 08:00:00\',\'2026-05-04 10:00:00\',NULL),'),
('(7,\'emergency\',NULL,5,\'Pool pump pressure warning — filtration system\',\'normal\',\'completed\',3,2,\'Pressure relief valve adjusted. System stable.\',NULL,45,1,\'2026-05-03 06:00:00\',\'2026-05-03 06:45:00\',NULL),'),
('(8,\'preventative\',NULL,7,\'Boiler pressure check and descaling — quarterly\',\'normal\',\'pending_parts\',3,1,NULL,\'[]\',NULL,1,\'2026-05-05 08:00:00\',NULL,NULL),'),
('(9,\'emergency\',3,NULL,\'Bathroom ceiling light flickering — Room 201\',\'low\',\'open\',3,2,NULL,NULL,NULL,NULL,\'2026-05-05 09:30:00\',NULL,NULL),'),
('(10,\'preventative\',NULL,NULL,\'Annual fire suppression system inspection — all zones\',\'high\',\'open\',3,1,NULL,NULL,NULL,1,\'2026-05-05 10:00:00\',NULL,NULL);'),
(''),
('-- ── HOUSEKEEPING TASKS for next 10 days ──────────────────'),
('INSERT IGNORE INTO `housekeeping_tasks`'),
(' (room_id,assigned_to,task_type,status,notes,quality_score,created_at,updated_at,completed_at)'),
('VALUES'),
('-- May 5'),
('(10,5,\'cleaning\',\'done\',\'Check-in clean for res 30. Vegetarian welcome plate placed.\',88,\'2026-05-05 07:00:00\',\'2026-05-05 09:30:00\',\'2026-05-05 09:30:00\'),'),
('(11,6,\'cleaning\',\'done\',\'Check-in clean for res 31. Prayer mat positioned.\',91,\'2026-05-05 07:30:00\',\'2026-05-05 09:45:00\',\'2026-05-05 09:45:00\'),'),
('-- May 6'),
('(10,5,\'turndown\',\'done\',\'Evening turndown res 30.\',85,\'2026-05-06 17:00:00\',\'2026-05-06 18:15:00\',\'2026-05-06 18:15:00\'),'),
('(15,6,\'cleaning\',\'done\',\'Pre-arrival clean for res 32. Business setup arranged.\',90,\'2026-05-06 10:00:00\',\'2026-05-06 12:00:00\',\'2026-05-06 12:00:00\'),'),
('(12,7,\'cleaning\',\'done\',\'Pre-arrival clean for res 33. Anniversary rose petals placed.\',92,\'2026-05-06 10:30:00\',\'2026-05-06 12:30:00\',\'2026-05-06 12:30:00\'),'),
('-- May 7'),
('(16,5,\'cleaning\',\'done\',\'Pre-arrival clean for res 34. Champagne chilled.\',89,\'2026-05-07 10:00:00\',\'2026-05-07 12:00:00\',\'2026-05-07 12:00:00\'),'),
('(17,6,\'cleaning\',\'done\',\'Pre-arrival clean for res 35. Yoga mat placed.\',87,\'2026-05-07 10:30:00\',\'2026-05-07 12:30:00\',\'2026-05-07 12:30:00\'),'),
('-- May 8: res 30 checkout + new check-ins'),
('(10,7,\'deep_clean\',\'done\',\'Post-checkout deep clean res 30 departure.\',84,\'2026-05-08 12:00:00\',\'2026-05-08 14:30:00\',\'2026-05-08 14:30:00\'),'),
('(5, 5,\'cleaning\',\'done\',\'VIP prep res 36. Fruit basket + champagne + platinum amenities.\',95,\'2026-05-08 10:00:00\',\'2026-05-08 12:30:00\',\'2026-05-08 12:30:00\'),'),
('(13,6,\'cleaning\',\'done\',\'Pre-arrival clean res 37. Extra pillows set.\',88,\'2026-05-08 10:30:00\',\'2026-05-08 12:00:00\',\'2026-05-08 12:00:00\'),'),
('-- May 9'),
('(18,7,\'cleaning\',\'done\',\'Pre-arrival clean res 38. Quiet setup, DND sign placed.\',86,\'2026-05-09 10:00:00\',\'2026-05-09 12:00:00\',\'2026-05-09 12:00:00\'),'),
('-- May 10'),
('(12,5,\'turndown\',\'done\',\'Turndown res 33.\',83,\'2026-05-10 17:00:00\',\'2026-05-10 18:00:00\',\'2026-05-10 18:00:00\'),'),
('(19,6,\'cleaning\',\'in_progress\',\'Pre-arrival clean res 39. Baby cot being set up.\',NULL,\'2026-05-10 10:00:00\',\'2026-05-10 10:00:00\',NULL),'),
('-- May 11 onwards — pending'),
('(10,7,\'cleaning\',\'pending\',\'Post-checkout clean res 30, then turnover for res 40.\',NULL,\'2026-05-11 07:00:00\',\'2026-05-11 07:00:00\',NULL),'),
('(15,5,\'turndown\',\'pending\',\'Turndown res 32.\',NULL,\'2026-05-11 17:00:00\',\'2026-05-11 17:00:00\',NULL),'),
('(15,6,\'cleaning\',\'pending\',\'Post-checkout clean res 32 → turnover for res 41.\',NULL,\'2026-05-12 07:00:00\',\'2026-05-12 07:00:00\',NULL),'),
('(11,7,\'cleaning\',\'pending\',\'Post-checkout clean res 31 → turnover for res 42.\',NULL,\'2026-05-12 07:30:00\',\'2026-05-12 07:30:00\',NULL),'),
('(16,5,\'cleaning\',\'pending\',\'Post-checkout clean res 34 → turnover for res 43.\',NULL,\'2026-05-13 07:00:00\',\'2026-05-13 07:00:00\',NULL),'),
('(12,6,\'cleaning\',\'pending\',\'Post-checkout clean res 33 → turnover for res 44.\',NULL,\'2026-05-14 07:00:00\',\'2026-05-14 07:00:00\',NULL),'),
('(17,7,\'cleaning\',\'pending\',\'Post-checkout clean res 35 → turnover for res 45.\',NULL,\'2026-05-15 07:00:00\',\'2026-05-15 07:00:00\',NULL),'),
('(18,5,\'cleaning\',\'pending\',\'Post-checkout clean res 38 → turnover for res 46.\',NULL,\'2026-05-16 07:00:00\',\'2026-05-16 07:00:00\',NULL),'),
('(13,6,\'cleaning\',\'pending\',\'Post-checkout clean res 37 → turnover for res 47.\',NULL,\'2026-05-17 07:00:00\',\'2026-05-17 07:00:00\',NULL),'),
('(19,7,\'cleaning\',\'pending\',\'Post-checkout clean res 39 → turnover for res 48.\',NULL,\'2026-05-18 07:00:00\',\'2026-05-18 07:00:00\',NULL),'),
('(5, 5,\'cleaning\',\'pending\',\'Post-checkout clean res 36 → turnover for res 49.\',NULL,\'2026-05-19 07:00:00\',\'2026-05-19 07:00:00\',NULL);');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('charge','refund','preauth') NOT NULL DEFAULT 'charge',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `gateway_ref` varchar(100) DEFAULT NULL,
  `status` enum('success','failed','pending') NOT NULL DEFAULT 'pending',
  `idempotency_key` varchar(255) NOT NULL,
  `failure_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `password`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ahmed Hassan', 'ahmed.hassan@grandhotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2021-03-15 07:00:00', '2026-05-02 00:18:35'),
(2, 1, 'Sara Mohamed', 'sara.mohamed@grandhotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2022-07-01 07:30:00', '2026-05-02 00:18:35'),
(3, 2, 'Omar Ali', 'omar.ali@grandhotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2022-11-20 06:45:00', '2026-05-02 00:18:35'),
(4, 2, 'Nour Ibrahim', 'nour.ibrahim@grandhotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2023-04-10 07:15:00', '2026-05-02 00:18:35'),
(5, 3, 'Fatma Khaled', 'fatma.khaled@grandhotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2023-01-08 09:00:00', '2026-05-02 00:18:35'),
(6, 3, 'Mohamed Samir', 'mohamed.samir@grandhotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2023-06-25 05:30:00', '2026-05-02 00:18:35'),
(7, 3, 'Layla Ahmed', 'layla.ahmed@grandhotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2024-02-14 07:45:00', '2026-05-02 00:18:35'),
(8, 4, 'John Smith', 'john.smith@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(9, 4, 'Emma Wilson', 'emma.wilson@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(10, 4, 'Carlos Rodriguez', 'carlos.rodriguez@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(11, 4, 'Yuki Tanaka', 'yuki.tanaka@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(12, 4, 'Aisha Al-Rashid', 'aisha.alrashid@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(13, 4, 'Pierre Dubois', 'pierre.dubois@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(14, 4, 'Priya Sharma', 'priya.sharma@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(15, 4, 'David Chen', 'david.chen@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-05-01 21:45:43', '2026-05-01 21:45:43'),
(17, 5, 'Karim Nasser', 'karim.nasser@grandhotel.com', '3fe20d68a85f0ca590301eb12d95603bc1bc3bc42907d22503fe06bc03000782', 1, '2023-09-03 07:00:00', '2026-05-02 00:18:35'),
(18, 5, 'Lina Youssef', 'lina.youssef@grandhotel.com', '3fe20d68a85f0ca590301eb12d95603bc1bc3bc42907d22503fe06bc03000782', 1, '2024-05-20 06:30:00', '2026-05-02 00:18:35');

-- --------------------------------------------------------

--
-- Table structure for table `virtual_inventory`
--

CREATE TABLE `virtual_inventory` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_type_id` int(10) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `physical_rooms` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `virtual_max` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `confirmed_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by_user_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `virtual_inventory`
--

INSERT INTO `virtual_inventory` (`id`, `room_type_id`, `date`, `physical_rooms`, `virtual_max`, `confirmed_count`, `updated_at`, `updated_by_user_id`) VALUES
(1, 1, '2026-05-02', 3, 3, 1, '2026-05-01 21:45:44', 1),
(2, 1, '2026-05-03', 3, 3, 2, '2026-05-01 21:45:44', 1),
(3, 1, '2026-05-04', 3, 2, 2, '2026-05-01 21:45:44', 1),
(4, 2, '2026-05-02', 3, 3, 0, '2026-05-01 21:45:44', 1),
(5, 2, '2026-05-03', 3, 3, 1, '2026-05-01 21:45:44', 1),
(6, 3, '2026-05-02', 2, 2, 1, '2026-05-01 21:45:44', 1),
(7, 3, '2026-05-03', 2, 2, 2, '2026-05-01 21:45:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `work_orders`
--

CREATE TABLE `work_orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('emergency','preventative') NOT NULL,
  `room_id` int(10) UNSIGNED DEFAULT NULL,
  `asset_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `priority` enum('low','normal','high','emergency') NOT NULL DEFAULT 'normal',
  `status` enum('open','in_progress','pending_parts','completed','closed','rejected') NOT NULL DEFAULT 'open',
  `assigned_to_user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `work_performed` text DEFAULT NULL,
  `parts_used` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parts_used`)),
  `time_spent_minutes` int(10) UNSIGNED DEFAULT NULL,
  `supervisor_id` int(10) UNSIGNED DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_orders`
--

INSERT INTO `work_orders` (`id`, `type`, `room_id`, `asset_id`, `description`, `priority`, `status`, `assigned_to_user_id`, `created_by_user_id`, `work_performed`, `parts_used`, `time_spent_minutes`, `supervisor_id`, `rejection_reason`, `created_at`, `completed_at`, `closed_at`) VALUES
(1, 'emergency', 7, NULL, 'AC unit grinding noise and no cooling ÔÇö Room 302 taken OOO', 'high', 'in_progress', 3, 2, NULL, NULL, NULL, 1, NULL, '2026-05-03 09:00:00', NULL, NULL),
(2, 'preventative', NULL, 1, 'Quarterly HVAC filter replacement ÔÇö all floors', 'normal', 'completed', 3, 1, 'Replaced filters on floors 1-3. All units operational.', NULL, 180, 1, NULL, '2026-05-01 08:00:00', '2026-05-01 11:00:00', NULL),
(3, 'emergency', 4, NULL, 'Bathroom tap dripping ÔÇö Room 202. Needs washer replacement', 'low', 'open', 3, 2, NULL, NULL, NULL, NULL, NULL, '2026-05-04 10:00:00', NULL, NULL),
(4, 'preventative', NULL, 8, 'Monthly elevator inspection and lubrication ÔÇö Elevator B', 'normal', 'in_progress', 3, 1, NULL, NULL, NULL, 1, NULL, '2026-05-03 07:00:00', NULL, NULL),
(5, 'emergency', NULL, 8, 'Elevator B door sensor malfunction ÔÇö stuck open', 'high', 'open', 3, 2, NULL, NULL, NULL, 1, NULL, '2026-05-04 14:00:00', NULL, NULL),
(6, 'preventative', 5, NULL, 'Deep clean and carpet steam ÔÇö Room 203 post inspection', 'normal', 'completed', 5, 6, 'Full deep clean completed. Carpet steamed. Room cleared for occupancy.', NULL, 120, 1, NULL, '2026-05-04 08:00:00', '2026-05-04 10:00:00', NULL),
(7, 'emergency', NULL, 5, 'Pool pump pressure warning ÔÇö filtration system', 'normal', 'completed', 3, 2, 'Pressure relief valve adjusted. System stable.', NULL, 45, 1, NULL, '2026-05-03 06:00:00', '2026-05-03 06:45:00', NULL),
(8, 'preventative', NULL, 7, 'Boiler pressure check and descaling ÔÇö quarterly', 'normal', 'pending_parts', 3, 1, NULL, '[]', NULL, 1, NULL, '2026-05-05 08:00:00', NULL, NULL),
(9, 'emergency', 3, NULL, 'Bathroom ceiling light flickering ÔÇö Room 201', 'low', 'open', 3, 2, NULL, NULL, NULL, NULL, NULL, '2026-05-05 09:30:00', NULL, NULL),
(10, 'preventative', NULL, NULL, 'Annual fire suppression system inspection ÔÇö all zones', 'high', 'open', 3, 1, NULL, NULL, NULL, 1, NULL, '2026-05-05 10:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `work_order_logs`
--

CREATE TABLE `work_order_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `work_order_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL,
  `performed_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`);

--
-- Indexes for table `billing_adjustments`
--
ALTER TABLE `billing_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ba_reservation` (`reservation_id`),
  ADD KEY `fk_ba_user` (`applied_by_user_id`);

--
-- Indexes for table `billing_disputes`
--
ALTER TABLE `billing_disputes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bd_group` (`group_id`),
  ADD KEY `fk_bd_res` (`reservation_id`),
  ADD KEY `fk_bd_user` (`raised_by_user_id`);

--
-- Indexes for table `billing_items`
--
ALTER TABLE `billing_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bi_reservation` (`reservation_id`),
  ADD KEY `fk_bi_user` (`added_by_user_id`);

--
-- Indexes for table `billing_retry_queue`
--
ALTER TABLE `billing_retry_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_brq_res` (`reservation_id`);

--
-- Indexes for table `billing_split_log`
--
ALTER TABLE `billing_split_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bsl_group` (`group_id`),
  ADD KEY `fk_bsl_user` (`split_by_user_id`);

--
-- Indexes for table `corporate_accounts`
--
ALTER TABLE `corporate_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `corrective_tasks`
--
ALTER TABLE `corrective_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ct_qa` (`qa_inspection_id`),
  ADD KEY `fk_ct_user` (`assigned_to_user_id`);

--
-- Indexes for table `emergency_flags`
--
ALTER TABLE `emergency_flags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_order_id` (`work_order_id`);

--
-- Indexes for table `external_services`
--
ALTER TABLE `external_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_feedback_res` (`reservation_id`),
  ADD KEY `fk_feedback_guest` (`guest_id`);

--
-- Indexes for table `final_invoices`
--
ALTER TABLE `final_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fi_reservation` (`reservation_id`);

--
-- Indexes for table `folios`
--
ALTER TABLE `folios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `folio_charges`
--
ALTER TABLE `folio_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_charge_folio` (`folio_id`),
  ADD KEY `fk_charge_staff` (`posted_by`);

--
-- Indexes for table `found_items`
--
ALTER TABLE `found_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lf_reference` (`lf_reference`),
  ADD KEY `fk_fi_user` (`found_by_user_id`);

--
-- Indexes for table `front_desk_queue`
--
ALTER TABLE `front_desk_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fdq_invoice` (`invoice_id`),
  ADD KEY `fk_fdq_res` (`reservation_id`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_gm_reservation` (`reservation_id`),
  ADD KEY `fk_gm_group` (`group_reservation_id`);

--
-- Indexes for table `group_reservations`
--
ALTER TABLE `group_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_grp_coordinator` (`coordinator_guest_id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_guests_referrer` (`referred_by`);

--
-- Indexes for table `guest_corporate`
--
ALTER TABLE `guest_corporate`
  ADD PRIMARY KEY (`guest_id`,`corporate_id`),
  ADD KEY `fk_gc_corporate` (`corporate_id`);

--
-- Indexes for table `guest_preferences`
--
ALTER TABLE `guest_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_guest_pref` (`guest_id`,`pref_key`);

--
-- Indexes for table `housekeeper_performance`
--
ALTER TABLE `housekeeper_performance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `housekeeper_id` (`housekeeper_id`);

--
-- Indexes for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hk_room` (`room_id`),
  ADD KEY `fk_hk_staff` (`assigned_to`);

--
-- Indexes for table `inventory_change_log`
--
ALTER TABLE `inventory_change_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_icl_room_type` (`room_type_id`),
  ADD KEY `fk_icl_user` (`changed_by_user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inv_group` (`group_id`),
  ADD KEY `fk_inv_res` (`reservation_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ii_invoice` (`invoice_id`),
  ADD KEY `fk_ii_res` (`reservation_id`);

--
-- Indexes for table `item_returns`
--
ALTER TABLE `item_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ir_item` (`found_item_id`),
  ADD KEY `fk_ir_guest` (`guest_id`);

--
-- Indexes for table `lost_and_found`
--
ALTER TABLE `lost_and_found`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_laf_guest` (`guest_id`),
  ADD KEY `fk_laf_room` (`room_id`),
  ADD KEY `fk_laf_staff` (`found_by`);

--
-- Indexes for table `lost_item_reports`
--
ALTER TABLE `lost_item_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lir_guest` (`guest_id`),
  ADD KEY `fk_lir_res` (`reservation_id`),
  ADD KEY `fk_lir_fi` (`matched_found_item_id`);

--
-- Indexes for table `low_stock_alerts`
--
ALTER TABLE `low_stock_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lsa_item` (`item_id`),
  ADD KEY `fk_lsa_user` (`acknowledged_by`);

--
-- Indexes for table `maintenance_orders`
--
ALTER TABLE `maintenance_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_maint_room` (`room_id`),
  ADD KEY `fk_maint_reporter` (`reported_by`),
  ADD KEY `fk_maint_assigned` (`assigned_to`);

--
-- Indexes for table `minibar_inventory`
--
ALTER TABLE `minibar_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mi_room_item` (`room_id`,`item_id`),
  ADD KEY `fk_mi_item` (`item_id`);

--
-- Indexes for table `minibar_items`
--
ALTER TABLE `minibar_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `minibar_logs`
--
ALTER TABLE `minibar_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ml_room` (`room_id`),
  ADD KEY `fk_ml_res` (`reservation_id`),
  ADD KEY `fk_ml_hk` (`housekeeper_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pay_folio` (`folio_id`),
  ADD KEY `fk_pay_staff` (`processed_by`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pm_guest` (`guest_id`);

--
-- Indexes for table `payment_retry_queue`
--
ALTER TABLE `payment_retry_queue`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_prq_idempotency` (`idempotency_key`),
  ADD KEY `fk_prq_guest` (`guest_id`),
  ADD KEY `fk_prq_res` (`reservation_id`);

--
-- Indexes for table `pending_debts`
--
ALTER TABLE `pending_debts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pd_guest` (`guest_id`),
  ADD KEY `fk_pd_res` (`reservation_id`);

--
-- Indexes for table `preventative_schedules`
--
ALTER TABLE `preventative_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ps_wo` (`work_order_id`),
  ADD KEY `fk_ps_asset` (`asset_id`),
  ADD KEY `fk_ps_room` (`room_id`);

--
-- Indexes for table `property_wide_alerts`
--
ALTER TABLE `property_wide_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pwa_wo` (`triggered_by_work_order_id`);

--
-- Indexes for table `qa_inspections`
--
ALTER TABLE `qa_inspections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_qa_room` (`room_id`),
  ADD KEY `fk_qa_inspector` (`inspector_id`);

--
-- Indexes for table `quality_scores`
--
ALTER TABLE `quality_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_qs_inspection` (`inspection_id`),
  ADD KEY `fk_qs_housekeeper` (`housekeeper_id`),
  ADD KEY `fk_qs_room` (`room_id`),
  ADD KEY `fk_qs_submitter` (`submitted_by_user_id`);

--
-- Indexes for table `replacement_review_flags`
--
ALTER TABLE `replacement_review_flags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rrf_room` (`room_id`),
  ADD KEY `fk_rrf_asset` (`asset_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_res_room` (`room_id`),
  ADD KEY `fk_res_staff` (`assigned_by`),
  ADD KEY `idx_res_dates` (`check_in_date`,`check_out_date`),
  ADD KEY `idx_res_status` (`status`),
  ADD KEY `idx_res_guest` (`guest_id`);

--
-- Indexes for table `restocking_requisitions`
--
ALTER TABLE `restocking_requisitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rr_user` (`requested_by_user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `fk_rooms_type` (`room_type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `security_alerts`
--
ALTER TABLE `security_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sa_item` (`related_found_item_id`);

--
-- Indexes for table `service_bookings`
--
ALTER TABLE `service_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_service_booking_guest` (`guest_id`),
  ADD KEY `fk_service_booking_service` (`service_id`);

--
-- Indexes for table `supply_inventory`
--
ALTER TABLE `supply_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_si_item_loc` (`item_id`,`location`);

--
-- Indexes for table `supply_items`
--
ALTER TABLE `supply_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tx_idempotency` (`idempotency_key`),
  ADD KEY `fk_tx_guest` (`guest_id`),
  ADD KEY `fk_tx_res` (`reservation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- Indexes for table `virtual_inventory`
--
ALTER TABLE `virtual_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_vi_type_date` (`room_type_id`,`date`),
  ADD KEY `fk_vi_user` (`updated_by_user_id`);

--
-- Indexes for table `work_orders`
--
ALTER TABLE `work_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_wo_room` (`room_id`),
  ADD KEY `fk_wo_asset` (`asset_id`),
  ADD KEY `fk_wo_assigned` (`assigned_to_user_id`),
  ADD KEY `fk_wo_created` (`created_by_user_id`),
  ADD KEY `fk_wo_supervisor` (`supervisor_id`);

--
-- Indexes for table `work_order_logs`
--
ALTER TABLE `work_order_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_wol_wo` (`work_order_id`),
  ADD KEY `fk_wol_user` (`performed_by_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `billing_adjustments`
--
ALTER TABLE `billing_adjustments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `billing_disputes`
--
ALTER TABLE `billing_disputes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billing_items`
--
ALTER TABLE `billing_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `billing_retry_queue`
--
ALTER TABLE `billing_retry_queue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billing_split_log`
--
ALTER TABLE `billing_split_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `corporate_accounts`
--
ALTER TABLE `corporate_accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `corrective_tasks`
--
ALTER TABLE `corrective_tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_flags`
--
ALTER TABLE `emergency_flags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `external_services`
--
ALTER TABLE `external_services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `final_invoices`
--
ALTER TABLE `final_invoices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folios`
--
ALTER TABLE `folios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `folio_charges`
--
ALTER TABLE `folio_charges`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `found_items`
--
ALTER TABLE `found_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `front_desk_queue`
--
ALTER TABLE `front_desk_queue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `group_reservations`
--
ALTER TABLE `group_reservations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `guest_preferences`
--
ALTER TABLE `guest_preferences`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `housekeeper_performance`
--
ALTER TABLE `housekeeper_performance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `inventory_change_log`
--
ALTER TABLE `inventory_change_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_returns`
--
ALTER TABLE `item_returns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lost_and_found`
--
ALTER TABLE `lost_and_found`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lost_item_reports`
--
ALTER TABLE `lost_item_reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `low_stock_alerts`
--
ALTER TABLE `low_stock_alerts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_orders`
--
ALTER TABLE `maintenance_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `minibar_inventory`
--
ALTER TABLE `minibar_inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `minibar_items`
--
ALTER TABLE `minibar_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `minibar_logs`
--
ALTER TABLE `minibar_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_retry_queue`
--
ALTER TABLE `payment_retry_queue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_debts`
--
ALTER TABLE `pending_debts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `preventative_schedules`
--
ALTER TABLE `preventative_schedules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_wide_alerts`
--
ALTER TABLE `property_wide_alerts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qa_inspections`
--
ALTER TABLE `qa_inspections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quality_scores`
--
ALTER TABLE `quality_scores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `replacement_review_flags`
--
ALTER TABLE `replacement_review_flags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `restocking_requisitions`
--
ALTER TABLE `restocking_requisitions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `security_alerts`
--
ALTER TABLE `security_alerts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_bookings`
--
ALTER TABLE `service_bookings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `supply_inventory`
--
ALTER TABLE `supply_inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `supply_items`
--
ALTER TABLE `supply_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `virtual_inventory`
--
ALTER TABLE `virtual_inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `work_orders`
--
ALTER TABLE `work_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `work_order_logs`
--
ALTER TABLE `work_order_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `billing_adjustments`
--
ALTER TABLE `billing_adjustments`
  ADD CONSTRAINT `fk_ba_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ba_user` FOREIGN KEY (`applied_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `billing_disputes`
--
ALTER TABLE `billing_disputes`
  ADD CONSTRAINT `fk_bd_group` FOREIGN KEY (`group_id`) REFERENCES `group_reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bd_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_bd_user` FOREIGN KEY (`raised_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `billing_items`
--
ALTER TABLE `billing_items`
  ADD CONSTRAINT `fk_bi_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bi_user` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `billing_retry_queue`
--
ALTER TABLE `billing_retry_queue`
  ADD CONSTRAINT `fk_brq_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `billing_split_log`
--
ALTER TABLE `billing_split_log`
  ADD CONSTRAINT `fk_bsl_group` FOREIGN KEY (`group_id`) REFERENCES `group_reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bsl_user` FOREIGN KEY (`split_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `corrective_tasks`
--
ALTER TABLE `corrective_tasks`
  ADD CONSTRAINT `fk_ct_qa` FOREIGN KEY (`qa_inspection_id`) REFERENCES `qa_inspections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ct_user` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `emergency_flags`
--
ALTER TABLE `emergency_flags`
  ADD CONSTRAINT `fk_ef_wo` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`),
  ADD CONSTRAINT `fk_feedback_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `final_invoices`
--
ALTER TABLE `final_invoices`
  ADD CONSTRAINT `fk_fi_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `folios`
--
ALTER TABLE `folios`
  ADD CONSTRAINT `fk_folio_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `folio_charges`
--
ALTER TABLE `folio_charges`
  ADD CONSTRAINT `fk_charge_folio` FOREIGN KEY (`folio_id`) REFERENCES `folios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_charge_staff` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `found_items`
--
ALTER TABLE `found_items`
  ADD CONSTRAINT `fk_fi_user` FOREIGN KEY (`found_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `front_desk_queue`
--
ALTER TABLE `front_desk_queue`
  ADD CONSTRAINT `fk_fdq_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fdq_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `fk_gm_group` FOREIGN KEY (`group_reservation_id`) REFERENCES `group_reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_gm_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_reservations`
--
ALTER TABLE `group_reservations`
  ADD CONSTRAINT `fk_grp_coordinator` FOREIGN KEY (`coordinator_guest_id`) REFERENCES `guests` (`id`);

--
-- Constraints for table `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `fk_guests_referrer` FOREIGN KEY (`referred_by`) REFERENCES `guests` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `guest_corporate`
--
ALTER TABLE `guest_corporate`
  ADD CONSTRAINT `fk_gc_corporate` FOREIGN KEY (`corporate_id`) REFERENCES `corporate_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_gc_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guest_preferences`
--
ALTER TABLE `guest_preferences`
  ADD CONSTRAINT `fk_guestpref_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `housekeeper_performance`
--
ALTER TABLE `housekeeper_performance`
  ADD CONSTRAINT `fk_hp_housekeeper` FOREIGN KEY (`housekeeper_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  ADD CONSTRAINT `fk_hk_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `fk_hk_staff` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_change_log`
--
ALTER TABLE `inventory_change_log`
  ADD CONSTRAINT `fk_icl_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_icl_user` FOREIGN KEY (`changed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_inv_group` FOREIGN KEY (`group_id`) REFERENCES `group_reservations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_inv_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `fk_ii_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ii_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `item_returns`
--
ALTER TABLE `item_returns`
  ADD CONSTRAINT `fk_ir_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ir_item` FOREIGN KEY (`found_item_id`) REFERENCES `found_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lost_and_found`
--
ALTER TABLE `lost_and_found`
  ADD CONSTRAINT `fk_laf_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_laf_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_laf_staff` FOREIGN KEY (`found_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lost_item_reports`
--
ALTER TABLE `lost_item_reports`
  ADD CONSTRAINT `fk_lir_fi` FOREIGN KEY (`matched_found_item_id`) REFERENCES `found_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lir_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lir_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `low_stock_alerts`
--
ALTER TABLE `low_stock_alerts`
  ADD CONSTRAINT `fk_lsa_item` FOREIGN KEY (`item_id`) REFERENCES `supply_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lsa_user` FOREIGN KEY (`acknowledged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `maintenance_orders`
--
ALTER TABLE `maintenance_orders`
  ADD CONSTRAINT `fk_maint_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_maint_reporter` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_maint_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `minibar_inventory`
--
ALTER TABLE `minibar_inventory`
  ADD CONSTRAINT `fk_mi_item` FOREIGN KEY (`item_id`) REFERENCES `minibar_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mi_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `minibar_logs`
--
ALTER TABLE `minibar_logs`
  ADD CONSTRAINT `fk_ml_hk` FOREIGN KEY (`housekeeper_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ml_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ml_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_pay_folio` FOREIGN KEY (`folio_id`) REFERENCES `folios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pay_staff` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `fk_pm_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_retry_queue`
--
ALTER TABLE `payment_retry_queue`
  ADD CONSTRAINT `fk_prq_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_prq_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pending_debts`
--
ALTER TABLE `pending_debts`
  ADD CONSTRAINT `fk_pd_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pd_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `preventative_schedules`
--
ALTER TABLE `preventative_schedules`
  ADD CONSTRAINT `fk_ps_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ps_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ps_wo` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_wide_alerts`
--
ALTER TABLE `property_wide_alerts`
  ADD CONSTRAINT `fk_pwa_wo` FOREIGN KEY (`triggered_by_work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `qa_inspections`
--
ALTER TABLE `qa_inspections`
  ADD CONSTRAINT `fk_qa_inspector` FOREIGN KEY (`inspector_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qa_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quality_scores`
--
ALTER TABLE `quality_scores`
  ADD CONSTRAINT `fk_qs_housekeeper` FOREIGN KEY (`housekeeper_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qs_inspection` FOREIGN KEY (`inspection_id`) REFERENCES `qa_inspections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qs_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qs_submitter` FOREIGN KEY (`submitted_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `replacement_review_flags`
--
ALTER TABLE `replacement_review_flags`
  ADD CONSTRAINT `fk_rrf_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_rrf_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_res_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`),
  ADD CONSTRAINT `fk_res_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `fk_res_staff` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `restocking_requisitions`
--
ALTER TABLE `restocking_requisitions`
  ADD CONSTRAINT `fk_rr_user` FOREIGN KEY (`requested_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_rooms_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`);

--
-- Constraints for table `security_alerts`
--
ALTER TABLE `security_alerts`
  ADD CONSTRAINT `fk_sa_item` FOREIGN KEY (`related_found_item_id`) REFERENCES `found_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `service_bookings`
--
ALTER TABLE `service_bookings`
  ADD CONSTRAINT `fk_service_booking_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_service_booking_service` FOREIGN KEY (`service_id`) REFERENCES `external_services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supply_inventory`
--
ALTER TABLE `supply_inventory`
  ADD CONSTRAINT `fk_si_item` FOREIGN KEY (`item_id`) REFERENCES `supply_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_tx_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tx_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `virtual_inventory`
--
ALTER TABLE `virtual_inventory`
  ADD CONSTRAINT `fk_vi_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vi_user` FOREIGN KEY (`updated_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `work_orders`
--
ALTER TABLE `work_orders`
  ADD CONSTRAINT `fk_wo_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_wo_assigned` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_wo_created` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_wo_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_wo_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `work_order_logs`
--
ALTER TABLE `work_order_logs`
  ADD CONSTRAINT `fk_wol_user` FOREIGN KEY (`performed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_wol_wo` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE;
-- --------------------------------------------------------
-- Table structure for table `feedback`
-- Guest post-stay feedback and ratings
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `feedback` (
  `id`                 int(10) unsigned  NOT NULL AUTO_INCREMENT,
  `reservation_id`     int(10) unsigned  NOT NULL,
  `guest_id`           int(10) unsigned  NOT NULL,
  `guest_name`         varchar(120)      DEFAULT NULL,
  `overall_rating`     tinyint(4)        NOT NULL DEFAULT 1,
  `cleanliness_rating` tinyint(4)        NOT NULL DEFAULT 1,
  `staff_rating`       tinyint(4)        NOT NULL DEFAULT 1,
  `food_rating`        tinyint(4)        NOT NULL DEFAULT 1,
  `facilities_rating`  tinyint(4)        NOT NULL DEFAULT 1,
  `comment`            text              DEFAULT NULL,
  `recommend_hotel`    tinyint(1)        NOT NULL DEFAULT 1,
  `is_resolved`        tinyint(1)        NOT NULL DEFAULT 0,
  `resolved_at`        timestamp         NULL DEFAULT NULL,
  `resolved_by`        int(10) unsigned  DEFAULT NULL,
  `created_at`         timestamp         NOT NULL DEFAULT current_timestamp(),
  -- Legacy columns (kept for backwards compatibility)
  `rating`             tinyint(3) unsigned NOT NULL DEFAULT 1,
  `comments`           text              DEFAULT NULL,
  `submitted_at`       timestamp         NOT NULL DEFAULT current_timestamp(),
  `overall_score`      tinyint(3) unsigned DEFAULT NULL,
  `flagged_for_qa`     tinyint(1)        NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_feedback_guest`   (`guest_id`),
  KEY `idx_feedback_rating`  (`overall_rating`),
  KEY `idx_feedback_created` (`created_at`),
  KEY `idx_feedback_status`  (`is_resolved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_guest`
    FOREIGN KEY (`guest_id`)       REFERENCES `guests` (`id`)       ON DELETE CASCADE,
  ADD CONSTRAINT `fk_feedback_reservation`
    FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_feedback_resolver`
    FOREIGN KEY (`resolved_by`)    REFERENCES `users` (`id`)        ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
