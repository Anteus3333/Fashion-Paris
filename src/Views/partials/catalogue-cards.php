<ul class="product-grid" id="catalogue-list-inner">
    <?php foreach ($produits as $p): ?>
        <li class="product-card">
            <a href="<?= BASE_URL ?>/produit/<?= htmlspecialchars($p['slug']) ?>">
                <img src="<?= AssetHelper::imageUrl($p['image'] ?? null) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" loading="lazy">
                <h3><?= htmlspecialchars($p['nom']) ?></h3>
                <p class="muted"><?= htmlspecialchars($p['categorie_libelle'] ?? '') ?></p>
                <p class="price"><?= ViewHelper::prix($p['prix']) ?></p>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
