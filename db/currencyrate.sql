-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 21, 2021 at 08:22 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rkexport`
--

-- --------------------------------------------------------

--
-- Table structure for table `currencyrate`
--

CREATE TABLE `currencyrate` (
  `id` int(11) NOT NULL,
  `currency` varchar(30) DEFAULT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `createddate` datetime DEFAULT current_timestamp(),
  `modifieddate` datetime DEFAULT current_timestamp(),
  `addedby` bigint(20) DEFAULT NULL,
  `modifiedby` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `currencyrate`
--

INSERT INTO `currencyrate` (`id`, `currency`, `value`, `date`, `createddate`, `modifieddate`, `addedby`, `modifiedby`) VALUES
(3, 'OHW5xeDfR7', '123.00', '2021-11-13', '2021-12-20 17:31:37', '2021-12-21 10:29:01', 18, 18),
(9, 'SNP8H0FYrP', '123.00', '2021-12-21', '2021-12-21 10:35:13', '2021-12-21 10:35:13', 18, 18),
(10, 'SNP8H0FYrP', '123.00', '2021-12-21', '2021-12-21 10:35:24', '2021-12-21 10:35:24', 18, 18),
(11, 'tVdKpjvRZQ', '123.00', '2021-12-21', '2021-12-21 10:36:19', '2021-12-21 10:36:19', 18, 18),
(12, '2G1ehMRwVe', '123.00', '2021-12-21', '2021-12-21 10:44:06', '2021-12-21 10:46:35', 18, 18);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `currencyrate`
--
ALTER TABLE `currencyrate`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `currencyrate`
--
ALTER TABLE `currencyrate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
