<?php
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Models/NewsletterModel.php';
require_once __DIR__ . '/../Models/TentativeConnexionModel.php';

class NewsletterController {
    private const RATE_EMAIL = '__newsletter__';
    private const RATE_MAX_IP = 5;
    private const RATE_MAX_GLOBAL = 30;
    private const RATE_FENETRE_MIN = 60;

    public function inscrire() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verifier()) {
            $_SESSION['flash_erreur'] = 'Requête invalide.';
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $tentatives = new TentativeConnexionModel();
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');

        if ($tentatives->compterExact(self::RATE_EMAIL, $ip, self::RATE_FENETRE_MIN) >= self::RATE_MAX_IP) {
            $_SESSION['flash_erreur'] = 'Trop d\'inscriptions depuis cette connexion. Réessayez plus tard.';
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        if ($tentatives->compterMarqueur(self::RATE_EMAIL, self::RATE_FENETRE_MIN) >= self::RATE_MAX_GLOBAL) {
            $_SESSION['flash_erreur'] = 'Service newsletter temporairement saturé. Réessayez plus tard.';
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_erreur'] = 'E-mail newsletter invalide.';
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        if ((new NewsletterModel())->inscrire($email)) {
            $tentatives->enregistrer(self::RATE_EMAIL, $ip);
            $_SESSION['flash_succes'] = 'Inscription à la newsletter enregistrée.';
        } else {
            $_SESSION['flash_erreur'] = 'Impossible d\'enregistrer l\'inscription.';
        }
        header('Location: ' . BASE_URL . '/');
        exit;
    }
}
