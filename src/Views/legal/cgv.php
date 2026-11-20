<?php
ob_start();
$retourPaiement = !empty($retourPaiement);
?>
<?php if ($retourPaiement): ?>
    <div class="legal-checkout-bar" role="region" aria-label="Retour à la commande">
        <a class="btn btn-primary" href="<?= BASE_URL ?>/commande?etape=paiement">Retour au paiement</a>
    </div>
<?php endif; ?>
<section class="legal-hero">
    <h1>Conditions générales de vente</h1>
    <p>Conditions applicables aux commandes passées sur Fashion Paris.</p>
</section>

<section class="legal-page">
    <article class="legal-content">
        <h2>1. Objet</h2>
        <p>
            Les présentes conditions générales de vente (CGV) régissent les ventes de produits
            de prêt-à-porter et d’accessoires proposés sur le site Fashion Paris
            à toute personne physique agissant à des fins non professionnelles (ci-après « le Client »).
        </p>

        <h2>2. Produits</h2>
        <p>
            Les produits sont présentés avec une description, un prix TTC, un stock indicatif
            et, le cas échéant, une catégorie (Femme, Homme, Accessoires).
            Les photographies sont non contractuelles ; Fashion Paris s’efforce de les rendre fidèles.
        </p>

        <h2>3. Commande</h2>
        <p>
            La commande suppose un compte client, l’ajout d’articles au panier, puis la validation
            du récapitulatif. Un e-mail de confirmation est adressé au Client lorsque le service
            d’envoi est configuré. Tant que la commande n’est pas expédiée, son suivi est consultable
            depuis l’espace « Mes commandes ».
        </p>

        <h2>4. Prix et disponibilité</h2>
        <ul>
            <li>Les prix affichés sont en euros, toutes taxes comprises.</li>
            <li>Fashion Paris se réserve le droit de modifier les prix à tout moment ; le prix applicable est celui en vigueur au moment de la validation de la commande.</li>
            <li>En cas d’indisponibilité après commande, le Client est informé et remboursé le cas échéant selon les modalités convenues.</li>
        </ul>

        <h2>5. Paiement</h2>
        <p>
            Les modalités de paiement (moyens acceptés, échéances) sont indiquées lors du checkout
            ou confirmées par e-mail. La commande n’est considérée comme ferme qu’après validation
            du paiement selon le processus en place sur le site.
        </p>

        <h2>6. Livraison</h2>
        <p>
            Les délais et zones de livraison sont communiqués au Client lors de la commande
            ou dans l’espace de suivi. Fashion Paris ne peut être tenu responsable des retards
            imputables au transporteur, sous réserve des dispositions légales applicables.
        </p>

        <h2>7. Droit de rétractation</h2>
        <p>
            Conformément au Code de la consommation, le Client consommateur dispose en principe
            d’un délai de quatorze (14) jours pour exercer son droit de rétractation, hors exceptions
            légales (produits scellés ouverts, articles personnalisés, etc.).
            Les modalités de retour sont précisées sur demande via
            <a href="<?= BASE_URL ?>/contact">la page Contact</a>.
        </p>

        <h2>8. Responsabilité</h2>
        <p>
            Fashion Paris est responsable dans les conditions prévues par la loi.
            Les informations produit sont fournies de bonne foi ; le Client reste responsable
            du choix et de l’usage des articles commandés.
        </p>

        <h2>9. Litiges</h2>
        <p>
            En cas de litige, une solution amiable sera recherchée en priorité
            (<a href="mailto:contact@anteusweb.com">contact@anteusweb.com</a>).
            À défaut, le droit français s’applique et les tribunaux français sont compétents,
            sous réserve des règles protectrices du consommateur.
        </p>

        <p class="legal-meta">Dernière mise à jour : novembre 2026</p>
        <?php if ($retourPaiement): ?>
            <p class="legal-back">
                <a class="btn btn-primary" href="<?= BASE_URL ?>/commande?etape=paiement">← Retour au paiement</a>
            </p>
        <?php else: ?>
            <p class="legal-back"><a href="<?= BASE_URL ?>/">← Retour à l’accueil</a></p>
        <?php endif; ?>
    </article>
</section>
<?php
$contenu = ob_get_clean();
require __DIR__ . '/../layout.php';
