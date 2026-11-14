<?php
// src/Views/personnel/_nav.php — navigation espace personnel (équivalent ECF emp__nav)
$urlPath = trim($_GET['url'] ?? 'espace-personnel', '/');
$segment = explode('/', $urlPath);
$page = $segment[1] ?? '';
$isCommandes = in_array($page, ['commandes', 'commande'], true);
$isDashboard = $page === '' || $page === null;
?>
<nav class="personnel-nav" aria-label="Espace personnel">
    <a href="<?= BASE_URL ?>/espace-personnel" class="<?= $isDashboard ? 'is-active' : '' ?>">Tableau de bord</a>
    <a href="<?= BASE_URL ?>/espace-personnel/commandes" class="<?= $isCommandes ? 'is-active' : '' ?>">Commandes</a>
    <a href="<?= BASE_URL ?>/espace-personnel/avis" class="<?= $page === 'avis' ? 'is-active' : '' ?>">Avis</a>
</nav>
