<?php
// src/Repositories/MysqlStatsRepository.php — agrégats MySQL pour dashboard admin

require_once __DIR__ . '/../Services/SqlDatabase.php';

class MysqlStatsRepository {
    private $conn;

    public function __construct() {
        $database = new SqlDatabase();
        $this->conn = $database->getConnection();
    }

    public function resume(): array {
        if (!$this->conn) {
            return [
                'nb_produits' => 0,
                'nb_clients' => 0,
                'nb_commandes' => 0,
                'ca_total' => 0,
                'ruptures' => 0,
            ];
        }

        $nbProduits = (int) $this->conn->query('SELECT COUNT(*) FROM produit WHERE actif = 1')->fetchColumn();
        $nbClients = (int) $this->conn->query(
            "SELECT COUNT(*) FROM role WHERE libelle = 'utilisateur'"
        )->fetchColumn();
        $nbCommandes = (int) $this->conn->query(
            "SELECT COUNT(*) FROM commande WHERE statut <> 'annulee'"
        )->fetchColumn();
        $ca = (float) $this->conn->query(
            "SELECT COALESCE(SUM(montant_total),0) FROM commande WHERE statut <> 'annulee'"
        )->fetchColumn();
        $ruptures = (int) $this->conn->query(
            'SELECT COUNT(*) FROM produit WHERE actif = 1 AND stock <= 0'
        )->fetchColumn();

        return [
            'nb_produits'  => $nbProduits,
            'nb_clients'   => $nbClients,
            'nb_commandes' => $nbCommandes,
            'ca_total'     => $ca,
            'ruptures'     => $ruptures,
        ];
    }

    /** Jeu de documents prêts pour sync Mongo (1 doc = 1 commande). */
    public function commandesPourSync(): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->query("
            SELECT c.numero_commande, c.date_commande, c.statut, c.montant_total,
                   c.frais_livraison, c.mode_paiement, c.ville_livraison,
                   u.email, u.prenom, u.nom
            FROM commande c
            JOIN utilisateur u ON u.utilisateur_id = c.utilisateur_id
            ORDER BY c.date_commande DESC
        ");
        return $stmt->fetchAll() ?: [];
    }
}
