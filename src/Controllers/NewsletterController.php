<?php
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Models/NewsletterModel.php';

class NewsletterController {

    public function inscrire() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verifier()) {
            $_SESSION['flash_erreur'] = 'Requête invalide.';
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
            $_SESSION['flash_succes'] = 'Inscription à la newsletter enregistrée.';
        } else {
            $_SESSION['flash_erreur'] = 'Impossible d\'enregistrer l\'inscription.';
        }
        header('Location: ' . BASE_URL . '/');
        exit;
    }
}
