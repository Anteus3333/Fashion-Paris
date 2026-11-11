<?php
// src/Controllers/AdminController.php — back-office

require_once __DIR__ . '/../Services/AuthGuard.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Models/ProduitModel.php';
require_once __DIR__ . '/../Models/CategorieModel.php';
require_once __DIR__ . '/../Models/CommandeModel.php';
require_once __DIR__ . '/../Models/UtilisateurModel.php';
require_once __DIR__ . '/../Models/MysqlStatsModel.php';
require_once __DIR__ . '/../Models/AvisModel.php';
require_once __DIR__ . '/../Models/MongoStatsModel.php';

class AdminController {

    public function index() {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $titrePage = 'Administration — Fashion Paris';
        $stats = (new MysqlStatsModel())->resume();
        $commandesRecentes = (new CommandeModel())->getAll();
        $commandesRecentes = array_slice($commandesRecentes, 0, 5);
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        require __DIR__ . '/../Views/admin/index.php';
    }

    public function produits() {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $titrePage = 'Produits — Admin';
        $produits = (new ProduitModel())->getAllAdmin();
        require __DIR__ . '/../Views/admin/produits.php';
    }

    public function produitForm(?int $id = null) {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $model = new ProduitModel();
        $categories = (new CategorieModel())->getAll();
        $produit = $id ? $model->getById($id) : null;
        if ($id && !$produit) {
            $_SESSION['flash_erreur'] = 'Produit introuvable.';
            header('Location: ' . BASE_URL . '/admin/produits');
            exit;
        }

        $titrePage = ($produit ? 'Modifier' : 'Ajouter') . ' un produit — Admin';
        $erreurs = [];
        $old = [
            'categorie_id'  => $produit['categorie_id'] ?? ($categories[0]['categorie_id'] ?? ''),
            'nom'           => $produit['nom'] ?? '',
            'slug'          => $produit['slug'] ?? '',
            'description'   => $produit['description'] ?? '',
            'prix'          => $produit['prix'] ?? '',
            'stock'         => $produit['stock'] ?? '0',
            'sku'           => $produit['sku'] ?? '',
            'actif'         => isset($produit) ? (string) (int) $produit['actif'] : '1',
            'mis_en_avant'  => isset($produit) ? (string) (int) $produit['mis_en_avant'] : '0',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach (['categorie_id', 'nom', 'slug', 'description', 'prix', 'stock', 'sku'] as $champ) {
                $old[$champ] = trim((string) ($_POST[$champ] ?? ''));
            }
            $old['actif'] = isset($_POST['actif']) ? '1' : '0';
            $old['mis_en_avant'] = isset($_POST['mis_en_avant']) ? '1' : '0';

            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée.';
            }
            if ($old['nom'] === '') {
                $erreurs[] = 'Le nom est obligatoire.';
            }
            if ($old['slug'] === '') {
                $old['slug'] = ProduitModel::slugifier($old['nom']);
            } else {
                $old['slug'] = ProduitModel::slugifier($old['slug']);
            }
            if (!is_numeric($old['prix']) || (float) $old['prix'] < 0) {
                $erreurs[] = 'Prix invalide.';
            }
            if (!ctype_digit((string) $old['stock'])) {
                $erreurs[] = 'Stock invalide.';
            }
            if ($model->slugExiste($old['slug'], $id)) {
                $erreurs[] = 'Ce slug existe déjà.';
            }

            if (empty($erreurs)) {
                $payload = [
                    'categorie_id'  => (int) $old['categorie_id'],
                    'nom'           => $old['nom'],
                    'slug'          => $old['slug'],
                    'description'   => $old['description'],
                    'prix'          => (float) $old['prix'],
                    'stock'         => (int) $old['stock'],
                    'sku'           => $old['sku'],
                    'actif'         => (int) $old['actif'],
                    'mis_en_avant'  => (int) $old['mis_en_avant'],
                ];
                if ($id) {
                    $model->update($id, $payload);
                    $_SESSION['flash_succes'] = 'Produit mis à jour.';
                } else {
                    $model->create($payload);
                    $_SESSION['flash_succes'] = 'Produit créé.';
                }
                header('Location: ' . BASE_URL . '/admin/produits');
                exit;
            }
        }

        require __DIR__ . '/../Views/admin/produit-form.php';
    }

    public function commandes() {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $titrePage = 'Commandes — Admin';
        $statut = trim((string) ($_GET['statut'] ?? ''));
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        if ($statut !== '' && !isset($config['statuts'][$statut])) {
            $statut = '';
        }
        $commandes = (new CommandeModel())->getAll($statut !== '' ? $statut : null);
        require __DIR__ . '/../Views/admin/commandes.php';
    }

    public function commandeDetail(string $numero) {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $model = new CommandeModel();
        $commande = $model->getByNumero($numero);
        if (!$commande) {
            $_SESSION['flash_erreur'] = 'Commande introuvable.';
            header('Location: ' . BASE_URL . '/admin/commandes');
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
                    header('Location: ' . BASE_URL . '/admin/commande/' . rawurlencode($numero));
                    exit;
                } else {
                    $erreurs[] = 'Impossible de mettre à jour.';
                }
            }
        }

        $lignes = $model->getLignes($numero);
        $suivi = $model->getSuivi($numero);
        $client = (new UtilisateurModel())->findById((int) $commande['utilisateur_id']);
        $titrePage = 'Commande ' . $numero . ' — Admin';
        require __DIR__ . '/../Views/admin/commande-detail.php';
    }

    public function clients() {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $titrePage = 'Clients — Admin';
        $clients = (new UtilisateurModel())->getClients();
        require __DIR__ . '/../Views/admin/clients.php';
    }

    public function avis() {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $model = new AvisModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::verifier()) {
                $_SESSION['flash_erreur'] = 'Session expirée.';
            } else {
                $id = (int) ($_POST['avis_id'] ?? 0);
                $action = (string) ($_POST['action'] ?? '');
                if ($model->moderer($id, $action)) {
                    $_SESSION['flash_succes'] = 'Avis mis à jour.';
                } else {
                    $_SESSION['flash_erreur'] = 'Action impossible.';
                }
            }
            header('Location: ' . BASE_URL . '/admin/avis');
            exit;
        }

        $titrePage = 'Avis — Admin';
        $avis = $model->getEnAttente();
        require __DIR__ . '/../Views/admin/avis.php';
    }

    public function stats() {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $mongo = new MongoStatsModel();
        $mongoOk = $mongo->estDisponible();
        $nbDocs = 0;
        $parVille = [];

        if ($mongoOk && $_SERVER['REQUEST_METHOD'] === 'POST' && Csrf::verifier() && isset($_POST['sync'])) {
            try {
                $n = $mongo->syncDepuisMysql();
                $_SESSION['flash_succes'] = "Sync effectuée : {$n} document(s).";
            } catch (Throwable $e) {
                $_SESSION['flash_erreur'] = 'Sync échouée : ' . $e->getMessage();
            }
            header('Location: ' . BASE_URL . '/admin/stats');
            exit;
        }

        if ($mongoOk) {
            try {
                $nbDocs = $mongo->compter();
                $parVille = $mongo->caParVille();
            } catch (Throwable $e) {
                $mongoOk = false;
                $_SESSION['flash_erreur'] = 'Mongo : ' . $e->getMessage();
            }
        }

        $titrePage = 'Stats Mongo — Admin';
        require __DIR__ . '/../Views/admin/stats.php';
    }
}
