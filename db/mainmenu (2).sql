-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 31, 2022 at 11:32 AM
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
-- Table structure for table `mainmenu`
--

CREATE TABLE `mainmenu` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `menuurl` text NOT NULL,
  `icon` varchar(50) NOT NULL,
  `file` varchar(255) NOT NULL,
  `menuvisible` text NOT NULL,
  `menuadd` text NOT NULL,
  `menuedit` text NOT NULL,
  `menudelete` text NOT NULL,
  `menuviewalldata` text NOT NULL,
  `inorder` bigint(20) NOT NULL,
  `showinrole` tinyint(4) NOT NULL,
  `mainmenuvisibleinrole` tinyint(1) NOT NULL,
  `mainmenuaddinrole` tinyint(1) NOT NULL,
  `mainmenueditinrole` tinyint(1) NOT NULL,
  `mainmenudeleteinrole` tinyint(1) NOT NULL,
  `managelog` tinyint(1) NOT NULL,
  `approvallevel` tinyint(1) NOT NULL,
  `additionalrights` text NOT NULL,
  `assignadditionalrights` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mainmenu`
--

INSERT INTO `mainmenu` (`id`, `name`, `menuurl`, `icon`, `file`, `menuvisible`, `menuadd`, `menuedit`, `menudelete`, `menuviewalldata`, `inorder`, `showinrole`, `mainmenuvisibleinrole`, `mainmenuaddinrole`, `mainmenueditinrole`, `mainmenudeleteinrole`, `managelog`, `approvallevel`, `additionalrights`, `assignadditionalrights`) VALUES
(1, 'Dashboard', 'dashboard', 'fa fa-dashboard', '', ',1,9,17,19,16,10,20,21,22,', ',1,9,19,20,21,22,', ',1,9,19,20,21,22,', ',1,9,19,20,21,22,', ',23,23,24,25,', 1, 1, 1, 0, 0, 0, 1, 1, '16,14,15,4,7,6,12,13,5,8,9,10,11', '{\"24\":\"#1\",\"21\":\"#16,#14,#15,#4,#7,#6,#12,#13,#5,#8,#9,#10,#11\",\"20\":\"#14,#15,#7,#6,#12,#13,#8,#9,#10,#11\",\"1\":\"#16,#14,#15,#4,#7,#6,#12,#13,#5,#8,#9,#10,#11\"}'),
(2, 'System Setting', '', 'fa fa-cogs', '', '1,', '1,', '1,', '1,', ',23,', 26, 1, 1, 1, 1, 1, 0, 0, '', ''),
(3, 'Product Management', '', 'fa fa-shopping-cart', '', ',1,17,19,20,21,22,', ',1,19,20,', ',1,19,20,', ',1,19,21,20,', ',23,', 4, 1, 1, 1, 1, 1, 0, 0, '', ''),
(5, 'Employee', '', 'fa fa-user', '', ',1,9,20,21,22,', ',1,9,20,21,22,', ',1,9,20,21,22,', ',1,9,20,21,22,', ',23,', 8, 1, 1, 1, 1, 1, 0, 0, '', ''),
(10, 'Region', '', 'fa fa-map-marker', '', ',1,', ',1,', ',1,', ',1,', ',23,', 24, 1, 0, 0, 0, 0, 0, 0, '', ''),
(19, 'Action Log', 'action-log', 'fa fa-database', '', ',1,20,21,', ',1,20,', ',1,20,', ',1,20,', ',23,23,', 23, 1, 0, 0, 0, 0, 1, 0, '1', '{\"1\":\"#1\"}'),
(28, 'Party', '', 'fa fa-user-o', '', ',1,', ',1,', ',1,', ',1,', '', 10, 1, 0, 0, 0, 0, 1, 0, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mainmenu`
--
ALTER TABLE `mainmenu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `icon` (`icon`),
  ADD KEY `file` (`file`),
  ADD KEY `id` (`id`),
  ADD KEY `inorder` (`inorder`),
  ADD KEY `showinrole` (`showinrole`),
  ADD KEY `mainmenuvisibleinrole` (`mainmenuvisibleinrole`),
  ADD KEY `mainmenuaddinrole` (`mainmenuaddinrole`),
  ADD KEY `mainmenueditinrole` (`mainmenueditinrole`),
  ADD KEY `mainmenudeleteinrole` (`mainmenudeleteinrole`),
  ADD KEY `managelog` (`managelog`),
  ADD KEY `approvallevel` (`approvallevel`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mainmenu`
--
ALTER TABLE `mainmenu`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
