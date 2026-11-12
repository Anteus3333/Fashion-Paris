<?php
// Copier en mail.local.php (local Docker) et/ou sur Infomaniak.
// Mot de passe = mot de passe d'application Infomaniak (Mail), pas le mdp Manager.

return [
    'contact_email' => 'contact@anteusweb.com',
    'from_email'    => 'contact@anteusweb.com',
    'from_name'     => 'Fashion Paris',

    'smtp' => [
        'enabled'    => true,
        'host'       => 'mail.infomaniak.com',
        'port'       => 587,
        'encryption' => 'tls',
        'username'   => 'contact@anteusweb.com',
        'password'   => 'VOTRE_MOT_DE_PASSE_APPLICATION',
    ],
];
