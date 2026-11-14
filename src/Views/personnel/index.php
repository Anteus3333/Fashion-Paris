<?php
ob_start();
$prenom = $_SESSION['utilisateur']['prenom'] ?? '';
?>
<section class="section page-narrow">
    <h1>Espace personnel</h1>
    <p class="section-intro">
        Bienvenue<?= $prenom !== '' ? ' ' . htmlspecialchars($prenom) : '' ?> —
        gérez les commandes en cours et la modération des avis clients.
    </p>
    <?php require __DIR__ . '/_nav.php'; ?>

    <div class="personnel-kpis">
        <p><strong><?= (int) $enAttente ?></strong> commande(s) en attente</p>
        <p><strong><?= (int) $enCoursNb ?></strong> commande(s) en cours</p>
        <p><strong><?= (int) $avisAttente ?></strong> avis à modérer</p>
    </div>

    <h2>Commandes à traiter</h2>
    <?php if (empty($commandesRecentes)): ?>
        <p class="muted">Aucune commande en attente.</p>
    <?php else: ?>
        <ul class="order-list">
            <?php foreach ($commandesRecentes as $c): ?>
                <li>
                    <a href="<?= BASE_URL ?>/espace-personnel/commande/<?= htmlspecialchars($c['numero_commande']) ?>">
                        <?= htmlspecialchars($c['numero_commande']) ?>
                    </a>
                    — <?= htmlspecialchars(trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? ''))) ?>
                    — <?= ViewHelper::prix($c['montant_total']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p><?= ViewHelper::btnNav(BASE_URL . '/espace-personnel/commandes?statut=en_attente', 'Voir les commandes', 'btn btn-ghost') ?></p>
    <?php endif; ?>

    <h2>Avis en attente</h2>
    <?php if (empty($avisRecents)): ?>
        <p class="muted">Aucun avis à modérer.</p>
    <?php else: ?>
        <ul class="order-list">
            <?php foreach ($avisRecents as $a): ?>
                <li>
                    <strong><?= (int) $a['note'] ?>/5</strong>
                    « <?= htmlspecialchars(mb_strimwidth((string) ($a['commentaire'] ?? ''), 0, 80, '…')) ?> »
                    — <?= htmlspecialchars($a['prenom'] ?? '') ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p><?= ViewHelper::btnNav(BASE_URL . '/espace-personnel/avis', 'Modérer les avis', 'btn btn-ghost') ?></p>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
