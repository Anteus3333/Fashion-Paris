<?php
ob_start();
?>
<section class="legal-hero">
    <h1>Accessibilité</h1>
    <p>Déclaration d’accessibilité du site Fashion Paris.</p>
</section>

<section class="legal-page">
    <article class="legal-content">
        <h2>État de conformité</h2>
        <p>
            Le site <strong>Fashion Paris</strong> est en cours de mise en conformité
            avec le référentiel général d’amélioration de l’accessibilité (RGAA), version 4.
            À ce jour, le site est considéré comme <strong>partiellement conforme</strong>
            compte tenu des non-conformités listées ci-dessous.
        </p>

        <h2>Résultats des tests</h2>
        <p>
            Un audit interne a été réalisé sur les pages principales
            (accueil, catalogue, fiche produit, panier, contact, authentification, compte client et administration).
            Les points suivants sont en place :
        </p>
        <ul>
            <li>Lien d’évitement « Aller au contenu principal »</li>
            <li>Structure sémantique (<code>header</code>, <code>nav</code>, <code>main</code>, <code>footer</code>)</li>
            <li>Menu mobile utilisable au clavier (<code>aria-expanded</code> / <code>aria-controls</code>)</li>
            <li>Messages flash annoncés aux lecteurs d’écran (<code>role="alert"</code> / <code>aria-live</code>)</li>
            <li>Labels associés aux champs de formulaire (<code>for</code> / <code>id</code>)</li>
            <li>Textes alternatifs sur les images produits</li>
            <li>Respect de la préférence « réduire les animations » (<code>prefers-reduced-motion</code>) sur le bandeau d’accueil</li>
        </ul>

        <h2>Non-conformités connues</h2>
        <ul>
            <li>Amélioration continue des contrastes sur certains états de boutons / survols</li>
            <li>Documents PDF (manuel, charte) non publiés sur le site</li>
            <li>Audit RGAA exhaustif par un organisme tiers non encore réalisé</li>
        </ul>

        <h2>Établissement de cette déclaration</h2>
        <p>
            Cette déclaration a été établie le <time datetime="2026-11-14">14 novembre 2026</time>.
            Elle sera mise à jour en fonction des évolutions du site.
        </p>

        <h2>Voies de recours</h2>
        <p>
            Si vous n’arrivez pas à accéder à un contenu ou à un service,
            contactez-nous pour obtenir une assistance :
        </p>
        <ul>
            <li>E-mail : <a href="mailto:contact@anteusweb.com">contact@anteusweb.com</a></li>
            <li>Formulaire : <a href="<?= BASE_URL ?>/contact">page Contact</a></li>
        </ul>
        <p>
            Si vous estimez que la réponse apportée est insuffisante, vous pouvez saisir
            le Défenseur des droits.
        </p>

        <p class="legal-back"><a href="<?= BASE_URL ?>/">← Retour à l’accueil</a></p>
    </article>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
