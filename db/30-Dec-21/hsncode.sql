-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 30, 2021 at 07:52 AM
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
-- Table structure for table `hsncode`
--

CREATE TABLE `hsncode` (
  `id` bigint(20) NOT NULL,
  `hsncode` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `integratedtax` decimal(10,2) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '0->Admin User, 1->Member',
  `createddate` datetime NOT NULL,
  `modifieddate` datetime NOT NULL,
  `addedby` bigint(20) NOT NULL,
  `modifiedby` bigint(20) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `channelid` int(11) DEFAULT 0,
  `memberid` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `hsncode`
--

INSERT INTO `hsncode` (`id`, `hsncode`, `description`, `integratedtax`, `type`, `createddate`, `modifieddate`, `addedby`, `modifiedby`, `status`, `channelid`, `memberid`) VALUES
(1, '9724855508', 'not', '18.00', 0, '2021-12-30 12:18:24', '2021-12-30 12:18:24', 18, 18, 1, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hsncode`
--
ALTER TABLE `hsncode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `hsncode` (`hsncode`),
  ADD KEY `addedby` (`addedby`),
  ADD KEY `modifiedby` (`modifiedby`),
  ADD KEY `status` (`status`),
  ADD KEY `type` (`type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hsncode`
--
ALTER TABLE `hsncode`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
