-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 30, 2021 at 12:57 PM
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
-- Table structure for table `transactionproductstockmapping`
--

CREATE TABLE `transactionproductstockmapping` (
  `id` int(11) NOT NULL,
  `action` int(11) DEFAULT NULL,
  `priceid` int(11) DEFAULT NULL,
  `productid` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `referenceid` int(11) DEFAULT NULL,
  `referencetype` int(11) DEFAULT NULL,
  `stocktype` int(11) DEFAULT NULL,
  `stocktypeid` int(11) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `modifieddate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transactionproductstockmapping`
--

INSERT INTO `transactionproductstockmapping` (`id`, `action`, `priceid`, `productid`, `qty`, `referenceid`, `referencetype`, `stocktype`, `stocktypeid`, `createddate`, `modifieddate`) VALUES
(1, 0, 2, 2, 100, 3, 5, 2, 3, '2021-12-30 00:00:00', '2021-12-30 16:44:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `transactionproductstockmapping`
--
ALTER TABLE `transactionproductstockmapping`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transactionproductstockmapping`
--
ALTER TABLE `transactionproductstockmapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
