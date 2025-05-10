-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 10 avr. 2025 à 15:47
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00"


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `greencity`
--

-- --------------------------------------------------------

--
-- Structure de la table `commentairepub`
--

CREATE TABLE `commentairepub` (
  `Id` int(11) NOT NULL,
  `IdUser` int(11) NOT NULL,
  `IdPublication` int(11) NOT NULL,
  `Score` int(11) NOT NULL,
  `Contenu` varchar(40) NOT NULL,
  `DateCommentaire` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commentairesujet`
--

CREATE TABLE `commentairesujet` (
  `Id` int(11) NOT NULL,
  `IdSujet` int(11) NOT NULL,
  `IdAuteur` int(11) NOT NULL,
  `Contenu` varchar(50) NOT NULL,
  `DatePublication` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `defi`
--

CREATE TABLE `defi` (
  `Id` int(11) NOT NULL,
  `Titre` varchar(20) NOT NULL,
  `Description` varchar(40) NOT NULL,
  `Objectif` varchar(20) NOT NULL,
  `DateDebut` date NOT NULL,
  `DateFin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE `evenement` (
  `Id` int(11) NOT NULL,
  `Titre` varchar(20) NOT NULL,
  `Description` varchar(40) NOT NULL,
  `Date` date NOT NULL,
  `Ville` varchar(20) NOT NULL,
  `FraisParticipation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `niveau`
--

CREATE TABLE `niveau` (
  `Id` int(11) NOT NULL,
  `ScoreMin` int(11) NOT NULL,
  `DroitOrganisation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `niveau`
--

INSERT INTO `niveau` (`Id`, `ScoreMin`, `DroitOrganisation`) VALUES
(1, 0, 0),
(2, 10, 0);

-- --------------------------------------------------------

--
-- Structure de la table `participationdefi`
--

CREATE TABLE `participationdefi` (
  `Id` int(11) NOT NULL,
  `IdDefi` int(11) NOT NULL,
  `IdUser` int(11) NOT NULL,
  `Statut` varchar(20) NOT NULL,
  `Progression` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `participationevent`
--

CREATE TABLE `participationevent` (
  `Id` int(11) NOT NULL,
  `IdEvent` int(11) NOT NULL,
  `IdUser` int(11) NOT NULL,
  `DateInscri` date NOT NULL,
  `Payement` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `publication`
--

CREATE TABLE `publication` (
  `Id` int(11) NOT NULL,
  `IdUser` int(11) NOT NULL,
  `Titre` varchar(20) NOT NULL,
  `Contenu` varchar(40) NOT NULL,
  `DatePublication` date NOT NULL,
  `Categorie` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
--

CREATE TABLE `reclamation` (
  `Id` int(11) NOT NULL,
  `IdUser` int(11) NOT NULL,
  `Type` varchar(20) NOT NULL,
  `Description` varchar(40) NOT NULL,
  `Statut` varchar(20) NOT NULL,
  `DateEnvoi` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sujet`
--

CREATE TABLE `sujet` (
  `Id` int(11) NOT NULL,
  `Titre` varchar(30) NOT NULL,
  `IdCreateur` int(11) NOT NULL,
  `DateCreation` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `Id` int(11) NOT NULL,
  `IdNiveau` int(11) NOT NULL DEFAULT 1,
  `Nom` varchar(20) NOT NULL,
  `Prenom` varchar(20) NOT NULL,
  `Mail` varchar(30) NOT NULL,
  `Mdp` varchar(60) NOT NULL,
  `Role` varchar(20) NOT NULL DEFAULT 'utilisateur',
  `Score` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`Id`, `IdNiveau`, `Nom`, `Prenom`, `Mail`, `Mdp`, `Role`, `Score`) VALUES
(12, 1, 'Maalla', 'Lina', 'linamaalla15@gmail.com', '$2y$10$CfhIQoESIFyLRSaVsvWP3.ariTWzljCbWhsGzDFOtptSUeWXr3TcC', 'utilisateur', 0),
(13, 1, 'Abdelhak', 'Malek', 'malekabdelhak@gmail.com', '$2y$10$VG1Tom/w/ZlnpnL545y3aueXevq/iCOR3OYGdhYJhkoj3jtPgqVKu', 'utilisateur', 0),
(14, 1, 'Boujnah', 'Islem', 'Islemboujnah@gmail.com', '$2y$10$h8xOrntk9/VdgqxwJVnNI.Murhqv3gW/AKQz6mS8boVteESYX..lS', 'utilisateur', 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commentairepub`
--
ALTER TABLE `commentairepub`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `commentPub` (`IdPublication`),
  ADD KEY `commentPubUser` (`IdUser`);

--
-- Index pour la table `commentairesujet`
--
ALTER TABLE `commentairesujet`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `commentSujet` (`IdSujet`),
  ADD KEY `commentUser` (`IdAuteur`);

--
-- Index pour la table `defi`
--
ALTER TABLE `defi`
  ADD PRIMARY KEY (`Id`);

--
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`Id`);

--
-- Index pour la table `niveau`
--
ALTER TABLE `niveau`
  ADD PRIMARY KEY (`Id`);

--
-- Index pour la table `participationdefi`
--
ALTER TABLE `participationdefi`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `participationDefi` (`IdDefi`),
  ADD KEY `participationDefiUser` (`IdUser`);

--
-- Index pour la table `participationevent`
--
ALTER TABLE `participationevent`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `participationEvent` (`IdEvent`),
  ADD KEY `participationEventUser` (`IdUser`);

--
-- Index pour la table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `publicationUser` (`IdUser`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `reclamationUser` (`IdUser`);

--
-- Index pour la table `sujet`
--
ALTER TABLE `sujet`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `sujetUser` (`IdCreateur`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `niveauUser` (`IdNiveau`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commentairepub`
--
ALTER TABLE `commentairepub`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `commentairesujet`
--
ALTER TABLE `commentairesujet`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `defi`
--
ALTER TABLE `defi`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `niveau`
--
ALTER TABLE `niveau`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `participationdefi`
--
ALTER TABLE `participationdefi`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `participationevent`
--
ALTER TABLE `participationevent`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `publication`
--
ALTER TABLE `publication`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sujet`
--
ALTER TABLE `sujet`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentairepub`
--
ALTER TABLE `commentairepub`
  ADD CONSTRAINT `commentPub` FOREIGN KEY (`IdPublication`) REFERENCES `publication` (`Id`),
  ADD CONSTRAINT `commentPubUser` FOREIGN KEY (`IdUser`) REFERENCES `user` (`Id`);

--
-- Contraintes pour la table `commentairesujet`
--
ALTER TABLE `commentairesujet`
  ADD CONSTRAINT `commentSujet` FOREIGN KEY (`IdSujet`) REFERENCES `sujet` (`Id`),
  ADD CONSTRAINT `commentUser` FOREIGN KEY (`IdAuteur`) REFERENCES `user` (`Id`);

--
-- Contraintes pour la table `participationdefi`
--
ALTER TABLE `participationdefi`
  ADD CONSTRAINT `participationDefi` FOREIGN KEY (`IdDefi`) REFERENCES `defi` (`Id`),
  ADD CONSTRAINT `participationDefiUser` FOREIGN KEY (`IdUser`) REFERENCES `user` (`Id`);

--
-- Contraintes pour la table `participationevent`
--
ALTER TABLE `participationevent`
  ADD CONSTRAINT `participationEvent` FOREIGN KEY (`IdEvent`) REFERENCES `evenement` (`Id`),
  ADD CONSTRAINT `participationEventUser` FOREIGN KEY (`IdUser`) REFERENCES `user` (`Id`);

--
-- Contraintes pour la table `publication`
--
ALTER TABLE `publication`
  ADD CONSTRAINT `publicationUser` FOREIGN KEY (`IdUser`) REFERENCES `user` (`Id`);

--
-- Contraintes pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `reclamationUser` FOREIGN KEY (`IdUser`) REFERENCES `user` (`Id`);

--
-- Contraintes pour la table `sujet`
--
ALTER TABLE `sujet`
  ADD CONSTRAINT `sujetUser` FOREIGN KEY (`IdCreateur`) REFERENCES `user` (`Id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `niveauUser` FOREIGN KEY (`IdNiveau`) REFERENCES `niveau` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
