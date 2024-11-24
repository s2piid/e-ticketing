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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-25  7:12:27
