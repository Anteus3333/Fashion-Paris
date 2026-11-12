<?php
// src/Controllers/CommandeController.php — checkout depuis le panier

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
            $_SESSION['flash_erreur'] = 'Votre panier est vide.';
            header('Location: ' . BASE_URL . '/panier');
            exit;
        }

        $titrePage = 'Commander — Fashion Paris';
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        $utilisateur = (new UtilisateurModel())->findById((int) $_SESSION['utilisateur']['id']);
        $detail = $this->detailPanier();
        $lignes = $detail['lignes'];
        $sousTotal = $detail['sous_total'];
        $commandeModel = new CommandeModel();
        $frais = $commandeModel->calculerFrais($sousTotal);
        $total = $sousTotal + $frais;
        $erreurs = [];

        $old = [
            'adresse_livraison'     => $utilisateur['adresse_postale'] ?? '',
            'code_postal_livraison' => $utilisateur['code_postal'] ?? '',
            'ville_livraison'       => $utilisateur['ville'] ?? '',
            'mode_paiement'         => 'carte',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($old as $champ => $_) {
                $old[$champ] = trim((string) ($_POST[$champ] ?? ''));
            }

            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée, veuillez renvoyer le formulaire.';
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
            if (!isset($config['modes_paiement'][$old['mode_paiement']])) {
                $erreurs[] = 'Mode de paiement invalide.';
            }

            // Revalider stock juste avant création
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
                        'utilisateur_id'         => (int) $_SESSION['utilisateur']['id'],
                        'statut'                 => 'en_attente',
                        'montant_total'          => $sous + $fraisFinal,
                        'frais_livraison'        => $fraisFinal,
                        'adresse_livraison'      => $old['adresse_livraison'],
                        'code_postal_livraison'  => $old['code_postal_livraison'],
                        'ville_livraison'        => $old['ville_livraison'],
                        'mode_paiement'          => $old['mode_paiement'],
                    ], $lignesSql);

                    PanierService::vider();

                    $commandeCreee = $commandeModel->getByNumero($numero);
                    $lignesMail = $commandeModel->getLignes($numero);
                    if ($commandeCreee && $utilisateur) {
                        (new SmtpMailer())->envoyerConfirmationCommande($commandeCreee, $lignesMail, $utilisateur);
                    }

                    $_SESSION['flash_succes'] = 'Commande ' . $numero . ' enregistrée. Un e-mail de confirmation vous a été envoyé (si SMTP configuré).';
                    header('Location: ' . BASE_URL . '/mon-compte/commande/' . rawurlencode($numero));
                    exit;
                } catch (Throwable $e) {
                    $erreurs[] = $e->getMessage();
                }
            }

            // Recalcul affichage après erreur
            $detail = $this->detailPanier();
            $lignes = $detail['lignes'];
            $sousTotal = $detail['sous_total'];
            $frais = $commandeModel->calculerFrais($sousTotal);
            $total = $sousTotal + $frais;
        }

        require __DIR__ . '/../Views/commande.php';
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
