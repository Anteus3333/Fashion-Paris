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

    <h2>Avis clients</h2>
    <?php if (empty($listeAvis)): ?>
        <p class="muted">Pas encore d'avis validés.</p>
    <?php else: ?>
        <ul class="avis-list">
            <?php foreach ($listeAvis as $a): ?>
                <li>
                    <strong><?= (int) $a['note'] ?>/5</strong>
                    — <?= htmlspecialchars(ViewHelper::formatPrenom($a['prenom'])) ?>
                    <p><?= nl2br(htmlspecialchars($a['commentaire'] ?? '')) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (isset($_SESSION['utilisateur'])): ?>
        <h3>Laisser un avis</h3>
        <?php if (!empty($erreursAvis)): ?>
            <ul class="form-error"><?php foreach ($erreursAvis as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        <?php endif; ?>
        <form method="post" class="auth-form">
            <?= Csrf::champ() ?>
            <input type="hidden" name="form" value="avis">
            <label for="note">Note</label>
            <select id="note" name="note" required>
                <?php for ($n = 5; $n >= 1; $n--): ?>
                    <option value="<?= $n ?>"><?= $n ?>/5</option>
                <?php endfor; ?>
            </select>
            <label for="commentaire">Commentaire</label>
            <textarea id="commentaire" name="commentaire" rows="3" required></textarea>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    <?php else: ?>
        <p><a href="<?= BASE_URL ?>/login?redirect=<?= urlencode('/produit/' . $produit['slug']) ?>">Connectez-vous</a> pour laisser un avis.</p>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/layout.php';
