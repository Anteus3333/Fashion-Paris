<?php
// src/Controllers/CommandeController.php — checkout en étapes (livraison → paiement)

require_once __DIR__ . '/../Services/AuthGuard.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Services/PanierService.php';
require_once __DIR__ . '/../Models/CommandeModel.php';
require_once __DIR__ . '/../Models/ProduitModel.php';
require_once __DIR__ . '/../Models/UtilisateurModel.php';
require_once __DIR__ . '/../Services/SmtpMailer.php';

class CommandeController {

    public function index() {
        AuthGuard::exigerConnexion('/commande');

        if (empty(PanierService::lignes())) {
            unset($_SESSION['checkout']);
            $_SESSION['flash_erreur'] = 'Votre panier est vide.';
            header('Location: ' . BASE_URL . '/panier');
            exit;
        }

        $titrePage = 'Commander — Fashion Paris';
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        $utilisateurModel = new UtilisateurModel();
        $utilisateur = $utilisateurModel->findById((int) $_SESSION['utilisateur']['id']);
        $detail = $this->detailPanier();
        $lignes = $detail['lignes'];
        $sousTotal = $detail['sous_total'];
        $commandeModel = new CommandeModel();
        $frais = $commandeModel->calculerFrais($sousTotal);
        $total = $sousTotal + $frais;
        $erreurs = [];

        $brouillon = $_SESSION['checkout'] ?? [];
        $old = [
            'telephone'             => $brouillon['telephone'] ?? ($utilisateur['telephone'] ?? ''),
            'adresse_livraison'     => $brouillon['adresse_livraison'] ?? ($utilisateur['adresse_postale'] ?? ''),
            'code_postal_livraison' => $brouillon['code_postal_livraison'] ?? ($utilisateur['code_postal'] ?? ''),
            'ville_livraison'       => $brouillon['ville_livraison'] ?? ($utilisateur['ville'] ?? ''),
            'mode_paiement'         => $brouillon['mode_paiement'] ?? 'carte',
            'accepte_cgv'           => !empty($brouillon['accepte_cgv']),
        ];

        $etapeGet = (string) ($_GET['etape'] ?? '');
        if ($etapeGet === 'paiement') {
            $etape = 'paiement';
        } elseif ($etapeGet === 'livraison') {
            $etape = 'livraison';
        } else {
            // Reprise : mémorise la dernière étape atteinte (après un détour panier / CGV)
            $etapeMemoire = (string) ($_SESSION['checkout']['etape'] ?? 'livraison');
            $etape = ($etapeMemoire === 'paiement' && !empty($_SESSION['checkout']['adresse_livraison']))
                ? 'paiement'
                : 'livraison';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée, veuillez renvoyer le formulaire.';
            }

            $action = (string) ($_POST['action'] ?? '');

            if ($action === 'livraison' && empty($erreurs)) {
                $old['telephone'] = trim((string) ($_POST['telephone'] ?? ''));
                $old['adresse_livraison'] = trim((string) ($_POST['adresse_livraison'] ?? ''));
                $old['code_postal_livraison'] = trim((string) ($_POST['code_postal_livraison'] ?? ''));
                $old['ville_livraison'] = trim((string) ($_POST['ville_livraison'] ?? ''));

                $erreurs = array_merge($erreurs, $this->validerLivraison($old));

                if (empty($erreurs)) {
                    $_SESSION['checkout'] = array_merge($brouillon, [
                        'telephone'             => $old['telephone'],
                        'adresse_livraison'     => $old['adresse_livraison'],
                        'code_postal_livraison' => $old['code_postal_livraison'],
                        'ville_livraison'       => $old['ville_livraison'],
                    ]);
                    header('Location: ' . BASE_URL . '/commande?etape=paiement');
                    exit;
                }
                $etape = 'livraison';
            }

            if ($action === 'confirmer') {
                if (empty($_SESSION['checkout']['adresse_livraison'])) {
                    $_SESSION['flash_erreur'] = 'Complétez d’abord vos informations de livraison.';
                    header('Location: ' . BASE_URL . '/commande');
                    exit;
                }

                $old = array_merge($old, $_SESSION['checkout']);
                $old['mode_paiement'] = trim((string) ($_POST['mode_paiement'] ?? 'carte'));
                $old['accepte_cgv'] = isset($_POST['accepte_cgv']);

                if (!isset($config['modes_paiement'][$old['mode_paiement']])) {
                    $erreurs[] = 'Mode de paiement invalide.';
                }
                if (empty($old['accepte_cgv'])) {
                    $erreurs[] = 'Vous devez accepter les CGV pour confirmer la commande.';
                }

                $produitModel = new ProduitModel();
                $lignesSql = [];
                foreach (PanierService::lignes() as $pid => $qte) {
                    $p = $produitModel->getById((int) $pid);
                    if (!$p || (int) $p['actif'] !== 1 || (int) $p['stock'] < (int) $qte) {
                        $erreurs[] = 'Stock insuffisant pour un article. Vérifiez votre panier.';
                        break;
                    }
                    $lignesSql[] = [
                        'produit_id'    => (int) $pid,
                        'quantite'      => (int) $qte,
                        'prix_unitaire' => (float) $p['prix'],
                    ];
                }

                if (empty($erreurs) && empty($lignesSql)) {
                    $erreurs[] = 'Panier invalide.';
                }

                if (empty($erreurs)) {
                    $sous = 0.0;
                    foreach ($lignesSql as $l) {
                        $sous += $l['quantite'] * $l['prix_unitaire'];
                    }
                    $fraisFinal = $commandeModel->calculerFrais($sous);

                    try {
                        $numero = $commandeModel->creer([
                            'utilisateur_id'        => (int) $_SESSION['utilisateur']['id'],
                            'statut'                => 'en_attente',
                            'montant_total'         => $sous + $fraisFinal,
                            'frais_livraison'       => $fraisFinal,
                            'adresse_livraison'     => $old['adresse_livraison'],
                            'code_postal_livraison' => $old['code_postal_livraison'],
                            'ville_livraison'       => $old['ville_livraison'],
                            'mode_paiement'         => $old['mode_paiement'],
                        ], $lignesSql);

                        $utilisateurModel->updateProfil((int) $_SESSION['utilisateur']['id'], [
                            'prenom'          => $utilisateur['prenom'] ?? ($_SESSION['utilisateur']['prenom'] ?? ''),
                            'nom'             => $utilisateur['nom'] ?? ($_SESSION['utilisateur']['nom'] ?? ''),
                            'telephone'       => $old['telephone'],
                            'adresse_postale' => $old['adresse_livraison'],
                            'code_postal'     => $old['code_postal_livraison'],
                            'ville'           => $old['ville_livraison'],
                        ]);

                        unset($_SESSION['checkout']);
                        PanierService::vider();

                        $commandeCreee = $commandeModel->getByNumero($numero);
                        $lignesMail = $commandeModel->getLignes($numero);
                        $utilisateurMail = $utilisateurModel->findById((int) $_SESSION['utilisateur']['id']) ?: $utilisateur;
                        if ($commandeCreee && $utilisateurMail) {
                            (new SmtpMailer())->envoyerConfirmationCommande($commandeCreee, $lignesMail, $utilisateurMail);
                        }

                        $_SESSION['flash_succes'] = 'Commande ' . $numero . ' enregistrée. Un e-mail de confirmation vous a été envoyé (si SMTP configuré).';
                        header('Location: ' . BASE_URL . '/mon-compte/commande/' . rawurlencode($numero));
                        exit;
                    } catch (Throwable $e) {
                        $erreurs[] = $e->getMessage();
                    }
                }

                $etape = 'paiement';
                $detail = $this->detailPanier();
                $lignes = $detail['lignes'];
                $sousTotal = $detail['sous_total'];
                $frais = $commandeModel->calculerFrais($sousTotal);
                $total = $sousTotal + $frais;
            }
        }

