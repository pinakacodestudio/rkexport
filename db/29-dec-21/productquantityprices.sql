-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2021 at 07:01 AM
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
-- Table structure for table `productquantityprices`
--

CREATE TABLE `productquantityprices` (
  `id` int(11) NOT NULL,
  `price` float DEFAULT 0,
  `quantity` int(11) DEFAULT 0,
  `discount` decimal(10,2) DEFAULT 0.00,
  `productpricesid` int(11) NOT NULL DEFAULT 0,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `addedby` int(11) NOT NULL DEFAULT 0,
  `modifiedby` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `productquantityprices`
--

INSERT INTO `productquantityprices` (`id`, `price`, `quantity`, `discount`, `productpricesid`, `createddate`, `modifieddate`, `addedby`, `modifiedby`) VALUES
(1, 25, 1, '0.00', 1, NULL, NULL, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `productquantityprices`
--
ALTER TABLE `productquantityprices`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `productquantityprices`
--
ALTER TABLE `productquantityprices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
