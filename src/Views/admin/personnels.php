<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <h1>Comptes personnel</h1>
    <?php require __DIR__ . '/_nav.php'; ?>
    <p class="section-intro">Gérez les accès à l’espace personnel (commandes).</p>
    <p><?= ViewHelper::btnNav(BASE_URL . '/admin/personnel/nouveau', 'Ajouter un compte', 'btn btn-primary') ?></p>

    <?php if (empty($personnels)): ?>
        <p class="muted">Aucun compte personnel.</p>
    <?php else: ?>
        <div class="commandes-table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>E-mail</th>
                        <th>Téléphone</th>
                        <th>Ville</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($personnels as $p): ?>
                        <?php
                        $actif = (int) ($p['actif'] ?? 1) === 1;
                        $id = (int) $p['utilisateur_id'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars(ViewHelper::formatNomComplet($p['prenom'] ?? '', $p['nom'] ?? '')) ?></td>
                            <td><a href="mailto:<?= htmlspecialchars($p['email']) ?>"><?= htmlspecialchars($p['email']) ?></a></td>
                            <td><?= htmlspecialchars(($p['telephone'] ?? '') !== '' ? $p['telephone'] : '—') ?></td>
                            <td><?= htmlspecialchars(($p['ville'] ?? '') !== '' ? $p['ville'] : '—') ?></td>
                            <td><?= $actif ? 'Actif' : 'Désactivé' ?></td>
                            <td class="admin-actions">
                                <?= ViewHelper::btnNav(BASE_URL . '/admin/personnel/' . $id, 'Modifier', 'btn btn-ghost') ?>
                                <?php if ($actif): ?>
                                    <form method="post" action="<?= BASE_URL ?>/admin/personnel/<?= $id ?>/desactiver" class="inline-form" data-confirm="Désactiver ce compte personnel ?">
                                        <?= Csrf::champ() ?>
                                        <button type="submit" class="btn btn-ghost">Désactiver</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="<?= BASE_URL ?>/admin/personnel/<?= $id ?>/activer" class="inline-form">
                                        <?= Csrf::champ() ?>
                                        <button type="submit" class="btn btn-ghost">Réactiver</button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" action="<?= BASE_URL ?>/admin/personnel/<?= $id ?>/supprimer" class="inline-form" data-confirm="Supprimer définitivement ce compte ?">
                                    <?= Csrf::champ() ?>
                                    <button type="submit" class="btn btn-ghost">Supprimer</button>
                                </form>
                            </td>
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
