<?php
ob_start();
?>
<section class="section page-narrow">
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

        <label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone" required value="<?= htmlspecialchars($old['telephone']) ?>">

        <label for="adresse_postale">Adresse</label>
        <input type="text" id="adresse_postale" name="adresse_postale" required value="<?= htmlspecialchars($old['adresse_postale']) ?>">

        <label for="code_postal">Code postal</label>
        <input type="text" id="code_postal" name="code_postal" required pattern="\d{5}" value="<?= htmlspecialchars($old['code_postal']) ?>">

        <label for="ville">Ville</label>
        <input type="text" id="ville" name="ville" required value="<?= htmlspecialchars($old['ville']) ?>">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required minlength="10" autocomplete="new-password">
        <p class="muted">Au moins 10 caractères, avec majuscule, minuscule, chiffre et caractère spécial.</p>

        <label for="confirmation">Confirmation</label>
        <input type="password" id="confirmation" name="confirmation" required autocomplete="new-password">

        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
    <p><a href="<?= BASE_URL ?>/login">Déjà un compte ? Connexion</a></p>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
