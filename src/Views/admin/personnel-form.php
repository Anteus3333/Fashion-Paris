<?php
ob_start();
$modeEdition = !empty($modeEdition);
$formAction = $modeEdition
    ? BASE_URL . '/admin/personnel/' . (int) $personnelId
    : BASE_URL . '/admin/personnel/nouveau';
$nomAffiche = ViewHelper::formatNomComplet($old['prenom'] ?? '', $old['nom'] ?? '');
?>
<section class="section admin-page">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin/personnel">Personnel</a></p>
    <header class="admin-page-head">
        <h1><?= $modeEdition ? 'Modifier le compte personnel' : 'Nouveau compte personnel' ?></h1>
        <?php if ($modeEdition && $nomAffiche !== ''): ?>
            <p class="muted admin-page-sub"><?= htmlspecialchars($nomAffiche) ?></p>
        <?php endif; ?>
    </header>
    <?php require __DIR__ . '/_nav.php'; ?>

    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($formAction) ?>" class="admin-staff-form">
        <?= Csrf::champ() ?>

        <fieldset class="admin-fieldset">
            <legend>Identité</legend>
            <div class="admin-field-row admin-field-row--2">
                <div class="admin-field">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" required maxlength="50"
                           value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" autocomplete="given-name">
                </div>
                <div class="admin-field">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" required maxlength="50"
                           value="<?= htmlspecialchars($old['nom'] ?? '') ?>" autocomplete="family-name">
                </div>
            </div>
            <div class="admin-field-row admin-field-row--2">
                <div class="admin-field">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" required maxlength="100"
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>" autocomplete="email">
                </div>
                <div class="admin-field">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" maxlength="50"
                           value="<?= htmlspecialchars($old['telephone'] ?? '') ?>" autocomplete="tel">
                </div>
            </div>
        </fieldset>

        <fieldset class="admin-fieldset">
            <legend>Adresse</legend>
            <div class="admin-field">
                <label for="adresse_postale">Adresse postale *</label>
                <input type="text" id="adresse_postale" name="adresse_postale" required maxlength="255"
                       value="<?= htmlspecialchars($old['adresse_postale'] ?? '') ?>" autocomplete="street-address">
            </div>
            <div class="admin-field-row admin-field-row--2">
                <div class="admin-field">
                    <label for="code_postal">Code postal *</label>
                    <input type="text" id="code_postal" name="code_postal" required maxlength="10" inputmode="numeric"
                           value="<?= htmlspecialchars($old['code_postal'] ?? '') ?>" autocomplete="postal-code">
                </div>
                <div class="admin-field">
                    <label for="ville">Ville *</label>
                    <input type="text" id="ville" name="ville" required maxlength="50"
                           value="<?= htmlspecialchars($old['ville'] ?? '') ?>" autocomplete="address-level2">
                </div>
            </div>
        </fieldset>

        <fieldset class="admin-fieldset">
            <legend>Sécurité</legend>
            <p class="muted admin-hint">
                <?php if ($modeEdition): ?>
                    Laisser vide pour conserver le mot de passe actuel.
                <?php else: ?>
                    Mot de passe obligatoire à la création.
                <?php endif; ?>
                Règle : 10 caractères min., majuscule, minuscule, chiffre, caractère spécial.
            </p>
            <div class="admin-field">
                <label for="password"><?= $modeEdition ? 'Nouveau mot de passe' : 'Mot de passe *' ?></label>
                <input type="password" id="password" name="password" <?= $modeEdition ? '' : 'required' ?>
                       autocomplete="new-password"
                       data-password-toggle>
            </div>
            <?php if (!empty($erreursPassword)): ?>
                <ul class="form-error"><?php foreach ($erreursPassword as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            <?php endif; ?>
            <div class="admin-field">
                <label for="confirmation"><?= $modeEdition ? 'Confirmer le nouveau mot de passe' : 'Confirmer le mot de passe *' ?></label>
                <input type="password" id="confirmation" name="confirmation" <?= $modeEdition ? '' : 'required' ?>
                       autocomplete="new-password"
                       data-password-toggle>
            </div>
            <?php if (!empty($erreurConfirmation)): ?>
                <p class="form-error"><?= htmlspecialchars($erreurConfirmation) ?></p>
            <?php endif; ?>
        </fieldset>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary"><?= $modeEdition ? 'Enregistrer' : 'Créer le compte' ?></button>
            <a class="btn btn-secondary" href="<?= BASE_URL ?>/admin/personnel">Annuler</a>
        </div>
    </form>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
