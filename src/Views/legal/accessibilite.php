<?php
ob_start();
?>
<section class="section page-narrow">
    <h1>Accessibilité</h1>
    <p>Déclaration d’accessibilité à rédiger (skip-link, labels, contrastes — déjà amorcés dans le layout).</p>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
