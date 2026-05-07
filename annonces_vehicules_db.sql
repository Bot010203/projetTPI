-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 07 mai 2026 à 08:22
-- Version du serveur : 10.6.22-MariaDB-0ubuntu0.22.04.1
-- Version de PHP : 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `annonces_vehicules_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `Advertisement`
--

CREATE TABLE `Advertisement` (
  `id_advertisement` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sale` tinyint(1) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `year_first_registration` int(11) DEFAULT NULL,
  `date_publication` datetime DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Advertisement`
--

INSERT INTO `Advertisement` (`id_advertisement`, `title`, `description`, `sale`, `location`, `brand`, `model`, `price`, `year_first_registration`, `date_publication`, `id_user`) VALUES
(1, 'BMW Série 3', 'Très bon état', 1, 'Lausanne', 'BMW', 'Série 3', '25000.00', 2019, '2026-04-27 11:06:07', 15),
(2, 'BMW Série 3', 'Très bon état', 1, 'Genève', 'BMW', 'Série 3', NULL, 2019, '2026-04-27 11:11:03', 15),
(4, 'BMW Série 3', 'Très bon état', 1, 'Nyon', 'BMW', 'Série 3', '25000.00', 2019, '2026-04-27 11:56:42', 17),
(5, 'BMW Série 3', 'Très bon état, peu de kilomètres', 1, 'Genève', 'BMW', 'Série 3', '25000.00', 2019, '2026-04-28 08:54:25', 18),
(18, 'q', 'q', 1, 'q', 'q', 'q', NULL, NULL, '2026-05-05 08:28:22', 20),
(23, 'testFinal', '', 1, '', '', '', NULL, NULL, '2026-05-05 11:36:48', 26),
(24, 'annonce de couteau', '', 1, '', '', '', NULL, NULL, '2026-05-05 11:51:29', 28),
(25, '1er annonce', '', 1, '', '', '', NULL, NULL, '2026-05-05 12:03:03', 30),
(28, 'Cbr1000rr', 'Goat moto pour le A2', 1, 'Genève', 'Honda', 'Cbr1000rr', '20000.00', 2018, '2026-05-05 12:47:30', 34),
(34, 'kawa10000', '', 1, '', '', '', NULL, NULL, '2026-05-06 08:26:28', 32),
(35, 'a', '', 1, '', '', '', NULL, NULL, '2026-05-06 08:30:12', 35),
(36, 'b', '', 1, '', '', '', NULL, NULL, '2026-05-06 08:30:26', 35),
(37, 'qqq', '', 1, '', '', '', NULL, NULL, '2026-05-06 08:31:18', 35),
(38, 'bleeeeh', '', 1, '', '', '', NULL, NULL, '2026-05-06 08:33:07', 32),
(39, 'test', '', 0, '', '', '', NULL, NULL, '2026-05-06 11:18:12', 32),
(40, 's', '', 0, '', '', '', NULL, NULL, '2026-05-06 11:19:07', 32),
(41, 'q', '', 1, '', '', '', NULL, NULL, '2026-05-06 12:12:01', 32),
(42, 'a', '', 1, '', '', '', NULL, NULL, '2026-05-06 12:39:06', 32),
(43, 'Kawasaki', '', 1, '', '', '', NULL, NULL, '2026-05-06 12:43:29', 32),
(44, 'TestFinal', '', 1, '', '', '', NULL, NULL, '2026-05-07 06:22:36', 32);

-- --------------------------------------------------------

--
-- Structure de la table `Message`
--

CREATE TABLE `Message` (
  `id_message` int(11) NOT NULL,
  `text` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `read` tinyint(1) DEFAULT 0,
  `id_sender` int(11) NOT NULL,
  `id_recipient` int(11) NOT NULL,
  `id_advertisement` int(11) NOT NULL,
  `original_message_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Message`
--

INSERT INTO `Message` (`id_message`, `text`, `timestamp`, `read`, `id_sender`, `id_recipient`, `id_advertisement`, `original_message_id`) VALUES
(1, 'Bonjour, est-ce que ce véhicule est encore disponible ?', '2026-04-29 10:59:03', 0, 15, 17, 4, NULL),
(4, 'ok', '2026-05-05 11:43:09', 1, 26, 26, 23, NULL),
(5, 'ok', '2026-05-05 11:43:19', 1, 26, 26, 23, NULL),
(6, 'je comprends pas', '2026-05-05 11:44:28', 1, 26, 26, 23, NULL),
(8, 'je veux bien', '2026-05-05 11:52:17', 1, 29, 28, 24, NULL),
(9, 'tu es libre quand', '2026-05-05 11:55:26', 1, 28, 28, 24, NULL),
(10, 'alala', '2026-05-05 11:57:56', 1, 29, 28, 24, NULL),
(11, 'tu veux quoi', '2026-05-05 11:58:28', 0, 28, 28, 24, NULL),
(12, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2026-05-05 11:58:54', 1, 29, 28, 24, NULL),
(13, 'hgfgfhf', '2026-05-05 11:59:13', 0, 28, 28, 24, NULL),
(14, 'gfdgfd', '2026-05-05 11:59:16', 0, 28, 28, 24, NULL),
(15, 'gfdgd', '2026-05-05 11:59:19', 0, 28, 28, 24, NULL),
(16, 'qqqqq', '2026-05-05 11:59:37', 1, 29, 28, 24, NULL),
(17, 'j veux', '2026-05-05 12:03:45', 1, 31, 30, 25, NULL),
(18, 'tu peux me repondre', '2026-05-05 12:04:08', 0, 30, 30, 25, NULL),
(19, 'oui ?', '2026-05-05 12:04:33', 1, 31, 30, 25, NULL),
(20, 'je peux pas envoyer de message', '2026-05-05 12:07:50', 0, 30, 30, 25, NULL),
(21, 'je peux que enoyer a moi meme', '2026-05-05 12:08:18', 1, 31, 30, 25, NULL),
(57, 'je veux', '2026-05-06 12:39:20', 1, 33, 32, 42, NULL),
(58, 'oki', '2026-05-06 12:39:40', 0, 32, 33, 42, NULL),
(59, 'je suis interressé', '2026-05-06 12:43:51', 1, 33, 32, 43, NULL),
(60, 'Je le vend pour 25000 francs', '2026-05-06 12:46:13', 0, 32, 33, 43, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `Picture`
--

CREATE TABLE `Picture` (
  `id_picture` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `id_advertisement` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Picture`
