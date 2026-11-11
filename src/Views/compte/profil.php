<?php
ob_start();
?>
<section class="section page-narrow">
    <h1>Mon profil</h1>
    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="post" class="auth-form">
        <?= Csrf::champ() ?>
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" required value="<?= htmlspecialchars($old['prenom']) ?>">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($old['nom']) ?>">
        <label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($old['telephone']) ?>">
        <label for="adresse_postale">Adresse</label>
        <input type="text" id="adresse_postale" name="adresse_postale" value="<?= htmlspecialchars($old['adresse_postale']) ?>">
        <label for="code_postal">Code postal</label>
        <input type="text" id="code_postal" name="code_postal" pattern="\d{5}" value="<?= htmlspecialchars($old['code_postal']) ?>">
        <label for="ville">Ville</label>
        <input type="text" id="ville" name="ville" value="<?= htmlspecialchars($old['ville']) ?>">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
    <p><a href="<?= BASE_URL ?>/mon-compte">Retour</a></p>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
