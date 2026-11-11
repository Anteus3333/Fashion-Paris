<?php
ob_start();
?>
<section class="hero">
    <div class="hero-copy">
        <p class="brand-mark">Fashion Paris</p>
        <h1>La mode parisienne, livrée chez vous</h1>
        <p class="lede">Catalogue, panier et compte client — e-commerce sécurisé pour le dossier projet.</p>
        <div class="hero-actions">
            <?= ViewHelper::btnNav(BASE_URL . '/catalogue', 'Voir le catalogue', 'btn btn-primary') ?>
            <?= ViewHelper::btnNav(BASE_URL . '/login', 'Se connecter', 'btn btn-ghost') ?>
        </div>
    </div>
</section>

<section class="section">
    <h2>Sélection du moment</h2>
    <p class="section-intro">Produits mis en avant (données MySQL via Model → Repository).</p>
    <?php if (empty($produitsVedette)): ?>
        <p class="empty-state">Aucun produit en vedette pour le moment. Lancez Docker pour charger les données de test.</p>
    <?php else: ?>
        <ul class="product-grid">
            <?php foreach ($produitsVedette as $p): ?>
                <li class="product-card">
                    <a href="<?= BASE_URL ?>/produit/<?= htmlspecialchars($p['slug']) ?>">
                        <img src="<?= AssetHelper::imageUrl($p['image'] ?? null) ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
                        <h3><?= htmlspecialchars($p['nom']) ?></h3>
                        <p class="price"><?= ViewHelper::prix($p['prix']) ?></p>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="section page-narrow newsletter">
    <h2>Newsletter</h2>
    <p class="section-intro">Recevez les nouveautés et promotions Fashion Paris.</p>
    <form method="post" action="<?= BASE_URL ?>/newsletter/inscrire" class="search-row">
        <?= Csrf::champ() ?>
        <label class="sr-only" for="newsletter-email">E-mail</label>
        <input type="email" id="newsletter-email" name="email" required placeholder="vous@exemple.fr">
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/layout.php';
