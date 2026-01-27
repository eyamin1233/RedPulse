-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2026 at 02:49 AM
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
-- Database: `blooddonationmanagementsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` int(10) NOT NULL,
  `UserName` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `UserName`, `Password`, `Email`) VALUES
(12, 'Eyamin Hossain', '$2y$10$zBX4DQEGGPmBVA/0CdLnOeDyKRbm.cjbaSXAOH2jgIeaGA4Xtwd4a', 'eyamin555h@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `bloodbank`
--

CREATE TABLE `bloodbank` (
  `BloodBankID` int(10) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `ContactNumber` int(11) NOT NULL,
  `pic1` varchar(255) DEFAULT NULL,
  `pic2` varchar(255) DEFAULT NULL,
  `AdminID` int(10) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `OperatingHours` varchar(100) DEFAULT NULL,
  `LicenseFile` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bloodbank`
--

INSERT INTO `bloodbank` (`BloodBankID`, `Name`, `Location`, `ContactNumber`, `pic1`, `pic2`, `AdminID`, `email`, `password`, `OperatingHours`, `LicenseFile`) VALUES
(1, 'Quantum Blood Lab', '31/V Shilpacharya Zainul Abedin Sarak, Shantinagar, Dhaka-1217', 1714010869, 'im2.jpg', 'im3.jpg', 0, 'quantumlab@gmail.com', '$2y$10$U/B9JVb26nveisnBIrpa3.Vo8XG.eUnKn./E9ZoWQW9q708rw.yXq', NULL, NULL),
(2, 'Bangladesh Blood Bank', '12,, 22 Babar Rd, Dhaka', 1850077185, 'im4.jpg', 'im5.jpg', 1, 'bbbank@gmail.com', '$2y$10$h7UlwwKfRZFT8N60/vnqLucg.4O3wv9WasYi0xIQZTetKx9z.odBC', NULL, NULL),
(3, 'Red Crescent Blood Centre', ' 7, 5 Aurangajeb Rd, Dhaka', 1811458537, 'im1.jpg', 'im6.jpg', 2, 'rcbloodc@gmail.com', '$2y$10$63KCuelLSXLmGnoN9QDWH.DuAwuaHqhu.i.A7DfH57pm4U2GaWEFG', NULL, NULL),
(4, 'Shadhin Blood Bank & Diagnostic Center', '14/2 Nawab Katra Nimtoli Ln, Dhaka', 1760504746, 'im7.jpg', 'im8.jpg', 0, 'sbbdcentre@gmail.com', '$2y$10$vlwRZRLl4C2GqxyUUeigHOjItdwtRaNztRKAcZo02l17AlR3RPVjG', NULL, NULL),
(12, 'ABCD', 'Noton Bazar, Badda', 1989171814, NULL, NULL, NULL, 'abcd@gmail.com', '$2y$10$vTp3pV6/56yTnGA6jDjvNuItNMiaOzVVAHnWAOTlZjCkG6yQF0jAe', NULL, NULL),
(13, 'XYZ', 'Mohakhali', 1287672456, NULL, NULL, NULL, 'xyz@gmail.com', '$2y$10$fntfFYR9QH/E10YKvlOPD.dIiX/63xcI/iKJNA0sKuBmGwBEqT8ZS', NULL, NULL),
(14, 'Banani Medical', 'Banani', 1897456241, NULL, NULL, NULL, 'bananimedical@gmail.com', '$2y$10$sJvkoOAMZqiVGfnGlKMaZuL9G8q4movAtVmwG364gF/Tm2DwjeXZq', '24/7', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bloodrequest`
--

CREATE TABLE `bloodrequest` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blood_needed` int(11) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  `bloodtype` varchar(10) NOT NULL,
  `urgent_datetime` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `received_at` datetime DEFAULT NULL,
  `marked_received` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bloodrequest`
--

INSERT INTO `bloodrequest` (`id`, `user_id`, `blood_needed`, `contact`, `location`, `bloodtype`, `urgent_datetime`, `status`, `created_at`, `received_at`, `marked_received`) VALUES
(17, 40, 2, '01786724252', 'Mohakhali', 'O-', '2025-06-30 12:08:00', 0, '2025-06-30 06:08:39', NULL, 0),
(18, 34, 2, '01828790124', 'Noton Bazar, Badda', 'O-', '2025-07-03 13:30:00', 0, '2025-06-30 10:15:57', NULL, 0),
(19, 41, 1, '01891671516', 'Banasree', 'A-', '2025-07-04 12:00:00', 0, '2025-06-30 12:06:37', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `donated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donor_id`, `recipient_id`, `donated_at`) VALUES
(26, 47, 17, '2025-06-30'),
(28, 78, 42, '2026-01-20'),
(29, 48, 43, '2026-01-12'),
(30, 79, 44, '2026-01-20');

-- --------------------------------------------------------

--
-- Table structure for table `donation_answers`
--

CREATE TABLE `donation_answers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_id` int(11) DEFAULT NULL,
  `health_issue` varchar(255) DEFAULT NULL,
  `medical_conditions` varchar(255) DEFAULT NULL,
  `medications` varchar(255) DEFAULT NULL,
  `hospital_visits` varchar(255) DEFAULT NULL,
  `fatigue` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_answers`
--

INSERT INTO `donation_answers` (`id`, `user_id`, `request_id`, `health_issue`, `medical_conditions`, `medications`, `hospital_visits`, `fatigue`, `created_at`) VALUES
(36, 34, 17, 'No', 'No', 'No', 'No', 'No', '2025-06-30 05:11:48');

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `bloodtype` varchar(10) NOT NULL,
  `lastdonationdate` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`id`, `user_id`, `name`, `location`, `bloodtype`, `lastdonationdate`, `created_at`) VALUES
(47, 34, 'Karim Ahmed', 'Mirpur DOHS, Dhaka', 'O-', '2025-02-12', '2025-06-30 05:12:59'),
(48, 45, 'Asif Ahmed', 'Mohanogor', 'A+', '2026-01-12', '2026-01-20 07:01:14'),
(78, 49, 'Sakib ', 'Dhaka', 'O+', '2026-01-20', '2026-01-23 05:43:42'),
(79, 44, 'Eyamin Hossain', '205/1, Ulon, Rampura,', 'A+', '2026-01-20', '2026-01-26 20:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `ID` int(11) NOT NULL,
  `BloodBankID` int(11) DEFAULT NULL,
  `Title` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `EventDate` date DEFAULT NULL,
  `Location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`ID`, `BloodBankID`, `Title`, `Description`, `EventDate`, `Location`) VALUES
(7, 12, 'Give Blood, Save Life', 'Join us and save life\'s', '2025-08-06', 'Mohakhali'),
(8, 13, 'Your Donation Can Save  A Life', 'It is a open field blood donation camp where people can donate blood, which will be then used to save others life.', '2025-12-20', 'Mohakhali'),
(9, 14, 'Donate Blood for Life', '“Join us for a life-saving Blood Donation Drive! This event brings together volunteers and donors to help those in need of blood. By giving just a small amount, you can make a big difference in someone’s life. Together, we can support our community and save lives.”', '2026-01-22', 'Mirpur');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `ID` int(11) NOT NULL,
  `BloodBankID` int(11) DEFAULT NULL,
  `BloodType` varchar(5) DEFAULT NULL,
  `Units` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`ID`, `BloodBankID`, `BloodType`, `Units`) VALUES
