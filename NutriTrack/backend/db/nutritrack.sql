-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 31, 2026 at 06:53 PM
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
-- Database: `nutritrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_summaries`
--

DROP TABLE IF EXISTS `daily_summaries`;
CREATE TABLE IF NOT EXISTS `daily_summaries` (
  `summary_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `summary_date` date NOT NULL,
  `total_calories` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`summary_id`),
  UNIQUE KEY `unique_user_summary` (`user_id`,`summary_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_summaries`
--

INSERT INTO `daily_summaries` (`summary_id`, `user_id`, `summary_date`, `total_calories`) VALUES
(1, 1, '2026-10-31', 644.50);

-- --------------------------------------------------------

--
-- Table structure for table `foods`
--

DROP TABLE IF EXISTS `foods`;
CREATE TABLE IF NOT EXISTS `foods` (
  `food_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `calories_per_100g` decimal(8,2) NOT NULL,
  `protein_per_100g` decimal(8,2) NOT NULL DEFAULT '0.00',
  `carbohydrates_per_100g` decimal(8,2) NOT NULL DEFAULT '0.00',
  `fat_per_100g` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`food_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `foods`
--

INSERT INTO `foods` (`food_id`, `name`, `calories_per_100g`, `protein_per_100g`, `carbohydrates_per_100g`, `fat_per_100g`, `created_at`) VALUES
(1, 'Apple', 52.00, 0.30, 14.00, 0.20, '2026-10-31 18:50:52'),
(2, 'Boiled Egg', 155.00, 13.00, 1.10, 11.00, '2026-10-31 18:50:52'),
(3, 'Oats', 389.00, 16.90, 66.30, 6.90, '2026-10-31 18:50:52'),
(4, 'Chicken Breast', 165.00, 31.00, 0.00, 3.60, '2026-10-31 18:50:52'),
(5, 'Cooked Rice', 130.00, 2.70, 28.00, 0.30, '2026-10-31 18:50:52');

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

DROP TABLE IF EXISTS `meals`;
CREATE TABLE IF NOT EXISTS `meals` (
  `meal_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `meal_date` date NOT NULL,
  `meal_type` enum('breakfast','lunch','dinner','snack') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`meal_id`),
  KEY `fk_meals_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`meal_id`, `user_id`, `meal_date`, `meal_type`) VALUES
(1, 1, '2026-10-31', 'breakfast'),
(2, 1, '2026-10-31', 'lunch');

-- --------------------------------------------------------

--
-- Table structure for table `meal_entries`
--

DROP TABLE IF EXISTS `meal_entries`;
CREATE TABLE IF NOT EXISTS `meal_entries` (
  `entry_id` int NOT NULL AUTO_INCREMENT,
  `meal_id` int NOT NULL,
  `food_id` int NOT NULL,
  `serving_grams` decimal(8,2) NOT NULL,
  PRIMARY KEY (`entry_id`),
  KEY `fk_entries_meal` (`meal_id`),
  KEY `fk_entries_food` (`food_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meal_entries`
--

INSERT INTO `meal_entries` (`entry_id`, `meal_id`, `food_id`, `serving_grams`) VALUES
(1, 1, 3, 50.00),
(2, 1, 2, 100.00),
(3, 2, 4, 100.00),
(4, 2, 5, 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_goals`
--

DROP TABLE IF EXISTS `nutrition_goals`;
CREATE TABLE IF NOT EXISTS `nutrition_goals` (
  `goal_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `goal_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_value` decimal(10,2) NOT NULL,
  PRIMARY KEY (`goal_id`),
  KEY `fk_goals_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nutrition_goals`
--

INSERT INTO `nutrition_goals` (`goal_id`, `user_id`, `goal_type`, `target_value`) VALUES
(1, 1, 'daily_calories', 2000.00),
(2, 1, 'daily_protein', 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Eman Alajim', 'eman.alajim@nutritrack.demo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-10-31 18:50:52');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_summaries`
--
ALTER TABLE `daily_summaries`
  ADD CONSTRAINT `fk_summaries_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meals`
--
ALTER TABLE `meals`
  ADD CONSTRAINT `fk_meals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meal_entries`
--
ALTER TABLE `meal_entries`
  ADD CONSTRAINT `fk_entries_food` FOREIGN KEY (`food_id`) REFERENCES `foods` (`food_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entries_meal` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`meal_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nutrition_goals`
--
ALTER TABLE `nutrition_goals`
  ADD CONSTRAINT `fk_goals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
