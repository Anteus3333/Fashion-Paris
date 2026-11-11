<?php
// src/Repositories/CategorieRepository.php — accès SQL catégories

require_once __DIR__ . '/../Services/SqlDatabase.php';

class CategorieRepository {
    private $conn;

    public function __construct() {
        $database = new SqlDatabase();
        $this->conn = $database->getConnection();
    }

    public function findAllActives(): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->query(
            'SELECT * FROM categorie WHERE actif = 1 ORDER BY ordre ASC, libelle ASC'
        );
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare('SELECT * FROM categorie WHERE slug = :slug AND actif = 1');
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch() ?: null;
    }
}
