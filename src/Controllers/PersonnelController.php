<?php
// src/Controllers/PersonnelController.php — espace personnel (gestion commandes)

require_once __DIR__ . '/../Services/AuthGuard.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Models/CommandeModel.php';
require_once __DIR__ . '/../Models/UtilisateurModel.php';

class PersonnelController {

    public function index() {
        AuthGuard::exigerRole(AuthGuard::rolesPersonnel(), '/espace-personnel');
        $titrePage = 'Espace personnel — Fashion Paris';
        $statut = trim((string) ($_GET['statut'] ?? ''));
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        if ($statut !== '' && !isset($config['statuts'][$statut])) {
            $statut = '';
        }
        $commandes = (new CommandeModel())->getAll($statut !== '' ? $statut : null);
        $basePath = BASE_URL . '/espace-personnel';
        require __DIR__ . '/../Views/personnel/commandes.php';
    }

    public function commandeDetail(string $numero) {
        AuthGuard::exigerRole(AuthGuard::rolesPersonnel(), '/espace-personnel');
        $model = new CommandeModel();
        $commande = $model->getByNumero($numero);
        if (!$commande) {
            $_SESSION['flash_erreur'] = 'Commande introuvable.';
            header('Location: ' . BASE_URL . '/espace-personnel');
            exit;
        }
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        $erreurs = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée.';
            } else {
                $nouveau = trim((string) ($_POST['statut'] ?? ''));
                $commentaire = trim((string) ($_POST['commentaire'] ?? ''));
                if (!isset($config['statuts'][$nouveau])) {
                    $erreurs[] = 'Statut invalide.';
                } elseif ($model->updateStatut($numero, $nouveau, $commentaire !== '' ? $commentaire : null)) {
                    $_SESSION['flash_succes'] = 'Statut mis à jour.';
                    header('Location: ' . BASE_URL . '/espace-personnel/commande/' . rawurlencode($numero));
                    exit;
                } else {
                    $erreurs[] = 'Impossible de mettre à jour.';
                }
            }
        }

        $lignes = $model->getLignes($numero);
        $suivi = $model->getSuivi($numero);
        $client = (new UtilisateurModel())->findById((int) $commande['utilisateur_id']);
        $titrePage = 'Commande ' . $numero . ' — Personnel';
        $basePath = BASE_URL . '/espace-personnel';
        require __DIR__ . '/../Views/personnel/commande-detail.php';
    }
}