        if ($etape === 'paiement' && empty($_SESSION['checkout']['adresse_livraison'])) {
            if (!isset($_SESSION['checkout']) || !is_array($_SESSION['checkout'])) {
                $_SESSION['checkout'] = [];
            }
            $_SESSION['checkout']['etape'] = 'livraison';
            header('Location: ' . BASE_URL . '/commande?etape=livraison');
            exit;
        }

        if ($etape === 'paiement') {
            $old = array_merge($old, $_SESSION['checkout'] ?? []);
        }

        if (!isset($_SESSION['checkout']) || !is_array($_SESSION['checkout'])) {
            $_SESSION['checkout'] = [];
        }
        $_SESSION['checkout']['etape'] = $etape;
        $etapeAtteinte = $etape;

        require __DIR__ . '/../Views/commande.php';
    }

    /** API JSON frais de livraison (AJAX checkout). */
    public function calculFraisApi(): void {
        header('Content-Type: application/json; charset=utf-8');
        $sousTotal = isset($_GET['sous_total']) ? (float) $_GET['sous_total'] : 0.0;
        $detail = (new CommandeModel())->detailFrais($sousTotal);
        echo json_encode([
            'success'          => true,
            'sous_total'       => $detail['sous_total'],
            'frais'            => $detail['frais'],
            'total'            => $detail['total'],
            'franco'           => false,
            'delai_livraison'  => $detail['delai_livraison'] ?? '7 jours ouvrés',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** @return list<string> */
    private function validerLivraison(array $old): array {
        $erreurs = [];
        if ($old['telephone'] === '') {
            $erreurs[] = 'Le téléphone est obligatoire.';
        }
        if ($old['adresse_livraison'] === '') {
            $erreurs[] = 'L\'adresse de livraison est obligatoire.';
        }
        if (!preg_match('/^\d{5}$/', $old['code_postal_livraison'])) {
            $erreurs[] = 'Le code postal doit contenir 5 chiffres.';
        }
        if ($old['ville_livraison'] === '') {
            $erreurs[] = 'La ville est obligatoire.';
        }
        return $erreurs;
    }

    /** @return array{lignes: array, sous_total: float} */
    private function detailPanier(): array {
        $model = new ProduitModel();
        $lignes = [];
        $sousTotal = 0.0;
        foreach (PanierService::lignes() as $pid => $qte) {
            $p = $model->getById((int) $pid);
            if (!$p) {
                continue;
            }
            $ligneTotal = (int) $qte * (float) $p['prix'];
            $sousTotal += $ligneTotal;
            $lignes[] = ['produit' => $p, 'quantite' => (int) $qte, 'ligne_total' => $ligneTotal];
        }
        return ['lignes' => $lignes, 'sous_total' => $sousTotal];
    }
}
