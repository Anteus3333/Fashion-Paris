<?php
// src/Models/CommandeModel.php — règles métier commandes

require_once __DIR__ . '/../Repositories/CommandeRepository.php';

class CommandeModel {
    private CommandeRepository $repository;

    public function __construct() {
        $this->repository = new CommandeRepository();
    }

    public function creer(array $commande, array $lignes): string {
        return $this->repository->creer($commande, $lignes);
    }

    public function getByUtilisateur(int $utilisateurId): array {
        return $this->repository->getByUtilisateur($utilisateurId);
    }

    public function getByNumeroPourUtilisateur(string $numero, int $utilisateurId): ?array {
        return $this->repository->getByNumeroPourUtilisateur($numero, $utilisateurId);
    }

    public function getByNumero(string $numero): ?array {
        return $this->repository->getByNumero($numero);
    }

    public function getLignes(string $numero): array {
        return $this->repository->getLignes($numero);
    }

    public function getSuivi(string $numero): array {
        return $this->repository->getSuivi($numero);
    }

    public function getAll(?string $statut = null): array {
        return $this->repository->getAll($statut);
    }

    public function updateStatut(string $numero, string $statut, ?string $commentaire = null): bool {
        return $this->repository->updateStatut($numero, $statut, $commentaire);
    }

    public function calculerFrais(float $sousTotal): float {
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        if ($sousTotal <= 0 || $sousTotal >= (float) $config['seuil_franco']) {
            return 0.0;
        }
        return (float) $config['frais_livraison'];
    }
}
