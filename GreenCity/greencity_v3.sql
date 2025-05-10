-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 09 mai 2025 à 17:13
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `greencity`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id_comment` int(11) NOT NULL,
  `id_topic` int(11) NOT NULL,
  `id_client` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('En Attente','Confirmé') NOT NULL DEFAULT 'Confirmé',
  `comment_text` text NOT NULL,
  `sentiment` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id_comment`, `id_topic`, `id_client`, `client_name`, `created_at`, `status`, `comment_text`, `sentiment`) VALUES
(21, 25, '', 'syrine', '2025-04-29 15:58:38', 'Confirmé', 'countt', NULL),
(28, 25, '', 'ttt', '2025-04-30 08:45:16', 'Confirmé', 'vvv', NULL),
(29, 25, '', 'sis', '2025-05-01 15:34:09', 'Confirmé', '<dvjsdbd<vjdvl', NULL),
(34, 27, '', 'sirine', '2025-05-03 15:43:41', 'Confirmé', 'bon poste', NULL),
(35, 30, '', 'sis', '2025-05-06 18:37:49', 'Confirmé', 'hello', NULL),
(36, 30, '', 'si', '2025-05-06 23:43:24', 'Confirmé', 'mauvais', NULL),
(37, 27, '', 'darine', '2025-05-06 23:52:03', 'Confirmé', 'nul', NULL),
(38, 27, '', 'ryry', '2025-05-06 23:53:05', 'Confirmé', 'mal', NULL),
(41, 27, '', 'ri', '2025-05-07 09:25:05', 'Confirmé', 'esprit', NULL),
(42, 29, '', 'Lina', '2025-05-09 14:21:37', 'Confirmé', 'test1', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `defi`
--

CREATE TABLE `defi` (
  `Id_defi` int(11) NOT NULL,
  `titreD` varchar(20) NOT NULL,
  `descriptionD` varchar(20) NOT NULL,
  `objectifD` varchar(20) NOT NULL,
  `datedebutD` date NOT NULL,
  `datefinD` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `defi`
--

INSERT INTO `defi` (`Id_defi`, `titreD`, `descriptionD`, `objectifD`, `datedebutD`, `datefinD`) VALUES
(4, 'test2', 'test2', 'hbukhk', '2025-04-23', '2025-04-30'),
(5, 'aaaaa', 'zzzzzzeee', 'rrrrrrrr', '2025-04-24', '2025-04-30'),
(6, 'ffff', 'ggg', 'hhhh', '2025-04-29', '2025-04-30'),
(7, 'dddd', 'ggg', 'jjjj', '2025-05-01', '2025-05-07');

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE `evenement` (
  `id` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evenement`
--

INSERT INTO `evenement` (`id`, `titre`, `description`, `image`, `type`) VALUES
(43, 'yosrri', 'dddd', '', 'ddd'),
(44, ':::', ':::', '', ':::');

-- --------------------------------------------------------

--
-- Structure de la table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `feedback`
--

INSERT INTO `feedback` (`id`, `username`, `comment`, `created_at`) VALUES
(1, 'aa', 'dfsfs', '2025-05-07 05:25:15');

-- --------------------------------------------------------

--
-- Structure de la table `participationdefi`
--

CREATE TABLE `participationdefi` (
  `id` int(11) NOT NULL,
  `IdDefi` int(11) NOT NULL,
  `IdUser` int(11) NOT NULL,
  `statut` varchar(20) NOT NULL,
  `progression` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `participationdefi`
--

INSERT INTO `participationdefi` (`id`, `IdDefi`, `IdUser`, `statut`, `progression`) VALUES
(13, 1, 1, 'dssdq', 100),
(14, 1, 1, 'dsq', 10),
(15, 1, 1, 'dsqqsd', 50),
(16, 1, 1, 'jkjn', 40),
(17, 1, 1, 'azer', 40),
(18, 1, 101, 'Terminé', 80),
(19, 2, 102, 'Terminé', 90),
(20, 3, 101, 'Terminé', 70),
(21, 4, 103, 'Terminé', 100),
(22, 1, 1, 'dsqdqsd', 100),
(23, 1, 1, 'dsqdqsd', 100),
(24, 1, 1, 'dsqdqsd', 100);

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `participants` int(11) NOT NULL,
  `allergies` text DEFAULT NULL,
  `evenement_id` int(11) NOT NULL,
  `statut` enum('en_attente','confirmee','annulee') NOT NULL DEFAULT 'en_attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id`, `fullname`, `email`, `date`, `participants`, `allergies`, `evenement_id`, `statut`) VALUES
(36, 'ddd', 'dddd@gmail.com', '2025-05-02', 15, 'ssss', 43, ''),
(37, 'dddd', 'zzz@gmail.com', '2025-05-01', 455, 'ddd', 43, ''),
(38, ';;', 'debba@gmail.com', '2025-05-03', 14, ':::', 43, 'en_attente');

-- --------------------------------------------------------

--
-- Structure de la table `site_ratings`
--

CREATE TABLE `site_ratings` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `site_ratings`
--

INSERT INTO `site_ratings` (`id`, `user_email`, `rating`, `created_at`) VALUES
(64, 'linda@gmail.com', 5, '2025-04-29 15:43:56'),
(65, 'dyy@gmail.com', 5, '2025-04-29 15:44:16'),
(66, 'syrin@gmail.com', 5, '2025-04-29 15:47:09'),
(67, 'bon@gmail.com', 5, '2025-04-29 15:49:24'),
(68, 'dyyx@gmail.com', 1, '2025-04-29 15:55:11'),
(69, 'sis@gmail.com', 4, '2025-04-29 22:37:56'),
(70, 'rr@gmail.com', 1, '2025-04-29 22:38:24'),
(71, 'sou@gmail.com', 5, '2025-05-06 18:27:30'),
(72, 'ri@gmail.com', 1, '2025-05-07 09:17:19'),
(73, 'sisis@gmail.com', 4, '2025-05-07 09:26:39'),
(74, 'zi@gmail.com', 2, '2025-05-07 09:27:00');

-- --------------------------------------------------------

--
-- Structure de la table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `site_rating` float DEFAULT NULL,
  `rating_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `site_settings`
--

INSERT INTO `site_settings` (`id`, `site_rating`, `rating_count`) VALUES
(1, 3.4545, 11);

-- --------------------------------------------------------

--
-- Structure de la table `topics`
--

CREATE TABLE `topics` (
  `id_topic` int(11) NOT NULL,
  `id_client` varchar(255) DEFAULT NULL,
  `topic_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('En Attente','Confirmé') NOT NULL,
  `rating` float DEFAULT 0,
  `rating_count` int(11) DEFAULT 0,
  `comment_count` int(11) DEFAULT 0,
  `is_pinned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `topics`
--

INSERT INTO `topics` (`id_topic`, `id_client`, `topic_text`, `created_at`, `status`, `rating`, `rating_count`, `comment_count`, `is_pinned`) VALUES
(25, 'sis', 'hello', '2025-04-28 19:04:35', 'Confirmé', 0, 0, 3, 0),
(27, 'ameni', 'salutt', '2025-04-29 22:33:31', 'Confirmé', 0, 0, 4, 0),
(29, 'syrine', 'syrineeeeeeee', '2025-05-01 15:53:21', 'Confirmé', 0, 0, 1, 0),
(30, 'mimi', 'green city', '2025-05-06 18:31:39', 'Confirmé', 0, 0, 2, 1),
(31, 'jbj', 'knlikj', '2025-05-09 14:15:14', 'En Attente', 0, 0, 0, 0),
(32, 'jbj', 'knlikj', '2025-05-09 14:16:31', 'En Attente', 0, 0, 0, 0),
(33, 'k,k', 'jkhkuhlj', '2025-05-09 14:16:41', 'En Attente', 0, 0, 0, 0),
(34, 'jhbk', 'kjnkn', '2025-05-09 14:19:02', 'En Attente', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `Id` int(11) NOT NULL,
  `Nom` varchar(30) NOT NULL,
  `Prenom` varchar(30) NOT NULL,
  `Role` enum('Client','Agriculteur','Admin') NOT NULL,
  `Status` enum('En Attente','Confirmé') NOT NULL,
  `Mail` varchar(50) NOT NULL,
  `Mdp` varchar(255) NOT NULL,
  `Img` varchar(255) DEFAULT NULL,
  `FaceDescriptor` text DEFAULT NULL,
  `Phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`Id`, `Nom`, `Prenom`, `Role`, `Status`, `Mail`, `Mdp`, `Img`, `FaceDescriptor`, `Phone`) VALUES
(10, 'Boujnah', 'Islem', 'Client', 'Confirmé', 'Islemboujnah@gmail.com', '$2y$10$tMqz8FwUJ1ykVJySMXgd5eKaYJCcGV0dgdj1Mky.lctKcnXeg9Q3i', NULL, NULL, ''),
(11, 'Maalla', 'Lina', 'Admin', 'Confirmé', 'linamaalla15@gmail.com', '$2y$10$nwqO/X.a3m4DikRmYPD5lu9cING8plXwuolp/mo/9QwyonBgo.zYO', 'Ressources/profils/6811fd061ed20.jpg', '{\"feature1\":0.1,\"feature2\":0.2,\"feature3\":0.3}', '+21629309605'),
(14, 'Hichri', 'Syrine', 'Agriculteur', 'Confirmé', 'HichriSyrine@gmail.com', '$2y$10$ItVedPM/bJ3.3kKqT9Ujs.qUvQ834WfgxhkYnAVE17p3lx..../CS', NULL, NULL, ''),
(15, 'test', 'test1111', 'Agriculteur', 'Confirmé', 'test@esprit.tn', '$2y$10$yHQtnP0nVI2eb2nEkYtwAurM7tjgQHp1n7uQ7AlqQx1iKvu345YfW', NULL, NULL, ''),
(19, 'test1', 'test', 'Agriculteur', 'Confirmé', 'test@gmail.com', '$2y$10$64laopoJLiaAYbKVLyONduPsMUphi5vZnNCUculIWA4T1BIhXEmwu', NULL, NULL, ''),
(20, 'validation', 'validation', 'Agriculteur', 'Confirmé', 'validation@gmail.com', '$2y$10$sJG1LBaGpVpI9M/CJzAmHOn2/XMxxmH1d3RvPf74X.EcHvRuMIaEq', 'Ressources/profils/6811dc275b1ba.jpg', '{\"feature1\":0.1,\"feature2\":0.2,\"feature3\":0.3}', ''),
(22, 'Abdelhak', 'Malek', 'Client', 'Confirmé', 'malekabdelhak@gmail.com', '$2y$10$FZ3Xp1bkDlPfy/nDPIVtUOXANWdQf6CoOXw3dQbA3uG6Z0B8s/2Gu', NULL, NULL, '53616235'),
(23, 'Hichri', 'Syrine', 'Client', 'Confirmé', 'HichriSyrine@gmail.com', '$2y$10$lTHTs/XtlomdrXZofwgDB.OfIAX9uOTnHgy6jKZc8AnSJbP9pbc2S', NULL, NULL, '+21628718635');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id_comment`),
  ADD KEY `id_topic` (`id_topic`);

--
-- Index pour la table `defi`
--
ALTER TABLE `defi`
  ADD PRIMARY KEY (`Id_defi`);

--
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `participationdefi`
--
ALTER TABLE `participationdefi`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_ibfk_1` (`evenement_id`);

--
-- Index pour la table `site_ratings`
--
ALTER TABLE `site_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD UNIQUE KEY `user_email_2` (`user_email`);

--
-- Index pour la table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id_topic`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pour la table `defi`
--
ALTER TABLE `defi`
  MODIFY `Id_defi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `participationdefi`
--
ALTER TABLE `participationdefi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `site_ratings`
--
ALTER TABLE `site_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT pour la table `topics`
--
ALTER TABLE `topics`
  MODIFY `id_topic` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `commentstopic` FOREIGN KEY (`id_topic`) REFERENCES `topics` (`id_topic`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`evenement_id`) REFERENCES `evenement` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
