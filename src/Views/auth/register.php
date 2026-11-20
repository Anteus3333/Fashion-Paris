<?php
ob_start();
?>
<section class="section page-narrow auth-page">
    <h1>Créer un compte</h1>

    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" class="auth-form" novalidate>
        <?= Csrf::champ() ?>

        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" required value="<?= htmlspecialchars($old['prenom']) ?>">

        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($old['nom']) ?>">

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required autocomplete="username" value="<?= htmlspecialchars($old['email']) ?>">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required minlength="10" autocomplete="new-password">
        <p class="muted">Au moins 10 caractères, avec majuscule, minuscule, chiffre et caractère spécial.</p>

        <label for="confirmation">Confirmation</label>
        <input type="password" id="confirmation" name="confirmation" required autocomplete="new-password">

        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
    <p class="auth-liens"><a href="<?= BASE_URL ?>/login">Déjà un compte ? Connexion</a></p>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
