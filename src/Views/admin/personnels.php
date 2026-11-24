<?php
ob_start();
$nb = count($personnels ?? []);
?>
<section class="section admin-page">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <header class="admin-page-head">
        <h1>Comptes personnel</h1>
        <p class="muted admin-page-sub">Accès à l’espace personnel (suivi des commandes).</p>
    </header>
    <?php require __DIR__ . '/_nav.php'; ?>

    <div class="admin-toolbar">
        <?= ViewHelper::btnNav(BASE_URL . '/admin/personnel/nouveau', 'Ajouter un compte', 'btn btn-primary') ?>
    </div>

    <p class="admin-filters__count muted" style="margin-top:0">
        <strong><?= $nb ?></strong> compte<?= $nb > 1 ? 's' : '' ?>
    </p>

    <?php if ($nb === 0): ?>
        <p class="empty-state">Aucun compte personnel pour le moment.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>E-mail</th>
                    <th>Téléphone</th>
                    <th>Ville</th>
                    <th scope="col" class="col-visibilite">Actif</th>
                    <th scope="col"><span class="sr-only">Actions</span></th>
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
                        <td class="col-visibilite">
                            <?php if ($actif): ?>
                                <span class="statut-visibilite is-on" title="Compte actif">
                                    <span class="statut-visibilite__icon" aria-hidden="true">✓</span>
                                    <span class="sr-only">Actif</span>
                                </span>
                            <?php else: ?>
                                <span class="statut-visibilite is-off" title="Compte désactivé">
                                    <span class="statut-visibilite__icon" aria-hidden="true">–</span>
                                    <span class="sr-only">Désactivé</span>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="admin-actions">
                            <a href="<?= BASE_URL ?>/admin/personnel/<?= $id ?>">Modifier</a>
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
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
