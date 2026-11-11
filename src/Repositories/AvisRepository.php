<?php
require_once __DIR__ . '/../Services/SqlDatabase.php';

class AvisRepository {
    private $conn;

    public function __construct() {
        $database = new SqlDatabase();
        $this->conn = $database->getConnection();
    }

    public function findValidesParProduit(int $produitId): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->prepare("
            SELECT a.*, u.prenom, u.nom
            FROM avis a
            JOIN utilisateur u ON u.utilisateur_id = a.utilisateur_id
            WHERE a.produit_id = :pid AND a.statut = 'valide'
            ORDER BY a.date_avis DESC
        ");
        $stmt->execute([':pid' => $produitId]);
        return $stmt->fetchAll() ?: [];
    }

    public function create(array $data): int {
        $stmt = $this->conn->prepare("
            INSERT INTO avis (produit_id, utilisateur_id, note, commentaire, statut)
            VALUES (:pid, :uid, :note, :commentaire, 'en_attente')
        ");
        $stmt->execute([
            ':pid'         => $data['produit_id'],
            ':uid'         => $data['utilisateur_id'],
            ':note'        => $data['note'],
            ':commentaire' => $data['commentaire'],
        ]);
        return (int) $this->conn->lastInsertId();
    }

    public function findEnAttente(): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->query("
            SELECT a.*, u.prenom, u.nom, p.nom AS produit_nom, p.slug AS produit_slug
            FROM avis a
            JOIN utilisateur u ON u.utilisateur_id = a.utilisateur_id
            JOIN produit p ON p.produit_id = a.produit_id
            WHERE a.statut = 'en_attente'
            ORDER BY a.date_avis ASC
        ");
        return $stmt->fetchAll() ?: [];
    }

    public function updateStatut(int $avisId, string $statut): bool {
        $stmt = $this->conn->prepare('UPDATE avis SET statut = :statut WHERE avis_id = :id');
        $stmt->execute([':statut' => $statut, ':id' => $avisId]);
        return $stmt->rowCount() > 0;
    }
}
