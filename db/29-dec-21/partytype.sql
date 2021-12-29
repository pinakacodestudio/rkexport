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
-- Table structure for table `partytype`
--

CREATE TABLE `partytype` (
  `id` bigint(20) NOT NULL,
  `partytype` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1->Active,0->Inactive',
  `createddate` datetime NOT NULL,
  `modifieddate` datetime NOT NULL,
  `addedby` bigint(20) NOT NULL,
  `modifiedby` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `partytype`
--

INSERT INTO `partytype` (`id`, `partytype`, `status`, `createddate`, `modifieddate`, `addedby`, `modifiedby`) VALUES
(1, 'test1', 1, '2021-12-23 13:05:37', '2021-12-24 09:31:33', 18, 18);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `partytype`
--
ALTER TABLE `partytype`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `partytype` (`partytype`),
  ADD KEY `status` (`status`),
  ADD KEY `createddate` (`createddate`),
  ADD KEY `modifieddate` (`modifieddate`),
  ADD KEY `addedby` (`addedby`),
  ADD KEY `modifiedby` (`modifiedby`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `partytype`
--
ALTER TABLE `partytype`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
