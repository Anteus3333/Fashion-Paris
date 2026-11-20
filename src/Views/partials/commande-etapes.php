<?php
/** Fil conducteur checkout. Variable : $etapeCourante = panier|livraison|paiement */
$etapeCourante = $etapeCourante ?? 'panier';
$etapes = [
    'panier'    => ['label' => 'Panier', 'href' => BASE_URL . '/panier'],
    'livraison' => ['label' => 'Livraison', 'href' => BASE_URL . '/commande'],
    'paiement'  => ['label' => 'Paiement', 'href' => BASE_URL . '/commande?etape=paiement'],
];
$ordre = array_keys($etapes);
$indexCourant = array_search($etapeCourante, $ordre, true);
if ($indexCourant === false) {
    $indexCourant = 0;
}
?>
<nav class="checkout-steps" aria-label="Étapes de commande">
    <ol class="checkout-steps__list">
        <?php foreach ($etapes as $cle => $etape): ?>
            <?php
            $i = array_search($cle, $ordre, true);
            $classe = 'checkout-steps__item';
            if ($i < $indexCourant) {
                $classe .= ' is-done';
            } elseif ($i === $indexCourant) {
                $classe .= ' is-current';
            }
            $cliquable = $i < $indexCourant;
            ?>
            <li class="<?= $classe ?>">
                <?php if ($cliquable): ?>
                    <a class="checkout-steps__link" href="<?= htmlspecialchars($etape['href']) ?>">
                        <span class="checkout-steps__num" aria-hidden="true"><?= $i + 1 ?></span>
                        <span class="checkout-steps__label"><?= htmlspecialchars($etape['label']) ?></span>
                    </a>
                <?php else: ?>
                    <span class="checkout-steps__link" <?= $i === $indexCourant ? 'aria-current="step"' : '' ?>>
                        <span class="checkout-steps__num" aria-hidden="true"><?= $i + 1 ?></span>
                        <span class="checkout-steps__label"><?= htmlspecialchars($etape['label']) ?></span>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
