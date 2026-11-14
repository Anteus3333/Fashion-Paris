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

    /**
     * Crée le compte client + rôle utilisateur (non vérifié).
     * @return array{id: int, token: string}
     */
    public function create(array $data): array {
        $token = bin2hex(random_bytes(32));
        $stmt = $this->conn->prepare("
            INSERT INTO utilisateur (
                email, password, prenom, nom, telephone, adresse_postale, code_postal, ville,
                email_verifie, email_token, email_token_expire, actif
            ) VALUES (
                :email, :password, :prenom, :nom, :telephone, :adresse_postale, :code_postal, :ville,
                0, :token, DATE_ADD(NOW(), INTERVAL 24 HOUR), 1
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
            ':token'           => $token,
        ]);
        $id = (int) $this->conn->lastInsertId();

        $role = $this->conn->prepare('INSERT INTO role (libelle, utilisateur_id) VALUES (:libelle, :id)');
        $role->execute([':libelle' => 'utilisateur', ':id' => $id]);

        return ['id' => $id, 'token' => $token];
    }

    public function findByValidEmailToken(string $token): ?array {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare("
            SELECT * FROM utilisateur
            WHERE email_token = :token
              AND email_token_expire > NOW()
              AND email_verifie = 0
        ");
        $stmt->execute([':token' => $token]);
        return $stmt->fetch() ?: null;
    }

    public function confirmerEmail(int $utilisateurId): void {
        $stmt = $this->conn->prepare("
            UPDATE utilisateur
            SET email_verifie = 1, email_token = NULL, email_token_expire = NULL
            WHERE utilisateur_id = :id
        ");
        $stmt->execute([':id' => $utilisateurId]);
    }

    public function estEmailVerifie(array $utilisateur): bool {
        if (!array_key_exists('email_verifie', $utilisateur)) {
            return true;
        }
        return (int) $utilisateur['email_verifie'] === 1;
    }

    /** Réinscription sur compte pas encore confirmé : maj infos + nouveau jeton. */
    public function reactiverInscriptionNonVerifiee(int $utilisateurId, array $data): ?string {
        if ($this->getRole($utilisateurId) !== 'utilisateur') {
            return null;
        }
        $token = bin2hex(random_bytes(32));
        $stmt = $this->conn->prepare("
            UPDATE utilisateur SET
                prenom = :prenom,
                nom = :nom,
                telephone = :telephone,
                adresse_postale = :adresse_postale,
                code_postal = :code_postal,
                ville = :ville,
                password = :password,
                email_token = :token,
                email_token_expire = DATE_ADD(NOW(), INTERVAL 24 HOUR)
            WHERE utilisateur_id = :id AND email_verifie = 0
        ");
        $stmt->execute([
            ':prenom'          => $data['prenom'],
            ':nom'             => $data['nom'],
            ':telephone'       => $data['telephone'],
            ':adresse_postale' => $data['adresse_postale'],
            ':code_postal'     => $data['code_postal'] ?? '',
            ':ville'           => $data['ville'] ?? '',
            ':password'        => password_hash($data['password'], PASSWORD_DEFAULT),
            ':token'           => $token,
            ':id'              => $utilisateurId,
        ]);
        return $stmt->rowCount() > 0 ? $token : null;
    }

    public function saveResetToken(int $utilisateurId, string $token): void {
        $stmt = $this->conn->prepare("
            UPDATE utilisateur
            SET reset_token = :token, reset_expire = DATE_ADD(NOW(), INTERVAL 1 HOUR)
            WHERE utilisateur_id = :id
        ");
        $stmt->execute([':token' => $token, ':id' => $utilisateurId]);
    }

    public function findByValidResetToken(string $token): ?array {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare("
            SELECT * FROM utilisateur
            WHERE reset_token = :token AND reset_expire > NOW()
        ");
        $stmt->execute([':token' => $token]);
        return $stmt->fetch() ?: null;
    }

    public function updatePassword(int $utilisateurId, string $nouveauPassword): void {
        $stmt = $this->conn->prepare("
            UPDATE utilisateur
            SET password = :password, reset_token = NULL, reset_expire = NULL
            WHERE utilisateur_id = :id
        ");
        $stmt->execute([
            ':password' => password_hash($nouveauPassword, PASSWORD_DEFAULT),
            ':id'       => $utilisateurId,
        ]);
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

    /** Comptes rôle personnel (administration). */
    public function getPersonnels(): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->query("
            SELECT u.utilisateur_id, u.email, u.prenom, u.nom, u.telephone,
                   u.adresse_postale, u.code_postal, u.ville, u.actif
            FROM utilisateur u
            INNER JOIN role r ON r.utilisateur_id = u.utilisateur_id
            WHERE r.libelle = 'personnel'
            ORDER BY u.nom ASC, u.prenom ASC
        ");
        return $stmt->fetchAll() ?: [];
    }

    public function estPersonnel(int $utilisateurId): bool {
        return $this->getRole($utilisateurId) === 'personnel';
    }

    public function createPersonnel(array $data): int {
        $stmt = $this->conn->prepare("
            INSERT INTO utilisateur (
                email, password, prenom, nom, telephone,
                adresse_postale, code_postal, ville, actif, email_verifie
            ) VALUES (
                :email, :password, :prenom, :nom, :telephone,
                :adresse_postale, :code_postal, :ville, 1, 1
            )
        ");
        $stmt->execute([
            ':email'           => $data['email'],
            ':password'        => password_hash($data['password'], PASSWORD_DEFAULT),
            ':prenom'          => $data['prenom'],
            ':nom'             => $data['nom'],
            ':telephone'       => $data['telephone'] ?? '',
            ':adresse_postale' => $data['adresse_postale'] ?? '',
            ':code_postal'     => $data['code_postal'] ?? '',
            ':ville'           => $data['ville'] ?? '',
        ]);
        $id = (int) $this->conn->lastInsertId();
        $role = $this->conn->prepare("INSERT INTO role (libelle, utilisateur_id) VALUES ('personnel', :id)");
        $role->execute([':id' => $id]);
        return $id;
    }

    public function updatePersonnel(int $utilisateurId, array $data): bool {
        if (!$this->estPersonnel($utilisateurId)) {
            return false;
        }
        $sql = "
            UPDATE utilisateur SET
                email = :email,
                prenom = :prenom,
                nom = :nom,
                telephone = :telephone,
                adresse_postale = :adresse_postale,
                code_postal = :code_postal,
                ville = :ville
        ";
        $params = [
            ':email'           => $data['email'],
            ':prenom'          => $data['prenom'],
            ':nom'             => $data['nom'],
            ':telephone'       => $data['telephone'] ?? '',
            ':adresse_postale' => $data['adresse_postale'] ?? '',
            ':code_postal'     => $data['code_postal'] ?? '',
            ':ville'           => $data['ville'] ?? '',
            ':id'              => $utilisateurId,
        ];
        if (!empty($data['password'])) {
            $sql .= ', password = :password';
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $sql .= ' WHERE utilisateur_id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return true;
    }

    public function setActifPersonnel(int $utilisateurId, bool $actif): bool {
        $stmt = $this->conn->prepare("
            UPDATE utilisateur u
            INNER JOIN role r ON r.utilisateur_id = u.utilisateur_id
            SET u.actif = :actif
            WHERE u.utilisateur_id = :id AND r.libelle = 'personnel'
        ");
        $stmt->execute([
            ':actif' => $actif ? 1 : 0,
            ':id'    => $utilisateurId,
        ]);
        return $stmt->rowCount() > 0;
    }

    /** Suppression définitive — refuse si commandes liées. */
    public function supprimerPersonnel(int $utilisateurId): bool {
        if (!$this->estPersonnel($utilisateurId)) {
            return false;
        }
        $stmtCmd = $this->conn->prepare('SELECT COUNT(*) FROM commande WHERE utilisateur_id = :id');
        $stmtCmd->execute([':id' => $utilisateurId]);
        if ((int) $stmtCmd->fetchColumn() > 0) {
            return false;
        }
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("DELETE FROM role WHERE utilisateur_id = :id AND libelle = 'personnel'");
            $stmt->execute([':id' => $utilisateurId]);
            $stmt = $this->conn->prepare('DELETE FROM utilisateur WHERE utilisateur_id = :id');
            $stmt->execute([':id' => $utilisateurId]);
            if ($stmt->rowCount() === 0) {
                $this->conn->rollBack();
                return false;
            }
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }
}

