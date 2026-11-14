<?php
ob_start();
?>
<section class="section page-narrow product-detail">
    <p class="breadcrumb">
        <a href="<?= BASE_URL ?>/catalogue">Catalogue</a>
        · <?= htmlspecialchars($produit['categorie_libelle'] ?? '') ?>
    </p>
    <h1><?= htmlspecialchars($produit['nom']) ?></h1>
    <p class="price"><?= ViewHelper::prix($produit['prix']) ?></p>
    <p><?= nl2br(htmlspecialchars($produit['description'] ?? '')) ?></p>
    <p class="muted">Stock : <?= (int) $produit['stock'] ?> — SKU <?= htmlspecialchars($produit['sku'] ?? '—') ?></p>

    <?php if ((int) $produit['stock'] > 0): ?>
        <form method="post" action="<?= BASE_URL ?>/panier/ajouter" class="add-cart-form">
            <?= Csrf::champ() ?>
            <input type="hidden" name="produit_id" value="<?= (int) $produit['produit_id'] ?>">
            <label for="quantite">Quantité</label>
            <input type="number" id="quantite" name="quantite" value="1" min="1" max="<?= (int) $produit['stock'] ?>">
            <button type="submit" class="btn btn-primary">Ajouter au panier</button>
        </form>
    <?php else: ?>
        <p class="form-error">Produit en rupture de stock.</p>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/layout.php';
