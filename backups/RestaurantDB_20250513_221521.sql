-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: RestaurantDB
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Pizza',1),(3,'Drinks',2),(4,'Desserts',3),(5,'Appetizer',0);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer` (
  `customer_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES (1,'yannis','yanismm14@hotmail.com','$2y$10$pkFOogbQqXx7mK/2slVX4euWZ3MsVqRARGZXYQ/QdBeQVdQG5Xnw.','38877927','gv 972 road 4308 block 943','2025-03-24 12:47:01','2025-04-06 13:21:54'),(4,'drake','drake@gmail.com','$2y$10$J//L.tYnIn/0lLpIdiYHye9IK8eJAaxep0Uu9QspDcdneLMqFe.qi','38877927','gv 972 road 4308 block 943','2025-03-24 13:33:36','2025-03-24 13:33:36'),(5,'abdulla','abdulla@gmail.com','$2y$10$WY9Z8sgsfemum63cWnNRoOUme7LOe0VT0c6ZpsyMI76qbKnhAMnRi','38877927','gv 972 road 4308 block 943','2025-03-25 10:39:36','2025-03-25 10:39:36'),(6,'james','james@hotmail.com','$2y$10$Bh7rN8fc0FiihBh6EAL5rOnNo/r.ErDbvE.Sqc8a9MrQbyWG7pADK','38877927','house 972, block 8','2025-04-28 14:23:50','2025-04-28 14:23:50'),(7,'yanis123','yanismm14444@hotmail.com','$2y$10$qiPDjjOEnJP8vGJwJiUMb.iBJemOPCA2EpRR1wRdGSjt/ZY485lHG','38877927','gv 972 road 4308 block 943','2025-05-03 15:15:44','2025-05-03 15:15:44'),(8,'aaron','aaron@hotmail.com','$2y$10$5SfqBcQ.TnYOaT1riLAX8uR/vbYQ/J/geJxBAj8y8ZacvrWVIeNKu','38877927','gv 972 road 4308 block 943','2025-05-08 15:32:57','2025-05-08 15:32:57');
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_logins`
--

DROP TABLE IF EXISTS `customer_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_logins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `login_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `customer_logins_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_logins`
--

