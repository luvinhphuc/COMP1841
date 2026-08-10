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
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(75) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','resolved') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (2,7,'Phuc Lu','anhtuan123@gmail.com','Need an upload documentation verify for Figma Education Plan','aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','resolved','2026-08-05 04:42:01','2026-08-05 13:35:56'),(3,7,'Phuc Lu','anhtuan123@gmail.com','123','123','unread','2026-08-05 06:14:22',NULL),(4,7,'Phuc Lu','anhtuan123@gmail.com','1','1','unread','2026-08-07 22:33:24',NULL),(5,7,'Phuc Lu','anhtuan123@gmail.com','1','1','unread','2026-08-07 22:52:25',NULL),(6,17,'Codex QA','codexqa2608102116@gre.ac.uk','QA F20 Contact 2608102116','Temporary manual contact-form test F20. Please disregard.','unread','2026-08-10 14:36:55',NULL);
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (1,3,NULL,'image','uploads/images/tailwind-error.png','tailwind-error.png','image/png',245760,'2026-06-28 14:50:25'),(2,5,NULL,'image','uploads/images/navbar-mockup.jpg','navbar-mockup.jpg','image/jpeg',187420,'2026-06-28 14:50:25'),(3,7,NULL,'document','uploads/documents/20260629071211-997e665014862949.sql.txt','uog_discussion_db.sql','text/plain',14890,'2026-06-29 05:12:11'),(17,27,NULL,'image','uploads/images/20260810142049-fe71cd24dd8a27d6.jpg','dmytro-bayer-_oz4mB_80ww-unsplash.jpg','image/jpeg',4016263,'2026-08-10 12:20:49'),(19,31,NULL,'image','uploads/images/20260810165055-084b1f409c455302.png','logo-icon.png','image/png',43449,'2026-08-10 14:50:55');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (1,'COMP1841','Web Programming 1','PHP MVC, PDO, MySQL and CRUD web development.','2026-06-28 14:50:25',NULL),(2,'COMP1551','Application Development','Programming fundamentals and application development.','2026-06-28 14:50:25',NULL),(3,'COMP1786','Object Oriented Programming','Object oriented programming concepts and software structure.','2026-06-28 14:50:25',NULL),(4,'DESN2200','Web Design','UI, UX, accessibility and web design practice.','2026-06-28 14:50:25',NULL),(5,'MATH1020','Discrete Mathematics','Logic, proofs, sets and mathematical foundations.','2026-06-28 14:50:25',NULL),(6,'COMP1771','User Interface Design',NULL,'2026-07-13 15:42:30','2026-07-16 06:53:08');
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
  CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'How to implement MVC routing in PHP?','how-to-implement-mvc-routing-in-php','I am struggling with creating a custom router for my COMP1841 project. Can someone explain the basic structure?','open',3,1,1,'2026-06-28 14:50:25',NULL,NULL),(2,'Difference between composition and inheritance','difference-between-composition-and-inheritance','Can anyone explain when composition should be used instead of inheritance in OOP?','solved',1,4,3,'2026-06-28 14:50:25',NULL,NULL),(3,'TailwindCSS not loading after npm build','tailwindcss-not-loading-after-npm-build','My CSS file is generated successfully but the styles are not appearing in the browser.','open',1,3,1,'2026-06-28 14:50:25','2026-06-29 09:27:32',NULL),(4,'Best way to study Discrete Mathematics','best-way-to-study-discrete-mathematics','Any tips for understanding proofs and logic questions?','open',1,4,5,'2026-06-28 14:50:25','2026-08-05 04:51:15',NULL),(5,'Responsive navbar design ideas','responsive-navbar-design-ideas','I need inspiration for a responsive navigation menu for DESN2200.','solved',0,3,4,'2026-06-28 14:50:25',NULL,NULL),(6,'How should input validation be handled in a PHP application?','how-should-input-validation-be-handled-in-a-php-application','I am validating required form fields in my application. Should validation be performed only in the browser, or should the same rules also be checked on the server before saving data?','open',0,1,2,'2026-06-29 04:39:48',NULL,NULL),(7,'Organising controller validation without repeating code','organising-controller-validation-without-repeating-code','Several controllers in my project perform similar validation for form inputs. What is a clean way to share validation logic without making the controllers difficult to understand?','open',4,1,2,'2026-06-29 05:12:11','2026-07-07 16:11:54',NULL),(8,'Displaying validation errors next to form fields','displaying-validation-errors-next-to-form-fields','I want users to clearly understand which form field is invalid after submission. What is a good approach for storing validation messages and displaying them next to the correct field?','open',0,7,2,'2026-07-07 16:10:58',NULL,NULL),(9,'Interface or abstract class for shared behaviour?','interface-or-abstract-class-for-shared-behaviour','Several classes in my application need similar methods, but they do not all share the same state. When would an interface be more appropriate than an abstract base class?','open',6,7,3,'2026-07-07 16:19:10','2026-07-07 18:20:51','2026-07-07 18:20:51'),(10,'How can form data be preserved after validation fails?','how-can-form-data-be-preserved-after-validation-fails','When a form fails server-side validation, I want the valid values to remain in the form so the user does not need to enter everything again. What is the usual approach in PHP?','open',0,7,3,'2026-07-07 18:49:18',NULL,NULL),(11,'Separating business logic from controllers','separating-business-logic-from-controllers','Some of my controller methods are becoming quite long because they perform validation, database operations and redirects. What responsibilities should normally stay inside a controller?','open',1,7,2,'2026-07-07 18:49:24',NULL,NULL),(12,'Handling exceptions during database operations','handling-exceptions-during-database-operations','I am using PDO for database access. If an insert or update fails, where should the exception be handled so that users receive a useful error without exposing database details?','open',1,7,2,'2026-07-07 18:49:36',NULL,NULL),(13,'Using prepared statements for dynamic search','using-prepared-statements-for-dynamic-search','My discussion search supports a keyword and optional module filter. How can I build the query with PDO prepared statements while keeping all user input safely parameterised?','open',3,7,1,'2026-07-07 18:51:20',NULL,NULL),(14,'Improving colour contrast in a web interface','improving-colour-contrast-in-a-web-interface','Some secondary text and button states in my interface look too faint on a light background. What should I check when choosing accessible foreground and background colour combinations?','open',2,7,4,'2026-07-07 19:12:09','2026-08-05 06:05:21',NULL),(15,'Making a responsive navigation menu keyboard accessible','making-a-responsive-navigation-menu-keyboard-accessible','I have created a collapsible navigation menu for smaller screens. What keyboard and focus behaviour should be included so the menu remains usable without a mouse?','open',6,7,4,'2026-07-07 19:44:12','2026-08-10 12:00:54','2026-08-10 12:00:54'),(20,'Preventing duplicate form submissions in PHP','preventing-duplicate-form-submissions-in-php','After submitting a form, refreshing the page can send the same POST request again. Is the Post/Redirect/Get pattern the recommended way to prevent duplicate submissions?','open',4,7,2,'2026-07-12 21:28:20','2026-08-10 12:00:49','2026-08-10 12:00:49'),(21,'Difference between encapsulation and abstraction','difference-between-encapsulation-and-abstraction','I understand both terms individually, but I still confuse encapsulation with abstraction when discussing object-oriented design. Could someone explain the difference using a simple application example?','open',7,7,3,'2026-07-18 03:36:53','2026-08-10 11:57:34','2026-08-10 11:57:34'),(22,'Accessible form errors without relying only on colour','accessible-form-errors-without-relying-only-on-colour','My validation errors currently use red text. I want the form to remain understandable for users who may not distinguish colours easily. What additional visual or semantic feedback should be provided?','open',11,7,4,'2026-08-05 06:28:18','2026-08-10 11:57:27','2026-08-10 11:57:27'),(23,'Creating consistent spacing across UI components','creating-consistent-spacing-across-ui-components','I am trying to make cards, forms and buttons feel visually consistent across the site. What is a sensible way to define reusable spacing rules in a small design system?','open',5,7,6,'2026-08-05 23:52:13','2026-08-07 23:02:26','2026-08-07 23:02:26'),(25,'Test','test','Test','open',3,15,3,'2026-08-07 23:54:06','2026-08-10 12:17:12','2026-08-10 12:17:12'),(26,'Test','test-2','Test','open',2,15,3,'2026-08-07 23:56:14','2026-08-10 12:01:21',NULL),(27,'Can I use this','can-i-use-this','','open',3,7,6,'2026-08-10 12:20:49','2026-08-10 12:23:04',NULL),(28,'QA F08 Discussion 2608102116 Edited F12','qa-f08-discussion-2608102116-edited-f12','Temporary QA discussion created for manual tests F08–F17. Edited successfully during F12.','open',2,17,1,'2026-08-10 14:22:41','2026-08-10 14:52:28','2026-08-10 14:52:28'),(29,'QA F10 Attachment 2608102116','qa-f10-attachment-2608102116','Temporary QA discussion with a supported PNG attachment for F10.','open',1,17,1,'2026-08-10 14:29:47','2026-08-10 14:52:26','2026-08-10 14:52:26'),(30,'How should I structure routes in a PHP MVC application?','how-should-i-structure-routes-in-a-php-mvc-application','I am using a front controller and want each route to validate its HTTP method before dispatching to a controller action. How should I organise this cleanly?','open',1,18,1,'2026-08-10 14:48:54','2026-08-10 14:51:48',NULL),(31,'Why is my PHP form upload not saving the selected image?','why-is-my-php-form-upload-not-saving-the-selected-image','The form submits successfully, but I want to confirm that the uploaded PNG is stored and remains attached to the discussion. Which PHP upload checks should I perform?','open',1,18,1,'2026-08-10 14:50:55',NULL,NULL);
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
  CONSTRAINT `fk_replies_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `replies`
--

LOCK TABLES `replies` WRITE;
/*!40000 ALTER TABLE `replies` DISABLE KEYS */;
INSERT INTO `replies` VALUES (1,1,NULL,5,'Create a front controller using public/index.php and route every request through it.',0,'2026-06-28 14:50:25',NULL,NULL),(2,1,NULL,3,'Use .htaccess to redirect requests and map URLs to controllers.',1,'2026-06-28 14:50:25',NULL,NULL),(3,2,NULL,5,'Composition is preferred when objects have a HAS-A relationship.',1,'2026-06-28 14:50:25',NULL,NULL),(4,3,NULL,2,'Check that app.css is correctly linked and clear the browser cache.',0,'2026-06-28 14:50:25',NULL,NULL),(5,5,NULL,5,'Look at university navigation patterns and keep the mobile menu simple.',1,'2026-06-28 14:50:25',NULL,NULL),(6,7,NULL,1,'hello 123 123',0,'2026-06-29 05:58:21','2026-06-29 06:35:59','2026-06-29 06:35:59'),(7,3,NULL,1,'1ad',0,'2026-06-29 09:17:49','2026-06-29 09:27:32',NULL),(8,7,NULL,7,'123123',0,'2026-07-07 16:11:54',NULL,NULL),(9,14,NULL,7,'123',0,'2026-07-07 19:21:09','2026-08-05 06:05:21',NULL),(10,15,NULL,7,'123',0,'2026-07-07 21:09:45','2026-08-10 12:00:54','2026-08-10 12:00:54'),(11,20,NULL,7,'112222',0,'2026-07-12 21:28:40','2026-08-10 12:00:49','2026-08-10 12:00:49'),(12,20,NULL,7,'123',0,'2026-07-13 16:15:16','2026-08-10 12:00:49','2026-08-10 12:00:49'),(13,21,NULL,7,'123',0,'2026-07-18 03:37:02','2026-08-10 11:57:34','2026-08-10 11:57:34'),(14,21,13,7,'123',0,'2026-07-18 03:37:06','2026-08-10 11:57:34','2026-08-10 11:57:34'),(15,21,14,7,'123',0,'2026-08-05 04:46:49','2026-08-10 11:57:34','2026-08-10 11:57:34'),(16,4,NULL,7,'No kids',0,'2026-08-05 04:51:15',NULL,NULL),(17,22,NULL,7,'<pre>\r\n123\r\n</pre>',0,'2026-08-05 06:28:34','2026-08-10 11:57:27','2026-08-10 11:57:27'),(18,22,NULL,7,'<pre>\r\ncode\r\n</pre>',0,'2026-08-05 07:22:48','2026-08-10 11:57:27','2026-08-10 11:57:27'),(19,22,NULL,7,'<pre>\r\ncode\r\n</pre>',0,'2026-08-05 08:08:58','2026-08-10 11:57:27','2026-08-10 11:57:27'),(21,25,NULL,15,'123',0,'2026-08-07 23:54:11','2026-08-10 12:17:12','2026-08-10 12:17:12'),(22,27,NULL,12,'Cool! You can use that',0,'2026-08-10 12:22:54','2026-08-10 12:23:04',NULL),(23,28,NULL,17,'QA F14 valid reply 2608102116 - temporary manual-test response.',0,'2026-08-10 14:32:30','2026-08-10 14:52:28','2026-08-10 14:52:28'),(24,28,23,17,'QA F16 nested reply 2608102116 - child of F14.',0,'2026-08-10 14:33:56','2026-08-10 14:52:28','2026-08-10 14:52:28');
/*!40000 ALTER TABLE `replies` ENABLE KEYS */;
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_deleted_at` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Lù','Phúc','lvp9852','lvp9852@gre.ac.uk','$2y$10$AbxFLbzxJXegheULQWa1beWu9kMwIB8qjTqUhtNENPwuY.BQCCIIi',NULL,'student','2026-06-28 14:50:25',NULL,NULL),(2,'Linh','Nguyen','linhnguyen','linh.nguyen@gre.ac.uk',NULL,'uploads/avatars/linh-nguyen.png','student','2026-06-28 14:50:25',NULL,NULL),(3,'Deleted','User 03','deleted_user_03','deleted_user_03@deleted.invalid',NULL,NULL,'student','2026-06-28 14:50:25','2026-08-07 22:10:29','2026-08-07 22:10:29'),(4,'An','Pham','anpham','an.pham@gre.ac.uk',NULL,NULL,'student','2026-06-28 14:50:25',NULL,NULL),(5,'Matt','Tutor','matttutor','matt.tutor@gre.ac.uk',NULL,NULL,'tutor','2026-06-28 14:50:25',NULL,NULL),(7,'Phuc','Lu','luvinhfuc','anhtuan123@gmail.com','$2y$10$qjU5a.c5GwAugHAxqjB0yurkU24Z9xGuW/aXK0nMd.phZY18m3C/K','uploads/avatars/20260805081557-d6604d62ed5d7166.webp','admin','2026-07-07 15:49:50','2026-08-05 06:15:57',NULL),(8,'Deleted','User 08','deleted_user_08','deleted_user_08@deleted.invalid',NULL,NULL,'student','2026-07-13 15:43:18','2026-08-07 22:10:08','2026-08-07 22:10:08'),(9,'Le','Le','lelelele','lelele@gmail.com','$2y$10$ykaYZI67pwuaOJR5KnjtvOsFNZ/CRi7oXP3LoGCSTzHWgUQaiGi6m',NULL,'student','2026-07-13 16:23:52',NULL,NULL),(12,'Andy','Lu','123aaaa','lvp9852@gmail.com','$2y$10$PM/02pV1t5MpouQZFtzD5OUNFdI6GzCNfxi/3vWThce8tdb0Areny',NULL,'student','2026-08-05 08:09:40','2026-08-10 12:22:44',NULL),(15,'Lù','Phúc','fuc___','luvinhfuc@gre.ac.uk','$2y$10$0ezww1LiVgsdOH3xJGq/LOwP8AIXXnfIr7XxAhU1YSVJ9TofmvhC6','uploads/avatars/20260808015337-501a35e107593af0.png','student','2026-08-07 23:53:14','2026-08-07 23:53:37',NULL),(16,'Andy','Lu','andylu','andylu@gmail.com','$2y$10$HdQGZoRRqTxRJZFqdj2rXemqqrbrf4Ayp.9PYO2kGXfyfaMZ61/O6',NULL,'student','2026-08-10 13:22:25',NULL,NULL),(17,'Deleted','User 17','deleted_user_17','deleted_user_17@deleted.invalid',NULL,NULL,'student','2026-08-10 14:17:06','2026-08-10 14:52:33','2026-08-10 14:52:33'),(18,'Minh','Nguyễn','minhnguyen260810','minh.nguyen260810@gre.ac.uk','$2y$10$OwdIktBeMnzMuFUs3OIzy.JVheo3onnObOYPKwmZKBG/3Yvxe76li',NULL,'student','2026-08-10 14:44:58',NULL,NULL);
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

-- Dump completed on 2026-08-10 21:53:06
