<?php
/** Fil conducteur checkout.
 *  $etapeCourante = panier|livraison|paiement
 *  $etapeAtteinte = étape la plus avancée mémorisée (reprise)
 */
$etapeCourante = $etapeCourante ?? 'panier';
$etapeAtteinte = $etapeAtteinte ?? $etapeCourante;
$etapes = [
    'panier'    => ['label' => 'Panier', 'href' => BASE_URL . '/panier'],
    'livraison' => ['label' => 'Livraison', 'href' => BASE_URL . '/commande?etape=livraison'],
    'paiement'  => ['label' => 'Paiement', 'href' => BASE_URL . '/commande?etape=paiement'],
];
$ordre = array_keys($etapes);
$indexCourant = array_search($etapeCourante, $ordre, true);
$indexAtteint = array_search($etapeAtteinte, $ordre, true);
if ($indexCourant === false) {
    $indexCourant = 0;
}
if ($indexAtteint === false) {
    $indexAtteint = $indexCourant;
}
$indexAtteint = max($indexAtteint, $indexCourant);
?>
<nav class="checkout-steps" aria-label="Étapes de commande">
    <ol class="checkout-steps__list">
        <?php foreach ($etapes as $cle => $etapeInfo): ?>
            <?php
            $i = array_search($cle, $ordre, true);
            $classe = 'checkout-steps__item';
            if ($i === $indexCourant) {
                $classe .= ' is-current';
            } elseif ($i < $indexCourant) {
                $classe .= ' is-done';
            } elseif ($i <= $indexAtteint) {
                $classe .= ' is-reachable';
            }
            $cliquable = ($i !== $indexCourant) && ($i <= $indexAtteint);
            ?>
            <li class="<?= $classe ?>">
                <?php if ($cliquable): ?>
                    <a class="checkout-steps__link" href="<?= htmlspecialchars($etapeInfo['href']) ?>">
                        <span class="checkout-steps__num" aria-hidden="true"><?= $i + 1 ?></span>
                        <span class="checkout-steps__label"><?= htmlspecialchars($etapeInfo['label']) ?></span>
                    </a>
                <?php else: ?>
                    <span class="checkout-steps__link" <?= $i === $indexCourant ? 'aria-current="step"' : '' ?>>
                        <span class="checkout-steps__num" aria-hidden="true"><?= $i + 1 ?></span>
                        <span class="checkout-steps__label"><?= htmlspecialchars($etapeInfo['label']) ?></span>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        <?php unset($etapeInfo, $cle, $i, $classe, $cliquable); ?>
    </ol>
</nav>
