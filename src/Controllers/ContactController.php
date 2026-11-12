<?php
// src/Controllers/ContactController.php — formulaire contact → contact@anteusweb.com

require_once __DIR__ . '/../Services/SmtpMailer.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Services/TurnstileService.php';
require_once __DIR__ . '/../Models/TentativeConnexionModel.php';

class ContactController {
    private const RATE_EMAIL = '__contact_form__';
    private const RATE_MAX_IP = 3;
    private const RATE_MAX_GLOBAL = 15;
    private const RATE_FENETRE_MIN = 60;

    public function index() {
        $titrePage = 'Contact — Fashion Paris';
        $erreurs = [];
        $succes = false;
        $old = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];
        $mailer = new SmtpMailer();
        $turnstile = new TurnstileService();
        $turnstileActif = $turnstile->estActif();
        $turnstileSiteKey = $turnstile->siteKey();
        $tentatives = new TentativeConnexionModel();
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($old as $champ => $_) {
                $old[$champ] = trim((string) ($_POST[$champ] ?? ''));
            }

            // Honeypot anti-bot : succès feint + compte dans le rate limit
            if (trim((string) ($_POST['website'] ?? '')) !== '') {
                $tentatives->enregistrer(self::RATE_EMAIL, $ip);
                $succes = true;
                $old = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];
                require __DIR__ . '/../Views/contact.php';
                return;
            }

            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée, veuillez renvoyer le formulaire.';
            }

            if ($tentatives->compterExact(self::RATE_EMAIL, $ip, self::RATE_FENETRE_MIN) >= self::RATE_MAX_IP) {
                $erreurs[] = 'Trop de messages envoyés depuis cette connexion. Merci de patienter '
                    . self::RATE_FENETRE_MIN . ' minutes avant de réessayer.';
            } elseif ($tentatives->compterMarqueur(self::RATE_EMAIL, self::RATE_FENETRE_MIN) >= self::RATE_MAX_GLOBAL) {
                $erreurs[] = 'Le formulaire de contact est temporairement saturé. Merci de réessayer plus tard.';
            }

            $verif = $turnstile->verifier($_POST['cf-turnstile-response'] ?? null, $ip);
            if (!$verif['ok']) {
                $erreurs[] = $verif['erreur'] ?? 'Vérification anti-spam échouée.';
            }

            if ($old['nom'] === '') {
                $erreurs[] = 'Le nom est obligatoire.';
            }
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = 'Adresse e-mail invalide.';
            }
            if ($old['sujet'] === '') {
                $erreurs[] = 'Le sujet est obligatoire.';
            }
            if (strlen($old['message']) < 10) {
                $erreurs[] = 'Le message doit contenir au moins 10 caractères.';
            }

            if (empty($erreurs)) {
                $corpsBoutique = "Nouveau message depuis fashion.anteusweb.com\n\n"
                    . "Nom : {$old['nom']}\n"
                    . "E-mail : {$old['email']}\n"
                    . "Sujet : {$old['sujet']}\n\n"
                    . "Message :\n{$old['message']}\n";

                $okBoutique = $mailer->send(
                    $mailer->getContactEmail(),
                    'Contact Fashion Paris — ' . $old['sujet'],
                    $corpsBoutique,
                    $old['email']
                );

                $corpsClient = "Bonjour {$old['nom']},\n\n"
                    . "Nous avons bien reçu votre message et vous répondrons rapidement.\n\n"
                    . "Sujet : {$old['sujet']}\n\n"
                    . "L'équipe Fashion Paris\n"
                    . "contact@anteusweb.com\n";

                $mailer->send($old['email'], 'Votre message a bien été reçu — Fashion Paris', $corpsClient);

                if ($okBoutique || $mailer->estConfigure() === false) {
                    $tentatives->enregistrer(self::RATE_EMAIL, $ip);
                    $succes = true;
                    $old = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];
                    if (!$mailer->estConfigure()) {
                        $_SESSION['flash_succes'] = 'Message enregistré (SMTP non configuré : voir logs/mails/).';
                    }
                } else {
                    $erreurs[] = 'L\'envoi a échoué. Réessayez plus tard ou écrivez à contact@anteusweb.com.';
                }
            }
        }

        if ($turnstileActif) {
            $scriptsFooter = '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
        }

        require __DIR__ . '/../Views/contact.php';
    }
}
