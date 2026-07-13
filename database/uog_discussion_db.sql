-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: uog_discussion_db
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
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `reply_id` int(11) DEFAULT NULL,
  `type` enum('image','video','document') NOT NULL,
  `path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_media_post` (`post_id`),
  KEY `fk_media_reply` (`reply_id`),
  CONSTRAINT `fk_media_post_new` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_media_reply` FOREIGN KEY (`reply_id`) REFERENCES `replies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (1,3,NULL,'image','uploads/images/tailwind-error.png','tailwind-error.png','image/png',245760,'2026-06-28 14:50:25'),(2,5,NULL,'image','uploads/images/navbar-mockup.jpg','navbar-mockup.jpg','image/jpeg',187420,'2026-06-28 14:50:25'),(3,7,NULL,'document','uploads/documents/20260629071211-997e665014862949.sql.txt','uog_discussion_db.sql','text/plain',14890,'2026-06-29 05:12:11'),(4,15,NULL,'image','uploads/images/20260707214412-1995c8c7e1ac94fd.jpg','jason-zeis-GLGjCQ1VyIg-unsplash.jpg','image/jpeg',5348666,'2026-07-07 19:44:12');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_code` varchar(20) NOT NULL,
  `module_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_code` (`module_code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (1,'COMP1841','Web Programming 1','PHP MVC, PDO, MySQL and CRUD web development.','2026-06-28 14:50:25',NULL),(2,'COMP1551','Application Development','Programming fundamentals and application development.','2026-06-28 14:50:25',NULL),(3,'COMP1786','Object Oriented Programming','Object oriented programming concepts and software structure.','2026-06-28 14:50:25',NULL),(4,'DESN2200','Web Design','UI, UX, accessibility and web design practice.','2026-06-28 14:50:25',NULL),(5,'MATH1020','Discrete Mathematics','Logic, proofs, sets and mathematical foundations.','2026-06-28 14:50:25',NULL);
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('open','solved') DEFAULT 'open',
  `view_count` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_posts_user` (`user_id`),
  KEY `fk_posts_module` (`module_id`),
  CONSTRAINT `fk_posts_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'How to implement MVC routing in PHP?','how-to-implement-mvc-routing-in-php','I am struggling with creating a custom router for my COMP1841 project. Can someone explain the basic structure?','open',0,1,1,'2026-06-28 14:50:25',NULL,NULL),(2,'Difference between composition and inheritance','difference-between-composition-and-inheritance','Can anyone explain when composition should be used instead of inheritance in OOP?','solved',0,4,3,'2026-06-28 14:50:25',NULL,NULL),(3,'TailwindCSS not loading after npm build','tailwindcss-not-loading-after-npm-build','My CSS file is generated successfully but the styles are not appearing in the browser.','open',0,3,1,'2026-06-28 14:50:25','2026-06-29 09:27:32',NULL),(4,'Best way to study Discrete Mathematics','best-way-to-study-discrete-mathematics','Any tips for understanding proofs and logic questions?','open',0,4,5,'2026-06-28 14:50:25',NULL,NULL),(5,'Responsive navbar design ideas','responsive-navbar-design-ideas','I need inspiration for a responsive navigation menu for DESN2200.','solved',0,3,4,'2026-06-28 14:50:25',NULL,NULL),(6,'123123123','123123123','123123123123123123123','open',0,1,2,'2026-06-29 04:39:48',NULL,NULL),(7,'asd1asdasdasdasd','asd1asdasdasdasd','asdasdasdasdasdasdasddddaaaaaaaaaaaaaaaaaa','open',4,1,2,'2026-06-29 05:12:11','2026-07-07 16:11:54',NULL),(8,'123','123','123123','open',0,7,2,'2026-07-07 16:10:58',NULL,NULL),(9,'123123123','123123123-2','123123123123','open',6,7,3,'2026-07-07 16:19:10','2026-07-07 18:20:51','2026-07-07 18:20:51'),(10,'123123','123123','123123123','open',0,7,3,'2026-07-07 18:49:18',NULL,NULL),(11,'123123','123123-2','1231231231231','open',1,7,2,'2026-07-07 18:49:24',NULL,NULL),(12,'123123','123123-3','123123','open',1,7,2,'2026-07-07 18:49:36',NULL,NULL),(13,'1111111111111111111','1111111111111111111','123123','open',0,7,1,'2026-07-07 18:51:20',NULL,NULL),(14,'123hg','123hg','123123','open',1,7,4,'2026-07-07 19:12:09','2026-07-07 19:21:14',NULL),(15,'HELLO','hello','12312312313','open',2,7,4,'2026-07-07 19:44:12','2026-07-07 21:09:45',NULL);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `replies`
