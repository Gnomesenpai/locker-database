-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Generation Time: Mar 25, 2025 at 12:53 PM
-- Server version: 11.4.5-MariaDB-ubu2404
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lockers`
--

-- --------------------------------------------------------

--
-- Table structure for table `locker`
--
CREATE DATABASE `locker`;
USE `locker`;
CREATE TABLE `locker` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `reference` text NOT NULL,
  `date` date DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `IP` text DEFAULT NULL
) ENGINE=innodb DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `locker`
--

INSERT INTO `locker` (`id`, `name`, `reference`, `date`, `last_modified`, `IP`) VALUES
(1, '', '', '1970-01-01', NULL, NULL),
(2, '', '', '1970-01-01', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `locker`
--
ALTER TABLE `locker`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
