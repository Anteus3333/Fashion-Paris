<?php
// src/Controllers/ContactController.php — formulaire contact → contact@anteusweb.com

require_once __DIR__ . '/../Services/SmtpMailer.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Services/TurnstileService.php';

class ContactController {

    public function index() {
        $titrePage = 'Contact — Fashion Paris';
        $erreurs = [];
        $succes = false;
        $old = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];
        $mailer = new SmtpMailer();
        $turnstile = new TurnstileService();
        $turnstileActif = $turnstile->estActif();
        $turnstileSiteKey = $turnstile->siteKey();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($old as $champ => $_) {
                $old[$champ] = trim((string) ($_POST[$champ] ?? ''));
            }

            // Honeypot anti-bot
            if (trim((string) ($_POST['website'] ?? '')) !== '') {
                $succes = true;
                $old = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];
                require __DIR__ . '/../Views/contact.php';
                return;
            }

            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée, veuillez renvoyer le formulaire.';
            }

            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
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
