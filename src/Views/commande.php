<?php
ob_start();
$config = $config ?? require __DIR__ . '/../../config/commandeconfig.php';
$etape = $etape ?? 'livraison';
$etapeCourante = $etape;
?>
<section class="section checkout-page commande-page" data-base-url="<?= htmlspecialchars(BASE_URL) ?>">
    <?php require __DIR__ . '/partials/commande-etapes.php'; ?>

    <header class="checkout-header">
        <h1><?= $etape === 'paiement' ? 'Paiement' : 'Livraison' ?></h1>
        <?php if ($etape === 'paiement'): ?>
            <p class="muted"><a href="<?= BASE_URL ?>/commande">← Retour à la livraison</a></p>
        <?php else: ?>
            <p class="muted"><a href="<?= BASE_URL ?>/panier">← Retour au panier</a></p>
        <?php endif; ?>
    </header>

    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="checkout-layout">
        <div class="checkout-main">
            <?php if ($etape === 'livraison'): ?>
                <p class="muted">Indiquez vos coordonnées de livraison (enregistrées sur votre profil pour les prochaines commandes).</p>
                <form method="post" id="commande-form" class="auth-form checkout-form" data-abandon-guard
                      data-abandon-titre="Quitter la commande ?"
                      data-abandon-message="Vos informations de livraison ne seront pas enregistrées si vous quittez maintenant."
                      data-abandon-ok="Quitter"
                      data-abandon-cancel="Rester"
                      novalidate>
                    <?= Csrf::champ() ?>
                    <input type="hidden" name="action" value="livraison">

                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" required autocomplete="tel"
                           value="<?= htmlspecialchars($old['telephone']) ?>">

                    <label for="adresse_livraison">Adresse</label>
                    <input type="text" id="adresse_livraison" name="adresse_livraison" required autocomplete="street-address"
                           value="<?= htmlspecialchars($old['adresse_livraison']) ?>">

                    <label for="code_postal_livraison">Code postal</label>
                    <input type="text" id="code_postal_livraison" name="code_postal_livraison" required pattern="\d{5}"
                           autocomplete="postal-code" value="<?= htmlspecialchars($old['code_postal_livraison']) ?>">

                    <label for="ville_livraison">Ville</label>
                    <input type="text" id="ville_livraison" name="ville_livraison" required autocomplete="address-level2"
                           value="<?= htmlspecialchars($old['ville_livraison']) ?>">

                    <button type="submit" class="btn btn-primary">Continuer vers le paiement</button>
                </form>
            <?php else: ?>
                <form method="post" id="commande-form" class="auth-form checkout-form" data-abandon-guard
                      data-abandon-titre="Quitter le paiement ?"
                      data-abandon-message="La commande n’a pas encore été confirmée. Voulez-vous vraiment quitter ?"
                      data-abandon-ok="Quitter"
                      data-abandon-cancel="Rester"
                      novalidate>
                    <?= Csrf::champ() ?>
                    <input type="hidden" name="action" value="confirmer">

                    <fieldset class="checkout-pay-method">
                        <legend>Mode de paiement</legend>
                        <?php foreach ($config['modes_paiement'] as $code => $libelle): ?>
                            <label class="checkout-pay-option">
                                <input type="radio" name="mode_paiement" value="<?= htmlspecialchars($code) ?>"
                                    <?= ($old['mode_paiement'] ?? 'carte') === $code ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($libelle) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>

                    <label class="checkout-cgv">
                        <input type="checkbox" name="accepte_cgv" value="1" <?= !empty($old['accepte_cgv']) ? 'checked' : '' ?> required>
                        <span>J’ai lu et j’accepte les <a href="<?= BASE_URL ?>/cgv" target="_blank" rel="noopener">CGV</a>.</span>
                    </label>

                    <p class="checkout-secure muted">Paiement simulé — aucune carte n’est débitée.</p>

                    <button type="submit" class="btn btn-primary" id="btn-confirmer-commande">
                        Payer <?= ViewHelper::prix($total) ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <aside class="checkout-aside" aria-label="Récapitulatif de commande">
            <h2>Récapitulatif</h2>
            <ul class="checkout-aside__lines">
                <?php foreach ($lignes as $ligne): ?>
                    <li>
                        <span><?= htmlspecialchars($ligne['produit']['nom']) ?> × <?= (int) $ligne['quantite'] ?></span>
                        <span><?= ViewHelper::prix($ligne['ligne_total']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="cart-totals" id="recap-content"
                 data-sous-total="<?= htmlspecialchars((string) $sousTotal) ?>">
                <p>Sous-total : <strong id="recap-sous-total"><?= ViewHelper::prix($sousTotal) ?></strong></p>
                <p>Livraison (forfait) : <strong id="recap-frais"><?= ViewHelper::prix($frais) ?></strong></p>
                <p class="muted" id="recap-delai">
                    Délai estimé : <?= htmlspecialchars((string) ($config['delai_livraison'] ?? '7 jours ouvrés')) ?>.
                </p>
                <p class="cart-total">Total : <strong id="recap-total"><?= ViewHelper::prix($total) ?></strong></p>
            </div>
            <?php if ($etape === 'paiement' && !empty($old['adresse_livraison'])): ?>
                <div class="checkout-aside__info">
                    <h3>Livraison</h3>
                    <p>
                        <?= htmlspecialchars($old['adresse_livraison']) ?><br>
                        <?= htmlspecialchars($old['code_postal_livraison']) ?> <?= htmlspecialchars($old['ville_livraison']) ?><br>
                        <?= htmlspecialchars($old['telephone']) ?>
                    </p>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</section>
<?php
$contenu = ob_get_clean();
$scriptsFooter = '<script src="' . BASE_URL . '/js/commande.js?v=' . (@filemtime(__DIR__ . '/../../public/js/commande.js') ?: time()) . '" defer></script>';
require __DIR__ . '/layout.php';
