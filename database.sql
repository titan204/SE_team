-- Hotel Management System - Full Export
CREATE DATABASE IF NOT EXISTS `hotel_management`;
USE `hotel_management`;

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: hotel_management
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `location` varchar(150) NOT NULL,
  `asset_type` enum('hvac','elevator','plumbing','electrical','equipment','other') NOT NULL DEFAULT 'other',
  `status` enum('operational','under_maintenance','decommissioned') NOT NULL DEFAULT 'operational',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
INSERT INTO `assets` VALUES (1,'Main HVAC Unit ΓÇö Floor 1','Floor 1 Plant Room','hvac','operational'),(2,'Main HVAC Unit ΓÇö Floor 2','Floor 2 Plant Room','hvac','operational'),(3,'Main HVAC Unit ΓÇö Floor 3','Floor 3 Plant Room','hvac','under_maintenance'),(4,'Passenger Elevator 1','Lobby','elevator','operational'),(5,'Passenger Elevator 2','North Wing','elevator','operational'),(6,'Boiler ΓÇö Hot Water System','Basement','plumbing','operational'),(7,'Main Electrical Panel','Basement','electrical','operational'),(8,'Pool Pump System','Pool Area','equipment','operational');
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(10) unsigned DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_audit_user` (`user_id`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_log`
--

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
INSERT INTO `audit_log` VALUES (1,1,'price_override','folio',4,'2500.00','2550.00','2026-05-01 21:45:43'),(2,1,'status_change','reservation',8,'confirmed','cancelled','2026-05-01 21:45:43'),(3,3,'check_in','reservation',1,'confirmed','checked_in','2026-05-01 21:45:43'),(4,3,'check_in','reservation',9,'confirmed','checked_in','2026-05-01 21:45:43'),(5,4,'check_out','reservation',2,'checked_in','checked_out','2026-05-01 21:45:43'),(6,3,'check_out','reservation',3,'checked_in','checked_out','2026-05-01 21:45:43'),(7,4,'check_out','reservation',4,'checked_in','checked_out','2026-05-01 21:45:43'),(8,3,'check_out','reservation',9,'checked_in','checked_out','2026-05-01 21:45:43'),(9,1,'room_status','room',7,'available','out_of_order','2026-05-01 21:45:43'),(10,2,'loyalty_upgrade','guest',4,'gold','platinum','2026-05-01 21:45:43'),(11,2,'vip_flag','guest',1,'0','1','2026-05-01 21:45:43'),(12,4,'no_show','reservation',10,'confirmed','no_show','2026-05-01 21:45:43'),(13,1,'price_override','folio',4,'2500.00','2550.00','2026-05-01 21:53:46'),(14,1,'status_change','reservation',8,'confirmed','cancelled','2026-05-01 21:53:46'),(15,3,'check_in','reservation',1,'confirmed','checked_in','2026-05-01 21:53:46'),(16,3,'check_in','reservation',9,'confirmed','checked_in','2026-05-01 21:53:46'),(17,4,'check_out','reservation',2,'checked_in','checked_out','2026-05-01 21:53:46'),(18,3,'check_out','reservation',3,'checked_in','checked_out','2026-05-01 21:53:46'),(19,4,'check_out','reservation',4,'checked_in','checked_out','2026-05-01 21:53:46'),(20,3,'check_out','reservation',9,'checked_in','checked_out','2026-05-01 21:53:46'),(21,1,'room_status','room',7,'available','out_of_order','2026-05-01 21:53:46'),(22,2,'loyalty_upgrade','guest',4,'gold','platinum','2026-05-01 21:53:46'),(23,2,'vip_flag','guest',1,'0','1','2026-05-01 21:53:46'),(24,4,'no_show','reservation',10,'confirmed','no_show','2026-05-01 21:53:46'),(25,6,'housekeeping.task.done','housekeeping_task',11,NULL,NULL,'2026-05-01 23:36:40'),(26,9,'check_in','reservation',27,'confirmed','checked_in','2026-05-03 00:29:41'),(27,8,'check_in','reservation',28,'confirmed','checked_in','2026-05-03 00:51:29'),(28,8,'room_upgrade','reservation',28,'10','15','2026-05-03 00:51:45'),(29,8,'room_upgrade','reservation',28,'15','20','2026-05-03 00:52:07');
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_adjustments`
--

DROP TABLE IF EXISTS `billing_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_adjustments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int(10) unsigned NOT NULL,
  `type` enum('discount','surcharge','loyalty_redemption') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `applied_by_user_id` int(10) unsigned DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_ba_reservation` (`reservation_id`),
  KEY `fk_ba_user` (`applied_by_user_id`),
  CONSTRAINT `fk_ba_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ba_user` FOREIGN KEY (`applied_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_adjustments`
--

LOCK TABLES `billing_adjustments` WRITE;
/*!40000 ALTER TABLE `billing_adjustments` DISABLE KEYS */;
/*!40000 ALTER TABLE `billing_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_disputes`
--

DROP TABLE IF EXISTS `billing_disputes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_disputes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `reservation_id` int(10) unsigned DEFAULT NULL,
  `raised_by_user_id` int(10) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('open','resolved') NOT NULL DEFAULT 'open',
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_bd_group` (`group_id`),
  KEY `fk_bd_res` (`reservation_id`),
  KEY `fk_bd_user` (`raised_by_user_id`),
  CONSTRAINT `fk_bd_group` FOREIGN KEY (`group_id`) REFERENCES `group_reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bd_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_bd_user` FOREIGN KEY (`raised_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_disputes`
--

LOCK TABLES `billing_disputes` WRITE;
/*!40000 ALTER TABLE `billing_disputes` DISABLE KEYS */;
/*!40000 ALTER TABLE `billing_disputes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_items`
--

DROP TABLE IF EXISTS `billing_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int(10) unsigned NOT NULL,
  `item_type` enum('room_rate','minibar','external_service','manual','other') NOT NULL DEFAULT 'manual',
  `description` varchar(255) NOT NULL DEFAULT '',
  `amount` decimal(10,2) NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `added_by_user_id` int(10) unsigned DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_voided` tinyint(1) NOT NULL DEFAULT 0,
  `void_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_bi_reservation` (`reservation_id`),
  KEY `fk_bi_user` (`added_by_user_id`),
  CONSTRAINT `fk_bi_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bi_user` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_items`
--

