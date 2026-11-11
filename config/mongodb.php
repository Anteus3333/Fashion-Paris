<?php
// config/mongodb.php — MongoDB Atlas (stats dashboard admin / NoSQL ECF)
// Copier mongodb.local.example.php → mongodb.local.php et renseigner l'URI.

$config = [
    'uri'        => '',
    'database'   => 'fashion_paris_stats',
    'collection' => 'commandes_stats',
];

$local = __DIR__ . '/mongodb.local.php';
if (is_file($local)) {
    $override = require $local;
    if (is_array($override)) {
        $config = array_replace($config, $override);
    }
}

return $config;
