<?php
ob_start();
$modeEdition = !empty($modeEdition);
$formAction = $modeEdition
    ? BASE_URL . '/admin/personnel/' . (int) $personnelId
    : BASE_URL . '/admin/personnel/nouveau';
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin/personnel">Personnel</a></p>
    <h1><?= $modeEdition ? 'Modifier le compte personnel' : 'Nouveau compte personnel' ?></h1>
    <?php require __DIR__ . '/_nav.php'; ?>

    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($formAction) ?>" class="auth-form">
        <?= Csrf::champ() ?>
        <label for="prenom">Prénom *</label>
        <input type="text" id="prenom" name="prenom" required maxlength="50"
               value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" autocomplete="given-name">

        <label for="nom">Nom *</label>
        <input type="text" id="nom" name="nom" required maxlength="50"
               value="<?= htmlspecialchars($old['nom'] ?? '') ?>" autocomplete="family-name">

        <label for="email">E-mail *</label>
        <input type="email" id="email" name="email" required maxlength="100"
               value="<?= htmlspecialchars($old['email'] ?? '') ?>" autocomplete="email">

        <label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone" maxlength="50"
               value="<?= htmlspecialchars($old['telephone'] ?? '') ?>" autocomplete="tel">

        <label for="adresse_postale">Adresse postale *</label>
        <input type="text" id="adresse_postale" name="adresse_postale" required maxlength="255"
               value="<?= htmlspecialchars($old['adresse_postale'] ?? '') ?>" autocomplete="street-address">

        <label for="code_postal">Code postal *</label>
        <input type="text" id="code_postal" name="code_postal" required maxlength="10" inputmode="numeric"
               value="<?= htmlspecialchars($old['code_postal'] ?? '') ?>" autocomplete="postal-code">

        <label for="ville">Ville *</label>
        <input type="text" id="ville" name="ville" required maxlength="50"
               value="<?= htmlspecialchars($old['ville'] ?? '') ?>" autocomplete="address-level2">

        <label for="password"><?= $modeEdition ? 'Nouveau mot de passe' : 'Mot de passe *' ?></label>
        <input type="password" id="password" name="password" <?= $modeEdition ? '' : 'required' ?>
               autocomplete="new-password"
               data-password-toggle>
        <p class="muted">
            <?php if ($modeEdition): ?>
                Laisser vide pour conserver le mot de passe. Sinon : 10 caractères min., majuscule, minuscule, chiffre, caractère spécial.
            <?php else: ?>
                10 caractères min., majuscule, minuscule, chiffre, caractère spécial.
            <?php endif; ?>
        </p>
        <?php if (!empty($erreursPassword)): ?>
            <ul class="form-error"><?php foreach ($erreursPassword as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        <?php endif; ?>

        <label for="confirmation"><?= $modeEdition ? 'Confirmer le nouveau mot de passe' : 'Confirmer le mot de passe *' ?></label>
        <input type="password" id="confirmation" name="confirmation" <?= $modeEdition ? '' : 'required' ?>
               autocomplete="new-password">
        <?php if (!empty($erreurConfirmation)): ?>
            <p class="form-error"><?= htmlspecialchars($erreurConfirmation) ?></p>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary"><?= $modeEdition ? 'Enregistrer' : 'Créer le compte' ?></button>
    </form>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
