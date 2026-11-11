<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <h1>Statistiques MongoDB</h1>
    <?php if (!$mongoOk): ?>
        <p class="form-error">MongoDB non configuré. Copiez <code>config/mongodb.local.example.php</code> vers <code>mongodb.local.php</code> avec votre URI Atlas.</p>
        <p class="muted">Les stats MySQL du tableau de bord restent disponibles sans Mongo.</p>
    <?php else: ?>
        <p>Documents synchronisés : <strong><?= (int) $nbDocs ?></strong></p>
        <form method="post">
            <?= Csrf::champ() ?>
            <button type="submit" name="sync" value="1" class="btn btn-primary">Synchroniser MySQL → Mongo</button>
        </form>
        <h2>CA par ville (agrégation Mongo)</h2>
        <?php if (empty($parVille)): ?>
            <p class="muted">Aucune donnée — lancez une sync après des commandes.</p>
        <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Ville</th><th>Commandes</th><th>CA</th></tr></thead>
                <tbody>
                    <?php foreach ($parVille as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) ($row['_id'] ?? '')) ?></td>
                            <td><?= (int) ($row['nb'] ?? 0) ?></td>
                            <td><?= ViewHelper::prix($row['ca'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
