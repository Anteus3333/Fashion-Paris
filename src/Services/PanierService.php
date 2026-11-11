<?php
// src/Services/PanierService.php — panier en session (quantités par produit_id)

class PanierService {

    public static function lignes(): array {
        return $_SESSION['panier'] ?? [];
    }

    public static function ajouter(int $produitId, int $quantite): void {
        if ($produitId < 1 || $quantite < 1) {
            return;
        }
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
        $actuel = (int) ($_SESSION['panier'][$produitId] ?? 0);
        $_SESSION['panier'][$produitId] = $actuel + $quantite;
    }

    public static function definir(int $produitId, int $quantite): void {
        if ($produitId < 1) {
            return;
        }
        if ($quantite < 1) {
            self::retirer($produitId);
            return;
        }
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
        $_SESSION['panier'][$produitId] = $quantite;
    }

    public static function retirer(int $produitId): void {
        unset($_SESSION['panier'][$produitId]);
        if (empty($_SESSION['panier'])) {
            unset($_SESSION['panier']);
        }
    }

    public static function vider(): void {
        unset($_SESSION['panier']);
    }

    public static function nombreArticles(): int {
        return array_sum(self::lignes());
    }
}
