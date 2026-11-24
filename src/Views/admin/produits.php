<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <h1>Produits</h1>
    <?php require __DIR__ . '/_nav.php'; ?>
    <p><?= ViewHelper::btnNav(BASE_URL . '/admin/produit/nouveau', 'Ajouter un produit', 'btn btn-primary') ?></p>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Stock</th>
                <th scope="col" class="col-visibilite">Visible</th>
                <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $p): ?>
                <?php $estActif = (int) $p['actif'] === 1; ?>
                <tr class="<?= (int) $p['stock'] <= 0 ? 'is-warning' : '' ?>">
                    <td>
                        <?= htmlspecialchars($p['nom']) ?>
                        <?php if (!empty($p['mis_en_avant'])): ?>
                            <span class="tag-vedette" title="Mis en avant sur l’accueil">Vedette</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['categorie_libelle']) ?></td>
                    <td><?= ViewHelper::prix($p['prix']) ?></td>
                    <td><?= (int) $p['stock'] ?></td>
                    <td class="col-visibilite">
                        <?php if ($estActif): ?>
                            <span class="statut-visibilite is-on" title="Visible dans le catalogue">
                                <span class="statut-visibilite__icon" aria-hidden="true">✓</span>
                                <span class="sr-only">Visible</span>
                            </span>
                        <?php else: ?>
                            <span class="statut-visibilite is-off" title="Masqué du catalogue">
                                <span class="statut-visibilite__icon" aria-hidden="true">–</span>
                                <span class="sr-only">Masqué</span>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td><a href="<?= BASE_URL ?>/admin/produit/<?= (int) $p['produit_id'] ?>">Modifier</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
