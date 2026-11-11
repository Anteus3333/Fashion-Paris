<?php
ob_start();
$config = $config ?? require __DIR__ . '/../../../config/commandeconfig.php';
?>
<section class="section page-narrow">
    <h1>Mon compte</h1>
    <p>Bonjour <?= htmlspecialchars(ViewHelper::formatNomComplet($utilisateur['prenom'] ?? '', $utilisateur['nom'] ?? '')) ?>.</p>
    <p><a href="<?= BASE_URL ?>/mon-compte/profil">Modifier mon profil</a></p>

    <h2>Mes commandes</h2>
    <?php if (empty($commandes)): ?>
        <p class="empty-state">Aucune commande pour le moment.</p>
        <p><?= ViewHelper::btnNav(BASE_URL . '/catalogue', 'Voir le catalogue', 'btn btn-primary') ?></p>
    <?php else: ?>
        <ul class="order-list">
            <?php foreach ($commandes as $c): ?>
                <li>
                    <a href="<?= BASE_URL ?>/mon-compte/commande/<?= htmlspecialchars($c['numero_commande']) ?>">
                        <strong><?= htmlspecialchars($c['numero_commande']) ?></strong>
                    </a>
                    — <?= htmlspecialchars($config['statuts'][$c['statut']] ?? $c['statut']) ?>
                    — <?= ViewHelper::prix($c['montant_total']) ?>
                    <span class="muted">(<?= htmlspecialchars($c['date_commande']) ?>)</span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
