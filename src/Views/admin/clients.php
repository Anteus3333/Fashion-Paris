<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <h1>Clients</h1>
    <table class="data-table">
        <thead>
            <tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Ville</th></tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $c): ?>
                <tr>
                    <td><?= htmlspecialchars(ViewHelper::formatNomComplet($c['prenom'], $c['nom'])) ?></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= htmlspecialchars($c['role'] ?? '') ?></td>
                    <td><?= htmlspecialchars($c['ville'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
