-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2019 at 04:57 PM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `copytube`
--
CREATE DATABASE IF NOT EXISTS `copytube` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `copytube`;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `comment` varchar(400) NOT NULL,
  `author` varchar(80) NOT NULL,
  `date_posted` date NOT NULL,
  `video_posted_on` varchar(200) NOT NULL,
  `user_id` int(11)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `comment`, `author`, `date_posted`, `video_posted_on`, `user_id`) VALUES
(172,'Super long comment to test how a comment would display when its content is so large that it might end up not overflowing correctly for example it might just keep displaying on the right and move out of view which we do not want do we','Edward Home','2019-03-08','Something More',21),
(173,'test comment lava sample','Edward Home','2019-03-08','Lava Sample',21),
(174,'test','Edward Work','2019-03-11','Something More',20),
(175,'DROP TABLE users','Edward Work','2019-03-11','Something More',20),
(178,'testing SQL injection\\\"; DROP TABLE test','Edward Work','2019-03-11','Something More',20),
(179,'Hello','Edward Work','2019-03-27','Lava Sample',20),
(182,'Something More 22','Edward Work','2019-04-17','Something More',20),
(184,'\\\' OR 1=1','Edward Work','2019-04-17','Something More',20),
(185,'\\\'\\\'); DROP TABLE test; //','Edward Work','2019-04-17','Something More',20),
(190,'Super long comment to test how a comment would display when its content is so large that it might end up not overflowing correctly for example it might just keep displaying on the right and move out of view which we do not want do we','Edward Work','2019-05-02','Lava Sample',20),
(191,'testing automated console input1','Edward Work','2019-05-02','Something More',20),
(192,'testing automated console input','Edward Work','2019-05-02','Something More',20);


-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint(20) NOT NULL,
  `session_id` varchar(155) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `session_id`, `user_id`) VALUES
(203, '838daa633554d641aeae986451a54b09', 23);

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `test` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `email_address` varchar(50) NOT NULL,
  `password` varchar(80) NOT NULL,
  `logged_in` tinyint(1) NOT NULL,
  `login_attempts` int(10) UNSIGNED NOT NULL,
  `profile_picture` varchar(155),
  `recover_token` varchar(8000),
  `updated_at` timestamp NOT NULL DEFAULT NOW() ON UPDATE NOW(),
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email_address`, `password`, `logged_in`, `login_attempts`, `profile_picture`) VALUES
(20, 'Edward Work', 'edward.bebbington@intercity.technology', '$2y$10$NItdrDuiuOpB6cdamy3NKekjvdylBjYvCoepjJPQR0d5eAC0IKxNa', 1, 3, 'img/an_iceland_venture.jpg'),
(21, 'Edward Home', 'EdwardSBebbington@hotmail.com', '$2y$10$Arh7UfnUuyl8UOZlhmmiquAWtcJdq9YE0fj.bbUx4dNl5DnUW3oxS', 1, 3, 'img/lava_sample.jpg'),
(23, 'test', 'test@hotmail.com', '$2y$10$DCTZq7Wm4SfmAV2Bu4DVXO6MGWgE33jZ6QnKSazq7XEH6ssOCvyam', 1, 3, 'img/something_more.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(400) NOT NULL,
  `src` varchar(200) NOT NULL,
  `height` int(3) NOT NULL DEFAULT '220',
  `width` int(3) NOT NULL DEFAULT '230',
  `poster` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `description`, `src`, `poster`) VALUES
(1, 'Something More', 'Watch this inspirational video as we look at all of the beautiful things inside this world', 'videos/something_more.mp4', 'img/something_more.jpg'),
(2, 'Lava Sample', 'Watch this lava flow through the earth, burning and sizzling as it progresses', 'videos/lava_sample.mp4', 'img/lava_sample.jpg'),
(3, 'An Iceland Venture', 'Iceland, beautiful and static, watch as we venture through this glorious place', 'videos/an_iceland_venture.mp4', 'img/an_iceland_venture.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
