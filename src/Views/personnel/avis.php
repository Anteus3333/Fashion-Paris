<?php
ob_start();
$gestionBase = BASE_URL . '/espace-personnel';
$labelsAvis = [
    'en_attente' => 'En attente',
    'valide'     => 'Publié',
    'refuse'     => 'Refusé',
];
?>
<section class="section page-narrow">
    <h1>Modération des avis</h1>
    <p class="section-intro">Validez ou refusez les avis clients avant publication sur l’accueil.</p>
    <?php require __DIR__ . '/_nav.php'; ?>

    <nav class="cat-nav" aria-label="Filtrer les avis">
        <a href="<?= htmlspecialchars($gestionBase) ?>/avis?statut=en_attente" class="<?= ($filtreActif ?? '') === 'en_attente' ? 'is-active' : '' ?>">
            En attente (<?= (int) $nbAttente ?>)
        </a>
        <a href="<?= htmlspecialchars($gestionBase) ?>/avis?statut=valide" class="<?= ($filtreActif ?? '') === 'valide' ? 'is-active' : '' ?>">Publiés</a>
        <a href="<?= htmlspecialchars($gestionBase) ?>/avis?statut=refuse" class="<?= ($filtreActif ?? '') === 'refuse' ? 'is-active' : '' ?>">Refusés</a>
        <a href="<?= htmlspecialchars($gestionBase) ?>/avis?statut=tous" class="<?= ($filtreActif ?? '') === 'tous' ? 'is-active' : '' ?>">Tous</a>
    </nav>

    <?php if (empty($avis)): ?>
        <p class="muted">Aucun avis pour ce filtre.</p>
    <?php else: ?>
        <ul class="personnel-avis-list">
            <?php foreach ($avis as $a): ?>
                <li class="personnel-avis-card">
                    <div class="personnel-avis-head">
                        <strong><?= htmlspecialchars(ViewHelper::formatNomComplet($a['prenom'] ?? '', $a['nom'] ?? '')) ?></strong>
                        <span><?= (int) $a['note'] ?>/5</span>
                        <span class="muted"><?= htmlspecialchars($labelsAvis[$a['statut']] ?? $a['statut']) ?></span>
                    </div>
                    <p>« <?= htmlspecialchars($a['commentaire'] ?? '') ?> »</p>
                    <p class="muted">
                        <?= htmlspecialchars($a['email'] ?? '') ?>
                        — <?= htmlspecialchars($a['produit_nom'] ?? '') ?>
                    </p>
                    <?php if (($a['statut'] ?? '') === 'en_attente'): ?>
                        <div class="inline-actions">
                            <form method="post" action="<?= htmlspecialchars($gestionBase) ?>/avis/<?= (int) $a['avis_id'] ?>/valider" class="inline-form">
                                <?= Csrf::champ() ?>
                                <button type="submit" class="btn btn-primary">Publier</button>
                            </form>
                            <form method="post" action="<?= htmlspecialchars($gestionBase) ?>/avis/<?= (int) $a['avis_id'] ?>/refuser" class="inline-form" data-confirm="Refuser cet avis ?">
                                <?= Csrf::champ() ?>
                                <button type="submit" class="btn btn-ghost">Refuser</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
