<?php
// src/Services/SqlDatabase.php — connexion PDO MySQL (couche Database du pattern Repository)

class SqlDatabase {
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;

    /** @var PDO|null */
    public $conn;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->host     = $config['host'];
        $this->db_name  = $config['db_name'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    public function getConnection(): ?PDO {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4',
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            // Pas d'echo ici : les Repositories renvoient vide / null si $conn est null.
            // Brancher un log fichiers si besoin (dossier logs/).
            error_log('Fashion Paris BDD : ' . $exception->getMessage());
        }

        return $this->conn;
    }
}
