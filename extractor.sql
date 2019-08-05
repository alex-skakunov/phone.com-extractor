-- phpMyAdmin SQL Dump
-- version 4.7.8
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 11, 2018 at 07:36 PM
-- Server version: 5.7.21
-- PHP Version: 5.6.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `extractor`
--

-- --------------------------------------------------------

--
-- Table structure for table `import_stats`
--

CREATE TABLE `import_stats` (
  `id` int(10) UNSIGNED NOT NULL,
  `started_at` datetime NOT NULL,
  `finished_at` datetime DEFAULT NULL,
  `status` enum('in progress','fail','success') DEFAULT NULL,
  `error_message` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `phones`
--

CREATE TABLE `phones` (
  `number` varchar(20) NOT NULL,
  `area_code` char(5) NOT NULL,
  `price` decimal(9,4) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `name` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--


--
-- Indexes for dumped tables
--

--
-- Indexes for table `import_stats`
--
ALTER TABLE `import_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phones`
--
ALTER TABLE `phones`
  ADD KEY `number` (`number`),
  ADD KEY `area_code` (`area_code`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `import_stats`
--
ALTER TABLE `import_stats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

INSERT INTO `settings` (`name`, `value`) VALUES 
('password', '1'),
('remote file url', '');

ALTER TABLE `import_stats` ADD `way` ENUM('auto','manual') NOT NULL DEFAULT 'auto' AFTER `error_message`;
ALTER TABLE `import_stats` ADD `filesize` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `way`;

ALTER TABLE `import_stats`
  ADD `file_id` TINYINT UNSIGNED NOT NULL DEFAULT '1' AFTER `id`,
  ADD INDEX (`file_id`);

ALTER TABLE `import_stats`
  ADD `records_number` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `way`,
  ADD INDEX (`records_number`);

ALTER TABLE `phones`
  ADD `file_id` TINYINT UNSIGNED NOT NULL DEFAULT '1' AFTER `price`,
  ADD INDEX (`file_id`);

UPDATE `settings`
SET `name` = 'remote file1 url'
WHERE `name` = 'remote file url';

INSERT INTO `settings` (`name`, `value`) VALUES
('remote file2 url', 'https://s3-us-west-2.amazonaws.com/files.phone.com/scs/available_numbers2.zip');