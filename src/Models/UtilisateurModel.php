<?php
// src/Models/UtilisateurModel.php — couche métier utilisateurs

require_once __DIR__ . '/../Repositories/UtilisateurRepository.php';

class UtilisateurModel {
    private UtilisateurRepository $repository;

    public function __construct() {
        $this->repository = new UtilisateurRepository();
    }

    public function findByEmail(string $email): ?array {
        return $this->repository->findByEmail($email);
    }

    public function findById(int $id): ?array {
        return $this->repository->findById($id);
    }

    public function getRole(int $utilisateurId): ?string {
        return $this->repository->getRole($utilisateurId);
    }

    public function create(array $data): int {
        return $this->repository->create($data);
    }

    public function updateProfil(int $utilisateurId, array $data): void {
        $this->repository->updateProfil($utilisateurId, $data);
    }

    public function getClients(): array {
        return $this->repository->getClients();
    }
}
