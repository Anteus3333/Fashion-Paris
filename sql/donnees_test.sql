-- donnees_test.sql — jeu de démonstration Fashion Paris
-- À importer après schema.sql (Docker / réinstall).
-- Mot de passe de tous les comptes : Password1!
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @pwd = '$2y$10$IIO6gazxesXrkEt8AjTMZu06ZzjstaYNV5Ir4HHFi4CjR7KhOT1Pm';

-- -----------------------------------------------------
-- Comptes (admin / personnel / clients)
-- -----------------------------------------------------
INSERT INTO `utilisateur` (`utilisateur_id`, `email`, `password`, `prenom`, `nom`, `telephone`, `adresse_postale`, `code_postal`, `ville`, `actif`, `email_verifie`) VALUES
(1, 'admin@fashionparis.fr', @pwd, 'Alice', 'Admin', '0600000001', '10 Rue de la Paix', '75002', 'Paris', 1, 1),
(2, 'client@fashionparis.fr', @pwd, 'Camille', 'Client', '0600000002', '25 Avenue Montaigne', '75008', 'Paris', 1, 1),
(3, 'personnel@fashionparis.fr', @pwd, 'Paul', 'Personnel', '0600000003', '5 Rue du Faubourg', '75009', 'Paris', 1, 1),
(4, 'sophie@example.fr', @pwd, 'Sophie', 'Martin', '0600000004', '14 rue de Rivoli', '75001', 'Paris', 1, 1),
(5, 'stephane@example.fr', @pwd, 'Stéphane', 'Durand', '0600000005', '3 avenue de l’Opéra', '75001', 'Paris', 1, 1);

INSERT INTO `role` (`libelle`, `utilisateur_id`) VALUES
('administrateur', 1),
('utilisateur', 2),
('personnel', 3),
('utilisateur', 4),
('utilisateur', 5);

-- -----------------------------------------------------
-- Catalogue
-- -----------------------------------------------------
INSERT INTO `categorie` (`categorie_id`, `libelle`, `slug`, `description`, `ordre`, `actif`) VALUES
(1, 'Femme', 'femme', 'Collection femme', 1, 1),
(2, 'Homme', 'homme', 'Collection homme', 2, 1),
(3, 'Accessoires', 'accessoires', 'Sacs, ceintures, bijoux', 3, 1);

INSERT INTO `produit` (`produit_id`, `categorie_id`, `nom`, `slug`, `description`, `prix`, `stock`, `sku`, `actif`, `mis_en_avant`) VALUES
(1, 1, 'Robe midi soie', 'robe-midi-soie', 'Robe midi fluide en soie, coupe élégante.', 189.00, 12, 'FP-F-001', 1, 1),
(2, 1, 'Blazer structuring', 'blazer-structuring', 'Blazer structuré, épaulettes discrètes.', 249.00, 8, 'FP-F-002', 1, 1),
(3, 2, 'Chemise oxford', 'chemise-oxford', 'Chemise oxford classique, coton peigné.', 89.00, 20, 'FP-H-001', 1, 0),
(4, 3, 'Sac cabas cuir', 'sac-cabas-cuir', 'Cabas en cuir pleine fleur, doublure coton.', 320.00, 5, 'FP-A-001', 1, 1);

INSERT INTO `produit_image` (`produit_id`, `chemin`, `alt`, `ordre`) VALUES
(1, 'produits/robe-midi-soie.jpg', 'Robe midi soie', 0),
(2, 'produits/blazer-structuring.jpg', 'Blazer structuring', 0),
(3, 'produits/chemise-oxford.jpg', 'Chemise oxford', 0),
(4, 'produits/sac-cabas-cuir.jpg', 'Sac cabas cuir', 0);

-- -----------------------------------------------------
-- Horaires boutique (lun–ven 10–19, samedi 10–18 ; dimanche fermé)
-- -----------------------------------------------------
INSERT INTO `horaire` (`jour`, `heure_ouverture`, `heure_fermeture`) VALUES
('Lundi', '10:00', '19:00'),
('Mardi', '10:00', '19:00'),
('Mercredi', '10:00', '19:00'),
('Jeudi', '10:00', '19:00'),
('Vendredi', '10:00', '19:00'),
('Samedi', '10:00', '18:00');

-- -----------------------------------------------------
-- Commandes démo (dashboard admin / espace personnel)
-- -----------------------------------------------------
INSERT INTO `commande` (
  `numero_commande`, `utilisateur_id`, `date_commande`, `statut`,
  `montant_total`, `frais_livraison`, `adresse_livraison`, `code_postal_livraison`,
  `ville_livraison`, `mode_paiement`
) VALUES
('FP-20261101-001', 2, '2026-11-01 14:22:00', 'livree', 189.00, 0.00, '25 Avenue Montaigne', '75008', 'Paris', 'carte'),
('FP-20261105-002', 4, '2026-11-05 10:05:00', 'expediee', 569.00, 0.00, '14 rue de Rivoli', '75001', 'Paris', 'carte'),
('FP-20261110-003', 5, '2026-11-10 16:40:00', 'confirmee', 89.00, 5.90, '3 avenue de l’Opéra', '75001', 'Paris', 'carte'),
('FP-20261112-004', 2, '2026-11-12 09:15:00', 'en_attente', 249.00, 5.90, '25 Avenue Montaigne', '75008', 'Paris', 'carte');

INSERT INTO `commande_ligne` (`numero_commande`, `produit_id`, `quantite`, `prix_unitaire`) VALUES
('FP-20261101-001', 1, 1, 189.00),
('FP-20261105-002', 2, 1, 249.00),
('FP-20261105-002', 4, 1, 320.00),
('FP-20261110-003', 3, 1, 89.00),
('FP-20261112-004', 2, 1, 249.00);

INSERT INTO `suivi_commande` (`numero_commande`, `statut`, `commentaire`, `date_modification`) VALUES
('FP-20261101-001', 'en_attente', 'Commande reçue', '2026-11-01 14:22:00'),
('FP-20261101-001', 'confirmee', 'Paiement validé', '2026-11-01 14:30:00'),
('FP-20261101-001', 'expediee', 'Colis remis au transporteur', '2026-11-02 09:00:00'),
('FP-20261101-001', 'livree', 'Livraison effectuée', '2026-11-03 11:20:00'),
('FP-20261105-002', 'en_attente', 'Commande reçue', '2026-11-05 10:05:00'),
('FP-20261105-002', 'confirmee', 'Paiement validé', '2026-11-05 10:12:00'),
('FP-20261105-002', 'expediee', 'Colis remis au transporteur', '2026-11-06 08:45:00'),
('FP-20261110-003', 'en_attente', 'Commande reçue', '2026-11-10 16:40:00'),
('FP-20261110-003', 'confirmee', 'Paiement validé', '2026-11-10 16:48:00'),
('FP-20261112-004', 'en_attente', 'Commande reçue', '2026-11-12 09:15:00');

-- -----------------------------------------------------
-- Newsletter démo
-- -----------------------------------------------------
INSERT INTO `newsletter` (`email`, `actif`) VALUES
('newsletter@example.fr', 1),
('sophie@example.fr', 1);
