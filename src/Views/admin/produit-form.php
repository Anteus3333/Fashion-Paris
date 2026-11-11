<?php
ob_start();
?>
<section class="section page-narrow">
    <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin/produits">Produits</a></p>
    <h1><?= $produit ? 'Modifier' : 'Ajouter' ?> un produit</h1>
    <?php if (!empty($erreurs)): ?>
        <ul class="form-error" role="alert">
            <?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="post" class="auth-form">
        <?= Csrf::champ() ?>
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($old['nom']) ?>">
        <label for="slug">Slug (URL)</label>
        <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($old['slug']) ?>" placeholder="auto si vide">
        <label for="categorie_id">Catégorie</label>
        <select id="categorie_id" name="categorie_id" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int) $cat['categorie_id'] ?>" <?= (int) $old['categorie_id'] === (int) $cat['categorie_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['libelle']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($old['description']) ?></textarea>
        <label for="prix">Prix (€)</label>
        <input type="number" id="prix" name="prix" step="0.01" min="0" required value="<?= htmlspecialchars((string) $old['prix']) ?>">
        <label for="stock">Stock</label>
        <input type="number" id="stock" name="stock" min="0" required value="<?= htmlspecialchars((string) $old['stock']) ?>">
        <label for="sku">SKU</label>
        <input type="text" id="sku" name="sku" value="<?= htmlspecialchars($old['sku']) ?>">
        <label class="checkbox"><input type="checkbox" name="actif" value="1" <?= $old['actif'] === '1' ? 'checked' : '' ?>> Actif</label>
        <label class="checkbox"><input type="checkbox" name="mis_en_avant" value="1" <?= $old['mis_en_avant'] === '1' ? 'checked' : '' ?>> Mis en avant</label>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
