<?php
require_once __DIR__ . '/../Repositories/MongoStatsRepository.php';
require_once __DIR__ . '/MysqlStatsModel.php';

class MongoStatsModel {
    private MongoStatsRepository $repository;

    public function __construct() {
        $this->repository = new MongoStatsRepository();
    }

    public function estDisponible(): bool {
        return $this->repository->estDisponible();
    }

    public function syncDepuisMysql(): int {
        $rows = (new MysqlStatsModel())->commandesPourSync();
        $docs = [];
        foreach ($rows as $row) {
            $docs[] = [
                'numero_commande' => $row['numero_commande'],
                'date_commande'   => $row['date_commande'],
                'statut'          => $row['statut'],
                'montant_total'   => (float) $row['montant_total'],
                'frais_livraison' => (float) $row['frais_livraison'],
                'mode_paiement'   => $row['mode_paiement'],
                'ville_livraison' => $row['ville_livraison'],
                'client_email'    => $row['email'],
                'client_nom'      => trim(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? '')),
                'synced_at'       => date('c'),
            ];
        }
        return $this->repository->remplacerTout($docs);
    }

    public function compter(): int {
        return $this->repository->compter();
    }

    public function caParVille(): array {
        return $this->repository->caParVille();
    }
}
