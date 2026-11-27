<?php
ob_start();
$series = $series ?? ['labels' => [], 'commandes' => [], 'ca' => []];
$statsSource = $statsSource ?? 'mysql';
$statsErreur = $statsErreur ?? null;
$syncNecessaire = !empty($syncNecessaire);
$aDesSeries = !empty($series['labels']);
?>
<section class="section admin-page">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <header class="admin-page-head">
        <h1>Statistiques</h1>
        <p class="muted admin-page-sub">Performance 12 mois — MongoDB prioritaire, MySQL en secours transparent.</p>
    </header>
    <?php require __DIR__ . '/_nav.php'; ?>

    <div class="admin-toolbar admin-stats-toolbar">
        <p class="muted" style="margin:0">
            Source affichée :
            <strong><?= $statsSource === 'mongodb' ? 'MongoDB' : 'MySQL (secours)' ?></strong>
            <?php if ($mongoOk): ?>
                · Documents Mongo : <strong><?= (int) $nbDocs ?></strong>
                <?php if ($syncNecessaire): ?>
                    · <span class="admin-stats-sync-flag">Sync requise</span>
                <?php endif; ?>
            <?php endif; ?>
        </p>
        <?php if ($mongoOk): ?>
            <form method="post">
                <?= Csrf::champ() ?>
                <button type="submit" name="sync" value="1" class="btn btn-primary<?= $syncNecessaire ? ' btn-sync-needed' : '' ?>">
                    Synchroniser MySQL → Mongo
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($mongoOk && $syncNecessaire): ?>
        <div class="flash-banner flash-banner-alerte" role="alert">
            <p class="flash-banner-text">
                MongoDB n’est plus à jour par rapport à MySQL (nouvelles commandes ou statuts modifiés).
                Les graphiques peuvent être incomplets ou périmés — lancez <strong>Synchroniser MySQL → Mongo</strong>.
            </p>
        </div>
    <?php endif; ?>

    <?php if (!empty($statsErreur)): ?>
        <p class="muted"><?= htmlspecialchars($statsErreur) ?></p>
    <?php endif; ?>

    <h2>Performance (12 mois)</h2>
    <?php if ($statsSource === 'mongodb'): ?>
        <p class="muted">Données issues de Mongo après sync MySQL → Mongo.</p>
    <?php else: ?>
        <p class="muted">Données calculées en direct depuis MySQL (les graphiques restent disponibles sans Mongo).</p>
    <?php endif; ?>

    <?php if (!$aDesSeries): ?>
        <p class="empty-state">Aucune commande à afficher sur la période.</p>
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
</section>
<?php
$contenu = ob_get_clean();
$scriptsFooter = '';
if ($aDesSeries) {
    $scriptsFooter = ''
        . '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>'
        . '<script src="' . BASE_URL . '/js/admin-dashboard.js?v=' . (@filemtime(__DIR__ . '/../../../public/js/admin-dashboard.js') ?: time()) . '" defer></script>';
}
require __DIR__ . '/../layout.php';
