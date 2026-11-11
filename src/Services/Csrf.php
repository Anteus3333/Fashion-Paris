<?php
// src/Services/Csrf.php — protection CSRF (même principe que Studi_ECF)

class Csrf {

    public static function token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function champ(): string {
        return '<input type="hidden" name="csrf_token" value="' . self::token() . '">';
    }

    public static function verifier(): bool {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
}
