<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/mon-compte">Mon compte</a></p>
    <h1>Commande <?= htmlspecialchars($commande['numero_commande']) ?></h1>
    <p>
        Statut :
        <strong><?= htmlspecialchars($config['statuts'][$commande['statut']] ?? $commande['statut']) ?></strong>
        · <?= htmlspecialchars($commande['date_commande']) ?>
    </p>
    <p class="muted">
        Livraison : <?= htmlspecialchars($commande['adresse_livraison']) ?>,
        <?= htmlspecialchars($commande['code_postal_livraison']) ?>
        <?= htmlspecialchars($commande['ville_livraison']) ?>
    </p>

    <h2>Articles</h2>
    <ul class="cart-list">
        <?php foreach ($lignes as $l): ?>
            <li class="cart-item">
                <span>
                    <a href="<?= BASE_URL ?>/produit/<?= htmlspecialchars($l['slug']) ?>"><?= htmlspecialchars($l['nom']) ?></a>
                    × <?= (int) $l['quantite'] ?>
                </span>
                <span class="price"><?= ViewHelper::prix((float) $l['prix_unitaire'] * (int) $l['quantite']) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="cart-totals">
        <p>Livraison : <?= ViewHelper::prix($commande['frais_livraison']) ?></p>
        <p class="cart-total">Total : <strong><?= ViewHelper::prix($commande['montant_total']) ?></strong></p>
    </div>

    <h2>Suivi</h2>
    <?php if (empty($suivi)): ?>
        <p class="muted">Aucun événement.</p>
    <?php else: ?>
        <ol class="suivi-list">
            <?php foreach ($suivi as $s): ?>
                <li>
                    <strong><?= htmlspecialchars($config['statuts'][$s['statut']] ?? $s['statut']) ?></strong>
                    <span class="muted"><?= htmlspecialchars($s['date_modification']) ?></span>
                    <?php if (!empty($s['commentaire'])): ?>
                        — <?= htmlspecialchars($s['commentaire']) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
