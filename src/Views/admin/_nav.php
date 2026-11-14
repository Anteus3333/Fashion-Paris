<?php
// src/Views/admin/_nav.php — navigation back-office (équivalent ECF admin/_nav)
$urlPath = trim($_GET['url'] ?? 'admin', '/');
$segment = explode('/', $urlPath);
$page = $segment[1] ?? '';
$isProduits = ($page === 'produits') || ($page === 'produit');
$isCommandes = in_array($page, ['commandes', 'commande'], true);
$isPersonnel = ($page === 'personnel') || ($page === 'personnels');
?>
<nav class="admin-nav" aria-label="Administration">
    <a href="<?= BASE_URL ?>/admin" class="<?= $page === '' ? 'is-active' : '' ?>">Tableau de bord</a>
    <a href="<?= BASE_URL ?>/admin/produits" class="<?= $isProduits ? 'is-active' : '' ?>">Produits</a>
    <a href="<?= BASE_URL ?>/admin/commandes" class="<?= $isCommandes ? 'is-active' : '' ?>">Commandes</a>
    <a href="<?= BASE_URL ?>/admin/clients" class="<?= $page === 'clients' ? 'is-active' : '' ?>">Clients</a>
    <a href="<?= BASE_URL ?>/admin/avis" class="<?= $page === 'avis' ? 'is-active' : '' ?>">Avis</a>
    <a href="<?= BASE_URL ?>/admin/personnel" class="<?= $isPersonnel ? 'is-active' : '' ?>">Personnel</a>
    <a href="<?= BASE_URL ?>/admin/horaires" class="<?= $page === 'horaires' ? 'is-active' : '' ?>">Horaires</a>
    <a href="<?= BASE_URL ?>/admin/stats" class="<?= $page === 'stats' ? 'is-active' : '' ?>">Stats Mongo</a>
</nav>
