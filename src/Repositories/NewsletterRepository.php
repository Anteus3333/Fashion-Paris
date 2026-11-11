<?php
require_once __DIR__ . '/../Services/SqlDatabase.php';

class NewsletterRepository {
    private $conn;

    public function __construct() {
        $database = new SqlDatabase();
        $this->conn = $database->getConnection();
    }

    public function inscrire(string $email): bool {
        if (!$this->conn) {
            return false;
        }
        $stmt = $this->conn->prepare("
            INSERT INTO newsletter (email, actif)
            VALUES (:email, 1)
            ON DUPLICATE KEY UPDATE actif = 1
        ");
        return $stmt->execute([':email' => $email]);
    }
}
