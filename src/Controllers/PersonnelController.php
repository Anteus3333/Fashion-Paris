<?php
// src/Controllers/PersonnelController.php — espace personnel (commandes)

require_once __DIR__ . '/../Services/AuthGuard.php';
require_once __DIR__ . '/../Models/CommandeModel.php';
require_once __DIR__ . '/Traits/GestionCommandesTrait.php';

class PersonnelController {
    use GestionCommandesTrait;

    /** Tableau de bord. */
    public function index(): void {
        $this->exigerGestionAcces();
        $commandeModel = new CommandeModel();

        $commandesRecentes = array_slice($commandeModel->getAll('en_attente'), 0, 5);
        $enAttente = count($commandeModel->getAll('en_attente'));
        $enCoursNb = count($commandeModel->getAll('confirmee')) + count($commandeModel->getAll('expediee'));

        $titrePage = 'Espace personnel — Fashion Paris';
        require __DIR__ . '/../Views/personnel/index.php';
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
