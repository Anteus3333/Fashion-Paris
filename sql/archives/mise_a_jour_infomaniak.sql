-- mise_a_jour_infomaniak.sql
-- À exécuter sur la BDD Infomaniak EXISTANTE (sans tout écraser).
-- Corrige notamment : table horaire manquante → fatal error footer.
-- Ordre : 1) ce fichier  2) éventuellement update_images_produits.sql
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- 1) Auth : colonnes tokens si ancienne base
-- -----------------------------------------------------
SET @db := DATABASE();

SET @q := (
  SELECT IF(
    EXISTS(
      SELECT 1 FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'utilisateur' AND COLUMN_NAME = 'email_token'
    ),
    'SELECT 1',
    'ALTER TABLE `utilisateur`
       ADD COLUMN `email_token` VARCHAR(64) NULL AFTER `email_verifie`,
       ADD COLUMN `email_token_expire` DATETIME NULL AFTER `email_token`,
       ADD COLUMN `reset_token` VARCHAR(64) NULL AFTER `email_token_expire`,
       ADD COLUMN `reset_expire` DATETIME NULL AFTER `reset_token`'
  )
);
PREPARE stmt FROM @q; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------
-- 2) Horaires boutique (cause du crash footer)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `horaire` (
  `horaire_id` INT NOT NULL AUTO_INCREMENT,
  `jour` VARCHAR(50) NOT NULL,
  `heure_ouverture` VARCHAR(50) NOT NULL,
  `heure_fermeture` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`horaire_id`)
) ENGINE = InnoDB;

INSERT INTO `horaire` (`jour`, `heure_ouverture`, `heure_fermeture`)
SELECT v.jour, v.ouverture, v.fermeture
FROM (
  SELECT 'Lundi' AS jour, '10:00' AS ouverture, '19:00' AS fermeture UNION ALL
  SELECT 'Mardi', '10:00', '19:00' UNION ALL
  SELECT 'Mercredi', '10:00', '19:00' UNION ALL
  SELECT 'Jeudi', '10:00', '19:00' UNION ALL
  SELECT 'Vendredi', '10:00', '19:00' UNION ALL
  SELECT 'Samedi', '10:00', '18:00'
) AS v
WHERE NOT EXISTS (SELECT 1 FROM horaire LIMIT 1);

UPDATE horaire SET heure_ouverture = '10:00', heure_fermeture = '19:00'
WHERE jour IN ('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi');
UPDATE horaire SET heure_ouverture = '10:00', heure_fermeture = '18:00' WHERE jour = 'Samedi';
DELETE FROM horaire WHERE jour = 'Dimanche';

-- -----------------------------------------------------
-- 3) Compte personnel de démo (si absent)
-- Mot de passe : Password1!
-- -----------------------------------------------------
SET @pwd = '$2y$10$IIO6gazxesXrkEt8AjTMZu06ZzjstaYNV5Ir4HHFi4CjR7KhOT1Pm';

INSERT INTO `utilisateur` (`email`, `password`, `prenom`, `nom`, `telephone`, `adresse_postale`, `code_postal`, `ville`, `actif`, `email_verifie`)
SELECT 'personnel@fashionparis.fr', @pwd, 'Paul', 'Personnel', '0600000003', '5 Rue du Faubourg', '75009', 'Paris', 1, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM utilisateur WHERE email = 'personnel@fashionparis.fr');

INSERT INTO `role` (`libelle`, `utilisateur_id`)
SELECT 'personnel', u.utilisateur_id
FROM utilisateur u
WHERE u.email = 'personnel@fashionparis.fr'
  AND NOT EXISTS (
      SELECT 1 FROM role r WHERE r.utilisateur_id = u.utilisateur_id AND r.libelle = 'personnel'
  );

-- -----------------------------------------------------
-- 4) Retrait avis (fonctionnalité abandonnée)
-- -----------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `avis`;
SET FOREIGN_KEY_CHECKS = 1;
