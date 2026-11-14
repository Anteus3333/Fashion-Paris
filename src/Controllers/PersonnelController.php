<?php
// src/Controllers/PersonnelController.php — espace personnel (commandes + avis)

require_once __DIR__ . '/../Services/AuthGuard.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Models/CommandeModel.php';
require_once __DIR__ . '/../Models/AvisModel.php';
require_once __DIR__ . '/Traits/GestionCommandesTrait.php';

class PersonnelController {
    use GestionCommandesTrait;

    /** Tableau de bord. */
    public function index(): void {
        $this->exigerGestionAcces();
        $commandeModel = new CommandeModel();
        $avisModel = new AvisModel();

        $commandesRecentes = array_slice($commandeModel->getAll('en_attente'), 0, 5);
        $enAttente = count($commandeModel->getAll('en_attente'));
        $enCoursNb = count($commandeModel->getAll('confirmee')) + count($commandeModel->getAll('expediee'));
        $avisAttente = $avisModel->compterParStatut('en_attente');
        $avisRecents = array_slice($avisModel->getAll('en_attente'), 0, 5);

        $titrePage = 'Espace personnel — Fashion Paris';
        require __DIR__ . '/../Views/personnel/index.php';
    }

    public function avis(): void {
        $this->exigerGestionAcces();
        $model = new AvisModel();
        $filtre = trim((string) ($_GET['statut'] ?? 'en_attente'));
        if (!in_array($filtre, ['en_attente', 'valide', 'refuse', 'tous'], true)) {
            $filtre = 'en_attente';
        }
        $filtreActif = $filtre;
        $avis = $model->getAll($filtre === 'tous' ? null : $filtre);
        $nbAttente = $model->compterParStatut('en_attente');
        $titrePage = 'Avis — Espace personnel';
        require __DIR__ . '/../Views/personnel/avis.php';
    }

    public function avisValider(int $avisId): void {
        $this->modererAvis($avisId, 'valide');
    }

    public function avisRefuser(int $avisId): void {
        $this->modererAvis($avisId, 'refuse');
    }

    private function modererAvis(int $avisId, string $statut): void {
        $this->exigerGestionAcces();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verifier()) {
            $_SESSION['flash_erreur'] = 'Action non autorisée.';
            header('Location: ' . BASE_URL . '/espace-personnel/avis');
            exit;
        }
        if ((new AvisModel())->moderer($avisId, $statut)) {
            $_SESSION['flash_succes'] = $statut === 'valide' ? 'Avis publié.' : 'Avis refusé.';
        } else {
            $_SESSION['flash_erreur'] = 'Impossible de mettre à jour cet avis.';
        }
        header('Location: ' . BASE_URL . '/espace-personnel/avis?statut=en_attente');
        exit;
    }

    protected function gestionBasePath(): string {
        return '/espace-personnel';
    }

    protected function gestionCommandesListPath(): string {
        return '/commandes';
    }

    protected function gestionTitreEspace(): string {
        return 'Espace personnel';
    }

    protected function exigerGestionAcces(): void {
        AuthGuard::exigerRole(AuthGuard::rolesPersonnel(), '/espace-personnel');
    }
}
