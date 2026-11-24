<?php
// src/Controllers/Traits/GestionCommandesTrait.php — commandes partagées admin + personnel

require_once __DIR__ . '/../../Services/Csrf.php';
require_once __DIR__ . '/../../Services/SmtpMailer.php';
require_once __DIR__ . '/../../Models/CommandeModel.php';
require_once __DIR__ . '/../../Models/UtilisateurModel.php';

trait GestionCommandesTrait {

    abstract protected function gestionBasePath(): string;

    abstract protected function exigerGestionAcces(): void;

    abstract protected function gestionTitreEspace(): string;

    /** Suffixe liste : '/commandes' (admin) ou '' (personnel). */
    abstract protected function gestionCommandesListPath(): string;

    protected function gestionBaseUrl(): string {
        return BASE_URL . $this->gestionBasePath();
    }

    protected function gestionCommandesListUrl(): string {
        return $this->gestionBaseUrl() . $this->gestionCommandesListPath();
    }

    protected function gestionCommandeDetailUrl(string $numero): string {
        return $this->gestionBaseUrl() . '/commande/' . rawurlencode($numero);
    }

    /** Liste des commandes (filtre statut + recherche client en JS). */
    public function commandes(): void {
        $this->exigerGestionAcces();
        $titrePage = 'Commandes — ' . $this->gestionTitreEspace();
        $statut = trim((string) ($_GET['statut'] ?? ''));
        $config = require __DIR__ . '/../../../config/commandeconfig.php';
        if ($statut !== '' && !isset($config['statuts'][$statut])) {
            $statut = '';
        }
        $commandes = (new CommandeModel())->getAll($statut !== '' ? $statut : null);
        $gestionBase = $this->gestionBaseUrl();
        $commandesListUrl = $this->gestionCommandesListUrl();
        $titreEspace = $this->gestionTitreEspace();
        require __DIR__ . '/../../Views/personnel/commandes.php';
    }

    /** Détail + changement de statut (POST CSRF). */
    public function commandeDetail(string $numero): void {
        $this->exigerGestionAcces();
        $model = new CommandeModel();
        $commande = $model->getByNumero($numero);
        if (!$commande) {
            $_SESSION['flash_erreur'] = 'Commande introuvable.';
            header('Location: ' . $this->gestionCommandesListUrl());
            exit;
        }
        $config = require __DIR__ . '/../../../config/commandeconfig.php';
        $erreurs = [];
        $gestionBase = $this->gestionBaseUrl();
        $commandesListUrl = $this->gestionCommandesListUrl();
        $titreEspace = $this->gestionTitreEspace();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée.';
            } else {
                $nouveau = trim((string) ($_POST['statut'] ?? ''));
                $commentaire = trim((string) ($_POST['commentaire'] ?? ''));
                $ancien = (string) ($commande['statut'] ?? '');
                if (!isset($config['statuts'][$nouveau])) {
                    $erreurs[] = 'Statut invalide.';
                } elseif ($model->updateStatut($numero, $nouveau, $commentaire !== '' ? $commentaire : null)) {
                    if ($nouveau !== $ancien) {
                        $clientMail = (new UtilisateurModel())->findById((int) $commande['utilisateur_id']);
                        if ($clientMail) {
                            (new SmtpMailer())->envoyerChangementStatutCommande(
                                $commande,
                                $clientMail,
                                $nouveau,
                                (string) ($config['statuts'][$nouveau] ?? $nouveau),
                                $commentaire !== '' ? $commentaire : null
                            );
                        }
                    }
                    $_SESSION['flash_succes'] = 'Statut mis à jour.';
                    header('Location: ' . $this->gestionCommandeDetailUrl($numero));
                    exit;
                } else {
                    $erreurs[] = 'Impossible de mettre à jour.';
                }
            }
        }

        $lignes = $model->getLignes($numero);
        $suivi = $model->getSuivi($numero);
        $client = (new UtilisateurModel())->findById((int) $commande['utilisateur_id']);
        $titrePage = 'Commande ' . $numero . ' — ' . $this->gestionTitreEspace();
        require __DIR__ . '/../../Views/personnel/commande-detail.php';
    }
}
