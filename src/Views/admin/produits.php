<?php
ob_start();
$filtres = $filtres ?? ['q' => '', 'categorie_id' => null, 'actif' => '', 'vedette' => ''];
$nb = count($produits ?? []);
$filtreActif = ($filtres['q'] ?? '') !== ''
    || ($filtres['categorie_id'] ?? null) !== null
    || ($filtres['actif'] ?? '') !== ''
    || ($filtres['vedette'] ?? '') !== '';
?>
<section class="section admin-page">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <header class="admin-page-head">
        <h1>Produits</h1>
    </header>
    <?php require __DIR__ . '/_nav.php'; ?>

    <div class="admin-toolbar">
        <?= ViewHelper::btnNav(BASE_URL . '/admin/produit/nouveau', 'Ajouter un produit', 'btn btn-primary') ?>
    </div>

    <form method="get" action="<?= BASE_URL ?>/admin/produits" class="admin-filters" role="search">
        <div class="admin-filters__grid">
            <div class="admin-filters__field admin-filters__field--search">
                <label for="admin-q">Recherche</label>
                <input type="search" id="admin-q" name="q"
                       value="<?= htmlspecialchars($filtres['q'] ?? '') ?>"
                       placeholder="Nom, description…"
                       autocomplete="off" enterkeyhint="search">
            </div>
            <div class="admin-filters__field">
                <label for="admin-categorie">Catégorie</label>
                <select id="admin-categorie" name="categorie_id">
                    <option value="">Toutes</option>
                    <?php foreach ($categories ?? [] as $cat): ?>
                        <option value="<?= (int) $cat['categorie_id'] ?>"
                            <?= (int) ($filtres['categorie_id'] ?? 0) === (int) $cat['categorie_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="admin-filters__field">
                <label for="admin-actif">Visibilité</label>
                <select id="admin-actif" name="actif">
                    <option value="" <?= ($filtres['actif'] ?? '') === '' ? 'selected' : '' ?>>Toutes</option>
                    <option value="1" <?= ($filtres['actif'] ?? '') === '1' ? 'selected' : '' ?>>Visibles</option>
                    <option value="0" <?= ($filtres['actif'] ?? '') === '0' ? 'selected' : '' ?>>Masqués</option>
                </select>
            </div>
            <div class="admin-filters__field">
                <label for="admin-vedette">Vedette</label>
                <select id="admin-vedette" name="vedette">
                    <option value="" <?= ($filtres['vedette'] ?? '') === '' ? 'selected' : '' ?>>Toutes</option>
                    <option value="1" <?= ($filtres['vedette'] ?? '') === '1' ? 'selected' : '' ?>>Mises en avant</option>
                    <option value="0" <?= ($filtres['vedette'] ?? '') === '0' ? 'selected' : '' ?>>Hors accueil</option>
                </select>
            </div>
            <div class="admin-filters__actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <?php if ($filtreActif): ?>
                    <a class="btn btn-secondary" href="<?= BASE_URL ?>/admin/produits">Réinitialiser</a>
                <?php endif; ?>
            </div>
        </div>
        <p class="admin-filters__count muted">
            <strong><?= $nb ?></strong> produit<?= $nb > 1 ? 's' : '' ?>
        </p>
    </form>

    <?php if ($nb === 0): ?>
        <p class="empty-state">Aucun produit ne correspond à ces critères.</p>
    <?php else: ?>
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
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
