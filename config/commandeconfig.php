<?php
// Frais de livraison Fashion Paris (simplifié pour le dossier projet)

return [
    'frais_livraison'     => 5.90,
    'seuil_franco'        => 150.00, // livraison offerte au-delà
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
