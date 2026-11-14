-- Avis d’exemple multi-auteurs (accueil) + clients démo
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET @pwd = '$2y$10$IIO6gazxesXrkEt8AjTMZu06ZzjstaYNV5Ir4HHFi4CjR7KhOT1Pm';

INSERT INTO `utilisateur` (`email`, `password`, `prenom`, `nom`, `telephone`, `adresse_postale`, `code_postal`, `ville`, `actif`, `email_verifie`)
SELECT 'sophie@example.fr', @pwd, 'Sophie', 'Martin', '0600000004', '14 rue de Rivoli', '75001', 'Paris', 1, 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM utilisateur WHERE email = 'sophie@example.fr');

INSERT INTO `utilisateur` (`email`, `password`, `prenom`, `nom`, `telephone`, `adresse_postale`, `code_postal`, `ville`, `actif`, `email_verifie`)
SELECT 'stephane@example.fr', @pwd, 'Stéphane', 'Durand', '0600000005', '3 avenue de l’Opéra', '75001', 'Paris', 1, 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM utilisateur WHERE email = 'stephane@example.fr');

INSERT INTO `role` (`libelle`, `utilisateur_id`)
SELECT 'utilisateur', u.utilisateur_id FROM utilisateur u
WHERE u.email IN ('sophie@example.fr', 'stephane@example.fr')
  AND NOT EXISTS (SELECT 1 FROM role r WHERE r.utilisateur_id = u.utilisateur_id AND r.libelle = 'utilisateur');

-- Réaffecte / recrée les avis publiés avec des auteurs distincts
DELETE FROM avis WHERE commentaire IN (
  'Parfait. Merci.',
  'Coupe impeccable, livraison rapide.',
  'Très belle pièce, qualité au rendez-vous.',
  'Bonne chemise, taille juste.'
);

INSERT INTO `avis` (`produit_id`, `utilisateur_id`, `note`, `commentaire`, `statut`)
SELECT 4, u.utilisateur_id, 5, 'Parfait. Merci.', 'valide'
FROM utilisateur u WHERE u.email = 'stephane@example.fr';

INSERT INTO `avis` (`produit_id`, `utilisateur_id`, `note`, `commentaire`, `statut`)
SELECT 2, u.utilisateur_id, 5, 'Coupe impeccable, livraison rapide.', 'valide'
FROM utilisateur u WHERE u.email = 'sophie@example.fr';

INSERT INTO `avis` (`produit_id`, `utilisateur_id`, `note`, `commentaire`, `statut`)
SELECT 1, u.utilisateur_id, 5, 'Très belle pièce, qualité au rendez-vous.', 'valide'
FROM utilisateur u WHERE u.email = 'client@fashionparis.fr';

INSERT INTO `avis` (`produit_id`, `utilisateur_id`, `note`, `commentaire`, `statut`)
SELECT 3, u.utilisateur_id, 4, 'Bonne chemise, taille juste.', 'en_attente'
FROM utilisateur u WHERE u.email = 'client@fashionparis.fr';
