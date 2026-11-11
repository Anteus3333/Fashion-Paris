<?php
// src/Controllers/CompteController.php — espace client

require_once __DIR__ . '/../Services/AuthGuard.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Models/UtilisateurModel.php';
require_once __DIR__ . '/../Models/CommandeModel.php';

class CompteController {

    public function index() {
        AuthGuard::exigerConnexion('/mon-compte');
        $titrePage = 'Mon compte — Fashion Paris';
        $utilisateur = (new UtilisateurModel())->findById((int) $_SESSION['utilisateur']['id']);
        $commandes = (new CommandeModel())->getByUtilisateur((int) $_SESSION['utilisateur']['id']);
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        require __DIR__ . '/../Views/compte/index.php';
    }

    public function profil() {
        AuthGuard::exigerConnexion('/mon-compte/profil');
        $titrePage = 'Mon profil — Fashion Paris';
        $model = new UtilisateurModel();
        $utilisateur = $model->findById((int) $_SESSION['utilisateur']['id']);
        $erreurs = [];
        $old = [
            'prenom'          => $utilisateur['prenom'] ?? '',
            'nom'             => $utilisateur['nom'] ?? '',
            'telephone'       => $utilisateur['telephone'] ?? '',
            'adresse_postale' => $utilisateur['adresse_postale'] ?? '',
            'code_postal'     => $utilisateur['code_postal'] ?? '',
            'ville'           => $utilisateur['ville'] ?? '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($old as $champ => $_) {
                $old[$champ] = trim((string) ($_POST[$champ] ?? ''));
            }
            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée.';
            }
            if ($old['prenom'] === '' || $old['nom'] === '') {
                $erreurs[] = 'Nom et prénom obligatoires.';
            }
            if (!preg_match('/^\d{5}$/', $old['code_postal'])) {
                $erreurs[] = 'Code postal invalide.';
            }
            if (empty($erreurs)) {
                $model->updateProfil((int) $_SESSION['utilisateur']['id'], $old);
                $_SESSION['utilisateur']['prenom'] = $old['prenom'];
                $_SESSION['utilisateur']['nom'] = $old['nom'];
                $_SESSION['flash_succes'] = 'Profil mis à jour.';
                header('Location: ' . BASE_URL . '/mon-compte');
                exit;
            }
        }

        require __DIR__ . '/../Views/compte/profil.php';
    }

    public function commandeDetail(string $numero) {
        AuthGuard::exigerConnexion('/mon-compte');
        $model = new CommandeModel();
        $commande = $model->getByNumeroPourUtilisateur($numero, (int) $_SESSION['utilisateur']['id']);
        if (!$commande) {
            $_SESSION['flash_erreur'] = 'Commande introuvable.';
            header('Location: ' . BASE_URL . '/mon-compte');
            exit;
        }
        $lignes = $model->getLignes($numero);
        $suivi = $model->getSuivi($numero);
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        $titrePage = 'Commande ' . $numero . ' — Fashion Paris';
        require __DIR__ . '/../Views/compte/commande-detail.php';
    }
}
