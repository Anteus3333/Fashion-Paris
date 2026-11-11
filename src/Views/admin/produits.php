<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <h1>Produits</h1>
    <p><?= ViewHelper::btnNav(BASE_URL . '/admin/produit/nouveau', 'Ajouter un produit', 'btn btn-primary') ?></p>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Actif</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $p): ?>
                <tr class="<?= (int) $p['stock'] <= 0 ? 'is-warning' : '' ?>">
                    <td><?= htmlspecialchars($p['nom']) ?></td>
                    <td><?= htmlspecialchars($p['categorie_libelle']) ?></td>
                    <td><?= ViewHelper::prix($p['prix']) ?></td>
                    <td><?= (int) $p['stock'] ?></td>
                    <td><?= (int) $p['actif'] === 1 ? 'Oui' : 'Non' ?></td>
                    <td><a href="<?= BASE_URL ?>/admin/produit/<?= (int) $p['produit_id'] ?>">Modifier</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
