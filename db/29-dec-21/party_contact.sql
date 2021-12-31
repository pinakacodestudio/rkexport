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
-- Table structure for table `party_contact`
--

CREATE TABLE `party_contact` (
  `id` int(11) NOT NULL,
  `partyid` int(11) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `contactno` varchar(20) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `anniversarydate` date DEFAULT NULL,
  `email` varchar(20) DEFAULT NULL,
  `createddate` date NOT NULL,
  `modifieddate` date NOT NULL,
  `addedby` int(11) NOT NULL,
  `modifiedby` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `party_contact`
--

INSERT INTO `party_contact` (`id`, `partyid`, `firstname`, `lastname`, `contactno`, `birthdate`, `anniversarydate`, `email`, `createddate`, `modifieddate`, `addedby`, `modifiedby`) VALUES
(1, 1, 'kmp9jpLy6D', 'Jay', '6417991886', '2003-12-25', '2021-12-20', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(2, 2, 'kmp9jpLy6D', 'Jay', '6417991886', '2003-12-25', '2021-12-20', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(3, 3, 'kmp9jpLy6D', 'Jay', '6417991886', '2003-12-25', '2021-12-20', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(4, 4, 'q3ZYB2TlKN', 'Jay', '3346049652', '2003-12-01', '2021-12-01', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(5, 5, 'q3ZYB2TlKN', 'Jay', '3346049652', '2003-12-01', '2021-12-01', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(6, 6, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(7, 7, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(8, 8, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(9, 9, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(10, 10, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(11, 11, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(12, 12, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(13, 13, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(14, 14, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(15, 15, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(16, 16, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(17, 17, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(18, 18, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(19, 19, 'pHAQ4C9cRo', 'Jay', '6140739274', '2003-12-16', '2021-12-21', 'cmNKSDJKCNSK@gmail.c', '2021-12-28', '2021-12-28', 18, 18),
(20, 20, 'pHAQ4C9cRo12', 'Jay12', '614073927412', '2003-12-27', '2021-12-19', 'cmN1KSDJKCNSK2@gmail', '2021-12-28', '2021-12-29', 18, 18);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `party_contact`
--
ALTER TABLE `party_contact`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `party_contact`
--
ALTER TABLE `party_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
