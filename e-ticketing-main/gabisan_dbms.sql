-- MySQL dump 10.13  Distrib 8.0.37, for Win64 (x86_64)
--
-- Host: localhost    Database: gabisan
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accommodation`
--

DROP TABLE IF EXISTS `accommodation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accommodation` (
  `accom_price_id` int(11) NOT NULL AUTO_INCREMENT,
  `accom_type` varchar(45) NOT NULL,
  PRIMARY KEY (`accom_price_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accommodation`
--

LOCK TABLES `accommodation` WRITE;
/*!40000 ALTER TABLE `accommodation` DISABLE KEYS */;
INSERT INTO `accommodation` VALUES (1,'Tourist A (Aircon)'),(2,'Tourist B (Aircon)'),(3,'Economy A'),(4,'Economy B'),(5,'Tourist C (Aircon)'),(6,'Economy C'),(7,'Deck A'),(8,'Deck B'),(9,'Tourist');
/*!40000 ALTER TABLE `accommodation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accommodation_prices`
--

DROP TABLE IF EXISTS `accommodation_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accommodation_prices` (
  `ferry_id` int(11) NOT NULL,
  `accom_id` int(11) NOT NULL,
  `price` decimal(9,2) NOT NULL,
  PRIMARY KEY (`ferry_id`,`accom_id`),
  KEY `fk_accom_id_idx` (`accom_id`),
  CONSTRAINT `fk_accom_id` FOREIGN KEY (`accom_id`) REFERENCES `accommodation` (`accom_price_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ferry_price_id` FOREIGN KEY (`ferry_id`) REFERENCES `ferries` (`ferry_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accommodation_prices`
--

LOCK TABLES `accommodation_prices` WRITE;
/*!40000 ALTER TABLE `accommodation_prices` DISABLE KEYS */;
INSERT INTO `accommodation_prices` VALUES (7001,1,550.00),(7001,3,420.00),(7001,4,400.00),(7001,5,500.00),(7002,1,550.00),(7002,5,500.00),(7002,7,420.00),(7002,8,400.00),(7003,3,650.00),(7003,4,550.00),(7003,9,700.00),(7004,1,450.00),(7004,4,350.00),(7004,6,370.00);
/*!40000 ALTER TABLE `accommodation_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_actions_log`
--

DROP TABLE IF EXISTS `admin_actions_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_actions_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` enum('add new ferry','delete ferry','add sail sched','update sail sched','delete sail sched','update user book','accept user book cancel','rejected user book cancel','confirmed user payment','add accommodation type','add accommodation price') NOT NULL,
  `target_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `admin_id_idx` (`admin_id`),
  CONSTRAINT `fk_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_actions_log`
--

