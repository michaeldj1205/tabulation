-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 19, 2025 at 03:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mascot` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `code`, `name`, `mascot`) VALUES
(1, 'CCIS', 'College of Computing and Information Sciences', 'Yellow Phoenix'),
(2, 'CCJS', 'College of Criminal Justice Studies', 'Greywolves'),
(3, 'COED', 'College of Education', 'Blue Falcons'),
(4, 'COM', 'College of Management', 'Blue Tigers'),
(5, 'CAT', 'College of Arts and Trades', 'Maroon Lynx'),
(6, 'CEA', 'College of Engineering and Architecture', 'Orange Lion'),
(7, 'CON', 'College of Nursing', 'White Raccoon');

-- --------------------------------------------------------

--
-- Table structure for table `event_results`
--

CREATE TABLE `event_results` (
  `id` int(11) NOT NULL,
  `sport_id` int(11) NOT NULL,
  `gold_winner` int(11) DEFAULT NULL,
  `silver_winner` int(11) DEFAULT NULL,
  `bronze_winner` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_schedule`
--

CREATE TABLE `game_schedule` (
  `id` int(11) NOT NULL,
  `sport_id` int(11) NOT NULL,
  `team_a_id` int(11) NOT NULL,
  `team_b_id` int(11) NOT NULL,
  `game_day` int(1) NOT NULL CHECK (`game_day` between 1 and 5),
  `game_date` date DEFAULT NULL,
  `game_time` time DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medals`
--

CREATE TABLE `medals` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `gold` int(11) DEFAULT 0,
  `silver` int(11) DEFAULT 0,
  `bronze` int(11) DEFAULT 0,
  `total` int(11) DEFAULT 0,
  `category` varchar(20) DEFAULT 'Mix'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sports`
--

CREATE TABLE `sports` (
  `id` int(11) NOT NULL,
  `sport_name` varchar(100) NOT NULL,
  `category` enum('Men','Women','Mixed') NOT NULL DEFAULT 'Men',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sports`
--

INSERT INTO `sports` (`id`, `sport_name`, `category`, `created_at`) VALUES
(1, 'Arnis', 'Men', '2025-10-18 06:08:11'),
(2, 'Arnis', 'Women', '2025-10-18 06:08:11'),
(3, 'Sepak Takraw', 'Men', '2025-10-18 06:08:11'),
(4, 'Taekwondo', 'Men', '2025-10-18 06:08:11'),
(5, 'Taekwondo', 'Women', '2025-10-18 06:08:11'),
(6, 'Futsal', 'Women', '2025-10-18 06:08:11'),
(7, 'Chess', 'Men', '2025-10-18 06:08:11'),
(8, 'Chess', 'Women', '2025-10-18 06:08:11'),
(9, 'Basketball', 'Men', '2025-10-18 06:08:11'),
(10, 'Basketball', 'Women', '2025-10-18 06:08:11'),
(11, 'Badminton', 'Men', '2025-10-18 06:08:11'),
(12, 'Badminton', 'Women', '2025-10-18 06:08:11'),
(13, 'Athletics - Runs, Throws, and Jumps', 'Men', '2025-10-18 06:08:11'),
(14, 'Athletics - Runs, Throws, and Jumps', 'Women', '2025-10-18 06:08:11'),
(15, 'Volleyball', 'Men', '2025-10-18 06:08:11'),
(16, 'Volleyball', 'Women', '2025-10-18 06:08:11'),
(17, 'Beach Volleyball', 'Men', '2025-10-18 06:08:11'),
(18, 'Beach Volleyball', 'Women', '2025-10-18 06:08:11'),
(19, 'Softball', 'Women', '2025-10-18 06:08:11'),
(20, 'Baseball', 'Men', '2025-10-18 06:08:11'),
(21, 'Football', 'Men', '2025-10-18 06:08:11'),
(23, 'Archery', 'Men', '2025-10-18 06:08:11'),
(24, 'Archery', 'Mixed', '2025-10-18 06:08:11'),
(25, 'Swimming', 'Men', '2025-10-18 06:08:11'),
(26, 'Swimming', 'Women', '2025-10-18 06:08:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_results`
--
ALTER TABLE `event_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sport_id` (`sport_id`),
  ADD KEY `gold_winner` (`gold_winner`),
  ADD KEY `silver_winner` (`silver_winner`),
  ADD KEY `bronze_winner` (`bronze_winner`);

--
-- Indexes for table `game_schedule`
--
ALTER TABLE `game_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sport_id` (`sport_id`),
  ADD KEY `team_a_id` (`team_a_id`),
  ADD KEY `team_b_id` (`team_b_id`),
  ADD KEY `winner_id` (`winner_id`);

--
-- Indexes for table `medals`
--
ALTER TABLE `medals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_dept_category` (`department_id`,`category`);

--
-- Indexes for table `sports`
--
ALTER TABLE `sports`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `event_results`
--
ALTER TABLE `event_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `game_schedule`
--
ALTER TABLE `game_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `medals`
--
ALTER TABLE `medals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sports`
--
ALTER TABLE `sports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_results`
--
ALTER TABLE `event_results`
  ADD CONSTRAINT `event_results_ibfk_1` FOREIGN KEY (`sport_id`) REFERENCES `sports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_results_ibfk_2` FOREIGN KEY (`gold_winner`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `event_results_ibfk_3` FOREIGN KEY (`silver_winner`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `event_results_ibfk_4` FOREIGN KEY (`bronze_winner`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `game_schedule`
--
ALTER TABLE `game_schedule`
  ADD CONSTRAINT `game_schedule_ibfk_1` FOREIGN KEY (`sport_id`) REFERENCES `sports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_schedule_ibfk_2` FOREIGN KEY (`team_a_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_schedule_ibfk_3` FOREIGN KEY (`team_b_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_schedule_ibfk_4` FOREIGN KEY (`winner_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `medals`
--
ALTER TABLE `medals`
  ADD CONSTRAINT `medals_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
