<?php
require_once __DIR__ . '/../Repositories/AvisRepository.php';

class AvisModel {
    private AvisRepository $repository;

    public function __construct() {
        $this->repository = new AvisRepository();
    }

    public function getValidesParProduit(int $produitId): array {
        return $this->repository->findValidesParProduit($produitId);
    }

    public function creer(array $data): int {
        return $this->repository->create($data);
    }

    public function getEnAttente(): array {
        return $this->repository->findEnAttente();
    }

    public function moderer(int $avisId, string $statut): bool {
        if (!in_array($statut, ['valide', 'refuse'], true)) {
            return false;
        }
        return $this->repository->updateStatut($avisId, $statut);
    }

    public function getAvisValidesAccueil(int $limit = 3): array {
        return $this->repository->getAvisValidesAccueil($limit);
    }

    public function getAll(?string $statut = null): array {
        return $this->repository->getAll($statut);
    }

    public function compterParStatut(string $statut): int {
        return $this->repository->compterParStatut($statut);
    }
}

