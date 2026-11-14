<?php
ob_start();
$bannerUrl = AssetHelper::imageUrl('hero-fashion-paris.jpg');
?>
<section class="hero home-hero" style="--hero-image: url('<?= htmlspecialchars($bannerUrl) ?>')">
    <div class="hero-copy">
        <p class="brand-mark">Fashion Paris</p>
        <h1>La mode parisienne, livrée chez vous</h1>
        <p class="lede">Une boutique en ligne soignée, de Paris à votre porte. Coupes nettes, matières soignées, pièces qui traversent la saison : Fashion Paris concentre l'essentiel.</p>
        <div class="hero-actions">
            <?= ViewHelper::btnNav(BASE_URL . '/catalogue', 'Explorer le catalogue', 'btn btn-primary') ?>
            <?= ViewHelper::btnNav(BASE_URL . '/login', 'Se connecter', 'btn btn-hero-ghost') ?>
        </div>
    </div>
</section>

<?php if (!empty($categories)): ?>
<section class="home-collections" aria-labelledby="collections-titre">
    <div class="home-section-head">
        <h2 id="collections-titre">Collections</h2>
        <p class="section-intro">Entrez par univers — femme, homme ou accessoires.</p>
    </div>
    <ul class="home-collections-grid">
        <?php foreach ($categories as $i => $cat): ?>
            <li>
                <a class="home-collection home-collection--<?= (int) $i % 3 ?>"
                   href="<?= BASE_URL ?>/catalogue/<?= htmlspecialchars($cat['slug']) ?>">
                    <span class="home-collection-label"><?= htmlspecialchars($cat['libelle']) ?></span>
                    <span class="home-collection-cta">Voir</span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>

<section class="section home-featured" aria-labelledby="vedettes-titre">
    <div class="home-section-head">
        <h2 id="vedettes-titre">Sélection du moment</h2>
        <p class="section-intro">Pièces mises en avant cette saison.</p>
    </div>
    <?php if (empty($produitsVedette)): ?>
        <p class="empty-state">Aucun produit en vedette pour le moment.</p>
    <?php else: ?>
        <ul class="product-grid product-grid--featured">
            <?php foreach ($produitsVedette as $p): ?>
                <li class="product-card product-card--featured">
                    <a href="<?= BASE_URL ?>/produit/<?= htmlspecialchars($p['slug']) ?>">
                        <img src="<?= AssetHelper::imageUrl($p['image'] ?? null) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" loading="lazy">
                        <h3><?= htmlspecialchars($p['nom']) ?></h3>
                        <p class="price"><?= ViewHelper::prix($p['prix']) ?></p>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="avis-clients" aria-labelledby="avis-clients-titre">
    <div class="avis-clients-inner">
        <h2 id="avis-clients-titre">Ce que pensent nos clients</h2>
        <div class="avis-grid">
            <?php if (!empty($listeAvis)): ?>
                <?php foreach ($listeAvis as $avis): ?>
                    <article class="avis-card">
                        <p class="note">Note : <?= (int) $avis['note'] ?> / 5</p>
                        <p class="commentaire">"<?= htmlspecialchars($avis['commentaire'] ?? '') ?>"</p>
                        <p class="auteur">— <?= htmlspecialchars(ViewHelper::formatPrenom($avis['prenom'] ?? '')) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="muted">Aucun avis pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="home-newsletter" aria-labelledby="newsletter-titre">
    <div class="home-newsletter-inner">
        <h2 id="newsletter-titre">Newsletter</h2>
        <p class="section-intro">Recevez les nouveautés et promotions Fashion Paris.</p>
        <form method="post" action="<?= BASE_URL ?>/newsletter/inscrire" class="search-row home-newsletter-form">
            <?= Csrf::champ() ?>
            <label class="sr-only" for="newsletter-email">E-mail</label>
            <input type="email" id="newsletter-email" name="email" required placeholder="vous@exemple.fr">
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
    </div>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/layout.php';
