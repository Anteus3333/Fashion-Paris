<?php
// src/Controllers/CatalogueController.php

require_once __DIR__ . '/../Models/ProduitModel.php';
require_once __DIR__ . '/../Models/CategorieModel.php';
require_once __DIR__ . '/../Models/AvisModel.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Services/AuthGuard.php';

class CatalogueController {

    public function index(?string $categorieSlug = null) {
        $titrePage = 'Catalogue — Fashion Paris';
        $categorieModel = new CategorieModel();
        $produitModel = new ProduitModel();

        $categories = $categorieModel->getAll();
        $categorieActive = null;
        $categorieId = null;
        $q = '';

        if ($categorieSlug) {
            $categorieActive = $categorieModel->getBySlug($categorieSlug);
            if (!$categorieActive) {
                $_SESSION['flash_erreur'] = 'Catégorie introuvable.';
                header('Location: ' . BASE_URL . '/catalogue');
                exit;
            }
            $categorieId = (int) $categorieActive['categorie_id'];
            $titrePage = $categorieActive['libelle'] . ' — Fashion Paris';
        }

        $produits = $produitModel->getCatalogue($categorieId);
        require __DIR__ . '/../Views/catalogue.php';
    }

    public function produit(string $slug) {
        $produitModel = new ProduitModel();
        $produit = $produitModel->getBySlug($slug);
        if (!$produit) {
            $_SESSION['flash_erreur'] = 'Produit introuvable.';
            header('Location: ' . BASE_URL . '/catalogue');
            exit;
        }

        $avisModel = new AvisModel();
        $erreursAvis = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'avis') {
            AuthGuard::exigerConnexion('/produit/' . $slug);
            if (!Csrf::verifier()) {
                $erreursAvis[] = 'Session expirée.';
            } else {
                $note = (int) ($_POST['note'] ?? 0);
                $commentaire = trim((string) ($_POST['commentaire'] ?? ''));
                if ($note < 1 || $note > 5) {
                    $erreursAvis[] = 'Note entre 1 et 5.';
                }
                if ($commentaire === '') {
                    $erreursAvis[] = 'Commentaire obligatoire.';
                }
                if (empty($erreursAvis)) {
                    $avisModel->creer([
                        'produit_id'     => (int) $produit['produit_id'],
                        'utilisateur_id' => (int) $_SESSION['utilisateur']['id'],
                        'note'           => $note,
                        'commentaire'    => $commentaire,
                    ]);
                    $_SESSION['flash_succes'] = 'Avis envoyé — en attente de modération.';
                    header('Location: ' . BASE_URL . '/produit/' . rawurlencode($slug));
                    exit;
                }
            }
        }

        $listeAvis = $avisModel->getValidesParProduit((int) $produit['produit_id']);
        $titrePage = $produit['nom'] . ' — Fashion Paris';
        require __DIR__ . '/../Views/produit-detail.php';
    }

    public function recherche() {
        $q = trim((string) ($_GET['q'] ?? ''));
        $titrePage = 'Recherche — Fashion Paris';
        $produitModel = new ProduitModel();
        $produits = $produitModel->rechercher($q);
        $categories = (new CategorieModel())->getAll();
        $categorieActive = null;
        require __DIR__ . '/../Views/catalogue.php';
    }
}
