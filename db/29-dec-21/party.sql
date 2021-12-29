-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2021 at 05:37 AM
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
-- Table structure for table `party`
--

CREATE TABLE `party` (
  `id` int(11) NOT NULL,
  `partytypeid` int(11) NOT NULL,
  `partycode` int(11) NOT NULL,
  `address` text NOT NULL,
  `cityid` int(11) DEFAULT NULL,
  `provinceid` int(11) DEFAULT NULL,
  `websitename` varchar(30) NOT NULL,
  `companyid` int(11) NOT NULL,
  `gst` varchar(20) NOT NULL,
  `pan` varchar(20) NOT NULL,
  `countryid` int(11) NOT NULL,
  `billingaddress` text DEFAULT NULL,
  `shippingaddress` text DEFAULT NULL,
  `courieraddress` text DEFAULT NULL,
  `openingdate` date DEFAULT NULL,
  `openingamount` float DEFAULT NULL,
  `createddate` date DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` date DEFAULT NULL,
  `addedby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `party`
--

INSERT INTO `party` (`id`, `partytypeid`, `partycode`, `address`, `cityid`, `provinceid`, `websitename`, `companyid`, `gst`, `pan`, `countryid`, `billingaddress`, `shippingaddress`, `courieraddress`, `openingdate`, `openingamount`, `createddate`, `modifiedby`, `modifieddate`, `addedby`) VALUES
(1, 1, 0, '', 13550, 815, 'mQJkFZoj8o', 1, 'LWYvMFsZ82', 'ndo7HmfyXd', 49, 'uv6tYkH9mm', '9AYWh9I9eU', 'SG6Z4mvaJ3', '2021-12-28', 120021, '2021-12-28', 18, '2021-12-28', 18),
(2, 1, 0, '', 13550, 815, 'mQJkFZoj8o', 1, 'LWYvMFsZ82', 'ndo7HmfyXd', 49, 'uv6tYkH9mm', '9AYWh9I9eU', 'SG6Z4mvaJ3', '2021-12-28', 120021, '2021-12-28', 18, '2021-12-28', 18),
(3, 1, 0, '', 13550, 815, 'mQJkFZoj8o', 1, 'LWYvMFsZ82', 'ndo7HmfyXd', 49, 'uv6tYkH9mm', '9AYWh9I9eU', 'SG6Z4mvaJ3', '2021-12-28', 120021, '2021-12-28', 18, '2021-12-28', 18),
(4, 1, 0, '', 1011, 12, 'wS981q6RJT', 1, '0eAq7SvnbF', 'RBLREnOhZN', 101, 'WwjphVgRJQ', 'dOR76Dxj6Q', '3W2EwAwLIS', '2021-12-01', 120021, '2021-12-28', 18, '2021-12-28', 18),
(5, 1, 0, '', 1011, 12, 'wS981q6RJT', 1, '0eAq7SvnbF', 'RBLREnOhZN', 101, 'WwjphVgRJQ', 'dOR76Dxj6Q', '3W2EwAwLIS', '2021-12-01', 120021, '2021-12-28', 18, '2021-12-28', 18),
(6, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(7, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(8, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(9, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(10, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(11, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(12, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(13, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(14, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(15, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(16, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(17, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(18, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(19, 1, 0, '', 872, 12, '2O2ib0IYJ7', 1, 'MjhiZLLcum', 'RU2JDsuPWV', 101, 'DvAEYxe1hx', '09qolcNuGq', 'BVRRyotliC', '2021-12-08', 120021, '2021-12-28', 18, '2021-12-28', 18),
(20, 1, 0, '', 872, 12, '2O2ib0IYJ712', 1, 'MjhiZLLcum12', 'RU2JDsuPWV12', 101, 'DvAEYxe1hx112', '09qolcNuGq112', 'BVRRyotliC121', '1914-07-01', 120021, '2021-12-29', 18, '2021-12-29', 18);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `party`
--
ALTER TABLE `party`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `party`
--
ALTER TABLE `party`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
