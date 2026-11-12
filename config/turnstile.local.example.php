<?php
// config/turnstile.local.example.php — copier vers turnstile.local.php
// Créer les clés : https://dash.cloudflare.com/ → Turnstile
// En local Fashion = mêmes clés que Vite & Gourmand (domaine localhost).

return [
    'enabled'    => true,
    // Clés de test Cloudflare (toujours OK) :
    // site  : 1x00000000000000000000AA
    // secret: 1x0000000000000000000000000000000AA
    'site_key'   => 'VOTRE_SITE_KEY',
    'secret_key' => 'VOTRE_SECRET_KEY',
    'widget'     => 'managed',
];
