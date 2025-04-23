-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 06:03 PM
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
-- Database: `sysarch`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `announcement_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) NOT NULL DEFAULT 'Unknown Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `announcement_text`, `created_at`, `created_by`) VALUES
(5, 'Good Day!', '2025-03-24 15:48:56', 'Admin'),
(10, 'Hi ', '2025-04-18 13:01:17', 'Admin'),
(11, 'Happy birthday!', '2025-04-18 13:01:37', 'Admin'),
(12, 'gegegegegege', '2025-04-18 13:01:52', 'Admin'),
(13, 'hasjdkasdlasdqeqweda', '2025-04-18 13:03:01', 'Admin'),
(14, 'hello', '2025-04-23 15:35:18', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `id_no` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `record_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `id_no`, `message`, `date`, `record_id`) VALUES
(2, 2, 'hi', '2025-04-18', 58),
(3, 123456, 'heeeeeeeeeeeeeeeey', '2025-04-19', 57),
(4, 5000, 'good day!', '2025-04-19', 56);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `id_no` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `lab_number` varchar(50) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `id_no`, `name`, `lab_number`, `purpose`, `date`, `status`, `created_at`) VALUES
(1, '2', '', '540', 'PHP Programming', '2025-04-20', 'Approved', '2025-04-19 06:06:04'),
(2, '1000', '', '530', 'C Programming', '2025-04-26', 'Approved', '2025-04-19 06:10:55'),
(3, '1000', '', '526', 'C Programming', '2025-04-28', 'Rejected', '2025-04-19 06:11:12'),
(4, '1000', '', '526', 'C Programming', '2025-04-28', 'Approved', '2025-04-19 06:11:54'),
(5, '123456', '', '530', 'Java Programming', '2025-04-30', 'Rejected', '2025-04-19 06:28:45'),
(6, '1000', '', '540', 'PHP Programming', '2025-04-23', 'Approved', '2025-04-19 06:34:51'),
(7, '1000', '', '540', 'Java Programming', '2025-04-30', 'Approved', '2025-04-19 12:11:57'),
(8, '789', '', '530', 'Java Programming', '2025-04-20', 'Approved', '2025-04-19 12:14:17');

-- --------------------------------------------------------

--
-- Table structure for table `sit_in_records`
--

