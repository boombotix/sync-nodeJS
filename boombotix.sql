-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 28, 2013 at 12:40 PM
-- Server version: 5.5.32
-- PHP Version: 5.4.17-5+debphp.org~precise+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `boombotix`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_playlist_share`
--

CREATE TABLE IF NOT EXISTS `tb_playlist_share` (
  `session_id` bigint(20) NOT NULL,
  `dj_user_id` bigint(20) NOT NULL,
  `listner_user_id` bigint(20) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 invited ,2 for listening',
  `share_datetime` datetime NOT NULL,
  KEY `session_id` (`session_id`),
  KEY `dj_user_id` (`dj_user_id`),
  KEY `listner_user_id` (`listner_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_playlist_share`
--

INSERT INTO `tb_playlist_share` (`session_id`, `dj_user_id`, `listner_user_id`, `status`, `share_datetime`) VALUES
(45051379322174, 264, 264, 2, '2013-09-16 09:03:37'),
(45051379322174, 264, 266, 2, '2013-09-16 09:51:47'),
(4021379333011, 270, 267, 0, '2013-09-16 12:03:31'),
(45051379322174, 264, 271, 0, '2013-09-16 12:08:27'),
(85591379357512, 272, 274, 2, '2013-09-16 18:52:47'),
(85591379357512, 272, 275, 2, '2013-09-16 18:54:27'),
(85591379357512, 272, 276, 0, '2013-09-16 18:56:36'),
(13931379361916, 277, 276, 0, '2013-09-16 20:05:16'),
(13931379361916, 277, 267, 0, '2013-09-17 04:48:44'),
(76661379392399, 271, 278, 2, '2013-09-17 05:00:00'),
(66381379395218, 278, 271, 0, '2013-09-17 05:23:46'),
(29291379335218, 267, 268, 0, '2013-09-17 05:37:59'),
(29291379335218, 267, 279, 0, '2013-09-17 05:38:19'),
(87491379396443, 268, 280, 0, '2013-09-17 05:41:56'),
(29291379335218, 267, 271, 0, '2013-09-17 07:17:44'),
(76661379392399, 271, 267, 0, '2013-09-17 07:27:34'),
(41501379423893, 285, 286, 2, '2013-09-17 13:19:32'),
(30431379424573, 287, 288, 2, '2013-09-17 13:30:22'),
(13931379361916, 277, 289, 2, '2013-09-17 14:59:44'),
(72801379424680, 288, 267, 0, '2013-09-18 06:41:45'),
(82171379515410, 294, 295, 2, '2013-09-18 14:45:20'),
(19571379499993, 293, 296, 2, '2013-09-19 05:18:35'),
(98841379585966, 305, 306, 0, '2013-09-19 10:23:10'),
(98841379585966, 305, 272, 0, '2013-09-19 10:31:50'),
(96441379589552, 306, 267, 0, '2013-09-19 11:19:12'),
(79681379590137, 307, 308, 0, '2013-09-19 11:28:57'),
(79681379590137, 307, 267, 0, '2013-09-19 11:29:09'),
(45261379590287, 309, 267, 0, '2013-09-19 11:31:44'),
(96441379589552, 306, 270, 2, '2013-09-20 12:52:19'),
(5921379911063, 321, 320, 0, '2013-09-23 04:37:43'),
(5921379911063, 321, 321, 0, '2013-09-23 04:38:24'),
(21941379591022, 300, 320, 2, '2013-09-23 09:22:21'),
(85591379357512, 272, 322, 0, '2013-09-23 20:04:13'),
(27501379975067, 322, 294, 0, '2013-09-23 22:25:48'),
(29291379335218, 267, 306, 0, '2013-09-24 04:24:29'),
(29291379335218, 267, 324, 0, '2013-09-24 04:25:35'),
(96441379589552, 306, 326, 0, '2013-09-24 04:43:19'),
(76741379998037, 326, 306, 0, '2013-09-24 04:47:17'),
(82171379515410, 294, 322, 2, '2013-09-24 16:11:44'),
(21811380164440, 328, 294, 2, '2013-09-26 03:01:28'),
(82171379515410, 294, 327, 0, '2013-09-26 03:05:03'),
(82171379515410, 294, 276, 2, '2013-09-26 03:06:25'),
(98841379585966, 305, 267, 2, '2013-09-26 11:38:15'),
(27501379975067, 322, 319, 2, '2013-09-28 04:27:49'),
(98841379585966, 305, 330, 2, '2013-09-28 10:10:39');

-- --------------------------------------------------------

--
-- Table structure for table `tb_playlist_share_archive`
--

CREATE TABLE IF NOT EXISTS `tb_playlist_share_archive` (
  `session_id` bigint(20) NOT NULL,
  `dj_user_id` bigint(20) NOT NULL,
  `listner_user_id` bigint(20) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 invited ,2 for listening',
  `share_datetime` datetime NOT NULL,
  KEY `dj_user_id` (`dj_user_id`),
  KEY `listner_user_id` (`listner_user_id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_pubnub_data`
--

CREATE TABLE IF NOT EXISTS `tb_pubnub_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ntp_date` varchar(100) NOT NULL,
  `song_status` varchar(100) NOT NULL,
  `selected_index` int(11) NOT NULL,
  `selected_song` varchar(200) NOT NULL,
  `dj_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `bit_rate` varchar(50) NOT NULL,
  `npackets` varchar(50) NOT NULL,
  `num_bytes` varchar(50) NOT NULL,
  `song_url` varchar(200) NOT NULL,
  `audio_bytes` text NOT NULL,
  `data_offset` text NOT NULL,
  `song_file_length` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dj_id` (`dj_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=553 ;

--
-- Dumping data for table `tb_pubnub_data`
--

