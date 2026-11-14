<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <h1>Horaires d’ouverture</h1>
    <?php require __DIR__ . '/_nav.php'; ?>
    <p class="section-intro">Modifiez les horaires affichés dans le pied de page et sur la page Contact.</p>

    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (empty($horaires)): ?>
        <p class="muted">Aucun horaire en base. Exécutez <code>sql/add_horaires.sql</code> ou le seed.</p>
    <?php else: ?>
        <form method="post" class="auth-form horaires-form">
            <?= Csrf::champ() ?>
            <?php foreach ($horaires as $h): ?>
                <fieldset class="admin-horaire-row">
                    <legend><?= htmlspecialchars($h['jour']) ?></legend>
                    <input type="hidden" name="horaire_id[]" value="<?= (int) $h['horaire_id'] ?>">
                    <label for="ouverture-<?= (int) $h['horaire_id'] ?>">Ouverture</label>
                    <?= ViewHelper::champHeure(
                        'ouverture-' . (int) $h['horaire_id'],
                        'heure_ouverture[]',
                        substr($h['heure_ouverture'] ?? '10:00', 0, 5)
                    ) ?>
                    <label for="fermeture-<?= (int) $h['horaire_id'] ?>">Fermeture</label>
                    <?= ViewHelper::champHeure(
                        'fermeture-' . (int) $h['horaire_id'],
                        'heure_fermeture[]',
                        substr($h['heure_fermeture'] ?? '19:00', 0, 5)
                    ) ?>
                </fieldset>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Enregistrer les horaires</button>
        </form>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
