-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2025 at 07:18 AM
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
-- Database: `carrental`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'Aryan@gmail.com', '$2y$10$isHuMNsuqjpM07WpWUPecOS.2GKtw9OfLA7XL1vEuqSxGUyPO1hKa', '2025-10-05 10:12:27');

-- --------------------------------------------------------

--
-- Table structure for table `admin_secret`
--

CREATE TABLE `admin_secret` (
  `id` int(11) NOT NULL,
  `secret_code` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_secret`
--

INSERT INTO `admin_secret` (`id`, `secret_code`) VALUES
(1, '23082221116');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `payment_status` enum('Paid','Pending') NOT NULL DEFAULT 'Pending',
  `booking_status` enum('Pending','Approved','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_delay_reason` text DEFAULT NULL,
  `notification_flag` tinyint(1) NOT NULL DEFAULT 0,
  `new_due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `car_id`, `start_date`, `end_date`, `total_amount`, `payment_method`, `payment_status`, `booking_status`, `created_at`, `payment_delay_reason`, `notification_flag`, `new_due_date`) VALUES
(1, 2, 1, '2025-09-08', '2025-09-11', 12000.00, 'Card', '', 'Cancelled', '2025-09-08 11:45:22', NULL, 0, NULL),
(2, 2, 2, '2025-09-09', '2025-09-12', 8000.00, 'Card', 'Paid', '', '2025-09-08 11:54:51', NULL, 0, NULL),
(3, 2, 3, '2025-09-08', '2025-09-09', 3600.00, 'UPI', 'Paid', '', '2025-09-08 12:09:40', NULL, 0, NULL),
(4, 3, 1, '2025-09-11', '2025-09-11', 3000.00, 'Card', 'Paid', '', '2025-09-09 18:36:43', NULL, 0, NULL),
(5, 4, 4, '2025-09-10', '2025-09-12', 6000.00, 'UPI', 'Paid', '', '2025-09-09 18:45:18', NULL, 0, NULL),
(7, 2, 6, '2025-09-13', '2025-09-25', 15600.00, 'UPI', 'Paid', '', '2025-09-13 16:09:22', NULL, 0, NULL),
(8, 2, 6, '2025-09-13', '2025-09-25', 15600.00, 'Card', '', 'Approved', '2025-09-13 16:14:05', NULL, 0, NULL),
(9, 2, 6, '2025-09-13', '2025-09-14', 2400.00, 'Card', '', 'Approved', '2025-09-13 16:26:25', NULL, 0, NULL),
(10, 2, 6, '2025-09-13', '2025-09-14', 2400.00, 'Card', '', 'Approved', '2025-09-13 16:30:25', NULL, 0, NULL),
(11, 2, 6, '2025-09-14', '2025-09-14', 1200.00, 'Card', '', 'Approved', '2025-09-13 16:32:59', NULL, 0, NULL),
(12, 2, 6, '2025-09-14', '2025-09-14', 1200.00, 'Card', '', 'Approved', '2025-09-13 16:38:45', NULL, 0, NULL),
(13, 2, 6, '2025-09-14', '2025-09-14', 1200.00, 'Card', '', 'Approved', '2025-09-13 16:42:03', NULL, 0, NULL),
(14, 2, 6, '2025-09-13', '2025-09-14', 2400.00, 'Card', '', 'Approved', '2025-09-13 16:45:45', NULL, 0, NULL),
(15, 2, 6, '2025-09-13', '2025-09-14', 2400.00, 'Card', '', 'Approved', '2025-09-13 16:48:37', NULL, 0, NULL),
(16, 2, 6, '2025-09-13', '2025-09-14', 1200.00, 'UPI', 'Paid', '', '2025-09-13 16:51:33', NULL, 0, NULL),
(17, 2, 1, '2025-09-13', '2025-09-14', 3000.00, 'UPI', 'Paid', '', '2025-09-13 17:22:46', NULL, 0, NULL),
(18, 2, 6, '2025-09-13', '2025-09-13', 1200.00, 'Card', '', 'Approved', '2025-09-13 17:34:02', NULL, 0, NULL),
(19, 2, 6, '2025-09-13', '2025-09-14', 1200.00, 'UPI', 'Paid', '', '2025-09-13 17:34:27', NULL, 0, NULL),
(20, 2, 1, '2025-09-13', '2025-09-13', 3000.00, 'Card', '', 'Approved', '2025-09-13 18:06:49', NULL, 0, NULL),
(21, 2, 1, '2025-09-13', '2025-09-13', 3000.00, 'Card', '', 'Approved', '2025-09-13 18:07:45', NULL, 0, NULL),
(22, 2, 1, '2025-09-13', '2025-09-13', 3000.00, 'Card', '', 'Approved', '2025-09-13 18:09:01', NULL, 0, NULL),
(23, 2, 1, '2025-09-13', '2025-09-14', 6000.00, '', '', 'Approved', '2025-09-13 18:19:36', NULL, 0, NULL),
(24, 2, 1, '2025-09-13', '2025-09-13', 3000.00, 'UPI', 'Paid', '', '2025-09-13 18:20:19', NULL, 0, NULL),
(25, 2, 1, '2025-09-18', '2025-10-03', 48000.00, '', 'Pending', 'Cancelled', '2025-09-14 16:14:15', NULL, 1, NULL),
(26, 2, 2, '2025-09-18', '2025-09-29', 24000.00, '', '', 'Approved', '2025-09-14 16:28:01', NULL, 0, NULL),
(27, 2, 3, '2025-09-19', '2025-09-20', 3600.00, '', '', 'Approved', '2025-09-14 16:29:56', NULL, 0, NULL),
(28, 2, 3, '2025-09-26', '2025-09-26', 1800.00, '', '', 'Approved', '2025-09-14 16:31:49', NULL, 0, NULL),
(29, 2, 1, '2025-09-14', '2025-09-15', 6000.00, '', '', 'Approved', '2025-09-14 16:36:08', NULL, 0, NULL),
(30, 2, 1, '2025-09-18', '2025-09-19', 6000.00, '', '', 'Approved', '2025-09-18 08:09:21', NULL, 0, NULL),
(31, 2, 1, '2025-09-18', '2025-09-19', 6000.00, '', '', 'Approved', '2025-09-18 17:30:19', NULL, 0, NULL),
(32, 2, 1, '2025-09-20', '2025-09-21', 6000.00, '', '', 'Approved', '2025-09-20 08:01:54', NULL, 0, NULL),
(33, 2, 1, '2025-09-21', '2025-09-21', 3000.00, '', '', 'Approved', '2025-09-20 08:04:00', NULL, 0, NULL),
(34, 2, 2, '2025-09-20', '2025-09-21', 4000.00, '', '', 'Approved', '2025-09-20 08:07:27', NULL, 0, NULL),
(35, 2, 3, '2025-09-20', '2025-09-21', 3600.00, 'UPI', 'Paid', '', '2025-09-20 08:13:10', NULL, 0, NULL),
(36, 2, 4, '2025-09-20', '2025-09-20', 2000.00, 'UPI', 'Paid', '', '2025-09-20 08:16:31', NULL, 0, NULL),
(37, 2, 7, '2025-09-21', '2025-09-21', 4000.00, 'UPI', 'Paid', '', '2025-09-20 08:30:22', NULL, 0, NULL),
(38, 2, 1, '2025-09-20', '2025-09-20', 3000.00, 'UPI', 'Paid', '', '2025-09-20 08:35:43', NULL, 0, NULL),
(39, 2, 1, '2025-09-22', '2025-09-22', 3000.00, 'UPI', 'Paid', '', '2025-09-20 09:43:21', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `car_name` varchar(100) NOT NULL,
  `model` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `fuel_type` varchar(20) NOT NULL,
  `seating_capacity` int(2) NOT NULL,
  `rate_per_day` decimal(10,2) NOT NULL,
  `location` varchar(50) NOT NULL,
  `availability` enum('Available','Booked') NOT NULL DEFAULT 'Available',
  `image` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `car_name`, `model`, `type`, `fuel_type`, `seating_capacity`, `rate_per_day`, `location`, `availability`, `image`) VALUES
(1, 'Toyota Innova', '2023', 'SUV', 'Diesel', 7, 3000.00, 'Mumbai', 'Booked', 'innova.jpg'),
(2, 'Honda City', '2022', 'Sedan', 'Petrol', 5, 2000.00, 'Delhi', 'Available', 'honda_city.jpg'),
(3, 'Swift Dzire', '2022', 'Sedan', 'Petrol', 5, 1800.00, 'Bangalore', 'Available', 'Maruti Swift.jpg'),
(4, 'Thar', '2024', 'SUV', 'Diesel', 4, 2000.00, 'Idar', 'Available', 'Mahindra Thar comes with a 4 seating capacity and….jpg'),
(6, 'Verna', '2023', 'Sedan', 'Diesel', 5, 1200.00, 'Idar', 'Available', 'Mahindra Thar comes with a 4 seating capacity and….jpg'),
(7, 'scorpio', '2022', 'SUV', 'Diesel', 5, 4000.00, 'Idar', 'Available', 'images.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `subject`, `message`, `created_at`) VALUES
(1, 2, 'hii', 'done', '2025-09-13 15:42:49'),
(2, 2, 'Om', 'Best Card', '2025-09-13 15:50:52'),
(3, 2, 'Om', 'hii', '2025-09-18 17:32:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `city` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(10) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `mobile`, `city`, `image`, `password`, `role`) VALUES
(1, 'John Doe', 'user@example.com', '9876543210', 'Mumbai', NULL, '$2y$10$ziQHnQ6Tm6w1tl9zQ8UoqeVgN1Xo1lC0aIpoQfwMZC5k8hYj0y2Gq', 'user'),
(2, 'Aryan', 'Aryan@gmail.com', '1234567890', 'Anandpura', '68cc413e0038b.JPG', '$2y$10$cCR43OLiYIDfRC7QFG1Q0OKg/OvltXu4RvhDqeT3FCwKnqod6Qxha', 'user'),
(3, 'Kalpesh', 'Kalpesh@gmail.com', '9426315421', 'Idar', '68c0736e6e85e.jpg', '$2y$10$y6sLm//nEZsCFwNB6T3fE.54uDDyrcQ/Aq/udNCX2Sp7PwIU2v5eC', 'user'),
(4, 'het', 'het21@gmail.com', '3211234321', 'himmatnagar', '68c07539c3567.png', '$2y$10$V7bDDCPU5Jk3IAE.NmwZj.a6TwcgLlrT4hLL4449So0rdMNVaxucC', 'user'),
(5, 'prit', 'patelprit6836@gmail.com', '9601139753', 'Idar', NULL, '$2y$10$RJiAgeGYdJtj8LYlxOATJuIy7t.SCeB.uxtATyGIy/EYnj1R36AGe', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_secret`
--
ALTER TABLE `admin_secret`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_secret`
--
ALTER TABLE `admin_secret`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
