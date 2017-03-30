-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 30, 2017 at 02:40 PM
-- Server version: 5.6.16-1~exp1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `raisinbread_17_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `issues_default_assignee`
--

CREATE TABLE `issues_default_assignee` (
  `center_id` tinyint(2) UNSIGNED NOT NULL,
  `issue_category_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'This refers to the user''s numeric id and not their username. srsly.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='I seriously do not like "plural" forms of words on my tables';

--
-- Indexes for table `issues_default_assignee`
--
ALTER TABLE `issues_default_assignee`
  ADD PRIMARY KEY (`issue_category_id`,`center_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `center_id` (`center_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `issues_default_assignee`
--
ALTER TABLE `issues_default_assignee`
  ADD CONSTRAINT `issues_default_assignee_ibfk_1` FOREIGN KEY (`issue_category_id`) REFERENCES `issues_categories` (`categoryID`),
  ADD CONSTRAINT `issues_default_assignee_ibfk_2` FOREIGN KEY (`center_id`) REFERENCES `psc` (`CenterID`),
  ADD CONSTRAINT `issues_default_assignee_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
