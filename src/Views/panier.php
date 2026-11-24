<?php
ob_start();
?>
<section class="section page-narrow checkout-page">
    <?php
    $etapeCourante = 'panier';
    $etapeAtteinte = 'panier';
    if (!empty($_SESSION['checkout']['adresse_livraison'])) {
        $etapeAtteinte = (($_SESSION['checkout']['etape'] ?? '') === 'paiement') ? 'paiement' : 'livraison';
    }
    $urlRepriseCommande = ($etapeAtteinte === 'paiement')
        ? BASE_URL . '/commande?etape=paiement'
        : BASE_URL . '/commande';
    require __DIR__ . '/partials/commande-etapes.php';
    ?>
    <h1>Panier</h1>

    <?php if (empty($lignes)): ?>
        <p class="empty-state">Votre panier est vide.</p>
        <p><?= ViewHelper::btnNav(BASE_URL . '/catalogue', 'Continuer vos achats', 'btn btn-primary') ?></p>
    <?php else: ?>
        <ul class="cart-list">
            <?php foreach ($lignes as $ligne): ?>
                <?php $p = $ligne['produit']; ?>
                <li class="cart-item">
                    <div class="cart-item-info">
                        <a href="<?= BASE_URL ?>/produit/<?= htmlspecialchars($p['slug']) ?>">
                            <strong><?= htmlspecialchars($p['nom']) ?></strong>
                        </a>
                        <p class="muted"><?= ViewHelper::prix($p['prix']) ?> · stock <?= (int) $p['stock'] ?></p>
                    </div>
                    <form method="post" action="<?= BASE_URL ?>/panier/retirer" class="cart-item-remove">
                        <?= Csrf::champ() ?>
                        <input type="hidden" name="produit_id" value="<?= (int) $p['produit_id'] ?>">
                        <button type="submit" class="cart-remove-btn" title="Retirer" aria-label="Retirer <?= htmlspecialchars($p['nom']) ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path fill="currentColor" d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1zm1 2v0h4V5h-4zM6 9h2v10H6V9zm5 0h2v10h-2V9zm5 0h2v10h-2V9zM7 21a1 1 0 0 1-1-1V8h12v12a1 1 0 0 1-1 1H7z"/>
                            </svg>
                        </button>
                    </form>
                    <form method="post" action="<?= BASE_URL ?>/panier/modifier" class="cart-qty-form">
                        <?= Csrf::champ() ?>
                        <input type="hidden" name="produit_id" value="<?= (int) $p['produit_id'] ?>">
                        <label class="sr-only" for="qte-<?= (int) $p['produit_id'] ?>">Quantité</label>
                        <input type="number" id="qte-<?= (int) $p['produit_id'] ?>" name="quantite"
                               value="<?= (int) $ligne['quantite'] ?>" min="1" max="<?= (int) $p['stock'] ?>">
                        <button type="submit" class="btn btn-ghost">OK</button>
                    </form>
                    <p class="price cart-item-line"><?= ViewHelper::prix($ligne['ligne_total']) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php
        $configLivraison = $configLivraison ?? require __DIR__ . '/../../config/commandeconfig.php';
        $delaiLivraison = (string) ($configLivraison['delai_livraison'] ?? '7 jours ouvrés');
        ?>
        <div class="cart-totals">
            <div class="cart-totals__row">
                <span>Sous-total</span>
                <strong><?= ViewHelper::prix($sousTotal) ?></strong>
            </div>
            <div class="cart-totals__row">
                <span>Livraison (forfait)</span>
                <strong><?= ViewHelper::prix($frais) ?></strong>
            </div>
            <p class="cart-totals__delay muted">Délai estimé : <?= htmlspecialchars($delaiLivraison) ?></p>
            <div class="cart-totals__row cart-totals__row--total">
                <span>Total</span>
                <strong><?= ViewHelper::prix($total) ?></strong>
            </div>
        </div>

        <div class="cart-actions">
            <?= ViewHelper::btnNav(BASE_URL . '/catalogue', 'Continuer ses achats', 'btn btn-secondary') ?>
            <?= ViewHelper::btnNav(
                $urlRepriseCommande,
                $etapeAtteinte === 'paiement' ? 'Reprendre le paiement' : 'Passer commande',
                'btn btn-primary'
            ) ?>
        </div>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/layout.php';
