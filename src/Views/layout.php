<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titrePage ?? 'Fashion Paris') ?></title>
    <link rel="icon" href="<?= BASE_URL ?>/images/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php $cssVersion = @filemtime(__DIR__ . '/../../public/css/style.css') ?: time(); ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=<?= $cssVersion ?>">
</head>
<body>
    <a class="skip-link" href="#contenu-principal">Aller au contenu principal</a>
    <header role="banner">
        <nav class="navbar" aria-label="Navigation principale">
            <div class="navbar-brand">
                <a href="<?= BASE_URL ?>/" class="logo">Fashion Paris</a>
                <?php if (isset($_SESSION['utilisateur'])): ?>
                    <span class="nav-greeting" aria-label="Utilisateur connecté">
                        Bonjour <?= htmlspecialchars($_SESSION['utilisateur']['prenom'] ?? '') ?>
                    </span>
                <?php endif; ?>
            </div>

            <button type="button" class="burger" id="burger"
                aria-expanded="false" aria-controls="nav-links"
                data-label-open="Ouvrir le menu de navigation"
                data-label-close="Fermer le menu de navigation"
                aria-label="Ouvrir le menu de navigation">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </button>

            <ul class="nav-links" id="nav-links">
                <li><a href="<?= BASE_URL ?>/">Accueil</a></li>
                <li><a href="<?= BASE_URL ?>/catalogue">Catalogue</a></li>
                <li><a href="<?= BASE_URL ?>/contact">Contact</a></li>
                <?php $nbPanier = PanierService::nombreArticles(); ?>
                <li>
                    <a href="<?= BASE_URL ?>/panier">
                        Panier<?= $nbPanier > 0 ? ' (' . $nbPanier . ')' : '' ?>
                    </a>
                </li>
                <?php if (isset($_SESSION['utilisateur'])): ?>
                    <?php $roleNav = $_SESSION['utilisateur']['role'] ?? 'utilisateur'; ?>
                    <?php if ($roleNav === 'administrateur'): ?>
                        <li><a href="<?= BASE_URL ?>/admin">Administration</a></li>
                    <?php elseif ($roleNav === 'personnel'): ?>
                        <li><a href="<?= BASE_URL ?>/espace-personnel">Espace personnel</a></li>
                    <?php endif; ?>
                    <li><a href="<?= BASE_URL ?>/mon-compte">Mon compte</a></li>
                    <li class="nav-bonjour" aria-label="Utilisateur connecté">
                        Bonjour <?= htmlspecialchars($_SESSION['utilisateur']['prenom'] ?? '') ?>
                    </li>
                    <li>
                        <form method="post" action="<?= BASE_URL ?>/deconnexion" class="nav-logout">
                            <?= Csrf::champ() ?>
                            <button type="submit">Déconnexion</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>/login">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php if (!empty($barreCheckoutCgv)): ?>
            <div class="legal-checkout-bar" role="region" aria-label="Retour à la commande">
                <a class="btn btn-primary" href="<?= BASE_URL ?>/commande?etape=paiement">Retour au paiement</a>
            </div>
        <?php endif; ?>
    </header>

    <main id="contenu-principal" tabindex="-1">
        <?php if (!empty($_SESSION['flash_succes'])): ?>
            <div class="flash-banner flash-banner-succes" role="status" aria-live="polite">
                <p class="flash-banner-text"><?= htmlspecialchars($_SESSION['flash_succes']) ?></p>
            </div>
            <?php unset($_SESSION['flash_succes']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_erreur'])): ?>
            <div class="flash-banner flash-banner-erreur" role="alert">
                <p class="flash-banner-text"><?= htmlspecialchars($_SESSION['flash_erreur']) ?></p>
            </div>
            <?php unset($_SESSION['flash_erreur']); ?>
        <?php endif; ?>

        <?= $contenu ?? '' ?>
    </main>

    <footer role="contentinfo" class="site-footer">
        <?php
        if (!isset($resumeHorairesFooter)) {
            require_once __DIR__ . '/../Models/HoraireModel.php';
            $resumeHorairesFooter = (new HoraireModel())->getResumeFooter();
        }
        if (!isset($boutique)) {
            $boutique = require __DIR__ . '/../../config/boutique.php';
        }
        ?>
        <div class="site-footer-inner">
            <div class="footer-horaires">
                <h2 class="footer-heading">Nos horaires</h2>
                <?php foreach ((array) $resumeHorairesFooter as $ligneHoraire): ?>
                    <p><?= htmlspecialchars($ligneHoraire) ?></p>
                <?php endforeach; ?>
            </div>
            <div class="footer-infos">
                <p><?= htmlspecialchars($boutique['tagline']) ?></p>
                <p>
                    <?= htmlspecialchars($boutique['adresse']) ?>,
                    <?= htmlspecialchars($boutique['code_postal']) ?>
                    <?= htmlspecialchars($boutique['ville']) ?>
                </p>
                <p>
                    <a href="mailto:<?= htmlspecialchars($boutique['email']) ?>"><?= htmlspecialchars($boutique['email']) ?></a>
                    · <?= htmlspecialchars($boutique['telephone']) ?>
                </p>
            </div>
            <div class="footer-legal">
                <nav class="footer-links" aria-label="Liens légaux">
                    <a href="<?= BASE_URL ?>/mentions-legales">Mentions légales</a>
                    <span aria-hidden="true"> · </span>
                    <a href="<?= BASE_URL ?>/politique-confidentialite">Confidentialité</a>
                    <span aria-hidden="true"> · </span>
                    <a href="<?= BASE_URL ?>/cgv">CGV</a>
                    <span aria-hidden="true"> · </span>
                    <a href="<?= BASE_URL ?>/accessibilite">Accessibilité</a>
                </nav>
                <p class="footer-copy">&copy; <?= date('Y') ?> <?= htmlspecialchars($boutique['nom']) ?></p>
            </div>
        </div>
    </footer>

    <?php
    $jsMain = @filemtime(__DIR__ . '/../../public/js/main.js') ?: time();
    $jsPickers = @filemtime(__DIR__ . '/../../public/js/pickers.js') ?: time();
    ?>
    <script src="<?= BASE_URL ?>/js/main.js?v=<?= $jsMain ?>" defer></script>
    <script src="<?= BASE_URL ?>/js/pickers.js?v=<?= $jsPickers ?>" defer></script>
    <?= $scriptsFooter ?? '' ?>
</body>
</html>
