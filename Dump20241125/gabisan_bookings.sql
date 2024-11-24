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
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(11) NOT NULL,
  `fk_ferry_id` int(11) NOT NULL,
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
  `valid_id` varchar(255) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('confirmed','cancelled','pending') NOT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `user_id_idx` (`fk_user_id`),
  KEY `flight_id_idx` (`fk_ferry_id`),
  CONSTRAINT `fk_ferry_id` FOREIGN KEY (`fk_ferry_id`) REFERENCES `ferries` (`ferry_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_id` FOREIGN KEY (`fk_user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (12,20240006,7002,'Julius','Plasabas','Canonio','','2001-11-07','','Filipino','Brgy, Zaragosa Matalom, Leyte','student',0.00,'uploads/#bini (1).jpg',0.00,0.00,'2024-11-24 06:23:43','confirmed'),(13,20240006,7002,'Aiah','Plasabas','Arceta','','2001-01-27','','Filipino','Cebu','student',0.00,'uploads/#bini (1).jpg',0.00,0.00,'2024-11-24 06:26:53','confirmed'),(14,20240006,7004,'Mikha','Jana','Lim','','2001-07-11','','Filipino','Cebu','regular',100.00,'uploads/✿.jpg',0.00,0.00,'2024-11-24 06:42:27','confirmed'),(15,20240006,7002,'Julius','Plasabas','Canonio','','2001-07-11','','Filipino','Brgy, Zaragosa Matalom, Leyte','student',90.00,'uploads/bini (5).jpg',0.00,0.00,'2024-11-24 06:57:52','confirmed'),(16,20240006,7002,'Aiah','Jana','Arceta','','2001-01-27','','Filipino','Cebu','student',100.00,'',0.00,0.00,'2024-11-24 15:48:30','confirmed'),(17,20240006,7002,'Mikha','Jana','Lim','','2001-11-01','','Filipino','Cebu','student',100.00,'',0.00,0.00,'2024-11-24 16:00:58','confirmed'),(18,20240006,7001,'Mikha','Jana','Lim','','2001-11-07','','Filipino','Cebu','student',100.00,'',0.00,0.00,'2024-11-24 16:26:28','confirmed'),(19,20240006,7002,'Aiah','Jana','Arceta','','2001-02-01','','Filipino','Cebu','student',100.00,'',0.00,0.00,'2024-11-24 16:29:28','confirmed'),(20,20240006,7002,'Julius','Plasabas','Canonio','','2001-11-07','','Filipino','Brgy, Zaragosa Matalom, Leyte','student',100.00,'',0.00,0.00,'2024-11-24 16:31:05','confirmed'),(21,20240006,7003,'Aiah','Jana','Arceta','','2001-11-07','','Filipino','Cebu','student',0.00,'',0.00,0.00,'2024-11-24 16:57:30','confirmed'),(22,20240006,7003,'Julius','Plasabas','Canonio','','2001-11-07','','Filipino','Cebu','student',0.00,'',0.00,0.00,'2024-11-24 17:02:10','confirmed'),(23,20240006,7002,'Aiah','Jana','Arceta','','2001-01-27','','Filipino','Cebu','student',0.00,'',0.00,0.00,'2024-11-24 17:19:20','confirmed'),(24,20240006,7002,'Mikha','','Lim','','2001-02-01','','','Cebu','student',550.00,'',0.00,0.00,'2024-11-24 17:23:51','confirmed'),(25,20240006,7002,'Julius','','Canonio','','2001-11-07','','','Brgy, Zaragosa Matalom, Leyte','student',550.00,'',0.00,0.00,'2024-11-24 17:44:17','confirmed');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-25  7:12:26
