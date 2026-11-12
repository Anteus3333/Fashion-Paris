<?php
ob_start();
?>
<section class="section page-narrow">
    <h1>Contact</h1>
    <p class="muted">Une question ? Écrivez-nous — réponse via <strong>contact@anteusweb.com</strong>.</p>

    <?php if (!empty($succes)): ?>
        <p class="flash-banner flash-banner-succes" role="status">Merci, votre message a bien été envoyé.</p>
    <?php endif; ?>

    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" class="auth-form" novalidate>
        <?= Csrf::champ() ?>
        <!-- Honeypot -->
        <div class="sr-only" aria-hidden="true">
            <label for="website">Site web</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($old['nom']) ?>">

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($old['email']) ?>">

        <label for="sujet">Sujet</label>
        <input type="text" id="sujet" name="sujet" required value="<?= htmlspecialchars($old['sujet']) ?>">

        <label for="message">Message</label>
        <textarea id="message" name="message" rows="5" required><?= htmlspecialchars($old['message']) ?></textarea>

        <?php if (!empty($turnstileActif)): ?>
            <div class="champ-turnstile">
                <div class="cf-turnstile"
                     data-sitekey="<?= htmlspecialchars($turnstileSiteKey) ?>"
                     data-theme="light"></div>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/layout.php';
