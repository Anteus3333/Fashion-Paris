-- schema.sql — Fashion Paris (e-commerce) — structure complète
-- Mis à jour : 24/11/2026
--
-- Infomaniak (réinstall propre) :
--   1) Vider / supprimer toutes les tables de la BDD
--   2) Importer ce fichier
--   3) Importer ensuite sql/donnees_test.sql
--
-- Docker : monté en 01-schema.sql au premier démarrage du volume.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- Ordre inverse des dépendances (+ avis legacy si ancienne install)
DROP TABLE IF EXISTS `avis`;
DROP TABLE IF EXISTS `tentative_connexion`;
DROP TABLE IF EXISTS `newsletter`;
DROP TABLE IF EXISTS `suivi_commande`;
DROP TABLE IF EXISTS `commande_ligne`;
DROP TABLE IF EXISTS `commande`;
DROP TABLE IF EXISTS `produit_image`;
DROP TABLE IF EXISTS `produit`;
DROP TABLE IF EXISTS `categorie`;
DROP TABLE IF EXISTS `horaire`;
DROP TABLE IF EXISTS `role`;
DROP TABLE IF EXISTS `utilisateur`;

-- -----------------------------------------------------
-- Utilisateurs, rôles, sécurité auth
-- -----------------------------------------------------
CREATE TABLE `utilisateur` (
  `utilisateur_id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `prenom` VARCHAR(50) NULL,
  `nom` VARCHAR(50) NULL,
  `telephone` VARCHAR(50) NULL,
  `adresse_postale` VARCHAR(255) NULL,
  `code_postal` VARCHAR(10) NULL,
  `ville` VARCHAR(50) NULL,
  `actif` TINYINT(1) NOT NULL DEFAULT 1,
  `email_verifie` TINYINT(1) NOT NULL DEFAULT 0,
  `email_token` VARCHAR(64) NULL,
  `email_token_expire` DATETIME NULL,
  `reset_token` VARCHAR(64) NULL,
  `reset_expire` DATETIME NULL,
  `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE INDEX `idx_email_unique` (`email`)
) ENGINE = InnoDB;

CREATE TABLE `role` (
  `role_id` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(50) NOT NULL,
  `utilisateur_id` INT NOT NULL,
  PRIMARY KEY (`role_id`),
  INDEX `fk_role_utilisateur_idx` (`utilisateur_id`),
  CONSTRAINT `fk_role_utilisateur`
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `tentative_connexion` (
  `tentative_id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `ip` VARCHAR(45) NULL,
  `reussie` TINYINT(1) NOT NULL DEFAULT 0,
  `date_tentative` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tentative_id`),
  INDEX `idx_tentative_email` (`email`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Catalogue
-- -----------------------------------------------------
CREATE TABLE `categorie` (
  `categorie_id` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `description` TEXT NULL,
  `ordre` INT NOT NULL DEFAULT 0,
  `actif` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`categorie_id`),
  UNIQUE INDEX `idx_categorie_slug` (`slug`)
) ENGINE = InnoDB;

CREATE TABLE `produit` (
  `produit_id` INT NOT NULL AUTO_INCREMENT,
  `categorie_id` INT NOT NULL,
  `nom` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(180) NOT NULL,
  `description` TEXT NULL,
  `prix` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `sku` VARCHAR(50) NULL,
  `actif` TINYINT(1) NOT NULL DEFAULT 1,
  `mis_en_avant` TINYINT(1) NOT NULL DEFAULT 0,
  `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`produit_id`),
  UNIQUE INDEX `idx_produit_slug` (`slug`),
  INDEX `fk_produit_categorie_idx` (`categorie_id`),
  CONSTRAINT `fk_produit_categorie`
    FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`categorie_id`)
) ENGINE = InnoDB;

CREATE TABLE `produit_image` (
  `image_id` INT NOT NULL AUTO_INCREMENT,
  `produit_id` INT NOT NULL,
  `chemin` VARCHAR(255) NOT NULL,
  `alt` VARCHAR(255) NULL,
  `ordre` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`image_id`),
  INDEX `fk_image_produit_idx` (`produit_id`),
  CONSTRAINT `fk_image_produit`
    FOREIGN KEY (`produit_id`) REFERENCES `produit` (`produit_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Commandes
-- -----------------------------------------------------
CREATE TABLE `commande` (
  `numero_commande` VARCHAR(50) NOT NULL,
  `utilisateur_id` INT NOT NULL,
  `date_commande` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` VARCHAR(50) NOT NULL DEFAULT 'en_attente',
  `montant_total` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `frais_livraison` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `adresse_livraison` VARCHAR(255) NULL,
  `code_postal_livraison` VARCHAR(10) NULL,
  `ville_livraison` VARCHAR(100) NULL,
  `mode_paiement` VARCHAR(50) NULL,
  `motif_annulation` VARCHAR(500) NULL,
  PRIMARY KEY (`numero_commande`),
  INDEX `fk_commande_utilisateur_idx` (`utilisateur_id`),
  CONSTRAINT `fk_commande_utilisateur`
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`)
) ENGINE = InnoDB;

CREATE TABLE `commande_ligne` (
  `ligne_id` INT NOT NULL AUTO_INCREMENT,
  `numero_commande` VARCHAR(50) NOT NULL,
  `produit_id` INT NOT NULL,
  `quantite` INT NOT NULL DEFAULT 1,
  `prix_unitaire` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`ligne_id`),
  INDEX `fk_ligne_commande_idx` (`numero_commande`),
  INDEX `fk_ligne_produit_idx` (`produit_id`),
  CONSTRAINT `fk_ligne_commande`
    FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ligne_produit`
    FOREIGN KEY (`produit_id`) REFERENCES `produit` (`produit_id`)
) ENGINE = InnoDB;

CREATE TABLE `suivi_commande` (
  `suivi_id` INT NOT NULL AUTO_INCREMENT,
  `numero_commande` VARCHAR(50) NOT NULL,
  `statut` VARCHAR(50) NOT NULL,
  `commentaire` TEXT NULL,
  `date_modification` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`suivi_id`),
  INDEX `idx_suivi_numero` (`numero_commande`),
  CONSTRAINT `fk_suivi_commande`
    FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Newsletter
-- -----------------------------------------------------
CREATE TABLE `newsletter` (
  `newsletter_id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `actif` TINYINT(1) NOT NULL DEFAULT 1,
  `date_inscription` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`newsletter_id`),
  UNIQUE INDEX `idx_newsletter_email` (`email`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Horaires boutique (footer / contact / admin)
-- -----------------------------------------------------
CREATE TABLE `horaire` (
  `horaire_id` INT NOT NULL AUTO_INCREMENT,
  `jour` VARCHAR(50) NOT NULL,
  `heure_ouverture` VARCHAR(50) NOT NULL,
  `heure_fermeture` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`horaire_id`)
) ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