--

INSERT INTO `Picture` (`id_picture`, `path`, `id_advertisement`) VALUES
(1, '/uploads/img_5_69f07a558f7be5.35082120.jpg', 5),
(6, '/uploads/img_37_69fafc569f0646.80548714.jpg', 37),
(7, '/uploads/img_43_69fb377198c2f6.56935960.jpg', 43);

-- --------------------------------------------------------

--
-- Structure de la table `User`
--

CREATE TABLE `User` (
  `id_user` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `login` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `User`
--

INSERT INTO `User` (`id_user`, `email`, `login`, `password`, `token`) VALUES
(4, 'paul.chccch@eduge.ch', 'saluuuut', '$2y$12$2elM0KcUJ7myaPjkRIfepO11njT2dOH4SITW4.VFgL/E0Hvu6oGXS', NULL),
(10, 'paul.chfcch@eduge.ch', 'ddasda', '$2y$12$mIUJPEShG502iEp27o.zce9O8tKVBdsJO6XU70pHBonvyr2yDdjVG', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MTAsImVtYWlsIjoicGF1bC5jaGZjY2hAZWR1Z2UuY2giLCJleHAiOjE3NzY5NDcyODN9.xn6qVXXoEA3ApP8c4bgjngcgHF5m0WCEo49abRvx83Q'),
(11, 'paul.chfcdch@eduge.ch', 'ddasdda', '$2y$12$GlLHQGgRrE9Az8QUO.VatuZ01czIoenFxexehhlHFN2oZQY0.MHlS', NULL),
(12, 'samuel.tdkz@eduge.ch', 'samuel', '$2y$12$EKD4SNxqoOrk8SQs3sCPzOFLxRMoVzL3MuKZ.XJCa6//wY8OSEOhe', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MTIsImVtYWlsIjoic2FtdWVsLnRka3pAZWR1Z2UuY2giLCJleHAiOjE3Nzc1NDA5ODZ9.LeUhmu26HAtinl-qQIIJTS_Vc1AEUMFid0gZjPrEn3U'),
(13, 'ahhhhh@eduge.ch', 'ahmad', '$2y$12$8Io54YeN2VWNw7TF1HGWwOG1irmTb1RCWVpp0QoXPq7SC/5CFFYoO', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MTMsImVtYWlsIjoiYWhoaGhoQGVkdWdlLmNoIiwiZXhwIjoxNzc2OTQ5ODA2fQ.rtwHrCwkiUgehBIbSvWB61PJhM6hyfApV-PN33u9wf8'),
(14, 'ike@eduge.ch', 'Ike', '$2y$12$cY91Hv3dl.e7lnaWEGoJoOSMsvCYpCSN48cI9RFmjfcHWEhhB/MRG', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MTQsImVtYWlsIjoiaWtlQGVkdWdlLmNoIiwiZXhwIjoxNzc3MjczODkzfQ.unUKaACF4Ht-cgrwH3sUkYjhAPH-s7LkSbi0KxYR3ec'),
(15, 'wassime@eduge.ch', 'wassime', '$2y$12$VAzPwf3SEUKhZEAOLrCBgu/T7GDWiM2MbT.XyfyBsXdaKR42I0IOy', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MTUsImVtYWlsIjoid2Fzc2ltZUBlZHVnZS5jaCIsImV4cCI6MTc3Nzg4NDc0OX0.l-3dRAjf2lIYKV2CNuA0nb9weX-X56sYSM9vksfRTLM'),
(16, 'wassimee@eduge.ch', 'wasssime', '$2y$12$Gj.NAC/XdeqMG5qHPWVI2uIkpenxPoq/ODBJf/YsqmIvXn1xG1ePe', NULL),
(17, 'wassimeee@eduge.ch', 'wassssime', '$2y$12$AbA5sZOJdfg/xy1Tj3F5pOf24SxwFrDx/gA91eat/QrnOYQLmp/B6', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MTcsImVtYWlsIjoid2Fzc2ltZWVlQGVkdWdlLmNoIiwiZXhwIjoxNzc3Mjk0NTY4fQ.nOREymLJt3MOc54RtOuGpdvFU2HI16zD-jAcqXcG_Gk'),
(18, 'maman@eduge.ch', 'maman', '$2y$12$rVu3MXGF.zc0Iyz1fZX/u.v6yx9JSB2lO02o4OSsrH66.YI81P7XC', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MTgsImVtYWlsIjoibWFtYW5AZWR1Z2UuY2giLCJleHAiOjE3NzgwNzAxMzd9.4Ujce-ULMHm6rgHL5MNUQUUKM4rPu0E1piuctO9Z800'),
(19, 'claudio.chcch@eduge.ch', 'Claudio', '$2y$12$PqddJVn4eid9cRacE3HioupS77qyx1eVsZdA3TdOtkOe5U.VXXaZ6', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MTksImVtYWlsIjoiY2xhdWRpby5jaGNjaEBlZHVnZS5jaCIsImV4cCI6MTc3NzU0MDYwNX0.MzX4uJK6GFDyVfRywxopvVG0enh6wi2P5XyvGTdhRCk'),
(20, 'cyrah@eduge.ch', 'cyrah', '$2y$12$I5KQMdpshpulHYcjZfcfv.g..N5VwJS/NR3Pm/Bj3jIsAAPdIXSIS', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjAsImVtYWlsIjoiY3lyYWhAZWR1Z2UuY2giLCJleHAiOjE3Nzc5ODQzMDd9._ms505qqB5D5LBpUmlk631K3n-N4LAYhoamttWU60Z8'),
(21, 'jeanne@eduge.ch', 'Jeanne', '$2y$12$2I7iKe2l9llutgABT31PUuXKdJSbcuoFYGtgSR82mBZev90Ir16Vq', NULL),
(22, '1@eduge.ch', '1', '$2y$12$ZeBlKflxvzOtWdooPxzdFOQGUlrTUQlz1rw.hWbTXiutrZJOc1Y1a', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjIsImVtYWlsIjoiMUBlZHVnZS5jaCIsImV4cCI6MTc3Nzg4NDMxOH0.4VJkuAw5xlwIfkxRBNbUbfuuZlkco3mLWxBlujMFxvE'),
(23, 'damksad@dsa.ch', '2', '$2y$12$2XjbPH4EkiJLbhG/jZgreOcmjhgH.DN7GHnNpowqHl5HzzjgA1JJq', NULL),
(24, 'jsp@gmail.com', 'jsp', '$2y$12$tzl7RFC6DDRcZkVi2xeKneJC.5TMDHiWhmzvHLqe4ABkc8lSxyDbm', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjQsImVtYWlsIjoianNwQGdtYWlsLmNvbSIsImV4cCI6MTc3NzkwNjA4NH0.RtwFmseMJss0b0dFCFxUp8qoeiOy3b4o0caZHu4AtDQ'),
(25, 'luffy@gmail.com', 'luffy', '$2y$12$IQ8mLbARIyUcgZrM2V6RA.cHy2X14k8c/EiuX69c1I.EZHGBS57Nm', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjUsImVtYWlsIjoibHVmZnlAZ21haWwuY29tIiwiZXhwIjoxNzc3OTc2OTAxfQ.obwSfuj9AnyCsyjlZTQPBiHEomYZGKTR_gGCfqrig5M'),
(26, 'a@gmail.com', 'a', '$2y$12$4mhBeVU.Z2ba1FcqT4.mqeTNNRfE2iLRrV4wtZAZEDqNG2Aza29xu', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjYsImVtYWlsIjoiYUBnbWFpbC5jb20iLCJleHAiOjE3Nzc5ODUwOTl9.TqIPxLp1R8J1oEin5nJTi6KnbEyN5CK-VTsqT9rp04I'),
(27, 'b@gmail.com', 'b', '$2y$12$0nDgWpmVgdf0PQIFpoXi6uBk8tcJqV7xrEq6pty7gomUZdA7aJbRC', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjcsImVtYWlsIjoiYkBnbWFpbC5jb20iLCJleHAiOjE3Nzc5ODUxMTV9.voeW42lhTtHSv2J-mFMVZvqv7O5rkANjWxV5KUTthu8'),
(28, 'couteau@gmail.com', 'couteau', '$2y$12$pR00jdlNTqkiem/zaHc.TOiDW09C4CwYVW4gS8CGkkeDgkRBn1lvK', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjgsImVtYWlsIjoiY291dGVhdUBnbWFpbC5jb20iLCJleHAiOjE3Nzc5ODYwMTF9.vKgxgMYEFn8BCoPWFWqWz6xsc5VgcnKvsErB9Os-lcU'),
(29, 'fourchette@gmail.com', 'fourchette', '$2y$12$HUrTto2HDV3VXERNNh2PZeq9FsjsjJlPLXzab1tmLRAPLEbaZvhG6', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjksImVtYWlsIjoiZm91cmNoZXR0ZUBnbWFpbC5jb20iLCJleHAiOjE3Nzc5ODU5Njh9.5SO0wsL0GeTitNHEIPBlEIdTq1Euh2W9_Z7uwYDXCVk'),
(30, 'celuiQuiMetEnligne@gmail.com', 'celuiQuiMetEnligne', '$2y$12$ToZWM3D92RahnttzfoMnmuZ1AV1NezY0QZk3D7YVaSmURcIL7PVt2', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzAsImVtYWlsIjoiY2VsdWlRdWlNZXRFbmxpZ25lQGdtYWlsLmNvbSIsImV4cCI6MTc3Nzk4NjUwNX0.A9f1de9ZEFvndBuVqb66xvhLGMQ36ar02_y_SXNiwc4'),
(31, 'interesser@gmail.com', 'interesser', '$2y$12$noA1v9.ZfqBw1j1ZUBLbc.sI5KyWDfcjXI70rOhgPdjLR/9852hBi', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzEsImVtYWlsIjoiaW50ZXJlc3NlckBnbWFpbC5jb20iLCJleHAiOjE3Nzc5ODY0ODV9.z19pxd4Apk4p1rWLJnOq23HEFxa963vOMM-H_BqrTr8'),
(32, 'utilisateur1@gmail.com', 'utilisateur1', '$2y$12$aCAYOwF.BIKOZJKR4zINSur7XBy54WeuRRdyq490y05Gnw59Z6eyy', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzIsImVtYWlsIjoidXRpbGlzYXRldXIxQGdtYWlsLmNvbSIsImV4cCI6MTc3ODEzNzk2N30.HKh52S0Y60rXd3ftaxoS0tEV6RTbBC9amQY7YIqYmcM'),
(33, 'utilisateur2@gmail.com', 'utilisateur2', '$2y$12$s.qmil4Z2zJLEUPh8prpFuQTZ6QX9AFt2z8/rZfu7WYg0Gg2URrZK', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzMsImVtYWlsIjoidXRpbGlzYXRldXIyQGdtYWlsLmNvbSIsImV4cCI6MTc3ODA3NTAyMX0.NiDQ0Ee12o2FJAV8DHMZWJhMIzIxSnhJMm3G_aHiPZg'),
(34, 'ahmad.aml@edugee.ch', 'ahmaad', '$2y$12$iqMhLpEZvCbFqzrMIeJRye/zMPvfPEPgYDArGZQ6qKm1BBpFcUKaq', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzQsImVtYWlsIjoiYWhtYWQuYW1sQGVkdWdlZS5jaCIsImV4cCI6MTc3Nzk4ODY5Nn0.mW_wSiVM8gPu028FKnGhaZ8NktfHpla9oV6wJ3JSP20'),
(35, '1@gmail.com', '12', '$2y$12$I4XpmNBDNefR4A5/yJYDKugOmFaWglbyvFiJt95ntIA1Cb3IbPudS', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzUsImVtYWlsIjoiMUBnbWFpbC5jb20iLCJleHAiOjE3NzgwNTk2MTd9.PwNkLGbYYwp1aqwybUxz0WfASJHd7W_mTP7IRl7k-qQ');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Advertisement`
--
ALTER TABLE `Advertisement`
  ADD PRIMARY KEY (`id_advertisement`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `Message`
--
ALTER TABLE `Message`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `sender_id` (`id_sender`),
  ADD KEY `recipient_id` (`id_recipient`),
  ADD KEY `id_advertisement` (`id_advertisement`),
  ADD KEY `original_message_id` (`original_message_id`);

--
-- Index pour la table `Picture`
--
ALTER TABLE `Picture`
  ADD PRIMARY KEY (`id_picture`),
  ADD KEY `id_advertisement` (`id_advertisement`);

--
-- Index pour la table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `Advertisement`
--
ALTER TABLE `Advertisement`
  MODIFY `id_advertisement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `Message`
--
ALTER TABLE `Message`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `Picture`
--
ALTER TABLE `Picture`
  MODIFY `id_picture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `User`
--
ALTER TABLE `User`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Advertisement`
--
ALTER TABLE `Advertisement`
  ADD CONSTRAINT `Advertisement_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `User` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `Message`
--
ALTER TABLE `Message`
  ADD CONSTRAINT `Message_ibfk_1` FOREIGN KEY (`id_sender`) REFERENCES `User` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `Message_ibfk_2` FOREIGN KEY (`id_recipient`) REFERENCES `User` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `Message_ibfk_3` FOREIGN KEY (`id_advertisement`) REFERENCES `Advertisement` (`id_advertisement`) ON DELETE CASCADE,
  ADD CONSTRAINT `Message_ibfk_4` FOREIGN KEY (`original_message_id`) REFERENCES `Message` (`id_message`) ON DELETE SET NULL;

--
-- Contraintes pour la table `Picture`
--
ALTER TABLE `Picture`
  ADD CONSTRAINT `Picture_ibfk_1` FOREIGN KEY (`id_advertisement`) REFERENCES `Advertisement` (`id_advertisement`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
