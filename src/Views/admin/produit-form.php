<?php
ob_start();
$edition = !empty($produit);
?>
<section class="section admin-page">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin/produits">Produits</a></p>
    <header class="admin-page-head">
        <h1><?= $edition ? 'Modifier un produit' : 'Ajouter un produit' ?></h1>
        <?php if ($edition): ?>
            <p class="muted admin-page-sub">
                <?= htmlspecialchars($old['nom'] !== '' ? $old['nom'] : 'Produit') ?>
                <?php if ($old['sku'] !== ''): ?>
                    · SKU <?= htmlspecialchars($old['sku']) ?>
                <?php endif; ?>
            </p>
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
                    <label for="slug">Slug (URL)</label>
                    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($old['slug']) ?>" placeholder="Généré automatiquement si vide">
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
                <div class="admin-field-row">
                    <div class="admin-field">
                        <label for="prix">Prix (€)</label>
                        <input type="number" id="prix" name="prix" step="0.01" min="0" required value="<?= htmlspecialchars((string) $old['prix']) ?>">
                    </div>
                    <div class="admin-field">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" min="0" required value="<?= htmlspecialchars((string) $old['stock']) ?>">
                    </div>
                    <div class="admin-field">
                        <label for="sku">SKU</label>
                        <input type="text" id="sku" name="sku" value="<?= htmlspecialchars($old['sku']) ?>">
                    </div>
                </div>
            </fieldset>

            <fieldset class="admin-fieldset">
                <legend>Publication</legend>
                <div class="admin-checks">
                    <label class="checkbox"><input type="checkbox" name="actif" value="1" <?= $old['actif'] === '1' ? 'checked' : '' ?>> Actif (visible catalogue)</label>
                    <label class="checkbox"><input type="checkbox" name="mis_en_avant" value="1" <?= $old['mis_en_avant'] === '1' ? 'checked' : '' ?>> Mis en avant (accueil)</label>
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
