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
-- Table structure for table `productprices`
--

CREATE TABLE `productprices` (
  `id` bigint(20) NOT NULL,
  `productid` bigint(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` bigint(20) NOT NULL,
  `pointsforseller` int(11) NOT NULL,
  `pointsforbuyer` int(11) NOT NULL,
  `unitid` bigint(20) NOT NULL,
  `barcode` varchar(30) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `weight` decimal(14,3) NOT NULL,
  `minimumstocklimit` int(11) NOT NULL,
  `minimumsalesprice` decimal(14,2) NOT NULL,
  `minimumorderqty` int(50) DEFAULT NULL,
  `maximumorderqty` int(11) DEFAULT 0,
  `pricetype` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `productprices`
--

INSERT INTO `productprices` (`id`, `productid`, `price`, `stock`, `pointsforseller`, `pointsforbuyer`, `unitid`, `barcode`, `sku`, `weight`, `minimumstocklimit`, `minimumsalesprice`, `minimumorderqty`, `maximumorderqty`, `pricetype`) VALUES
(1, 1, '0.00', 0, 0, 0, 1, '4960569593', 'ngjLdH9wdP', '0.000', 0, '0.00', NULL, 0, 0),
(2, 2, '120.00', 0, 0, 0, 1, '', 'yiknlC5YlR', '0.000', 0, '0.00', NULL, 0, 0),
(3, 2, '15.00', 0, 0, 0, 1, '', 'yiknlC5YlR', '0.000', 0, '0.00', NULL, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `productprices`
--
ALTER TABLE `productprices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `productid` (`productid`),
  ADD KEY `price` (`price`),
  ADD KEY `stock` (`stock`),
  ADD KEY `pointsforseller` (`pointsforseller`),
  ADD KEY `pointsforbuyer` (`pointsforbuyer`),
  ADD KEY `unitid` (`unitid`),
  ADD KEY `barcode` (`barcode`),
  ADD KEY `sku` (`sku`),
  ADD KEY `minimumstocklimit` (`minimumstocklimit`),
  ADD KEY `weight` (`weight`),
  ADD KEY `minimumsalesprice` (`minimumsalesprice`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `productprices`
--
ALTER TABLE `productprices`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