LOCK TABLES `admin_actions_log` WRITE;
/*!40000 ALTER TABLE `admin_actions_log` DISABLE KEYS */;
INSERT INTO `admin_actions_log` VALUES (1,20240001,'add new ferry',7004,'2024-11-14 17:32:30'),(2,20240001,'add new ferry',7003,'2024-11-14 18:25:31'),(3,20240001,'',7003,'2024-11-19 01:17:02'),(4,20240001,'add new ferry',7003,'2024-11-19 01:17:02'),(5,20240001,'',7003,'2024-11-19 01:17:56'),(6,20240001,'add new ferry',7003,'2024-11-19 01:17:56'),(7,20240001,'',7003,'2024-11-19 01:30:51'),(8,20240001,'',7003,'2024-11-19 01:31:33'),(9,20240001,'',7003,'2024-11-19 01:31:33'),(10,20240001,'',7003,'2024-11-19 02:03:42'),(11,20240001,'',7003,'2024-11-19 02:03:42'),(12,20240001,'',7003,'2024-11-19 05:25:37'),(13,20240001,'',7003,'2024-11-19 05:49:12'),(14,20240001,'add new ferry',7004,'2024-11-19 06:30:48'),(15,20240001,'',7004,'2024-11-19 06:33:03'),(16,20240001,'',7001,'2024-11-19 14:40:26'),(17,20240001,'',7001,'2024-11-19 14:40:41'),(18,20240001,'',7001,'2024-11-19 14:40:54'),(19,20240001,'',7001,'2024-11-19 14:41:08'),(20,20240001,'',7002,'2024-11-19 14:41:24'),(21,20240001,'',7002,'2024-11-19 14:41:40'),(22,20240001,'',7002,'2024-11-19 14:41:54'),(23,20240001,'',7002,'2024-11-19 14:42:03'),(24,20240001,'',7004,'2024-11-19 14:42:15'),(25,20240001,'',7004,'2024-11-19 14:42:28'),(26,20240001,'',7004,'2024-11-19 14:42:39'),(27,20240001,'',7003,'2024-11-19 14:45:50'),(28,20240001,'',7003,'2024-11-19 14:46:00'),(29,20240001,'',7003,'2024-11-19 14:46:12');
/*!40000 ALTER TABLE `admin_actions_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(11) NOT NULL,
  `fk_ferry_id` int(11) NOT NULL,
  `departure` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `departure_date` date DEFAULT NULL,
  `first_name` varchar(45) NOT NULL,
  `middle_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `gender` varchar(45) NOT NULL,
  `birth_date` date NOT NULL,
  `civil_status` varchar(45) NOT NULL,
  `nationality` varchar(45) NOT NULL,
  `address` varchar(45) NOT NULL,
  `passenger_type` enum('regular','student','senior','pwd','child','infant') NOT NULL,
  `accom_price` decimal(10,2) NOT NULL,
  `valid_id` varchar(255) DEFAULT NULL,
  `discount` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('confirmed','cancelled','pending') NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `user_id_idx` (`fk_user_id`),
  KEY `flight_id_idx` (`fk_ferry_id`),
  CONSTRAINT `fk_ferry_id` FOREIGN KEY (`fk_ferry_id`) REFERENCES `ferries` (`ferry_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_id` FOREIGN KEY (`fk_user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (12,20240006,7002,'','',NULL,'Julius','Plasabas','Canonio','','2001-11-07','','Filipino','Brgy, Zaragosa Matalom, Leyte','student',0.00,'uploads/#bini (1).jpg',0.00,0.00,'2024-11-24 06:23:43','confirmed',NULL),(13,20240006,7002,'','',NULL,'Aiah','Plasabas','Arceta','','2001-01-27','','Filipino','Cebu','student',0.00,'uploads/#bini (1).jpg',0.00,0.00,'2024-11-24 06:26:53','confirmed',NULL),(14,20240006,7004,'','',NULL,'Mikha','Jana','Lim','','2001-07-11','','Filipino','Cebu','regular',100.00,'uploads/✿.jpg',0.00,0.00,'2024-11-24 06:42:27','confirmed',NULL),(15,20240006,7002,'','',NULL,'Julius','Plasabas','Canonio','','2001-07-11','','Filipino','Brgy, Zaragosa Matalom, Leyte','student',90.00,'uploads/bini (5).jpg',0.00,0.00,'2024-11-24 06:57:52','confirmed',NULL),(16,20240006,7002,'','',NULL,'Aiah','Jana','Arceta','','2001-01-27','','Filipino','Cebu','student',100.00,'',0.00,0.00,'2024-11-24 15:48:30','confirmed',NULL),(17,20240006,7002,'','',NULL,'Mikha','Jana','Lim','','2001-11-01','','Filipino','Cebu','student',100.00,'',0.00,0.00,'2024-11-24 16:00:58','confirmed',NULL),(18,20240006,7001,'','',NULL,'Mikha','Jana','Lim','','2001-11-07','','Filipino','Cebu','student',100.00,'',0.00,0.00,'2024-11-24 16:26:28','confirmed',NULL),(19,20240006,7002,'','',NULL,'Aiah','Jana','Arceta','','2001-02-01','','Filipino','Cebu','student',100.00,'',0.00,0.00,'2024-11-24 16:29:28','confirmed',NULL),(20,20240006,7002,'','',NULL,'Julius','Plasabas','Canonio','','2001-11-07','','Filipino','Brgy, Zaragosa Matalom, Leyte','student',100.00,'',0.00,0.00,'2024-11-24 16:31:05','confirmed',NULL),(21,20240006,7003,'','',NULL,'Aiah','Jana','Arceta','','2001-11-07','','Filipino','Cebu','student',0.00,'',0.00,0.00,'2024-11-24 16:57:30','confirmed',NULL),(22,20240006,7003,'','',NULL,'Julius','Plasabas','Canonio','','2001-11-07','','Filipino','Cebu','student',0.00,'',0.00,0.00,'2024-11-24 17:02:10','confirmed',NULL),(23,20240006,7002,'','',NULL,'Aiah','Jana','Arceta','','2001-01-27','','Filipino','Cebu','student',0.00,'',0.00,0.00,'2024-11-24 17:19:20','confirmed',NULL),(24,20240006,7002,'','',NULL,'Mikha','','Lim','','2001-02-01','','','Cebu','student',550.00,'',0.00,0.00,'2024-11-24 17:23:51','confirmed',NULL),(25,20240006,7002,'','',NULL,'Julius','','Canonio','','2001-11-07','','','Brgy, Zaragosa Matalom, Leyte','student',550.00,'',0.00,0.00,'2024-11-24 17:44:17','confirmed',NULL),(26,20240006,7002,'','',NULL,'Julius','','Canonio','','2001-11-07','','','Cebu','student',0.00,'',0.00,0.00,'2024-11-25 14:36:23','confirmed',NULL),(27,20240006,7002,'','',NULL,'Julius','','Canonio','','2001-11-07','','','Cebu','student',0.00,'',0.00,0.00,'2024-11-25 14:55:35','confirmed',NULL),(28,20240006,7002,'','',NULL,'Aiah','','Arceta','','2001-01-27','','','Cebu','student',0.00,'',0.00,0.00,'2024-11-25 14:56:54','confirmed',NULL),(29,20240006,7003,'','',NULL,'Julius','','Canonio','','2001-11-07','','','Cebu','student',585.00,'',0.00,0.00,'2024-11-25 15:52:37','confirmed',NULL),(30,20240006,7002,'','',NULL,'Mikha','','Lim','','2001-03-12','','','Cebu','',550.00,'',0.00,0.00,'2024-11-25 16:14:41','confirmed',NULL),(31,20240006,7001,'','',NULL,'Aiah','','Canonio','','2001-02-11','','','Cebu','',550.00,'',0.00,0.00,'2024-11-25 16:33:29','confirmed',NULL),(32,20240006,7002,'','',NULL,'Mikha','','Lim','','2001-02-01','','','Cebu','',550.00,'',0.00,0.00,'2024-11-25 16:34:44','confirmed',NULL),(33,20240006,7001,'','',NULL,'Mikha','','Canonio','','1998-12-09','','','Cebu','',550.00,'',0.00,0.00,'2024-11-25 16:36:23','confirmed',NULL),(34,20240006,7001,'','',NULL,'Julius','','Canonio','','2001-11-07','','','Cebu','',550.00,'',0.00,0.00,'2024-11-25 16:54:25','confirmed',NULL),(35,20240006,7002,'','',NULL,'Mikha','','Lim','','1999-02-12','','','Cebu','student',0.00,'',0.00,0.00,'2024-11-25 16:58:35','confirmed',NULL),(36,20240006,7002,'','',NULL,'Julius','','Canonio','','2001-11-07','','','Cebu','student',495.00,'',0.00,0.00,'2024-11-25 16:59:49','confirmed',NULL),(37,20240006,7002,'','',NULL,'Mikha','','Arceta','','1999-02-11','','','Cebu','',550.00,'',0.00,0.00,'2024-11-25 17:02:09','confirmed',NULL),(38,20240006,7002,'','',NULL,'Mikha','','Arceta','','2001-11-07','','','Cebu','',550.00,'',0.00,0.00,'2024-11-25 17:04:02','confirmed',NULL),(39,20240006,7001,'','',NULL,'Mikha','','Arceta','','2001-02-11','','','Cebu','student',495.00,'',0.00,0.00,'2024-11-25 17:05:47','confirmed',NULL),(40,20240006,7001,'','',NULL,'Gwen','','Apuli','','2001-11-07','','','Cebu','senior',440.00,'',0.00,0.00,'2024-11-26 05:00:26','confirmed',NULL),(41,20240006,7001,'','',NULL,'Julius','','Canonio','','2001-11-07','','','Brgy, Zaragosa Matalom, Leyte','student',495.00,'',0.00,0.00,'2024-11-26 05:54:50','confirmed',NULL),(42,20240006,7001,'','',NULL,'Aiah','','Arceta','','2001-11-07','','','Cebu','student',495.00,'',0.00,0.00,'2024-11-26 05:54:50','confirmed',NULL),(43,20240006,7004,'','',NULL,'Cedric','','Pada','','2000-05-15','','','Brgy, Zaragosa Matalom, Leyte','student',405.00,'',0.00,0.00,'2024-11-27 12:24:28','confirmed',NULL),(44,20240006,7002,'','',NULL,'Julius','','Canonio','','2001-07-11','','','Brgy, Zaragosa Matalom, Leyte','student',495.00,'',0.00,0.00,'2024-11-30 05:52:08','confirmed',NULL),(45,20240006,7002,'','',NULL,'Julius','','Canonio','','2001-07-11','','','Brgy, Zaragosa Matalom, Leyte','student',495.00,'',0.00,0.00,'2024-11-30 06:08:32','confirmed',NULL),(46,20240006,7002,'','',NULL,'Aiah','','Arceta','','2001-01-27','','','Cebu','student',495.00,'',0.00,0.00,'2024-11-30 06:08:32','confirmed',NULL),(47,20240006,7002,'','',NULL,'Gwen','Gwenny','Apuli','Female','0000-00-00','Single','Filipino','Cebu','regular',550.00,'',0.00,550.00,'2024-11-30 06:21:47','pending',NULL),(48,20240006,7003,'','',NULL,'Mikha','Jana','Lim','Female','2001-02-01','Married','Filipino','Cebu','pwd',700.00,'',0.00,595.00,'2024-11-30 06:34:23','pending',NULL),(49,20240006,7001,'','',NULL,'Aiah','Jana','Arceta','Female','2001-01-27','Married','Filipino','Cebu','student',400.00,'',0.00,360.00,'2024-11-30 08:19:52','pending',NULL),(50,20240006,7004,'','',NULL,'Cedric','Vercide','Pada','Male','2001-02-25','Divorced','Filipino','Brgy, Zaragosa Matalom, Leyte','regular',350.00,'',0.00,350.00,'2024-11-30 08:43:17','pending',NULL),(51,20240006,7001,'','',NULL,'Julius','Plasabas','Canonio','Male','2001-07-11','Single','Filipino','Brgy, Zaragosa Matalom, Leyte','student',550.00,'',0.00,495.00,'2024-11-30 08:46:14','pending',NULL),(52,20240006,7001,'','',NULL,'Cedric','Vercide','Pada','Male','2001-05-05','Single','Filipino','Cebu','student',550.00,'',0.00,495.00,'2024-11-30 08:46:14','pending',NULL),(53,20240006,7001,'','',NULL,'Aiah','Jana','Arceta','Female','2001-01-27','Single','Filipino','Cebu','student',550.00,'',0.00,495.00,'2024-11-30 09:15:31','pending',NULL),(54,20240006,7004,'','',NULL,'Julius','Plasabas','Canonio','Male','2001-07-11','Single','Filipino','Brgy, Zaragosa Matalom, Leyte','student',350.00,'',0.00,315.00,'2024-11-30 09:32:46','pending',NULL),(55,20240006,7001,'','',NULL,'Julius','Plasabas','Canonio','Male','2001-07-11','Single','Filipino','Cebu','student',550.00,'',0.00,495.00,'2024-11-30 13:30:13','pending',NULL),(56,20240006,7004,'','',NULL,'Mikha','Jana','Lim','Female','2001-05-05','Single','Filipino','Cebu','student',350.00,'',0.00,315.00,'2024-11-30 14:05:34','pending',NULL),(57,20240006,7002,'Hilongos','Cebu','2024-12-12','Julius','Plasabas','Canonio','Male','1111-11-11','','Filipino','','',0.00,'',0.20,0.00,'2024-12-09 17:40:11','pending','3023082279114'),(58,20240006,7002,'Hilongos','Cebu','2024-12-17','Julius','Plasabas','Canonio','Male','2000-12-11','','Filipino','','',500.00,'',0.20,400.00,'2024-12-09 18:09:28','pending','3023082279116'),(59,20240006,7002,'Hilongos','Cebu','2024-12-11','Julius','Plasabas','Canonio','Male','2000-02-11','','Filipino','','',550.00,'',0.20,440.00,'2024-12-09 18:30:03','pending','3023082279119'),(60,20240006,7002,'Hilongos','Cebu','2024-12-12','Julius','Plasabas','Canonio','Male','2000-11-11','','Filipino','','',550.00,'',0.20,440.00,'2024-12-09 18:31:25','pending','3023082279122'),(61,20240006,7002,'Hilongos','Cebu','2024-12-13','Mikha','Jana','Arceta','Female','2001-11-11','','Filipino','','',550.00,'',0.20,440.00,'2024-12-09 18:41:06','pending','3023082279125'),(62,20240006,7002,'Hilongos','Cebu','2024-12-12','Julius','Plasabas','Canonio','Male','1111-11-11','','Filipino','','',550.00,'',0.20,440.00,'2024-12-09 19:06:00','pending','3023082279110'),(63,20240006,7002,'Hilongos','Cebu','2024-12-20','Julius','Plasabas','Canonio','Male','1111-11-11','','Filipino','','',550.00,'',0.50,275.00,'2024-12-09 19:11:52','pending','3023082279121'),(64,20240006,7004,'Hilongos','Cebu','2024-12-14','Julius','Plasabas','Canonio','Male','2001-07-11','','Filipino','','',370.00,'',0.20,296.00,'2024-12-11 23:03:24','pending','3123082279114'),(65,20240006,7001,'Hilongos','Cebu','2024-12-14','Julius','Plasabas','Canonio','Male','2001-07-11','','Filipino','','',400.00,'',0.20,320.00,'2024-12-11 23:15:16','pending','3423082279114'),(66,20240006,7002,'Hilongos','Cebu','2024-12-26','Julius','Plasabas','Canonio','Male','2001-07-11','','Filipino','','',400.00,'',0.20,720.00,'2024-12-11 23:26:41','pending','3023082279410'),(67,20240006,7002,'Hilongos','Cebu','2024-12-26','Mikha','Plasabas','Arceta','Female','2001-07-27','','Filipino','','',400.00,'',0.00,720.00,'2024-12-11 23:26:41','pending','3023082279410'),(68,20240006,7002,'Hilongos','Cebu','2024-12-14','Julius','Plasabas','Canonio','Male','2001-07-11','','Filipino','','',420.00,'',0.50,210.00,'2024-12-11 23:40:20','pending','3023082269114'),(69,20240006,7001,'Hilongos','Cebu','2024-12-26','Julius','Plasabas','Canonio','Male','2001-07-11','','Filipino','','',400.00,'',0.20,320.00,'2024-12-14 16:34:44','cancelled','3023082279614'),(70,20240006,7002,'Hilongos','Cebu','2024-12-16','Aiah','Jana','Arceta','Female','2001-07-11','Married','Filipino','Cebu','student',500.00,'0',0.20,400.00,'2024-12-12 02:12:41','cancelled','3023082279814'),(71,20240006,7003,'Hilongos','Cebu','2024-12-21','Julius','Plasabas','Canonio','Male','2001-07-11','Single','Filipino','Brgy, Zaragosa Matalom, Leyte','student',550.00,'0',0.20,440.00,'2024-12-12 02:12:39','cancelled','3023085279114'),(72,20240006,7003,'Hilongos','Cebu','2024-12-27','Julius','Plasabas','Canonio','Male','2000-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',650.00,'0',0.20,520.00,'2024-12-12 02:12:38','cancelled','3028082279114'),(73,20240006,7001,'Hilongos','Cebu','2024-12-14','Julius','Plasabas','Canonio','Male','2001-07-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','regular',500.00,'0',0.00,500.00,'2024-12-12 02:12:35','cancelled','3223082279114'),(74,20240006,7002,'Hilongos','Cebu','2024-12-13','Mikha','Jana','Lim','Female','2001-07-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',550.00,'0',0.20,440.00,'2024-12-12 02:11:43','cancelled','3023082279514'),(75,20240006,7002,'Hilongos','Cebu','2024-12-28','Julius','Plasabas','Canonio','Male','2201-07-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','child',400.00,'0',0.50,400.00,'2024-12-12 02:47:11','cancelled','3723082279114'),(76,20240006,7002,'Hilongos','Cebu','2024-12-28','Mikha','Plasabas','Lim','Male','2000-02-07','Widowed','Filipino','Cebu','child',400.00,'0',0.50,400.00,'2024-12-12 02:47:12','cancelled','3723082279114'),(77,20240006,7002,'Hilongos','Cebu','2024-12-27','Julius','Plasabas','Canonio','Male','2001-07-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,'0',0.20,320.00,'2024-12-14 15:20:23','cancelled','3013082279114'),(78,20240006,7003,'Hilongos','Cebu','2024-12-20','Cedric','Vercide','Pada','Male','2001-07-11','Widowed','Filipino','Brgy, Zaragosa Matalom, Leyte','student',700.00,'0',0.20,560.00,'2024-12-14 14:24:20','cancelled','3023083279114'),(79,20240006,7003,'Hilongos','Cebu','2024-12-16','Julius','Plasabas','Canonio','Male','2001-07-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',700.00,'0',0.20,1120.00,'2024-12-14 15:12:07','cancelled','3923082279114'),(80,20240006,7003,'Hilongos','Cebu','2024-12-16','Aiah','Jana','Arceta','Male','2001-01-27','Single','Filipino','Cebu','student',700.00,'0',0.20,1120.00,'2024-12-14 15:12:42','cancelled','3923082279114'),(81,20240006,7001,'Hilongos','Cebu','2024-12-24','Cedric','Vercide','Pada','Male','2000-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',420.00,'0',0.20,336.00,'2024-12-14 16:52:49','pending','3023088279114'),(82,20240006,7002,'Hilongos','Cebu','2024-12-25','Julius','Plasabas','Canonio','Male','2000-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,'0',0.20,320.00,'2024-12-14 16:54:21','pending','9023082279114'),(83,20240006,7003,'Hilongos','Cebu','2024-12-18','Gwen','Gwenny','Apuli','Female','2000-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',650.00,NULL,0.20,520.00,'2024-12-14 17:01:30','pending','1234567891234'),(84,20240006,7002,'Hilongos','Cebu','2024-12-18','Mikha','Jana','Lim','Male','2001-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,'0',0.20,320.00,'2024-12-14 17:05:27','pending','7894561230245'),(85,20240006,7002,'Hilongos','Cebu','2024-12-18','adsa','asdasd','asdsadsa','Male','1111-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,'0',0.20,320.00,'2024-12-14 17:07:26','pending','1234567890528'),(86,20240006,7002,'Hilongos','Cebu','2024-12-26','Maraiah','Queen','Arceta','Female','2001-01-27','Married','Filipino','Cebu','student',400.00,'0',0.20,320.00,'2024-12-15 01:49:46','pending','5457845213695'),(87,20240006,7002,'Hilongos','Cebu','2024-12-18','Maraiah','Queen','Arceta','Female','2001-01-27','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',550.00,'0',0.00,550.00,'2024-12-15 02:00:49','pending','2587469325148'),(88,20240006,7002,'Hilongos','Cebu','2024-12-18','Mikha','Jana','Lim','Female','2001-01-27','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',550.00,'0',0.00,1100.00,'2024-12-15 02:00:49','pending','2587469325148'),(89,20240006,7003,'Hilongos','Cebu','2024-12-23','John','Carlo','Laude','Male','2001-01-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','child',550.00,'0',0.00,550.00,'2024-12-15 02:08:46','pending','0215489654235'),(90,20240006,7003,'Hilongos','Cebu','2024-12-24','Philip','Palca','Golo','Male','2001-02-25','Single','Filipino','Brgy, Zaragosa Matalom, Leyte','senior',550.00,'0',0.00,550.00,'2024-12-15 02:12:30','pending','8564582547965'),(92,20240006,7001,'Hilongos','Cebu','2024-12-24','Julius','Plasabas','Canonio','Male','2001-07-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,'0',0.00,400.00,'2024-12-15 02:40:02','pending','2564893254120'),(94,20240006,7003,'Hilongos','Cebu','2024-12-17','Julius','Plasabas','Canonio','Male','2000-02-07','Single','Filipino','Brgy, Zaragosa Matalom, Leyte','student',550.00,'0',0.00,550.00,'2024-12-15 03:10:03','pending','8459632145247'),(95,20240006,7003,'Hilongos','Cebu','2024-12-25','Mikha','Jana','Lim','Male','2000-11-11','Married','Filipino','Cebu','student',550.00,'0',0.00,550.00,'2024-12-15 04:05:11','cancelled','8549647523654'),(99,20240006,7002,'Hilongos','Cebu','2024-12-17','Mikha','Jana','Lim','Female','2000-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','child',400.00,'0',0.00,400.00,'2024-12-15 04:09:40','pending','2547896315423'),(100,20240006,7002,'Hilongos','Cebu','2024-12-18','Aiah','Queen','Arceta','Female','2000-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,'0',0.20,320.00,'2024-12-15 04:13:50','pending','8547965214326'),(101,20240006,7002,'Hilongos','Cebu','2024-12-25','Gwen','Gwenny','Apuli','Female','2000-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,NULL,0.20,320.00,'2024-12-15 04:21:02','pending','8547965412365'),(102,20240006,7002,'Hilongos','Cebu','2024-12-24','Julius','Plasabas','Canonio','Male','2001-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,NULL,0.20,320.00,'2024-12-15 04:23:53','pending','9854751236545'),(103,20240006,7003,'Hilongos','Cebu','2024-12-25','Cedric','Vercide','Pada','Male','1999-11-11','Single','Filipino','Brgy, Zaragosa Matalom, Leyte','student',650.00,'0',0.20,520.00,'2024-12-15 04:33:48','pending','8954621458962'),(104,20240006,7002,'Hilongos','Cebu','2024-12-26','Aiah','Jana','Arceta','Female','2001-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,'0',0.20,320.00,'2024-12-15 04:41:00','pending','8654123025968'),(105,20240006,7002,'Hilongos','Cebu','2024-12-26','Julius','Plasabas','Canonio','Male','2001-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','regular',400.00,'0',0.00,400.00,'2024-12-15 04:48:07','pending','8954126539654'),(106,20240006,7002,'Hilongos','Cebu','2024-12-25','Julius','Plasabas','Canonio','Male','2001-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,'0',0.20,320.00,'2024-12-15 05:02:43','pending','5478542125694'),(107,20240006,7002,'Hilongos','Cebu','2024-12-25','Julius','Plasabas','Canonio','Male','2001-11-11','Single','Filipino','Brgy, Zaragosa Matalom, Leyte','student',400.00,NULL,0.20,320.00,'2024-12-15 05:09:15','pending','8547125632014'),(108,20240006,7003,'Hilongos','Cebu','2024-12-24','Aiah','Jana','Arceta','Female','2001-11-11','Married','Filipino','Cebu','child',550.00,NULL,5.00,-2200.00,'2024-12-15 05:14:35','pending','8596452136542'),(109,20240006,7003,'Hilongos','Cebu','2024-12-25','Philip','Pagasian','Golo','Male','2001-11-11','Married','Filipino','Caridad','child',550.00,'0',0.50,275.00,'2024-12-15 06:44:02','cancelled','3023082279190'),(110,20240006,7003,'Hilongos','Cebu','2024-12-26','Maraiah','Queen','Arceta','Female','2001-11-11','Married','Filipino','Brgy, Zaragosa Matalom, Leyte','student',650.00,NULL,0.20,520.00,'2024-12-15 06:16:20','cancelled','5632147895642');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cancellation`
--

DROP TABLE IF EXISTS `cancellation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cancellation` (
  `cancellation_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_booking_id` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `cancellation_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reason` text NOT NULL,
  PRIMARY KEY (`cancellation_id`),
  KEY `booking_id_idx` (`fk_booking_id`),
  CONSTRAINT `fk_cancellation_booking_id` FOREIGN KEY (`fk_booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cancellation`
--

LOCK TABLES `cancellation` WRITE;
/*!40000 ALTER TABLE `cancellation` DISABLE KEYS */;
/*!40000 ALTER TABLE `cancellation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ferries`
--

DROP TABLE IF EXISTS `ferries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ferries` (
  `ferry_id` int(11) NOT NULL AUTO_INCREMENT,
  `ferry_name` varchar(45) NOT NULL,
  PRIMARY KEY (`ferry_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7005 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ferries`
--

LOCK TABLES `ferries` WRITE;
/*!40000 ALTER TABLE `ferries` DISABLE KEYS */;
INSERT INTO `ferries` VALUES (7001,'MV GLORIA FIVE'),(7002,'MV GLORIA THREE'),(7003,'MV GLORIA G-1'),(7004,'MV GLORIA TWO');
/*!40000 ALTER TABLE `ferries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ferry_schedule`
--

DROP TABLE IF EXISTS `ferry_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ferry_schedule` (
  `ferry_id` int(11) NOT NULL,
  `departure_port` varchar(45) NOT NULL,
  `arrival_port` varchar(45) NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  KEY `fk_ferry_schedule_id_idx` (`ferry_id`),
  CONSTRAINT `fk_ferry_schedule_id` FOREIGN KEY (`ferry_id`) REFERENCES `ferries` (`ferry_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ferry_schedule`
--

LOCK TABLES `ferry_schedule` WRITE;
/*!40000 ALTER TABLE `ferry_schedule` DISABLE KEYS */;
INSERT INTO `ferry_schedule` VALUES (7001,'Hilongos','Cebu','21:00:00','03:00:00','active'),(7002,'Hilongos','Cebu','21:00:00','03:00:00','active'),(7003,'Hilongos','Cebu','08:30:00','14:30:00','active'),(7004,'Hilongos','Cebu','21:30:00','04:30:00','active');
/*!40000 ALTER TABLE `ferry_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_method` enum('credit_card','debit_card','paypal') NOT NULL,
  `status` enum('completed','refunded') NOT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `booking_id_idx` (`fk_booking_id`),
  CONSTRAINT `fk_booking_id` FOREIGN KEY (`fk_booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sail_history`
--

DROP TABLE IF EXISTS `sail_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sail_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `ferry_id` int(11) NOT NULL,
  `action` enum('booked','cancelled','complete') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`history_id`),
  KEY `user_id_idx` (`user_id`),
  KEY `ferry_id_idx` (`ferry_id`),
  KEY `booking_id_idx` (`booking_id`),
  CONSTRAINT `fk_sailhistory_booking_id` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sailhistory_ferry_id` FOREIGN KEY (`ferry_id`) REFERENCES `ferries` (`ferry_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sailhistory_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sail_history`
--

LOCK TABLES `sail_history` WRITE;
/*!40000 ALTER TABLE `sail_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `sail_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_booking_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL,
  `ticket_number` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `passenger_type` enum('regular','student','senior','pwd','child','infant') NOT NULL,
  `accom_price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `departure` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `departure_date` date NOT NULL,
  `reference_number` varchar(255) NOT NULL,
  `ticket_status` enum('issued','cancelled','used') NOT NULL,
  `issue_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `valid_until` date NOT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `fk_booking_id` (`fk_booking_id`),
  KEY `fk_user_id` (`fk_user_id`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`fk_booking_id`) REFERENCES `bookings` (`booking_id`),
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`fk_user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `acc_type` enum('admin','customer') NOT NULL,
  `email` varchar(45) NOT NULL,
  `phone_num` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20240007 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (20240001,'admin','$2y$10$1w0MHqEyeSbCzB/VJJMWHOb0mf4vC7kpWiEW7g','admin','admin@gmail.com','09560051733','2024-11-14 01:48:08',NULL,NULL),(20240002,'admin1','$2y$10$U2xRwjV.SKJfUaHch3LhLuJxqKihsndxVhk0bd','admin','admin45@gmail.com','09123456789','2024-11-14 01:51:02',NULL,NULL),(20240003,'Percival','$2y$10$MsI7GQE0n5HjT/ixDFW3TObnS63MOfpW/OR45Z','customer','percival@gmail.com','09123456789','2024-11-14 16:08:58',NULL,NULL),(20240004,'Ceejay','$2y$10$wDTCLGXosyyzZtCoMMNVY.0A84jGYA8bbV6VJe','customer','ceejay@gmail.com','09123456789','2024-11-18 08:52:51',NULL,NULL),(20240005,'Julius','$2y$10$r/nISyrX9r6gI59.neAp3OIbgquO0Hp3bjBrbww.DFn.loID3qa.G','customer','julius@gmail.com','09987654321','2024-11-18 09:23:52',NULL,NULL),(20240006,'aiah','$2y$10$23j85tWE5dvIt7mkXYiFEu.tCl5DMTx/spBNT7veIqkHX2qOHrV06','customer','arceta@gmail.com','09757723864','2024-12-05 10:53:46','2024-12-05 10:53:46',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-15 15:16:06
