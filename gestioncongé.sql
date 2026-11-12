-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : Dim 25 jan. 2026 à 16:59
-- Version du serveur : 10.4.8-MariaDB
-- Version de PHP : 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestioncongé`
--

-- --------------------------------------------------------

--
-- Structure de la table `demande_conge`
--

CREATE TABLE `demande_conge` (
  `id_demandeConge` int(11) NOT NULL,
  `employe_matricule` char(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `nombre_jours` int(11) DEFAULT NULL,
  `date_soumission` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'EN_ATTENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `demande_conge`
--

INSERT INTO `demande_conge` (`id_demandeConge`, `employe_matricule`, `date_debut`, `date_fin`, `nombre_jours`, `date_soumission`, `statut`) VALUES
(3, 'EMP00001', '2026-01-22', '2026-01-30', 9, '2026-01-17 16:14:37', 'ACCEPTEE');

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

CREATE TABLE `departement` (
  `id_departement` int(11) NOT NULL,
  `nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `responsable_matricule` char(8) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `departement`
--

INSERT INTO `departement` (`id_departement`, `nom`, `responsable_matricule`) VALUES
(6, 'Informatique', 'RH00001'),
(11, 'Rédaction', 'RH00001'),
(12, 'Production', 'RH00001'),
(13, 'Technique', 'RH00001'),
(14, 'Marketing', 'RH00001'),
(15, 'Diffusion', 'RH00001'),
(16, 'Programmation', 'RH00001'),
(17, 'Ressources Humaines', 'RH00001'),
(18, 'Comptabilité', 'RH00001');

-- --------------------------------------------------------

--
-- Structure de la table `employe`
--

CREATE TABLE `employe` (
  `matricule` char(8) COLLATE utf8_unicode_ci NOT NULL,
  `date_embauche` date DEFAULT NULL,
  `poste` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `departement_id` int(11) DEFAULT NULL,
  `nom_employe` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `employe`
--

INSERT INTO `employe` (`matricule`, `date_embauche`, `poste`, `departement_id`, `nom_employe`) VALUES
('EMP00001', '2020-01-15', 'programmeur', 6, 'alice');

-- --------------------------------------------------------

--
-- Structure de la table `rh`
--

CREATE TABLE `rh` (
  `matricule` char(8) COLLATE utf8_unicode_ci NOT NULL,
  `nom_prenom` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `rh`
--

INSERT INTO `rh` (`matricule`, `nom_prenom`) VALUES
('RH00001', 'Sami Ben Ali');

-- --------------------------------------------------------

--
-- Structure de la table `solde_conge`
--

CREATE TABLE `solde_conge` (
  `id_soldeConge` int(11) NOT NULL,
  `employe_matricule` char(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jours_restants` int(11) DEFAULT NULL,
  `jours_pris_annee_courante` int(11) DEFAULT NULL,
  `annee_reference` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `solde_conge`
--

INSERT INTO `solde_conge` (`id_soldeConge`, `employe_matricule`, `jours_restants`, `jours_pris_annee_courante`, `annee_reference`) VALUES
(1, 'EMP00001', 21, 9, 2026);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `matricule` char(8) COLLATE utf8_unicode_ci NOT NULL,
  `login` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type_utilisateur` enum('EMPLOYE','RH') COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`matricule`, `login`, `mot_de_passe`, `type_utilisateur`) VALUES
('EMP00001', 'employe01', 'employe123', 'EMPLOYE'),
('RH00001', 'rh_admin', 'admin123', 'RH');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `demande_conge`
--
ALTER TABLE `demande_conge`
  ADD PRIMARY KEY (`id_demandeConge`),
  ADD KEY `fk_demande_employe` (`employe_matricule`);

--
-- Index pour la table `departement`
--
ALTER TABLE `departement`
  ADD PRIMARY KEY (`id_departement`),
  ADD KEY `fk_departement_rh` (`responsable_matricule`);

--
-- Index pour la table `employe`
--
ALTER TABLE `employe`
  ADD PRIMARY KEY (`matricule`),
  ADD KEY `fk` (`departement_id`);

--
-- Index pour la table `rh`
--
ALTER TABLE `rh`
  ADD PRIMARY KEY (`matricule`);

--
-- Index pour la table `solde_conge`
--
ALTER TABLE `solde_conge`
  ADD PRIMARY KEY (`id_soldeConge`),
  ADD KEY `fk_solde_employe` (`employe_matricule`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`matricule`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `demande_conge`
--
ALTER TABLE `demande_conge`
  MODIFY `id_demandeConge` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `departement`
--
ALTER TABLE `departement`
  MODIFY `id_departement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `solde_conge`
--
ALTER TABLE `solde_conge`
  MODIFY `id_soldeConge` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `demande_conge`
--
ALTER TABLE `demande_conge`
  ADD CONSTRAINT `fk_demande_employe` FOREIGN KEY (`employe_matricule`) REFERENCES `employe` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `departement`
--
ALTER TABLE `departement`
  ADD CONSTRAINT `fk_departement_rh` FOREIGN KEY (`responsable_matricule`) REFERENCES `rh` (`matricule`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `employe`
--
ALTER TABLE `employe`
  ADD CONSTRAINT `fk` FOREIGN KEY (`departement_id`) REFERENCES `departement` (`id_departement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_employe_utilisateur` FOREIGN KEY (`matricule`) REFERENCES `utilisateur` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `rh`
--
ALTER TABLE `rh`
  ADD CONSTRAINT `fk_rh_utilisateur` FOREIGN KEY (`matricule`) REFERENCES `utilisateur` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `solde_conge`
--
ALTER TABLE `solde_conge`
  ADD CONSTRAINT `fk_solde_employe` FOREIGN KEY (`employe_matricule`) REFERENCES `employe` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
