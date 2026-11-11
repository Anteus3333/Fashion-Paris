<?php
/**
 * Sync MySQL → MongoDB Atlas (stats admin).
 * Usage Docker : docker compose exec web php scripts/sync_stats_mongo.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Models/MongoStatsModel.php';

$model = new MongoStatsModel();
if (!$model->estDisponible()) {
    fwrite(STDERR, "MongoDB non configuré. Copiez config/mongodb.local.example.php → mongodb.local.php\n");
    exit(1);
}

try {
    $n = $model->syncDepuisMysql();
    echo "Sync OK : {$n} document(s) dans MongoDB.\n";
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, 'Erreur sync : ' . $e->getMessage() . "\n");
    exit(1);
}
