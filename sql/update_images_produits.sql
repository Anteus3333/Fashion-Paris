-- À exécuter sur Infomaniak (phpMyAdmin) ou Docker si les produits existent déjà
-- Remplace placeholders (default.svg / SVG démo) par les photos JPG catalogue.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

UPDATE produit_image SET chemin = 'produits/robe-midi-soie.jpg', alt = 'Robe midi soie'
WHERE produit_id = 1
  AND chemin IN ('produits/default.svg', 'produits/robe-midi-soie.svg');

UPDATE produit_image SET chemin = 'produits/blazer-structuring.jpg', alt = 'Blazer structuring'
WHERE produit_id = 2
  AND chemin IN ('produits/default.svg', 'produits/blazer-structuring.svg');

UPDATE produit_image SET chemin = 'produits/chemise-oxford.jpg', alt = 'Chemise oxford'
WHERE produit_id = 3
  AND chemin IN ('produits/default.svg', 'produits/chemise-oxford.svg');

UPDATE produit_image SET chemin = 'produits/sac-cabas-cuir.jpg', alt = 'Sac cabas cuir'
WHERE produit_id = 4
  AND chemin IN ('produits/default.svg', 'produits/sac-cabas-cuir.svg');
