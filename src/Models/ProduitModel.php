<?php
// src/Models/ProduitModel.php — couche métier catalogue

require_once __DIR__ . '/../Repositories/ProduitRepository.php';

class ProduitModel {
    private ProduitRepository $repository;

    public function __construct() {
        $this->repository = new ProduitRepository();
    }

    public function getById(int $id): ?array {
        return $this->repository->findById($id);
    }

    public function getMisEnAvant(int $limit = 6): array {
        return $this->repository->findMisEnAvant($limit);
    }

    public function getCatalogue(?int $categorieId = null): array {
        return $this->repository->findAllActifs($categorieId);
    }

    public function getBySlug(string $slug): ?array {
        return $this->repository->findBySlug($slug);
    }

    public function rechercher(string $q): array {
        $q = trim($q);
        if ($q === '') {
            return [];
        }
        return $this->repository->search($q);
    }

    public function getAllAdmin(): array {
        return $this->repository->findAllAdmin();
    }

    public function create(array $data): int {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool {
        return $this->repository->update($id, $data);
    }

    public function remplacerImagePrincipale(int $produitId, string $chemin, string $alt): ?string {
        return $this->repository->remplacerImagePrincipale($produitId, $chemin, $alt);
    }

    public function slugExiste(string $slug, ?int $exclureId = null): bool {
        return $this->repository->slugExiste($slug, $exclureId);
    }

    public static function slugifier(string $texte): string {
        $texte = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texte);
        $texte = strtolower((string) $texte);
        $texte = preg_replace('/[^a-z0-9]+/', '-', $texte) ?? '';
        return trim($texte, '-') ?: 'produit';
    }
}
