<?php
// config/mail.php — envoi d'e-mails (SMTP Infomaniak)
// Surcharge locale : créer mail.local.php (non versionné).

return [
    'contact_email' => 'contact@anteusweb.com',
    'from_email'    => 'contact@anteusweb.com',
    'from_name'     => 'Fashion Paris',

    'smtp' => [
        'enabled'    => false,
        'host'       => 'mail.infomaniak.com',
        'port'       => 587,
        'encryption' => 'tls', // STARTTLS
        'username'   => 'contact@anteusweb.com',
        'password'   => '',
    ],
];
