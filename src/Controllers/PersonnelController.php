<?php
// src/Controllers/PersonnelController.php — espace personnel (gestion commandes)

require_once __DIR__ . '/../Services/AuthGuard.php';
require_once __DIR__ . '/Traits/GestionCommandesTrait.php';

class PersonnelController {
    use GestionCommandesTrait;

    /** Accueil espace = liste des commandes. */
    public function index(): void {
        $this->commandes();
    }

    protected function gestionBasePath(): string {
        return '/espace-personnel';
    }

    protected function gestionCommandesListPath(): string {
        return '';
    }

    protected function gestionTitreEspace(): string {
        return 'Espace personnel';
    }

    protected function exigerGestionAcces(): void {
        AuthGuard::exigerRole(AuthGuard::rolesPersonnel(), '/espace-personnel');
    }
}