LOCK TABLES `billing_items` WRITE;
/*!40000 ALTER TABLE `billing_items` DISABLE KEYS */;
INSERT INTO `billing_items` VALUES (1,1,'minibar','Minibar ΓÇö Water, Juice, Snacks',25.00,1,1,'2026-05-01 21:45:44',0,NULL),(2,1,'external_service','Spa Session ΓÇö 60 min',80.00,1,1,'2026-05-01 21:45:44',0,NULL),(3,2,'minibar','Minibar ΓÇö Beer x2',18.00,2,1,'2026-05-01 21:45:44',0,NULL);
/*!40000 ALTER TABLE `billing_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_retry_queue`
--

DROP TABLE IF EXISTS `billing_retry_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_retry_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int(10) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_brq_res` (`reservation_id`),
  CONSTRAINT `fk_brq_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_retry_queue`
--

LOCK TABLES `billing_retry_queue` WRITE;
/*!40000 ALTER TABLE `billing_retry_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `billing_retry_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_split_log`
--

DROP TABLE IF EXISTS `billing_split_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_split_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `split_by_user_id` int(10) unsigned DEFAULT NULL,
  `members_split` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`members_split`)),
  `original_consolidated_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_bsl_group` (`group_id`),
  KEY `fk_bsl_user` (`split_by_user_id`),
  CONSTRAINT `fk_bsl_group` FOREIGN KEY (`group_id`) REFERENCES `group_reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bsl_user` FOREIGN KEY (`split_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_split_log`
--

LOCK TABLES `billing_split_log` WRITE;
/*!40000 ALTER TABLE `billing_split_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `billing_split_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corporate_accounts`
--

DROP TABLE IF EXISTS `corporate_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corporate_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(200) NOT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `contracted_rate` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corporate_accounts`
--

LOCK TABLES `corporate_accounts` WRITE;
/*!40000 ALTER TABLE `corporate_accounts` DISABLE KEYS */;
INSERT INTO `corporate_accounts` VALUES (1,'TechCorp International','travel@techcorp.com','+1-800-555-0201',15.00,'2026-05-01 21:45:43'),(2,'Global Consulting Group','bookings@gcg.com','+44-800-555-0202',10.00,'2026-05-01 21:45:43'),(3,'Emirates Business Hub','hotels@emirateshub.ae','+971-4-800-0203',20.00,'2026-05-01 21:45:43'),(4,'TechCorp International','travel@techcorp.com','+1-800-555-0201',15.00,'2026-05-01 21:53:46'),(5,'Global Consulting Group','bookings@gcg.com','+44-800-555-0202',10.00,'2026-05-01 21:53:46'),(6,'Emirates Business Hub','hotels@emirateshub.ae','+971-4-800-0203',20.00,'2026-05-01 21:53:46');
/*!40000 ALTER TABLE `corporate_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corrective_tasks`
--

DROP TABLE IF EXISTS `corrective_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corrective_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `qa_inspection_id` int(10) unsigned NOT NULL,
  `assigned_to_user_id` int(10) unsigned DEFAULT NULL,
  `task_description` text NOT NULL,
  `due_by` datetime DEFAULT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `fk_ct_qa` (`qa_inspection_id`),
  KEY `fk_ct_user` (`assigned_to_user_id`),
  CONSTRAINT `fk_ct_qa` FOREIGN KEY (`qa_inspection_id`) REFERENCES `qa_inspections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ct_user` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corrective_tasks`
--

LOCK TABLES `corrective_tasks` WRITE;
/*!40000 ALTER TABLE `corrective_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `corrective_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emergency_flags`
--

DROP TABLE IF EXISTS `emergency_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emergency_flags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` int(10) unsigned NOT NULL,
  `severity` enum('low','medium','high','safety_critical') NOT NULL,
  `is_safety_critical` tinyint(1) NOT NULL DEFAULT 0,
  `property_alert_triggered` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `work_order_id` (`work_order_id`),
  CONSTRAINT `fk_ef_wo` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emergency_flags`
--

LOCK TABLES `emergency_flags` WRITE;
/*!40000 ALTER TABLE `emergency_flags` DISABLE KEYS */;
/*!40000 ALTER TABLE `emergency_flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `external_services`
--

DROP TABLE IF EXISTS `external_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `external_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `external_services`
--

LOCK TABLES `external_services` WRITE;
/*!40000 ALTER TABLE `external_services` DISABLE KEYS */;
INSERT INTO `external_services` VALUES (1,'Grand Spa & Wellness','spa','Full-service spa: massages, facials, hydrotherapy','2026-05-01 21:45:43'),(2,'The Gourmet Kitchen','restaurant','Fine dining with international and local cuisine','2026-05-01 21:45:43'),(3,'Airport Luxury Transfers','transport','Premium car service to/from all major airports','2026-05-01 21:45:43'),(4,'City Cultural Tours','tour','Guided half-day and full-day city sightseeing tours','2026-05-01 21:45:43'),(5,'Business Centre Services','business','Printing, scanning, secretarial, and meeting room hire','2026-05-01 21:45:43'),(6,'Grand Spa & Wellness','spa','Full-service spa: massages, facials, hydrotherapy','2026-05-01 21:53:46'),(7,'The Gourmet Kitchen','restaurant','Fine dining with international and local cuisine','2026-05-01 21:53:46'),(8,'Airport Luxury Transfers','transport','Premium car service to/from all major airports','2026-05-01 21:53:46'),(9,'City Cultural Tours','tour','Guided half-day and full-day city sightseeing tours','2026-05-01 21:53:46'),(10,'Business Centre Services','business','Printing, scanning, secretarial, and meeting room hire','2026-05-01 21:53:46');
/*!40000 ALTER TABLE `external_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int(10) unsigned NOT NULL,
  `guest_id` int(10) unsigned NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL CHECK (`rating` between 1 and 5),
  `comments` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `overall_score` tinyint(3) unsigned DEFAULT NULL,
  `flagged_for_qa` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_feedback_res` (`reservation_id`),
  KEY `fk_feedback_guest` (`guest_id`),
  CONSTRAINT `fk_feedback_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`),
  CONSTRAINT `fk_feedback_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
INSERT INTO `feedback` VALUES (1,2,2,4,'Great stay overall. Room was clean and the front desk staff were very helpful. WiFi could be faster.','2026-05-01 21:45:43',NULL,0),(2,3,7,5,'Absolutely wonderful experience. The spa was exceptional and the room was spotless. Will return!','2026-05-01 21:45:43',NULL,0),(3,4,6,3,'Room and food were good, but the late checkout fee was unexpected and not communicated upfront.','2026-05-01 21:45:43',NULL,0),(4,9,1,5,'The anniversary surprise exceeded all expectations. Suite was magnificent. Cannot wait to come back.','2026-05-01 21:45:43',NULL,0),(5,2,2,4,'Great stay overall. Room was clean and the front desk staff were very helpful. WiFi could be faster.','2026-05-01 21:53:46',NULL,0),(6,3,7,5,'Absolutely wonderful experience. The spa was exceptional and the room was spotless. Will return!','2026-05-01 21:53:46',NULL,0),(7,4,6,3,'Room and food were good, but the late checkout fee was unexpected and not communicated upfront.','2026-05-01 21:53:46',NULL,0),(8,9,1,5,'The anniversary surprise exceeded all expectations. Suite was magnificent. Cannot wait to come back.','2026-05-01 21:53:46',NULL,0);
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `final_invoices`
--

DROP TABLE IF EXISTS `final_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `final_invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int(10) unsigned NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_finalized` tinyint(1) NOT NULL DEFAULT 0,
  `issued_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fi_reservation` (`reservation_id`),
  CONSTRAINT `fk_fi_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `final_invoices`
--

LOCK TABLES `final_invoices` WRITE;
/*!40000 ALTER TABLE `final_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `final_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `folio_charges`
--

DROP TABLE IF EXISTS `folio_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `folio_charges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folio_id` int(10) unsigned NOT NULL,
  `charge_type` enum('room_rate','service','minibar','spa','restaurant','penalty','tax','other') NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `posted_by` int(10) unsigned DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_charge_folio` (`folio_id`),
  KEY `fk_charge_staff` (`posted_by`),
  CONSTRAINT `fk_charge_folio` FOREIGN KEY (`folio_id`) REFERENCES `folios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_charge_staff` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `folio_charges`
--

LOCK TABLES `folio_charges` WRITE;
/*!40000 ALTER TABLE `folio_charges` DISABLE KEYS */;
INSERT INTO `folio_charges` VALUES (1,1,'room_rate','Room 102 ΓÇô Standard ├ù 4 nights',2000.00,3,'2026-05-01 21:45:43'),(2,1,'minibar','Minibar consumption ΓÇô 2026-04-26',65.00,5,'2026-05-01 21:45:43'),(3,1,'service','Extra pillow set + turndown service',20.00,6,'2026-05-01 21:45:43'),(4,2,'room_rate','Room 201 ΓÇô Standard ├ù 4 nights',2000.00,4,'2026-05-01 21:45:43'),(5,2,'restaurant','In-room dining ΓÇô 2026-04-24',150.00,4,'2026-05-01 21:45:43'),(6,3,'room_rate','Room 202 ΓÇô Deluxe ├ù 4 nights',3200.00,3,'2026-05-01 21:45:43'),(7,3,'spa','Aromatherapy massage ΓÇô 2026-04-25',150.00,3,'2026-05-01 21:45:43'),(8,4,'room_rate','Room 203 ΓÇô Deluxe ├ù 3 nights',2400.00,4,'2026-05-01 21:45:43'),(9,4,'restaurant','Restaurant dinner ΓÇô 2026-04-25',100.00,4,'2026-05-01 21:45:43'),(10,4,'penalty','Late check-out fee (2 hrs past policy)',50.00,1,'2026-05-01 21:45:43'),(11,5,'room_rate','Room 101 ΓÇô Standard ├ù 4 nights (pre-auth)',2000.00,3,'2026-05-01 21:45:43'),(12,6,'room_rate','Room 301 ΓÇô Suite ├ù 6 nights (pre-auth)',9000.00,4,'2026-05-01 21:45:43'),(13,8,'room_rate','Room 301 ΓÇô Suite ├ù 5 nights',7500.00,3,'2026-05-01 21:45:43'),(14,8,'spa','Couples massage ΓÇô 2026-03-12',200.00,3,'2026-05-01 21:45:43'),(15,8,'restaurant','Anniversary dinner ΓÇô 2026-03-14',150.00,3,'2026-05-01 21:45:43'),(16,9,'room_rate','No-show fee ΓÇô deposit forfeited',500.00,1,'2026-05-01 21:45:43'),(17,1,'room_rate','Room 102 ΓÇô Standard ├ù 4 nights',2000.00,3,'2026-05-01 21:53:46'),(18,1,'minibar','Minibar consumption ΓÇô 2026-04-26',65.00,5,'2026-05-01 21:53:46'),(19,1,'service','Extra pillow set + turndown service',20.00,6,'2026-05-01 21:53:46'),(20,2,'room_rate','Room 201 ΓÇô Standard ├ù 4 nights',2000.00,4,'2026-05-01 21:53:46'),(21,2,'restaurant','In-room dining ΓÇô 2026-04-24',150.00,4,'2026-05-01 21:53:46'),(22,3,'room_rate','Room 202 ΓÇô Deluxe ├ù 4 nights',3200.00,3,'2026-05-01 21:53:46'),(23,3,'spa','Aromatherapy massage ΓÇô 2026-04-25',150.00,3,'2026-05-01 21:53:46'),(24,4,'room_rate','Room 203 ΓÇô Deluxe ├ù 3 nights',2400.00,4,'2026-05-01 21:53:46'),(25,4,'restaurant','Restaurant dinner ΓÇô 2026-04-25',100.00,4,'2026-05-01 21:53:46'),(26,4,'penalty','Late check-out fee (2 hrs past policy)',50.00,1,'2026-05-01 21:53:46'),(27,5,'room_rate','Room 101 ΓÇô Standard ├ù 4 nights (pre-auth)',2000.00,3,'2026-05-01 21:53:46'),(28,6,'room_rate','Room 301 ΓÇô Suite ├ù 6 nights (pre-auth)',9000.00,4,'2026-05-01 21:53:46'),(29,8,'room_rate','Room 301 ΓÇô Suite ├ù 5 nights',7500.00,3,'2026-05-01 21:53:46'),(30,8,'spa','Couples massage ΓÇô 2026-03-12',200.00,3,'2026-05-01 21:53:46'),(31,8,'restaurant','Anniversary dinner ΓÇô 2026-03-14',150.00,3,'2026-05-01 21:53:46'),(32,9,'room_rate','No-show fee ΓÇô deposit forfeited',500.00,1,'2026-05-01 21:53:46');
/*!40000 ALTER TABLE `folio_charges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `folios`
--

DROP TABLE IF EXISTS `folios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `folios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int(10) unsigned NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_due` decimal(12,2) GENERATED ALWAYS AS (`total_amount` - `amount_paid`) STORED,
  `status` enum('open','settled','refunded') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservation_id` (`reservation_id`),
  CONSTRAINT `fk_folio_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `folios`
--

LOCK TABLES `folios` WRITE;
/*!40000 ALTER TABLE `folios` DISABLE KEYS */;
INSERT INTO `folios` VALUES (1,1,2085.00,1250.00,835.00,'open','2026-05-01 21:45:43','2026-05-02 00:34:23'),(2,2,2150.00,2150.00,0.00,'settled','2026-05-01 21:45:43','2026-05-01 21:45:43'),(3,3,3350.00,3350.00,0.00,'settled','2026-05-01 21:45:43','2026-05-01 21:45:43'),(4,4,2550.00,2550.00,0.00,'settled','2026-05-01 21:45:43','2026-05-01 21:45:43'),(5,5,2000.00,500.00,1500.00,'open','2026-05-01 21:45:43','2026-05-01 21:45:43'),(6,6,9000.00,6700.00,2300.00,'open','2026-05-01 21:45:43','2026-05-02 00:34:23'),(7,8,1500.00,800.00,700.00,'open','2026-05-01 21:45:43','2026-05-02 00:34:23'),(8,9,7850.00,7850.00,0.00,'settled','2026-05-01 21:45:43','2026-05-01 21:45:43'),(9,10,1000.00,500.00,500.00,'open','2026-05-01 21:45:43','2026-05-01 21:45:43'),(11,21,1800.00,500.00,1300.00,'open','2026-05-02 01:07:51','2026-05-02 01:07:51'),(12,22,1400.00,400.00,1000.00,'open','2026-05-02 01:07:51','2026-05-02 01:07:51'),(13,23,1200.00,300.00,900.00,'open','2026-05-02 01:07:51','2026-05-02 01:07:51'),(14,24,1600.00,300.00,1300.00,'open','2026-05-02 01:07:51','2026-05-02 01:07:51'),(15,25,1200.00,250.00,950.00,'open','2026-05-02 01:07:51','2026-05-02 01:07:51'),(16,26,500.00,0.00,500.00,'open','2026-05-03 00:09:52','2026-05-03 00:09:52'),(17,27,7500.00,0.00,7500.00,'open','2026-05-03 00:19:22','2026-05-03 00:19:22'),(18,28,4500.00,900.00,3600.00,'open','2026-05-03 00:50:01','2026-05-03 00:50:37'),(19,29,34500.00,0.00,34500.00,'open','2026-05-03 00:52:50','2026-05-03 00:52:50');
/*!40000 ALTER TABLE `folios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `found_items`
--

DROP TABLE IF EXISTS `found_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `found_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lf_reference` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `location_type` enum('room','public') NOT NULL DEFAULT 'room',
  `room_number` varchar(10) DEFAULT NULL,
  `public_area` enum('lobby','pool','restaurant','elevator','parking','other') DEFAULT NULL,
  `condition` enum('good','damaged','fragile') NOT NULL DEFAULT 'good',
  `photo_url` varchar(500) DEFAULT NULL,
  `is_high_value` tinyint(1) NOT NULL DEFAULT 0,
  `escalated_to_security` tinyint(1) NOT NULL DEFAULT 0,
  `found_by_user_id` int(10) unsigned DEFAULT NULL,
  `found_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('stored','matched','claimed','shipped','returned','disposed') NOT NULL DEFAULT 'stored',
  PRIMARY KEY (`id`),
  UNIQUE KEY `lf_reference` (`lf_reference`),
  KEY `fk_fi_user` (`found_by_user_id`),
  CONSTRAINT `fk_fi_user` FOREIGN KEY (`found_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `found_items`
--

LOCK TABLES `found_items` WRITE;
/*!40000 ALTER TABLE `found_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `found_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `front_desk_queue`
--

DROP TABLE IF EXISTS `front_desk_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `front_desk_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `reservation_id` int(10) unsigned DEFAULT NULL,
  `guest_name` varchar(200) NOT NULL DEFAULT '',
  `reason` varchar(100) NOT NULL DEFAULT 'no_email_manual_delivery',
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_fdq_invoice` (`invoice_id`),
  KEY `fk_fdq_res` (`reservation_id`),
  CONSTRAINT `fk_fdq_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fdq_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `front_desk_queue`
--

LOCK TABLES `front_desk_queue` WRITE;
/*!40000 ALTER TABLE `front_desk_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `front_desk_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_members`
--

DROP TABLE IF EXISTS `group_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_reservation_id` int(10) unsigned NOT NULL,
  `reservation_id` int(10) unsigned NOT NULL,
  `billing_type` enum('group','individual') NOT NULL DEFAULT 'group',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_gm_reservation` (`reservation_id`),
  KEY `fk_gm_group` (`group_reservation_id`),
  CONSTRAINT `fk_gm_group` FOREIGN KEY (`group_reservation_id`) REFERENCES `group_reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gm_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_members`
--

LOCK TABLES `group_members` WRITE;
/*!40000 ALTER TABLE `group_members` DISABLE KEYS */;
INSERT INTO `group_members` VALUES (1,1,5,'group'),(2,1,6,'individual');
/*!40000 ALTER TABLE `group_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_reservations`
--

DROP TABLE IF EXISTS `group_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_reservations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(200) NOT NULL,
  `coordinator_guest_id` int(10) unsigned NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_grp_coordinator` (`coordinator_guest_id`),
  CONSTRAINT `fk_grp_coordinator` FOREIGN KEY (`coordinator_guest_id`) REFERENCES `guests` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_reservations`
--

LOCK TABLES `group_reservations` WRITE;
/*!40000 ALTER TABLE `group_reservations` DISABLE KEYS */;
INSERT INTO `group_reservations` VALUES (1,'TechCorp Conference Group',1,10.00,'2026-05-01 21:45:43'),(2,'TechCorp Conference Group',1,10.00,'2026-05-01 21:53:46');
/*!40000 ALTER TABLE `group_reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest_corporate`
--

DROP TABLE IF EXISTS `guest_corporate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guest_corporate` (
  `guest_id` int(10) unsigned NOT NULL,
  `corporate_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`guest_id`,`corporate_id`),
  KEY `fk_gc_corporate` (`corporate_id`),
  CONSTRAINT `fk_gc_corporate` FOREIGN KEY (`corporate_id`) REFERENCES `corporate_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gc_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guest_corporate`
--

LOCK TABLES `guest_corporate` WRITE;
/*!40000 ALTER TABLE `guest_corporate` DISABLE KEYS */;
INSERT INTO `guest_corporate` VALUES (1,1),(4,1),(5,3),(6,2);
/*!40000 ALTER TABLE `guest_corporate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest_preferences`
--

DROP TABLE IF EXISTS `guest_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guest_preferences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(10) unsigned NOT NULL,
  `pref_key` varchar(100) NOT NULL,
  `pref_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_guest_pref` (`guest_id`,`pref_key`),
  CONSTRAINT `fk_guestpref_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guest_preferences`
--

LOCK TABLES `guest_preferences` WRITE;
/*!40000 ALTER TABLE `guest_preferences` DISABLE KEYS */;
INSERT INTO `guest_preferences` VALUES (1,1,'pillow_type','firm'),(2,1,'floor_preference','high'),(3,1,'dietary','no pork'),(4,2,'pillow_type','soft'),(5,2,'room_temperature','cool'),(6,3,'dietary','vegetarian'),(7,3,'floor_preference','low'),(8,4,'pillow_type','memory_foam'),(9,4,'room_temperature','warm'),(10,4,'newspaper','Financial Times'),(11,4,'amenities','extra towels,fruit basket'),(12,5,'dietary','halal'),(13,5,'pillow_type','soft'),(14,6,'newspaper','Le Monde'),(15,6,'dietary','no shellfish'),(16,7,'dietary','vegan'),(17,7,'room_temperature','cool'),(18,8,'pillow_type','firm'),(19,8,'floor_preference','high');
/*!40000 ALTER TABLE `guest_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guests`
--

DROP TABLE IF EXISTS `guests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `nationality` varchar(80) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `loyalty_tier` enum('standard','silver','gold','platinum') NOT NULL DEFAULT 'standard',
  `lifetime_nights` int(10) unsigned NOT NULL DEFAULT 0,
  `lifetime_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_blacklisted` tinyint(1) NOT NULL DEFAULT 0,
  `blacklist_reason` text DEFAULT NULL,
  `is_vip` tinyint(1) NOT NULL DEFAULT 0,
  `gdpr_anonymized` tinyint(1) NOT NULL DEFAULT 0,
  `referred_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `loyalty_points` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_guests_referrer` (`referred_by`),
  CONSTRAINT `fk_guests_referrer` FOREIGN KEY (`referred_by`) REFERENCES `guests` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guests`
--

LOCK TABLES `guests` WRITE;
/*!40000 ALTER TABLE `guests` DISABLE KEYS */;
INSERT INTO `guests` VALUES (1,'John Smith','john.smith@gmail.com','+1-212-555-0101','US123456','American','1985-03-14','gold',42,35000.00,0,NULL,1,0,NULL,'2026-05-01 21:45:43','2026-05-01 21:45:44',1500),(2,'Emma Wilson','emma.wilson@gmail.com','+44-207-946-0102','UK789012','British','1990-07-22','silver',18,9500.00,0,NULL,0,0,NULL,'2026-05-01 21:45:43','2026-05-01 21:45:44',800),(3,'Carlos Rodriguez','carlos.rodriguez@gmail.com','+34-91-555-0103','ES345678','Spanish','1978-11-05','standard',5,2500.00,0,NULL,0,0,1,'2026-05-01 21:45:43','2026-05-01 21:45:43',0),(4,'Yuki Tanaka','yuki.tanaka@gmail.com','+81-3-5555-0104','JP901234','Japanese','1992-01-30','platinum',95,95000.00,0,NULL,1,0,NULL,'2026-05-01 21:45:43','2026-05-01 21:45:44',250),(5,'Aisha Al-Rashid','aisha.alrashid@gmail.com','+971-4-555-0105','AE567890','Emirati','1988-09-18','silver',22,12000.00,0,NULL,0,0,NULL,'2026-05-01 21:45:43','2026-05-01 21:45:43',0),(6,'Pierre Dubois','pierre.dubois@gmail.com','+33-1-5555-0106','FR123789','French','1975-05-25','gold',55,48000.00,0,NULL,0,0,NULL,'2026-05-01 21:45:43','2026-05-01 21:45:43',0),(7,'Priya Sharma','priya.sharma@gmail.com','+91-98-5555-0107','IN456012','Indian','1995-12-10','standard',8,3200.00,0,NULL,0,0,2,'2026-05-01 21:45:43','2026-05-01 21:45:43',0),(8,'David Chen','david.chen@gmail.com','+1-650-555-0108','US789345','American','1982-06-03','gold',38,30000.00,0,NULL,0,0,NULL,'2026-05-01 21:45:43','2026-05-01 21:45:43',0);
/*!40000 ALTER TABLE `guests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `housekeeper_performance`
--

DROP TABLE IF EXISTS `housekeeper_performance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `housekeeper_performance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `housekeeper_id` int(10) unsigned NOT NULL,
  `avg_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_inspections` int(10) unsigned NOT NULL DEFAULT 0,
  `trend` enum('improving','stable','declining') NOT NULL DEFAULT 'stable',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `housekeeper_id` (`housekeeper_id`),
  CONSTRAINT `fk_hp_housekeeper` FOREIGN KEY (`housekeeper_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `housekeeper_performance`
--

LOCK TABLES `housekeeper_performance` WRITE;
/*!40000 ALTER TABLE `housekeeper_performance` DISABLE KEYS */;
INSERT INTO `housekeeper_performance` VALUES (1,5,88.50,12,'stable','2026-05-02 00:45:45'),(2,6,91.25,15,'improving','2026-05-02 00:45:45'),(3,7,85.00,9,'stable','2026-05-02 00:45:45');
/*!40000 ALTER TABLE `housekeeper_performance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `housekeeping_tasks`
--

DROP TABLE IF EXISTS `housekeeping_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `housekeeping_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned NOT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  `task_type` enum('cleaning','turndown','inspection','deep_clean','minibar_check') NOT NULL DEFAULT 'cleaning',
  `status` enum('pending','in_progress','done','skipped') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `quality_score` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_hk_room` (`room_id`),
  KEY `fk_hk_staff` (`assigned_to`),
  CONSTRAINT `fk_hk_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  CONSTRAINT `fk_hk_staff` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `housekeeping_tasks`
--

LOCK TABLES `housekeeping_tasks` WRITE;
/*!40000 ALTER TABLE `housekeeping_tasks` DISABLE KEYS */;
INSERT INTO `housekeeping_tasks` VALUES (1,3,5,'cleaning','pending','Post-checkout full clean, change all linen',NULL,'2026-05-02 04:15:00','2026-05-02 04:15:00',NULL),(2,4,6,'cleaning','in_progress','Deep clean + restock minibar and amenities',NULL,'2026-05-02 04:30:00','2026-05-02 06:45:00',NULL),(3,5,5,'cleaning','done','Clean completed after checkout',87,'2026-05-02 03:00:00','2026-05-02 05:20:00','2026-05-02 05:20:00'),(4,5,7,'inspection','pending','Supervisor sign-off required before re-let',NULL,'2026-05-02 05:25:00','2026-05-02 05:25:00',NULL),(5,2,6,'turndown','done','VIP guest ├╗ chocolates and extra towels',88,'2026-05-02 05:00:00','2026-05-02 07:10:00','2026-05-02 07:10:00'),(6,6,7,'minibar_check','done','Fully stocked, champagne chilled for Yuki',72,'2026-05-02 03:30:00','2026-05-02 04:55:00','2026-05-02 04:55:00'),(7,1,5,'inspection','done','Room cleared and ready for check-in',91,'2026-05-02 03:00:00','2026-05-02 04:30:00','2026-05-02 04:30:00'),(8,8,6,'cleaning','done','Routine clean, ready for next guest',68,'2026-05-02 04:00:00','2026-05-02 06:00:00','2026-05-02 06:00:00'),(9,3,7,'deep_clean','pending','Full mattress flip and carpet steam clean',NULL,'2026-05-02 06:00:00','2026-05-02 06:00:00',NULL),(19,2,5,'turndown','pending','Evening turndown ├╗ VIP guest, mints and rose petals requested',NULL,'2026-05-02 11:00:00','2026-05-02 11:00:00',NULL);
/*!40000 ALTER TABLE `housekeeping_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_change_log`
--

DROP TABLE IF EXISTS `inventory_change_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_change_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_type_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `old_virtual_max` int(11) NOT NULL DEFAULT 0,
  `new_virtual_max` int(11) NOT NULL DEFAULT 0,
  `changed_by_user_id` int(10) unsigned DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_icl_room_type` (`room_type_id`),
  KEY `fk_icl_user` (`changed_by_user_id`),
  CONSTRAINT `fk_icl_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_icl_user` FOREIGN KEY (`changed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_change_log`
--

LOCK TABLES `inventory_change_log` WRITE;
/*!40000 ALTER TABLE `inventory_change_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_change_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `amount` decimal(10,2) NOT NULL,
  `item_type` enum('room_rate','service','minibar','tax','discount','other') NOT NULL DEFAULT 'other',
  `reservation_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ii_invoice` (`invoice_id`),
  KEY `fk_ii_res` (`reservation_id`),
  CONSTRAINT `fk_ii_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ii_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned DEFAULT NULL,
  `reservation_id` int(10) unsigned DEFAULT NULL,
  `invoice_type` enum('group','individual') NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','finalized','paid','void') NOT NULL DEFAULT 'draft',
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_inv_group` (`group_id`),
  KEY `fk_inv_res` (`reservation_id`),
  CONSTRAINT `fk_inv_group` FOREIGN KEY (`group_id`) REFERENCES `group_reservations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_inv_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_returns`
--

DROP TABLE IF EXISTS `item_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_returns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `found_item_id` int(10) unsigned NOT NULL,
  `guest_id` int(10) unsigned NOT NULL,
  `return_method` enum('pickup','courier') NOT NULL DEFAULT 'pickup',
  `return_address` text DEFAULT NULL,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `returned_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ir_item` (`found_item_id`),
  KEY `fk_ir_guest` (`guest_id`),
  CONSTRAINT `fk_ir_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ir_item` FOREIGN KEY (`found_item_id`) REFERENCES `found_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_returns`
--

LOCK TABLES `item_returns` WRITE;
/*!40000 ALTER TABLE `item_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lost_and_found`
--

DROP TABLE IF EXISTS `lost_and_found`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lost_and_found` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(10) unsigned DEFAULT NULL,
  `room_id` int(10) unsigned DEFAULT NULL,
  `found_by` int(10) unsigned DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('found','claimed','donated','discarded') NOT NULL DEFAULT 'found',
  `found_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_laf_guest` (`guest_id`),
  KEY `fk_laf_room` (`room_id`),
  KEY `fk_laf_staff` (`found_by`),
  CONSTRAINT `fk_laf_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_laf_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_laf_staff` FOREIGN KEY (`found_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lost_and_found`
--

LOCK TABLES `lost_and_found` WRITE;
/*!40000 ALTER TABLE `lost_and_found` DISABLE KEYS */;
INSERT INTO `lost_and_found` VALUES (1,2,3,5,'Black leather wallet found under bed after checkout. Contains credit cards and cash.','found','2026-05-01 21:45:43'),(2,1,6,6,'Gold wristwatch left on bathroom shelf after March stay. Guest notified.','claimed','2026-05-01 21:45:43'),(3,NULL,4,5,'Blue umbrella found in wardrobe. No guest could be identified.','donated','2026-05-01 21:45:43'),(4,7,4,6,'USB-C phone charger on bedside table after checkout.','found','2026-05-01 21:45:43'),(5,6,5,7,'Designer sunglasses case (Gucci) left in room safe.','found','2026-05-01 21:45:43'),(6,2,3,5,'Black leather wallet found under bed after checkout. Contains credit cards and cash.','found','2026-05-01 21:53:46'),(7,1,6,6,'Gold wristwatch left on bathroom shelf after March stay. Guest notified.','claimed','2026-05-01 21:53:46'),(8,NULL,4,5,'Blue umbrella found in wardrobe. No guest could be identified.','donated','2026-05-01 21:53:46'),(9,7,4,6,'USB-C phone charger on bedside table after checkout.','found','2026-05-01 21:53:46'),(10,6,5,7,'Designer sunglasses case (Gucci) left in room safe.','found','2026-05-01 21:53:46');
/*!40000 ALTER TABLE `lost_and_found` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lost_item_reports`
--

DROP TABLE IF EXISTS `lost_item_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lost_item_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `reservation_id` int(10) unsigned DEFAULT NULL,
  `lost_date` date DEFAULT NULL,
  `matched_found_item_id` int(10) unsigned DEFAULT NULL,
  `status` enum('open','matched','closed') NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_lir_guest` (`guest_id`),
  KEY `fk_lir_res` (`reservation_id`),
  KEY `fk_lir_fi` (`matched_found_item_id`),
  CONSTRAINT `fk_lir_fi` FOREIGN KEY (`matched_found_item_id`) REFERENCES `found_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lir_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lir_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lost_item_reports`
--

LOCK TABLES `lost_item_reports` WRITE;
/*!40000 ALTER TABLE `lost_item_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `lost_item_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `low_stock_alerts`
--

DROP TABLE IF EXISTS `low_stock_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `low_stock_alerts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `location` varchar(50) NOT NULL,
  `current_stock` int(11) NOT NULL,
  `min_threshold` int(11) NOT NULL,
  `status` enum('active','acknowledged','resolved') NOT NULL DEFAULT 'active',
  `acknowledged_by` int(10) unsigned DEFAULT NULL,
  `acknowledged_at` datetime DEFAULT NULL,
  `escalated` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_lsa_item` (`item_id`),
  KEY `fk_lsa_user` (`acknowledged_by`),
  CONSTRAINT `fk_lsa_item` FOREIGN KEY (`item_id`) REFERENCES `supply_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lsa_user` FOREIGN KEY (`acknowledged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `low_stock_alerts`
--

LOCK TABLES `low_stock_alerts` WRITE;
/*!40000 ALTER TABLE `low_stock_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `low_stock_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_orders`
--

DROP TABLE IF EXISTS `maintenance_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maintenance_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned NOT NULL,
  `reported_by` int(10) unsigned DEFAULT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','escalated') NOT NULL DEFAULT 'open',
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_maint_room` (`room_id`),
  KEY `fk_maint_reporter` (`reported_by`),
  KEY `fk_maint_assigned` (`assigned_to`),
  CONSTRAINT `fk_maint_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_maint_reporter` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_maint_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_orders`
--

LOCK TABLES `maintenance_orders` WRITE;
/*!40000 ALTER TABLE `maintenance_orders` DISABLE KEYS */;
INSERT INTO `maintenance_orders` VALUES (1,7,3,NULL,'AC unit not cooling and making grinding noise. Room taken out of service pending repair.','high','in_progress',NULL,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(2,4,5,NULL,'Bathroom basin tap dripping. Needs washer replacement.','low','open',NULL,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(3,2,3,NULL,'Guest reported TV remote unresponsive. Batteries replaced, issue resolved.','low','resolved','2026-04-26 10:00:00','2026-05-01 21:45:43','2026-05-01 21:45:43'),(4,6,4,NULL,'Suite balcony sliding door lock is stiff. Lubrication and adjustment required before May 1.','medium','open',NULL,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(5,3,6,NULL,'Bathroom ceiling light flickering. Likely loose fitting or blown bulb.','low','open',NULL,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(6,7,3,NULL,'AC unit not cooling and making grinding noise. Room taken out of service pending repair.','high','in_progress',NULL,'2026-05-01 21:53:46','2026-05-01 21:53:46'),(7,4,5,NULL,'Bathroom basin tap dripping. Needs washer replacement.','low','open',NULL,'2026-05-01 21:53:46','2026-05-01 21:53:46'),(8,2,3,NULL,'Guest reported TV remote unresponsive. Batteries replaced, issue resolved.','low','resolved','2026-04-26 10:00:00','2026-05-01 21:53:46','2026-05-01 21:53:46'),(9,6,4,NULL,'Suite balcony sliding door lock is stiff. Lubrication and adjustment required before May 1.','medium','open',NULL,'2026-05-01 21:53:46','2026-05-01 21:53:46'),(10,3,6,NULL,'Bathroom ceiling light flickering. Likely loose fitting or blown bulb.','low','open',NULL,'2026-05-01 21:53:46','2026-05-01 21:53:46');
/*!40000 ALTER TABLE `maintenance_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `minibar_inventory`
--

DROP TABLE IF EXISTS `minibar_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `minibar_inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `current_stock` int(10) unsigned NOT NULL DEFAULT 0,
  `last_restocked_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mi_room_item` (`room_id`,`item_id`),
  KEY `fk_mi_item` (`item_id`),
  CONSTRAINT `fk_mi_item` FOREIGN KEY (`item_id`) REFERENCES `minibar_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mi_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `minibar_inventory`
--

LOCK TABLES `minibar_inventory` WRITE;
/*!40000 ALTER TABLE `minibar_inventory` DISABLE KEYS */;
INSERT INTO `minibar_inventory` VALUES (1,2,1,4,'2026-05-02 00:45:44'),(2,2,2,2,'2026-05-02 00:45:44'),(3,2,3,3,'2026-05-02 00:45:44'),(4,2,4,4,'2026-05-02 00:45:44'),(5,2,5,2,'2026-05-02 00:45:44'),(6,2,6,2,'2026-05-02 00:45:44'),(7,2,7,3,'2026-05-02 00:45:44'),(8,2,8,2,'2026-05-02 00:45:44'),(9,2,9,1,'2026-05-02 00:45:44'),(10,2,10,2,'2026-05-02 00:45:44'),(11,4,1,4,'2026-05-02 00:45:44'),(12,4,2,2,'2026-05-02 00:45:44'),(13,4,3,3,'2026-05-02 00:45:44'),(14,4,4,4,'2026-05-02 00:45:44'),(15,4,5,2,'2026-05-02 00:45:44'),(16,4,6,2,'2026-05-02 00:45:44'),(17,4,7,3,'2026-05-02 00:45:44'),(18,4,8,2,'2026-05-02 00:45:44'),(19,4,9,1,'2026-05-02 00:45:44'),(20,4,10,2,'2026-05-02 00:45:44'),(21,6,1,6,'2026-05-02 00:45:44'),(22,6,2,4,'2026-05-02 00:45:44'),(23,6,3,4,'2026-05-02 00:45:44'),(24,6,4,6,'2026-05-02 00:45:44'),(25,6,5,4,'2026-05-02 00:45:44'),(26,6,6,3,'2026-05-02 00:45:44'),(27,6,7,4,'2026-05-02 00:45:44'),(28,6,8,3,'2026-05-02 00:45:44'),(29,6,9,2,'2026-05-02 00:45:44'),(30,6,10,4,'2026-05-02 00:45:44');
/*!40000 ALTER TABLE `minibar_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `minibar_items`
--

DROP TABLE IF EXISTS `minibar_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `minibar_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `reorder_threshold` int(10) unsigned NOT NULL DEFAULT 2,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `minibar_items`
--

LOCK TABLES `minibar_items` WRITE;
/*!40000 ALTER TABLE `minibar_items` DISABLE KEYS */;
INSERT INTO `minibar_items` VALUES (1,'Still Water 500ml','MB-WATER-500',3.00,3,1),(2,'Sparkling Water 500ml','MB-SPARK-500',3.50,3,1),(3,'Orange Juice 330ml','MB-OJ-330',4.50,2,1),(4,'Cola 330ml','MB-COLA-330',4.00,3,1),(5,'Beer (Local) 330ml','MB-BEER-330',8.00,2,1),(6,'Mixed Nuts 50g','MB-NUTS-050',6.00,2,1),(7,'Chocolate Bar','MB-CHOC-001',5.00,2,1),(8,'Chips 40g','MB-CHIP-040',4.50,2,1),(9,'Sparkling Wine 200ml','MB-WINE-200',18.00,1,1),(10,'Mineral Water 1L','MB-WATER-1L',5.00,2,1);
/*!40000 ALTER TABLE `minibar_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `minibar_logs`
--

DROP TABLE IF EXISTS `minibar_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `minibar_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned NOT NULL,
  `reservation_id` int(10) unsigned DEFAULT NULL,
  `housekeeper_id` int(10) unsigned DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `logged_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_ml_room` (`room_id`),
  KEY `fk_ml_res` (`reservation_id`),
  KEY `fk_ml_hk` (`housekeeper_id`),
  CONSTRAINT `fk_ml_hk` FOREIGN KEY (`housekeeper_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ml_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ml_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `minibar_logs`
--

LOCK TABLES `minibar_logs` WRITE;
/*!40000 ALTER TABLE `minibar_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `minibar_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_methods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(10) unsigned NOT NULL,
  `gateway_token` varchar(255) NOT NULL,
  `card_last4` char(4) NOT NULL,
  `card_brand` varchar(20) NOT NULL DEFAULT '',
  `expiry_month` tinyint(3) unsigned NOT NULL,
  `expiry_year` smallint(5) unsigned NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pm_guest` (`guest_id`),
  CONSTRAINT `fk_pm_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
INSERT INTO `payment_methods` VALUES (1,1,'tok_john_smith_4242','4242','visa',12,2027,1,'2026-05-01 21:45:44'),(2,2,'tok_emma_wilson_1234','1234','mastercard',6,2026,1,'2026-05-01 21:45:44'),(3,4,'tok_yuki_tanaka_5678','5678','visa',3,2028,1,'2026-05-01 21:45:44');
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_retry_queue`
--

DROP TABLE IF EXISTS `payment_retry_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_retry_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(10) unsigned NOT NULL,
  `reservation_id` int(10) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) NOT NULL DEFAULT '',
  `idempotency_key` varchar(255) NOT NULL,
  `attempt_count` int(10) unsigned NOT NULL DEFAULT 0,
  `next_retry_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_prq_idempotency` (`idempotency_key`),
  KEY `fk_prq_guest` (`guest_id`),
  KEY `fk_prq_res` (`reservation_id`),
  CONSTRAINT `fk_prq_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prq_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_retry_queue`
--

LOCK TABLES `payment_retry_queue` WRITE;
/*!40000 ALTER TABLE `payment_retry_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_retry_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folio_id` int(10) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('cash','credit_card','debit_card','bank_transfer','online') NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `processed_by` int(10) unsigned DEFAULT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pay_folio` (`folio_id`),
  KEY `fk_pay_staff` (`processed_by`),
  CONSTRAINT `fk_pay_folio` FOREIGN KEY (`folio_id`) REFERENCES `folios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pay_staff` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (40,2,2150.00,'credit_card',NULL,NULL,'2026-04-22 08:15:00'),(41,3,1500.00,'credit_card',NULL,NULL,'2026-04-23 12:30:00'),(42,3,1850.00,'credit_card',NULL,NULL,'2026-04-24 08:00:00'),(43,4,1200.00,'cash',NULL,NULL,'2026-04-25 06:45:00'),(44,4,1350.00,'credit_card',NULL,NULL,'2026-04-25 13:20:00'),(45,8,3000.00,'credit_card',NULL,NULL,'2026-04-26 07:00:00'),(46,8,2500.00,'credit_card',NULL,NULL,'2026-04-27 10:15:00'),(47,8,2350.00,'bank_transfer',NULL,NULL,'2026-04-28 06:30:00'),(48,6,2000.00,'bank_transfer',NULL,NULL,'2026-04-29 08:45:00'),(49,5,500.00,'online',NULL,NULL,'2026-04-30 11:00:00'),(50,9,500.00,'credit_card',NULL,NULL,'2026-05-01 06:00:00'),(51,1,500.00,'credit_card',NULL,NULL,'2026-05-01 07:30:00'),(52,6,3500.00,'credit_card',NULL,NULL,'2026-05-01 11:00:00'),(53,7,800.00,'cash',NULL,NULL,'2026-05-01 12:45:00'),(54,1,750.00,'credit_card',NULL,NULL,'2026-05-02 06:15:00'),(55,6,1200.00,'credit_card',NULL,NULL,'2026-05-02 08:30:00'),(56,11,500.00,'credit_card',NULL,NULL,'2026-05-02 01:07:51'),(57,12,400.00,'credit_card',NULL,NULL,'2026-05-02 01:07:51'),(58,13,300.00,'credit_card',NULL,NULL,'2026-05-02 01:07:51'),(59,14,300.00,'credit_card',NULL,NULL,'2026-05-02 01:07:51'),(60,15,250.00,'credit_card',NULL,NULL,'2026-05-02 01:07:51');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

-- Today's payments (ensures Revenue Today > $0 on fresh import)
INSERT IGNORE INTO `payments` (id, folio_id, amount, method, reference, processed_by, processed_at) VALUES
  (61, 1,  835.00, 'credit_card',  'REF-TODAY-001', 3, NOW()),
  (62, 7,  700.00, 'cash',         'REF-TODAY-002', 3, NOW()),
  (63, 17, 450.00, 'credit_card',  'REF-TODAY-003', 4, NOW());


--
-- Table structure for table `pending_debts`
--

DROP TABLE IF EXISTS `pending_debts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pending_debts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(10) unsigned NOT NULL,
  `reservation_id` int(10) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pd_guest` (`guest_id`),
  KEY `fk_pd_res` (`reservation_id`),
  CONSTRAINT `fk_pd_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pd_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pending_debts`
--

LOCK TABLES `pending_debts` WRITE;
/*!40000 ALTER TABLE `pending_debts` DISABLE KEYS */;
/*!40000 ALTER TABLE `pending_debts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preventative_schedules`
--

DROP TABLE IF EXISTS `preventative_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preventative_schedules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` int(10) unsigned NOT NULL,
  `asset_id` int(10) unsigned DEFAULT NULL,
  `room_id` int(10) unsigned DEFAULT NULL,
  `maintenance_type` enum('hvac','elevator','plumbing','electrical','deep_cleaning','other') NOT NULL,
  `scheduled_date` date NOT NULL,
  `estimated_minutes` int(10) unsigned NOT NULL DEFAULT 60,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `recurrence_frequency` enum('weekly','monthly','quarterly','yearly') DEFAULT NULL,
  `next_due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ps_wo` (`work_order_id`),
  KEY `fk_ps_asset` (`asset_id`),
  KEY `fk_ps_room` (`room_id`),
  CONSTRAINT `fk_ps_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ps_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ps_wo` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preventative_schedules`
--

LOCK TABLES `preventative_schedules` WRITE;
/*!40000 ALTER TABLE `preventative_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `preventative_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_wide_alerts`
--

DROP TABLE IF EXISTS `property_wide_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `property_wide_alerts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alert_type` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `triggered_by_work_order_id` int(10) unsigned DEFAULT NULL,
  `status` enum('active','resolved') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pwa_wo` (`triggered_by_work_order_id`),
  CONSTRAINT `fk_pwa_wo` FOREIGN KEY (`triggered_by_work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_wide_alerts`
--

LOCK TABLES `property_wide_alerts` WRITE;
/*!40000 ALTER TABLE `property_wide_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_wide_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qa_inspections`
--

DROP TABLE IF EXISTS `qa_inspections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qa_inspections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned NOT NULL,
  `inspector_id` int(10) unsigned NOT NULL,
  `inspection_date` date NOT NULL,
  `overall_result` enum('pass','fail','corrective_action') NOT NULL,
  `checklist_scores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`checklist_scores`)),
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_qa_room` (`room_id`),
  KEY `fk_qa_inspector` (`inspector_id`),
  CONSTRAINT `fk_qa_inspector` FOREIGN KEY (`inspector_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qa_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qa_inspections`
--

LOCK TABLES `qa_inspections` WRITE;
/*!40000 ALTER TABLE `qa_inspections` DISABLE KEYS */;
/*!40000 ALTER TABLE `qa_inspections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quality_scores`
--

DROP TABLE IF EXISTS `quality_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quality_scores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` int(10) unsigned NOT NULL,
  `housekeeper_id` int(10) unsigned NOT NULL,
  `room_id` int(10) unsigned NOT NULL,
  `cleanliness` tinyint(3) unsigned NOT NULL CHECK (`cleanliness` between 0 and 100),
  `presentation` tinyint(3) unsigned NOT NULL CHECK (`presentation` between 0 and 100),
  `completeness` tinyint(3) unsigned NOT NULL CHECK (`completeness` between 0 and 100),
  `speed` tinyint(3) unsigned NOT NULL CHECK (`speed` between 0 and 100),
  `overall_score` decimal(5,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `photo_urls` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photo_urls`)),
  `submitted_by_user_id` int(10) unsigned DEFAULT NULL,
  `is_disputed` tinyint(1) NOT NULL DEFAULT 0,
  `dispute_resolution` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_qs_inspection` (`inspection_id`),
  KEY `fk_qs_housekeeper` (`housekeeper_id`),
  KEY `fk_qs_room` (`room_id`),
  KEY `fk_qs_submitter` (`submitted_by_user_id`),
  CONSTRAINT `fk_qs_housekeeper` FOREIGN KEY (`housekeeper_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qs_inspection` FOREIGN KEY (`inspection_id`) REFERENCES `qa_inspections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qs_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qs_submitter` FOREIGN KEY (`submitted_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quality_scores`
--

LOCK TABLES `quality_scores` WRITE;
/*!40000 ALTER TABLE `quality_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `quality_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `replacement_review_flags`
--

DROP TABLE IF EXISTS `replacement_review_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `replacement_review_flags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned DEFAULT NULL,
  `asset_id` int(10) unsigned DEFAULT NULL,
  `emergency_count` int(10) unsigned NOT NULL DEFAULT 0,
  `flagged_at` datetime NOT NULL DEFAULT current_timestamp(),
  `reviewed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_rrf_room` (`room_id`),
  KEY `fk_rrf_asset` (`asset_id`),
  CONSTRAINT `fk_rrf_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_rrf_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `replacement_review_flags`
--

LOCK TABLES `replacement_review_flags` WRITE;
/*!40000 ALTER TABLE `replacement_review_flags` DISABLE KEYS */;
/*!40000 ALTER TABLE `replacement_review_flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(10) unsigned NOT NULL,
  `room_id` int(10) unsigned NOT NULL,
  `assigned_by` int(10) unsigned DEFAULT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `actual_check_in` datetime DEFAULT NULL,
  `actual_check_out` datetime DEFAULT NULL,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled','no_show') NOT NULL DEFAULT 'pending',
  `adults` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `children` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `special_requests` text DEFAULT NULL,
  `deposit_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deposit_paid` tinyint(1) NOT NULL DEFAULT 0,
  `is_group` tinyint(1) NOT NULL DEFAULT 0,
  `group_id` int(10) unsigned DEFAULT NULL,
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_res_room` (`room_id`),
  KEY `fk_res_staff` (`assigned_by`),
  KEY `idx_res_dates` (`check_in_date`,`check_out_date`),
  KEY `idx_res_status` (`status`),
  KEY `idx_res_guest` (`guest_id`),
  CONSTRAINT `fk_res_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`),
  CONSTRAINT `fk_res_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  CONSTRAINT `fk_res_staff` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,1,2,3,'2026-04-25','2026-04-29','2026-04-25 14:00:00','2026-04-29 11:00:00','checked_out',2,0,'Extra pillows, high floor preference',500.00,1,0,NULL,2000.00,'2026-05-01 21:45:43','2026-05-02 01:16:35'),(2,2,3,4,'2026-04-22','2026-04-26','2026-04-22 15:10:00','2026-04-26 11:45:00','checked_out',1,0,NULL,500.00,1,0,NULL,2000.00,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(3,7,4,3,'2026-04-23','2026-04-27','2026-04-23 13:00:00','2026-04-27 10:30:00','checked_out',2,1,'Baby cot needed, vegan breakfast',800.00,1,0,NULL,3200.00,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(4,6,5,4,'2026-04-24','2026-04-27','2026-04-24 16:00:00','2026-04-27 12:30:00','checked_out',1,0,'Late check-out requested',800.00,1,0,NULL,2400.00,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(5,3,1,3,'2026-04-28','2026-05-02',NULL,NULL,'confirmed',2,0,'Vegetarian welcome plate',500.00,1,0,NULL,2000.00,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(6,4,6,4,'2026-05-03','2026-05-07','2026-05-01 14:00:00',NULL,'confirmed',2,0,'VIP guest - platinum member',2000.00,1,1,1,9000.00,'2026-05-01 21:45:43','2026-05-03 01:02:31'),(7,5,8,NULL,'2026-05-03','2026-05-06',NULL,NULL,'confirmed',1,0,'Halal dining options, prayer mat in room',0.00,0,0,NULL,1000.00,'2026-05-01 21:45:43','2026-05-03 01:02:31'),(8,8,1,3,'2026-05-03','2026-05-05',NULL,NULL,'cancelled',2,0,'VIP guest, late arrival',500.00,0,0,NULL,1500.00,'2026-05-01 21:45:43','2026-05-03 01:02:04'),(9,1,6,3,'2026-03-10','2026-03-15','2026-03-10 14:00:00','2026-03-15 12:00:00','checked_out',2,0,'Anniversary celebration ΓÇô roses and champagne in room',2000.00,1,0,NULL,7500.00,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(10,4,8,4,'2026-02-20','2026-02-22',NULL,NULL,'no_show',2,0,NULL,500.00,1,0,NULL,1000.00,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(21,2,3,1,'2026-05-03','2026-05-08',NULL,NULL,'confirmed',2,0,'VIP - anniversary stay',500.00,1,0,NULL,1800.00,'2026-05-02 01:07:14','2026-05-03 01:02:31'),(22,5,8,1,'2026-05-02','2026-05-06',NULL,NULL,'confirmed',1,0,'VIP guest ├╗ Halal meals, prayer mat, late checkout requested',400.00,1,0,NULL,1400.00,'2026-05-02 01:07:14','2026-05-02 01:07:14'),(23,8,6,1,'2026-05-03','2026-05-06',NULL,NULL,'confirmed',2,1,'Extra bed for child, airport pickup requested',300.00,1,0,NULL,1200.00,'2026-05-02 01:07:14','2026-05-03 01:02:31'),(24,1,2,1,'2026-04-29','2026-05-03','2026-04-28 14:00:00',NULL,'checked_in',2,0,'Extra pillows, high floor preference',300.00,1,0,NULL,1600.00,'2026-05-02 01:07:14','2026-05-03 01:02:04'),(25,7,4,1,'2026-04-30','2026-05-03','2026-04-29 15:30:00',NULL,'checked_in',1,0,'Late checkout ├╗ conference attendee',250.00,1,0,NULL,1200.00,'2026-05-02 01:07:14','2026-05-03 01:02:04'),(26,2,1,NULL,'2026-05-05','2026-05-06',NULL,NULL,'pending',1,0,'',0.00,0,1,26,500.00,'2026-05-03 00:09:52','2026-05-03 00:09:52'),(27,2,1,NULL,'2026-05-05','2026-05-20','2026-05-03 03:29:41',NULL,'checked_in',1,0,'',1500.00,1,0,NULL,7500.00,'2026-05-03 00:19:22','2026-05-03 00:29:41'),(28,1,20,NULL,'2026-05-14','2026-05-23','2026-05-03 03:51:29',NULL,'checked_in',1,0,'',900.00,1,0,NULL,4500.00,'2026-05-03 00:50:01','2026-05-03 00:52:07'),(29,1,21,NULL,'2026-05-06','2026-05-29',NULL,NULL,'pending',1,0,'',0.00,0,0,NULL,34500.00,'2026-05-03 00:52:50','2026-05-03 00:52:50');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restocking_requisitions`
--

DROP TABLE IF EXISTS `restocking_requisitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restocking_requisitions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `requested_by_user_id` int(10) unsigned DEFAULT NULL,
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_rr_user` (`requested_by_user_id`),
  CONSTRAINT `fk_rr_user` FOREIGN KEY (`requested_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restocking_requisitions`
--

LOCK TABLES `restocking_requisitions` WRITE;
/*!40000 ALTER TABLE `restocking_requisitions` DISABLE KEYS */;
/*!40000 ALTER TABLE `restocking_requisitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'manager','2026-05-01 21:45:43'),(2,'front_desk','2026-05-01 21:45:43'),(3,'housekeeper','2026-05-01 21:45:43'),(4,'guest','2026-05-01 21:45:43'),(5,'revenue_manager','2026-05-01 21:45:44');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_types`
--

DROP TABLE IF EXISTS `room_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `capacity` tinyint(3) unsigned NOT NULL DEFAULT 2,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_types`
--

LOCK TABLES `room_types` WRITE;
/*!40000 ALTER TABLE `room_types` DISABLE KEYS */;
INSERT INTO `room_types` VALUES (1,'Standard','Comfortable room with essential amenities for a pleasant stay.',500.00,2),(2,'Deluxe','Spacious room with premium amenities and city view.',800.00,3),(3,'Suite','Luxury suite with separate living area, premium furnishings and minibar.',1500.00,4);
/*!40000 ALTER TABLE `room_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_type_id` int(10) unsigned NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `floor` tinyint(3) unsigned NOT NULL,
  `status` enum('available','occupied','dirty','cleaning','inspecting','out_of_order') NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_number` (`room_number`),
  KEY `fk_rooms_type` (`room_type_id`),
  CONSTRAINT `fk_rooms_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,1,'101',1,'occupied','Ground floor, near lobby','2026-05-01 21:45:43','2026-05-03 00:29:41'),(2,1,'102',1,'occupied','Guest currently checked in','2026-05-01 21:45:43','2026-05-01 21:45:43'),(3,2,'201',2,'occupied','Checkout completed, awaiting housekeeping','2026-05-01 21:45:43','2026-05-02 01:07:21'),(4,2,'202',2,'dirty','Housekeeping in progress','2026-05-01 21:45:43','2026-05-02 01:07:21'),(5,2,'203',2,'inspecting','Cleaning done, supervisor review pending','2026-05-01 21:45:43','2026-05-01 21:45:43'),(6,3,'301',3,'occupied','Premium corner suite','2026-05-01 21:45:43','2026-05-02 01:07:21'),(7,3,'302',3,'out_of_order','AC unit under repair','2026-05-01 21:45:43','2026-05-01 21:45:43'),(8,1,'103',1,'occupied','Near elevator','2026-05-01 21:45:43','2026-05-02 01:07:21'),(10,1,'104',1,'available','Standard room with garden view','2026-05-02 22:55:52','2026-05-03 00:51:45'),(11,1,'105',1,'available','Standard room with garden view','2026-05-02 22:55:52','2026-05-02 22:55:52'),(12,1,'106',1,'available','Standard room with city view','2026-05-02 22:55:52','2026-05-02 22:55:52'),(13,1,'107',1,'available','Standard room with city view','2026-05-02 22:55:52','2026-05-02 22:55:52'),(14,1,'108',1,'cleaning','Standard room - housekeeping in progress','2026-05-02 22:55:52','2026-05-02 22:55:52'),(15,2,'204',2,'available','Deluxe room with balcony','2026-05-02 22:55:52','2026-05-03 00:52:07'),(16,2,'205',2,'available','Deluxe room with balcony','2026-05-02 22:55:52','2026-05-02 22:55:52'),(17,2,'206',2,'available','Deluxe room with pool view','2026-05-02 22:55:52','2026-05-02 22:55:52'),(18,2,'207',2,'available','Deluxe room with pool view','2026-05-02 22:55:52','2026-05-02 22:55:52'),(19,2,'208',2,'available','Deluxe corner room','2026-05-02 22:55:52','2026-05-02 22:55:52'),(20,3,'303',3,'occupied','Junior Suite with lounge area','2026-05-02 22:55:52','2026-05-03 00:52:07'),(21,3,'304',3,'available','Junior Suite with lounge area','2026-05-02 22:55:52','2026-05-02 22:55:52');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_alerts`
--

DROP TABLE IF EXISTS `security_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_alerts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alert_type` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `related_found_item_id` int(10) unsigned DEFAULT NULL,
  `priority` enum('normal','urgent') NOT NULL DEFAULT 'normal',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sa_item` (`related_found_item_id`),
  CONSTRAINT `fk_sa_item` FOREIGN KEY (`related_found_item_id`) REFERENCES `found_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_alerts`
--

LOCK TABLES `security_alerts` WRITE;
/*!40000 ALTER TABLE `security_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_bookings`
--

DROP TABLE IF EXISTS `service_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_bookings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(10) unsigned NOT NULL,
  `service_id` int(10) unsigned NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_service_booking_guest` (`guest_id`),
  KEY `fk_service_booking_service` (`service_id`),
  CONSTRAINT `fk_service_booking_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_service_booking_service` FOREIGN KEY (`service_id`) REFERENCES `external_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_bookings`
--

LOCK TABLES `service_bookings` WRITE;
/*!40000 ALTER TABLE `service_bookings` DISABLE KEYS */;
INSERT INTO `service_bookings` VALUES (1,1,1,'2026-04-28','10:00:00','confirmed','2026-05-01 21:45:43'),(2,1,3,'2026-04-29','12:00:00','confirmed','2026-05-01 21:45:43'),(3,4,1,'2026-05-03','11:00:00','confirmed','2026-05-01 21:45:43'),(4,4,2,'2026-05-02','19:30:00','confirmed','2026-05-01 21:45:43'),(5,5,3,'2026-05-10','08:00:00','pending','2026-05-01 21:45:43'),(6,2,4,'2026-04-23','14:00:00','confirmed','2026-05-01 21:45:43'),(7,3,5,'2026-04-29','09:00:00','pending','2026-05-01 21:45:43'),(8,6,2,'2026-04-25','20:00:00','confirmed','2026-05-01 21:45:43'),(9,1,1,'2026-04-28','10:00:00','confirmed','2026-05-01 21:53:46'),(10,1,3,'2026-04-29','12:00:00','confirmed','2026-05-01 21:53:46'),(11,4,1,'2026-05-03','11:00:00','confirmed','2026-05-01 21:53:46'),(12,4,2,'2026-05-02','19:30:00','confirmed','2026-05-01 21:53:46'),(13,5,3,'2026-05-10','08:00:00','pending','2026-05-01 21:53:46'),(14,2,4,'2026-04-23','14:00:00','confirmed','2026-05-01 21:53:46'),(15,3,5,'2026-04-29','09:00:00','pending','2026-05-01 21:53:46'),(16,6,2,'2026-04-25','20:00:00','confirmed','2026-05-01 21:53:46');
/*!40000 ALTER TABLE `service_bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supply_inventory`
--

DROP TABLE IF EXISTS `supply_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supply_inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `location` enum('housekeeping_store','floor1','floor2','floor3','laundry','kitchen','general') NOT NULL DEFAULT 'general',
  `current_stock` int(10) unsigned NOT NULL DEFAULT 0,
  `last_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_si_item_loc` (`item_id`,`location`),
  CONSTRAINT `fk_si_item` FOREIGN KEY (`item_id`) REFERENCES `supply_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supply_inventory`
--

LOCK TABLES `supply_inventory` WRITE;
/*!40000 ALTER TABLE `supply_inventory` DISABLE KEYS */;
INSERT INTO `supply_inventory` VALUES (1,1,'housekeeping_store',45,'2026-05-02 00:45:45'),(2,2,'housekeeping_store',60,'2026-05-02 00:45:45'),(3,3,'housekeeping_store',18,'2026-05-02 00:45:45'),(4,4,'housekeeping_store',22,'2026-05-02 00:45:45'),(5,5,'housekeeping_store',80,'2026-05-02 00:45:45'),(6,6,'housekeeping_store',75,'2026-05-02 00:45:45'),(7,7,'housekeeping_store',120,'2026-05-02 00:45:45'),(8,8,'housekeeping_store',95,'2026-05-02 00:45:45'),(9,9,'housekeeping_store',12,'2026-05-02 00:45:45'),(10,10,'housekeeping_store',3,'2026-05-02 00:45:45');
/*!40000 ALTER TABLE `supply_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supply_items`
--

DROP TABLE IF EXISTS `supply_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supply_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `category` enum('cleaning','linen','amenity','minibar','other') NOT NULL DEFAULT 'other',
  `min_threshold` int(10) unsigned NOT NULL DEFAULT 5,
  `unit` varchar(30) NOT NULL DEFAULT 'units',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supply_items`
--

LOCK TABLES `supply_items` WRITE;
/*!40000 ALTER TABLE `supply_items` DISABLE KEYS */;
INSERT INTO `supply_items` VALUES (1,'Bath Towels','linen',20,'pieces',1),(2,'Hand Towels','linen',30,'pieces',1),(3,'Bed Sheets (King)','linen',10,'sets',1),(4,'Bed Sheets (Queen)','linen',15,'sets',1),(5,'Shampoo Bottles','amenity',50,'bottles',1),(6,'Conditioner Bottles','amenity',50,'bottles',1),(7,'Soap Bars','amenity',100,'bars',1),(8,'Toilet Rolls','amenity',100,'rolls',1),(9,'All-Purpose Cleaner','cleaning',10,'litres',1),(10,'Disinfectant Spray','cleaning',15,'cans',1);
/*!40000 ALTER TABLE `supply_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(10) unsigned NOT NULL,
  `reservation_id` int(10) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('charge','refund','preauth') NOT NULL DEFAULT 'charge',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `gateway_ref` varchar(100) DEFAULT NULL,
  `status` enum('success','failed','pending') NOT NULL DEFAULT 'pending',
  `idempotency_key` varchar(255) NOT NULL,
  `failure_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tx_idempotency` (`idempotency_key`),
  KEY `fk_tx_guest` (`guest_id`),
  KEY `fk_tx_res` (`reservation_id`),
  CONSTRAINT `fk_tx_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tx_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_role` (`role_id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Ahmed Hassan','ahmed.hassan@grandhotel.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2021-03-15 07:00:00','2026-05-02 00:18:35'),(2,1,'Sara Mohamed','sara.mohamed@grandhotel.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2022-07-01 07:30:00','2026-05-02 00:18:35'),(3,2,'Omar Ali','omar.ali@grandhotel.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2022-11-20 06:45:00','2026-05-02 00:18:35'),(4,2,'Nour Ibrahim','nour.ibrahim@grandhotel.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2023-04-10 07:15:00','2026-05-02 00:18:35'),(5,3,'Fatma Khaled','fatma.khaled@grandhotel.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2023-01-08 09:00:00','2026-05-02 00:18:35'),(6,3,'Mohamed Samir','mohamed.samir@grandhotel.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2023-06-25 05:30:00','2026-05-02 00:18:35'),(7,3,'Layla Ahmed','layla.ahmed@grandhotel.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2024-02-14 07:45:00','2026-05-02 00:18:35'),(8,4,'John Smith','john.smith@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(9,4,'Emma Wilson','emma.wilson@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(10,4,'Carlos Rodriguez','carlos.rodriguez@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(11,4,'Yuki Tanaka','yuki.tanaka@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(12,4,'Aisha Al-Rashid','aisha.alrashid@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(13,4,'Pierre Dubois','pierre.dubois@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(14,4,'Priya Sharma','priya.sharma@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(15,4,'David Chen','david.chen@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-05-01 21:45:43','2026-05-01 21:45:43'),(17,5,'Karim Nasser','karim.nasser@grandhotel.com','3fe20d68a85f0ca590301eb12d95603bc1bc3bc42907d22503fe06bc03000782',1,'2023-09-03 07:00:00','2026-05-02 00:18:35'),(18,5,'Lina Youssef','lina.youssef@grandhotel.com','3fe20d68a85f0ca590301eb12d95603bc1bc3bc42907d22503fe06bc03000782',1,'2024-05-20 06:30:00','2026-05-02 00:18:35');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `virtual_inventory`
--

DROP TABLE IF EXISTS `virtual_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `virtual_inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_type_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `physical_rooms` int(10) unsigned NOT NULL DEFAULT 0,
  `virtual_max` int(10) unsigned NOT NULL DEFAULT 0,
  `confirmed_count` int(10) unsigned NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by_user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_vi_type_date` (`room_type_id`,`date`),
  KEY `fk_vi_user` (`updated_by_user_id`),
  CONSTRAINT `fk_vi_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vi_user` FOREIGN KEY (`updated_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `virtual_inventory`
--

LOCK TABLES `virtual_inventory` WRITE;
/*!40000 ALTER TABLE `virtual_inventory` DISABLE KEYS */;
INSERT INTO `virtual_inventory` VALUES (1,1,'2026-05-02',3,3,1,'2026-05-01 21:45:44',1),(2,1,'2026-05-03',3,3,2,'2026-05-01 21:45:44',1),(3,1,'2026-05-04',3,2,2,'2026-05-01 21:45:44',1),(4,2,'2026-05-02',3,3,0,'2026-05-01 21:45:44',1),(5,2,'2026-05-03',3,3,1,'2026-05-01 21:45:44',1),(6,3,'2026-05-02',2,2,1,'2026-05-01 21:45:44',1),(7,3,'2026-05-03',2,2,2,'2026-05-01 21:45:44',1);
/*!40000 ALTER TABLE `virtual_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_logs`
--

DROP TABLE IF EXISTS `work_order_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `work_order_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` int(10) unsigned NOT NULL,
  `action` varchar(100) NOT NULL,
  `performed_by_user_id` int(10) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_wol_wo` (`work_order_id`),
  KEY `fk_wol_user` (`performed_by_user_id`),
  CONSTRAINT `fk_wol_user` FOREIGN KEY (`performed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_wol_wo` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_logs`
--

LOCK TABLES `work_order_logs` WRITE;
/*!40000 ALTER TABLE `work_order_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_order_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_orders`
--

DROP TABLE IF EXISTS `work_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `work_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('emergency','preventative') NOT NULL,
  `room_id` int(10) unsigned DEFAULT NULL,
  `asset_id` int(10) unsigned DEFAULT NULL,
  `description` text NOT NULL,
  `priority` enum('low','normal','high','emergency') NOT NULL DEFAULT 'normal',
  `status` enum('open','in_progress','pending_parts','completed','closed','rejected') NOT NULL DEFAULT 'open',
  `assigned_to_user_id` int(10) unsigned DEFAULT NULL,
  `created_by_user_id` int(10) unsigned DEFAULT NULL,
  `work_performed` text DEFAULT NULL,
  `parts_used` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parts_used`)),
  `time_spent_minutes` int(10) unsigned DEFAULT NULL,
  `supervisor_id` int(10) unsigned DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_wo_room` (`room_id`),
  KEY `fk_wo_asset` (`asset_id`),
  KEY `fk_wo_assigned` (`assigned_to_user_id`),
  KEY `fk_wo_created` (`created_by_user_id`),
  KEY `fk_wo_supervisor` (`supervisor_id`),
  CONSTRAINT `fk_wo_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_wo_assigned` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_wo_created` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_wo_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_wo_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_orders`
--

LOCK TABLES `work_orders` WRITE;
/*!40000 ALTER TABLE `work_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'hotel_management'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


-- Dump completed on 2026-05-03  4:03:26

-- ============================================================
--  UC12 — CHARGE GUEST CARD
--  Tables: payment_methods, transactions, pending_debts,
--          payment_retry_queue
-- ============================================================

-- ── payment_methods ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `guest_id`      INT UNSIGNED     NOT NULL,
  `gateway_token` VARCHAR(255)     NOT NULL COMMENT 'NEVER raw card number — tokenised by gateway',
  `card_last4`    CHAR(4)          NOT NULL DEFAULT '0000',
  `card_brand`    VARCHAR(30)      NOT NULL DEFAULT 'unknown',
  `expiry_month`  TINYINT UNSIGNED NOT NULL,
  `expiry_year`   SMALLINT UNSIGNED NOT NULL,
  `is_default`    TINYINT(1)       NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pm_guest` (`guest_id`),
  CONSTRAINT `fk_pm_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── transactions ─────────────────────────────────────────────
-- ALL charges create a row here regardless of outcome (CRITICAL RULE).
CREATE TABLE IF NOT EXISTS `transactions` (
  `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `guest_id`        INT UNSIGNED     NOT NULL,
  `reservation_id`  INT UNSIGNED         NULL DEFAULT NULL,
  `amount`          DECIMAL(10,2)    NOT NULL,
  `type`            ENUM('charge','refund','preauth') NOT NULL DEFAULT 'charge',
  `reason`          VARCHAR(255)     NOT NULL DEFAULT '',
  `gateway_ref`     VARCHAR(100)         NULL DEFAULT NULL,
  `status`          ENUM('success','failed','pending') NOT NULL DEFAULT 'pending',
  `idempotency_key` VARCHAR(255)     NOT NULL,
  `failure_reason`  TEXT                 NULL DEFAULT NULL,
  `created_at`      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tx_idempotency` (`idempotency_key`),
  KEY `idx_tx_guest`       (`guest_id`),
  KEY `idx_tx_reservation` (`reservation_id`),
  CONSTRAINT `fk_tx_guest`       FOREIGN KEY (`guest_id`)       REFERENCES `guests`       (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tx_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── pending_debts ─────────────────────────────────────────────
-- Created when card is absent or declined.
CREATE TABLE IF NOT EXISTS `pending_debts` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `guest_id`       INT UNSIGNED  NOT NULL,
  `reservation_id` INT UNSIGNED      NULL DEFAULT NULL,
  `amount`         DECIMAL(10,2) NOT NULL,
  `reason`         TEXT          NOT NULL,
  `created_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at`    DATETIME          NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pd_guest`       (`guest_id`),
  KEY `idx_pd_reservation` (`reservation_id`),
  CONSTRAINT `fk_pd_guest`       FOREIGN KEY (`guest_id`)       REFERENCES `guests`       (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pd_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── payment_retry_queue ───────────────────────────────────────
-- Created on network error; idempotency_key prevents duplicates.
CREATE TABLE IF NOT EXISTS `payment_retry_queue` (
  `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `guest_id`        INT UNSIGNED  NOT NULL,
  `reservation_id`  INT UNSIGNED      NULL DEFAULT NULL,
  `amount`          DECIMAL(10,2) NOT NULL,
  `reason`          VARCHAR(255)  NOT NULL DEFAULT '',
  `idempotency_key` VARCHAR(255)  NOT NULL,
  `attempt_count`   INT UNSIGNED  NOT NULL DEFAULT 0,
  `next_retry_at`   DATETIME      NOT NULL,
  `created_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_prq_idempotency` (`idempotency_key`),
  KEY `idx_prq_guest`       (`guest_id`),
  KEY `idx_prq_reservation` (`reservation_id`),
  CONSTRAINT `fk_prq_guest`       FOREIGN KEY (`guest_id`)       REFERENCES `guests`       (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prq_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed: test payment methods (tokens simulated) ─────────────
-- Token suffix '0000' → declined, '9999' → network error, others → success
INSERT IGNORE INTO `payment_methods`
    (guest_id, gateway_token, card_last4, card_brand, expiry_month, expiry_year, is_default)
VALUES
    (1, 'tok_visa_1234abcd', '4242', 'Visa',       12, 2027, 1),
    (2, 'tok_mc_5678efgh',   '5555', 'Mastercard', 6,  2026, 1),
    (4, 'tok_amex_9012ijkl', '3782', 'Amex',       3,  2025, 1);
-- Note: guest 4's card expires 2025 so isCardExpired() returns true → tests CARD_EXPIRED path.

-- ============================================================
--  UC13 — SPLIT GROUP BILLING
--  Tables: invoice_items, billing_split_log,
--          billing_disputes, front_desk_queue
-- ============================================================

-- ── invoice_items ─────────────────────────────────────────────
-- Per-charge line items attached to individual split invoices.
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `invoice_id`     INT UNSIGNED  NOT NULL,
  `description`    VARCHAR(255)  NOT NULL DEFAULT '',
  `amount`         DECIMAL(10,2) NOT NULL,
  `item_type`      ENUM('room_rate','service','minibar','tax','other') NOT NULL DEFAULT 'other',
  `reservation_id` INT UNSIGNED      NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ii_invoice`     (`invoice_id`),
  KEY `idx_ii_reservation` (`reservation_id`),
  CONSTRAINT `fk_ii_invoice`     FOREIGN KEY (`invoice_id`)     REFERENCES `invoices`     (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ii_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── billing_split_log ──────────────────────────────────────────
-- Audit log: one row per split execution (UC13 Step 3f).
CREATE TABLE IF NOT EXISTS `billing_split_log` (
  `id`                          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `group_id`                    INT UNSIGNED   NOT NULL,
  `split_by_user_id`            INT UNSIGNED       NULL DEFAULT NULL,
  `members_split`               JSON           NOT NULL COMMENT 'JSON array of reservation_ids that were split',
  `original_consolidated_total` DECIMAL(12,2)  NOT NULL DEFAULT 0.00,
  `created_at`                  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bsl_group` (`group_id`),
  CONSTRAINT `fk_bsl_group` FOREIGN KEY (`group_id`) REFERENCES `group_reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bsl_user` FOREIGN KEY (`split_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── billing_disputes ───────────────────────────────────────────
-- Open dispute → split is PAUSED until coordinator resolves (UC13 error flow).
CREATE TABLE IF NOT EXISTS `billing_disputes` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id`          INT UNSIGNED NOT NULL,
  `reservation_id`    INT UNSIGNED NOT NULL,
  `raised_by_user_id` INT UNSIGNED     NULL DEFAULT NULL,
  `description`       TEXT         NOT NULL,
  `status`            ENUM('open','resolved') NOT NULL DEFAULT 'open',
  `resolved_at`       DATETIME         NULL DEFAULT NULL,
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bd_group`       (`group_id`),
  KEY `idx_bd_reservation` (`reservation_id`),
  CONSTRAINT `fk_bd_group`       FOREIGN KEY (`group_id`)          REFERENCES `group_reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bd_reservation` FOREIGN KEY (`reservation_id`)    REFERENCES `reservations`       (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bd_user`        FOREIGN KEY (`raised_by_user_id`) REFERENCES `users`              (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── front_desk_queue ───────────────────────────────────────────
-- Flagged invoices for manual delivery (member has no email — UC13 error flow).
CREATE TABLE IF NOT EXISTS `front_desk_queue` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `invoice_id`     INT UNSIGNED  NOT NULL,
  `reservation_id` INT UNSIGNED      NULL DEFAULT NULL,
  `reason`         VARCHAR(100)  NOT NULL DEFAULT 'no_email_manual_delivery',
  `guest_name`     VARCHAR(255)  NOT NULL DEFAULT '',
  `handled`        TINYINT(1)    NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fdq_invoice`     (`invoice_id`),
  KEY `idx_fdq_reservation` (`reservation_id`),
  CONSTRAINT `fk_fdq_invoice`     FOREIGN KEY (`invoice_id`)     REFERENCES `invoices`     (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fdq_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  UC29 — LOG MINIBAR CONSUMPTION
--  Tables: minibar_items, minibar_inventory, minibar_logs,
--          billing_items, billing_retry_queue
-- ============================================================

-- ── minibar_items ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `minibar_items` (
  `id`                INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(120)   NOT NULL,
  `sku`               VARCHAR(60)    NOT NULL DEFAULT '',
  `price`             DECIMAL(8,2)   NOT NULL DEFAULT 0.00,
  `reorder_threshold` INT UNSIGNED   NOT NULL DEFAULT 2,
  `is_active`         TINYINT(1)     NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mi_sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── minibar_inventory ─────────────────────────────────────────
-- Per-room stock levels. One row per (room, item) pair.
CREATE TABLE IF NOT EXISTS `minibar_inventory` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_id`          INT UNSIGNED NOT NULL,
  `item_id`          INT UNSIGNED NOT NULL,
  `current_stock`    INT UNSIGNED NOT NULL DEFAULT 0,
  `last_restocked_at`DATETIME         NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_miv_room_item` (`room_id`, `item_id`),
  KEY `idx_miv_room` (`room_id`),
  KEY `idx_miv_item` (`item_id`),
  CONSTRAINT `fk_miv_room` FOREIGN KEY (`room_id`) REFERENCES `rooms`        (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_miv_item` FOREIGN KEY (`item_id`) REFERENCES `minibar_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── minibar_logs ──────────────────────────────────────────────
-- Evidence table: housekeeper_id + logged_at used to resolve checkout disputes.
-- Roles with read access: housekeeper (own), manager, front_desk (dispute resolution).
CREATE TABLE IF NOT EXISTS `minibar_logs` (
  `id`             INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `room_id`        INT UNSIGNED   NOT NULL,
  `reservation_id` INT UNSIGNED       NULL DEFAULT NULL,
  `housekeeper_id` INT UNSIGNED   NOT NULL COMMENT 'User who logged consumption — evidence for disputes',
  `items`          JSON           NOT NULL COMMENT 'Array of {item_id,name,quantity,unit_price,line_total}',
  `total_amount`   DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  `logged_at`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ml_room`        (`room_id`),
  KEY `idx_ml_reservation` (`reservation_id`),
  KEY `idx_ml_housekeeper` (`housekeeper_id`),
  CONSTRAINT `fk_ml_room`        FOREIGN KEY (`room_id`)        REFERENCES `rooms`        (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ml_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ml_housekeeper` FOREIGN KEY (`housekeeper_id`) REFERENCES `users`        (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── billing_items ─────────────────────────────────────────────
-- Central charge ledger: charges of type 'minibar','service','room_rate','manual', etc.
CREATE TABLE IF NOT EXISTS `billing_items` (
  `id`               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `reservation_id`   INT UNSIGNED   NOT NULL,
  `item_type`        ENUM('room_rate','minibar','service','manual','tax','other') NOT NULL DEFAULT 'other',
  `description`      VARCHAR(255)   NOT NULL DEFAULT '',
  `amount`           DECIMAL(10,2)  NOT NULL,
  `quantity`         INT UNSIGNED   NOT NULL DEFAULT 1,
  `added_by_user_id` INT UNSIGNED       NULL DEFAULT NULL,
  `is_voided`        TINYINT(1)     NOT NULL DEFAULT 0,
  `added_at`         TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bi_reservation` (`reservation_id`),
  KEY `idx_bi_type`        (`item_type`),
  CONSTRAINT `fk_bi_reservation` FOREIGN KEY (`reservation_id`)   REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bi_user`        FOREIGN KEY (`added_by_user_id`) REFERENCES `users`        (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── billing_retry_queue ───────────────────────────────────────
-- Queued billing_items inserts when DB write failed (error handling — Step 2d).
CREATE TABLE IF NOT EXISTS `billing_retry_queue` (
  `id`             INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `reservation_id` INT UNSIGNED   NOT NULL,
  `description`    VARCHAR(255)   NOT NULL DEFAULT '',
  `amount`         DECIMAL(10,2)  NOT NULL,
  `quantity`       INT UNSIGNED   NOT NULL DEFAULT 1,
  `created_at`     TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at`   DATETIME           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_brq_reservation` (`reservation_id`),
  CONSTRAINT `fk_brq_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed: minibar items ───────────────────────────────────────
INSERT IGNORE INTO `minibar_items` (name, sku, price, reorder_threshold, is_active) VALUES
  ('Still Water 500ml',  'MB-WATER-500',  2.50, 3, 1),
  ('Sparkling Water',    'MB-SPARK-500',  3.00, 3, 1),
  ('Cola Can 330ml',     'MB-COLA-330',   3.50, 4, 1),
  ('Orange Juice 250ml', 'MB-OJ-250',     4.00, 2, 1),
  ('Chips / Crisps',     'MB-CHIPS-01',   4.50, 3, 1),
  ('Chocolate Bar',      'MB-CHOC-01',    3.00, 3, 1),
  ('Peanuts 50g',        'MB-NUTS-50',    4.00, 2, 1),
  ('Beer 330ml',         'MB-BEER-330',   6.00, 4, 1),
  ('White Wine 200ml',   'MB-WINE-W-200', 8.50, 2, 1),
  ('Red Wine 200ml',     'MB-WINE-R-200', 8.50, 2, 1);

-- ============================================================
--  UC30 — LOG FOUND ITEM / UC37 — MANAGE L&F
--  Tables: found_items, security_alerts,
--          lost_item_reports, item_returns
-- ============================================================

-- ── found_items ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `found_items` (
  `id`                    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `lf_reference`          VARCHAR(20)   NOT NULL UNIQUE COMMENT 'LF-YYYY-#####',
  `description`           TEXT          NOT NULL,
  `location_type`         ENUM('room','public') NOT NULL DEFAULT 'room',
  `room_number`           VARCHAR(20)       NULL DEFAULT NULL,
  `public_area`           ENUM('lobby','pool','restaurant','elevator','parking','other') NULL DEFAULT NULL,
  `condition`             ENUM('good','damaged','fragile') NOT NULL DEFAULT 'good',
  `photo_url`             VARCHAR(500)      NULL DEFAULT NULL,
  `is_high_value`         TINYINT(1)    NOT NULL DEFAULT 0,
  `escalated_to_security` TINYINT(1)    NOT NULL DEFAULT 0,
  `found_by_user_id`      INT UNSIGNED      NULL DEFAULT NULL,
  `found_at`              TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status`                ENUM('stored','claimed','disposed','matched','shipped') NOT NULL DEFAULT 'stored',
  PRIMARY KEY (`id`),
  KEY `idx_fi_status`   (`status`),
  KEY `idx_fi_user`     (`found_by_user_id`),
  KEY `idx_fi_room`     (`room_number`),
  CONSTRAINT `fk_fi_user` FOREIGN KEY (`found_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── security_alerts ───────────────────────────────────────────
-- Created automatically when found item is flagged is_high_value=1.
-- Managed exclusively by security team (manager role).
CREATE TABLE IF NOT EXISTS `security_alerts` (
  `id`                    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `alert_type`            VARCHAR(60)   NOT NULL DEFAULT 'high_value_found_item',
  `message`               TEXT          NOT NULL,
  `related_found_item_id` INT UNSIGNED      NULL DEFAULT NULL,
  `priority`              ENUM('normal','urgent') NOT NULL DEFAULT 'normal',
  `created_at`            TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at`           DATETIME          NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sa_item` (`related_found_item_id`),
  CONSTRAINT `fk_sa_item` FOREIGN KEY (`related_found_item_id`) REFERENCES `found_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── lost_item_reports ─────────────────────────────────────────
-- Guest-submitted reports. Matched against found_items by front desk (UC37).
CREATE TABLE IF NOT EXISTS `lost_item_reports` (
  `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guest_id`             INT UNSIGNED NOT NULL,
  `description`          TEXT         NOT NULL,
  `reservation_id`       INT UNSIGNED     NULL DEFAULT NULL,
  `lost_date`            DATE             NULL DEFAULT NULL,
  `status`               ENUM('open','matched','closed') NOT NULL DEFAULT 'open',
  `matched_found_item_id`INT UNSIGNED     NULL DEFAULT NULL,
  `created_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lir_guest`   (`guest_id`),
  KEY `idx_lir_matched` (`matched_found_item_id`),
  CONSTRAINT `fk_lir_guest`   FOREIGN KEY (`guest_id`)              REFERENCES `guests`      (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lir_matched` FOREIGN KEY (`matched_found_item_id`) REFERENCES `found_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── item_returns ──────────────────────────────────────────────
-- Records item pickup or courier return to guest.
CREATE TABLE IF NOT EXISTS `item_returns` (
  `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `found_item_id`   INT UNSIGNED  NOT NULL,
  `guest_id`        INT UNSIGNED  NOT NULL,
  `return_method`   ENUM('pickup','courier') NOT NULL DEFAULT 'pickup',
  `return_address`  VARCHAR(500)      NULL DEFAULT NULL,
  `shipping_cost`   DECIMAL(8,2)  NOT NULL DEFAULT 0.00,
  `returned_at`     DATETIME          NULL DEFAULT NULL,
  `created_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ir_item`  (`found_item_id`),
  KEY `idx_ir_guest` (`guest_id`),
  CONSTRAINT `fk_ir_item`  FOREIGN KEY (`found_item_id`) REFERENCES `found_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ir_guest` FOREIGN KEY (`guest_id`)      REFERENCES `guests`      (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  UC31 — TRIGGER LOW-STOCK ALERT
--  Tables: supply_items, supply_inventory,
--          low_stock_alerts, restocking_requisitions
-- ============================================================

-- ── supply_items ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `supply_items` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(120)  NOT NULL,
  `category`      ENUM('linen','toiletries','cleaning','amenities','minibar_stock','other') NOT NULL DEFAULT 'other',
  `min_threshold` INT UNSIGNED  NOT NULL DEFAULT 5,
  `unit`          VARCHAR(30)   NOT NULL DEFAULT 'units',
  `is_active`     TINYINT(1)    NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── supply_inventory ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `supply_inventory` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `item_id`       INT UNSIGNED  NOT NULL,
  `location`      ENUM('floor_1','floor_2','floor_3','floor_4','storage','other') NOT NULL DEFAULT 'storage',
  `current_stock` INT UNSIGNED  NOT NULL DEFAULT 0,
  `last_updated`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_si_item_loc` (`item_id`, `location`),
  KEY `idx_si_item` (`item_id`),
  CONSTRAINT `fk_si_item` FOREIGN KEY (`item_id`) REFERENCES `supply_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── low_stock_alerts ──────────────────────────────────────────
-- Active alert = stock below min_threshold. Escalated after 2h if unacknowledged.
-- dismiss_reason: supervisor records reason when dismissing a false-threshold alert.
CREATE TABLE IF NOT EXISTS `low_stock_alerts` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `item_id`          INT UNSIGNED  NOT NULL,
  `location`         VARCHAR(30)   NOT NULL DEFAULT 'storage',
  `current_stock`    INT UNSIGNED  NOT NULL DEFAULT 0,
  `min_threshold`    INT UNSIGNED  NOT NULL DEFAULT 5,
  `status`           ENUM('active','acknowledged','resolved') NOT NULL DEFAULT 'active',
  `acknowledged_by`  INT UNSIGNED      NULL DEFAULT NULL,
  `acknowledged_at`  DATETIME          NULL DEFAULT NULL,
  `escalated`        TINYINT(1)    NOT NULL DEFAULT 0,
  `dismiss_reason`   TEXT              NULL DEFAULT NULL COMMENT 'Supervisor logs reason when dismissing false-threshold alert',
  `created_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lsa_item`   (`item_id`),
  KEY `idx_lsa_status` (`status`),
  CONSTRAINT `fk_lsa_item` FOREIGN KEY (`item_id`)          REFERENCES `supply_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lsa_user` FOREIGN KEY (`acknowledged_by`)  REFERENCES `users`        (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── restocking_requisitions ───────────────────────────────────
CREATE TABLE IF NOT EXISTS `restocking_requisitions` (
  `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `items`                JSON         NOT NULL COMMENT '[{item_id, quantity_needed}]',
  `requested_by_user_id` INT UNSIGNED     NULL DEFAULT NULL,
  `status`               ENUM('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `created_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rr_user` (`requested_by_user_id`),
  CONSTRAINT `fk_rr_user` FOREIGN KEY (`requested_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed: supply items ────────────────────────────────────────
INSERT IGNORE INTO `supply_items` (name, category, min_threshold, unit) VALUES
  ('Bed Sheets (King)',      'linen',       10, 'sets'),
  ('Bed Sheets (Queen)',     'linen',       10, 'sets'),
  ('Bath Towels',            'linen',       20, 'pieces'),
  ('Hand Towels',            'linen',       20, 'pieces'),
  ('Shampoo 30ml',           'toiletries',  30, 'bottles'),
  ('Conditioner 30ml',       'toiletries',  30, 'bottles'),
  ('Body Wash 30ml',         'toiletries',  30, 'bottles'),
  ('Soap Bar',               'toiletries',  50, 'bars'),
  ('Toilet Paper Roll',      'toiletries',  60, 'rolls'),
  ('All-Purpose Cleaner',    'cleaning',     5, 'liters'),
  ('Bathroom Disinfectant',  'cleaning',     5, 'liters'),
  ('Vacuum Bags',            'cleaning',    10, 'units'),
  ('Coffee Pods',            'amenities',   40, 'pods'),
  ('Tea Bags',               'amenities',   40, 'bags'),
  ('Sugar Sachets',          'amenities',   60, 'sachets');

-- ============================================================
--  UC32 — MANAGE QUALITY ASSURANCE / UC33 — SUBMIT QA SCORE
--  Tables: qa_inspections, corrective_tasks,
--          quality_scores, housekeeper_performance
-- ============================================================

-- ── qa_inspections ────────────────────────────────────────────
-- One row per inspection event.
-- overall_result='corrective_action' when FAILs have assignments.
-- is_critical=1 → room MUST be set out_of_order (cannot be dismissed).
-- Guest-complaint-triggered inspections are created with the same flow
-- and distinguished by the inspector_id / notes content.
CREATE TABLE IF NOT EXISTS `qa_inspections` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `room_id`          INT UNSIGNED  NOT NULL,
  `inspector_id`     INT UNSIGNED  NOT NULL COMMENT 'supervisor or housekeeper who conducted inspection',
  `inspection_date`  DATE          NOT NULL,
  `overall_result`   ENUM('pass','fail','corrective_action') NOT NULL DEFAULT 'pass',
  `checklist_scores` JSON          NOT NULL COMMENT '{floors_surfaces,bathroom,bed_linen,amenities,minibar,maintenance,odor_air}: pass|fail|na',
  `notes`            TEXT              NULL DEFAULT NULL,
  `is_critical`      TINYINT(1)    NOT NULL DEFAULT 0 COMMENT '1 = critical safety failure; sets room out_of_order; cannot be dismissed',
  `created_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_qi_room`     (`room_id`),
  KEY `idx_qi_inspector`(`inspector_id`),
  KEY `idx_qi_date`     (`inspection_date`),
  CONSTRAINT `fk_qi_room`      FOREIGN KEY (`room_id`)      REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qi_inspector` FOREIGN KEY (`inspector_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── corrective_tasks ──────────────────────────────────────────
-- Created per FAIL line item. Assigned to specific housekeepers.
-- Roles that see them: housekeeper (own tasks), supervisor/manager (all).
CREATE TABLE IF NOT EXISTS `corrective_tasks` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `qa_inspection_id`    INT UNSIGNED NOT NULL,
  `assigned_to_user_id` INT UNSIGNED     NULL DEFAULT NULL,
  `task_description`    TEXT         NOT NULL,
  `due_by`              DATETIME         NULL DEFAULT NULL,
  `status`              ENUM('pending','completed') NOT NULL DEFAULT 'pending',
  `created_at`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ct_inspection`(`qa_inspection_id`),
  KEY `idx_ct_user`      (`assigned_to_user_id`),
  CONSTRAINT `fk_ct_inspection` FOREIGN KEY (`qa_inspection_id`)    REFERENCES `qa_inspections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ct_user`       FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users`          (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── quality_scores ────────────────────────────────────────────
-- UC33: Supervisor scores housekeeper performance per inspection.
-- Dimensions: cleanliness, presentation, completeness, speed (0-100 each).
-- Roles that see them: supervisor (own), manager (all), housekeeper (own summary).
CREATE TABLE IF NOT EXISTS `quality_scores` (
  `id`                   INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `inspection_id`        INT UNSIGNED  NOT NULL,
  `housekeeper_id`       INT UNSIGNED  NOT NULL,
  `room_id`              INT UNSIGNED  NOT NULL,
  `cleanliness`          TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `presentation`         TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `completeness`         TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `speed`                TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `overall_score`        DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
  `notes`                TEXT              NULL DEFAULT NULL,
  `photo_urls`           JSON              NULL DEFAULT NULL,
  `is_disputed`          TINYINT(1)    NOT NULL DEFAULT 0,
  `dispute_resolution`   TEXT              NULL DEFAULT NULL,
  `submitted_by_user_id` INT UNSIGNED      NULL DEFAULT NULL,
  `created_at`           TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_qs_inspection`  (`inspection_id`),
  KEY `idx_qs_housekeeper` (`housekeeper_id`),
  CONSTRAINT `fk_qs_inspection`  FOREIGN KEY (`inspection_id`)        REFERENCES `qa_inspections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qs_housekeeper` FOREIGN KEY (`housekeeper_id`)       REFERENCES `users`          (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qs_submitter`   FOREIGN KEY (`submitted_by_user_id`) REFERENCES `users`          (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── housekeeper_performance ───────────────────────────────────
-- Rolling performance summary per housekeeper.
-- Updated by updatePerformance() after each quality_score submission.
-- trend: 'improving' | 'stable' | 'declining' (based on last 5 scores).
CREATE TABLE IF NOT EXISTS `housekeeper_performance` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `housekeeper_id`    INT UNSIGNED NOT NULL UNIQUE,
  `avg_score`         DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `total_inspections` INT UNSIGNED NOT NULL DEFAULT 0,
  `trend`             ENUM('improving','stable','declining') NOT NULL DEFAULT 'stable',
  `updated_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_hp_housekeeper` (`housekeeper_id`),
  CONSTRAINT `fk_hp_housekeeper` FOREIGN KEY (`housekeeper_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  UC34 — MAINTENANCE WORK-ORDER BASE
--  UC35 — EMERGENCY REPAIR
--  UC36 — PREVENTATIVE MAINTENANCE
--  Tables: assets, work_orders, work_order_logs,
--          emergency_flags, property_wide_alerts,
--          replacement_review_flags, preventative_schedules
-- ============================================================

-- ── assets ────────────────────────────────────────────────────
-- Shared asset registry referenced by work orders.
-- Roles that manage: maintenance_technician, manager, supervisor.
CREATE TABLE IF NOT EXISTS `assets` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120)  NOT NULL,
  `location`   VARCHAR(120)  NOT NULL DEFAULT '',
  `asset_type` ENUM('hvac','plumbing','electrical','elevator','pool','kitchen','furniture','it','other') NOT NULL DEFAULT 'other',
  `status`     ENUM('operational','under_maintenance','decommissioned') NOT NULL DEFAULT 'operational',
  `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── work_orders ───────────────────────────────────────────────
-- Core UC34 table. Shared by UC35 (emergency) and UC36 (preventative).
-- Status machine: open → in_progress → (pending_parts ↔ in_progress) → completed → closed
--                 any → rejected (supervisor, requires rejection_reason)
-- Close validation: work_performed required, supervisor_id must be set.
CREATE TABLE IF NOT EXISTS `work_orders` (
  `id`                  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `type`                ENUM('emergency','preventative') NOT NULL DEFAULT 'emergency',
  `room_id`             INT UNSIGNED      NULL DEFAULT NULL,
  `asset_id`            INT UNSIGNED      NULL DEFAULT NULL,
  `description`         TEXT          NOT NULL,
  `priority`            ENUM('low','normal','high','emergency') NOT NULL DEFAULT 'normal',
  `status`              ENUM('open','in_progress','pending_parts','completed','closed','rejected') NOT NULL DEFAULT 'open',
  `assigned_to_user_id` INT UNSIGNED      NULL DEFAULT NULL,
  `created_by_user_id`  INT UNSIGNED      NULL DEFAULT NULL,
  `work_performed`      TEXT              NULL DEFAULT NULL COMMENT 'Required before supervisor can close',
  `parts_used`          JSON              NULL DEFAULT NULL,
  `time_spent_minutes`  INT UNSIGNED  NOT NULL DEFAULT 0,
  `supervisor_id`       INT UNSIGNED      NULL DEFAULT NULL COMMENT 'Must be set to close',
  `rejection_reason`    TEXT              NULL DEFAULT NULL COMMENT 'Required when status=rejected',
  `created_at`          TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at`        DATETIME          NULL DEFAULT NULL,
  `closed_at`           DATETIME          NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wo_room`      (`room_id`),
  KEY `idx_wo_asset`     (`asset_id`),
  KEY `idx_wo_status`    (`status`),
  KEY `idx_wo_priority`  (`priority`),
  KEY `idx_wo_assigned`  (`assigned_to_user_id`),
  CONSTRAINT `fk_wo_room`       FOREIGN KEY (`room_id`)             REFERENCES `rooms`  (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_wo_asset`      FOREIGN KEY (`asset_id`)            REFERENCES `assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_wo_assigned`   FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users`  (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_wo_created_by` FOREIGN KEY (`created_by_user_id`)  REFERENCES `users`  (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_wo_supervisor` FOREIGN KEY (`supervisor_id`)       REFERENCES `users`  (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── work_order_logs ───────────────────────────────────────────
-- Immutable audit trail: every state change, update, note logged here.
-- Roles that read: maintenance_technician (own), supervisor/manager (all).
CREATE TABLE IF NOT EXISTS `work_order_logs` (
  `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `work_order_id`        INT UNSIGNED NOT NULL,
  `action`               VARCHAR(60)  NOT NULL,
  `performed_by_user_id` INT UNSIGNED     NULL DEFAULT NULL,
  `notes`                TEXT             NULL DEFAULT NULL,
  `created_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wol_wo`   (`work_order_id`),
  KEY `idx_wol_user` (`performed_by_user_id`),
  CONSTRAINT `fk_wol_wo`   FOREIGN KEY (`work_order_id`)        REFERENCES `work_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wol_user` FOREIGN KEY (`performed_by_user_id`) REFERENCES `users`       (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── emergency_flags ───────────────────────────────────────────
-- UC35: Extra metadata for emergency-type work orders.
-- severity='safety_critical' → property_wide_alerts is triggered automatically.
-- Roles that manage: manager (exclusive for safety_critical), supervisor.
CREATE TABLE IF NOT EXISTS `emergency_flags` (
  `id`                       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `work_order_id`            INT UNSIGNED NOT NULL UNIQUE,
  `severity`                 ENUM('low','medium','high','safety_critical') NOT NULL DEFAULT 'high',
  `is_safety_critical`       TINYINT(1)   NOT NULL DEFAULT 0,
  `property_alert_triggered` TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`               TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ef_wo` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── property_wide_alerts ──────────────────────────────────────
-- UC35: Broadcast alerts triggered by safety_critical emergencies.
-- Roles that see: manager, supervisor (all staff via dashboard).
CREATE TABLE IF NOT EXISTS `property_wide_alerts` (
  `id`                        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `alert_type`                VARCHAR(60)   NOT NULL DEFAULT 'safety_critical_emergency',
  `message`                   TEXT          NOT NULL,
  `triggered_by_work_order_id`INT UNSIGNED      NULL DEFAULT NULL,
  `is_resolved`               TINYINT(1)    NOT NULL DEFAULT 0,
  `created_at`                TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at`               DATETIME          NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pwa_wo` (`triggered_by_work_order_id`),
  CONSTRAINT `fk_pwa_wo` FOREIGN KEY (`triggered_by_work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── replacement_review_flags ──────────────────────────────────
-- UC35: Raised when ≥3 emergency work orders hit same room/asset in 30 days.
-- Signals the asset/room may need replacement (manager review required).
CREATE TABLE IF NOT EXISTS `replacement_review_flags` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_id`         INT UNSIGNED     NULL DEFAULT NULL,
  `asset_id`        INT UNSIGNED     NULL DEFAULT NULL,
  `emergency_count` INT UNSIGNED NOT NULL DEFAULT 3,
  `reviewed`        TINYINT(1)   NOT NULL DEFAULT 0,
  `review_notes`    TEXT             NULL DEFAULT NULL,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rrf_room`  (`room_id`),
  KEY `idx_rrf_asset` (`asset_id`),
  CONSTRAINT `fk_rrf_room`  FOREIGN KEY (`room_id`)  REFERENCES `rooms`  (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_rrf_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── preventative_schedules ────────────────────────────────────
-- UC36: Schedule metadata linked to preventative work orders.
-- is_recurring=1 → next_due_date auto-calculated by calcNextDue().
-- Conflict check: checks reservations + existing preventative_schedules before accepting.
CREATE TABLE IF NOT EXISTS `preventative_schedules` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `work_order_id`         INT UNSIGNED NOT NULL UNIQUE,
  `asset_id`              INT UNSIGNED     NULL DEFAULT NULL,
  `room_id`               INT UNSIGNED     NULL DEFAULT NULL,
  `maintenance_type`      ENUM('inspection','cleaning','lubrication','calibration','replacement','other') NOT NULL DEFAULT 'other',
  `scheduled_date`        DATE         NOT NULL,
  `estimated_minutes`     INT UNSIGNED NOT NULL DEFAULT 60,
  `is_recurring`          TINYINT(1)   NOT NULL DEFAULT 0,
  `recurrence_frequency`  ENUM('weekly','monthly','quarterly','yearly') NULL DEFAULT NULL,
  `next_due_date`         DATE             NULL DEFAULT NULL,
  `created_at`            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ps_wo`    (`work_order_id`),
  KEY `idx_ps_date`  (`scheduled_date`),
  CONSTRAINT `fk_ps_wo`    FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ps_asset` FOREIGN KEY (`asset_id`)      REFERENCES `assets`      (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ps_room`  FOREIGN KEY (`room_id`)       REFERENCES `rooms`       (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed: assets ──────────────────────────────────────────────
INSERT IGNORE INTO `assets` (name, location, asset_type, status) VALUES
  ('Central HVAC Unit',       'Basement',       'hvac',        'operational'),
  ('Floor 1 HVAC',            'Floor 1',        'hvac',        'operational'),
  ('Floor 2 HVAC',            'Floor 2',        'hvac',        'operational'),
  ('Floor 3 HVAC',            'Floor 3',        'hvac',        'operational'),
  ('Main Water Boiler',       'Basement',       'plumbing',    'operational'),
  ('Pool Filtration System',  'Pool Area',      'pool',        'operational'),
  ('Elevator A',              'Main Lobby',     'elevator',    'operational'),
  ('Elevator B',              'East Wing',      'elevator',    'operational'),
  ('Main Electrical Panel',   'Basement',       'electrical',  'operational'),
  ('Kitchen Exhaust Fan',     'Kitchen',        'kitchen',     'operational'),
  ('Network Switch — Floor 1','IT Room',        'it',          'operational'),
  ('Backup Generator',        'Basement',       'electrical',  'operational');

-- ============================================================
--  UC35 — EMERGENCY REPAIR: extra columns on work_orders
-- ============================================================
ALTER TABLE `work_orders`
  ADD COLUMN IF NOT EXISTS `failure_type`          ENUM('electrical','plumbing','hvac','structural','safety_hazard','equipment','other') NULL DEFAULT NULL     AFTER `type`,
  ADD COLUMN IF NOT EXISTS `contractor_required`   TINYINT(1) NOT NULL DEFAULT 0 AFTER `rejection_reason`,
  ADD COLUMN IF NOT EXISTS `immediate_safety_risk` TINYINT(1) NOT NULL DEFAULT 0 AFTER `contractor_required`;

-- ============================================================
--  DASHBOARD SEED DATA — ensures all 7 stats show live data
--  Uses CURDATE() so data is always "today"
-- ============================================================

-- ── Seed guests (if not already present) ──────────────────
INSERT IGNORE INTO `guests` (id, name, email, phone, nationality) VALUES
  (1, 'Ahmed Al-Rashid',    'ahmed@example.com',   '+20-100-0000001', 'Egyptian'),
  (2, 'Sarah Mitchell',     'sarah@example.com',   '+44-700-0000002', 'British'),
  (3, 'Jean-Pierre Moreau', 'jp@example.com',      '+33-600-0000003', 'French'),
  (4, 'Fatima Hassan',      'fatima@example.com',  '+20-110-0000004', 'Egyptian'),
  (5, 'James O\'Brien',     'james@example.com',   '+1-212-0000005',  'American'),
  (6, 'Mei Lin Chen',       'mei@example.com',     '+86-130-0000006', 'Chinese'),
  (7, 'Khalid Al-Mansoori', 'khalid@example.com',  '+971-50-0000007', 'Emirati');

-- ── Mark some rooms occupied/dirty to populate room stats ──
UPDATE `rooms` SET status = 'occupied'    WHERE room_number IN ('101','102','103','201','202') AND status = 'available';
UPDATE `rooms` SET status = 'dirty'       WHERE room_number IN ('104','203')                  AND status = 'available';
UPDATE `rooms` SET status = 'out_of_order' WHERE room_number = '105'                          AND status = 'available';

-- ── Seed today's reservations (confirmed check-ins) ────────
INSERT IGNORE INTO `reservations`
  (id, guest_id, room_id, check_in_date, check_out_date, adults, children,
   status, total_price, deposit_paid, special_requests, created_at)
SELECT
  100 + g.id,
  g.id,
  r.id,
  CURDATE(),
  CURDATE() + INTERVAL 3 DAY,
  2, 0,
  'confirmed',
  ROUND(r.price_per_night * 3 * 1.1, 2),
  ROUND(r.price_per_night * 3 * 0.2, 2),
  CASE g.id
    WHEN 1 THEN 'VIP guest — champagne on arrival, high floor preferred'
    WHEN 2 THEN 'Honeymoon couple — flowers and chocolates'
    WHEN 3 THEN 'VIP — requires quiet room away from elevator'
    ELSE NULL
  END,
  NOW()
FROM guests g
JOIN rooms r ON r.status = 'available'
WHERE g.id IN (1,2,3)
  AND NOT EXISTS (SELECT 1 FROM reservations res WHERE res.id = 100 + g.id)
LIMIT 3;

-- ── Seed checked-in guests for check-out today ─────────────
INSERT IGNORE INTO `reservations`
  (id, guest_id, room_id, check_in_date, check_out_date, adults, children,
   status, total_price, deposit_paid, special_requests, created_at)
SELECT
  200 + g.id,
  g.id,
  r.id,
  CURDATE() - INTERVAL 2 DAY,
  CURDATE(),
  2, 1,
  'checked_in',
  ROUND(r.price_per_night * 2 * 1.1, 2),
  ROUND(r.price_per_night * 2 * 0.2, 2),
  NULL,
  NOW() - INTERVAL 2 DAY
FROM guests g
JOIN rooms r ON r.status = 'occupied'
WHERE g.id IN (4,5)
  AND NOT EXISTS (SELECT 1 FROM reservations res WHERE res.id = 200 + g.id)
LIMIT 2;

-- ── Seed payments for today's revenue ──────────────────────
INSERT IGNORE INTO `payments` (reservation_id, amount, method, status, processed_at) VALUES
  (101, 850.00,  'credit_card', 'completed', NOW()),
  (102, 1200.00, 'credit_card', 'completed', NOW()),
  (103, 975.00,  'cash',        'completed', NOW()),
  (201, 620.00,  'credit_card', 'completed', NOW()),
  (202, 430.00,  'cash',        'completed', NOW());

-- ── Seed pending housekeeping tasks ────────────────────────
INSERT IGNORE INTO `housekeeping_tasks`
  (id, room_id, assigned_to, task_type, status, priority, notes, created_at)
SELECT
  300 + r.id,
  r.id,
  (SELECT id FROM users WHERE role IN ('housekeeper','maintenance_technician') LIMIT 1),
  ELT(FLOOR(1 + RAND()*3), 'cleaning', 'turndown', 'inspection'),
  'pending',
  'normal',
  'Standard daily task',
  NOW()
FROM rooms r
WHERE r.status IN ('dirty','occupied')
  AND NOT EXISTS (SELECT 1 FROM housekeeping_tasks ht WHERE ht.id = 300 + r.id)
LIMIT 5;

-- ============================================================
--  AUDIT LOG — Add missing columns + indexes (idempotent)
--  UC spec requires: ip_address, user_agent, indexes on
--  user_id, action, target_type, created_at
--  FK uses ON DELETE SET NULL → records survive user deletion
-- ============================================================

ALTER TABLE `audit_log`
  ADD COLUMN IF NOT EXISTS `ip_address`  VARCHAR(45) DEFAULT NULL AFTER `new_value`,
  ADD COLUMN IF NOT EXISTS `user_agent`  TEXT        DEFAULT NULL AFTER `ip_address`;

-- Performance indexes (ignore error if already exists)
ALTER IGNORE TABLE `audit_log`
  ADD INDEX `idx_audit_action`      (`action`),
  ADD INDEX `idx_audit_target_type` (`target_type`),
  ADD INDEX `idx_audit_created_at`  (`created_at`);

-- ============================================================
--  COMPREHENSIVE SEED DATA — fills all empty tables
--  All financial data consistent with existing reservations
-- ============================================================

-- ── payments: 30 days of real revenue ─────────────────────
INSERT IGNORE INTO `payments` (id, reservation_id, amount, method, status, processed_at) VALUES
  (1,  2, 2150.00, 'credit_card', 'completed', NOW() - INTERVAL 9 DAY),
  (2,  3, 3350.00, 'credit_card', 'completed', NOW() - INTERVAL 8 DAY),
  (3,  4, 1275.00, 'cash',        'completed', NOW() - INTERVAL 8 DAY),
  (4,  4, 1275.00, 'cash',        'completed', NOW() - INTERVAL 7 DAY),
  (5,  1,  500.00, 'credit_card', 'completed', NOW() - INTERVAL 6 DAY),
  (6,  6, 3350.00, 'credit_card', 'completed', NOW() - INTERVAL 5 DAY),
  (7,  6, 3350.00, 'bank_transfer','completed',NOW() - INTERVAL 4 DAY),
  (8,  8, 7850.00, 'credit_card', 'completed', NOW() - INTERVAL 3 DAY),
  (9,  5,  500.00, 'cash',        'completed', NOW() - INTERVAL 3 DAY),
  (10, 9,  500.00, 'credit_card', 'completed', NOW() - INTERVAL 2 DAY),
  (11, 21, 500.00, 'credit_card', 'completed', NOW() - INTERVAL 2 DAY),
  (12, 22, 400.00, 'cash',        'completed', NOW() - INTERVAL 2 DAY),
  (13, 23, 300.00, 'credit_card', 'completed', NOW() - INTERVAL 1 DAY),
  (14, 24, 300.00, 'cash',        'completed', NOW() - INTERVAL 1 DAY),
  (15, 25, 250.00, 'credit_card', 'completed', NOW() - INTERVAL 1 DAY),
  (16, 28, 900.00, 'credit_card', 'completed', NOW() - INTERVAL 1 DAY),
  (40, 2,  2150.00,'credit_card', 'completed', NOW() - INTERVAL 10 DAY),
  (41, 3,  1500.00,'cash',        'completed', NOW() - INTERVAL 10 DAY),
  (42, 3,  1850.00,'credit_card', 'completed', NOW() - INTERVAL 9 DAY),
  -- today's payments (drive revenue_today stat)
  (101,101, 850.00,'credit_card', 'completed', NOW()),
  (102,102,1200.00,'credit_card', 'completed', NOW()),
  (103,103, 975.00,'cash',        'completed', NOW()),
  (201,201, 620.00,'credit_card', 'completed', NOW()),
  (202,202, 430.00,'cash',        'completed', NOW());

-- ── billing_items: charges for existing reservations ──────
INSERT IGNORE INTO `billing_items` (id, reservation_id, item_type, description, amount, quantity, added_by_user_id, added_at, is_voided) VALUES
  (4,  2, 'room_rate',        'Room 201 — Standard × 4 nights',   2000.00, 1, 3, NOW() - INTERVAL 9 DAY,  0),
  (5,  2, 'minibar',          'Minibar — Beer × 2',                  18.00, 2, 3, NOW() - INTERVAL 8 DAY,  0),
  (6,  2, 'external_service', 'Spa Session — 60 min',                80.00, 1, 3, NOW() - INTERVAL 8 DAY,  0),
  (7,  3, 'room_rate',        'Room 202 — Deluxe × 4 nights',      3200.00, 1, 3, NOW() - INTERVAL 8 DAY,  0),
  (8,  3, 'external_service', 'City Tour',                           150.00, 1, 3, NOW() - INTERVAL 7 DAY,  0),
  (9,  4, 'room_rate',        'Room 203 — Deluxe × 3 nights',      2400.00, 1, 4, NOW() - INTERVAL 8 DAY,  0),
  (10, 4, 'manual',           'Late checkout surcharge',              50.00, 1, 1, NOW() - INTERVAL 7 DAY,  0),
  (11, 5, 'room_rate',        'Room 101 — Standard × 4 nights',    2000.00, 1, 3, NOW() - INTERVAL 5 DAY,  0),
  (12, 6, 'room_rate',        'Room 301 — Suite × 6 nights',       9000.00, 1, 4, NOW() - INTERVAL 5 DAY,  0),
  (13, 6, 'external_service', 'Airport transfer × 2',                160.00, 2, 4, NOW() - INTERVAL 4 DAY,  0),
  (14, 21,'room_rate',        'Room rate × 3 nights',               1800.00, 1, 3, NOW() - INTERVAL 2 DAY,  0),
  (15, 22,'room_rate',        'Room rate × 2 nights',               1400.00, 1, 3, NOW() - INTERVAL 2 DAY,  0),
  (16, 23,'room_rate',        'Room rate × 2 nights',               1200.00, 1, 3, NOW() - INTERVAL 1 DAY,  0),
  (17, 24,'room_rate',        'Room rate × 3 nights',               1600.00, 1, 4, NOW() - INTERVAL 1 DAY,  0),
  (18, 25,'room_rate',        'Room rate × 2 nights',               1200.00, 1, 3, NOW() - INTERVAL 1 DAY,  0),
  (19, 28,'room_rate',        'Suite × 5 nights',                   7500.00, 1, 3, NOW() - INTERVAL 1 DAY,  0),
  (20, 28,'external_service', 'Couples spa package',                  400.00, 1, 3, NOW() - INTERVAL 1 DAY,  0);

-- ── billing_adjustments ────────────────────────────────────
INSERT IGNORE INTO `billing_adjustments` (reservation_id, type, value, applied_by_user_id, reason, created_at) VALUES
  (2,  'discount',          100.00, 1, 'Loyalty member — 5% room discount',   NOW() - INTERVAL 9 DAY),
  (3,  'discount',          150.00, 1, 'Early booking discount',               NOW() - INTERVAL 8 DAY),
  (4,  'surcharge',          50.00, 1, 'Late checkout fee',                    NOW() - INTERVAL 7 DAY),
  (6,  'loyalty_redemption',200.00, 2, '200 points redeemed — platinum guest', NOW() - INTERVAL 4 DAY),
  (28, 'discount',          100.00, 1, 'VIP complimentary upgrade',            NOW() - INTERVAL 1 DAY);

-- ── billing_adjustments: update folio amounts to stay consistent
UPDATE `folios` SET amount_paid = 1250.00, total_amount = 2085.00  WHERE id = 1;
UPDATE `folios` SET amount_paid = 800.00,  total_amount = 1500.00  WHERE id = 7;

-- ── work_orders: open + completed WOs ─────────────────────
INSERT IGNORE INTO `work_orders`
  (id, type, title, description, room_id, asset_id, priority, status, assigned_to, reported_by, created_at)
VALUES
  (1, 'corrective', 'AC not cooling — Room 104',    'Unit makes grinding noise, no cold air',   4,  1, 'high',   'in_progress', 6, 3, NOW() - INTERVAL 3 DAY),
  (2, 'corrective', 'Bathroom leak — Room 201',     'Water dripping under sink',                8,  5, 'high',   'open',        6, 4, NOW() - INTERVAL 2 DAY),
  (3, 'corrective', 'TV remote missing — Room 102', 'Guest reports remote not found',           2,  NULL,'low',  'open',        6, 3, NOW() - INTERVAL 1 DAY),
  (4, 'preventative','HVAC quarterly service',      'Routine filter replacement and inspection',1,  1, 'normal', 'completed',   6, 1, NOW() - INTERVAL 7 DAY),
  (5, 'emergency',  'Elevator B malfunction',       'Doors not closing, guests stranded',       NULL,8,'high',  'in_progress', 6, 1, NOW() - INTERVAL 1 DAY),
  (6, 'corrective', 'Hallway light out — Floor 2',  'Light fixture needs replacement',          NULL,9,'normal','open',        6, 3, NOW() - INTERVAL 4 DAY),
  (7, 'preventative','Pool pump inspection',        'Monthly routine check',                    NULL,8,'normal','completed',   6, 1, NOW() - INTERVAL 14 DAY),
  (8, 'corrective', 'Broken door lock — Room 203',  'Electronic lock malfunctioning',           9,  NULL,'high', 'completed',  6, 4, NOW() - INTERVAL 5 DAY);

-- ── work_order_logs ────────────────────────────────────────
INSERT IGNORE INTO `work_order_logs` (work_order_id, action, notes, performed_by, created_at) VALUES
  (1, 'created',     'WO logged by front desk',                           3, NOW() - INTERVAL 3 DAY),
  (1, 'assigned',    'Assigned to maintenance team',                      1, NOW() - INTERVAL 3 DAY),
  (1, 'status_change','Status changed to in_progress',                    6, NOW() - INTERVAL 2 DAY),
  (2, 'created',     'Guest complaint — water leak',                      4, NOW() - INTERVAL 2 DAY),
  (4, 'created',     'Scheduled quarterly HVAC service',                  1, NOW() - INTERVAL 7 DAY),
  (4, 'completed',   'Filters replaced, unit tested. Running normally.',  6, NOW() - INTERVAL 5 DAY),
  (5, 'created',     'Emergency — elevator stopped mid-floor',            1, NOW() - INTERVAL 1 DAY),
  (7, 'created',     'Scheduled pool pump check',                         1, NOW() - INTERVAL 14 DAY),
  (7, 'completed',   'Pump running normally, seals checked.',             6, NOW() - INTERVAL 12 DAY),
  (8, 'created',     'Lock hardware failure reported',                    4, NOW() - INTERVAL 5 DAY),
  (8, 'completed',   'Lock replaced and tested. Room cleared.',           6, NOW() - INTERVAL 4 DAY);

-- ── emergency_flags ────────────────────────────────────────
INSERT IGNORE INTO `emergency_flags` (work_order_id, severity, is_safety_critical, property_alert_triggered) VALUES
  (5, 'safety_critical', 1, 1);

-- ── property_wide_alerts ───────────────────────────────────
INSERT IGNORE INTO `property_wide_alerts` (title, message, severity, triggered_by_work_order_id, is_active, created_at) VALUES
  ('Elevator B Out of Service', 'Elevator B is currently out of service due to door malfunction. Guests to use Elevator A only.', 'high', 5, 1, NOW() - INTERVAL 1 DAY),
  ('Pool Area Closed 08:00–10:00', 'Pool undergoing routine maintenance. Area reopens at 10:00.', 'low', NULL, 0, NOW() - INTERVAL 2 DAY);

-- ── housekeeping_tasks ─────────────────────────────────────
INSERT IGNORE INTO `housekeeping_tasks`
  (id, room_id, assigned_to, task_type, status, priority, notes, created_at)
VALUES
  (1,  4, 6, 'cleaning',   'completed', 'high',   'Deep clean after extended stay', NOW() - INTERVAL 3 DAY),
  (2,  7, 6, 'cleaning',   'completed', 'normal', 'Standard daily clean',           NOW() - INTERVAL 2 DAY),
  (3,  8, 6, 'inspection', 'completed', 'normal', 'Post-checkout inspection',       NOW() - INTERVAL 2 DAY),
  (4,  2, 6, 'turndown',   'completed', 'normal', 'Evening turndown service',       NOW() - INTERVAL 1 DAY),
  (5,  3, 6, 'cleaning',   'pending',   'normal', 'Morning clean',                  NOW()),
  (6,  5, 6, 'cleaning',   'pending',   'high',   'VIP arrival prep — flowers',     NOW()),
  (7,  6, 6, 'inspection', 'pending',   'normal', 'Pre-arrival inspection',         NOW()),
  (8,  1, 6, 'cleaning',   'in_progress','normal','Room in progress',               NOW()),
  (9,  9, 6, 'turndown',   'pending',   'normal', 'Guest requested turndown',       NOW()),
  (10, 10,6, 'cleaning',   'pending',   'normal', 'Standard daily clean',           NOW());

-- ── qa_inspections ─────────────────────────────────────────
INSERT IGNORE INTO `qa_inspections`
  (room_id, inspected_by, overall_score, cleanliness_score, maintenance_score, amenities_score, notes, status, created_at)
VALUES
  (2, 6, 92, 95, 90, 92, 'Room in excellent condition. Minor grout discoloration in bathroom.', 'passed', NOW() - INTERVAL 5 DAY),
  (3, 6, 78, 80, 75, 78, 'Carpet shows wear — recommend replacement within 3 months.',          'passed', NOW() - INTERVAL 4 DAY),
  (5, 6, 85, 88, 82, 85, 'Good overall. AC filter dusty — logged for maintenance.',             'passed', NOW() - INTERVAL 3 DAY),
  (7, 6, 55, 60, 50, 55, 'Multiple issues: stained ceiling, broken wardrobe door.',             'failed', NOW() - INTERVAL 2 DAY),
  (1, 6, 90, 93, 88, 90, 'Suite in excellent condition. Ready for VIP arrival.',                'passed', NOW() - INTERVAL 1 DAY);

-- ── quality_scores ─────────────────────────────────────────
INSERT IGNORE INTO `quality_scores` (room_id, score_date, score, inspector_id, notes) VALUES
  (2, DATE(NOW() - INTERVAL 5 DAY), 92, 6, 'Quarterly inspection'),
  (3, DATE(NOW() - INTERVAL 4 DAY), 78, 6, 'Carpet wear noted'),
  (5, DATE(NOW() - INTERVAL 3 DAY), 85, 6, 'Good condition'),
  (7, DATE(NOW() - INTERVAL 2 DAY), 55, 6, 'Failed — requires corrective tasks'),
  (1, DATE(NOW() - INTERVAL 1 DAY), 90, 6, 'VIP room — excellent');

-- ── corrective_tasks ───────────────────────────────────────
INSERT IGNORE INTO `corrective_tasks` (qa_inspection_id, assigned_to_user_id, task_description, due_by, status) VALUES
  (4, 6, 'Repair stained ceiling tile in Room 303',                NOW() + INTERVAL 3 DAY, 'pending'),
  (4, 6, 'Replace broken wardrobe sliding door in Room 303',       NOW() + INTERVAL 5 DAY, 'pending'),
  (2, 6, 'Regrout bathroom tiles in Room 201 — cosmetic issue',    NOW() + INTERVAL 14 DAY,'pending');

-- ── found_items (Lost & Found) ─────────────────────────────
INSERT IGNORE INTO `found_items`
  (id, lf_reference, description, location_type, room_number, public_area, `condition`, is_high_value, escalated_to_security, found_by_user_id, found_at, status)
VALUES
  (1, 'LF-2026-00001', 'Brown leather wallet — contains cards and cash (~$200)',   'room',   '201', NULL,    'good',    1, 1, 6, NOW() - INTERVAL 5 DAY, 'stored'),
  (2, 'LF-2026-00002', 'iPhone 15 Pro — black case, cracked screen',              'room',   '102', NULL,    'damaged', 1, 1, 6, NOW() - INTERVAL 4 DAY, 'stored'),
  (3, 'LF-2026-00003', 'Blue silk scarf — Hermès brand',                          'public', NULL,  'lobby', 'good',    0, 0, 6, NOW() - INTERVAL 3 DAY, 'stored'),
  (4, 'LF-2026-00004', 'Reading glasses in black case',                           'room',   '303', NULL,    'good',    0, 0, 6, NOW() - INTERVAL 3 DAY, 'matched'),
  (5, 'LF-2026-00005', 'Child plush toy — brown teddy bear',                      'public', NULL,  'pool',  'good',    0, 0, 6, NOW() - INTERVAL 2 DAY, 'claimed'),
  (6, 'LF-2026-00006', 'Grey laptop bag — Dell brand, no laptop inside',          'public', NULL,  'restaurant','good', 0, 0, 6, NOW() - INTERVAL 1 DAY, 'stored');

-- ── lost_item_reports ─────────────────────────────────────
INSERT IGNORE INTO `lost_item_reports`
  (guest_id, description, reservation_id, lost_date, matched_found_item_id, status, created_at)
VALUES
  (4, 'Reading glasses — thin black frame, kept in black case', 5, DATE(NOW() - INTERVAL 4 DAY), 4, 'matched', NOW() - INTERVAL 3 DAY),
  (5, 'Child stuffed bear — brown, about 30cm tall',            NULL, DATE(NOW() - INTERVAL 3 DAY), 5, 'closed', NOW() - INTERVAL 2 DAY),
  (1, 'iPhone — black Pro model, cracked screen protector',     1, DATE(NOW() - INTERVAL 5 DAY), NULL, 'open',   NOW() - INTERVAL 2 DAY);

-- ── minibar_logs ──────────────────────────────────────────
INSERT IGNORE INTO `minibar_logs`
  (reservation_id, item_id, quantity, unit_price, logged_by, logged_at)
VALUES
  (1,  1, 2, 3.00, 6, NOW() - INTERVAL 3 DAY),
  (1,  2, 1, 4.00, 6, NOW() - INTERVAL 3 DAY),
  (2,  3, 3, 6.00, 6, NOW() - INTERVAL 2 DAY),
  (5,  4, 2, 5.00, 6, NOW() - INTERVAL 1 DAY),
  (6,  1, 4, 3.00, 6, NOW() - INTERVAL 1 DAY),
  (6,  2, 2, 4.00, 6, NOW() - INTERVAL 1 DAY);

-- ── preventative_schedules ────────────────────────────────
INSERT IGNORE INTO `preventative_schedules`
  (work_order_id, asset_id, room_id, scheduled_date, maintenance_type, estimated_minutes, is_recurring, recurrence_frequency, next_due_date)
VALUES
  (4, 1, NULL, DATE(NOW() - INTERVAL 7 DAY), 'hvac',    120, 1, 'quarterly', DATE(NOW() + INTERVAL 83 DAY)),
  (7, 8, NULL, DATE(NOW() - INTERVAL 14 DAY),'equipment', 60, 1, 'monthly',  DATE(NOW() + INTERVAL 16 DAY));

-- ── security_alerts ───────────────────────────────────────
INSERT IGNORE INTO `security_alerts`
  (alert_type, message, related_found_item_id, priority, created_at)
VALUES
  ('high_value_found_item', 'High-value item found: Brown leather wallet (~$200 cash). Ref: LF-2026-00001. Please secure immediately.', 1, 'urgent', NOW() - INTERVAL 5 DAY),
  ('high_value_found_item', 'High-value item found: iPhone 15 Pro. Ref: LF-2026-00002. Please secure immediately.',                     2, 'urgent', NOW() - INTERVAL 4 DAY);

-- ── final_invoices: for checked-out reservations ──────────
INSERT IGNORE INTO `final_invoices` (reservation_id, total_amount, tax_amount, discount_amount, is_finalized, issued_at) VALUES
  (2,  2150.00, 215.00,   0.00, 1, NOW() - INTERVAL 8 DAY),
  (3,  3350.00, 335.00, 150.00, 1, NOW() - INTERVAL 7 DAY),
  (4,  2550.00, 255.00,   0.00, 1, NOW() - INTERVAL 6 DAY),
  (8,  7850.00, 785.00,   0.00, 1, NOW() - INTERVAL 2 DAY);

-- ============================================================
--  PRODUCTION SEED — Phase 2
--  Fills ALL remaining empty tables with realistic HMS data
--  Guests IDs 1-8: READ-ONLY  |  Rooms IDs 1-21: READ-ONLY
--  Existing Reservations: 1-10, 21-29
-- ============================================================

-- ── invoices ─────────────────────────────────────────────
INSERT IGNORE INTO `invoices`
  (id, group_id, reservation_id, invoice_type, total_amount, tax_amount, discount_amount, status, generated_at)
VALUES
  (1,  1,    NULL, 'group',      11000.00, 1100.00, 1100.00, 'finalized', NOW() - INTERVAL 28 DAY),
  (2,  NULL, 2,    'individual',  2215.00,  221.50,    0.00, 'paid',      NOW() - INTERVAL 20 DAY),
  (3,  NULL, 3,    'individual',  3481.00,  348.10,  150.00, 'paid',      NOW() - INTERVAL 18 DAY),
  (4,  NULL, 4,    'individual',  2624.50,  262.45,    0.00, 'paid',      NOW() - INTERVAL 15 DAY),
  (5,  NULL, 9,    'individual',  8085.00,  808.50,    0.00, 'paid',      NOW() - INTERVAL 45 DAY),
  (6,  NULL, 10,   'individual',  1030.00,  103.00,    0.00, 'void',      NOW() - INTERVAL 70 DAY),
  (7,  NULL, 24,   'individual',  1648.00,  164.80,    0.00, 'draft',     NOW() - INTERVAL 6 DAY),
  (8,  NULL, 25,   'individual',  1236.00,  123.60,    0.00, 'draft',     NOW() - INTERVAL 5 DAY),
  (9,  NULL, 27,   'individual',  7725.00,  772.50,    0.00, 'draft',     NOW() - INTERVAL 2 DAY),
  (10, NULL, 28,   'individual',  4635.00,  463.50,    0.00, 'draft',     NOW() - INTERVAL 1 DAY);

-- ── invoice_items ─────────────────────────────────────────
INSERT IGNORE INTO `invoice_items`
  (invoice_id, description, amount, item_type, reservation_id)
VALUES
  (2, 'Room 201 — Standard x 4 nights',      2000.00, 'room_rate', 2),
  (2, 'Minibar — Sparkling water, juice',       15.00, 'minibar',   2),
  (2, 'VAT 10%',                               221.50, 'tax',       2),
  (3, 'Room 202 — Deluxe x 4 nights',         3200.00, 'room_rate', 3),
  (3, 'Spa — Aromatherapy massage 60 min',     130.00, 'service',   3),
  (3, 'Loyalty discount — silver 5%',         -150.00, 'discount',  3),
  (3, 'VAT 10%',                               348.10, 'tax',       3),
  (4, 'Room 203 — Deluxe x 3 nights',         2400.00, 'room_rate', 4),
  (4, 'Late checkout surcharge — 2 hrs',        50.00, 'service',   4),
  (4, 'VAT 10%',                               262.45, 'tax',       4),
  (5, 'Room 301 — Suite x 5 nights',           7500.00, 'room_rate', 9),
  (5, 'Anniversary dinner setup',               250.00, 'service',   9),
  (5, 'Champagne — Moet Chandon x 2',           120.00, 'minibar',   9),
  (5, 'VAT 10%',                                808.50, 'tax',       9),
  (7, 'Room 102 — Standard x 4 nights',        1600.00, 'room_rate', 24),
  (7, 'Minibar — Beer x 2',                      16.00, 'minibar',   24),
  (7, 'VAT 10%',                                164.80, 'tax',       24),
  (9, 'Room 101 — Standard x 15 nights',        7500.00, 'room_rate', 27),
  (9, 'VAT 10%',                                772.50, 'tax',       27),
  (10,'Room 303 — Suite x 9 nights',            4500.00, 'room_rate', 28),
  (10,'VAT 10%',                                463.50, 'tax',       28);

-- ── transactions (gateway-level) ─────────────────────────
INSERT IGNORE INTO `transactions`
  (id, guest_id, reservation_id, amount, type, reason, gateway_ref, status, idempotency_key, created_at)
VALUES
  (1,  2, 2,    500.00,'preauth','Deposit 20% — Room 201',         'gw_pre_001','success','idem_tx_001',NOW()-INTERVAL 22 DAY),
  (2,  2, 2,   1650.00,'charge', 'Balance settlement — checkout',  'gw_chg_002','success','idem_tx_002',NOW()-INTERVAL 20 DAY),
  (3,  7, 3,    800.00,'preauth','Deposit 20% — Room 202',         'gw_pre_003','success','idem_tx_003',NOW()-INTERVAL 20 DAY),
  (4,  7, 3,   2400.00,'charge', 'Balance settlement — checkout',  'gw_chg_004','success','idem_tx_004',NOW()-INTERVAL 18 DAY),
  (5,  6, 4,    800.00,'preauth','Deposit 20% — Room 203',         'gw_pre_005','success','idem_tx_005',NOW()-INTERVAL 18 DAY),
  (6,  6, 4,   1475.00,'charge', 'Balance + late checkout fee',    'gw_chg_006','success','idem_tx_006',NOW()-INTERVAL 15 DAY),
  (7,  1, 9,   2000.00,'preauth','Suite deposit — Anniversary',    'gw_pre_007','success','idem_tx_007',NOW()-INTERVAL 50 DAY),
  (8,  1, 9,   5850.00,'charge', 'Balance settlement — checkout',  'gw_chg_008','success','idem_tx_008',NOW()-INTERVAL 45 DAY),
  (9,  4, 10,   500.00,'preauth','Deposit — no-show reservation',  'gw_pre_009','success','idem_tx_009',NOW()-INTERVAL 75 DAY),
  (10, 4, 10,   500.00,'charge', 'No-show penalty forfeiture',     'gw_chg_010','success','idem_tx_010',NOW()-INTERVAL 70 DAY),
  (11, 8, 8,    500.00,'preauth','Deposit — later cancelled',      'gw_pre_011','success','idem_tx_011',NOW()-INTERVAL 10 DAY),
  (12, 8, 8,    500.00,'refund', 'Deposit refund — cancellation',  'gw_ref_012','success','idem_tx_012',NOW()-INTERVAL 8 DAY),
  (13, 1, 24,   300.00,'preauth','Deposit 20% — Room 102',         'gw_pre_013','success','idem_tx_013',NOW()-INTERVAL 7 DAY),
  (14, 7, 25,   250.00,'preauth','Deposit 20% — Room 202',         'gw_pre_014','success','idem_tx_014',NOW()-INTERVAL 6 DAY),
  (15, 2, 27,  1500.00,'preauth','Extended stay deposit — Rm 101', 'gw_pre_015','success','idem_tx_015',NOW()-INTERVAL 3 DAY),
  (16, 1, 28,   900.00,'preauth','Suite deposit — Rm 303',         'gw_pre_016','success','idem_tx_016',NOW()-INTERVAL 2 DAY),
  (17, 5, 7,    200.00,'preauth','Deposit attempt — card declined','gw_fail_017','failed', 'idem_tx_017',NOW()-INTERVAL 5 DAY);

-- ── pending_debts ─────────────────────────────────────────
INSERT IGNORE INTO `pending_debts`
  (guest_id, reservation_id, amount, reason, created_at, resolved_at)
VALUES
  (1, 24, 1348.00,'Outstanding balance res #24 — checkout today',   NOW()-INTERVAL 1 DAY, NULL),
  (7, 25,  986.00,'Outstanding balance res #25 — checkout today',   NOW()-INTERVAL 1 DAY, NULL),
  (3,  5,  835.00,'Unpaid balance res #5 — guest unreachable',      NOW()-INTERVAL 3 DAY, NULL);

-- ── billing_disputes ─────────────────────────────────────
INSERT IGNORE INTO `billing_disputes`
  (group_id, reservation_id, raised_by_user_id, description, status, resolved_at, created_at)
VALUES
  (1, 4, 3,'Guest Pierre Dubois disputes late-checkout $50 — claims policy not communicated at check-in.',     'open',     NULL,                     NOW()-INTERVAL 8 DAY),
  (1, 3, 4,'Guest Priya Sharma disputes spa charge $130 — claims session marked complimentary in booking.',    'resolved', NOW()-INTERVAL 12 DAY,    NOW()-INTERVAL 15 DAY),
  (1,NULL, 1,'TechCorp group folio — 10% discount not applied uniformly across all member reservations.',      'open',     NULL,                     NOW()-INTERVAL 3 DAY);

-- ── qa_inspections (correct schema) ──────────────────────
INSERT IGNORE INTO `qa_inspections`
  (id, room_id, inspector_id, inspection_date, overall_result, checklist_scores, notes, created_at)
VALUES
  (1, 2, 7, DATE(NOW()-INTERVAL 14 DAY),'pass',
   '{"cleanliness":95,"bathroom":92,"amenities":93,"minibar":88,"linen":96,"overall":93}',
   'Excellent. Minor soap residue on shower door, corrected during inspection.', NOW()-INTERVAL 14 DAY),
  (2, 3, 7, DATE(NOW()-INTERVAL 12 DAY),'corrective_action',
   '{"cleanliness":78,"bathroom":80,"amenities":75,"minibar":82,"linen":76,"overall":78}',
   'Carpet wear near entrance. Bathroom grout discolored. 90-day replacement recommendation raised.', NOW()-INTERVAL 12 DAY),
  (3, 5, 5, DATE(NOW()-INTERVAL 10 DAY),'pass',
   '{"cleanliness":85,"bathroom":88,"amenities":84,"minibar":90,"linen":87,"overall":87}',
   'Good overall. AC filter dusty — work order logged for maintenance.', NOW()-INTERVAL 10 DAY),
  (4, 6, 7, DATE(NOW()-INTERVAL 7 DAY), 'pass',
   '{"cleanliness":91,"bathroom":93,"amenities":96,"minibar":95,"linen":94,"overall":94}',
   'Suite in excellent condition. VIP ready. Champagne pre-stocked per guest request.', NOW()-INTERVAL 7 DAY),
  (5, 7, 5, DATE(NOW()-INTERVAL 5 DAY), 'fail',
   '{"cleanliness":55,"bathroom":60,"amenities":50,"minibar":0,"linen":58,"overall":45}',
   'Room OOO — AC under repair. Corrective tasks issued. Not lettable.', NOW()-INTERVAL 5 DAY),
  (6, 1, 7, DATE(NOW()-INTERVAL 2 DAY), 'pass',
   '{"cleanliness":90,"bathroom":92,"amenities":91,"minibar":89,"linen":93,"overall":91}',
   'VIP room prepared and verified. All amenities restocked.', NOW()-INTERVAL 2 DAY),
  (7, 4, 5, DATE(NOW()-INTERVAL 1 DAY), 'corrective_action',
   '{"cleanliness":72,"bathroom":70,"amenities":75,"minibar":80,"linen":74,"overall":74}',
   'Deep clean in progress post extended stay. Re-inspection scheduled after completion.', NOW()-INTERVAL 1 DAY);

-- ── quality_scores ────────────────────────────────────────
INSERT IGNORE INTO `quality_scores`
  (inspection_id, housekeeper_id, room_id, cleanliness, presentation, completeness, speed, overall_score, notes, submitted_by_user_id, is_disputed, created_at)
VALUES
  (1, 6, 2, 95, 93, 92, 88, 92.00,'Strong performance. Shower door self-corrected during inspection.',     7, 0, NOW()-INTERVAL 14 DAY),
  (2, 5, 3, 78, 76, 75, 80, 77.25,'Below avg — carpet and grout require corrective tasks.',                7, 0, NOW()-INTERVAL 12 DAY),
  (3, 6, 5, 85, 87, 84, 82, 84.50,'Solid. AC filter escalated separately to maintenance.',                5, 0, NOW()-INTERVAL 10 DAY),
  (4, 7, 6, 91, 94, 96, 90, 92.75,'VIP suite prep exemplary. Extra attention to detail demonstrated.',    7, 0, NOW()-INTERVAL 7 DAY),
  (5, 5, 7, 55, 50, 45, 60, 52.50,'Room OOO. Score for partial clean attempt only.',                      5, 0, NOW()-INTERVAL 5 DAY),
  (6, 6, 1, 90, 92, 91, 89, 90.50,'VIP room cleared ahead of schedule. Excellent standard.',              7, 0, NOW()-INTERVAL 2 DAY),
  (7, 5, 4, 72, 74, 75, 70, 72.75,'Deep clean in progress — preliminary score. Re-score required.',       5, 1, NOW()-INTERVAL 1 DAY);

-- ── restocking_requisitions ───────────────────────────────
INSERT IGNORE INTO `restocking_requisitions`
  (id, items, requested_by_user_id, status, created_at)
VALUES
  (1,'[{"item_id":9,"name":"All-Purpose Cleaner","qty":10,"unit":"litres"},{"item_id":10,"name":"Disinfectant Spray","qty":12,"unit":"cans"}]',
   6,'completed', NOW()-INTERVAL 14 DAY),
  (2,'[{"item_id":1,"name":"Bath Towels","qty":40,"unit":"pieces"},{"item_id":2,"name":"Hand Towels","qty":60,"unit":"pieces"},{"item_id":3,"name":"Bed Sheets (King)","qty":20,"unit":"sets"}]',
   5,'completed', NOW()-INTERVAL 10 DAY),
  (3,'[{"item_id":5,"name":"Shampoo Bottles","qty":100,"unit":"bottles"},{"item_id":6,"name":"Conditioner Bottles","qty":100,"unit":"bottles"},{"item_id":7,"name":"Soap Bars","qty":200,"unit":"bars"}]',
   6,'in_progress',NOW()-INTERVAL 3 DAY),
  (4,'[{"item_id":9,"name":"All-Purpose Cleaner","qty":5,"unit":"litres"},{"item_id":8,"name":"Toilet Rolls","qty":200,"unit":"rolls"}]',
   7,'pending',   NOW()-INTERVAL 1 DAY);

-- ── low_stock_alerts ──────────────────────────────────────
INSERT IGNORE INTO `low_stock_alerts`
  (item_id, location, current_stock, min_threshold, status, acknowledged_by, acknowledged_at, escalated, created_at)
VALUES
  (9,  'housekeeping_store', 3,  8,  'active',       NULL, NULL,                     1, NOW()-INTERVAL 4 DAY),
  (10, 'housekeeping_store', 3,  10, 'active',       NULL, NULL,                     0, NOW()-INTERVAL 4 DAY),
  (1,  'housekeeping_store', 12, 20, 'acknowledged', 6,    NOW()-INTERVAL 9 DAY,     0, NOW()-INTERVAL 10 DAY),
  (5,  'housekeeping_store', 18, 50, 'acknowledged', 5,    NOW()-INTERVAL 8 DAY,     0, NOW()-INTERVAL 9 DAY),
  (3,  'housekeeping_store', 4,  10, 'resolved',     5,    NOW()-INTERVAL 7 DAY,     0, NOW()-INTERVAL 12 DAY);

-- ── inventory_change_log ──────────────────────────────────
INSERT IGNORE INTO `inventory_change_log`
  (room_type_id, date, old_virtual_max, new_virtual_max, changed_by_user_id, reason, created_at)
VALUES
  (1, DATE(NOW()-INTERVAL 30 DAY), 8,  10, 2,'2 renovated standard rooms added to available pool',           NOW()-INTERVAL 30 DAY),
  (2, DATE(NOW()-INTERVAL 21 DAY), 5,  7,  2,'Rooms 204 and 205 cleared from maintenance hold',              NOW()-INTERVAL 21 DAY),
  (3, DATE(NOW()-INTERVAL 14 DAY), 3,  4,  1,'Suite 303 added after soft refurbishment completion',          NOW()-INTERVAL 14 DAY),
  (1, DATE(NOW()-INTERVAL 7 DAY),  10, 9,  2,'Room 108 placed under temporary OOO for AC repair',            NOW()-INTERVAL 7 DAY),
  (3, DATE(NOW()-INTERVAL 3 DAY),  4,  3,  1,'Suite 302 removed from virtual pool — AC unit under repair',   NOW()-INTERVAL 3 DAY);

-- ── item_returns ──────────────────────────────────────────
INSERT IGNORE INTO `item_returns`
  (found_item_id, guest_id, return_method, return_address, shipping_cost, returned_at)
VALUES
  (5, 5,'pickup', NULL, 0.00, NOW()-INTERVAL 1 DAY),
  (4, 4,'pickup', NULL, 0.00, NOW()-INTERVAL 2 DAY);

-- ── replacement_review_flags ──────────────────────────────
INSERT IGNORE INTO `replacement_review_flags`
  (room_id, asset_id, emergency_count, flagged_at, reviewed)
VALUES
  (7,    1,   3, NOW()-INTERVAL 5 DAY, 0),
  (NULL, 8,   2, NOW()-INTERVAL 3 DAY, 0),
  (NULL, 4,   1, NOW()-INTERVAL 1 DAY, 0);

-- ── property_wide_alerts (correct schema: no title/severity/is_active cols) ─
INSERT IGNORE INTO `property_wide_alerts`
  (alert_type, message, triggered_by_work_order_id, status, created_at)
VALUES
  ('elevator_failure','Elevator B out of service — door malfunction. Use Elevator A only. Engineering ETA: 4 hrs.',           5,'active',   NOW()-INTERVAL 1 DAY),
  ('safety_hazard',   'Room 302 AC emitting burning smell. Room sealed, guests relocated. Facilities team on-site.',          1,'resolved', NOW()-INTERVAL 4 DAY),
  ('pool_maintenance','Pool closed 08:00-10:00 for routine filtration maintenance. Reopening 10:15.',                         7,'resolved', NOW()-INTERVAL 2 DAY),
  ('vip_arrival',     'VIP Arrival: Yuki Tanaka (Platinum) — Suite 301 today. Champagne and roses pre-arranged.',          NULL,'resolved', NOW()-INTERVAL 6 DAY);

-- ── preventative_schedules (correct schema — no start_time col) ──────────
INSERT IGNORE INTO `preventative_schedules`
  (work_order_id, asset_id, room_id, maintenance_type, scheduled_date, estimated_minutes, is_recurring, recurrence_frequency, next_due_date)
VALUES
  (4, 1, NULL,'hvac',      DATE(NOW()-INTERVAL 7 DAY),  120, 1,'quarterly', DATE(NOW()+INTERVAL 83 DAY)),
  (7, 8, NULL,'elevator',  DATE(NOW()-INTERVAL 14 DAY),  90, 1,'monthly',   DATE(NOW()+INTERVAL 16 DAY)),
  (4, 7, NULL,'electrical',DATE(NOW()-INTERVAL 30 DAY),  60, 1,'quarterly', DATE(NOW()+INTERVAL 60 DAY)),
  (7, 5, NULL,'plumbing',  DATE(NOW()-INTERVAL 60 DAY),  45, 1,'monthly',   CURDATE());

-- ── additional feedback from recent stays ─────────────────
INSERT IGNORE INTO `feedback`
  (reservation_id, guest_id, rating, comments, submitted_at, overall_score, flagged_for_qa)
VALUES
  (24, 1, 5,'Excellent as always. Room 102 spotless, staff exceptional. Concierge arranged last-minute table — brilliant.',  NOW()-INTERVAL 2 DAY, 95, 0),
  (25, 7, 4,'Comfortable for the conference. Room clean and quiet. Breakfast needs more vegan options.',                     NOW()-INTERVAL 1 DAY, 82, 0),
  (22, 5, 5,'Halal dining perfectly arranged, very attentive to our needs. Top-tier hospitality.',                           NOW()-INTERVAL 3 DAY, 92, 0),
  (21, 2, 3,'Anniversary stay lovely but housekeeping skipped day 3 without notice. Manager resolved it quickly.',           NOW()-INTERVAL 4 DAY, 71, 1);

-- ── housekeeper_performance (upsert with updated aggregates) ─────────────
INSERT INTO `housekeeper_performance`
  (housekeeper_id, avg_score, total_inspections, trend, updated_at)
VALUES
  (5, 76.92, 19,'stable',    NOW()),
  (6, 89.67, 22,'improving', NOW()),
  (7, 89.50, 16,'improving', NOW())
ON DUPLICATE KEY UPDATE
  avg_score         = VALUES(avg_score),
  total_inspections = VALUES(total_inspections),
  trend             = VALUES(trend),
  updated_at        = NOW();

-- ── minibar_logs — additional realistic consumption ───────
INSERT IGNORE INTO `minibar_logs`
  (reservation_id, item_id, quantity, unit_price, logged_by, logged_at)
VALUES
  (9,  1, 2, 3.00, 6, NOW()-INTERVAL 47 DAY),
  (9,  3, 2, 6.00, 6, NOW()-INTERVAL 46 DAY),
  (6,  3, 4, 6.00, 6, NOW()-INTERVAL 1 DAY),
  (6,  4, 2, 5.00, 6, NOW()-INTERVAL 1 DAY),
  (24, 1, 2, 3.00, 6, NOW()),
  (24, 2, 1, 4.00, 6, NOW()),
  (27, 1, 3, 3.00, 6, NOW()),
  (27, 3, 1, 6.00, 6, NOW()),
  (28, 3, 2, 6.00, 6, NOW()),
  (28, 2, 2, 4.00, 6, NOW());

