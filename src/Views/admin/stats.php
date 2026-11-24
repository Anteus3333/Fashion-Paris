<?php
ob_start();
$series = $series ?? ['labels' => [], 'commandes' => [], 'ca' => []];
?>
<section class="section admin-page">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <header class="admin-page-head">
        <h1>Statistiques MongoDB</h1>
        <p class="muted admin-page-sub">Agrégations sur les commandes synchronisées depuis MySQL.</p>
    </header>
    <?php require __DIR__ . '/_nav.php'; ?>

    <?php if (!$mongoOk): ?>
        <p class="form-error">MongoDB non configuré. Copiez <code>config/mongodb.local.example.php</code> vers <code>mongodb.local.php</code> avec votre URI Atlas.</p>
        <p class="muted">Les indicateurs MySQL du tableau de bord restent disponibles sans Mongo.</p>
    <?php else: ?>
        <div class="admin-toolbar admin-stats-toolbar">
            <p class="muted" style="margin:0">Documents synchronisés : <strong><?= (int) $nbDocs ?></strong></p>
            <form method="post">
                <?= Csrf::champ() ?>
                <button type="submit" name="sync" value="1" class="btn btn-primary">Synchroniser MySQL → Mongo</button>
            </form>
        </div>

        <h2>Performance (12 mois)</h2>
        <p class="muted">Données issues de Mongo après sync MySQL → Mongo.</p>
        <?php if ($nbDocs === 0): ?>
            <p class="empty-state">Aucune donnée Mongo — lancez une synchronisation.</p>
        <?php else: ?>
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
        <?php endif; ?>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
$scriptsFooter = '';
if (!empty($mongoOk) && (int) ($nbDocs ?? 0) > 0) {
    $scriptsFooter = ''
        . '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>'
        . '<script src="' . BASE_URL . '/js/admin-dashboard.js?v=' . (@filemtime(__DIR__ . '/../../../public/js/admin-dashboard.js') ?: time()) . '" defer></script>';
}
require __DIR__ . '/../layout.php';
