<?php
ob_start();
?>
<section class="section page-narrow auth-page">
    <h1>Nouveau mot de passe</h1>
    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="post" action="<?= BASE_URL ?>/reinitialisation" class="auth-form">
        <?= Csrf::champ() ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">Nouveau mot de passe</label>
        <input type="password" id="password" name="password" required autofocus autocomplete="new-password" data-password-toggle>
        <p class="muted">10 caractères min., majuscule, minuscule, chiffre et caractère spécial.</p>

        <label for="confirmation">Confirmer le mot de passe</label>
        <input type="password" id="confirmation" name="confirmation" required autocomplete="new-password">

        <button type="submit" class="btn btn-primary">Enregistrer le nouveau mot de passe</button>
    </form>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
