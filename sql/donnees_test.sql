-- donnees_test.sql — jeu de démonstration Fashion Paris
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Mot de passe de tous les comptes de test : Password1!
-- Hash bcrypt généré avec password_hash('Password1!', PASSWORD_DEFAULT)
SET @pwd = '$2y$10$IIO6gazxesXrkEt8AjTMZu06ZzjstaYNV5Ir4HHFi4CjR7KhOT1Pm';

INSERT INTO `utilisateur` (`utilisateur_id`, `email`, `password`, `prenom`, `nom`, `telephone`, `adresse_postale`, `code_postal`, `ville`, `actif`, `email_verifie`) VALUES
(1, 'admin@fashionparis.fr', @pwd, 'Alice', 'Admin', '0600000001', '10 Rue de la Paix', '75002', 'Paris', 1, 1),
(2, 'client@fashionparis.fr', @pwd, 'Camille', 'Client', '0600000002', '25 Avenue Montaigne', '75008', 'Paris', 1, 1);

INSERT INTO `role` (`libelle`, `utilisateur_id`) VALUES
('administrateur', 1),
('utilisateur', 2);

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
(1, 'produits/robe-midi-soie.svg', 'Robe midi soie', 0),
(2, 'produits/blazer-structuring.svg', 'Blazer structuring', 0),
(3, 'produits/chemise-oxford.svg', 'Chemise oxford', 0),
(4, 'produits/sac-cabas-cuir.svg', 'Sac cabas cuir', 0);
