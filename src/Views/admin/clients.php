<?php
ob_start();
$nb = count($clients ?? []);
$libelleRole = static function (?string $role): string {
    return match ($role) {
        'administrateur' => 'Administrateur',
        'personnel' => 'Personnel',
        'utilisateur' => 'Client',
        default => (string) ($role ?? ''),
    };
};
?>
<section class="section admin-page">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <header class="admin-page-head">
        <h1>Clients</h1>
    </header>
    <?php require __DIR__ . '/_nav.php'; ?>

    <p class="admin-filters__count muted" style="margin-top:0">
        <strong><?= $nb ?></strong> compte<?= $nb > 1 ? 's' : '' ?>
    </p>

    <?php if ($nb === 0): ?>
        <p class="empty-state">Aucun client pour le moment.</p>
    <?php else: ?>
        <ul class="admin-clients-list">
            <?php foreach ($clients as $c): ?>
                <li class="admin-client-card">
                    <div class="admin-client-card__head">
                        <span class="admin-client-card__name"><?= htmlspecialchars(ViewHelper::formatNomComplet($c['prenom'], $c['nom'])) ?></span>
                        <span class="admin-client-card__role"><?= htmlspecialchars($libelleRole($c['role'] ?? '')) ?></span>
                    </div>
                    <dl class="admin-client-card__meta">
                        <div class="admin-client-card__email">
                            <dt>E-mail</dt>
                            <dd><?= htmlspecialchars($c['email']) ?></dd>
                        </div>
                        <div>
                            <dt>Ville</dt>
                            <dd><?= htmlspecialchars(($c['ville'] ?? '') !== '' ? $c['ville'] : '—') ?></dd>
                        </div>
                    </dl>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="admin-clients-table-wrap">
            <table class="data-table admin-clients-table">
                <thead>
                    <tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Ville</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars(ViewHelper::formatNomComplet($c['prenom'], $c['nom'])) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($libelleRole($c['role'] ?? '')) ?></td>
                            <td><?= htmlspecialchars(($c['ville'] ?? '') !== '' ? $c['ville'] : '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