INSERT INTO `tb_pubnub_data` (`id`, `ntp_date`, `song_status`, `selected_index`, `selected_song`, `dj_id`, `message`, `bit_rate`, `npackets`, `num_bytes`, `song_url`, `audio_bytes`, `data_offset`, `song_file_length`) VALUES
(521, '2013-09-28 07:36:09  0000', 'streamsong', 0, 'https://ec-media.soundcloud.com/PldDrJ1kBUDS.128.mp3?ff61182e3c2ecefa438cd02102d0e385713f0c1faf3b033959566bfa0c00ef1680bd4b8de3e135dee9a6344f90aeb136b6ebf5d37f3b7770b0209e495e733f7d29b36be6e8', 284, 'ankush', '128000', '19', '7942', 'https://api.soundcloud.com/tracks/79413036/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28', '61673', '2465', ''),
(528, '2013-09-28 11:12:23  0000', 'streamsong', 1, 'https://ec-media.soundcloud.com/eLHO7duoFBrt.128.mp3?ff61182e3c2ecefa438cd02102d0e385713f0c1faf3b033959566bfa0c03ec14ed17a05c4d2b2564a72b3f6ebf4b1893379ebd86e8c81e9debeb8beebe5c78bbae1005e912', 267, 'ankush', '128000', '19', '7942', 'https://api.soundcloud.com/tracks/111470045/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28', '63457', '2465', '3698519'),
(551, '2013-09-28 12:01:21  0000', 'streamsong', 6, 'https://ec-media.soundcloud.com/HozTI2uQiYpo.128.mp3?ff61182e3c2ecefa438cd02102d0e385713f0c1faf3b033959566bfa0c02ed1cc8b5a9bc55ba0a97cb0caf902f51b60aae3732fd9f1e38f9a11180acde43d4153a225c4b29', 305, 'ankush', '127000', '19', '7942', 'https://api.soundcloud.com/tracks/72229784/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28', '62014', '2465', '112674271'),
(552, '2013-09-28 12:31:01  0000', 'streamsong', 0, 'https://ec-media.soundcloud.com/lZQTXgYwtI2Y.128.mp3?ff61182e3c2ecefa438cd02102d0e385713f0c1faf3b033959566bfa0c02ea12003c32942c7d691d6e18a422ec09055caa5d6cfd77d869dfa9db6c625e2cf3634b62b4d69c', 268, 'ankush', '128000', '19', '7942', 'https://api.soundcloud.com/tracks/102602084/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28', '63458', '2465', '1598274');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE IF NOT EXISTS `tb_users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `user_fb_id` bigint(20) NOT NULL,
  `user_access_token` varchar(200) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_image` varchar(200) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `user_device_token` varchar(500) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login_date` datetime NOT NULL,
  `user_total_playlist` tinyint(4) NOT NULL,
  `user_active` tinyint(4) NOT NULL,
  `playlist_order` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_email` (`user_email`),
  KEY `password` (`password`),
  KEY `user_access_token` (`user_access_token`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=331 ;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`user_id`, `user_name`, `password`, `user_fb_id`, `user_access_token`, `user_email`, `user_image`, `longitude`, `latitude`, `country_code`, `user_device_token`, `reg_date`, `last_login_date`, `user_total_playlist`, `user_active`, `playlist_order`) VALUES
