<?php
// src/Repositories/CommandeRepository.php — commandes e-commerce

require_once __DIR__ . '/../Services/SqlDatabase.php';

class CommandeRepository {
    private $conn;

    public function __construct() {
        $database = new SqlDatabase();
        $this->conn = $database->getConnection();
    }

    public function genererNumero(): string {
        return 'FP-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    /**
     * Crée la commande + lignes + suivi, décrémente les stocks (transaction).
     * @param array $commande Données commande
     * @param array $lignes [ ['produit_id'=>, 'quantite'=>, 'prix_unitaire'=>], ... ]
     */
    public function creer(array $commande, array $lignes): string {
        if (!$this->conn) {
            throw new RuntimeException('Base de données indisponible.');
        }

        $this->conn->beginTransaction();
        try {
            $numero = $this->genererNumero();

            $stmt = $this->conn->prepare("
                INSERT INTO commande (
                    numero_commande, utilisateur_id, statut, montant_total, frais_livraison,
                    adresse_livraison, code_postal_livraison, ville_livraison, mode_paiement
                ) VALUES (
                    :numero, :uid, :statut, :montant, :frais,
                    :adresse, :cp, :ville, :paiement
                )
            ");
            $stmt->execute([
                ':numero'   => $numero,
                ':uid'      => $commande['utilisateur_id'],
                ':statut'   => $commande['statut'],
                ':montant'  => $commande['montant_total'],
                ':frais'    => $commande['frais_livraison'],
                ':adresse'  => $commande['adresse_livraison'],
                ':cp'       => $commande['code_postal_livraison'],
                ':ville'    => $commande['ville_livraison'],
                ':paiement' => $commande['mode_paiement'],
            ]);

            $stmtLigne = $this->conn->prepare("
                INSERT INTO commande_ligne (numero_commande, produit_id, quantite, prix_unitaire)
                VALUES (:numero, :pid, :qte, :prix)
            ");
            $stmtStock = $this->conn->prepare("
                UPDATE produit SET stock = stock - :qte
                WHERE produit_id = :pid AND stock >= :qte2 AND actif = 1
            ");

            foreach ($lignes as $ligne) {
                $stmtLigne->execute([
                    ':numero' => $numero,
                    ':pid'    => $ligne['produit_id'],
                    ':qte'    => $ligne['quantite'],
                    ':prix'   => $ligne['prix_unitaire'],
                ]);
                $stmtStock->execute([
                    ':qte'  => $ligne['quantite'],
                    ':pid'  => $ligne['produit_id'],
                    ':qte2' => $ligne['quantite'],
                ]);
                if ($stmtStock->rowCount() !== 1) {
                    throw new RuntimeException('Stock insuffisant pour un article du panier.');
                }
            }

            $this->ajouterSuivi($numero, $commande['statut'], 'Commande créée');
            $this->conn->commit();
            return $numero;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function ajouterSuivi(string $numero, string $statut, ?string $commentaire = null): void {
        $stmt = $this->conn->prepare("
            INSERT INTO suivi_commande (numero_commande, statut, commentaire)
            VALUES (:numero, :statut, :commentaire)
        ");
        $stmt->execute([
            ':numero'      => $numero,
            ':statut'      => $statut,
            ':commentaire' => $commentaire,
        ]);
    }

    public function getByUtilisateur(int $utilisateurId): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->prepare("
            SELECT * FROM commande
            WHERE utilisateur_id = :id
            ORDER BY date_commande DESC, numero_commande DESC
        ");
        $stmt->execute([':id' => $utilisateurId]);
        return $stmt->fetchAll() ?: [];
    }

    public function getByNumeroPourUtilisateur(string $numero, int $utilisateurId): ?array {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare("
            SELECT * FROM commande
            WHERE numero_commande = :numero AND utilisateur_id = :id
        ");
        $stmt->execute([':numero' => $numero, ':id' => $utilisateurId]);
        return $stmt->fetch() ?: null;
    }

    public function getByNumero(string $numero): ?array {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare('SELECT * FROM commande WHERE numero_commande = :numero');
        $stmt->execute([':numero' => $numero]);
        return $stmt->fetch() ?: null;
    }

    public function getLignes(string $numero): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->prepare("
            SELECT cl.*, p.nom, p.slug
            FROM commande_ligne cl
            JOIN produit p ON p.produit_id = cl.produit_id
            WHERE cl.numero_commande = :numero
        ");
        $stmt->execute([':numero' => $numero]);
        return $stmt->fetchAll() ?: [];
    }

    public function getSuivi(string $numero): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->prepare("
            SELECT statut, commentaire, date_modification
            FROM suivi_commande
            WHERE numero_commande = :numero
            ORDER BY date_modification ASC, suivi_id ASC
        ");
        $stmt->execute([':numero' => $numero]);
        return $stmt->fetchAll() ?: [];
    }

    public function getAll(?string $statut = null): array {
        if (!$this->conn) {
            return [];
        }
        $sql = "
            SELECT c.*, u.prenom, u.nom, u.email
            FROM commande c
            JOIN utilisateur u ON u.utilisateur_id = c.utilisateur_id
        ";
        $params = [];
        if ($statut) {
            $sql .= ' WHERE c.statut = :statut';
            $params[':statut'] = $statut;
        }
        $sql .= ' ORDER BY c.date_commande DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function updateStatut(string $numero, string $statut, ?string $commentaire = null): bool {
        if (!$this->conn) {
            return false;
        }
        $stmt = $this->conn->prepare('UPDATE commande SET statut = :statut WHERE numero_commande = :numero');
        $stmt->execute([':statut' => $statut, ':numero' => $numero]);
        if ($stmt->rowCount() < 1) {
            // même statut déjà en place : on accepte quand même un suivi
            $exists = $this->getByNumero($numero);
            if (!$exists) {
                return false;
            }
        }
        $this->ajouterSuivi($numero, $statut, $commentaire);
        return true;
    }
}
