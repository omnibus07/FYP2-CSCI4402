-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 02, 2024 at 03:12 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `weather_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `educational_materials`
--

CREATE TABLE `educational_materials` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `category` enum('weather_info','weather_tips') NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `educational_materials`
--

INSERT INTO `educational_materials` (`id`, `title`, `content`, `category`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Understanding Malaysian Weather Patterns', 'Detailed explanation of Malaysian weather patterns and seasons...', 'weather_info', 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37'),
(2, 'Monsoon Safety Tips', 'Essential safety tips for dealing with monsoon weather...', 'weather_tips', 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37'),
(3, 'Weather Warning Classifications', 'Understanding different types of weather warnings and their meanings...', 'weather_info', 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37'),
(4, 'Preparing for Natural Disasters', 'Steps to prepare for weather-related natural disasters...', 'weather_tips', 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37'),
(5, 'Climate Change in Southeast Asia', 'Understanding climate change impact on Southeast Asian weather...', 'weather_info', 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `region` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `latitude`, `longitude`, `region`) VALUES
(1, 'Kuala Lumpur', 3.13900000, 101.68690000, 'Central'),
(2, 'Penang', 5.41640000, 100.33270000, 'Northern'),
(3, 'Johor Bahru', 1.49270000, 103.74140000, 'Southern'),
(4, 'Kota Kinabalu', 5.98040000, 116.07350000, 'East Malaysia'),
(5, 'Kuching', 1.55330000, 110.35920000, 'East Malaysia');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `published_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `published_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'New Weather Monitoring System Launched', 'The Malaysian Meteorological Department has launched a new state-of-the-art weather monitoring system...', '2024-11-21 01:00:00', 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37'),
(2, 'Climate Change Impact on Malaysian Weather', 'Recent studies show significant changes in Malaysian weather patterns...', '2024-11-20 02:30:00', 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37'),
(3, 'Monsoon Season Preparation Guidelines', 'Guidelines for preparing for the upcoming monsoon season have been released...', '2024-11-19 06:15:00', 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37'),
(4, 'Weather App Update Released', 'New features added to the national weather monitoring app...', '2024-11-18 03:45:00', 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `profile_photo`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@weather.com', '$2y$10$75W1v0kqKDdTCWKvrksJUexPNORIc2tsSRYHFkTZaRdh54ihqC/QS', 'admin', '6745e7b801d76_1732634552.jpg', '2024-11-21 05:35:37', '2024-11-26 15:22:32'),
(2, 'john_doe', 'john@example.com', '$2y$10$Vesy9W4gZecAeKtECbqduOiPJjBxMUCO9jkcGXGi.byv6cAZ0vPdW', 'user', NULL, '2024-11-21 05:35:37', '2024-11-22 13:51:25'),
(3, 'mary_smith', 'mary@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NULL, '2024-11-21 05:35:37', '2024-11-21 05:39:32'),
(4, 'weather_expert', 'expert@weather.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, '2024-11-21 05:35:37', '2024-11-21 05:39:34'),
(5, 'jane_wilson', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NULL, '2024-11-21 05:35:37', '2024-11-21 05:39:36'),
(6, 'testuesr001', 'testuesr001@test.com', '$2y$10$75W1v0kqKDdTCWKvrksJUexPNORIc2tsSRYHFkTZaRdh54ihqC/QS', 'user', '6745e7f135a00_1732634609.jpg', '2024-11-26 05:37:30', '2024-11-26 15:23:29'),
(7, 'testuser002', 'testuser002@test.com', '$2y$10$75W1v0kqKDdTCWKvrksJUexPNORIc2tsSRYHFkTZaRdh54ihqC/QS', 'user', '6745f28a6d281_1732637322.jpg', '2024-11-26 06:07:37', '2024-11-26 16:08:42'),
(10, 'admin02', 'admin02@weather.com', '$2y$10$PtfBAiNPcNGKu13BxUCx5ejJVH9h/r33Du5FmX6oqtHGgAOPsmfLO', 'admin', '6745d35d044bf_1732629341.jpg', '2024-11-26 13:55:41', '2024-12-02 13:50:01'),
(11, 'johnsgraziano', 'johnsgraziano@test.com', '$2y$10$Qd1INBaxfLH3gjXbWSf9HOUhvEA7q6L3YshpnSm4m8MZs5azcuXNS', 'user', '674db8874b862_1733146759.jpg', '2024-12-02 13:36:22', '2024-12-02 13:39:19');

-- --------------------------------------------------------

--
-- Table structure for table `weather_forecasts`
--

CREATE TABLE `weather_forecasts` (
  `id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `temperature` decimal(4,1) NOT NULL,
  `humidity` int(11) NOT NULL,
  `wind_speed` decimal(5,2) NOT NULL,
  `weather_condition` varchar(50) NOT NULL,
  `forecast_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weather_forecasts`
