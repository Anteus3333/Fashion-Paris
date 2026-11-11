<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin/commandes">Commandes</a></p>
    <h1><?= htmlspecialchars($commande['numero_commande']) ?></h1>
    <?php if (!empty($erreurs)): ?>
        <ul class="form-error"><?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
    <p>
        Client : <?= htmlspecialchars(ViewHelper::formatNomComplet($client['prenom'] ?? '', $client['nom'] ?? '')) ?>
        (<?= htmlspecialchars($client['email'] ?? '') ?>)
    </p>
    <p>Statut actuel : <strong><?= htmlspecialchars($config['statuts'][$commande['statut']] ?? $commande['statut']) ?></strong></p>

    <h2>Lignes</h2>
    <ul class="cart-list">
        <?php foreach ($lignes as $l): ?>
            <li class="cart-item">
                <span><?= htmlspecialchars($l['nom']) ?> × <?= (int) $l['quantite'] ?></span>
                <span><?= ViewHelper::prix((float) $l['prix_unitaire'] * (int) $l['quantite']) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <p class="cart-total">Total : <?= ViewHelper::prix($commande['montant_total']) ?></p>

    <h2>Changer le statut</h2>
    <form method="post" class="auth-form">
        <?= Csrf::champ() ?>
        <label for="statut">Nouveau statut</label>
        <select id="statut" name="statut">
            <?php foreach ($config['statuts'] as $code => $libelle): ?>
                <option value="<?= htmlspecialchars($code) ?>" <?= $commande['statut'] === $code ? 'selected' : '' ?>>
                    <?= htmlspecialchars($libelle) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="commentaire">Commentaire (suivi)</label>
        <input type="text" id="commentaire" name="commentaire" placeholder="Optionnel">
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>

    <h2>Historique</h2>
    <ol class="suivi-list">
        <?php foreach ($suivi as $s): ?>
            <li>
                <strong><?= htmlspecialchars($config['statuts'][$s['statut']] ?? $s['statut']) ?></strong>
                <span class="muted"><?= htmlspecialchars($s['date_modification']) ?></span>
                <?php if ($s['commentaire']): ?> — <?= htmlspecialchars($s['commentaire']) ?><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