CREATE TABLE `sit_in_records` (
  `id` int(11) NOT NULL,
  `id_no` varchar(50) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `lab_number` varchar(50) NOT NULL,
  `remaining_sessions` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `year_level` varchar(10) NOT NULL,
  `course` varchar(255) DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` varchar(10) DEFAULT 'Active',
  `date` date DEFAULT curdate(),
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit_in_records`
--

INSERT INTO `sit_in_records` (`id`, `id_no`, `purpose`, `lab_number`, `remaining_sessions`, `name`, `year_level`, `course`, `time_in`, `time_out`, `status`, `date`, `points`) VALUES
(41, '100', 'PHP Programming', '540', 20, 'Zaira asddasdsa Mier', '', NULL, '23:50:51', '17:51:18', 'Offline', '2025-04-17', 0),
(42, '2', 'C Programming', '540', 23, 'frank  ocean', '', NULL, '23:54:32', '17:54:48', 'Offline', '2025-04-17', 0),
(43, '1000', 'PHP Programming', 'Mac Laboratory', 26, 'zaira b mier', '', NULL, '23:58:06', '17:58:53', 'Offline', '2025-04-17', 0),
(44, '123456', 'PHP Programming', '540', 30, 'lapu  lapu', '', NULL, '00:03:57', '18:06:02', 'Offline', '2025-04-18', 0),
(45, '3', 'ASP.Net Programming', '526', 30, 'rain  mier', '', NULL, '00:04:35', '18:04:47', 'Offline', '2025-04-18', 0),
(46, '2', 'Java Programming', 'Mac Laboratory', 22, 'frank  ocean', '', NULL, '12:39:42', '07:17:31', 'Offline', '2025-04-18', 1),
(47, '100', 'PHP Programming', '540', 19, 'Zaira asddasdsa Mier', '', NULL, '13:13:06', '07:13:33', 'Offline', '2025-04-18', 0),
(48, '123456', 'Java Programming', '530', 29, 'lapu  lapu', '', NULL, '13:19:27', '07:19:44', 'Offline', '2025-04-18', 0),
(49, '5000', 'Java Programming', 'Mac Laboratory', 30, 'hot  wheels', '', NULL, '13:26:13', '07:26:28', 'Offline', '2025-04-18', 1),
(56, '5000', 'C Programming', 'Mac Laboratory', 29, 'hot  wheels', '', NULL, '14:50:26', '08:51:03', 'Offline', '2025-04-18', 0),
(57, '123456', 'PHP Programming', 'Mac Laboratory', 26, 'lapu  lapu', '', NULL, '14:58:56', '15:09:15', 'Offline', '2025-04-18', 4),
(58, '2', 'PHP Programming', '540', 19, 'frank  ocean', '', NULL, '15:02:49', '23:22:12', 'Offline', '2025-04-18', 0),
(59, '3', 'PHP Programming', 'Mac Laboratory', 29, 'rain  mier', '', NULL, '15:05:32', '09:05:59', 'Offline', '2025-04-18', 0),
(61, '2', 'PHP Programming', '540', 30, 'frank  ocean', '', NULL, '23:26:42', NULL, 'Active', '2025-04-23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `id_no` int(11) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `yr_level` varchar(10) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `remaining_sessions` int(11) DEFAULT 30,
  `role` varchar(20) DEFAULT 'user',
  `purpose` varchar(255) DEFAULT NULL,
  `lab_number` varchar(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `id_no`, `profile_picture`, `lastname`, `firstname`, `middlename`, `yr_level`, `course`, `address`, `remaining_sessions`, `role`, `purpose`, `lab_number`, `name`) VALUES
(3, 'admin', '$2y$10$xlJWzLMMmctCj/gh3d0vT.ekll1ojjOya0u7lwSqWQdwJxNNP1jx2', '', NULL, NULL, 'User', 'Admin', NULL, NULL, NULL, NULL, 30, 'admin', NULL, NULL, NULL),
(15, 'mier', '$2y$10$Yn/.dYG7BTfyqWDQEH3RUul.5Jc8SUTDETYJcpenRVlbW8EY9IR82', 'z4mier@gmail.com', 1000, '', 'mier', 'zaira', 'b', '3', 'BSIT', '1137 Rose Homes Andres Abellana Extension Guadalupe Cebu City', 30, 'user', NULL, NULL, NULL),
(16, 'frank', '$2y$10$vD2VIrOfI9rp7mD6EFHWEO8kvWRt.VJr6wkzXu7RpcWvXM6WoHIBW', '', 2, NULL, 'ocean', 'frank', '', '1', 'BSED', NULL, 29, 'user', NULL, NULL, NULL),
(18, 'lapu', '$2y$10$wKiDyFczXp87k7/BYMksOOpR8RB.p0QiE3uTGcbmdMJB0HtCLxi7G', '', 123456, NULL, '', 'Lapu', '', '1', 'BSCpE', NULL, 28, 'user', NULL, NULL, NULL),
(19, 'hotwheels', '$2y$10$fpeDxZMh0oKkNYrkmOdOO.va/dqw9LU4QvieEfM2rCT6qdD8yQ1bG', '', 5000, NULL, 'wheels', 'hot', '', '3', 'bscpe', NULL, 30, 'user', NULL, NULL, NULL),
(20, 'zaira', '$2y$10$XSzpQnivVbVQYmKvGnyi3ezRNfvOml6EHff0h2I21SI6XR21p7YyC', '', 789, NULL, 'mier', 'zaira', 'b', '3', 'bsit', NULL, 30, 'user', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `fk_feedback_user` (`id_no`),
  ADD KEY `fk_feedback_record` (`record_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_no` (`id_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_record` FOREIGN KEY (`record_id`) REFERENCES `sit_in_records` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_feedback_user` FOREIGN KEY (`id_no`) REFERENCES `users` (`id_no`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