--

DROP TABLE IF EXISTS `replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `parent_reply_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_accepted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_replies_post` (`post_id`),
  KEY `fk_replies_user` (`user_id`),
  KEY `fk_replies_parent` (`parent_reply_id`),
  CONSTRAINT `fk_replies_parent` FOREIGN KEY (`parent_reply_id`) REFERENCES `replies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_replies_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_replies_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `replies`
--

LOCK TABLES `replies` WRITE;
/*!40000 ALTER TABLE `replies` DISABLE KEYS */;
INSERT INTO `replies` VALUES (1,1,NULL,5,'Create a front controller using public/index.php and route every request through it.',0,'2026-06-28 14:50:25',NULL,NULL),(2,1,NULL,3,'Use .htaccess to redirect requests and map URLs to controllers.',1,'2026-06-28 14:50:25',NULL,NULL),(3,2,NULL,5,'Composition is preferred when objects have a HAS-A relationship.',1,'2026-06-28 14:50:25',NULL,NULL),(4,3,NULL,2,'Check that app.css is correctly linked and clear the browser cache.',0,'2026-06-28 14:50:25',NULL,NULL),(5,5,NULL,5,'Look at university navigation patterns and keep the mobile menu simple.',1,'2026-06-28 14:50:25',NULL,NULL),(6,7,NULL,1,'hello 123 123',0,'2026-06-29 05:58:21','2026-06-29 06:35:59','2026-06-29 06:35:59'),(7,3,NULL,1,'1ad',0,'2026-06-29 09:17:49','2026-06-29 09:27:32',NULL),(8,7,NULL,7,'123123',0,'2026-07-07 16:11:54',NULL,NULL),(9,14,NULL,7,'123',0,'2026-07-07 19:21:09','2026-07-07 19:21:14',NULL),(10,15,NULL,7,'123',0,'2026-07-07 21:09:45',NULL,NULL);
/*!40000 ALTER TABLE `replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_modules`
--

DROP TABLE IF EXISTS `user_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_module` (`user_id`,`module_id`),
  KEY `fk_user_modules_module` (`module_id`),
  CONSTRAINT `fk_user_modules_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_modules_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_modules`
--

LOCK TABLES `user_modules` WRITE;
/*!40000 ALTER TABLE `user_modules` DISABLE KEYS */;
INSERT INTO `user_modules` VALUES (1,1,1,'2026-06-28 14:50:25'),(2,1,2,'2026-06-28 14:50:25'),(3,2,1,'2026-06-28 14:50:25'),(4,2,4,'2026-06-28 14:50:25'),(5,3,1,'2026-06-28 14:50:25'),(6,3,4,'2026-06-28 14:50:25'),(7,4,3,'2026-06-28 14:50:25'),(8,4,5,'2026-06-28 14:50:25');
/*!40000 ALTER TABLE `user_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(75) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('student','tutor','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Lù','Phúc','lvp9852','lvp9852@gre.ac.uk','$2y$10$AbxFLbzxJXegheULQWa1beWu9kMwIB8qjTqUhtNENPwuY.BQCCIIi',NULL,'student','2026-06-28 14:50:25',NULL),(2,'Linh','Nguyen','linhnguyen','linh.nguyen@gre.ac.uk',NULL,'uploads/avatars/linh-nguyen.png','student','2026-06-28 14:50:25',NULL),(3,'Minh','Tran','minhtran','minh.tran@gre.ac.uk',NULL,'uploads/avatars/minh-tran.png','student','2026-06-28 14:50:25',NULL),(4,'An','Pham','anpham','an.pham@gre.ac.uk',NULL,NULL,'student','2026-06-28 14:50:25',NULL),(5,'Matt','Tutor','matttutor','matt.tutor@gre.ac.uk',NULL,NULL,'tutor','2026-06-28 14:50:25',NULL),(6,'Admin','User','admin','admin@gre.ac.uk',NULL,NULL,'admin','2026-06-28 14:50:25',NULL),(7,'Phuc','Lu','luvinhfuc','anhtuan123@gmail.com','$2y$10$qjU5a.c5GwAugHAxqjB0yurkU24Z9xGuW/aXK0nMd.phZY18m3C/K',NULL,'student','2026-07-07 15:49:50',NULL);
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

-- Dump completed on 2026-07-09  0:22:58
