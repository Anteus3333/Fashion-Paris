<?php
// Copier ce fichier en mongodb.local.php (non versionné) et renseigner l'URI Atlas.

return [
    'uri'        => 'mongodb+srv://USER:PASSWORD@CLUSTER.mongodb.net/?retryWrites=true&w=majority',
    'database'   => 'fashion_paris_stats',
    'collection' => 'commandes_stats',
];
