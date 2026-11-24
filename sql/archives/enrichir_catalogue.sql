-- enrichir_catalogue.sql — ajoute 18 produits si absents (Docker / Infomaniak déjà seedés)
-- Sans wipe : garde les 4 produits historiques (ids 1–4).
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO `produit` (`produit_id`, `categorie_id`, `nom`, `slug`, `description`, `prix`, `stock`, `sku`, `actif`, `mis_en_avant`)
SELECT * FROM (
  SELECT 5 AS produit_id, 1 AS categorie_id, 'Manteau laine camel' AS nom, 'manteau-laine-camel' AS slug, 'Manteau long en laine, ceinture assortie, teinte camel.' AS description, 420.00 AS prix, 6 AS stock, 'FP-F-003' AS sku, 1 AS actif, 1 AS mis_en_avant UNION ALL
  SELECT 6, 1, 'Pantalon cigarette', 'pantalon-cigarette', 'Pantalon cigarette taille haute, drapé italien.', 129.00, 15, 'FP-F-004', 1, 0 UNION ALL
  SELECT 7, 1, 'Pull cachemire', 'pull-cachemire', 'Pull col V en cachemire, coupe droite.', 159.00, 10, 'FP-F-005', 1, 0 UNION ALL
  SELECT 8, 1, 'Jupe plissée midi', 'jupe-plissee-midi', 'Jupe midi plissée, ceinture fine intégrée.', 98.00, 14, 'FP-F-006', 1, 0 UNION ALL
  SELECT 9, 1, 'Top satiné', 'top-satine', 'Top satiné à bretelles fines, dos nu.', 79.00, 18, 'FP-F-007', 1, 0 UNION ALL
  SELECT 10, 1, 'Trench beige', 'trench-beige', 'Trench classique imperméable, boutons nacrés.', 289.00, 7, 'FP-F-008', 1, 0 UNION ALL
  SELECT 11, 1, 'Combinaison fluide', 'combinaison-fluide', 'Combinaison fluide jambe large, ceinture amovible.', 175.00, 9, 'FP-F-009', 1, 0 UNION ALL
  SELECT 12, 2, 'Costume deux pièces', 'costume-deux-pieces', 'Costume laine stretch, coupe droite contemporaine.', 399.00, 5, 'FP-H-002', 1, 1 UNION ALL
  SELECT 13, 2, 'Pull col roulé', 'pull-col-roule', 'Pull col roulé mérinos, maille fine.', 119.00, 12, 'FP-H-003', 1, 0 UNION ALL
  SELECT 14, 2, 'Pantalon chino', 'pantalon-chino', 'Chino coton sergé, coupe slim.', 95.00, 16, 'FP-H-004', 1, 0 UNION ALL
  SELECT 15, 2, 'Veste bomber', 'veste-bomber', 'Bomber matelassé léger, finitions contrastées.', 189.00, 8, 'FP-H-005', 1, 0 UNION ALL
  SELECT 16, 2, 'Polo piqué', 'polo-pique', 'Polo piqué coton, col boutonné.', 69.00, 22, 'FP-H-006', 1, 0 UNION ALL
  SELECT 17, 2, 'Manteau loden', 'manteau-loden', 'Manteau loden mi-long, col chemise.', 350.00, 4, 'FP-H-007', 1, 0 UNION ALL
  SELECT 18, 3, 'Ceinture cuir fine', 'ceinture-cuir-fine', 'Ceinture fine en cuir lisse, boucle dorée.', 65.00, 25, 'FP-A-002', 1, 0 UNION ALL
  SELECT 19, 3, 'Écharpe mérinos', 'echarpe-merinos', 'Écharpe mérinos tissée, finitions franges.', 85.00, 14, 'FP-A-003', 1, 0 UNION ALL
  SELECT 20, 3, 'Pochette soie', 'pochette-soie', 'Pochette de costume en soie imprimée.', 45.00, 30, 'FP-A-004', 1, 0 UNION ALL
  SELECT 21, 3, 'Lunettes monture acétate', 'lunettes-monture', 'Monture acétate façon écaille, verres neutres.', 140.00, 10, 'FP-A-005', 1, 0 UNION ALL
  SELECT 22, 3, 'Bottines chelsea', 'bottines-chelsea', 'Chelsea boots cuir, semelle Goodyear.', 229.00, 7, 'FP-A-006', 1, 0
) AS v
WHERE NOT EXISTS (SELECT 1 FROM produit p WHERE p.slug = v.slug);

INSERT INTO `produit_image` (`produit_id`, `chemin`, `alt`, `ordre`)
SELECT p.produit_id, v.chemin, v.alt, 0
FROM (
  SELECT 'manteau-laine-camel' AS slug, 'produits/manteau-laine-camel.jpg' AS chemin, 'Manteau laine camel' AS alt UNION ALL
  SELECT 'pantalon-cigarette', 'produits/pantalon-cigarette.jpg', 'Pantalon cigarette' UNION ALL
  SELECT 'pull-cachemire', 'produits/pull-cachemire.jpg', 'Pull cachemire' UNION ALL
  SELECT 'jupe-plissee-midi', 'produits/jupe-plissee-midi.jpg', 'Jupe plissée midi' UNION ALL
  SELECT 'top-satine', 'produits/top-satine.jpg', 'Top satiné' UNION ALL
  SELECT 'trench-beige', 'produits/trench-beige.jpg', 'Trench beige' UNION ALL
  SELECT 'combinaison-fluide', 'produits/combinaison-fluide.jpg', 'Combinaison fluide' UNION ALL
  SELECT 'costume-deux-pieces', 'produits/costume-deux-pieces.jpg', 'Costume deux pièces' UNION ALL
  SELECT 'pull-col-roule', 'produits/pull-col-roule.jpg', 'Pull col roulé' UNION ALL
  SELECT 'pantalon-chino', 'produits/pantalon-chino.jpg', 'Pantalon chino' UNION ALL
  SELECT 'veste-bomber', 'produits/veste-bomber.jpg', 'Veste bomber' UNION ALL
  SELECT 'polo-pique', 'produits/polo-pique.jpg', 'Polo piqué' UNION ALL
  SELECT 'manteau-loden', 'produits/manteau-loden.jpg', 'Manteau loden' UNION ALL
  SELECT 'ceinture-cuir-fine', 'produits/ceinture-cuir-fine.jpg', 'Ceinture cuir fine' UNION ALL
  SELECT 'echarpe-merinos', 'produits/echarpe-merinos.jpg', 'Écharpe mérinos' UNION ALL
  SELECT 'pochette-soie', 'produits/pochette-soie.jpg', 'Pochette soie' UNION ALL
  SELECT 'lunettes-monture', 'produits/lunettes-monture.jpg', 'Lunettes monture acétate' UNION ALL
  SELECT 'bottines-chelsea', 'produits/bottines-chelsea.jpg', 'Bottines chelsea'
) AS v
JOIN produit p ON p.slug = v.slug
WHERE NOT EXISTS (SELECT 1 FROM produit_image pi WHERE pi.produit_id = p.produit_id);
