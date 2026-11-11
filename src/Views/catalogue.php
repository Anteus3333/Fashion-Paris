<?php
ob_start();
?>
<section class="section page-narrow">
    <h1><?= $categorieActive ? htmlspecialchars($categorieActive['libelle']) : 'Catalogue' ?></h1>

    <form class="search-form" method="get" action="<?= BASE_URL ?>/catalogue/recherche" role="search">
        <label for="q">Rechercher un produit</label>
        <div class="search-row">
            <input type="search" id="q" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Robe, blazer…">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </div>
    </form>

    <?php if (!empty($categories)): ?>
        <nav class="cat-nav" aria-label="Catégories">
            <a href="<?= BASE_URL ?>/catalogue" class="<?= empty($categorieActive) ? 'is-active' : '' ?>">Tout</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= BASE_URL ?>/catalogue/<?= htmlspecialchars($cat['slug']) ?>"
                   class="<?= ($categorieActive['slug'] ?? '') === $cat['slug'] ? 'is-active' : '' ?>">
                    <?= htmlspecialchars($cat['libelle']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <?php if (empty($produits)): ?>
        <p class="empty-state">Aucun produit trouvé.</p>
    <?php else: ?>
        <ul class="product-grid">
            <?php foreach ($produits as $p): ?>
                <li class="product-card">
                    <a href="<?= BASE_URL ?>/produit/<?= htmlspecialchars($p['slug']) ?>">
                        <img src="<?= AssetHelper::imageUrl($p['image'] ?? null) ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
                        <h3><?= htmlspecialchars($p['nom']) ?></h3>
                        <p class="muted"><?= htmlspecialchars($p['categorie_libelle'] ?? '') ?></p>
                        <p class="price"><?= ViewHelper::prix($p['prix']) ?></p>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/layout.php';
