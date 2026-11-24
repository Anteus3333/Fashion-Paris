-- donnees_test.sql — jeu de données complet Fashion Paris
--
-- Infomaniak : importer APRÈS schema.sql (réinstall complète).
-- Docker : monté en 02-donnees.sql au premier démarrage du volume.
--
-- Contenu : comptes démo, 3 catégories, 22 produits + photos JPG,
--           horaires boutique, commandes / suivi / newsletter.
-- Mot de passe de tous les comptes : Password1!
--
-- Photos à déployer aussi : public/images/produits/*.jpg (22 fichiers).
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

-- 22 produits (9 femme, 7 homme, 6 accessoires)
-- mis_en_avant : l’accueil n’affiche que les 3 plus récents (ids 12, 5, 4)
INSERT INTO `produit` (`produit_id`, `categorie_id`, `nom`, `slug`, `description`, `prix`, `stock`, `sku`, `actif`, `mis_en_avant`) VALUES
(1, 1, 'Robe midi soie', 'robe-midi-soie', 'Robe midi fluide en soie, coupe élégante.', 189.00, 12, 'FP-F-001', 1, 1),
(2, 1, 'Blazer structuring', 'blazer-structuring', 'Blazer structuré, épaulettes discrètes.', 249.00, 8, 'FP-F-002', 1, 1),
(3, 2, 'Chemise oxford', 'chemise-oxford', 'Chemise oxford classique, coton peigné.', 89.00, 20, 'FP-H-001', 1, 0),
(4, 3, 'Sac cabas cuir', 'sac-cabas-cuir', 'Cabas en cuir pleine fleur, doublure coton.', 320.00, 5, 'FP-A-001', 1, 1),
(5, 1, 'Manteau laine camel', 'manteau-laine-camel', 'Manteau long en laine, ceinture assortie, teinte camel.', 420.00, 6, 'FP-F-003', 1, 1),
(6, 1, 'Pantalon cigarette', 'pantalon-cigarette', 'Pantalon cigarette taille haute, drapé italien.', 129.00, 15, 'FP-F-004', 1, 0),
(7, 1, 'Pull cachemire', 'pull-cachemire', 'Pull col V en cachemire, coupe droite.', 159.00, 10, 'FP-F-005', 1, 0),
(8, 1, 'Jupe plissée midi', 'jupe-plissee-midi', 'Jupe midi plissée, ceinture fine intégrée.', 98.00, 14, 'FP-F-006', 1, 0),
(9, 1, 'Top satiné', 'top-satine', 'Top satiné à bretelles fines, dos nu.', 79.00, 18, 'FP-F-007', 1, 0),
(10, 1, 'Trench beige', 'trench-beige', 'Trench classique imperméable, boutons nacrés.', 289.00, 7, 'FP-F-008', 1, 0),
(11, 1, 'Combinaison fluide', 'combinaison-fluide', 'Combinaison fluide jambe large, ceinture amovible.', 175.00, 9, 'FP-F-009', 1, 0),
(12, 2, 'Costume deux pièces', 'costume-deux-pieces', 'Costume laine stretch, coupe droite contemporaine.', 399.00, 5, 'FP-H-002', 1, 1),
(13, 2, 'Pull col roulé', 'pull-col-roule', 'Pull col roulé mérinos, maille fine.', 119.00, 12, 'FP-H-003', 1, 0),
(14, 2, 'Pantalon chino', 'pantalon-chino', 'Chino coton sergé, coupe slim.', 95.00, 16, 'FP-H-004', 1, 0),
(15, 2, 'Veste bomber', 'veste-bomber', 'Bomber matelassé léger, finitions contrastées.', 189.00, 8, 'FP-H-005', 1, 0),
(16, 2, 'Polo piqué', 'polo-pique', 'Polo piqué coton, col boutonné.', 69.00, 22, 'FP-H-006', 1, 0),
(17, 2, 'Manteau loden', 'manteau-loden', 'Manteau loden mi-long, col chemise.', 350.00, 4, 'FP-H-007', 1, 0),
(18, 3, 'Ceinture cuir fine', 'ceinture-cuir-fine', 'Ceinture fine en cuir lisse, boucle dorée.', 65.00, 25, 'FP-A-002', 1, 0),
(19, 3, 'Écharpe mérinos', 'echarpe-merinos', 'Écharpe mérinos tissée, finitions franges.', 85.00, 14, 'FP-A-003', 1, 0),
(20, 3, 'Pochette soie', 'pochette-soie', 'Pochette de costume en soie imprimée.', 45.00, 30, 'FP-A-004', 1, 0),
(21, 3, 'Lunettes monture acétate', 'lunettes-monture', 'Monture acétate façon écaille, verres neutres.', 140.00, 10, 'FP-A-005', 1, 0),
(22, 3, 'Bottines chelsea', 'bottines-chelsea', 'Chelsea boots cuir, semelle Goodyear.', 229.00, 7, 'FP-A-006', 1, 0);

INSERT INTO `produit_image` (`produit_id`, `chemin`, `alt`, `ordre`) VALUES
(1, 'produits/robe-midi-soie.jpg', 'Robe midi soie', 0),
(2, 'produits/blazer-structuring.jpg', 'Blazer structuring', 0),
(3, 'produits/chemise-oxford.jpg', 'Chemise oxford', 0),
(4, 'produits/sac-cabas-cuir.jpg', 'Sac cabas cuir', 0),
(5, 'produits/manteau-laine-camel.jpg', 'Manteau laine camel', 0),
(6, 'produits/pantalon-cigarette.jpg', 'Pantalon cigarette', 0),
(7, 'produits/pull-cachemire.jpg', 'Pull cachemire', 0),
(8, 'produits/jupe-plissee-midi.jpg', 'Jupe plissée midi', 0),
(9, 'produits/top-satine.jpg', 'Top satiné', 0),
(10, 'produits/trench-beige.jpg', 'Trench beige', 0),
(11, 'produits/combinaison-fluide.jpg', 'Combinaison fluide', 0),
(12, 'produits/costume-deux-pieces.jpg', 'Costume deux pièces', 0),
(13, 'produits/pull-col-roule.jpg', 'Pull col roulé', 0),
(14, 'produits/pantalon-chino.jpg', 'Pantalon chino', 0),
(15, 'produits/veste-bomber.jpg', 'Veste bomber', 0),
(16, 'produits/polo-pique.jpg', 'Polo piqué', 0),
(17, 'produits/manteau-loden.jpg', 'Manteau loden', 0),
(18, 'produits/ceinture-cuir-fine.jpg', 'Ceinture cuir fine', 0),
(19, 'produits/echarpe-merinos.jpg', 'Écharpe mérinos', 0),
(20, 'produits/pochette-soie.jpg', 'Pochette soie', 0),
(21, 'produits/lunettes-monture.jpg', 'Lunettes monture acétate', 0),
(22, 'produits/bottines-chelsea.jpg', 'Bottines chelsea', 0);

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
-- frais_livraison = forfait 10 € (hors livrées anciennes à 0)
-- -----------------------------------------------------
INSERT INTO `commande` (
  `numero_commande`, `utilisateur_id`, `date_commande`, `statut`,
  `montant_total`, `frais_livraison`, `adresse_livraison`, `code_postal_livraison`,
  `ville_livraison`, `mode_paiement`
) VALUES
('FP-20261101-001', 2, '2026-11-01 14:22:00', 'livree', 189.00, 0.00, '25 Avenue Montaigne', '75008', 'Paris', 'carte'),
('FP-20261105-002', 4, '2026-11-05 10:05:00', 'expediee', 579.00, 10.00, '14 rue de Rivoli', '75001', 'Paris', 'carte'),
('FP-20261110-003', 5, '2026-11-10 16:40:00', 'confirmee', 99.00, 10.00, '3 avenue de l’Opéra', '75001', 'Paris', 'carte'),
('FP-20261112-004', 2, '2026-11-12 09:15:00', 'en_attente', 259.00, 10.00, '25 Avenue Montaigne', '75008', 'Paris', 'carte');

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
