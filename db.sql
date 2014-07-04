-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Server version: 5.5.37-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `framework`
--

-- --------------------------------------------------------

--
-- Table structure for table `controllers`
--

CREATE TABLE IF NOT EXISTS `controllers` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `priority` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `controllers`
--

INSERT INTO `controllers` (`ID`, `controller`, `active`, `priority`) VALUES
(1, 'home', 1, '0');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`ID`, `key`, `value`) VALUES
(1, 'siteurl', 'http://localhost/');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `super_admin` tinyint(11) NOT NULL DEFAULT '0',
  `is_social` tinyint(4) NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `reset_key` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `reset_expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `session_user_uid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pswd_reset_time` int(11) NOT NULL,
  `confirmed` tinyint(4) NOT NULL,
  `confirm_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `gender` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `dob` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `pic_large` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `pic_small` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=135 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `password_salt`, `email`, `active`, `admin`, `super_admin`, `is_social`, `banned`, `reset_key`, `reset_expires`, `deleted`, `session_user_uid`, `pswd_reset_time`, `confirmed`, `confirm_code`, `name`, `gender`, `dob`, `pic_large`, `pic_small`) VALUES
(1, 'garvitdelhi', 'sha256:1000:6+c4jxBRBuBt3de7cM354nozI2QpwiTg:zFnpDUE6BJqYieYhC6HvaDBSAU0hiA4o', 'sha256:1000:Sv3Dya0jJkRBIVnEa2VmcO3lQItztGu4:FAdMdSetdHDrI5bGA6UbWisIzDL1QHvk', 'garvitdelhi@gmail.com', 1, 0, 0, 0, 0, '', '2014-06-23 19:39:28', 0, 'sha256:1000:RiEWOG556FUVQQQ0GMdGmxv0TGmgToiz:PXOiJXsmDGkaCnWkftbBS/mJDHe7vGQ1', 0, 0, '', 'Garvit', '', '', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
