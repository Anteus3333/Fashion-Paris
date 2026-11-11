<?php
// src/Controllers/PanierController.php — panier session

require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Services/PanierService.php';
require_once __DIR__ . '/../Models/ProduitModel.php';

class PanierController {

    public function index() {
        $titrePage = 'Panier — Fashion Paris';
        $detail = $this->construireDetail();
        $lignes = $detail['lignes'];
        $sousTotal = $detail['sous_total'];
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        $frais = ($sousTotal >= (float) $config['seuil_franco'] || $sousTotal <= 0)
            ? 0.0
            : (float) $config['frais_livraison'];
        $total = $sousTotal + $frais;
        require __DIR__ . '/../Views/panier.php';
    }

    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verifier()) {
            $_SESSION['flash_erreur'] = 'Requête invalide.';
            header('Location: ' . BASE_URL . '/catalogue');
            exit;
        }

        $produitId = (int) ($_POST['produit_id'] ?? 0);
        $quantite = max(1, (int) ($_POST['quantite'] ?? 1));
        $produit = (new ProduitModel())->getById($produitId);

        if (!$produit || (int) $produit['actif'] !== 1) {
            $_SESSION['flash_erreur'] = 'Produit introuvable.';
            header('Location: ' . BASE_URL . '/catalogue');
            exit;
        }

        $stock = (int) $produit['stock'];
        $deja = (int) (PanierService::lignes()[$produitId] ?? 0);
        if ($deja + $quantite > $stock) {
            $_SESSION['flash_erreur'] = 'Stock insuffisant pour « ' . $produit['nom'] . ' » (disponible : ' . $stock . ').';
            header('Location: ' . BASE_URL . '/produit/' . rawurlencode($produit['slug']));
            exit;
        }

        PanierService::ajouter($produitId, $quantite);
        $_SESSION['flash_succes'] = '« ' . $produit['nom'] . ' » ajouté au panier.';
        header('Location: ' . BASE_URL . '/panier');
        exit;
    }

    public function modifier() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verifier()) {
            $_SESSION['flash_erreur'] = 'Requête invalide.';
            header('Location: ' . BASE_URL . '/panier');
            exit;
        }

        $produitId = (int) ($_POST['produit_id'] ?? 0);
        $quantite = (int) ($_POST['quantite'] ?? 0);
        $produit = (new ProduitModel())->getById($produitId);

        if (!$produit) {
            PanierService::retirer($produitId);
            header('Location: ' . BASE_URL . '/panier');
            exit;
        }

        if ($quantite > (int) $produit['stock']) {
            $_SESSION['flash_erreur'] = 'Stock insuffisant (max ' . (int) $produit['stock'] . ').';
            header('Location: ' . BASE_URL . '/panier');
            exit;
        }

        PanierService::definir($produitId, $quantite);
        $_SESSION['flash_succes'] = 'Panier mis à jour.';
        header('Location: ' . BASE_URL . '/panier');
        exit;
    }

    public function retirer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verifier()) {
            $_SESSION['flash_erreur'] = 'Requête invalide.';
            header('Location: ' . BASE_URL . '/panier');
            exit;
        }
        PanierService::retirer((int) ($_POST['produit_id'] ?? 0));
        $_SESSION['flash_succes'] = 'Article retiré du panier.';
        header('Location: ' . BASE_URL . '/panier');
        exit;
    }

    /** @return array{lignes: array, sous_total: float} */
    private function construireDetail(): array {
        $model = new ProduitModel();
        $lignes = [];
        $sousTotal = 0.0;

        foreach (PanierService::lignes() as $produitId => $quantite) {
            $produit = $model->getById((int) $produitId);
            if (!$produit || (int) $produit['actif'] !== 1) {
                PanierService::retirer((int) $produitId);
                continue;
            }
            $qte = min((int) $quantite, (int) $produit['stock']);
            if ($qte < 1) {
                PanierService::retirer((int) $produitId);
                continue;
            }
            if ($qte !== (int) $quantite) {
                PanierService::definir((int) $produitId, $qte);
            }
            $ligneTotal = $qte * (float) $produit['prix'];
            $sousTotal += $ligneTotal;
            $lignes[] = [
                'produit' => $produit,
                'quantite' => $qte,
                'ligne_total' => $ligneTotal,
            ];
        }

        return ['lignes' => $lignes, 'sous_total' => $sousTotal];
    }
}
