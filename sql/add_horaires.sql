-- Table + seed horaires (Infomaniak / Docker déjà initialisé)
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

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
  SELECT 'Samedi', '10:00', '19:00' UNION ALL
  SELECT 'Dimanche', '12:00', '18:00'
) AS v
WHERE NOT EXISTS (SELECT 1 FROM horaire LIMIT 1);