LOCK TABLES `customer_logins` WRITE;
/*!40000 ALTER TABLE `customer_logins` DISABLE KEYS */;
INSERT INTO `customer_logins` VALUES (1,1,'2025-05-07 16:29:36'),(2,1,'2025-05-07 17:06:54'),(3,1,'2025-05-07 17:14:25'),(4,1,'2025-05-07 17:45:14'),(5,1,'2025-05-07 17:46:29'),(6,1,'2025-05-07 17:48:46'),(7,1,'2025-05-07 17:49:47'),(8,1,'2025-05-07 17:50:08'),(9,1,'2025-05-07 17:51:50'),(10,1,'2025-05-07 17:52:55'),(11,1,'2025-05-07 17:52:59'),(12,1,'2025-05-07 17:53:20'),(13,1,'2025-05-07 17:58:26'),(14,1,'2025-05-07 18:00:48'),(15,1,'2025-05-07 18:01:52'),(16,1,'2025-05-07 18:03:17'),(17,1,'2025-05-07 18:04:24'),(18,1,'2025-05-07 18:05:52'),(19,1,'2025-05-07 18:06:30'),(20,1,'2025-05-07 18:11:23'),(21,1,'2025-05-07 18:12:31'),(22,1,'2025-05-07 18:14:04'),(23,1,'2025-05-07 18:14:48'),(24,1,'2025-05-07 18:17:48'),(25,1,'2025-05-07 18:18:58'),(26,1,'2025-05-07 18:19:30'),(27,1,'2025-05-07 18:23:00'),(28,1,'2025-05-07 18:24:35'),(29,1,'2025-05-07 18:24:36'),(30,1,'2025-05-07 18:24:43'),(31,1,'2025-05-07 18:25:14'),(32,1,'2025-05-07 18:25:19'),(33,1,'2025-05-07 18:26:21'),(34,1,'2025-05-07 18:26:48'),(35,1,'2025-05-08 13:30:14'),(36,1,'2025-05-08 14:44:36'),(37,1,'2025-05-08 15:24:36'),(38,8,'2025-05-08 15:33:54'),(39,8,'2025-05-08 15:35:22'),(40,8,'2025-05-08 15:35:23'),(41,8,'2025-05-08 15:35:24'),(42,8,'2025-05-08 15:45:14'),(43,8,'2025-05-08 15:48:56'),(44,8,'2025-05-08 15:51:24'),(45,1,'2025-05-09 12:49:27'),(46,1,'2025-05-09 12:49:30'),(47,1,'2025-05-09 12:49:56'),(48,1,'2025-05-09 12:50:12'),(49,1,'2025-05-09 12:50:56'),(50,1,'2025-05-09 12:51:17'),(51,1,'2025-05-09 12:51:56'),(52,1,'2025-05-09 12:52:54'),(53,1,'2025-05-09 12:53:55'),(54,1,'2025-05-09 12:54:55'),(55,1,'2025-05-09 12:55:55'),(56,1,'2025-05-09 12:56:55'),(57,1,'2025-05-09 12:57:55'),(58,1,'2025-05-09 12:58:56'),(59,1,'2025-05-09 12:59:56'),(60,1,'2025-05-09 13:00:56'),(61,1,'2025-05-09 13:01:54'),(62,1,'2025-05-09 13:02:55'),(63,1,'2025-05-09 13:03:55'),(64,1,'2025-05-09 13:04:55'),(65,1,'2025-05-09 13:05:55'),(66,1,'2025-05-09 13:06:55'),(67,1,'2025-05-09 13:07:45'),(68,1,'2025-05-09 13:07:56'),(69,1,'2025-05-09 13:08:56'),(70,1,'2025-05-09 13:09:56'),(71,1,'2025-05-09 13:10:54'),(72,1,'2025-05-09 13:11:55'),(73,1,'2025-05-09 13:12:55'),(74,1,'2025-05-09 13:13:55'),(75,1,'2025-05-09 13:14:55'),(76,1,'2025-05-09 13:15:55'),(77,1,'2025-05-09 13:16:56'),(78,1,'2025-05-09 13:17:56'),(79,1,'2025-05-09 13:18:56'),(80,1,'2025-05-09 13:19:54'),(81,1,'2025-05-09 13:20:55'),(82,1,'2025-05-09 13:21:55'),(83,1,'2025-05-09 13:22:55'),(84,1,'2025-05-09 13:23:55'),(85,1,'2025-05-09 13:24:55'),(86,1,'2025-05-09 13:25:56'),(87,1,'2025-05-09 13:26:56'),(88,1,'2025-05-09 13:27:54'),(89,1,'2025-05-09 13:28:55'),(90,1,'2025-05-09 13:29:55'),(91,1,'2025-05-09 13:30:55'),(92,1,'2025-05-09 13:31:55'),(93,1,'2025-05-09 13:32:06'),(94,1,'2025-05-09 13:36:50'),(95,1,'2025-05-09 13:36:54'),(96,1,'2025-05-09 13:37:16'),(97,1,'2025-05-09 15:11:57'),(98,1,'2025-05-09 15:12:01'),(99,1,'2025-05-09 15:12:34'),(100,1,'2025-05-10 11:56:50'),(101,1,'2025-05-10 12:01:09'),(102,1,'2025-05-10 12:31:03'),(103,1,'2025-05-11 10:51:41'),(104,1,'2025-05-11 10:56:12'),(105,1,'2025-05-11 10:56:43'),(106,1,'2025-05-11 11:08:52'),(107,1,'2025-05-11 11:20:56'),(108,1,'2025-05-11 11:42:56'),(109,1,'2025-05-11 11:43:19'),(110,1,'2025-05-11 16:45:32'),(111,1,'2025-05-11 16:46:07'),(112,1,'2025-05-11 19:30:20'),(113,1,'2025-05-11 19:38:30'),(114,1,'2025-05-11 19:38:33'),(115,1,'2025-05-11 19:39:05'),(116,1,'2025-05-11 19:52:35'),(117,1,'2025-05-11 19:52:38'),(118,1,'2025-05-11 19:53:56'),(119,1,'2025-05-11 19:54:03'),(120,1,'2025-05-11 20:09:40'),(121,1,'2025-05-11 20:12:56'),(122,1,'2025-05-11 20:12:59'),(123,1,'2025-05-11 20:15:30'),(124,1,'2025-05-12 08:45:24'),(125,1,'2025-05-12 09:38:32'),(126,1,'2025-05-12 09:38:41'),(127,1,'2025-05-12 09:38:46'),(128,1,'2025-05-12 09:38:51'),(129,1,'2025-05-12 09:38:56'),(130,1,'2025-05-12 10:09:50'),(131,1,'2025-05-12 10:09:52'),(132,1,'2025-05-12 10:23:58'),(133,1,'2025-05-12 10:24:01'),(134,1,'2025-05-12 14:24:22'),(135,1,'2025-05-12 17:17:02'),(136,1,'2025-05-12 17:17:17'),(137,1,'2025-05-12 17:18:00'),(138,1,'2025-05-12 17:18:14'),(139,1,'2025-05-12 17:18:20'),(140,1,'2025-05-12 17:18:31'),(141,1,'2025-05-12 17:44:07'),(142,1,'2025-05-13 12:00:57'),(143,1,'2025-05-13 12:01:14'),(144,1,'2025-05-13 12:01:18'),(145,1,'2025-05-13 12:01:28'),(146,1,'2025-05-13 12:02:02'),(147,1,'2025-05-13 12:02:30'),(148,1,'2025-05-13 12:23:13');
/*!40000 ALTER TABLE `customer_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `driver_location`
--

DROP TABLE IF EXISTS `driver_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `driver_location` (
  `driver_id` int NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`driver_id`),
  CONSTRAINT `driver_location_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `employee` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driver_location`
--

LOCK TABLES `driver_location` WRITE;
/*!40000 ALTER TABLE `driver_location` DISABLE KEYS */;
INSERT INTO `driver_location` VALUES (5,26.08981400,50.56273990,'2025-05-13 12:01:49');
/*!40000 ALTER TABLE `driver_location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee` (
  `employee_id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
INSERT INTO `employee` VALUES (1,'John','John','John','John@gmail.com','$2y$10$P9/nmNa8b9vhSAfW0hUdwemc7dxnNG7ARCDWX6uxt6cgiF3PHIi7C','support','12312312','2025-04-10',500.00,'2025-04-10 11:51:45','2025-04-10 11:54:03',0),(2,'Yanis','yanis','yanis','yanismm14@hotmail.com','$2y$10$7angB3tMJJMaI5k4gTQ/euhB1I1B8G1Sdn.K.XZBjFQY2ggFjFa6q','admin','38877927','2025-04-16',500.00,'2025-04-16 12:13:54','2025-04-16 12:13:54',0),(3,'drake','drake','drake','drake111@gmail.com','$2y$10$IU4ed7FZhrQ9xANGfT1.2.yxHz7KlUrCXN00rpu0zxWs0JJyyuBPe','cashier','38877927','2025-04-16',1212.00,'2025-04-16 12:35:59','2025-04-16 12:35:59',0),(4,'james','james','james','james@gmail.com','$2y$10$h33EjiEZFxK3AiDqQ9PLh.JEzekqHk1PauryfUMTwDm0mGXrR3QuK','chef','38877927','2025-04-16',2000.00,'2025-04-16 12:40:29','2025-04-16 12:40:29',0),(5,'jack','jack','jack','jackemail@gmail.com','$2y$10$bP4aoT97HlmSpEJIc1PK1.GiIMV2rBDof71IWiWccNTZVBandz7cm','delivery','38877927','2025-04-16',2333.00,'2025-04-16 12:41:09','2025-05-03 19:34:28',0),(11,'Yanis','Moussai','qwqwq','Drain@email.xom','$2y$10$oCtKHvZFnpBEZTJ79odXEOFz936JTUYsXswO/mp2ZST4aFGiOBay6','support','38877927','2025-05-03',12222.00,'2025-05-03 13:41:39','2025-05-03 13:41:39',0),(12,'Yanis','Moussai','qdwqd','yanismm1wq4@hotmail.com','$2y$10$4yRkKXee.52hut5ek0o/Fes79B/4ao6529zaD9wyJMJeCr2wCAHAu','janitor','38877927','2025-05-03',111.00,'2025-05-03 13:44:24','2025-05-03 19:52:09',1),(13,'Yanis','Moussai','123123213','yanismm123w14@hotmail.com','$2y$10$gH3MEpF1G0hWTtNucWPH8OWqkxf9e.W8.Ht31AAWsVG2iSbS.1Mbi','chef','38877927','2025-05-03',1232.00,'2025-05-03 19:24:26','2025-05-03 19:52:04',1),(14,'admin','admin','admin','admin@hotmail.com','$2y$10$Rm6T3k31bx5nf0IyaWOUD.3NjLDmQ19N2qTLLbcyTZEsyGWLTlwtm','admin','38877927','2025-05-08',1000.00,'2025-05-08 15:59:10','2025-05-08 15:59:10',0);
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google_login`
--

DROP TABLE IF EXISTS `google_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_login` (
  `customer_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_login`
--

LOCK TABLES `google_login` WRITE;
/*!40000 ALTER TABLE `google_login` DISABLE KEYS */;
INSERT INTO `google_login` VALUES (1,'Mohamed Nacer','yanismm144@gmail.com','2025-05-11 19:30:20',NULL,NULL);
/*!40000 ALTER TABLE `google_login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `log_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `session_id` varchar(128) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `log_level` enum('INFO','WARNING','ERROR') DEFAULT 'INFO',
  PRIMARY KEY (`log_id`),
  KEY `employee_id` (`employee_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL,
  CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
INSERT INTO `logs` VALUES (1,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 11:53:20','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(2,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 12:40:34','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(3,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 12:41:03','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(4,NULL,NULL,'yanis','Login Failed','Invalid password entered for employee: yanis','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 12:43:30','02738262c978316cf3a203ddd04109c5','/login.php','WARNING'),(5,NULL,NULL,'yanis','Login Failed','Invalid password entered for employee: yanis','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 12:43:37','02738262c978316cf3a203ddd04109c5','/login.php','WARNING'),(6,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 12:43:43','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(7,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 13:34:11','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(8,2,NULL,'yanis','Add Employee','New employee \'qdwqd\' added with role janitor','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 13:44:24','02738262c978316cf3a203ddd04109c5','/add_employee.php','INFO'),(9,2,NULL,'yanis','Add Menu Item','Menu item \'Chicken dippers\' was added by employee yanis','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 13:47:19','02738262c978316cf3a203ddd04109c5','/add_menu_item.php?','INFO'),(10,2,NULL,'yanis','Order Cancelled','Order #30 marked as \'Cancelled\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 14:34:48','02738262c978316cf3a203ddd04109c5','/orders.php','WARNING'),(11,3,NULL,'drake','Order Status Change','Order #31 marked as \'Preparing\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 14:41:47','02738262c978316cf3a203ddd04109c5','/orders.php','WARNING'),(12,3,NULL,'drake','Assign Driver','Assigned jack jack to order #31','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 14:45:18','02738262c978316cf3a203ddd04109c5','/assign_driver.php?order_id=31','INFO'),(13,3,NULL,'drake','Order Status Change','Order #31 marked as \'Out for Delivery\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 14:45:37','02738262c978316cf3a203ddd04109c5','/orders.php','WARNING'),(14,3,NULL,'drake','Order Cancelled','Order #31 marked as \'Cancelled\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 14:45:42','02738262c978316cf3a203ddd04109c5','/orders.php','WARNING'),(15,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 15:07:37','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(16,NULL,NULL,'yanis','Login Failed','Invalid password entered for employee: yanis','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 15:12:55','02738262c978316cf3a203ddd04109c5','/login.php','WARNING'),(17,NULL,NULL,'yanis','Login Failed','Invalid password entered for employee: yanis','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 15:14:14','02738262c978316cf3a203ddd04109c5','/login.php','WARNING'),(18,NULL,7,'yanis123','Registration Success','New customer registered successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 15:15:44','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(19,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 15:17:42','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(20,5,NULL,'jack','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 15:52:51','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(21,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 16:00:16','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(22,2,NULL,'yanis','Add Menu Item','Menu item \'Vegetarian Pizza\' was added by employee yanis','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 16:27:23','02738262c978316cf3a203ddd04109c5','/add_menu_item.php?','INFO'),(23,3,NULL,'drake','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 18:20:43','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(24,3,NULL,'drake','Order Status Change','Order #31 marked as \'Preparing\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 18:20:51','02738262c978316cf3a203ddd04109c5','/orders.php','WARNING'),(25,3,NULL,'drake','Order Status Change','Order #31 marked as \'Preparing\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 18:21:48','02738262c978316cf3a203ddd04109c5','/orders.php','WARNING'),(26,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 18:22:54','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(27,2,NULL,'yanis','Add Employee','New employee \'123123213\' added with role chef','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 19:24:26','02738262c978316cf3a203ddd04109c5','/add_employee.php','INFO'),(28,2,NULL,'yanis','Update Employee','Updated employee #5 (jack)','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-03 19:35:29','02738262c978316cf3a203ddd04109c5','/edit_employee.php?id=5','INFO'),(29,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-04 09:59:05','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(30,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-05 11:57:39','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(31,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-05 13:19:30','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(32,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-06 10:33:58','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(33,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-06 10:34:35','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(34,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-06 10:41:06','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(35,1,NULL,'John','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-06 10:41:19','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(36,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 10:00:19','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(37,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 11:21:17','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(38,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 11:41:26','02738262c978316cf3a203ddd04109c5','/login.php','INFO'),(39,NULL,NULL,'<script>alert(\\\'XSS\\\')</script>','Login Failed','Unknown username: <script>alert(\\\'XSS\\\')</script>','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 11:47:36','7ede9250c144ac3486e2fceefdef4194','/login.php','WARNING'),(40,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 11:47:39','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(41,NULL,NULL,'\\\" onmouseover=\\\"alert(\\\'XSS\\\')','Login Failed','Unknown username: \\\" onmouseover=\\\"alert(\\\'XSS\\\')','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 11:48:56','7ede9250c144ac3486e2fceefdef4194','/login.php','WARNING'),(42,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 11:49:59','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(43,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 16:25:10','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(44,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 16:26:05','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(45,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 16:29:23','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(46,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 16:29:35','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(47,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 16:29:40','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(48,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 16:58:51','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(49,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 17:01:32','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(50,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 17:06:48','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(51,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 17:06:54','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(52,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 17:14:25','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(53,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 17:45:14','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(54,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-08 15:24:36','ea398fcfc8b8341f72a935ae0b5b03f0','/login.php','INFO'),(55,NULL,8,'aaron','Registration Success','New customer registered','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-08 15:32:57','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(56,NULL,8,'aaron','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-08 15:33:54','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(57,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-08 15:53:39','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(58,2,NULL,'yanis','Add Employee','New employee \'admin\' added with role admin','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-08 15:59:10','7ede9250c144ac3486e2fceefdef4194','/add_employee.php','INFO'),(59,NULL,NULL,'admin','Login Failed','Invalid password for employee: admin','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-08 16:00:04','7ede9250c144ac3486e2fceefdef4194','/login.php','WARNING'),(60,14,NULL,'admin','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-08 16:00:09','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(61,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 10:33:34','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(62,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 12:49:27','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(63,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 13:36:50','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(64,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 13:37:42','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(65,1,NULL,'John','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 13:48:24','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(66,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 14:53:00','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(67,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 15:11:57','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(68,3,NULL,'drake','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 15:12:41','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(69,3,NULL,'drake','Order Status Change','Order #35 marked as \'Preparing\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 15:12:55','7ede9250c144ac3486e2fceefdef4194','/orders.php','WARNING'),(70,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 15:19:40','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(71,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-09 16:27:58','0fa31a28c519346c20a46bf25472ea12','/login.php','INFO'),(72,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-10 11:56:50','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(73,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-10 12:01:09','ea398fcfc8b8341f72a935ae0b5b03f0','/login.php','INFO'),(74,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-10 12:31:03','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(75,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-10 12:45:02','7ede9250c144ac3486e2fceefdef4194','/login.php','INFO'),(76,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-10 12:57:18','b6f1d25fbfb80b8644c40ca43ea8bc4e','/login.php','INFO'),(77,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 10:51:41','8e7fca001ebe6939022af64171e64088','/login.php','INFO'),(78,3,NULL,'drake','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 10:56:18','8e7fca001ebe6939022af64171e64088','/login.php','INFO'),(79,3,NULL,'drake','Order Status Change','Order #35 marked as \'Out for Delivery\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 10:56:22','8e7fca001ebe6939022af64171e64088','/orders.php','WARNING'),(80,3,NULL,'drake','Order Cancelled','Order #35 marked as \'Cancelled\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 10:56:24','8e7fca001ebe6939022af64171e64088','/orders.php','WARNING'),(81,3,NULL,'drake','Order Status Change','Order #34 marked as \'Preparing\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 10:56:26','8e7fca001ebe6939022af64171e64088','/orders.php','WARNING'),(82,3,NULL,'drake','Assign Driver','Assigned jack jack to order #34','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 10:56:29','8e7fca001ebe6939022af64171e64088','/assign_driver.php?order_id=34','INFO'),(83,3,NULL,'drake','Order Status Change','Order #34 marked as \'Out for Delivery\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 10:56:34','8e7fca001ebe6939022af64171e64088','/orders.php','WARNING'),(84,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 10:56:43','8e7fca001ebe6939022af64171e64088','/login.php','INFO'),(85,1,NULL,'John','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 11:07:56','8e7fca001ebe6939022af64171e64088','/login.php','INFO'),(86,5,NULL,'jack','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 11:08:07','8e7fca001ebe6939022af64171e64088','/login.php','INFO'),(87,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-11 11:08:52','e4db7b3ff991812f8fbdb41ee9568a18','/login.php','INFO'),(88,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-11 11:19:38','319b60a25a6fb60310c8d456e98353c0','/login.php','INFO'),(89,5,NULL,'jack','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-11 11:19:59','319b60a25a6fb60310c8d456e98353c0','/login.php','INFO'),(90,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-11 11:20:56','319b60a25a6fb60310c8d456e98353c0','/login.php','INFO'),(91,3,NULL,'drake','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-11 11:39:27','7433aec1ac6fe5e32523ba50faffb2dc','/login.php','INFO'),(92,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 11:42:02','4a048c70f0b66928711cd43858f5d90e','/login.php','INFO'),(93,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 11:42:08','bf44c275ae27e99e0c53ad7c0fbd94c8','/login.php','INFO'),(94,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 11:42:19','843c70b2b74dcddbec8458b00ce3ab89','/login.php','INFO'),(95,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 11:42:46','e3ee57bbee87887a85f2c8b60b69bbea','/login.php','INFO'),(96,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-11 11:42:56','17d253075f56d12ba4ff80d760fc2b2c','/login.php','INFO'),(97,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-11 11:43:04','743a93f2c125c50504e33a1e34529d72','/login.php','INFO'),(98,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 11:43:19','68b0f3194bf124de70ac678d6ffbece7','/login.php','INFO'),(99,1,NULL,'John','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-11 11:45:00','63103f6d9e540942274cb647355c48b2','/login.php','INFO'),(100,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-11 16:45:32','2dd510b841b9902c8bb655e4a15ac6b3','/login.php','INFO'),(101,5,NULL,'jack','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-11 16:45:56','837baeed328f38a44e9a7df86ddf0630','/login.php','INFO'),(102,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-11 16:46:07','3e38e7f9f3773a64ba4f5354a57f0d34','/login.php','INFO'),(103,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-11 19:39:05','1051a878f87a2ab1f70e022e57669677','/login.php','INFO'),(104,5,NULL,'jack','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-12 08:50:36','780e2aa51a62795897739173cc7e924d','/login.php','INFO'),(105,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-12 09:38:32','47a34849b751503f462ca8f7e2820475','/login.php','INFO'),(106,5,NULL,'jack','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-12 10:25:36','5bbb15a4676cf07224092f0912b29b7c','/login.php','INFO'),(107,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-12 14:24:22','b224d37f8bc3fe547a977be3ed28eeb3','/login.php','INFO'),(108,5,NULL,'jack','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-12 17:17:23','751bacfd0901987ac18a36f068f0b561','/login.php','INFO'),(109,5,NULL,'jack','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-13 12:01:36','8289701f53958878db882e907657e04e','/login.php','INFO'),(110,NULL,1,'yannis','Login Success','Customer logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-13 12:02:02','1ac95757483faddb6a36b3f2c400901a','/login.php','INFO'),(111,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-13 12:23:30','bf576832c974f6041256ba824e719108','/login.php','INFO'),(112,2,NULL,'yanis','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-13 18:40:10','cdbcbecba5bfae3b8e32c35e47cda5d8','/login.php','INFO'),(113,3,NULL,'drake','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-13 18:53:22','6e63c13d09ff983c5fdbed168b0355ac','/login.php','INFO'),(114,3,NULL,'drake','Order Status Change','Order #36 marked as \'Preparing\'','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-13 18:53:25','6e63c13d09ff983c5fdbed168b0355ac','/orders.php','WARNING'),(115,3,NULL,'drake','Login Success','Employee logged in successfully','172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','2025-05-13 18:54:32','9958a9ed554e302c6b49d935f3290cfd','/login.php','INFO');
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `item_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `availability` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL,
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`menu_id`),
  KEY `fk_category` (`category_id`),
  CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'peperoni pizza','nice pizza with peperonni',3.00,'681397b858590_new-york-style-pizza2.jpg',1,'2025-03-24 12:10:52','2025-05-03 17:03:07',1,0),(2,'cheese pizza','Handcrafted Cheese Pizza For The Cheeselovers.',5.00,'680e02f00b472_Cheese-Pizzas_EXPS_FT23_275162_ST_1128_8.jpg',1,'2025-03-24 12:53:48','2025-05-01 13:21:02',1,1),(3,'Cola','Nice soft drink',2.00,'681397d241b2e_download.jpg',1,'2025-03-25 10:36:12','2025-05-01 15:48:34',3,0),(4,'sprite','sprite',1.00,'68139813c7f35_istockphoto-486786315-612x612.jpg',1,'2025-04-18 16:15:34','2025-05-01 15:49:39',3,1),(5,'pepsi','pepsi',1.00,'681398305e348_360_F_323862457_5RaEzJNg6yeYx6RjbU4WwkAl3R0yxNQt.jpg',1,'2025-04-18 16:16:37','2025-05-01 15:50:08',3,2),(8,'Meat Pizza','Meaty and Delicious Pizza',5.00,'680de1b8cf274_pizza-with-a-purpose-horizontal.jpg',1,'2025-04-27 07:47:20','2025-05-01 13:17:06',1,2),(10,'Chicken Strips','Delicious Chicken Strips',2.50,'68160e78e8245_Fried-Chicken-Strips_EXPS_FT24_25184_JR_0301_1.jpg',1,'2025-05-03 12:39:20','2025-05-03 12:39:20',5,0),(11,'Lava Cake','Delicious Lava Cake',2.00,'68160eb7d87c5_download.jpg',1,'2025-05-03 12:40:23','2025-05-03 12:40:23',4,0),(12,'Chicken dippers','Delicious Dippers',1.00,'68165ae46ae4a_chicken-dippers-10080-1.jpg',1,'2025-05-03 13:47:18','2025-05-03 18:05:24',5,0),(13,'Vegetarian Pizza','Delicious Vegeterian Pizza',4.00,'681643eb56c9d_Veggie-Pizza-2-of-5-e1691215701129.jpg',1,'2025-05-03 16:27:23','2025-05-03 16:27:23',1,0);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delivery_driver_id` int DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `fk_order_delivery_driver` (`delivery_driver_id`),
  CONSTRAINT `fk_order_delivery_driver` FOREIGN KEY (`delivery_driver_id`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL,
  CONSTRAINT `order_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (1,1,'2025-03-24 12:52:50','Cancelled',3.00,'2025-03-24 12:52:50','2025-03-25 07:09:17',NULL),(2,1,'2025-03-24 12:55:24','Cancelled',13.00,'2025-03-24 12:55:24','2025-03-25 07:09:57',NULL),(3,4,'2025-03-25 07:26:12','Cancelled',5.00,'2025-03-25 07:26:12','2025-04-06 13:05:19',NULL),(4,4,'2025-03-25 10:34:47','Cancelled',4.00,'2025-03-25 10:34:47','2025-04-06 13:05:18',NULL),(5,5,'2025-03-25 10:40:14','Cancelled',22.00,'2025-03-25 10:40:14','2025-03-25 10:40:52',NULL),(6,4,'2025-04-06 12:07:06','Cancelled',4.00,'2025-04-06 12:07:06','2025-04-06 13:05:24',NULL),(7,1,'2025-04-06 14:26:42','Cancelled',1.00,'2025-04-06 14:26:42','2025-04-06 14:30:13',NULL),(8,1,'2025-04-06 14:27:41','Cancelled',1.00,'2025-04-06 14:27:41','2025-04-06 14:30:13',NULL),(9,1,'2025-04-06 14:29:30','Completed',1.00,'2025-04-06 14:29:30','2025-04-16 12:50:30',NULL),(10,1,'2025-04-07 13:52:50','Completed',4.00,'2025-04-07 13:52:50','2025-04-16 12:50:28',NULL),(11,1,'2025-04-08 10:34:35','Completed',4.00,'2025-04-08 10:34:35','2025-04-16 12:50:20',NULL),(12,1,'2025-04-09 07:21:12','Completed',4.00,'2025-04-09 07:21:12','2025-04-16 12:50:19',NULL),(13,1,'2025-04-09 07:36:55','Completed',4.00,'2025-04-09 07:36:55','2025-04-16 12:50:15',NULL),(14,1,'2025-04-09 07:37:59','Completed',4.00,'2025-04-09 07:37:59','2025-04-16 12:50:10',NULL),(15,1,'2025-04-09 07:39:12','Completed',4.00,'2025-04-09 07:39:12','2025-04-16 12:50:12',NULL),(16,1,'2025-04-09 07:50:51','Completed',4.00,'2025-04-09 07:50:51','2025-04-16 12:50:09',NULL),(17,1,'2025-04-09 07:57:25','Completed',4.00,'2025-04-09 07:57:25','2025-04-16 12:50:06',NULL),(18,1,'2025-04-09 12:05:14','Completed',4.00,'2025-04-09 12:05:14','2025-04-16 12:50:05',NULL),(19,1,'2025-04-15 10:45:50','Completed',4.00,'2025-04-15 10:45:50','2025-04-16 12:50:02',NULL),(20,5,'2025-04-16 12:59:51','Completed',6.00,'2025-04-16 12:59:51','2025-04-16 13:20:47',5),(21,5,'2025-04-16 13:34:05','Completed',3.00,'2025-04-16 13:34:05','2025-04-17 10:03:13',5),(22,1,'2025-04-18 15:45:47','Cancelled',1.00,'2025-04-18 15:45:47','2025-04-27 14:18:33',5),(23,1,'2025-04-20 06:11:02','Completed',1.00,'2025-04-20 06:11:02','2025-04-22 10:43:34',5),(24,1,'2025-04-22 10:50:09','Completed',1.00,'2025-04-22 10:50:09','2025-04-27 14:22:11',5),(25,1,'2025-04-29 11:46:11','Pending',10.00,'2025-04-29 11:46:11','2025-05-03 18:19:53',NULL),(26,1,'2025-04-29 12:18:23','Cancelled',5.00,'2025-04-29 12:18:23','2025-04-29 12:29:34',5),(27,1,'2025-04-29 12:20:37','Cancelled',5.00,'2025-04-29 12:20:37','2025-04-29 12:29:35',5),(28,1,'2025-04-29 12:31:24','Cancelled',5.00,'2025-04-29 12:31:24','2025-04-29 12:32:11',NULL),(29,1,'2025-04-29 12:34:37','Completed',5.00,'2025-04-29 12:34:37','2025-04-29 12:47:34',5),(30,1,'2025-04-30 12:23:18','Cancelled',5.00,'2025-04-30 12:23:18','2025-05-03 14:34:48',NULL),(31,1,'2025-05-01 16:04:15','Preparing',5.00,'2025-05-01 16:04:15','2025-05-03 18:21:48',5),(32,1,'2025-05-05 14:54:24','Pending',1.00,'2025-05-05 14:54:24','2025-05-05 14:54:24',NULL),(33,8,'2025-05-08 15:42:40','Pending',8.50,'2025-05-08 15:42:40','2025-05-08 15:42:40',NULL),(34,1,'2025-05-09 13:08:41','Out for Delivery',2.00,'2025-05-09 13:08:41','2025-05-11 10:56:34',5),(35,1,'2025-05-09 15:12:33','Cancelled',2.50,'2025-05-09 15:12:33','2025-05-11 10:56:24',NULL),(36,5,'2025-05-13 18:53:04','Preparing',2.50,'2025-05-13 18:53:04','2025-05-13 18:53:25',NULL),(37,5,'2025-05-13 18:59:07','Pending',10.00,'2025-05-13 18:59:07','2025-05-13 18:59:07',NULL);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `menu_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,1,3.00,'2025-03-24 12:52:50','2025-03-24 12:52:50'),(2,2,1,3,9.00,'2025-03-24 12:55:24','2025-03-24 12:55:24'),(3,2,2,4,4.00,'2025-03-24 12:55:24','2025-03-24 12:55:24'),(4,3,2,2,2.00,'2025-03-25 07:26:12','2025-03-25 07:26:12'),(5,3,1,1,3.00,'2025-03-25 07:26:12','2025-03-25 07:26:12'),(6,4,2,1,1.00,'2025-03-25 10:34:47','2025-03-25 10:34:47'),(7,4,1,1,3.00,'2025-03-25 10:34:47','2025-03-25 10:34:47'),(8,5,2,4,4.00,'2025-03-25 10:40:14','2025-03-25 10:40:14'),(9,5,1,6,18.00,'2025-03-25 10:40:14','2025-03-25 10:40:14'),(10,6,2,1,1.00,'2025-04-06 12:07:06','2025-04-06 12:07:06'),(11,6,1,1,3.00,'2025-04-06 12:07:06','2025-04-06 12:07:06'),(12,7,2,1,1.00,'2025-04-06 14:26:42','2025-04-06 14:26:42'),(13,8,2,1,1.00,'2025-04-06 14:27:41','2025-04-06 14:27:41'),(14,9,2,1,1.00,'2025-04-06 14:29:30','2025-04-06 14:29:30'),(15,10,2,1,1.00,'2025-04-07 13:52:50','2025-04-07 13:52:50'),(16,10,1,1,3.00,'2025-04-07 13:52:50','2025-04-07 13:52:50'),(17,11,2,1,1.00,'2025-04-08 10:34:35','2025-04-08 10:34:35'),(18,11,1,1,3.00,'2025-04-08 10:34:35','2025-04-08 10:34:35'),(19,12,2,1,1.00,'2025-04-09 07:21:12','2025-04-09 07:21:12'),(20,12,1,1,3.00,'2025-04-09 07:21:12','2025-04-09 07:21:12'),(21,13,2,1,1.00,'2025-04-09 07:36:55','2025-04-09 07:36:55'),(22,13,1,1,3.00,'2025-04-09 07:36:55','2025-04-09 07:36:55'),(23,14,2,1,1.00,'2025-04-09 07:37:59','2025-04-09 07:37:59'),(24,14,1,1,3.00,'2025-04-09 07:37:59','2025-04-09 07:37:59'),(25,15,2,1,1.00,'2025-04-09 07:39:12','2025-04-09 07:39:12'),(26,15,1,1,3.00,'2025-04-09 07:39:12','2025-04-09 07:39:12'),(27,16,2,1,1.00,'2025-04-09 07:50:51','2025-04-09 07:50:51'),(28,16,1,1,3.00,'2025-04-09 07:50:51','2025-04-09 07:50:51'),(29,17,2,1,1.00,'2025-04-09 07:57:25','2025-04-09 07:57:25'),(30,17,1,1,3.00,'2025-04-09 07:57:25','2025-04-09 07:57:25'),(31,18,2,1,1.00,'2025-04-09 12:05:14','2025-04-09 12:05:14'),(32,18,1,1,3.00,'2025-04-09 12:05:14','2025-04-09 12:05:14'),(33,19,2,1,1.00,'2025-04-15 10:45:50','2025-04-15 10:45:50'),(34,19,1,1,3.00,'2025-04-15 10:45:50','2025-04-15 10:45:50'),(35,20,1,1,3.00,'2025-04-16 12:59:51','2025-04-16 12:59:51'),(36,20,2,1,1.00,'2025-04-16 12:59:51','2025-04-16 12:59:51'),(37,20,3,1,2.00,'2025-04-16 12:59:51','2025-04-16 12:59:51'),(38,21,1,1,3.00,'2025-04-16 13:34:05','2025-04-16 13:34:05'),(39,22,2,1,1.00,'2025-04-18 15:45:47','2025-04-18 15:45:47'),(40,23,2,1,1.00,'2025-04-20 06:11:02','2025-04-20 06:11:02'),(41,24,2,1,1.00,'2025-04-22 10:50:09','2025-04-22 10:50:09'),(42,25,2,2,10.00,'2025-04-29 11:46:11','2025-04-29 11:46:11'),(43,26,2,1,5.00,'2025-04-29 12:18:23','2025-04-29 12:18:23'),(44,27,8,1,5.00,'2025-04-29 12:20:37','2025-04-29 12:20:37'),(45,28,2,1,5.00,'2025-04-29 12:31:24','2025-04-29 12:31:24'),(46,29,2,1,5.00,'2025-04-29 12:34:37','2025-04-29 12:34:37'),(47,30,2,1,5.00,'2025-04-30 12:23:18','2025-04-30 12:23:18'),(48,31,2,1,5.00,'2025-05-01 16:04:15','2025-05-01 16:04:15'),(49,32,12,1,1.00,'2025-05-05 14:54:24','2025-05-05 14:54:24'),(50,33,12,1,1.00,'2025-05-08 15:42:40','2025-05-08 15:42:40'),(51,33,10,1,2.50,'2025-05-08 15:42:40','2025-05-08 15:42:40'),(52,33,2,1,5.00,'2025-05-08 15:42:40','2025-05-08 15:42:40'),(53,34,12,2,2.00,'2025-05-09 13:08:41','2025-05-09 13:08:41'),(54,35,10,1,2.50,'2025-05-09 15:12:33','2025-05-09 15:12:33'),(55,36,10,1,2.50,'2025-05-13 18:53:04','2025-05-13 18:53:04'),(56,37,10,4,10.00,'2025-05-13 18:59:07','2025-05-13 18:59:07');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitors`
--

DROP TABLE IF EXISTS `visitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(512) NOT NULL,
  `visit_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`,`user_agent`)
) ENGINE=InnoDB AUTO_INCREMENT=724 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitors`
--

LOCK TABLES `visitors` WRITE;
/*!40000 ALTER TABLE `visitors` DISABLE KEYS */;
INSERT INTO `visitors` VALUES (1,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-07 16:25:04'),(2,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36','2025-05-07 16:44:40'),(3,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46','2025-05-07 16:47:57'),(5,'172.18.0.1','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36','2025-05-07 16:57:55'),(6,'172.18.0.1','','2025-05-07 17:03:10'),(7,'172.18.0.1','Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)','2025-05-07 17:52:20'),(8,'172.18.0.1','Python-urllib/2.7','2025-05-07 17:52:47'),(10,'172.18.0.1','curl/7.88.1','2025-05-07 18:32:23'),(11,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36','2025-05-07 18:39:07'),(15,'172.18.0.1','Go-http-client/1.1','2025-05-07 20:28:54'),(17,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.190 Safari/537.36','2025-05-07 20:38:49'),(21,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36','2025-05-07 22:18:50'),(22,'172.18.0.1','Mozilla/5.0 zgrab/0.x','2025-05-07 22:25:40'),(25,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36','2025-05-07 23:05:05'),(26,'172.18.0.1','Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)','2025-05-07 23:05:19'),(32,'172.18.0.1','Mozilla/5.0 (Windows NT 5.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36','2025-05-08 01:14:27'),(36,'172.18.0.1','Expanse, a Palo Alto Networks company, searches across the global IPv4 space multiple times per day to identify customers&#39; presences on the Internet. If you would like to be excluded from our scans, please send IP addresses/domains to: scaninfo@paloaltonetworks.com','2025-05-08 02:33:00'),(37,'172.18.0.1','python-requests/2.32.3','2025-05-08 02:37:19'),(42,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36','2025-05-08 03:30:04'),(45,'172.18.0.1','curl/7.81.0','2025-05-08 03:41:42'),(48,'172.18.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36','2025-05-08 04:06:35'),(56,'172.18.0.1','\'Mozilla/5.0 (compatible; GenomeCrawlerd/1.0; +https://www.nokia.com/genomecrawler)\'','2025-05-08 07:24:16'),(64,'172.18.0.1','masscan/1.0 (https://github.com/robertdavidgraham/masscan)','2025-05-08 09:39:07'),(67,'172.18.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36','2025-05-08 10:18:36'),(72,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0','2025-05-08 14:49:00'),(77,'172.18.0.1','Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0;  Trident/5.0)','2025-05-08 16:21:18'),(85,'172.18.0.1','l9tcpid/v1.1.0','2025-05-08 17:42:45'),(86,'172.18.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0','2025-05-08 18:11:02'),(90,'172.18.0.1','curl/7.61.1','2025-05-08 19:07:39'),(95,'172.18.0.1','Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 10.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729)','2025-05-08 20:00:36'),(96,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36','2025-05-08 21:47:50'),(97,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36','2025-05-08 21:51:21'),(100,'172.18.0.1','Custom-AsyncHttpClient','2025-05-08 22:05:37'),(104,'172.18.0.1','curl/7.64.1','2025-05-08 22:45:47'),(105,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36','2025-05-08 22:46:45'),(121,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36','2025-05-09 02:20:22'),(139,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36','2025-05-09 05:54:50'),(141,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.2 Safari/605.1.15','2025-05-09 05:56:17'),(145,'172.18.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0','2025-05-09 06:41:55'),(150,'172.18.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','2025-05-09 07:27:39'),(158,'172.18.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:76.0) Gecko/20100101 Firefox/76.0','2025-05-09 09:42:08'),(165,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-09 11:45:58'),(166,'172.18.0.1','Python/3.9 python-socks/2.0.3','2025-05-09 12:06:45'),(172,'172.18.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36','2025-05-09 13:20:04'),(181,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/601.7.7 (KHTML, like Gecko) Version/9.1.2 Safari/601.7.7','2025-05-09 15:14:17'),(183,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0','2025-05-09 16:00:11'),(194,'172.18.0.1','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','2025-05-09 20:17:54'),(214,'172.18.0.1','Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36','2025-05-09 22:23:28'),(219,'172.18.0.1','Hello-World/1.0','2025-05-09 23:32:10'),(226,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.117 Safari/537.36','2025-05-10 00:22:38'),(228,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.82 Safari/537.36 OPR/29.0.1795.41','2025-05-10 00:41:09'),(233,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0','2025-05-10 01:31:35'),(248,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36','2025-05-10 04:07:54'),(258,'172.18.0.1','Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.1; en-US)','2025-05-10 05:42:56'),(259,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36','2025-05-10 05:43:25'),(265,'172.18.0.1','Mozilla/5.0 (Windows NT 6.2;en-US) AppleWebKit/537.32.36 (KHTML, live Gecko) Chrome/53.0.3025.74 Safari/537.32','2025-05-10 06:29:00'),(267,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36','2025-05-10 06:45:11'),(271,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.14; rv:62.0) Gecko/20100101 Firefox/62.0','2025-05-10 07:30:18'),(286,'172.18.0.1','Mozilla/5.0 (Windows NT 6.1; rv:102.0) Gecko/20100101 Goanna/6.6 Firefox/102.0 PaleMoon/33.0.0','2025-05-10 10:08:40'),(293,'172.18.0.1','Mozilla/5.0 (iPad; CPU OS 8_0_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML like Gecko) Mobile/12A405 Version/7.0 Safari/9537.53','2025-05-10 11:28:19'),(297,'172.18.0.1','Mozilla/5.0','2025-05-10 11:49:40'),(306,'172.18.0.1','Linux Gnu (cow)','2025-05-10 12:57:39'),(310,'172.18.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-10 17:24:12'),(324,'172.18.0.1','HTTP Banner Detection (https://security.ipip.net)','2025-05-10 19:48:37'),(325,'172.18.0.1','curl/7.29.0','2025-05-10 20:02:38'),(326,'172.18.0.1','Mozilla/5.0 (Windows NT 7_0_1; Win64; x64) AppleWebKit/580.45 (KHTML, like Gecko) Chrome/51.0.722 Safari/537.36','2025-05-10 20:03:12'),(330,'172.18.0.1','Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G973F Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.4 Chrome/67.0.3396.87 Mobile Safari/537.36','2025-05-10 20:34:55'),(361,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36','2025-05-11 02:03:19'),(367,'172.18.0.1','Mozilla/5.0 (Linux; Android 8.0.0; SM-G935F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.111 Mobile Safari/537.36','2025-05-11 02:26:19'),(370,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36','2025-05-11 03:23:58'),(381,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:124.0) Gecko/20100101 Firefox/124.0','2025-05-11 06:28:06'),(394,'172.18.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:136.0) Gecko/20100101 Firefox/136.0','2025-05-11 08:15:19'),(400,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; rv:91.0) Gecko/20100101 Firefox/91.0','2025-05-11 09:32:28'),(406,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36','2025-05-11 10:06:42'),(444,'172.18.0.1','Mozilla/5.0 (Linux; Android 9; Mi MIX 2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.111 Mobile Safari/537.36','2025-05-11 16:32:25'),(445,'172.18.0.1','Mozilla/5.0 (Kubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0','2025-05-11 16:57:41'),(451,'172.18.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36','2025-05-11 17:34:56'),(466,'172.18.0.1','Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0','2025-05-11 20:36:57'),(483,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36','2025-05-11 22:33:27'),(484,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0 Safari/537.36','2025-05-11 23:01:27'),(497,'172.18.0.1','SonyEricssonK750i/R1CA Browser/SEMC-Browser/4.2 Profile/MIDP-2.0 Configuration/CLDC-1.1','2025-05-12 00:38:21'),(502,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36','2025-05-12 01:23:58'),(526,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 OPR/95.0.0.0','2025-05-12 04:57:41'),(537,'172.18.0.1','Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.137 Mobile Safari/537.36','2025-05-12 05:50:14'),(553,'172.18.0.1','Python/3.11 aiohttp/3.8.4','2025-05-12 08:52:27'),(554,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36','2025-05-12 08:56:17'),(575,'172.18.0.1','WanScannerBot/1.0','2025-05-12 11:41:52'),(593,'172.18.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','2025-05-12 14:33:03'),(625,'172.18.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:125.0) Gecko/20100101 Firefox/125.0','2025-05-13 09:21:21'),(626,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36','2025-05-13 09:27:15'),(631,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Edg/110.0.1587.56','2025-05-13 09:37:44'),(633,'172.18.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36','2025-05-13 09:43:34'),(634,'172.18.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36','2025-05-13 09:47:58'),(674,'172.18.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:59.0) Gecko/20100101 Firefox/59.0','2025-05-13 13:47:23'),(693,'172.18.0.1','Mozilla/5.0 (Linux; Android 11; M2004J15SC) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.114 Mobile Safari/537.36','2025-05-13 15:37:05'),(706,'172.18.0.1','Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36','2025-05-13 17:21:18'),(707,'172.18.0.1','LG-LX550 AU-MIC-LX550/2.0 MMP/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1','2025-05-13 17:21:37');
/*!40000 ALTER TABLE `visitors` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-13 19:15:21
