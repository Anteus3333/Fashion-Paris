<?php
// Livraison Fashion Paris — forfait fixe (dossier projet)

return [
    'frais_livraison'     => 10.00,
    'delai_livraison'     => '7 jours ouvrés',
    // Plus de franco : forfait appliqué dès qu’il y a au moins un article
    'seuil_franco'        => null,
    'statuts'             => [
        'en_attente'   => 'En attente',
        'confirmee'    => 'Confirmée',
        'expediee'     => 'Expédiée',
        'livree'       => 'Livrée',
        'annulee'      => 'Annulée',
    ],
    'modes_paiement'      => [
        'carte'   => 'Carte bancaire (simulation)',
        'paypal'  => 'PayPal (simulation)',
    ],
];
