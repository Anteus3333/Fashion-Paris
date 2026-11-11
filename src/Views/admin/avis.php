<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <h1>Modération des avis</h1>
    <?php if (empty($avis)): ?>
        <p class="muted">Aucun avis en attente.</p>
    <?php else: ?>
        <ul class="order-list">
            <?php foreach ($avis as $a): ?>
                <li>
                    <strong><?= (int) $a['note'] ?>/5</strong>
                    sur <a href="<?= BASE_URL ?>/produit/<?= htmlspecialchars($a['produit_slug']) ?>"><?= htmlspecialchars($a['produit_nom']) ?></a>
                    par <?= htmlspecialchars($a['prenom']) ?>
                    <p><?= nl2br(htmlspecialchars($a['commentaire'] ?? '')) ?></p>
                    <form method="post" class="inline-actions">
                        <?= Csrf::champ() ?>
                        <input type="hidden" name="avis_id" value="<?= (int) $a['avis_id'] ?>">
                        <button type="submit" name="action" value="valide" class="btn btn-primary">Valider</button>
                        <button type="submit" name="action" value="refuse" class="btn btn-ghost">Refuser</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
