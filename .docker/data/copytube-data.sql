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
  `video_posted_on` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `comment`, `author`, `date_posted`, `video_posted_on`) VALUES
(90, 'An Iceland Venture 1', 'h', '2018-10-24', 'An Iceland Venture'),
(91, 'An Iceland Venture 2', 'h', '2018-10-24', 'An Iceland Venture'),
(92, 'Lava Sample 1', 'h', '2018-10-24', 'Lava Sample'),
(93, 'Lava Sample 2', 'h', '2018-10-24', 'Lava Sample'),
(94, 'Something more 1', 'h', '2018-10-24', 'Something More'),
(95, 'Something More 2', 'h', '2018-10-24', 'Something More'),
(96, 'An Iceland Venture 3', 'jkkj', '2018-10-24', 'An Iceland Venture'),
(97, 'An Iceland Venture 4', 'jkkj', '2018-10-24', 'An Iceland Venture'),
(98, 'An Iceland Venture 5', 'hjhj', '2018-10-24', 'An Iceland Venture'),
(99, 'An Iceland Venture 6', 'hjhj', '2018-10-24', 'An Iceland Venture'),
(100, 'An Iceland Venture 7', 'iu', '2018-10-24', 'An Iceland Venture'),
(101, 'Something More 3', 'GF', '2018-10-26', 'Something More'),
(104, 'Something More 4', 'dhhdgdgh', '2018-10-30', 'Something More'),
(105, 'Something More 5', 'dghdf', '2018-10-30', 'Something More'),
(106, 'Something More 5', 'dgdghdg', '2018-10-30', 'Something More'),
(107, 'Lava Sample 3', 'h', '2018-11-05', 'Lava Sample'),
(108, 'Something More 6', 'ed', '2019-01-25', 'Something More'),
(109, 'Something More 7', 'ed', '2019-01-25', 'Something More'),
(110, 'Lava Sample 3', 'ed', '2019-01-25', 'Lava Sample'),
(111, 'SomethingMore8', '', '2019-02-03', 'Something More'),
(112, 'Something More 8', 'jhk', '2019-02-05', 'Something More'),
(113, 'Something More 9', '', '2019-02-08', 'Something More'),
(114, 'Something More 10', '', '2019-02-08', 'Something More'),
(115, 'Something More 11', '', '2019-02-08', 'Something More'),
(116, 'Something more 12', 'test', '2019-02-08', 'Something More'),
(117, '', '', '0000-00-00', ''),
(118, '', '', '0000-00-00', 'Something More'),
(119, '', '', '0000-00-00', 'Something More'),
(120, '', '', '0000-00-00', ''),
(121, '', '', '0000-00-00', ''),
(122, '', 'Array', '0000-00-00', ''),
(123, '', 'Array', '0000-00-00', ''),
(124, 'hjkl', 'Array', '0000-00-00', ''),
(125, 'test', '', '0000-00-00', ''),
(126, 'test', 'Array', '0000-00-00', 'Array'),
(127, 'gfdnd\n\nfhghghfghgfjgfjhhjjhfhfgj', '', '2019-02-18', 'Something More'),
(128, 'fgdhd', '', '2019-02-18', 'Something More'),
(129, 'hgfgnf', '', '2019-03-04', 'Something More'),
(130, 'fgncgfbcv', '', '2019-03-04', 'Something More'),
(131, 'hdghnfngb', '', '2019-03-04', 'Something More'),
(132, 'testhello', '', '2019-03-04', 'Something More'),
(133, 'gsdvxc', '', '2019-03-04', 'Something More'),
(134, 'dghdgfdjgh', '', '2019-03-04', 'Something More'),
(135, 'TESTING', '', '2019-03-04', 'Something More'),
(136, 'gfjghvkhmvhgtest', '', '2019-03-04', 'Something More'),
(137, 'fhdghnvgfbTESTINGGGGGG', '', '2019-03-04', 'Something More'),
(138, 'dvbkl;kjhgcv', '', '2019-03-04', 'Something More'),
(139, '1', '', '2019-03-04', 'Something More'),
(140, 'dvvbvbm', '', '2019-03-04', 'Something More'),
(141, '2', '', '2019-03-04', 'Something More'),
(142, '3', '', '2019-03-04', 'Something More'),
(143, 'hello test', '', '2019-03-04', 'Something More'),
(144, 'fghbnm', 'Test username', '2019-03-05', 'Something More'),
(145, 'fghjnm', 'Test username', '2019-03-05', 'Something More'),
(146, 'GHNM', 'Test username', '2019-03-05', 'Something More'),
(147, 'ghbjnm', 'Test username', '2019-03-05', 'Something More'),
(148, 'mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm', 'Test username', '2019-03-05', 'Something More'),
(149, 'ghj', 'Test username', '2019-03-05', 'Something More'),
(150, 'fgnfb', 'Array', '2019-03-08', 'Something More'),
(151, 'gfdfs', 'dfghjk', '2019-03-08', 'Something More'),
(152, 'gfdfs', 'dfghjk', '2019-03-08', 'Something More'),
(153, 'fghj', '[', '2019-03-08', 'Something More'),
(154, 'rtjhgf', 'd', '2019-03-08', 'Something More'),
(155, 'hjklj', 'dfghjk', '2019-03-08', 'Something More'),
(156, 'hjklj', 'dfghjk', '2019-03-08', 'Something More'),
(157, 'hjklj', 'dfghjk', '2019-03-08', 'Something More'),
(158, 'ghjk', 'dfghjk', '2019-03-08', 'Something More'),
(159, 'drftg', 'dfghjk', '2019-03-08', 'Something More'),
(160, 'jyhgfd', 'dfghjk', '2019-03-08', 'Something More'),
(161, 'ddgh', 'dfghjk', '2019-03-08', 'Something More'),
(162, 'dfghjk', 'dfghjk', '2019-03-08', 'Something More'),
(163, 'test', 'dfghjk', '2019-03-08', 'Something More'),
(164, 'df', 'dfghjk', '2019-03-08', 'Something More'),
(165, 'fgv', 'dfghjk', '2019-03-08', 'Something More'),
(166, 'fgv', 'dfghjk', '2019-03-08', 'Something More'),
(167, 'fghj', 'dfghjk', '2019-03-08', 'Something More'),
(168, 'tfgh', 'dfghjk', '2019-03-08', 'Something More'),
(169, 'test', 'dfghjk', '2019-03-08', 'Something More'),
(170, 'wow great vid man', 'dfghjk', '2019-03-08', 'Something More'),
(171, 'test', 'dfghjk', '2019-03-13', 'Something More'),
(172, 'Test comment', 'Edward Home', '2019-03-08', 'Something More'),
(173, 'test comment lava sample', 'Edward Home', '2019-03-08', 'Lava Sample'),
(174, 'test', 'Edward Work', '2019-03-11', 'Something More'),
(175, 'DROP TABLE users', 'Edward Work', '2019-03-11', 'Something More'),
(178, 'testing SQL injection\\\"; DROP TABLE test', 'Edward Work', '2019-03-11', 'Something More'),
(179, 'Hello', 'Edward Work', '2019-03-27', 'Lava Sample'),
(180, 'A Iceland Venture 3', 'Edward Home', '2019-04-08', 'An Iceland Venture'),
(181, 'An Iceland Venture 4', 'Edward Home', '2019-04-08', 'An Iceland Venture'),
(182, 'Something More 22', 'Edward Work', '2019-04-17', 'Something More'),
(184, '\\\' OR 1=1', 'Edward Work', '2019-04-17', 'Something More'),
(185, '\\\'\\\'); DROP TABLE test; //', 'Edward Work', '2019-04-17', 'Something More'),
(186, 'Looks fab. On my list of places to go!', 'siobhanquinn', '2019-05-02', 'An Iceland Venture'),
(187, 'Siobhan adding a comment again', 'siobhanquinn', '2019-05-02', 'An Iceland Venture'),
(188, 'Siobhan comments\\n', 'siobhanquinn', '2019-05-02', 'An Iceland Venture'),
(189, 'test comment for an iceland venture', 'Edward Work', '2019-05-02', 'An Iceland Venture'),
(190, 'test', 'Edward Work', '2019-05-02', 'Lava Sample'),
(191, 'testing automated console input1', 'Edward Work', '2019-05-02', 'Something More'),
(192, 'testing automated console input', 'Edward Work', '2019-05-02', 'Something More');

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
  `test` int(11) NOT NULL
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
  `updated_at` timestamp NOT NULL DEFAULT NOW() ON UPDATE NOW(),
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email_address`, `password`, `logged_in`, `login_attempts`) VALUES
(20, 'Edward Work', 'edward.bebbington@intercity.technology', '$2y$10$NItdrDuiuOpB6cdamy3NKekjvdylBjYvCoepjJPQR0d5eAC0IKxNa', 1, 2),
(21, 'Edward Home', 'EdwardSBebbington@hotmail.com', '$2y$10$Arh7UfnUuyl8UOZlhmmiquAWtcJdq9YE0fj.bbUx4dNl5DnUW3oxS', 0, 0),
(23, 'test', 'test@hotmail.com', '$2y$10$DCTZq7Wm4SfmAV2Bu4DVXO6MGWgE33jZ6QnKSazq7XEH6ssOCvyam', 0, 3);

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

INSERT INTO `videos` (`id`, `title`, `description`, `src`, `height`, `width`, `poster`) VALUES
(1, 'Something More', 'Watch this inspirational video as we look at all of the beautiful things inside this world', 'http://localhost/copytube/videos/something_more.mp4', 220, 230, 'http://localhost/copytube/images/something_more.jpg'),
(2, 'Lava Sample', 'Watch this lava flow through the earth, burning and sizzling as it progresses', 'http://localhost/copytube/videos/lava_sample.mp4', 220, 230, 'http://localhost/copytube/images/lava_sample.jpg'),
(3, 'An Iceland Venture', 'Iceland, beautiful and static, watch as we venture through this glorious place', 'http://localhost/copytube/videos/an_iceland_venture.mp4', 220, 230, 'http://localhost/copytube/images/an_iceland_venture.jpg');

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

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
