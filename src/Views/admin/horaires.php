<?php
ob_start();
$semaine = $semaine ?? ['ouverture' => '10:00', 'fermeture' => '19:00'];
$samedi = $samedi ?? ['ouverture' => '10:00', 'fermeture' => '18:00'];
?>
<section class="section admin-page">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
    <header class="admin-page-head">
        <h1>Horaires d’ouverture</h1>
        <p class="muted admin-page-sub">Affichés dans le pied de page et sur la page Contact.</p>
    </header>
    <?php require __DIR__ . '/_nav.php'; ?>

    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (empty($horaires)): ?>
        <p class="empty-state">Aucun horaire en base. Réimportez <code>sql/donnees_test.sql</code>.</p>
    <?php else: ?>
        <form method="post" class="horaires-form">
            <?= Csrf::champ() ?>

            <fieldset class="admin-horaire-row">
                <legend>Du lundi au vendredi</legend>
                <div class="admin-horaire-times">
                    <div class="admin-field">
                        <label for="ouverture-semaine">Ouverture</label>
                        <?= ViewHelper::champHeure('ouverture-semaine', 'ouverture_semaine', $semaine['ouverture']) ?>
                    </div>
                    <div class="admin-field">
                        <label for="fermeture-semaine">Fermeture</label>
                        <?= ViewHelper::champHeure('fermeture-semaine', 'fermeture_semaine', $semaine['fermeture']) ?>
                    </div>
                </div>
            </fieldset>

            <fieldset class="admin-horaire-row">
                <legend>Samedi</legend>
                <div class="admin-horaire-times">
                    <div class="admin-field">
                        <label for="ouverture-samedi">Ouverture</label>
                        <?= ViewHelper::champHeure('ouverture-samedi', 'ouverture_samedi', $samedi['ouverture']) ?>
                    </div>
                    <div class="admin-field">
                        <label for="fermeture-samedi">Fermeture</label>
                        <?= ViewHelper::champHeure('fermeture-samedi', 'fermeture_samedi', $samedi['fermeture']) ?>
                    </div>
                </div>
            </fieldset>

            <div class="admin-form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer les horaires</button>
            </div>
        </form>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
