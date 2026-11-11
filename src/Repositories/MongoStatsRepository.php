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
}
