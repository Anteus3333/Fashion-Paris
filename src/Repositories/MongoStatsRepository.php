<?php
// src/Repositories/MongoStatsRepository.php — accès documents MongoDB (stats)

require_once __DIR__ . '/../Services/MongoDatabase.php';

class MongoStatsRepository {

    public function estDisponible(): bool {
        return MongoDatabase::estConfigure();
    }

    public function remplacerTout(array $documents): int {
        $collection = MongoDatabase::collection();
        $collection->deleteMany([]);
        if (empty($documents)) {
            return 0;
        }
        $result = $collection->insertMany($documents);
        return count($result->getInsertedIds());
    }

    public function compter(): int {
        if (!$this->estDisponible()) {
            return 0;
        }
        return (int) MongoDatabase::collection()->countDocuments();
    }

    public function caParVille(): array {
        if (!$this->estDisponible()) {
            return [];
        }
        $pipeline = [
            ['$match' => ['statut' => ['$ne' => 'annulee']]],
            ['$group' => [
                '_id' => '$ville_livraison',
                'ca' => ['$sum' => '$montant_total'],
                'nb' => ['$sum' => 1],
            ]],
            ['$sort' => ['ca' => -1]],
            ['$limit' => 10],
        ];
        return MongoDatabase::collection()->aggregate($pipeline)->toArray();
    }

    /**
     * Performance 12 mois depuis les docs Mongo (sync MySQL → Mongo).
     * @return array{labels: string[], commandes: int[], ca: float[]}
     */
    public function seriesMensuelles(int $nbMois = 12): array {
        $vide = ['labels' => [], 'commandes' => [], 'ca' => []];
        if (!$this->estDisponible()) {
            return $vide;
        }
        $nbMois = max(1, min(24, $nbMois));

        $pipeline = [
            ['$match' => ['statut' => ['$ne' => 'annulee']]],
            ['$addFields' => [
                'mois' => ['$substr' => ['$date_commande', 0, 7]],
            ]],
            ['$group' => [
                '_id' => '$mois',
                'nb'  => ['$sum' => 1],
                'ca'  => ['$sum' => '$montant_total'],
            ]],
        ];
        $rows = MongoDatabase::collection()->aggregate($pipeline)->toArray();
        $map = [];
        foreach ($rows as $r) {
            $key = (string) ($r['_id'] ?? '');
            if ($key !== '') {
                $map[$key] = $r;
            }
        }

        $labels = [];
        $commandes = [];
        $ca = [];
        $cursor = new DateTime('first day of this month');
        $cursor->modify('-' . ($nbMois - 1) . ' months');
        for ($i = 0; $i < $nbMois; $i++) {
            $key = $cursor->format('Y-m');
            $labels[] = $cursor->format('m/Y');
            $commandes[] = isset($map[$key]) ? (int) $map[$key]['nb'] : 0;
            $ca[] = isset($map[$key]) ? round((float) $map[$key]['ca'], 2) : 0.0;
            $cursor->modify('+1 month');
        }
        return ['labels' => $labels, 'commandes' => $commandes, 'ca' => $ca];
    }
}
