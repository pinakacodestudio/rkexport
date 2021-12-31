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
-- Table structure for table `party_doc`
--

CREATE TABLE `party_doc` (
  `id` int(11) NOT NULL,
  `partyid` int(11) DEFAULT NULL,
  `doc` varchar(250) DEFAULT NULL,
  `docname` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `party_doc`
--

INSERT INTO `party_doc` (`id`, `partyid`, `doc`, `docname`) VALUES
(1, 18, 'test1640693576.png', '7461347696'),
(2, 19, 'test1640693618.png', '7461347696'),
(3, 20, 'test1640693698.png', '746134769612'),
(4, NULL, '', NULL),
(5, NULL, 'test1640746781.png', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `party_doc`
--
ALTER TABLE `party_doc`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `party_doc`
--
ALTER TABLE `party_doc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
