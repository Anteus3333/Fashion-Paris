<?php
// src/Router.php — routeur manuel (même principe que Studi_ECF)

require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/CatalogueController.php';
require_once __DIR__ . '/Controllers/PanierController.php';
require_once __DIR__ . '/Controllers/CommandeController.php';
require_once __DIR__ . '/Controllers/CompteController.php';
require_once __DIR__ . '/Controllers/AdminController.php';
require_once __DIR__ . '/Controllers/LegalController.php';
require_once __DIR__ . '/Controllers/NewsletterController.php';

class Router {

    public function dispatch($url) {
        $parts = explode('/', trim($url, '/'));
        $route = $parts[0] ?? '';
        $param = $parts[1] ?? null;
        $param2 = $parts[2] ?? null;

        switch ($route) {
            case '':
            case 'accueil':
                (new HomeController())->index();
                break;

            case 'catalogue':
                if ($param === 'recherche') {
                    (new CatalogueController())->recherche();
                } elseif ($param) {
                    (new CatalogueController())->index($param);
                } else {
                    (new CatalogueController())->index();
                }
                break;

            case 'produit':
                if ($param) {
                    (new CatalogueController())->produit($param);
                } else {
                    header('Location: ' . BASE_URL . '/catalogue');
                    exit;
                }
                break;

            case 'panier':
                if ($param === 'ajouter') {
                    (new PanierController())->ajouter();
                } elseif ($param === 'modifier') {
                    (new PanierController())->modifier();
                } elseif ($param === 'retirer') {
                    (new PanierController())->retirer();
                } else {
                    (new PanierController())->index();
                }
                break;

            case 'commande':
                (new CommandeController())->index();
                break;

            case 'login':
                (new AuthController())->login();
                break;

            case 'inscription':
                (new AuthController())->register();
                break;

            case 'deconnexion':
                (new AuthController())->logout();
                break;

            case 'newsletter':
                if ($param === 'inscrire') {
                    (new NewsletterController())->inscrire();
                } else {
                    header('Location: ' . BASE_URL . '/');
                    exit;
                }
                break;

            case 'mon-compte':
                if ($param === 'profil') {
                    (new CompteController())->profil();
                } elseif ($param === 'commande' && $param2) {
                    (new CompteController())->commandeDetail($param2);
                } else {
                    (new CompteController())->index();
                }
                break;

            case 'admin':
                if ($param === 'produits') {
                    (new AdminController())->produits();
                } elseif ($param === 'produit' && $param2 === 'nouveau') {
                    (new AdminController())->produitForm(null);
                } elseif ($param === 'produit' && $param2) {
                    (new AdminController())->produitForm((int) $param2);
                } elseif ($param === 'commandes') {
                    (new AdminController())->commandes();
                } elseif ($param === 'commande' && $param2) {
                    (new AdminController())->commandeDetail($param2);
                } elseif ($param === 'clients') {
                    (new AdminController())->clients();
                } elseif ($param === 'avis') {
                    (new AdminController())->avis();
                } elseif ($param === 'stats') {
                    (new AdminController())->stats();
                } else {
                    (new AdminController())->index();
                }
                break;

            case 'mentions-legales':
                (new LegalController())->mentions();
                break;

            case 'accessibilite':
                (new LegalController())->accessibilite();
                break;

            default:
                http_response_code(404);
                $titrePage = 'Page introuvable — Fashion Paris';
                $contenu = '<section class="page-narrow"><h1>404</h1><p>Page introuvable.</p></section>';
                require __DIR__ . '/Views/layout.php';
                break;
        }
    }
}
