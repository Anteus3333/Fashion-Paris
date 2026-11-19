<?php
ob_start();
?>
<section class="section page-narrow">
    <h1>Connexion</h1>
    <?php if (!empty($erreur)): ?>
        <p class="form-error" role="alert"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <form method="post" class="auth-form" novalidate>
        <?= Csrf::champ() ?>
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required autocomplete="username"
               value="<?= htmlspecialchars($oldEmail ?? '') ?>">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">

        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
    <p class="auth-liens">
        <a href="<?= BASE_URL ?>/mot-de-passe-oublie">Mot de passe oublié ?</a><br>
        Pas encore de compte ? <a href="<?= BASE_URL ?>/inscription">Créer un compte</a>
    </p>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
