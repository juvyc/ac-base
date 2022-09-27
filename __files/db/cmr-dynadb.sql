-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 27, 2022 at 11:46 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cmr-dynadb`
--

-- --------------------------------------------------------

--
-- Table structure for table `data`
--

DROP TABLE IF EXISTS `data`;
CREATE TABLE `data` (
  `id` int(11) NOT NULL,
  `type` varchar(45) NOT NULL COMMENT 'possible value: lead, deal, company, log, task, project',
  `datetime_created` datetime NOT NULL,
  `datetime_updated` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` varchar(45) NOT NULL COMMENT 'possible value: active, inactive, deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `data`
--

INSERT INTO `data` (`id`, `type`, `datetime_created`, `datetime_updated`, `created_by`, `status`) VALUES
(1, 'lead', '2022-09-27 11:24:58', '0000-00-00 00:00:00', 1, 'active'),
(2, 'lead', '2022-09-27 11:36:39', '0000-00-00 00:00:00', 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `meta_data`
--

DROP TABLE IF EXISTS `meta_data`;
CREATE TABLE `meta_data` (
  `id` int(11) NOT NULL,
  `data_id` bigint(20) NOT NULL,
  `meta_key` varchar(200) NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `meta_data`
--

INSERT INTO `meta_data` (`id`, `data_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'first_name', 'Testing'),
(2, 1, 'last_name', 'Testing'),
(3, 1, 'phone', '9089898989'),
(4, 1, 'email', 'dsfdsfs@site.com'),
(5, 2, 'first_name', 'Lemuel'),
(6, 2, 'last_name', 'Salazar'),
(7, 2, 'phone', '454878878'),
(8, 2, 'email', 'fsdfdsfdsf@site.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meta_data`
--
ALTER TABLE `meta_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data`
--
ALTER TABLE `data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `meta_data`
--
ALTER TABLE `meta_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
