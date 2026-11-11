<?php
ob_start();
?>
<section class="section page-narrow">
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
                    <form method="post" action="<?= BASE_URL ?>/panier/modifier" class="cart-qty-form">
                        <?= Csrf::champ() ?>
                        <input type="hidden" name="produit_id" value="<?= (int) $p['produit_id'] ?>">
                        <label class="sr-only" for="qte-<?= (int) $p['produit_id'] ?>">Quantité</label>
                        <input type="number" id="qte-<?= (int) $p['produit_id'] ?>" name="quantite"
                               value="<?= (int) $ligne['quantite'] ?>" min="1" max="<?= (int) $p['stock'] ?>">
                        <button type="submit" class="btn btn-ghost">OK</button>
                    </form>
                    <p class="price"><?= ViewHelper::prix($ligne['ligne_total']) ?></p>
                    <form method="post" action="<?= BASE_URL ?>/panier/retirer">
                        <?= Csrf::champ() ?>
                        <input type="hidden" name="produit_id" value="<?= (int) $p['produit_id'] ?>">
                        <button type="submit" class="btn btn-ghost">Retirer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="cart-totals">
            <p>Sous-total : <strong><?= ViewHelper::prix($sousTotal) ?></strong></p>
            <p>Livraison : <strong><?= $frais <= 0 ? 'Offerte' : ViewHelper::prix($frais) ?></strong></p>
            <p class="cart-total">Total : <strong><?= ViewHelper::prix($total) ?></strong></p>
        </div>

        <div class="hero-actions">
            <?= ViewHelper::btnNav(BASE_URL . '/catalogue', 'Continuer', 'btn btn-ghost') ?>
            <?= ViewHelper::btnNav(BASE_URL . '/commande', 'Passer commande', 'btn btn-primary') ?>
        </div>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/layout.php';
