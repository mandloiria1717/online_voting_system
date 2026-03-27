-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 27, 2026 at 08:11 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `voting_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-23 06:01:38');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
CREATE TABLE IF NOT EXISTS `candidates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.png',
  `vote_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `name`, `position`, `department`, `description`, `photo`, `vote_count`, `created_at`) VALUES
(1, 'Alice Johnson', 'President', 'Computer Science', 'Passionate about student welfare and tech innovation. 3 years experience in student council.', 'default.png', 3, '2026-03-23 06:01:38'),
(2, 'Bob Martinez', 'President', 'Mechanical Engineering', 'Focused on infrastructure improvements and lab upgrades. Former class representative.', 'default.png', 1, '2026-03-23 06:01:38'),
(3, 'Carol White', 'Vice President', 'Business Administration', 'Dedicated to improving campus facilities and student resources.', 'default.png', 1, '2026-03-23 06:01:38'),
(4, 'David Kim', 'Vice President', 'Electronics', 'Committed to bridging the gap between students and administration.', 'default.png', 2, '2026-03-23 06:01:38'),
(5, 'Dheeraj Mishra', 'President', 'Computer Science', 'Focused on Student Welfare and Campus Improvement', 'default.png', 1, '2026-03-24 06:25:11'),
(6, 'Ajay Verma', 'President', 'MCA', 'He is teaching computer subject', 'default.png', 1, '2026-03-24 06:27:24'),
(7, 'Avika tiwari', 'President', 'BCA', 'She is a nice person and best in all', 'default.png', 0, '2026-03-24 06:28:05'),
(8, 'Abhilash Jain', 'President', 'computer science', 'best in all', 'default.png', 0, '2026-03-24 06:28:40'),
(9, 'Aditi Jain', 'Vice President', 'Enginnering', 'Dedicated candidate committed to student growth and leadership', 'default.png', 1, '2026-03-24 07:04:58'),
(10, 'Trapti Kulkarni', 'Vice President', 'FCA', 'Confident leader working for better student oppurtunities', 'default.png', 0, '2026-03-24 07:06:32');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'voting_status', 'on'),
(2, 'election_title', 'Student Council Election 2025'),
(3, 'college_name', 'Acropolis Institute of Management Studies and Research');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `has_voted` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `student_id`, `password`, `has_voted`, `is_active`, `created_at`) VALUES
(1, 'Nirali Patel', 'nirali@gmail.com', '201', '$2y$10$BmDQJ0t2x4yMMTaP.EjHdeXghNHbjjDDFLGYMlh2R4X7zB2rBxMUO', 1, 1, '2026-03-23 06:13:06'),
(2, 'Vaishnavi Parmar', 'vish@gmail.com', '202', '$2y$10$MG7b6fwhhiviILfRUbVUquXAcVMQLWAGOaK8wO.q0yuAwtathue2C', 1, 1, '2026-03-23 16:02:47'),
(3, 'Payal Jirati', 'payal@gmail.com', '203', '$2y$10$dAZ9zZXIRZKWSRir841mp.inhqu3AdseQCoHzYKW8V9nNqIMWuTIS', 1, 1, '2026-03-23 16:14:09'),
(4, 'Nitu Choudhary', 'nitu@gmail.com', '204', '$2y$10$Bv//gXSSsdjr1.pGjwA2MukxaLk.QeQLq7.DNeHbMoFc5fLa1fXMO', 1, 1, '2026-03-23 16:15:33'),
(5, 'Ansh choudhary', 'ansh@gmail.com', '205', '$2y$10$UYGEPDybyvHh4frHEmOpWeRn2TrTfvNeCTbFLhakwdE/IxY9pPU1y', 0, 1, '2026-03-24 05:15:19'),
(6, 'Naman Sharma', 'naman@gmail.com', '206', '$2y$10$I1MfQBRrojGZXWBO9btFZuzK69plYmgtuXoCVH6mlGTJscENGhqEe', 1, 1, '2026-03-24 05:37:07'),
(7, 'Soumya soni', 'soumya@gmail.com', '207', '$2y$10$eDGCoEKjH/B15.BVvpek7eewDNHBxrVPrzZa7nJtr3v0sKAbkwU2y', 0, 1, '2026-03-24 05:44:43'),
(8, 'Sushil Patel', 'sushil@gmail.com', '208', '$2y$10$C4SxoT94enAp2ZhetO2BN.ewjI7QYbNoN86VdbzjnwgZww0IMD9w.', 1, 1, '2026-03-24 05:49:05'),
(9, 'Roma Choudhary', 'roma@gmail.com', '209', '$2y$10$VD22HhF.hEYMKapZVwQJDe/ktjOxXhIDi8KYhxku9KOwFw7BXZtKC', 1, 1, '2026-03-24 05:57:47'),
(10, 'Kareena Patel', 'kareena@gmail.com', '210', '$2y$10$ZTD7RQ3.gZcpQqiD5LD.AuKUMDFUvwpS8q9IjWSu44Ugr9G1TbBGm', 1, 1, '2026-03-24 06:31:14'),
(11, 'Ram Patel', 'ram@gmail.com', '211', '$2y$10$neygk5nlC/6MmQMeAVNK/.koWC6AplA7OmiJcbrgrj656iLYpJIhq', 0, 1, '2026-03-24 06:38:57'),
(12, 'Aditi Sharma', 'aditi@gmail.com', '212', '$2y$10$5w3i/q9bacXCiU5Kdm2JjexBRbaw5VDxCeDTaCPLGP/RjkfMI/D1m', 1, 1, '2026-03-24 06:58:55'),
(13, 'Bhumi Patel', 'bhumi@gmail.com', '213', '$2y$10$CPkKM7KbwPN45u6b.yO7aOzcsiQohisdluzGcZliUCleyxdC1zYoi', 1, 1, '2026-03-24 08:41:14');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
CREATE TABLE IF NOT EXISTS `votes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `candidate_id` int NOT NULL,
  `voted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_voter` (`user_id`),
  KEY `candidate_id` (`candidate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `user_id`, `candidate_id`, `voted_at`) VALUES
(1, 1, 1, '2026-03-23 06:15:36'),
(2, 2, 2, '2026-03-23 16:03:58'),
(3, 3, 3, '2026-03-23 16:14:39'),
(4, 4, 4, '2026-03-23 16:15:55'),
(5, 6, 1, '2026-03-24 05:37:39'),
(6, 8, 4, '2026-03-24 05:49:26'),
(7, 9, 1, '2026-03-24 05:58:04'),
(8, 10, 6, '2026-03-24 06:31:34'),
(9, 12, 5, '2026-03-24 06:59:18'),
(10, 13, 9, '2026-03-24 08:41:57');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
