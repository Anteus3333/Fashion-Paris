<?php
// src/Controllers/AuthController.php — login, inscription, reset mdp, confirmation e-mail

require_once __DIR__ . '/../Models/UtilisateurModel.php';
require_once __DIR__ . '/../Models/TentativeConnexionModel.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Services/SmtpMailer.php';
require_once __DIR__ . '/../Services/UrlHelper.php';

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
                    if (!$this->utilisateurModel->estEmailVerifie($user)) {
                        $erreur = 'Compte non activé. Vérifiez votre boîte mail pour confirmer votre adresse.';
                    } else {
                        $tentatives->purger($oldEmail);
                        session_regenerate_id(true);
                        $_SESSION['utilisateur'] = [
                            'id'     => (int) $user['utilisateur_id'],
                            'email'  => $user['email'],
                            'prenom' => $user['prenom'],
                            'nom'    => $user['nom'],
                            'role'   => $this->utilisateurModel->getRole((int) $user['utilisateur_id']) ?? 'utilisateur',
                        ];
                        $redirect = (string) ($_GET['redirect'] ?? ($_POST['redirect'] ?? '/'));
                        if ($redirect === '' || $redirect[0] !== '/') {
                            $redirect = '/';
                        }
                        header('Location: ' . BASE_URL . $redirect);
                        exit;
                    }
                } else {
                    $tentatives->enregistrer($oldEmail !== '' ? $oldEmail : '__login__', $ip);
                    $erreur = 'Identifiants incorrects.';
                }
            }
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function register() {
        $titrePage = 'Inscription — Fashion Paris';
        $erreurs = [];
        $old = [
            'nom'    => '',
            'prenom' => '',
            'email'  => '',
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
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = 'Adresse e-mail invalide.';
            }
            $erreurs = array_merge($erreurs, $this->validerMotDePasse($password));
            if ($password !== $confirmation) {
                $erreurs[] = 'La confirmation ne correspond pas au mot de passe.';
            }

            // Coordonnées de livraison saisies au checkout, pas à l’inscription
            $payload = $old + [
                'password'        => $password,
                'telephone'       => '',
                'adresse_postale' => '',
                'code_postal'     => '',
                'ville'           => '',
            ];

            $existant = empty($erreurs) ? $this->utilisateurModel->findByEmail($old['email']) : null;
            if ($existant) {
                if ((int) ($existant['email_verifie'] ?? 1) === 0
                    && $this->utilisateurModel->getRole((int) $existant['utilisateur_id']) === 'utilisateur'
                ) {
                    $token = $this->utilisateurModel->reactiverInscriptionNonVerifiee(
                        (int) $existant['utilisateur_id'],
                        $payload
                    );
                    if ($token) {
                        $this->envoyerMailConfirmation($old['email'], $old['prenom'], $token);
                        $_SESSION['flash_succes'] = 'Un nouveau lien de confirmation vient de vous être envoyé. Vérifiez votre boîte mail.';
                        header('Location: ' . BASE_URL . '/login');
                        exit;
                    }
                }
                $erreurs[] = 'Un compte existe déjà avec cette adresse e-mail.';
            }

            if (empty($erreurs)) {
                $compte = $this->utilisateurModel->create($payload);
                $this->envoyerMailConfirmation($old['email'], $old['prenom'], $compte['token']);
                $_SESSION['flash_succes'] = 'Compte créé. Confirmez votre adresse e-mail via le lien reçu (valable 24 h) avant de vous connecter.';
                header('Location: ' . BASE_URL . '/login');
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

    public function forgotPassword(): void {
        $titrePage = 'Mot de passe oublié — Fashion Paris';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::verifier()) {
                $_SESSION['flash_erreur'] = 'Session expirée. Merci de réessayer.';
                header('Location: ' . BASE_URL . '/mot-de-passe-oublie');
                exit;
            }

            $email = trim((string) ($_POST['email'] ?? ''));
            $utilisateur = $email !== '' ? $this->utilisateurModel->findByEmail($email) : null;

            if ($utilisateur) {
                $token = bin2hex(random_bytes(32));
                $this->utilisateurModel->saveResetToken((int) $utilisateur['utilisateur_id'], $token);
                (new SmtpMailer())->envoyerResetMotDePasse($utilisateur, $token);
            }

            $_SESSION['flash_succes'] = 'Si un compte existe avec cette adresse, un mail de réinitialisation vient de lui être envoyé.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        require __DIR__ . '/../Views/auth/mdp_oublie.php';
    }

    public function resetPassword(): void {
        $titrePage = 'Nouveau mot de passe — Fashion Paris';
        $erreurs = [];

        $token = (string) ($_POST['token'] ?? $_GET['token'] ?? '');
        $utilisateur = $token !== '' ? $this->utilisateurModel->findByValidResetToken($token) : null;

        if (!$utilisateur) {
            $_SESSION['flash_erreur'] = 'Ce lien de réinitialisation est invalide ou a expiré. Merci de refaire une demande.';
            header('Location: ' . BASE_URL . '/mot-de-passe-oublie');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = (string) ($_POST['password'] ?? '');
            $confirmation = (string) ($_POST['confirmation'] ?? '');

            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée. Merci de réessayer.';
            }
            $erreurs = array_merge($erreurs, $this->validerMotDePasse($password));
            if ($password !== $confirmation) {
                $erreurs[] = 'La confirmation ne correspond pas au mot de passe.';
            }

            if (empty($erreurs)) {
                $this->utilisateurModel->updatePassword((int) $utilisateur['utilisateur_id'], $password);
                $mailOk = (new SmtpMailer())->envoyerAlerteMotDePasseModifie($utilisateur);
                $_SESSION['flash_succes'] = $mailOk
                    ? 'Votre mot de passe a bien été modifié. Un e-mail de confirmation vous a été envoyé. Vous pouvez vous connecter.'
                    : 'Votre mot de passe a bien été modifié. Vous pouvez vous connecter.';
                header('Location: ' . BASE_URL . '/login');
                exit;
            }
        }

        require __DIR__ . '/../Views/auth/reset_mdp.php';
    }

    public function confirmerEmail(): void {
        $token = trim((string) ($_GET['token'] ?? ''));
        $utilisateur = $token !== '' ? $this->utilisateurModel->findByValidEmailToken($token) : null;

        if (!$utilisateur) {
            $_SESSION['flash_erreur'] = 'Ce lien de confirmation est invalide ou a expiré.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $this->utilisateurModel->confirmerEmail((int) $utilisateur['utilisateur_id']);
        (new SmtpMailer())->envoyerBienvenue($utilisateur['email'], $utilisateur['prenom'] ?? '');
        $_SESSION['flash_succes'] = 'Adresse e-mail confirmée ! Votre compte est actif, vous pouvez vous connecter.';
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    private function envoyerMailConfirmation(string $email, string $prenom, string $token): void {
        (new SmtpMailer())->envoyerConfirmationInscription($email, $prenom, $token);
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
