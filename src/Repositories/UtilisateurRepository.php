<?php
// src/Repositories/UtilisateurRepository.php — accès SQL table utilisateur

require_once __DIR__ . '/../Services/SqlDatabase.php';

class UtilisateurRepository {
    private $conn;

    public function __construct() {
        $database = new SqlDatabase();
        $this->conn = $database->getConnection();
    }

    public function findByEmail(string $email): ?array {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare('SELECT * FROM utilisateur WHERE email = :email');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare('SELECT * FROM utilisateur WHERE utilisateur_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getRole(int $utilisateurId): ?string {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare('SELECT libelle FROM role WHERE utilisateur_id = :id LIMIT 1');
        $stmt->execute([':id' => $utilisateurId]);
        $row = $stmt->fetch();
        return $row['libelle'] ?? null;
    }

    /** Crée le compte + rôle utilisateur. Retourne l'id. */
    public function create(array $data): int {
        $stmt = $this->conn->prepare("
            INSERT INTO utilisateur (
                email, password, prenom, nom, telephone, adresse_postale, code_postal, ville,
                email_verifie, actif
            ) VALUES (
                :email, :password, :prenom, :nom, :telephone, :adresse_postale, :code_postal, :ville,
                1, 1
            )
        ");
        $stmt->execute([
            ':email'           => $data['email'],
            ':password'        => password_hash($data['password'], PASSWORD_DEFAULT),
            ':prenom'          => $data['prenom'],
            ':nom'             => $data['nom'],
            ':telephone'       => $data['telephone'],
            ':adresse_postale' => $data['adresse_postale'],
            ':code_postal'     => $data['code_postal'] ?? '',
            ':ville'           => $data['ville'] ?? '',
        ]);
        $id = (int) $this->conn->lastInsertId();

        $role = $this->conn->prepare('INSERT INTO role (libelle, utilisateur_id) VALUES (:libelle, :id)');
        $role->execute([':libelle' => 'utilisateur', ':id' => $id]);

        return $id;
    }

    public function updateProfil(int $utilisateurId, array $data): void {
        $stmt = $this->conn->prepare("
            UPDATE utilisateur SET
                prenom = :prenom,
                nom = :nom,
                telephone = :telephone,
                adresse_postale = :adresse_postale,
                code_postal = :code_postal,
                ville = :ville
            WHERE utilisateur_id = :id
        ");
        $stmt->execute([
            ':prenom'          => $data['prenom'],
            ':nom'             => $data['nom'],
            ':telephone'       => $data['telephone'],
            ':adresse_postale' => $data['adresse_postale'],
            ':code_postal'     => $data['code_postal'],
            ':ville'           => $data['ville'],
            ':id'              => $utilisateurId,
        ]);
    }

    public function getClients(): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->query("
            SELECT u.*, r.libelle AS role
            FROM utilisateur u
            LEFT JOIN role r ON r.utilisateur_id = u.utilisateur_id
            ORDER BY u.date_creation DESC
        ");
        return $stmt->fetchAll() ?: [];
    }
}
