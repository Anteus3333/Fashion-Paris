<?php
ob_start();
?>
<section class="section page-narrow">
    <h1>Mentions légales</h1>
    <p>Fashion Paris — projet pédagogique (Dossier Projet Studi). Contenu juridique à compléter.</p>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
