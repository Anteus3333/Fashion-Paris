<?php
ob_start();
?>
<section class="section admin-page">
    <h1>Administration</h1>
    <?php require __DIR__ . '/_nav.php'; ?>

    <ul class="stats-grid">
        <li><strong><?= (int) $stats['nb_produits'] ?></strong><span>Produits actifs</span></li>
        <li><strong><?= (int) $stats['nb_commandes'] ?></strong><span>Commandes</span></li>
        <li><strong><?= ViewHelper::prix($stats['ca_total']) ?></strong><span>CA (hors annulées)</span></li>
        <li><strong><?= (int) $stats['nb_clients'] ?></strong><span>Clients</span></li>
        <li><strong><?= (int) $stats['ruptures'] ?></strong><span>Ruptures stock</span></li>
    </ul>

    <h2>Dernières commandes</h2>
    <?php if (empty($commandesRecentes)): ?>
        <p class="muted">Aucune commande.</p>
    <?php else: ?>
        <ul class="order-list">
            <?php foreach ($commandesRecentes as $c): ?>
                <li>
                    <a href="<?= BASE_URL ?>/admin/commande/<?= htmlspecialchars($c['numero_commande']) ?>">
                        <?= htmlspecialchars($c['numero_commande']) ?>
                    </a>
                    — <?= htmlspecialchars($config['statuts'][$c['statut']] ?? $c['statut']) ?>
                    — <?= ViewHelper::prix($c['montant_total']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
