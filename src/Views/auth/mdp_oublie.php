<?php
ob_start();
?>
<section class="section page-narrow auth-page">
    <h1>Mot de passe oublié</h1>
    <p class="section-intro">
        Indiquez votre adresse e-mail : nous vous enverrons un lien pour choisir un nouveau mot de passe.
    </p>
    <form method="post" action="<?= BASE_URL ?>/mot-de-passe-oublie" class="auth-form">
        <?= Csrf::champ() ?>
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" required autofocus autocomplete="username">
        <button type="submit" class="btn btn-primary">Envoyer le lien de réinitialisation</button>
    </form>
    <p><a href="<?= BASE_URL ?>/login">Retour à la connexion</a></p>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
