<?php
ob_start();
$config = $config ?? require __DIR__ . '/../../config/commandeconfig.php';
?>
<section class="section page-narrow commande-page" data-base-url="<?= htmlspecialchars(BASE_URL) ?>">
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
    <div class="cart-totals" id="recap-content"
         data-sous-total="<?= htmlspecialchars((string) $sousTotal) ?>"
         data-seuil-franco="<?= htmlspecialchars((string) ($config['seuil_franco'] ?? 150)) ?>">
        <p>Sous-total : <strong id="recap-sous-total"><?= ViewHelper::prix($sousTotal) ?></strong></p>
        <p>Livraison : <strong id="recap-frais"><?= $frais <= 0 ? 'Offerte' : ViewHelper::prix($frais) ?></strong></p>
        <p class="muted" id="recap-franco-hint" <?= $frais <= 0 ? 'hidden' : '' ?>>
            Plus que <span id="recap-reste-franco"><?= ViewHelper::prix(max(0, (float) ($config['seuil_franco'] ?? 150) - $sousTotal)) ?></span>
            pour la livraison offerte.
        </p>
        <p class="cart-total">Total : <strong id="recap-total"><?= ViewHelper::prix($total) ?></strong></p>
    </div>

    <h2>Livraison &amp; paiement</h2>
    <form method="post" id="commande-form" class="auth-form" data-abandon-guard novalidate>
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

        <label for="date_livraison">Date de livraison souhaitée (optionnel)</label>
        <?= ViewHelper::champDate('date_livraison', 'date_livraison', $old['date_livraison'] ?? '', false, date('Y-m-d')) ?>

        <label for="mode_paiement">Mode de paiement</label>
        <select id="mode_paiement" name="mode_paiement" class="form-select">
            <?php foreach ($config['modes_paiement'] as $code => $libelle): ?>
                <option value="<?= htmlspecialchars($code) ?>" <?= $old['mode_paiement'] === $code ? 'selected' : '' ?>>
                    <?= htmlspecialchars($libelle) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary" id="btn-confirmer-commande">Confirmer la commande</button>
    </form>
</section>
<?php
$contenu = ob_get_clean();
$scriptsFooter = '<script src="' . BASE_URL . '/js/commande.js?v=' . (@filemtime(__DIR__ . '/../../public/js/commande.js') ?: time()) . '" defer></script>';
require __DIR__ . '/layout.php';
