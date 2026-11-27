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

    /**
     * Séries mensuelles (12 derniers mois) pour Chart.js.
     * @return array{labels: string[], commandes: int[], ca: float[]}
     */
    public function seriesMensuelles(int $nbMois = 12): array {
        $vide = ['labels' => [], 'commandes' => [], 'ca' => []];
        if (!$this->conn) {
            return $vide;
        }
        $nbMois = max(1, min(24, $nbMois));
        $stmt = $this->conn->query("
            SELECT DATE_FORMAT(date_commande, '%Y-%m') AS mois,
                   COUNT(*) AS nb,
                   COALESCE(SUM(montant_total), 0) AS ca
            FROM commande
            WHERE statut <> 'annulee'
              AND date_commande >= DATE_SUB(CURDATE(), INTERVAL {$nbMois} MONTH)
            GROUP BY DATE_FORMAT(date_commande, '%Y-%m')
            ORDER BY mois ASC
        ");
        $rows = $stmt->fetchAll() ?: [];
        $map = [];
        foreach ($rows as $r) {
            $map[$r['mois']] = $r;
        }

        $labels = [];
        $commandes = [];
        $ca = [];
        $cursor = new DateTime('first day of this month');
        $cursor->modify('-' . ($nbMois - 1) . ' months');
        for ($i = 0; $i < $nbMois; $i++) {
            $key = $cursor->format('Y-m');
            $labels[] = $cursor->format('m/Y');
            $commandes[] = isset($map[$key]) ? (int) $map[$key]['nb'] : 0;
            $ca[] = isset($map[$key]) ? round((float) $map[$key]['ca'], 2) : 0.0;
            $cursor->modify('+1 month');
        }
        return ['labels' => $labels, 'commandes' => $commandes, 'ca' => $ca];
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

    /**
     * Empreinte des commandes MySQL pour détecter un écart vs Mongo (sync manuelle).
     * @return array{nb: int, hash: string}
     */
    public function empreinteSync(): array {
        if (!$this->conn) {
            return ['nb' => 0, 'hash' => hash('sha256', '')];
        }
        $stmt = $this->conn->query(
            'SELECT numero_commande, statut, montant_total
             FROM commande
             ORDER BY numero_commande ASC'
        );
        $rows = $stmt->fetchAll() ?: [];
        $lignes = [];
        foreach ($rows as $row) {
            $lignes[] = $row['numero_commande'] . '|' . $row['statut'] . '|' . number_format((float) $row['montant_total'], 2, '.', '');
        }
        return [
            'nb'   => count($lignes),
            'hash' => hash('sha256', implode("\n", $lignes)),
        ];
    }
}
