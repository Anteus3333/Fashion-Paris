<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titrePage ?? 'Fashion Paris') ?></title>
    <link rel="icon" href="<?= BASE_URL ?>/images/favicon.svg" type="image/svg+xml">
    <?php $cssVersion = @filemtime(__DIR__ . '/../../public/css/style.css') ?: time(); ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=<?= $cssVersion ?>">
</head>
<body>
    <a class="skip-link" href="#contenu-principal">Aller au contenu principal</a>
    <header role="banner">
        <nav class="navbar" aria-label="Navigation principale">
            <a href="<?= BASE_URL ?>/" class="logo">Fashion Paris</a>

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
        <p>&copy; <?= date('Y') ?> Fashion Paris</p>
        <nav aria-label="Liens légaux">
            <a href="<?= BASE_URL ?>/mentions-legales">Mentions légales</a>
            <a href="<?= BASE_URL ?>/accessibilite">Accessibilité</a>
        </nav>
    </footer>

    <?php $jsVersion = @filemtime(__DIR__ . '/../../public/js/main.js') ?: time(); ?>
    <script src="<?= BASE_URL ?>/js/main.js?v=<?= $jsVersion ?>" defer></script>
</body>
</html>
