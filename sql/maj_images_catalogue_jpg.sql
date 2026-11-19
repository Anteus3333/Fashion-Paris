-- Remplace les placeholders SVG par les photos JPG catalogue
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

UPDATE produit_image pi
JOIN produit p ON p.produit_id = pi.produit_id
SET pi.chemin = CONCAT('produits/', p.slug, '.jpg'),
    pi.alt = p.nom
WHERE pi.chemin LIKE '%.svg'
  AND p.slug IN (
    'manteau-laine-camel','pantalon-cigarette','pull-cachemire','jupe-plissee-midi','top-satine','trench-beige',
    'combinaison-fluide','costume-deux-pieces','pull-col-roule','pantalon-chino','veste-bomber','polo-pique',
    'manteau-loden','ceinture-cuir-fine','echarpe-merinos','pochette-soie','lunettes-monture','bottines-chelsea'
  );
