<?php
// src/Services/AuthGuard.php — contrôle d'accès par rôle

class AuthGuard {

    public static function exigerConnexion(string $redirect = '/mon-compte'): void {
        if (!isset($_SESSION['utilisateur']['id'])) {
            $_SESSION['flash_erreur'] = 'Connectez-vous pour accéder à cette page.';
            header('Location: ' . BASE_URL . '/login?redirect=' . urlencode($redirect));
            exit;
        }
    }

    public static function exigerRole(array $rolesAutorises, string $redirect = '/mon-compte'): void {
        self::exigerConnexion($redirect);
        $role = self::roleActuel();
        if (!in_array($role, $rolesAutorises, true)) {
            $_SESSION['flash_erreur'] = 'Vous n\'avez pas les droits pour accéder à cette page.';
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    }

    public static function roleActuel(): string {
        return $_SESSION['utilisateur']['role'] ?? 'utilisateur';
    }

    public static function rolesAdmin(): array {
        return ['administrateur'];
    }
}
