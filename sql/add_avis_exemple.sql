-- Avis d’exemple (accueil + 1 en attente) — Infomaniak / Docker déjà seedé
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO `avis` (`produit_id`, `utilisateur_id`, `note`, `commentaire`, `statut`)
SELECT 4, 2, 5, 'Parfait. Merci.', 'valide'
FROM DUAL
WHERE EXISTS (SELECT 1 FROM utilisateur WHERE utilisateur_id = 2)
  AND NOT EXISTS (SELECT 1 FROM avis WHERE commentaire = 'Parfait. Merci.' AND utilisateur_id = 2);

INSERT INTO `avis` (`produit_id`, `utilisateur_id`, `note`, `commentaire`, `statut`)
SELECT 2, 2, 5, 'Coupe impeccable, livraison rapide.', 'valide'
FROM DUAL
WHERE EXISTS (SELECT 1 FROM utilisateur WHERE utilisateur_id = 2)
  AND NOT EXISTS (SELECT 1 FROM avis WHERE commentaire = 'Coupe impeccable, livraison rapide.' AND utilisateur_id = 2);

INSERT INTO `avis` (`produit_id`, `utilisateur_id`, `note`, `commentaire`, `statut`)
SELECT 1, 2, 5, 'Très belle pièce, qualité au rendez-vous.', 'valide'
FROM DUAL
WHERE EXISTS (SELECT 1 FROM utilisateur WHERE utilisateur_id = 2)
  AND NOT EXISTS (SELECT 1 FROM avis WHERE commentaire = 'Très belle pièce, qualité au rendez-vous.' AND utilisateur_id = 2);

INSERT INTO `avis` (`produit_id`, `utilisateur_id`, `note`, `commentaire`, `statut`)
SELECT 3, 2, 4, 'Bonne chemise, taille juste.', 'en_attente'
FROM DUAL
WHERE EXISTS (SELECT 1 FROM utilisateur WHERE utilisateur_id = 2)
  AND NOT EXISTS (SELECT 1 FROM avis WHERE commentaire = 'Bonne chemise, taille juste.' AND utilisateur_id = 2);
