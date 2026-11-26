<?php
ob_start();
$commandesListUrl = $commandesListUrl ?? ($gestionBase ?? BASE_URL);
$titreEspace = $titreEspace ?? 'Gestion';
$isAdminEspace = ($gestionBase ?? '') === (BASE_URL . '/admin');
?>
<section class="section page-narrow" id="commandes-listing">
    <?php if ($isAdminEspace): ?>
        <p class="breadcrumb"><a href="<?= BASE_URL ?>/admin">Admin</a></p>
        <h1>Gestion des commandes</h1>
        <?php require __DIR__ . '/../admin/_nav.php'; ?>
    <?php else: ?>
        <h1>Gestion des commandes</h1>
        <p class="section-intro">Traitez les commandes clients (préparation, expédition, livraison).</p>
        <?php require __DIR__ . '/_nav.php'; ?>
    <?php endif; ?>


    <nav class="cat-nav" aria-label="Filtrer par statut">
        <a href="<?= htmlspecialchars($commandesListUrl) ?>" class="<?= ($statut ?? '') === '' ? 'is-active' : '' ?>">Toutes</a>
        <?php foreach ($config['statuts'] as $code => $libelle): ?>
            <a href="<?= htmlspecialchars($commandesListUrl) ?>?statut=<?= urlencode($code) ?>"
               class="<?= ($statut ?? '') === $code ? 'is-active' : '' ?>"><?= htmlspecialchars($libelle) ?></a>
        <?php endforeach; ?>
    </nav>

    <div class="search-row commandes-search">
        <label class="sr-only" for="search-client">Rechercher un client</label>
        <input type="search" id="search-client" placeholder="Nom du client…">
        <button type="button" id="search-client-clear" class="btn btn-ghost" hidden>Effacer</button>
    </div>
    <p id="commandes-search-empty" class="muted" hidden>Aucune commande pour ce client.</p>

    <?php if (empty($commandes)): ?>
        <p class="muted">Aucune commande.</p>
    <?php else: ?>
        <ul class="commandes-cards" id="commandes-cards">
            <?php foreach ($commandes as $c): ?>
                <?php
                $clientNom = trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? ''));
                $statutLibelle = $config['statuts'][$c['statut']] ?? $c['statut'];
                $detailUrl = $gestionBase . '/commande/' . rawurlencode($c['numero_commande']);
                ?>
                <li class="commande-card" data-client="<?= htmlspecialchars($clientNom) ?>">
                    <div class="commande-card__head">
                        <a class="commande-card__numero" href="<?= htmlspecialchars($detailUrl) ?>">
                            <?= htmlspecialchars($c['numero_commande']) ?>
                        </a>
                        <span class="commande-card__total"><?= ViewHelper::prix($c['montant_total']) ?></span>
                    </div>
                    <dl class="commande-card__meta">
                        <div>
                            <dt>Client</dt>
                            <dd><?= htmlspecialchars($clientNom) ?></dd>
                        </div>
                        <div>
                            <dt>Date</dt>
                            <dd><?= htmlspecialchars(ViewHelper::formatDateHeure($c['date_commande'] ?? '')) ?></dd>
                        </div>
                        <div class="commande-card__statut">
                            <dt>Statut</dt>
                            <dd><?= htmlspecialchars($statutLibelle) ?></dd>
                        </div>
                    </dl>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="commandes-table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>N°</th><th>Client</th><th>Date</th><th>Statut</th><th>Total</th></tr>
                </thead>
                <tbody id="commandes-tbody">
                    <?php foreach ($commandes as $c): ?>
                        <?php $clientNom = trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')); ?>
                        <tr data-client="<?= htmlspecialchars($clientNom) ?>">
                            <td>
                                <a href="<?= htmlspecialchars($gestionBase) ?>/commande/<?= htmlspecialchars($c['numero_commande']) ?>">
                                    <?= htmlspecialchars($c['numero_commande']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($clientNom) ?></td>
                            <td><?= htmlspecialchars(ViewHelper::formatDateHeure($c['date_commande'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($config['statuts'][$c['statut']] ?? $c['statut']) ?></td>
                            <td><?= ViewHelper::prix($c['montant_total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php
$contenu = ob_get_clean();
$scriptsFooter = '<script src="' . BASE_URL . '/js/personnel-commandes.js?v=' . (@filemtime(__DIR__ . '/../../../public/js/personnel-commandes.js') ?: time()) . '" defer></script>';
require __DIR__ . '/../layout.php';
