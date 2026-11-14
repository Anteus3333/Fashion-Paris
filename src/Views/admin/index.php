<?php
ob_start();
?>
<section class="section page-narrow">
    <h1>Administration</h1>
    <?php require __DIR__ . '/_nav.php'; ?>

    <ul class="stats-grid">
        <li><strong><?= (int) $stats['nb_produits'] ?></strong><span>Produits actifs</span></li>
        <li><strong><?= (int) $stats['nb_commandes'] ?></strong><span>Commandes</span></li>
        <li><strong><?= ViewHelper::prix($stats['ca_total']) ?></strong><span>CA (hors annulées)</span></li>
        <li><strong><?= (int) $stats['nb_clients'] ?></strong><span>Clients</span></li>
        <li><strong><?= (int) $stats['ruptures'] ?></strong><span>Ruptures stock</span></li>
    </ul>

    <h2>Performance (12 mois)</h2>
    <script type="application/json" id="admin-stats-data"><?= json_encode($series, JSON_UNESCAPED_UNICODE) ?></script>
    <div class="admin-charts">
        <div class="admin-chart-card">
            <h3>Commandes</h3>
            <div class="admin-chart-wrap"><canvas id="chart-commandes" aria-label="Graphique commandes"></canvas></div>
        </div>
        <div class="admin-chart-card">
            <h3>Chiffre d'affaires</h3>
            <div class="admin-chart-wrap"><canvas id="chart-ca" aria-label="Graphique CA"></canvas></div>
        </div>
    </div>

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
$scriptsFooter = ''
    . '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>'
    . '<script src="' . BASE_URL . '/js/admin-dashboard.js?v=' . (@filemtime(__DIR__ . '/../../../public/js/admin-dashboard.js') ?: time()) . '" defer></script>';
require __DIR__ . '/../layout.php';
