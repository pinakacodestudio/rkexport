-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2021 at 01:25 PM
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
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `partycode` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contactno1` varchar(50) NOT NULL,
  `contactno2` varchar(50) NOT NULL,
  `gender` int(11) NOT NULL,
  `birthdate` date NOT NULL,
  `anniversarydate` date NOT NULL,
  `education` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `cityid` int(11) DEFAULT NULL,
  `provinceid` int(11) DEFAULT NULL,
  `allowforlogin` int(11) DEFAULT NULL,
  `employeeroleid` int(11) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `createddate` date DEFAULT NULL,
  `modifieddate` date DEFAULT NULL,
  `addedby` int(11) DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `party`
--

INSERT INTO `party` (`id`, `partytypeid`, `firstname`, `middlename`, `lastname`, `partycode`, `email`, `contactno1`, `contactno2`, `gender`, `birthdate`, `anniversarydate`, `education`, `address`, `cityid`, `provinceid`, `allowforlogin`, `employeeroleid`, `password`, `createddate`, `modifieddate`, `addedby`, `modifiedby`) VALUES
(1, 1, 'eACVj6jctQ', 'YpkuEYQ9G7', 'fjNjCNWUqw', 0, 'SnlFJsSBZhdedevvv@gmail.com', '9373765453', '0016261576', 0, '2003-12-18', '2021-12-10', 'SdMQHUrXC5', 'zbJkp3Rjgs', 0, 0, 0, 0, '', '2021-12-23', '2021-12-23', 18, 18);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
