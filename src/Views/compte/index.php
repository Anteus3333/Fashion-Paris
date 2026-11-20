<?php
ob_start();
$config = $config ?? require __DIR__ . '/../../../config/commandeconfig.php';
$nomComplet = ViewHelper::formatNomComplet($utilisateur['prenom'] ?? '', $utilisateur['nom'] ?? '');
$adresseLigne = trim((string) ($utilisateur['adresse_postale'] ?? ''));
$cpVille = trim(($utilisateur['code_postal'] ?? '') . ' ' . ($utilisateur['ville'] ?? ''));
$telephone = trim((string) ($utilisateur['telephone'] ?? ''));
?>
<section class="section page-narrow">
    <h1>Mon compte</h1>
    <p>Bonjour <?= htmlspecialchars($nomComplet) ?>.</p>

    <div class="compte-profil">
        <h2>Mes informations</h2>
        <dl class="compte-infos">
            <div>
                <dt>Nom</dt>
                <dd><?= htmlspecialchars($nomComplet !== '' ? $nomComplet : '—') ?></dd>
            </div>
            <div>
                <dt>E-mail</dt>
                <dd><?= htmlspecialchars($utilisateur['email'] ?? '—') ?></dd>
            </div>
            <div>
                <dt>Téléphone</dt>
                <dd><?= htmlspecialchars($telephone !== '' ? $telephone : '—') ?></dd>
            </div>
            <div>
                <dt>Adresse</dt>
                <dd>
                    <?php if ($adresseLigne === '' && $cpVille === ''): ?>
                        —
                    <?php else: ?>
                        <?php if ($adresseLigne !== ''): ?>
                            <?= htmlspecialchars($adresseLigne) ?><br>
                        <?php endif; ?>
                        <?php if ($cpVille !== ''): ?>
                            <?= htmlspecialchars($cpVille) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </dd>
            </div>
        </dl>
        <p class="compte-profil__actions">
            <?= ViewHelper::btnNav(BASE_URL . '/mon-compte/profil', 'Modifier mon profil', 'btn btn-primary') ?>
        </p>
    </div>

    <h2>Mes commandes</h2>
    <?php if (empty($commandes)): ?>
        <p class="empty-state">Aucune commande pour le moment.</p>
        <p><?= ViewHelper::btnNav(BASE_URL . '/catalogue', 'Voir le catalogue', 'btn btn-primary') ?></p>
    <?php else: ?>
        <ul class="order-list">
            <?php foreach ($commandes as $c): ?>
                <li>
                    <a href="<?= BASE_URL ?>/mon-compte/commande/<?= htmlspecialchars($c['numero_commande']) ?>">
                        <strong><?= htmlspecialchars($c['numero_commande']) ?></strong>
                    </a>
                    — <?= htmlspecialchars($config['statuts'][$c['statut']] ?? $c['statut']) ?>
                    — <?= ViewHelper::prix($c['montant_total']) ?>
                    <span class="muted">(<?= htmlspecialchars($c['date_commande']) ?>)</span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