(263, 'Shreya Kapoor', '', 100005212775486, 'MTAwMDA1MjEyNzc1NDg2', '', 'https://graph.facebook.com/100005212775486/picture?width=168&height=168', 76.810112, 30.718849, '', '(null)', '2013-09-16 08:10:48', '2013-09-17 05:33:07', 0, 1, ''),
(264, 'ankush', 'f5e086de8eadba176a6c267c256de422', 0, 'YW5rdXNoQGNsaWNrLWxhYnMuY29t', 'ankush@click-labs.com', 'user.png', -122.406418, 37.785835, '', '(null)', '2013-09-16 09:02:39', '2013-09-17 07:44:39', 0, 1, ''),
(265, 'Hard Tester', '', 100005838482296, 'MTAwMDA1ODM4NDgyMjk2', '', 'https://graph.facebook.com/100005838482296/picture?width=168&height=168', 76.810127, 30.718769, '', '(null)', '2013-09-16 09:19:21', '2013-09-20 05:42:05', 0, 1, ''),
(266, 'ankush2', 'ff2b0cfc87ea3f31664fb07a1a3b35e2', 0, 'YW5rdXNoMkBtZS5jb20=', 'ankush2@me.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-16 09:51:23', '2013-09-19 05:32:19', 0, 1, ''),
(267, 'shreya', '38ab1c982af7d3ddd7d4c8b950b6cd16', 0, 'c2hyZXlhQGNsaWNrbGFicy5pbg==', 'shreya@clicklabs.in', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-16 11:22:23', '2013-09-28 11:52:12', 0, 1, '1467,1466,1465,1464,1463,1462,1461,1460,1459,1443,1415'),
(268, 'manpreet.kaur', '2f823aa82d0d025893142cd6a5be93fa', 0, 'bWFucHJlZXQua2F1ckBjbGlja2xhYnMuaW4=', 'manpreet.kaur@clicklabs.in', 'user.png', 76.810143, 30.718779, '', '(null)', '2013-09-16 11:23:24', '2013-09-28 12:39:09', 0, 1, ''),
(269, 'harmandeep.kaur', '13713abeeb69f833d08c7c6c8a7a36a0', 0, 'aGFybWFuZGVlcC5rYXVyQGNsaWNrbGFicy5pbg==', 'harmandeep.kaur@clicklabs.in', 'user.png', 151.210999, -33.863400, '', '(null)', '2013-09-16 11:32:22', '0000-00-00 00:00:00', 0, 1, ''),
(270, 'gagandeep', 'cf5f742474c31acc3a77ca223811ebbd', 0, 'Z2FnYW5kZWVwQGNsaWNrbGFicy5pbg==', 'gagandeep@clicklabs.in', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-16 11:32:57', '2013-09-23 04:52:30', 0, 1, ''),
(271, 'ankush2', 'c2b07ce65bc3ba3be548acde3e106674', 0, 'YW5rdXNoMkBjbGljay1sYWJzLmNvbQ==', 'ankush2@click-labs.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-16 12:07:46', '2013-09-19 10:19:03', 0, 1, ''),
(272, 'Lief Storer', '', 3207535, 'MzIwNzUzNQ==', '', 'https://graph.facebook.com/3207535/picture?width=168&height=168', 0.000000, 0.000000, '', '(null)', '2013-09-16 18:43:15', '2013-09-23 22:25:16', 0, 1, ''),
(273, 'Charlotte Mia Skinner', '', 100004934372627, 'MTAwMDA0OTM0MzcyNjI3', '', 'https://graph.facebook.com/100004934372627/picture?width=168&height=168', -115.175102, 36.088787, '', '(null)', '2013-09-16 18:51:59', '2013-09-16 18:54:09', 0, 1, ''),
(274, 'cskinner4', '', 0, 'Y3NraW5uZXI0QG1lLmNvbQ==', 'cskinner4@me.com', 'user.png', 76.810127, 30.718773, '', '(null)', '2013-09-16 18:52:47', '2013-09-20 04:29:33', 0, 0, ''),
(275, 'Lief.storer', '74feb7c27bcf56a9c834a585a0a60bde', 0, 'TGllZi5zdG9yZXJAZ21haWwuY29t', 'Lief.storer@gmail.com', 'user.png', -115.175102, 36.088776, '', '(null)', '2013-09-16 18:54:27', '2013-09-16 18:55:00', 0, 1, ''),
(276, 'cmac', '53c9a94230f5724e39ce639919fd7c53', 0, 'Y21hY0Bib29tYm90aXguY29t', 'cmac@boombotix.com', 'user.png', -122.417236, 37.753944, '', '(null)', '2013-09-16 18:56:10', '2013-09-26 04:20:05', 0, 1, ''),
(277, 'maxshreds247', 'a48213bfa77d3d87a2ce0c0a40d83238', 0, 'bWF4c2hyZWRzMjQ3QGdtYWlsLmNvbQ==', 'maxshreds247@gmail.com', 'user.png', -114.854233, 35.977356, '', '(null)', '2013-09-16 20:04:08', '2013-09-17 17:45:16', 0, 1, ''),
(278, 'chinmay 56', '4e590e01f083464fcaca5ae2e87b833f', 0, 'Y2hpbm1heSA1NkBjbGljay1sYWJzLmNvbQ==', 'chinmay 56@click-labs.com', 'user.png', -122.406418, 37.785835, '', '(null)', '2013-09-17 04:59:01', '2013-09-17 13:27:22', 0, 1, ''),
(279, 'tarun', '', 0, 'dGFydW5AY2xpY2tsYWJzLmlu', 'tarun@clicklabs.in', 'user.png', 0.000000, 0.000000, '', '', '2013-09-17 05:38:19', '0000-00-00 00:00:00', 0, 0, ''),
(280, 'harmandeep', '', 0, 'aGFybWFuZGVlcEBjbGlja2xhYnMuaW4=', 'harmandeep@clicklabs.in', 'user.png', 0.000000, 0.000000, '', '', '2013-09-17 05:41:56', '0000-00-00 00:00:00', 0, 0, ''),
(281, 'anu', '982c718030c432c58c4309f42e5b594e', 0, 'YW51QGNsaWNrbGFicy5pbg==', 'anu@clicklabs.in', 'user.png', -122.406418, 37.785835, '', '(null)', '2013-09-17 05:44:58', '2013-09-17 05:58:25', 0, 1, ''),
(282, 'shreya 1', 'ddf66556c72f005832ec29cd9aae6ca8', 0, 'c2hyZXlhIDFAY2xpY2tsYWJzLmlu', 'shreya 1@clicklabs.in', 'user.png', -122.406418, 37.785835, '', '(null)', '2013-09-17 07:04:36', '2013-09-17 07:14:32', 0, 1, ''),
(283, 'ankush 2', '469822cc883cb8f0ab36178fe2d723c4', 0, 'YW5rdXNoIDJAY2xpY2stbGFicy5jb20=', 'ankush 2@click-labs.com', 'user.png', -122.406418, 37.785835, '', '(null)', '2013-09-17 07:47:33', '0000-00-00 00:00:00', 0, 1, ''),
(284, 'a', 'c521cc3de832d3378fb4648e4763bdd4', 0, 'YUBtZS5jb20=', 'a@me.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-17 07:48:24', '2013-09-28 07:32:50', 0, 1, ''),
(285, 'lief 1', 'bbe9119c2044441a758e745ba8fb6907', 0, 'bGllZiAxQGJvb21ib3RpeC5jb20=', 'lief 1@boombotix.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-17 13:17:18', '0000-00-00 00:00:00', 0, 1, ''),
(286, 'ankush 4', '0b29843632df5c4c886d2465681820ac', 0, 'YW5rdXNoIDRAY2xpY2stbGFicy5jb20=', 'ankush 4@click-labs.com', 'user.png', -122.406418, 37.785835, '', '(null)', '2013-09-17 13:18:53', '2013-09-17 13:23:36', 0, 1, ''),
(287, 'chinmay 57', '4fa7db5bf157f6e3b018caad1faa9bbc', 0, 'Y2hpbm1heSA1N0BjbGljay1sYWJzLmNvbQ==', 'chinmay 57@click-labs.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-17 13:29:09', '2013-09-28 10:01:47', 0, 1, '1344,1339,1342,1338,1343'),
(288, 'chinmay 58', '6641cc1b7671e419bf3655b40ed0e84e', 0, 'Y2hpbm1heSA1OEBjbGljay1sYWJzLmNvbQ==', 'chinmay 58@click-labs.com', 'user.png', 76.809959, 30.718832, '', '(null)', '2013-09-17 13:29:26', '2013-09-18 10:04:03', 0, 1, '1375,1373'),
(289, 'lief', 'a6be7eb45b06cdae8b60f4ac5c46be7f', 0, 'bGllZkBza3VsbHlib29tLmNvbQ==', 'lief@skullyboom.com', 'user.png', -115.193817, 36.114567, '', '(null)', '2013-09-17 14:58:57', '0000-00-00 00:00:00', 0, 1, ''),
(290, 'Ben Radler', '', 6025021, 'NjAyNTAyMQ==', '', 'https://graph.facebook.com/6025021/picture?width=168&height=168', -122.392921, 37.807320, '', '(null)', '2013-09-17 16:03:37', '2013-09-19 17:51:14', 0, 1, ''),
(291, 'garima', '6d17bd291682f81fc64fa8b6ed578987', 0, 'Z2FyaW1hQGNsaWNrbGFicy5pbg==', 'garima@clicklabs.in', 'user.png', -122.406418, 37.785835, '', '(null)', '2013-09-18 04:31:59', '2013-09-18 05:09:55', 0, 1, ''),
(292, 'a1', 'ec33bc23154c0ec9c529ff8525db5d95', 0, 'YTFAbWUuY29t', 'a1@me.com', 'user.png', 76.810059, 30.718849, '', '(null)', '2013-09-18 10:14:58', '0000-00-00 00:00:00', 0, 1, ''),
(293, 'a3', '8569edac3e3a2b0d027adace3b041a90', 0, 'YTNAbWUuY29t', 'a3@me.com', 'user.png', 76.810097, 30.718821, '', '(null)', '2013-09-18 10:19:40', '2013-09-19 05:37:34', 0, 1, '1378,1377'),
(294, 'lief', '48d8fddb486e4ce01d9d89cf4f1f1a72', 0, 'bGllZkBib29tYm90aXguY29t', 'lief@boombotix.com', 'user.png', -122.417259, 37.753918, '', '(null)', '2013-09-18 14:05:25', '2013-09-27 21:13:55', 0, 1, ''),
(295, 'charlotte', 'f11132080d5d634c4fe9750b5c7f72d8', 0, 'Y2hhcmxvdHRlQGJvb21ib3RpeC5jb20=', 'charlotte@boombotix.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-18 14:43:52', '0000-00-00 00:00:00', 0, 1, ''),
(296, 'aw', '64265a2a4cf94fcaf3a0de8cd7ada936', 0, 'YXdAbWUuY29t', 'aw@me.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-19 05:13:05', '2013-09-19 05:33:44', 0, 1, ''),
(297, 'asdf', '36787cce70c02eaef5c6702b9d416161', 0, 'YXNkZkBoLmNv', 'asdf@h.co', 'user.png', 76.811844, 30.719650, '', '(null)', '2013-09-19 05:46:43', '2013-09-19 10:18:42', 0, 1, ''),
(298, 'q1', '433b2cbdd7400b66f1637b787e9bda09', 0, 'cTFAbWUuY29t', 'q1@me.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-19 06:06:34', '2013-09-19 07:25:31', 0, 1, ''),
(299, 'shreya2', '1bcbdc20f027c99532eb9de178d155ed', 0, 'c2hyZXlhMkBjbGlja2xhYnMuaW4=', 'shreya2@clicklabs.in', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-19 06:38:31', '0000-00-00 00:00:00', 0, 1, ''),
(300, 'a', 'c7b1411b518f69e2a0fd87dd4311c827', 0, 'YUBuZXIuY29t', 'a@ner.com', 'user.png', 76.810127, 30.718777, '', '(null)', '2013-09-19 07:27:39', '2013-09-23 09:33:26', 0, 1, ''),
(301, 'shreya3', '0758076f0456b49e5accd894fa86b1b9', 0, 'c2hyZXlhM0BhLmNvbQ==', 'shreya3@a.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-19 09:18:16', '2013-09-19 09:20:31', 0, 1, ''),
(302, 'shreya4', '993f95585c3c01ccff096696241a8373', 0, 'c2hyZXlhNEBhLmNvbQ==', 'shreya4@a.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-19 09:21:01', '2013-09-19 09:22:00', 0, 1, ''),
(303, 'shreya5', 'c9821296d328e54d4324edbe32ca09b8', 0, 'c2hyZXlhNUBhLmNvbQ==', 'shreya5@a.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-19 09:22:27', '2013-09-19 09:23:50', 0, 1, ''),
(304, 'agshhj', '7ed61bde0fcf999964a91e3ca8c2cacf', 0, 'YWdzaGhqQGRmZy5jb20=', 'agshhj@dfg.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-19 09:46:15', '2013-09-19 09:53:21', 0, 1, ''),
(305, 'Chinmay Agarwal', '', 597631367, 'NTk3NjMxMzY3', '', 'https://graph.facebook.com/597631367/picture?width=168&height=168', 76.810883, 30.719946, '', '(null)', '2013-09-19 10:19:02', '2013-09-28 11:49:29', 0, 1, ''),
(306, 'Ankush Kushwaha', '', 100001119109533, 'MTAwMDAxMTE5MTA5NTMz', '', 'https://graph.facebook.com/100001119109533/picture?width=168&height=168', 76.810135, 30.718801, '', '(null)', '2013-09-19 10:20:50', '2013-09-28 09:49:14', 0, 1, ''),
(307, 'a', '9dfdafe8d8aa93dd06e92bb375dc04b8', 0, 'YUBhcy5jb20=', 'a@as.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-19 11:28:39', '0000-00-00 00:00:00', 0, 1, ''),
(308, 'shreya', '', 0, 'c2hyZXlhQGNsaWNrbGFicy5jb20=', 'shreya@clicklabs.com', 'user.png', 0.000000, 0.000000, '', '', '2013-09-19 11:28:57', '0000-00-00 00:00:00', 0, 0, ''),
(309, 's', '96422ff9973d0b710a1164f0ef86dfc3', 0, 'c0Boai5hYQ==', 's@hj.aa', 'user.png', 76.810135, 30.718767, '', '(null)', '2013-09-19 11:31:19', '2013-09-19 12:46:07', 0, 1, ''),
(310, 'sdfhyuh', '205c576db7c777f0ba598318e80a3a91', 0, 'c2RmaHl1aEBkZy5jb20=', 'sdfhyuh@dg.com', 'user.png', -122.406418, 37.785835, '', '(null)', '2013-09-19 11:42:47', '0000-00-00 00:00:00', 0, 1, ''),
(311, 'qwer', '2b9458c197cb696bb30c9afea8486670', 0, 'cXdlckBuLmNvbQ==', 'qwer@n.com', 'user.png', 76.810135, 30.718767, '', '(null)', '2013-09-19 12:46:56', '2013-09-19 13:02:44', 0, 1, ''),
(312, 'Tester Clicklabs', '', 100006266290053, 'MTAwMDA2MjY2MjkwMDUz', '', 'https://graph.facebook.com/100006266290053/picture?width=168&height=168', 76.810120, 30.718769, '', '(null)', '2013-09-20 04:31:46', '2013-09-20 12:07:10', 0, 1, ''),
(313, 'c', 'a2fe7fc8c4d4d976b92fd490c1db5736', 0, 'Y0BhLmNvbQ==', 'c@a.com', 'user.png', 76.810066, 30.718849, '', '(null)', '2013-09-20 04:43:38', '2013-09-20 06:51:16', 0, 1, ''),
(314, 'dsrf', '77e08d12fe73f891872ccda0f35ea92b', 0, 'ZHNyZkBnaGR1LmNvbQ==', 'dsrf@ghdu.com', 'user.png', 76.810135, 30.718763, '', '(null)', '2013-09-20 05:44:27', '0000-00-00 00:00:00', 0, 1, ''),
(315, 'dhkffh', '9a37edb5694a79040c3d12376ee7f273', 0, 'ZGhrZmZoQGhmZy5jaw==', 'dhkffh@hfg.ck', 'user.png', 76.810135, 30.718769, '', '(null)', '2013-09-20 06:26:13', '2013-09-20 10:03:06', 0, 1, ''),
(316, 'Honey Singh', '', 100005403996237, 'MTAwMDA1NDAzOTk2MjM3', '', 'https://graph.facebook.com/100005403996237/picture?width=168&height=168', 76.810059, 30.718849, '', '(null)', '2013-09-20 06:51:46', '2013-09-20 07:11:22', 0, 1, ''),
(317, 'abc', '9be290f4e272a765503152cc54c781d5', 0, 'YWJjQGRlZi5nZA==', 'abc@def.gd', 'user.png', 76.810120, 30.718790, '', '(null)', '2013-09-20 10:07:58', '2013-09-23 09:31:55', 0, 1, ''),
(318, 'testerh3', 'dfd81db964f99352eccab32944d4f988', 0, 'dGVzdGVyaDNAZ21haWwuY29t', 'testerh3@gmail.com', 'user.png', -122.406418, 37.785835, '', '(null)', '2013-09-20 10:20:22', '2013-09-20 10:57:01', 0, 1, ''),
(319, 'sarahcs', '73f700d27d99875f2570a81514658c78', 0, 'c2FyYWhjc0Bob3RtYWlsLmNvbQ==', 'sarahcs@hotmail.com', 'user.png', -122.419716, 37.763596, '', '(null)', '2013-09-22 03:50:44', '2013-09-28 04:51:08', 0, 1, ''),
(320, 'ankush11', '499d8cd15abe79193fc1d05422477bb8', 0, 'YW5rdXNoMTFAYS5jb20=', 'ankush11@a.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-23 04:33:36', '2013-09-24 12:28:07', 0, 1, ''),
(321, 'tim', 'd2cf68b9d4e27af4d8cc9000bec97ece', 0, 'dGltQHEuY29t', 'tim@q.com', 'user.png', 76.810089, 30.718769, '', '(null)', '2013-09-23 04:36:39', '0000-00-00 00:00:00', 0, 1, ''),
(322, 'user', '8231a53fc43878ade9c9271a110ecdc2', 0, 'dXNlckBib29tYm90aXguY29t', 'user@boombotix.com', 'user.png', -122.417168, 37.753960, '', '(null)', '2013-09-23 05:11:54', '2013-09-28 04:26:23', 0, 1, ''),
(323, 'Shreya Kapoor', '', 100006436006561, 'MTAwMDA2NDM2MDA2NTYx', '', 'https://graph.facebook.com/100006436006561/picture?width=168&height=168', 76.810135, 30.718773, '', '(null)', '2013-09-23 05:14:25', '2013-09-25 12:03:20', 0, 1, ''),
(324, 'Gagandeep Arora', '', 1690793970, 'MTY5MDc5Mzk3MA==', '', 'https://graph.facebook.com/1690793970/picture?width=168&height=168', 0.000000, 0.000000, '', '', '2013-09-24 04:25:35', '0000-00-00 00:00:00', 0, 0, ''),
(325, 'Riya Sharma', '', 100005439183356, 'MTAwMDA1NDM5MTgzMzU2', '', 'https://graph.facebook.com/100005439183356/picture?width=168&height=168', 76.810081, 30.718853, '', '(null)', '2013-09-24 04:33:33', '0000-00-00 00:00:00', 0, 1, ''),
(326, 'Shreya Kapoor', '', 100000149944858, 'MTAwMDAwMTQ5OTQ0ODU4', '', 'https://graph.facebook.com/100000149944858/picture?width=168&height=168', 76.810120, 30.718782, '', '(null)', '2013-09-24 04:43:18', '2013-09-24 05:28:04', 0, 1, ''),
(327, 'chrismckleroy', '0515249ae6e070a6303fd277640f5e8c', 0, 'Y2hyaXNtY2tsZXJveUBnbWFpbC5jb20=', 'chrismckleroy@gmail.com', 'user.png', -122.417290, 37.753960, '', '(null)', '2013-09-26 02:48:42', '2013-09-26 02:54:36', 0, 1, ''),
(328, 'chrismckleroy', '75cecdcd1f9835a9c777304df8497d9e', 0, 'Y2hyaXNtY2tsZXJveUBnbWFpbC5jb24=', 'chrismckleroy@gmail.con', 'user.png', -122.417259, 37.753963, '', '(null)', '2013-09-26 02:58:04', '2013-09-26 03:05:32', 0, 1, ''),
(329, 'Ankush Kumar', '', 100003613987476, 'MTAwMDAzNjEzOTg3NDc2', '', 'https://graph.facebook.com/100003613987476/picture?width=168&height=168', 0.000000, 0.000000, '', '(null)', '2013-09-28 05:26:17', '0000-00-00 00:00:00', 0, 1, ''),
(330, 'chinmay 59', '91c559be9eff6ead54dc7c946f3a9cc5', 0, 'Y2hpbm1heSA1OUBjbGljay1sYWJzLmNvbQ==', 'chinmay 59@click-labs.com', 'user.png', 0.000000, 0.000000, '', '(null)', '2013-09-28 10:07:46', '2013-09-28 11:28:10', 0, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user_playlist`
--

CREATE TABLE IF NOT EXISTS `tb_user_playlist` (
  `playlist_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `song_name` varchar(200) NOT NULL,
  `song_artist` varchar(200) NOT NULL,
  `song_image` varchar(200) NOT NULL,
  `song_link` varchar(200) NOT NULL,
  `song_itunes_link` varchar(200) NOT NULL,
  `session_id` bigint(20) NOT NULL,
  `playlist_created_datetime` datetime NOT NULL,
  `song_status` tinyint(4) NOT NULL COMMENT '0 for my music,1 for You tube,2 for sound cloud',
  PRIMARY KEY (`playlist_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1472 ;

--
-- Dumping data for table `tb_user_playlist`
--

INSERT INTO `tb_user_playlist` (`playlist_id`, `user_id`, `song_name`, `song_artist`, `song_image`, `song_link`, `song_itunes_link`, `session_id`, `playlist_created_datetime`, `song_status`) VALUES
(1278, 264, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?5e64f12', '79413036', 'itunes_url', 45051379322174, '2013-09-16 09:02:54', 2),
(1279, 264, 'ANTS Podcast #012: Trapicana High Pulp', 'Baby Armie', 'http://i1.sndcdn.com/artworks-000048655632-s06dga-large.jpg?5e64f12', '93321366', 'itunes_url', 45051379322174, '2013-09-16 09:03:55', 2),
(1280, 270, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?5e64f12', '79413036', 'itunes_url', 4021379333011, '2013-09-16 12:15:43', 2),
(1281, 270, '10 - Land Of Ladies', 'J1K (of J1Kbeats.com)', 'http://i1.sndcdn.com/artworks-000053769798-e20tco-large.jpg?5e64f12', '102602084', 'itunes_url', 4021379333011, '2013-09-16 12:15:52', 2),
(1283, 272, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?5e64f12', '79413036', 'itunes_url', 85591379357512, '2013-09-16 18:51:52', 2),
(1284, 277, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?5e64f12', '79413036', 'itunes_url', 13931379361916, '2013-09-16 20:05:34', 2),
(1286, 278, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?5e64f12', '79413036', 'itunes_url', 66381379395218, '2013-09-17 05:20:18', 2),
(1287, 268, '10 - Land Of Ladies', 'J1K (of J1Kbeats.com)', 'http://i1.sndcdn.com/artworks-000053769798-e20tco-large.jpg?5e64f12', '102602084', 'itunes_url', 87491379396443, '2013-09-17 05:40:43', 2),
(1288, 281, 'RUSKO presents STIR-FRY. 2 hours of new music from around the world', 'Rusko', 'http://i1.sndcdn.com/artworks-000036775516-1c17df-large.jpg?5e64f12', '72229784', 'itunes_url', 60961379396720, '2013-09-17 05:45:20', 2),
(1291, 282, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?5e64f12', '79413036', 'itunes_url', 67471379401540, '2013-09-17 07:14:49', 2),
(1336, 285, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?9f49e2b', '79413036', 'itunes_url', 41501379423893, '2013-09-17 13:18:13', 2),
(1337, 285, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?9f49e2b', '79413036', 'itunes_url', 41501379423893, '2013-09-17 13:22:29', 2),
(1338, 287, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?9f49e2b', '110894813', 'itunes_url', 30431379424573, '2013-09-17 13:29:33', 2),
(1339, 287, 'ANTS Podcast #012: Trapicana High Pulp', 'Baby Armie', 'http://i1.sndcdn.com/artworks-000048655632-s06dga-large.jpg?9f49e2b', '93321366', 'itunes_url', 30431379424573, '2013-09-17 13:30:04', 2),
(1342, 287, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?9f49e2b', '79413036', 'itunes_url', 30431379424573, '2013-09-17 13:35:02', 2),
(1343, 287, '10 - Land Of Ladies', 'J1K (of J1Kbeats.com)', 'http://i1.sndcdn.com/artworks-000053769798-e20tco-large.jpg?9f49e2b', '102602084', 'itunes_url', 30431379424573, '2013-09-17 13:57:16', 2),
(1344, 287, 'French House Lovers - Chill Out Session #2', 'French House Lovers', 'http://i1.sndcdn.com/artworks-000051410586-garvs5-large.jpg?9f49e2b', '97709934', 'itunes_url', 30431379424573, '2013-09-17 14:00:06', 2),
(1345, 290, 'French House Lovers - Chill Out Session #1', 'French.House.Lovers', 'http://i1.sndcdn.com/artworks-000055146526-ofvh9s-large.jpg?9f49e2b', '78369705', 'itunes_url', 60341379433861, '2013-09-17 16:04:21', 2),
(1346, 290, 'French House Lovers - Chill Out Session #2', 'French House Lovers', 'http://i1.sndcdn.com/artworks-000051410586-garvs5-large.jpg?9f49e2b', '97709934', 'itunes_url', 60341379433861, '2013-09-17 16:04:31', 2),
(1347, 277, 'RUSKO presents STIR-FRY. 2 hours of new music from around the world', 'Rusko', 'http://i1.sndcdn.com/artworks-000036775516-1c17df-large.jpg?9f49e2b', '72229784', 'itunes_url', 13931379361916, '2013-09-17 16:47:35', 2),
(1348, 277, 'French House Lovers - Chill Out Session #1', 'French.House.Lovers', 'http://i1.sndcdn.com/artworks-000055146526-ofvh9s-large.jpg?9f49e2b', '78369705', 'itunes_url', 13931379361916, '2013-09-17 18:01:11', 2),
(1349, 291, 'ANTS Podcast #012: Trapicana High Pulp', 'Baby Armie', 'http://i1.sndcdn.com/artworks-000048655632-s06dga-large.jpg?9f49e2b', '93321366', 'itunes_url', 6251379479450, '2013-09-18 04:44:10', 2),
(1373, 288, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?9f49e2b', '110894813', 'itunes_url', 72801379424680, '2013-09-18 09:50:02', 2),
(1375, 288, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?9f49e2b', '79413036', 'itunes_url', 72801379424680, '2013-09-18 10:09:24', 2),
(1377, 293, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?9f49e2b', '110894813', 'itunes_url', 19571379499993, '2013-09-18 10:26:33', 2),
(1378, 293, 'ANTS Podcast #012: Trapicana High Pulp', 'Baby Armie', 'http://i1.sndcdn.com/artworks-000048655632-s06dga-large.jpg?9f49e2b', '93321366', 'itunes_url', 19571379499993, '2013-09-18 10:27:22', 2),
(1381, 298, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?9f49e2b', '110894813', 'itunes_url', 78771379571536, '2013-09-19 06:18:56', 2),
(1382, 298, 'ANTS Podcast #012: Trapicana High Pulp', 'Baby Armie', 'http://i1.sndcdn.com/artworks-000048655632-s06dga-large.jpg?9f49e2b', '93321366', 'itunes_url', 78771379571536, '2013-09-19 06:19:22', 2),
(1384, 301, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?b7e4910', '110894813', 'itunes_url', 13361379582316, '2013-09-19 09:18:36', 2),
(1385, 302, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?b7e4910', '110894813', 'itunes_url', 57341379582474, '2013-09-19 09:21:14', 2),
(1392, 304, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?b7e4910', '110894813', 'itunes_url', 99771379584001, '2013-09-19 09:46:41', 2),
(1393, 304, 'ANTS Podcast #012: Trapicana High Pulp', 'Baby Armie', 'http://i1.sndcdn.com/artworks-000048655632-s06dga-large.jpg?b7e4910', '93321366', 'itunes_url', 99771379584001, '2013-09-19 09:50:41', 2),
(1394, 271, '', '', '', '', '', 76661379392399, '2013-09-19 10:17:07', 0),
(1398, 306, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?b7e4910', '110894813', 'itunes_url', 96441379589552, '2013-09-19 11:19:22', 2),
(1399, 307, 'ANTS Podcast #012: Trapicana High Pulp', 'Baby Armie', 'http://i1.sndcdn.com/artworks-000048655632-s06dga-large.jpg?b7e4910', '93321366', 'itunes_url', 79681379590137, '2013-09-19 11:29:49', 2),
(1401, 310, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?b7e4910', '110894813', 'itunes_url', 80931379590977, '2013-09-19 11:42:57', 2),
(1402, 300, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?b7e4910', '110894813', 'itunes_url', 21941379591022, '2013-09-19 11:43:42', 2),
(1408, 309, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?b7e4910', '110894813', 'itunes_url', 45261379590287, '2013-09-19 12:46:33', 2),
(1414, 311, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?b7e4910', '79413036', 'itunes_url', 33211379594826, '2013-09-19 12:59:02', 2),
(1415, 267, 'ANTS Podcast #012: Trapicana High Pulp', 'Baby Armie', 'http://i1.sndcdn.com/artworks-000048655632-s06dga-large.jpg?f34f187', '93321366', 'itunes_url', 29291379335218, '2013-09-19 13:11:39', 2),
(1421, 305, 'RUSKO presents STIR-FRY. 2 hours of new music from around the world', 'Rusko', 'http://i1.sndcdn.com/artworks-000036775516-1c17df-large.jpg?f34f187', '72229784', 'itunes_url', 98841379585966, '2013-09-19 18:45:30', 2),
(1424, 305, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?f34f187', '110894813', 'itunes_url', 98841379585966, '2013-09-19 18:46:06', 2),
(1425, 305, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?f34f187', '79413036', 'itunes_url', 98841379585966, '2013-09-19 18:46:38', 2),
(1426, 305, '10 - Land Of Ladies', 'J1K (of J1Kbeats.com)', 'http://i1.sndcdn.com/artworks-000053769798-e20tco-large.jpg?f34f187', '102602084', 'itunes_url', 98841379585966, '2013-09-19 18:46:43', 2),
(1427, 305, 'French House Lovers - Chill Out Session #1', 'French.House.Lovers', 'http://i1.sndcdn.com/artworks-000055146526-ofvh9s-large.jpg?f34f187', '78369705', 'itunes_url', 98841379585966, '2013-09-19 18:46:49', 2),
(1428, 305, 'French House Lovers - Chill Out Session #2', 'French House Lovers', 'http://i1.sndcdn.com/artworks-000051410586-garvs5-large.jpg?f34f187', '97709934', 'itunes_url', 98841379585966, '2013-09-19 18:46:53', 2),
(1429, 274, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?f34f187', '110894813', 'itunes_url', 62281379651425, '2013-09-20 04:30:25', 2),
(1430, 312, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?f34f187', '79413036', 'itunes_url', 34441379651513, '2013-09-20 04:31:53', 2),
(1431, 312, '10 - Land Of Ladies', 'J1K (of J1Kbeats.com)', 'http://i1.sndcdn.com/artworks-000053769798-e20tco-large.jpg?f34f187', '102602084', 'itunes_url', 34441379651513, '2013-09-20 04:31:59', 2),
(1432, 313, '10 - Land Of Ladies', 'J1K (of J1Kbeats.com)', 'http://i1.sndcdn.com/artworks-000053769798-e20tco-large.jpg?f34f187', '102602084', 'itunes_url', 98991379652511, '2013-09-20 04:48:31', 2),
(1433, 314, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?f34f187', '79413036', 'itunes_url', 17561379655927, '2013-09-20 05:45:27', 2),
(1434, 314, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?f34f187', '110894813', 'itunes_url', 17561379655927, '2013-09-20 05:45:36', 2),
(1436, 316, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?f34f187', '79413036', 'itunes_url', 61711379660008, '2013-09-20 07:07:27', 2),
(1438, 318, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?f34f187', '79413036', 'itunes_url', 5541379672592, '2013-09-20 10:29:05', 2),
(1439, 319, 'RUSKO presents STIR-FRY. 2 hours of new music from around the world', 'Rusko', 'http://i1.sndcdn.com/artworks-000036775516-1c17df-large.jpg?3eddc42', '72229784', 'itunes_url', 60421379821866, '2013-09-22 03:51:06', 2),
(1440, 319, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?3eddc42', '79413036', 'itunes_url', 60421379821866, '2013-09-22 03:51:32', 2),
(1441, 321, '', '', '', '', '', 5921379911063, '2013-09-23 04:37:43', 0),
(1442, 320, '10 - Land Of Ladies', 'J1K (of J1Kbeats.com)', 'http://i1.sndcdn.com/artworks-000053769798-e20tco-large.jpg?3eddc42', '102602084', 'itunes_url', 9221379911695, '2013-09-23 04:48:15', 2),
(1443, 267, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?3eddc42', '110894813', 'itunes_url', 29291379335218, '2013-09-23 12:49:18', 2),
(1444, 272, 'French House Lovers - Chill Out Session #1', 'French.House.Lovers', 'http://i1.sndcdn.com/artworks-000055146526-ofvh9s-large.jpg?3eddc42', '78369705', 'itunes_url', 85591379357512, '2013-09-23 20:01:39', 2),
(1445, 322, 'Beats Antique - SPRING MIX 2013', 'beatsantique', 'http://i1.sndcdn.com/artworks-000046504327-32jsge-large.jpg?3eddc42', '89511667', 'itunes_url', 27501379975067, '2013-09-23 22:24:27', 2),
(1446, 322, 'Expansion Broadcast Live DJ mix Podcast #537', 'Jett Chandon', 'http://i1.sndcdn.com/artworks-000056455408-2bf1ai-large.jpg?3eddc42', '107739105', 'itunes_url', 27501379975067, '2013-09-23 22:24:35', 2),
(1448, 322, 'Bassnectar - Do It Like This feat. ill.Gates', 'Bassnectar', 'http://i1.sndcdn.com/artworks-000019260879-3yqrx8-large.jpg?3eddc42', '38405359', 'itunes_url', 27501379975067, '2013-09-24 03:12:43', 2),
(1449, 323, 'French House Lovers - Chill Out Session #1', 'French.House.Lovers', 'http://i1.sndcdn.com/artworks-000055146526-ofvh9s-large.jpg?3eddc42', '78369705', 'itunes_url', 85991379997606, '2013-09-24 04:40:06', 2),
(1450, 326, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?3eddc42', '79413036', 'itunes_url', 76741379998037, '2013-09-24 05:21:59', 2),
(1451, 326, 'Sherlock BBC', 'boombot', 'http://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?3eddc42', '110894813', 'itunes_url', 76741379998037, '2013-09-24 05:22:05', 2),
(1452, 326, '10 - Land Of Ladies', 'J1K (of J1Kbeats.com)', 'http://i1.sndcdn.com/artworks-000053769798-e20tco-large.jpg?3eddc42', '102602084', 'itunes_url', 76741379998037, '2013-09-24 05:22:09', 2),
(1453, 306, '01 - Standing on the shore', 'Xoul Rodriguez', 'http://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?3eddc42', '79413036', 'itunes_url', 96441379589552, '2013-09-24 07:24:39', 2),
(1456, 327, 'RUSKO presents STIR-FRY. 2 hours of new music from around the world', 'Rusko', 'http://i1.sndcdn.com/artworks-000036775516-1c17df-large.jpg?3eddc42', '72229784', 'itunes_url', 95471380163986, '2013-09-26 02:53:06', 2),
(1457, 328, 'French House Lovers - Chill Out Session #1', 'French.House.Lovers', 'http://i1.sndcdn.com/artworks-000055146526-ofvh9s-large.jpg?3eddc42', '78369705', 'itunes_url', 21811380164440, '2013-09-26 03:00:40', 2),
(1458, 305, 'BeepSong', 'user523159875', '/var/mobile/Applications/C7CC4F74-55FA-434C-9B7A-BBC2F1F2998D/Boombotix.app/noArtworkImage.png', '109681721', 'itunes_url', 98841379585966, '2013-09-26 11:46:38', 2),
(1459, 267, 'BeepSong', 'user523159875', '<null>', '109681721', 'itunes_url', 29291379335218, '2013-09-26 12:00:47', 2),
(1460, 267, 'Audio Beep Song', 'user523159875', 'https://i1.sndcdn.com/artworks-000057414928-1qlehn-large.jpg?3eddc42', '109681400', 'itunes_url', 29291379335218, '2013-09-26 12:10:48', 2),
(1461, 267, 'ANTS Podcast #011: ANTS Rebirth', 'Baby Armie', 'https://i1.sndcdn.com/artworks-000046415690-brp4tx-large.jpg?3eddc42', '89463813', 'itunes_url', 29291379335218, '2013-09-26 12:18:21', 2),
(1462, 267, 'Standing on the Shore (Empire of the Sun x Ryan Tedder x Joe Garston)', 'DJ Topsider', 'https://i1.sndcdn.com/artworks-000045940543-k9wise-large.jpg?3eddc42', '88642761', 'itunes_url', 29291379335218, '2013-09-26 12:18:28', 2),
(1463, 267, 'Calvin Harris - Feel So Close (Dillon Francis Remix)', 'DILLONFRANCIS', 'https://i1.sndcdn.com/artworks-000010060386-jrtgm5-large.jpg?3eddc42', '20223904', 'itunes_url', 29291379335218, '2013-09-26 12:18:36', 2),
(1464, 267, '"DON''T TALK TO" EP PREVIEW (SEP 30th)', 'RAC', 'https://i1.sndcdn.com/artworks-000058433225-mmkhr7-large.jpg?3eddc42', '111941071', 'itunes_url', 29291379335218, '2013-09-26 12:18:55', 2),
(1465, 267, 'Suddenly', 'Anna Calvi', 'https://i1.sndcdn.com/artworks-000058453348-5tyco2-large.jpg?3eddc42', '111989374', 'itunes_url', 29291379335218, '2013-09-26 12:19:01', 2),
(1466, 267, 'INSOMNIUM - Ephemeral', 'Century Media Records', 'https://i1.sndcdn.com/artworks-000058264858-mxy5gp-large.jpg?3eddc42', '111470045', 'itunes_url', 29291379335218, '2013-09-26 12:19:08', 2),
(1467, 267, 'November 2010 - Paloma Faith, Guy Barker, Afrocubism, China Moses', 'Barbican Centre', 'https://i1.sndcdn.com/artworks-000058441107-tojul1-large.jpg?3eddc42', '111962007', 'itunes_url', 29291379335218, '2013-09-26 12:19:14', 2),
(1468, 294, 'DIPLO Mix for #SwayInTheMorning', 'diplo', 'http://i1.sndcdn.com/artworks-000034260096-pxwi41-large.jpg?3eddc42', '67549647', 'itunes_url', 82171379515410, '2013-09-26 16:44:40', 2),
(1469, 322, 'DIPLO Mix for #SwayInTheMorning', 'diplo', 'http://i1.sndcdn.com/artworks-000034260096-pxwi41-large.jpg?3eddc42', '67549647', 'itunes_url', 27501379975067, '2013-09-28 04:28:22', 2),
(1470, 284, '01 - Standing on the shore', 'Xoul Rodriguez', 'https://i1.sndcdn.com/artworks-000040814379-epswfc-large.jpg?3eddc42', '79413036', 'itunes_url', 80241380346398, '2013-09-28 05:33:18', 2),
(1471, 330, 'Sherlock BBC', 'boombot', 'https://i1.sndcdn.com/artworks-000057986928-xy6d9b-large.jpg?3eddc42', '110894813', 'itunes_url', 4191380362922, '2013-09-28 10:08:42', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user_playlist_archive`
--

CREATE TABLE IF NOT EXISTS `tb_user_playlist_archive` (
  `playlist_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `song_name` varchar(200) NOT NULL,
  `song_artist` varchar(200) NOT NULL,
  `song_image` varchar(200) NOT NULL,
  `song_link` varchar(200) NOT NULL,
  `song_itunes_link` varchar(200) NOT NULL,
  `session_id` bigint(20) NOT NULL,
  `playlist_created_datetime` datetime NOT NULL,
  `song_status` tinyint(4) NOT NULL COMMENT '0 for my music,1 for You tube,2 for sound cloud',
  PRIMARY KEY (`playlist_id`),
  KEY `user_id` (`user_id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
