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

    /** @return array{id: int, token: string} */
    public function create(array $data): array {
        return $this->repository->create($data);
    }

    public function findByValidEmailToken(string $token): ?array {
        return $this->repository->findByValidEmailToken($token);
    }

    public function confirmerEmail(int $utilisateurId): void {
        $this->repository->confirmerEmail($utilisateurId);
    }

    public function estEmailVerifie(array $utilisateur): bool {
        return $this->repository->estEmailVerifie($utilisateur);
    }

    public function reactiverInscriptionNonVerifiee(int $utilisateurId, array $data): ?string {
        return $this->repository->reactiverInscriptionNonVerifiee($utilisateurId, $data);
    }

    public function saveResetToken(int $utilisateurId, string $token): void {
        $this->repository->saveResetToken($utilisateurId, $token);
    }

    public function findByValidResetToken(string $token): ?array {
        return $this->repository->findByValidResetToken($token);
    }

    public function updatePassword(int $utilisateurId, string $nouveauPassword): void {
        $this->repository->updatePassword($utilisateurId, $nouveauPassword);
    }

    public function updateProfil(int $utilisateurId, array $data): void {
        $this->repository->updateProfil($utilisateurId, $data);
    }

    public function getClients(): array {
        return $this->repository->getClients();
    }

    public function getPersonnels(): array {
        return $this->repository->getPersonnels();
    }

    public function estPersonnel(int $utilisateurId): bool {
        return $this->repository->estPersonnel($utilisateurId);
    }

    public function createPersonnel(array $data): int {
        return $this->repository->createPersonnel($data);
    }

    public function updatePersonnel(int $utilisateurId, array $data): bool {
        return $this->repository->updatePersonnel($utilisateurId, $data);
    }

    public function setActifPersonnel(int $utilisateurId, bool $actif): bool {
        return $this->repository->setActifPersonnel($utilisateurId, $actif);
    }

    public function supprimerPersonnel(int $utilisateurId): bool {
        return $this->repository->supprimerPersonnel($utilisateurId);
    }
}

