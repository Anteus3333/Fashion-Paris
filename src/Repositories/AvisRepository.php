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

    /** Avis publiés pour l’accueil (3 derniers). */
    public function getAvisValidesAccueil(int $limit = 3): array {
        if (!$this->conn) {
            return [];
        }
        $limit = max(1, min(12, $limit));
        $stmt = $this->conn->query("
            SELECT a.note, a.commentaire, u.prenom
            FROM avis a
            JOIN utilisateur u ON u.utilisateur_id = a.utilisateur_id
            WHERE a.statut = 'valide'
            ORDER BY a.avis_id DESC
            LIMIT {$limit}
        ");
        return $stmt->fetchAll() ?: [];
    }

    /** Liste modération (filtre statut optionnel). */
    public function getAll(?string $statut = null): array {
        if (!$this->conn) {
            return [];
        }
        $sql = "
            SELECT a.*, u.prenom, u.nom, u.email, p.nom AS produit_nom, p.slug AS produit_slug
            FROM avis a
            JOIN utilisateur u ON u.utilisateur_id = a.utilisateur_id
            JOIN produit p ON p.produit_id = a.produit_id
        ";
        $params = [];
        if ($statut !== null && $statut !== '' && $statut !== 'tous') {
            $sql .= ' WHERE a.statut = :statut';
            $params[':statut'] = $statut;
        }
        $sql .= ' ORDER BY a.date_avis DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function compterParStatut(string $statut): int {
        if (!$this->conn) {
            return 0;
        }
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM avis WHERE statut = :statut');
        $stmt->execute([':statut' => $statut]);
        return (int) $stmt->fetchColumn();
    }
}

