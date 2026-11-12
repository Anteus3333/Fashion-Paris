-- À exécuter sur Infomaniak (phpMyAdmin) si les produits existent déjà
-- avec l’ancien chemin produits/default.svg
-- Ne touche que les placeholders default.svg (préserve d’éventuelles autres images).
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

UPDATE produit_image SET chemin = 'produits/robe-midi-soie.svg', alt = 'Robe midi soie'
WHERE produit_id = 1 AND chemin = 'produits/default.svg';

UPDATE produit_image SET chemin = 'produits/blazer-structuring.svg', alt = 'Blazer structuring'
WHERE produit_id = 2 AND chemin = 'produits/default.svg';

UPDATE produit_image SET chemin = 'produits/chemise-oxford.svg', alt = 'Chemise oxford'
WHERE produit_id = 3 AND chemin = 'produits/default.svg';

UPDATE produit_image SET chemin = 'produits/sac-cabas-cuir.svg', alt = 'Sac cabas cuir'
WHERE produit_id = 4 AND chemin = 'produits/default.svg';
