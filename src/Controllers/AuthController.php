<?php
// src/Controllers/AuthController.php

require_once __DIR__ . '/../Models/UtilisateurModel.php';
require_once __DIR__ . '/../Models/TentativeConnexionModel.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Services/SmtpMailer.php';

class AuthController {
    private UtilisateurModel $utilisateurModel;

    public function __construct() {
        $this->utilisateurModel = new UtilisateurModel();
    }

    public function login() {
        $titrePage = 'Connexion — Fashion Paris';
        $erreur = null;
        $oldEmail = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldEmail = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $tentatives = new TentativeConnexionModel();
            $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');

            if (!Csrf::verifier()) {
                $erreur = 'Session expirée. Rechargez la page.';
            } elseif ($tentatives->compterRecentes($oldEmail, $ip) >= TentativeConnexionModel::MAX_TENTATIVES) {
                $erreur = 'Trop de tentatives de connexion. Merci de patienter '
                    . TentativeConnexionModel::FENETRE_MINUTES . ' minutes avant de réessayer.';
            } else {
                $user = $this->utilisateurModel->findByEmail($oldEmail);
                if ($user && (int) $user['actif'] === 1 && password_verify($password, $user['password'])) {
                    $tentatives->purger($oldEmail);
                    session_regenerate_id(true);
                    $_SESSION['utilisateur'] = [
                        'id'     => (int) $user['utilisateur_id'],
                        'email'  => $user['email'],
                        'prenom' => $user['prenom'],
                        'nom'    => $user['nom'],
                        'role'   => $this->utilisateurModel->getRole((int) $user['utilisateur_id']) ?? 'utilisateur',
                    ];
                    $redirect = (string) ($_GET['redirect'] ?? '/');
                    if ($redirect === '' || $redirect[0] !== '/') {
                        $redirect = '/';
                    }
                    header('Location: ' . BASE_URL . $redirect);
                    exit;
                }
                $tentatives->enregistrer($oldEmail !== '' ? $oldEmail : '__login__', $ip);
                $erreur = 'Identifiants incorrects.';
            }
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function register() {
        $titrePage = 'Inscription — Fashion Paris';
        $erreurs = [];
        $old = [
            'nom'             => '',
            'prenom'          => '',
            'telephone'       => '',
            'email'           => '',
            'adresse_postale' => '',
            'code_postal'     => '',
            'ville'           => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($old as $champ => $_) {
                $old[$champ] = trim((string) ($_POST[$champ] ?? ''));
            }
            $password = (string) ($_POST['password'] ?? '');
            $confirmation = (string) ($_POST['confirmation'] ?? '');

            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée, veuillez renvoyer le formulaire.';
            }
            if ($old['nom'] === '') {
                $erreurs[] = 'Le nom est obligatoire.';
            }
            if ($old['prenom'] === '') {
                $erreurs[] = 'Le prénom est obligatoire.';
            }
            if ($old['telephone'] === '') {
                $erreurs[] = 'Le téléphone est obligatoire.';
            }
            if ($old['adresse_postale'] === '') {
                $erreurs[] = 'L\'adresse est obligatoire.';
            }
            if (!preg_match('/^\d{5}$/', $old['code_postal'])) {
                $erreurs[] = 'Le code postal doit contenir 5 chiffres.';
            }
            if ($old['ville'] === '') {
                $erreurs[] = 'La ville est obligatoire.';
            }
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = 'Adresse e-mail invalide.';
            }
            $erreurs = array_merge($erreurs, $this->validerMotDePasse($password));
            if ($password !== $confirmation) {
                $erreurs[] = 'La confirmation ne correspond pas au mot de passe.';
            }
            if (empty($erreurs) && $this->utilisateurModel->findByEmail($old['email'])) {
                $erreurs[] = 'Un compte existe déjà avec cette adresse e-mail.';
            }

            if (empty($erreurs)) {
                $id = $this->utilisateurModel->create($old + ['password' => $password]);
                (new SmtpMailer())->envoyerBienvenue($old['email'], $old['prenom']);
                session_regenerate_id(true);
                $_SESSION['utilisateur'] = [
                    'id'     => $id,
                    'email'  => $old['email'],
                    'prenom' => $old['prenom'],
                    'nom'    => $old['nom'],
                    'role'   => 'utilisateur',
                ];
                $_SESSION['flash_succes'] = 'Compte créé. Bienvenue chez Fashion Paris !';
                header('Location: ' . BASE_URL . '/mon-compte');
                exit;
            }
        }

        require __DIR__ . '/../Views/auth/register.php';
    }

    public function logout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && Csrf::verifier()) {
            unset($_SESSION['utilisateur']);
            $_SESSION['flash_succes'] = 'Vous êtes déconnecté(e).';
        }
        header('Location: ' . BASE_URL . '/');
        exit;
    }

    /** @return string[] */
    private function validerMotDePasse(string $password): array {
        $erreurs = [];
        if (strlen($password) < 10) {
            $erreurs[] = 'Le mot de passe doit contenir au moins 10 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $erreurs[] = 'Le mot de passe doit contenir au moins une majuscule.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $erreurs[] = 'Le mot de passe doit contenir au moins une minuscule.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $erreurs[] = 'Le mot de passe doit contenir au moins un chiffre.';
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $erreurs[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
        }
        return $erreurs;
    }
}
