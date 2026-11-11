<?php
// src/Models/CategorieModel.php — couche métier catégories

require_once __DIR__ . '/../Repositories/CategorieRepository.php';

class CategorieModel {
    private CategorieRepository $repository;

    public function __construct() {
        $this->repository = new CategorieRepository();
    }

    public function getAll(): array {
        return $this->repository->findAllActives();
    }

    public function getBySlug(string $slug): ?array {
        return $this->repository->findBySlug($slug);
    }
}
