<?php
ob_start();
?>
<section class="section page-narrow catalogue-page">
    <h1><?= $categorieActive ? htmlspecialchars($categorieActive['libelle']) : 'Catalogue' ?></h1>

    <form id="filter-form" class="catalogue-filters" data-base-url="<?= htmlspecialchars(BASE_URL) ?>" role="search">
        <div class="search-row">
            <label class="sr-only" for="q">Rechercher</label>
            <input type="search" id="q" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Robe, blazer…">
            <label class="sr-only" for="categorie">Catégorie</label>
            <select id="categorie" name="categorie" class="form-select">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['slug']) ?>"
                        <?= ($categorieActive['slug'] ?? '') === $cat['slug'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="search-row catalogue-prix">
            <label for="prix_min">Prix min</label>
            <input type="number" id="prix_min" name="prix_min" min="0" step="1" placeholder="0">
            <label for="prix_max">Prix max</label>
            <input type="number" id="prix_max" name="prix_max" min="0" step="1" placeholder="500">
            <button type="button" id="apply-filters" class="btn btn-primary">Filtrer</button>
            <button type="button" id="reset-filters" class="btn btn-ghost">Reset</button>
        </div>
        <p class="muted">Résultats : <strong id="catalogue-count"><?= count($produits ?? []) ?></strong></p>
    </form>

    <noscript>
        <form class="search-form" method="get" action="<?= BASE_URL ?>/catalogue/recherche" role="search">
            <label for="q-noscript">Rechercher un produit</label>
            <div class="search-row">
                <input type="search" id="q-noscript" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Robe, blazer…">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>
        <?php if (!empty($categories)): ?>
            <nav class="cat-nav" aria-label="Catégories">
                <a href="<?= BASE_URL ?>/catalogue">Tout</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= BASE_URL ?>/catalogue/<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['libelle']) ?></a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>
    </noscript>

    <div id="catalogue-list">
        <?php if (empty($produits)): ?>
            <?php require __DIR__ . '/partials/catalogue-empty.php'; ?>
        <?php else: ?>
            <?php require __DIR__ . '/partials/catalogue-cards.php'; ?>
        <?php endif; ?>
    </div>
</section>
<?php
$contenu = ob_get_clean();
$scriptsFooter = '<script src="' . BASE_URL . '/js/catalogue.js?v=' . (@filemtime(__DIR__ . '/../../public/js/catalogue.js') ?: time()) . '" defer></script>';
require __DIR__ . '/layout.php';
