-- Compte personnel de test (Infomaniak / Docker déjà seedé sans ce compte)
-- Mot de passe : Password1!
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
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
