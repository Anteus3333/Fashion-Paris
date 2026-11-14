<?php
ob_start();
?>
<section class="legal-hero">
    <h1>Mentions légales</h1>
    <p>Informations légales relatives au site Fashion Paris.</p>
</section>

<section class="legal-page">
    <article class="legal-content">
        <h2>Éditeur du site</h2>
        <p>
            <strong>Fashion Paris</strong><br>
            Boutique en ligne de prêt-à-porter<br>
            Site : <a href="https://fashion.anteusweb.com">fashion.anteusweb.com</a><br>
            E-mail : <a href="mailto:contact@anteusweb.com">contact@anteusweb.com</a>
        </p>

        <h2>Directeur de la publication</h2>
        <p>Le responsable de la publication du site Fashion Paris.</p>

        <h2>Hébergement</h2>
        <p>
            Le site est hébergé par :<br>
            <strong>Infomaniak Network SA</strong><br>
            Rue Eugène-Marziano 25, 1227 Les Acacias (Genève), Suisse<br>
            Téléphone : +41 22 820 35 00<br>
            Site : <a href="https://www.infomaniak.com" rel="noopener noreferrer">www.infomaniak.com</a>
        </p>

        <h2>Conception et réalisation</h2>
        <p>
            Le site a été conçu et développé dans le cadre d’un dossier projet
            (architecture MVC maison, PHP / MySQL / MongoDB).
        </p>

        <h2>Propriété intellectuelle</h2>
        <p>
            L’ensemble des contenus présents sur ce site (textes, visuels, logos, structure)
            est la propriété de Fashion Paris, sauf mention contraire.
            Toute reproduction, même partielle, est interdite sans autorisation écrite préalable.
        </p>

        <h2>Données personnelles</h2>
        <p>
            Les données collectées lors de la création de compte, de la commande, de l’inscription
            newsletter ou du formulaire de contact sont traitées conformément au RGPD.
            Pour le détail (finalités, durées, destinataires et droits), consultez notre
            <a href="<?= BASE_URL ?>/politique-confidentialite">politique de confidentialité</a>.
        </p>

        <h2>Cookies</h2>
        <p>
            Le site utilise des cookies de session strictement nécessaires au fonctionnement
            de l’authentification, du panier et à la sécurité des formulaires (jeton CSRF).
            Aucun cookie publicitaire n’est déposé. Détails dans la
            <a href="<?= BASE_URL ?>/politique-confidentialite">politique de confidentialité</a>.
        </p>

        <h2>Limitation de responsabilité</h2>
        <p>
            Fashion Paris s’efforce d’assurer l’exactitude des informations publiées
            (descriptions, prix, stocks). Toutefois, l’éditeur ne saurait être tenu responsable
            des erreurs ou omissions, ni des dommages résultant de l’utilisation du site.
        </p>

        <p class="legal-back"><a href="<?= BASE_URL ?>/">← Retour à l’accueil</a></p>
    </article>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
