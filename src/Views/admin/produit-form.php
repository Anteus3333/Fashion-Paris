<?php
ob_start();
$edition = !empty($produit);
?>
<section class="section admin-page">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin/produits">Produits</a></p>
    <header class="admin-page-head">
        <h1><?= $edition ? 'Modifier un produit' : 'Ajouter un produit' ?></h1>
        <?php if ($edition && $old['nom'] !== ''): ?>
            <p class="muted admin-page-sub"><?= htmlspecialchars($old['nom']) ?></p>
        <?php endif; ?>
    </header>
    <?php require __DIR__ . '/_nav.php'; ?>

    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" class="admin-product-form" enctype="multipart/form-data">
        <?= Csrf::champ() ?>

        <aside class="admin-product-media" aria-labelledby="media-titre">
            <h2 id="media-titre" class="admin-panel-title">Visuel</h2>
            <?php $aImage = !empty($produit['image']); ?>
            <div class="admin-product-preview<?= $aImage ? '' : ' is-empty' ?>">
                <?php if ($aImage): ?>
                    <img src="<?= AssetHelper::imageUrl($produit['image']) ?>"
                         alt="<?= htmlspecialchars($old['nom'] !== '' ? $old['nom'] : 'Produit') ?>">
                <?php else: ?>
                    <p class="muted">Aucune image</p>
                <?php endif; ?>
            </div>
            <label for="image">Remplacer la photo</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
            <p class="muted admin-hint">JPEG, PNG ou WebP — 5 Mo max</p>
        </aside>

        <div class="admin-product-fields">
            <fieldset class="admin-fieldset">
                <legend>Identité</legend>
                <div class="admin-field">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($old['nom']) ?>">
                </div>
                <div class="admin-field">
                    <label for="categorie_id">Catégorie</label>
                    <select id="categorie_id" name="categorie_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int) $cat['categorie_id'] ?>" <?= (int) $old['categorie_id'] === (int) $cat['categorie_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5"><?= htmlspecialchars($old['description']) ?></textarea>
                </div>
            </fieldset>

            <fieldset class="admin-fieldset">
                <legend>Tarif & stock</legend>
                <div class="admin-field-row admin-field-row--2">
                    <div class="admin-field">
                        <label for="prix">Prix (€)</label>
                        <input type="number" id="prix" name="prix" step="0.01" min="0" required value="<?= htmlspecialchars((string) $old['prix']) ?>">
                    </div>
                    <div class="admin-field">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" min="0" required value="<?= htmlspecialchars((string) $old['stock']) ?>">
                    </div>
                </div>
            </fieldset>

            <fieldset class="admin-fieldset" data-publication>
                <legend>Publication</legend>
                <p class="muted admin-hint">
                    L’accueil affiche jusqu’à <?= (int) ProduitModel::MAX_MIS_EN_AVANT ?> pièces mises en avant.
                    La mise en avant n’est possible que si le produit est actif.
                </p>
                <div class="admin-checks">
                    <label class="checkbox">
                        <input type="checkbox" name="actif" id="produit-actif" value="1"
                               <?= $old['actif'] === '1' ? 'checked' : '' ?>>
                        Actif (visible catalogue)
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" name="mis_en_avant" id="produit-mis-en-avant" value="1"
                               <?= $old['mis_en_avant'] === '1' ? 'checked' : '' ?>
                               <?= $old['actif'] !== '1' ? 'disabled' : '' ?>>
                        Mis en avant (accueil)
                    </label>
                </div>
                <?php
                $dejaEnAvant = $edition && !empty($produit['mis_en_avant']) && $old['mis_en_avant'] === '1';
                $afficherRemplacement = !empty($placesVedettesPleines) && !$dejaEnAvant;
                ?>
                <div class="admin-field admin-replace-vedette<?= $afficherRemplacement ? '' : ' is-hidden' ?>"
                     id="bloc-remplace-vedette"
                     data-places-pleines="<?= !empty($placesVedettesPleines) ? '1' : '0' ?>"
                     data-deja-en-avant="<?= $edition && !empty($produit['mis_en_avant']) ? '1' : '0' ?>">
                    <label for="remplace_mis_en_avant">À la place de</label>
                    <select id="remplace_mis_en_avant" name="remplace_mis_en_avant"
                            <?= $afficherRemplacement ? 'required' : '' ?>>
                        <option value="">Choisir le produit à retirer…</option>
                        <?php foreach ($vedettesAutres ?? [] as $v): ?>
                            <option value="<?= (int) $v['produit_id'] ?>"
                                <?= (int) ($remplaceId ?? 0) === (int) $v['produit_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['nom']) ?>
                                <?= empty($v['actif']) ? ' (inactif)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </fieldset>

            <div class="admin-form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a class="btn btn-secondary" href="<?= BASE_URL ?>/admin/produits">Annuler</a>
            </div>
        </div>
    </form>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
