<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <h1>Commandes</h1>
    <nav class="cat-nav" aria-label="Filtrer par statut">
        <a href="<?= BASE_URL ?>/admin/commandes" class="<?= $statut === '' ? 'is-active' : '' ?>">Toutes</a>
        <?php foreach ($config['statuts'] as $code => $libelle): ?>
            <a href="<?= BASE_URL ?>/admin/commandes?statut=<?= urlencode($code) ?>"
               class="<?= $statut === $code ? 'is-active' : '' ?>"><?= htmlspecialchars($libelle) ?></a>
        <?php endforeach; ?>
    </nav>
    <?php if (empty($commandes)): ?>
        <p class="muted">Aucune commande.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr><th>N°</th><th>Client</th><th>Date</th><th>Statut</th><th>Total</th></tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $c): ?>
                    <tr>
                        <td><a href="<?= BASE_URL ?>/admin/commande/<?= htmlspecialchars($c['numero_commande']) ?>"><?= htmlspecialchars($c['numero_commande']) ?></a></td>
                        <td><?= htmlspecialchars(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($c['date_commande']) ?></td>
                        <td><?= htmlspecialchars($config['statuts'][$c['statut']] ?? $c['statut']) ?></td>
                        <td><?= ViewHelper::prix($c['montant_total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
