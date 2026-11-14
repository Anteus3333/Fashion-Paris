<?php
// src/Controllers/CatalogueController.php

require_once __DIR__ . '/../Models/ProduitModel.php';
require_once __DIR__ . '/../Models/CategorieModel.php';

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

    /** API JSON filtres catalogue (AJAX). */
    public function filterApi(): void {
        header('Content-Type: application/json; charset=utf-8');
        $categorieModel = new CategorieModel();
        $produitModel = new ProduitModel();

        $slug = trim((string) ($_GET['categorie'] ?? ''));
        $q = trim((string) ($_GET['q'] ?? ''));
        $prixMin = isset($_GET['prix_min']) && $_GET['prix_min'] !== '' ? (float) $_GET['prix_min'] : null;
        $prixMax = isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? (float) $_GET['prix_max'] : null;

        $categorieId = null;
        if ($slug !== '') {
            $cat = $categorieModel->getBySlug($slug);
            if ($cat) {
                $categorieId = (int) $cat['categorie_id'];
            }
        }

        $produits = $produitModel->filtrer($categorieId, $q, $prixMin, $prixMax);
        ob_start();
        if (empty($produits)) {
            require __DIR__ . '/../Views/partials/catalogue-empty.php';
        } else {
            require __DIR__ . '/../Views/partials/catalogue-cards.php';
        }
        $html = ob_get_clean();

        echo json_encode([
            'success' => true,
            'html'    => $html,
            'count'   => count($produits),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
