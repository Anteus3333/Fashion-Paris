<?php
// Copier en database.local.php (local Docker) ou sur Infomaniak.
// Ne jamais versionner database.local.php.

// --- Docker local ---
// return [
//     'host'     => 'db',
//     'db_name'  => 'fashion_paris',
//     'username' => 'fashion',
//     'password' => 'fashion',
// ];

// --- Infomaniak (Manager → Bases de données) ---
// Hôte = xxx.myd.infomaniak.com (PAS le FTP)
return [
    'host'     => 'gkhl.myd.infomaniak.com',
    'db_name'  => 'gkhl_fashion_paris',
    'username' => 'REMPLACER_USER_MYSQL',
    'password' => 'REMPLACER_MDP_MYSQL',
];
