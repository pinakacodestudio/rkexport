-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2021 at 01:46 PM
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
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(20) NOT NULL,
  `channelid` text NOT NULL,
  `roleid` varchar(255) NOT NULL,
  `workforchannelid` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobileno` bigint(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `cityid` bigint(20) NOT NULL,
  `reportingto` bigint(20) NOT NULL,
  `code` varchar(255) NOT NULL,
  `designationid` bigint(20) NOT NULL,
  `checkintime` time NOT NULL,
  `checkouttime` time NOT NULL,
  `newtransferinquiry` tinyint(1) NOT NULL,
  `followupstatuschange` text NOT NULL,
  `inquirystatuschange` text NOT NULL,
  `inquiryreportmailsending` tinyint(4) NOT NULL,
  `eodmailsending` tinyint(4) NOT NULL,
  `subemployeenotification` text NOT NULL,
  `myeodstatus` tinyint(4) NOT NULL,
  `teameodstatus` tinyint(4) NOT NULL,
  `sidebarcount` tinyint(4) NOT NULL COMMENT '0-Today,1-All',
  `status` tinyint(1) NOT NULL COMMENT '1->active, 0->inactive',
  `createddate` datetime NOT NULL,
  `modifieddate` datetime NOT NULL,
  `addedby` bigint(20) NOT NULL,
  `modifiedby` bigint(20) NOT NULL,
  `partycord` varchar(30) NOT NULL,
  `gender` int(11) NOT NULL,
  `branchid` int(11) NOT NULL,
  `countryid` int(11) DEFAULT NULL,
  `stateid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `channelid`, `roleid`, `workforchannelid`, `name`, `email`, `mobileno`, `password`, `image`, `address`, `cityid`, `reportingto`, `code`, `designationid`, `checkintime`, `checkouttime`, `newtransferinquiry`, `followupstatuschange`, `inquirystatuschange`, `inquiryreportmailsending`, `eodmailsending`, `subemployeenotification`, `myeodstatus`, `teameodstatus`, `sidebarcount`, `status`, `createddate`, `modifieddate`, `addedby`, `modifiedby`, `partycord`, `gender`, `branchid`, `countryid`, `stateid`) VALUES
(18, '9,1,2,3,4,8', '1', '9,1,2,3,4', 'admin', 'jjasani@gmail.com', 9173569325, 'aXVONHJiMTB1YzdmNHVRUjFsTW5ydz09', 'ardeecitymall1639750574.jpg', '', 0, 0, '', 1, '00:00:00', '00:00:00', 1, '6,1', '7,5', 0, 1, '1', 1, 1, 1, 1, '2020-09-25 12:12:46', '2021-12-17 19:46:14', 1, 18, '', 0, 0, NULL, NULL),
(34, '', '20', '1,2,3', 'Khodidas Raiyani', 'khodidas@rkinfotechindia.com', 9725679797, 'RFMwUDVPYlViRTlKV3lWbGljaWFVUT09', '', 'Rajkot', 0, 18, '', 2, '00:00:00', '00:00:00', 1, '1', '', 1, 1, '1', 1, 1, 0, 1, '2020-10-09 14:27:14', '2021-02-05 16:03:40', 18, 18, '', 0, 0, NULL, NULL),
(36, '', '22', '9,1,2,3,4', 'Piyush', 'piyush@rkinfotechindia.com', 9875899797, 'RFMwUDVPYlViRTlKV3lWbGljaWFVUT09', '', '', 0, 18, '', 4, '00:00:00', '00:00:00', 1, '', '', 1, 1, '1', 1, 1, 0, 1, '2020-10-09 14:42:06', '2021-02-05 16:03:04', 18, 18, '', 0, 0, NULL, NULL),
(37, '', '22', '', 'Priya', 'tankpriya298@gmail.com', 8238220623, 'TXJyWDFqT2RFY2lIRnJOUWY0aEdEdz09', 'sparklogo1612952858.png', '', 0, 18, '', 1, '00:00:00', '00:00:00', 0, '', '', 0, 0, '0', 0, 0, 0, 1, '2021-02-10 15:57:38', '2021-12-22 11:46:48', 18, 18, '', 0, 0, NULL, NULL),
(38, '', '0', '', 'OuLkh7xpqK', 'hpJ8ite8BH@gmail.com', 8973662156, 'NTF1eWRjZzdrY1l5aXpXelNIS1N1UT09', '', 'vvv', 0, 37, '', 1, '00:00:00', '00:00:00', 0, '', '', 0, 0, '0', 0, 0, 0, 0, '2021-12-22 15:27:59', '2021-12-22 16:13:01', 18, 18, 'lcjVY9u8bJ', 1, 0, NULL, NULL),
(39, '', '22', '', '57rwNsGgAAssss', 'hpJ8ite8BssH@gmail.com', 272385948, 'ZnRzZnRPMWw2NjNVdUY2a0ZycDRDUT09', '', '8Vp5s0aPW8', 3, 36, '', 1, '00:00:00', '00:00:00', 0, '', '', 0, 0, '0', 0, 0, 0, 0, '2021-12-22 16:28:24', '2021-12-22 16:28:24', 18, 18, '1h0lcqbnWS', 1, 1, NULL, NULL),
(40, '', '22', '', 'hyPcy4S2uj', 'SnlFJsSBZh@gmail.com', 5280018997, 'QmtPTXMzMWNLQnY1T29EM3dtYzl3Zz09', '', 'oCuXFznnSVttttt', 4, 18, '', 1, '00:00:00', '00:00:00', 0, '', '', 0, 0, '0', 0, 0, 0, 0, '2021-12-22 17:06:28', '2021-12-22 17:08:14', 18, 18, 'APh3xyi3Yt', 0, 1, NULL, NULL),
(41, '', '22', '', 'ueXD8AkHIJvvv', 'SnlFJsSBZhvvv@gmail.com', 9060613784, 'bTJFRnRGV2RzKzlBMEpQWUFWRTllQT09', '', 'NGxJJGMloO', 2, 36, '', 1, '00:00:00', '00:00:00', 0, '', '', 0, 0, '0', 0, 0, 0, 0, '2021-12-22 17:12:04', '2021-12-22 18:08:08', 18, 18, 'nvyrqbgGyZvvv', 0, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modifieddate` (`modifieddate`),
  ADD KEY `id` (`id`),
  ADD KEY `roleid` (`roleid`),
  ADD KEY `name` (`name`),
  ADD KEY `mobileno` (`mobileno`),
  ADD KEY `email` (`email`),
  ADD KEY `password` (`password`),
  ADD KEY `ceateddate` (`createddate`),
  ADD KEY `modifiedby` (`modifiedby`),
  ADD KEY `status` (`status`),
  ADD KEY `addedby` (`addedby`),
  ADD KEY `reportingto` (`reportingto`),
  ADD KEY `code` (`code`),
  ADD KEY `designationid` (`designationid`),
  ADD KEY `cityid` (`cityid`),
  ADD KEY `inquiryreportmailsending` (`inquiryreportmailsending`),
  ADD KEY `eodmailsending` (`eodmailsending`),
  ADD KEY `myeodstatus` (`myeodstatus`),
  ADD KEY `teameodstatus` (`teameodstatus`),
  ADD KEY `sidebarcount` (`sidebarcount`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