(17, 12, 'A+', 8),
(18, 12, 'A-', 0),
(19, 12, 'B+', 0),
(20, 12, 'B-', 0),
(21, 12, 'AB+', 4),
(22, 12, 'AB-', 4),
(23, 12, 'O+', 0),
(24, 12, 'O-', 0),
(25, 1, 'A+', 3),
(26, 1, 'A-', 4),
(27, 1, 'B+', 2),
(28, 1, 'B-', 1),
(29, 1, 'AB+', 2),
(30, 1, 'AB-', 1),
(31, 1, 'O+', 1),
(32, 1, 'O-', 1),
(33, 13, 'A+', 20),
(34, 13, 'A-', 12),
(35, 13, 'B+', 10),
(36, 13, 'B-', 18),
(37, 13, 'AB+', 15),
(38, 13, 'AB-', 11),
(39, 13, 'O+', 10),
(40, 13, 'O-', 5),
(41, 14, 'A+', 6),
(42, 14, 'A-', 4),
(43, 14, 'B+', 6),
(44, 14, 'B-', 3),
(45, 14, 'AB+', 8),
(46, 14, 'AB-', 12),
(47, 14, 'O+', 2),
(48, 14, 'O-', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `link`, `is_read`, `created_at`) VALUES
(55, 40, 'You have submitted your health info for Karim Ahmed\'s request (O- in Noton Bazar, Badda).', NULL, 1, '2025-06-30 04:24:32'),
(57, 40, 'You have been selected as a donor for Karim Ahmed\'s request of 2 bag(s) in Noton Bazar, Badda.', NULL, 1, '2025-06-30 04:40:41'),
(58, 19, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 0, '2025-06-30 04:55:23'),
(59, 40, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 1, '2025-06-30 04:55:23'),
(60, 21, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 0, '2025-06-30 04:55:23'),
(61, 34, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 1, '2025-06-30 04:55:23'),
(62, 28, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 0, '2025-06-30 04:55:23'),
(63, 41, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 1, '2025-06-30 04:55:23'),
(64, 31, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 1, '2025-06-30 04:55:23'),
(65, 26, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 0, '2025-06-30 04:55:23'),
(66, 30, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 0, '2025-06-30 04:55:23'),
(67, 33, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 0, '2025-06-30 04:55:23'),
(68, 15, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 0, '2025-06-30 04:55:23'),
(69, 27, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 0, '2025-06-30 04:55:23'),
(70, 42, '📢 New event \'Give Blood, Save Life\' organized by ABCD on 06 Aug 2025 at Mohakhali.', NULL, 1, '2025-06-30 04:55:23'),
(71, 34, 'You have submitted your health info for Imran\'s request (O- blood in Mohakhali).', NULL, 1, '2025-06-30 05:11:50'),
(72, 34, 'You have been selected as a donor for Imran\'s request of 2 bag(s) blood in Mohakhali.', NULL, 1, '2025-06-30 05:12:59'),
(73, 19, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:34'),
(74, 44, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 1, '2025-11-29 19:26:35'),
(75, 40, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(76, 21, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(77, 34, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(78, 28, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(79, 41, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(80, 31, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(81, 26, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(82, 30, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(83, 43, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(84, 33, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(85, 15, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(86, 27, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(87, 42, 'New event \'Your Donation Can Save  A Life\' organized by XYZ on 20 Dec 2025 at Mohakhali.', NULL, 0, '2025-11-29 19:26:35'),
(88, 45, 'New event \'Donate Blood for Life\' organized by Banani Medical on 22 Jan 2026 at Mirpur.', NULL, 1, '2026-01-20 06:53:15'),
(89, 19, 'New event \'Donate Blood for Life\' organized by Banani Medical on 22 Jan 2026 at Mirpur.', NULL, 0, '2026-01-20 06:53:15'),
(90, 44, 'New event \'Donate Blood for Life\' organized by Banani Medical on 22 Jan 2026 at Mirpur.', NULL, 1, '2026-01-20 06:53:15');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `otp_hash`, `expires_at`, `created_at`) VALUES
(7, 45, '$2y$10$yKtpOWyR0.VstC4HG.Y5wOBwh.iLEYfIUhZnvPA1QYTeLOW9qREPK', '2026-01-25 20:29:15', '2026-01-25 19:24:15'),
(14, 51, '$2y$10$YyXA.yiHAlB4ys3H50nc.OZ8zFBS6/EJbwdIGCroC1VyGymnhj/Hi', '2026-01-25 23:55:06', '2026-01-25 22:50:06');

-- --------------------------------------------------------

--
-- Table structure for table `recipient`
--

CREATE TABLE `recipient` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `bloodtype` varchar(5) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  `received_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipient`
--

INSERT INTO `recipient` (`id`, `user_id`, `recipient_name`, `bloodtype`, `contact`, `location`, `received_date`) VALUES
(29, 40, 'Imran', 'O-', '01786724252', 'Mohakhali', '2025-06-30'),
(30, 45, 'Jamil', 'B+', '01917824551', 'Dhanmondi.', '2026-01-20'),
(42, 49, 'Ibrahim', 'O+', '01891718157', 'Dhanmondi.', '2026-01-20'),
(43, 45, 'Jihad Islam', 'A+', '01897181614', 'Rampura', '2026-01-12'),
(44, 44, 'Akbar Ahmed', 'A+', '01761716156', 'Dhanmondi.', '2026-01-20');

-- --------------------------------------------------------

--
-- Table structure for table `review_bloodbank`
--

CREATE TABLE `review_bloodbank` (
  `userid` int(10) NOT NULL,
  `BloodBankID` int(10) NOT NULL,
  `review` varchar(1000) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_bloodbank`
--

INSERT INTO `review_bloodbank` (`userid`, `BloodBankID`, `review`, `date`) VALUES
(1, 3, 'hi', '2025-01-22 10:17:32'),
(1, 4, 'nice', '2025-01-21 22:14:34'),
(2, 5, 'good service.', '2025-01-24 14:59:51'),
(3, 3, 'good', '2025-01-27 10:27:24');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bloodtype` varchar(255) NOT NULL,
  `lastdonationdate` date DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL DEFAULT 'photos/images.png',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `contact`, `password`, `bloodtype`, `lastdonationdate`, `location`, `profile_picture`, `created_at`) VALUES
(15, 'Robi Ahmed', 'ro@gmail.com', '01897654132', '$2y$10$0oKCFO5MG77lTE/Cp12aJOnmh0MkgGNvvDIOZCC//1tAoQxyYKVIS', 'A+', '2024-12-12', 'Rampura, Dhaka', 'photos/images.png', NULL),
(19, 'Eyamin Hossain', 'e@gmail.com', '01879896514', '$2y$10$3qbd5PUupU.AN/21XsEhAOzCuxyW9ySsuqMYJwNUiuvCLYfDMutFy', 'A+', '2025-04-11', 'Rampura, Dhaka.', 'photos/images.png', NULL),
(21, 'Jack', 'j@gmail.com', '01786578312', '$2y$10$crJZTxFnK220dZ0fVIHrA.IybciVoc9eOq89Wfv79WCJDqfhtivuW', 'A-', '2024-12-12', 'Noton Bazar, Dhaka.', 'photos/images.png', NULL),
(26, 'Rasel', 'r@gmail.com', '01675678132', '$2y$10$xZRac4e9jZGEizAKgnvWq.SzGLF/nKyC2GSsmqH.85O8We0dSeqN.', 'B+', '2025-02-20', 'Khilgaon', 'photos/images.png', NULL),
(27, 'Rubayat Hasan', 'ru@gmail.com', '01897678234', '$2y$10$zkr40bm7TnsycIV2h.CNOuJuUF2.FvTtuEheI.doTIernAao7QQ.6', 'B-', '2025-03-20', 'Dhaka', 'photos/images.png', NULL),
(28, 'Motaher', 'm@gmail.com', '01987653424', '$2y$10$p5yhMgt6p.IZ0PiopKICBepMmBaceKyQEF3.aRHDRm1AYbdk5EbZe', 'B-', '2025-02-23', 'Mohakhali', 'photos/images.png', NULL),
(30, 'Rasel', 'ra@gmail.com', '01867671413', '$2y$10$bAPTldzz35SmuwAMr8Fym.QZFdM245NcPoUztb/2uYNhhprTbsUqK', 'O+', '2025-03-20', 'Mirpur', 'photos/images.png', NULL),
(31, 'Mujahidul', 'mu@gmail.com', '01287672456', '$2y$10$C/k89OvF.qAYB8QPp3HFXOUjjGFotu2MsIDmvV9lxFcV4yjn1Gjxe', 'O-', '2025-02-12', 'Banasree', 'photos/images.png', NULL),
(33, 'Rasel Islam', 'ras@gmail.com', '01892561435', '$2y$10$WZx.A/tEQ88Iccsijh1n1ePSUK4H3Fa18ul2RmQl6ktt7DqYX2XN.', 'A+', '2025-04-12', 'Rampura', 'photos/images.png', NULL),
(34, 'Karim Ahmed', 'k@gmail.com', '01897825246', '$2y$10$6HlB4n3fvK.7mMSbEkDJgelFq4htasN/RNyEYJxR5YqUmdx4.jh3.', 'O-', '2025-02-12', 'Mirpur DOHS, Dhaka', 'photos/images.png', NULL),
(40, 'Imran', 'i@gmail.com', '01786724252', '$2y$10$vFZIclYyCbZnAzefPDWKueT8v9JLeHdchlEVEVUU6T84cf/rrX7Nm', 'O-', '2025-02-12', 'Dhaka', 'photos/images.png', NULL),
(41, 'Marzia ', 'ma@gmail.com', '01891671516', '$2y$10$ZfW5pSZi/l4ekfC64SgAR.0WK7FWKLg2eYA/W5voS9hPML3O.RYye', 'A-', '2025-06-25', 'Rangpur', 'photos/images.png', NULL),
(42, 'Rubel Islam', 'rub@gmail.com', '01879165176', '$2y$10$zwsxW.e7kD1mWFmLNnEoPeyopI21GeRXrUtps0Gjq3GnL4MCTKnG6', 'O-', '2025-02-02', 'Mohakhali', 'photos/images.png', NULL),
(43, 'Rabbi Talukder', 'rahat@gmail.com', '01897161716', '$2y$10$zzrq0l84De.2EDeqRIeJuOf29yCIOvSySPzTF/Ya3gelv.3KFFlnK', 'A-', '2025-03-02', 'Dhanmondi', 'photos/images.png', NULL),
(44, 'Eyamin Hossain', 'eyamin555h@gmail.com', '01879891312', '$2y$10$UX/.rj1MUBaMvjwP/fYwiumsLFqDRj6NzYY2HNDU8Y9qQbcFaaQbK', 'A+', '2026-01-20', '205/1, Ulon, Rampura,', 'photos/6977d23917021_680376dcd526e_default-profile.jpg', NULL),
(45, 'Asif Ahmed', 'asif@gmail.com', '01787918191', '$2y$10$5xGWriax4RTZLVlLuQ8cc.TiYBjOwmEQMGLnaUSIRZ509k409w.yi', 'A+', '2026-01-12', 'Mohanogor', 'photos/images.png', NULL),
(48, 'Rahim Islam', 'rahim@gmail.com', '01891718161', '$2y$10$8UQmL2VFKfgN2Emnf1PkzO7qDT7aVryBFtfDldzj8/9h.CvuGjwgO', 'O-', '2025-11-12', 'Mohakhali', 'photos/images.png', NULL),
(49, 'Sakib ', 's@gmail.com', '01781716151', '$2y$10$VOxHGJx2w.l4dp/tAxPYXe7FmiPqFjpCQQ5r1oGZbLzP.ldXpJeX.', 'O+', '2026-01-20', 'Dhaka', 'photos/images.png', NULL),
(51, 'Eyamin Hossain', 'ehossain213174@bscse.uiu.ac.bd', '01879891313', '$2y$10$sTKH63tZr44CghgVsQcGiugTNbqulVzdrSKt8UtAWIKGpmKLyPVLm', 'A+', '2025-10-12', '205/1, Ulon, Rampura,', 'photos/images.png', NULL),
(52, 'Takdir Islam', 'takdir@gmail.com', '01826716152', '$2y$10$c19sNIfw0kV6nL6pzDAM1eqF1yc6dScn5uGyoPnTY5A/QSJHmnSmm', 'O-', '2025-10-10', 'Banani', 'photos/images.png', NULL),
(53, 'Azad Hossain', 'azad@gmail.com', '01717171861', '$2y$10$dW4vPZIg8BYYzlTpMSfWAeKHKHk6o13fLEZGPDxcKm1YhQ6/6.bCm', 'AB+', '2025-10-11', 'Malibagh', 'photos/images.png', NULL),
(54, 'Motahar Islam', 'Mo@gmail.com', '01981917161', '$2y$10$9rrYuN0XscPw1OFfhH/6GuUII6byICuQoGKTQ7nt9kR4KoNiL8eyy', 'AB-', '2025-09-20', 'Banasree', 'photos/images.png', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `bloodbank`
--
ALTER TABLE `bloodbank`
  ADD PRIMARY KEY (`BloodBankID`),
  ADD KEY `BloodBankID` (`BloodBankID`);

--
-- Indexes for table `bloodrequest`
--
ALTER TABLE `bloodrequest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `donation_answers`
--
ALTER TABLE `donation_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BloodBankID` (`BloodBankID`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BloodBankID` (`BloodBankID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recipient`
--
ALTER TABLE `recipient`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review_bloodbank`
--
ALTER TABLE `review_bloodbank`
  ADD PRIMARY KEY (`userid`,`BloodBankID`),
  ADD KEY `userid` (`userid`,`BloodBankID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `contact` (`contact`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `AdminID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `bloodbank`
--
ALTER TABLE `bloodbank`
  MODIFY `BloodBankID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `bloodrequest`
--
ALTER TABLE `bloodrequest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `donation_answers`
--
ALTER TABLE `donation_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `recipient`
--
ALTER TABLE `recipient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bloodbank`
--
ALTER TABLE `bloodbank`
  ADD CONSTRAINT `FKBlood Bank455652` FOREIGN KEY (`AdminID`) REFERENCES `admin` (`AdminID`);

--
-- Constraints for table `bloodrequest`
--
ALTER TABLE `bloodrequest`
  ADD CONSTRAINT `bloodrequest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donation_answers`
--
ALTER TABLE `donation_answers`
  ADD CONSTRAINT `donation_answers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `donation_answers_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `bloodrequest` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donors`
--
ALTER TABLE `donors`
  ADD CONSTRAINT `donors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`BloodBankID`) REFERENCES `bloodbank` (`BloodBankID`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`BloodBankID`) REFERENCES `bloodbank` (`BloodBankID`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_bloodbank`
--
ALTER TABLE `review_bloodbank`
  ADD CONSTRAINT `review_bloodbank_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
