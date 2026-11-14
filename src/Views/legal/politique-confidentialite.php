<?php
ob_start();
?>
<section class="legal-hero">
    <h1>Politique de confidentialité</h1>
    <p>Informations sur le traitement de vos données personnelles (RGPD).</p>
</section>

<section class="legal-page">
    <article class="legal-content">
        <h2>1. Responsable du traitement</h2>
        <p>
            Le responsable du traitement des données personnelles est :<br>
            <?php $boutique = $boutique ?? require __DIR__ . '/../../../config/boutique.php'; ?>
            <strong><?= htmlspecialchars($boutique['nom']) ?></strong><br>
            <?= htmlspecialchars($boutique['adresse']) ?>,
            <?= htmlspecialchars($boutique['code_postal']) ?> <?= htmlspecialchars($boutique['ville']) ?><br>

            E-mail : <a href="mailto:<?= htmlspecialchars($boutique['email']) ?>"><?= htmlspecialchars($boutique['email']) ?></a><br>
            Site : <a href="<?= htmlspecialchars($boutique['site']) ?>"><?= htmlspecialchars(preg_replace('#^https?://#', '', $boutique['site'])) ?></a>
        </p>

        <h2>2. Données collectées</h2>
        <p>Selon votre utilisation du site, nous pouvons collecter :</p>
        <ul>
            <li><strong>Compte client</strong> : e-mail, mot de passe hashé (bcrypt), identité, téléphone et adresse postale</li>
            <li><strong>Commande</strong> : articles, montants, adresse de livraison, statut et historique de suivi</li>
            <li><strong>Contact</strong> : nom, adresse e-mail, sujet et contenu du message</li>
            <li><strong>Newsletter</strong> : adresse e-mail d’inscription</li>
            <li><strong>Avis</strong> : note et commentaire liés à un produit / une commande</li>
            <li><strong>Technique</strong> : données de session (connexion, panier, jeton CSRF), jeton Cloudflare Turnstile le cas échéant, et adresse IP pour la limitation des tentatives de connexion et des formulaires</li>
        </ul>

        <h2>3. Finalités et bases légales</h2>
        <ul>
            <li><strong>Création et gestion de compte</strong> — exécution du contrat / mesures précontractuelles</li>
            <li><strong>Traitement des commandes et suivi</strong> — exécution du contrat</li>
            <li><strong>Réponse aux messages de contact</strong> — intérêt légitime et/ou mesures précontractuelles</li>
            <li><strong>Newsletter</strong> — consentement (inscription volontaire)</li>
            <li><strong>E-mails transactionnels</strong> (confirmation de commande, bienvenue) — exécution du contrat / intérêt légitime</li>
            <li><strong>Sécurité du site</strong> (session, CSRF, rate limiting, Turnstile) — intérêt légitime</li>
            <li><strong>Statistiques agrégées</strong> (MongoDB Atlas, espace admin) — intérêt légitime, données non publicitaires</li>
        </ul>

        <h2>4. Destinataires et sous-traitants</h2>
        <p>Les données sont destinées à Fashion Paris (administration du site).</p>
        <p>Elles peuvent être traitées par des prestataires techniques :</p>
        <ul>
            <li><strong>Infomaniak Network SA</strong> — hébergement web, base MySQL et envoi SMTP</li>
            <li><strong>MongoDB Atlas</strong> — stockage de statistiques de commandes pour le tableau de bord admin</li>
            <li><strong>Cloudflare</strong> — Turnstile (anti-bot) sur le formulaire de contact</li>
        </ul>
        <p>Ces prestataires agissent pour notre compte dans le cadre de leurs conditions de service.</p>

        <h2>5. Durée de conservation</h2>
        <ul>
            <li><strong>Compte client</strong> : pendant la relation commerciale ; suppression sur demande</li>
            <li><strong>Commandes</strong> : conservation nécessaire au suivi et aux obligations légales / comptables</li>
            <li><strong>Messages de contact</strong> : le temps nécessaire au traitement de la demande</li>
            <li><strong>Newsletter</strong> : jusqu’à désinscription ou demande d’effacement</li>
            <li><strong>Sessions / cookies techniques</strong> : durée de la session de navigation</li>
            <li><strong>Journal de tentatives</strong> (anti force brute / rate limit) : purge automatique sous 24 h</li>
        </ul>

        <h2>6. Vos droits (RGPD)</h2>
        <p>Conformément au RGPD, vous disposez des droits suivants :</p>
        <ul>
            <li>droit d’accès</li>
            <li>droit de rectification</li>
            <li>droit à l’effacement</li>
            <li>droit à la limitation du traitement</li>
            <li>droit d’opposition</li>
            <li>droit à la portabilité, lorsque applicable</li>
            <li>droit de retirer votre consentement (newsletter)</li>
        </ul>
        <p>
            Pour les exercer, contactez
            <a href="mailto:contact@anteusweb.com">contact@anteusweb.com</a>
            ou utilisez le <a href="<?= BASE_URL ?>/contact">formulaire de contact</a>.
        </p>
        <p>
            Si vous disposez d’un compte, vous pouvez aussi mettre à jour vos informations
            depuis <a href="<?= BASE_URL ?>/mon-compte">Mon compte</a>.
        </p>
        <p>
            En cas de difficulté, vous pouvez saisir la CNIL
            (<a href="https://www.cnil.fr" rel="noopener noreferrer">www.cnil.fr</a>).
        </p>

        <h2>7. Cookies</h2>
        <p>
            Le site utilise uniquement des cookies de session strictement nécessaires
            à l’authentification, au panier et à la sécurité des formulaires (jeton CSRF).
            Aucun cookie publicitaire, analytique tiers ou de suivi n’est déposé.
            Ces cookies techniques ne nécessitent pas de bandeau de consentement.
        </p>

        <h2>8. Sécurité</h2>
        <p>
            Mesures mises en œuvre notamment : mots de passe hashés (bcrypt),
            protection CSRF, sessions HttpOnly / SameSite, régénération d’identifiant de session,
            limitation des tentatives de connexion et des formulaires,
            Cloudflare Turnstile sur le contact, contrôle d’accès par rôles (AuthGuard),
            et protection des dossiers sensibles via fichiers <code>.htaccess</code>.
        </p>

        <h2>9. Mise à jour</h2>
        <p>
            La présente politique peut être mise à jour pour refléter l’évolution du site
            ou de la réglementation. Dernière mise à jour : novembre 2026.
        </p>

        <p class="legal-back">
            <a href="<?= BASE_URL ?>/mentions-legales">← Mentions légales</a>
            ·
            <a href="<?= BASE_URL ?>/">Accueil</a>
        </p>
    </article>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
