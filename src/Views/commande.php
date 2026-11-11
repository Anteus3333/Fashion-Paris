<?php
ob_start();
?>
<section class="section page-narrow">
    <h1>Passer commande</h1>

    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>Récapitulatif</h2>
    <ul class="cart-list">
        <?php foreach ($lignes as $ligne): ?>
            <li class="cart-item">
                <span><?= htmlspecialchars($ligne['produit']['nom']) ?> × <?= (int) $ligne['quantite'] ?></span>
                <span class="price"><?= ViewHelper::prix($ligne['ligne_total']) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="cart-totals">
        <p>Sous-total : <strong><?= ViewHelper::prix($sousTotal) ?></strong></p>
        <p>Livraison : <strong><?= $frais <= 0 ? 'Offerte' : ViewHelper::prix($frais) ?></strong></p>
        <p class="cart-total">Total : <strong><?= ViewHelper::prix($total) ?></strong></p>
    </div>

    <h2>Livraison &amp; paiement</h2>
    <form method="post" class="auth-form" novalidate>
        <?= Csrf::champ() ?>

        <label for="adresse_livraison">Adresse</label>
        <input type="text" id="adresse_livraison" name="adresse_livraison" required
               value="<?= htmlspecialchars($old['adresse_livraison']) ?>">

        <label for="code_postal_livraison">Code postal</label>
        <input type="text" id="code_postal_livraison" name="code_postal_livraison" required pattern="\d{5}"
               value="<?= htmlspecialchars($old['code_postal_livraison']) ?>">

        <label for="ville_livraison">Ville</label>
        <input type="text" id="ville_livraison" name="ville_livraison" required
               value="<?= htmlspecialchars($old['ville_livraison']) ?>">

        <label for="mode_paiement">Mode de paiement</label>
        <select id="mode_paiement" name="mode_paiement">
            <?php foreach ($config['modes_paiement'] as $code => $libelle): ?>
                <option value="<?= htmlspecialchars($code) ?>" <?= $old['mode_paiement'] === $code ? 'selected' : '' ?>>
                    <?= htmlspecialchars($libelle) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Confirmer la commande</button>
    </form>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/layout.php';
