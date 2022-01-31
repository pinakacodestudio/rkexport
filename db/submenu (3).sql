-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 31, 2022 at 11:33 AM
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
-- Table structure for table `submenu`
--

CREATE TABLE `submenu` (
  `id` bigint(20) NOT NULL,
  `mainmenuid` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` text NOT NULL,
  `icon` varchar(50) NOT NULL,
  `file` varchar(255) NOT NULL,
  `submenuvisible` text NOT NULL,
  `submenuadd` text NOT NULL,
  `submenuedit` text NOT NULL,
  `submenudelete` text NOT NULL,
  `submenuviewalldata` text NOT NULL,
  `inorder` bigint(20) NOT NULL,
  `showinrole` tinyint(4) NOT NULL,
  `submenuvisibleinrole` tinyint(1) NOT NULL,
  `submenuaddinrole` tinyint(1) NOT NULL,
  `submenueditinrole` tinyint(1) NOT NULL,
  `submenudeleteinrole` tinyint(1) NOT NULL,
  `managelog` tinyint(1) NOT NULL,
  `approvallevel` tinyint(1) NOT NULL,
  `additionalrights` text NOT NULL,
  `assignadditionalrights` text NOT NULL,
  `createddate` date DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` date DEFAULT NULL,
  `addedby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `submenu`
--

INSERT INTO `submenu` (`id`, `mainmenuid`, `name`, `url`, `icon`, `file`, `submenuvisible`, `submenuadd`, `submenuedit`, `submenudelete`, `submenuviewalldata`, `inorder`, `showinrole`, `submenuvisibleinrole`, `submenuaddinrole`, `submenueditinrole`, `submenudeleteinrole`, `managelog`, `approvallevel`, `additionalrights`, `assignadditionalrights`, `createddate`, `modifiedby`, `modifieddate`, `addedby`) VALUES
(1, 5, 'Employee Role', 'user-role', 'icon-dashboard2', '', ',1,9,20,21,22,', ',1,9,20,21,22,', ',1,9,20,21,22,', ',1,9,20,21,22,', ',23,', 2, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(2, 5, 'Employee Detail', 'user', 'icon-dashboard2', '', ',1,9,20,21,22,', ',1,9,20,21,22,', ',1,9,20,21,22,', ',1,9,20,21,22,', ',23,', 1, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(3, 2, 'Mail Format', 'email-format', 'icon-dashboard2', '', ',1,', ',1,', ',1,', ',1,', ',23,', 8, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(4, 2, 'Main Menu', 'menu', 'icon-dashboard2', '', ',1,', ',1,', ',1,', ',1,', ',23,', 1, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(5, 2, 'Sub Menu', 'menu/sub-menu', 'icon-dashboard2', '', ',1,', ',1,', ',1,', ',1,', ',23,', 2, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(6, 3, 'Product', 'product', 'icon-dashboard2', '', ',1,17,19,20,21,22,', ',1,19,20,', ',1,19,20,', ',1,19,21,20,', ',23,', 1, 1, 1, 1, 1, 1, 1, 0, '1,2,17,18,3', '{\"1\":\"#1,#2,#17,#18,#3\"}', NULL, NULL, NULL, NULL),
(8, 3, 'Product Category', 'category', 'icon-dashboard2', '', ',1,17,19,20,21,22,', ',1,19,20,', ',1,19,20,', ',1,19,21,20,', ',23,', 4, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(15, 2, 'Setting', 'setting', 'icon-dashboard2', '', ',1,', ',1,', ',1,', ',1,', ',23,', 19, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(17, 3, 'Attribute', 'attribute', '', '', ',1,17,19,20,21,22,', ',1,19,20,', ',1,19,20,', ',1,19,21,20,', ',23,', 2, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(18, 3, 'Variant', 'variant', '', '', ',1,17,19,20,21,22,', ',1,19,20,', ',1,19,20,', ',1,19,21,20,', ',23,', 3, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(19, 10, 'Country', 'country', '', '', ',1,', ',1,', ',1,', ',1,', ',23,', 1, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(20, 10, 'Province', 'province', '', '', ',1,', ',1,', ',1,', ',1,', ',23,', 2, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(21, 10, 'City', 'city', '', '', ',1,', ',1,', ',1,', ',1,', ',23,', 3, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(25, 2, 'System Configuration', 'system-configuration', '', '', ',1,', ',1,', ',1,', ',1,', '', 7, 0, 1, 1, 1, 1, 1, 0, '', '{\"25\":\"#1,#3\"}', NULL, NULL, NULL, NULL),
(35, 3, 'HSN Code', 'hsn-code', '', '', ',1,19,20,21,22,', ',1,19,20,', ',1,19,20,', ',1,19,21,20,', ',23,', 7, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(55, 3, 'Extra Charges', 'extra-charges', '', '', ',1,20,21,22,', ',1,20,', ',1,20,', ',1,21,20,', ',23,', 11, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(61, 2, 'SMS Gateway', 'sms-gateway', '', '', ',1,', ',1,', ',1,', ',1,', ',23,', 9, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(62, 2, 'SMS Format', 'sms-format', '', '', ',1,', ',1,', ',1,', ',1,', ',23,', 10, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(63, 3, 'Brand', 'brand', '', '', ',1,20,21,22,', ',1,20,', ',1,20,', ',1,20,', ',23,', 13, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(64, 3, 'Product Unit', 'product-unit', '', '', ',1,20,21,22,', ',1,20,', ',1,20,', ',1,20,', ',23,', 14, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(66, 3, 'Unit Conversation', 'unit-conversation', '', '', ',1,20,21,22,', ',1,20,', ',1,20,', ',1,20,', ',23,', 15, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(107, 10, 'Area', 'area', '', '', ',1,', ',1,', ',1,', ',1,', ',20,23,', 4, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(108, 10, 'Zone', 'zone', '', '', ',1,', ',1,', ',1,', ',1,', ',20,23,', 5, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(114, 5, 'Designation', 'designation', '', '', ',1,', ',1,', ',1,', ',1,', '', 4, 0, 1, 1, 1, 1, 0, 0, '', '', NULL, NULL, NULL, NULL),
(136, 2, 'Invoice Setting', 'invoice-setting', '', '', ',1,', ',1,', ',1,', ',1,', ',23,', 9, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(152, 2, 'Transaction Prefix', 'transaction-prefix', '', '', ',1,', ',1,', ',1,', ',1,', ',23,', 9, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(156, 2, 'Approval Levels', 'approval-levels', '', '', ',1,', ',1,', ',1,', ',1,', ',23,', 21, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(182, 3, 'Stock General Voucher', 'stock-general-voucher', '', '', ',1,', ',1,', ',1,', ',1,', '', 22, 1, 1, 1, 1, 1, 1, 0, '17', '{\"1\":\"#17\"}', NULL, NULL, NULL, NULL),
(202, 2, 'Third Level Sub Menu', 'menu/third-level-sub-menu', '', '', ',1,', ',1,', ',1,', ',1,', '', 2, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(210, 2, 'Additional Rights', 'additional-rights', '', '', ',1,', ',1,', ',1,', ',1,', '', 24, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(212, 3, 'Narration', 'narration', '', '', ',1,', ',1,', ',1,', ',1,', '', 23, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(223, 5, 'Expense Category', 'Expense_category', '', '', ',1,', ',1,', ',1,', ',1,', '', 5, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(224, 5, 'Branch', 'branch', '', '', ',1,', ',1,', ',1,', ',1,', '', 4, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(225, 5, 'Expense', 'expense', '', '', ',1,', ',1,', ',1,', ',1,', '', 8, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(226, 2, 'Payment Method', 'Payment-method', '', '', ',1,', ',1,', ',1,', ',1,', '', 30, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(227, 2, 'Currency Rate', 'currency-rate', '', '', ',1,', ',1,', ',1,', ',1,', '', 31, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(228, 2, 'Transport Type', 'Transport-type', '', '', ',1,', ',1,', ',1,', ',1,', '', 34, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(230, 2, 'Commission', 'commission', '', '', ',1,', ',1,', ',1,', ',1,', '', 32, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(231, 28, 'Party type', 'Party_type', '', '', ',1,', ',1,', ',1,', ',1,', '', 1, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(232, 28, 'Party', 'Party', '', '', ',1,', ',1,', ',1,', ',1,', '', 2, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL),
(233, 28, 'Company', 'Company', '', '', ',1,', ',1,', ',1,', ',1,', '', 3, 1, 1, 1, 1, 1, 1, 0, '', '', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `submenu`
--
ALTER TABLE `submenu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `icon` (`icon`),
  ADD KEY `file` (`file`),
  ADD KEY `inorder` (`inorder`),
  ADD KEY `name` (`name`),
  ADD KEY `id` (`id`),
  ADD KEY `mainmenuid` (`mainmenuid`),
  ADD KEY `showinrole` (`showinrole`),
  ADD KEY `submenuvisibleinrole` (`submenuvisibleinrole`),
  ADD KEY `submenuaddinrole` (`submenuaddinrole`),
  ADD KEY `submenueditinrole` (`submenueditinrole`),
  ADD KEY `submenudeleteinrole` (`submenudeleteinrole`),
  ADD KEY `managelog` (`managelog`),
  ADD KEY `approvallevel` (`approvallevel`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `submenu`
--
ALTER TABLE `submenu`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