--

INSERT INTO `weather_forecasts` (`id`, `location_id`, `temperature`, `humidity`, `wind_speed`, `weather_condition`, `forecast_date`, `created_at`) VALUES
(1, 1, 32.5, 75, 12.50, 'Partly Cloudy', '2024-11-21', '2024-11-21 05:35:37'),
(2, 2, 30.0, 80, 15.00, 'Rainy', '2024-11-21', '2024-11-21 05:35:37'),
(3, 3, 33.5, 70, 8.50, 'Sunny', '2024-11-21', '2024-11-21 05:35:37'),
(4, 4, 29.5, 85, 20.00, 'Thunderstorm', '2024-11-21', '2024-11-21 05:35:37'),
(5, 5, 31.0, 78, 10.50, 'Cloudy', '2024-11-21', '2024-11-21 05:35:37');

-- --------------------------------------------------------

--
-- Table structure for table `weather_warnings`
--

CREATE TABLE `weather_warnings` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `severity` enum('low','medium','high') NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `end_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `location_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `affected_area` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weather_warnings`
--

INSERT INTO `weather_warnings` (`id`, `title`, `description`, `severity`, `start_date`, `end_date`, `location_id`, `created_by`, `created_at`, `updated_at`, `affected_area`) VALUES
(1, 'Heavy Rain Alert', 'Expected heavy rainfall with possible flooding in low-lying areas', 'high', '2024-11-20 16:00:00', '2024-11-23 15:59:59', 1, 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37', NULL),
(2, 'Strong Winds Warning', 'Strong winds expected with speeds up to 60km/h', 'medium', '2024-11-21 16:00:00', '2024-11-24 15:59:59', 2, 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37', NULL),
(3, 'Thunderstorm Alert', 'Severe thunderstorms expected with possible lightning', 'high', '2024-11-21 04:00:00', '2024-11-22 04:00:00', 3, 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37', NULL),
(4, 'High Temperature Warning', 'Extreme temperatures expected to reach 38°C', 'medium', '2024-11-22 16:00:00', '2024-11-25 15:59:59', 4, 1, '2024-11-21 05:35:37', '2024-11-21 05:35:37', NULL),
(6, 'Thunder Storm with Rain', 'storm', 'low', '2024-11-26 14:25:00', '2024-11-30 14:21:00', 1, 10, '2024-11-26 14:21:47', '2024-11-26 17:27:23', '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"properties\":{},\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[101.501961,3.194266],[101.501961,3.231335],[101.603858,3.231335],[101.603858,3.194266],[101.501961,3.194266]]]}}]}'),
(9, 'Thunder Storm', 'Thunder Storm', 'high', '2024-12-03 13:57:00', '2024-12-05 13:57:00', 1, 1, '2024-12-02 13:57:45', '2024-12-02 13:57:45', '{\"type\":\"FeatureCollection\",\"features\":[{\"type\":\"Feature\",\"properties\":{},\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[101.585802,3.126386],[101.585802,3.194817],[101.789489,3.194817],[101.789489,3.126386],[101.585802,3.126386]]]}}]}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `educational_materials`
--
ALTER TABLE `educational_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `educational_materials_ibfk_1` (`created_by`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_ibfk_1` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `weather_forecasts`
--
ALTER TABLE `weather_forecasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `weather_forecasts_ibfk_1` (`location_id`);

--
-- Indexes for table `weather_warnings`
--
ALTER TABLE `weather_warnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `weather_warnings_ibfk_1` (`location_id`),
  ADD KEY `weather_warnings_ibfk_2` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `educational_materials`
--
ALTER TABLE `educational_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `weather_forecasts`
--
ALTER TABLE `weather_forecasts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `weather_warnings`
--
ALTER TABLE `weather_warnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `educational_materials`
--
ALTER TABLE `educational_materials`
  ADD CONSTRAINT `educational_materials_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `weather_forecasts`
--
ALTER TABLE `weather_forecasts`
  ADD CONSTRAINT `weather_forecasts_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Constraints for table `weather_warnings`
--
ALTER TABLE `weather_warnings`
  ADD CONSTRAINT `weather_warnings_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `weather_warnings_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
