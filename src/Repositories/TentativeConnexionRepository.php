<?php
// src/Repositories/TentativeConnexionRepository.php — journal anti force brute / rate limit

require_once __DIR__ . '/../Services/SqlDatabase.php';

class TentativeConnexionRepository {
    public const MAX_TENTATIVES  = 5;
    public const FENETRE_MINUTES = 15;

    private $conn;

    public function __construct() {
        $database = new SqlDatabase();
        $this->conn = $database->getConnection();
    }

    /** Échecs login récents pour cet email OU cette IP */
    public function compterRecentes(string $email, string $ip): int {
        if (!$this->conn) {
            return 0;
        }
        $stmt = $this->conn->prepare(
            'SELECT COUNT(*) AS nb
             FROM tentative_connexion
             WHERE (email = :email OR ip = :ip)
             AND date_tentative > DATE_SUB(NOW(), INTERVAL ' . self::FENETRE_MINUTES . ' MINUTE)'
        );
        $stmt->execute([':email' => $email, ':ip' => $ip]);
        $row = $stmt->fetch() ?: [];
        return (int) ($row['nb'] ?? 0);
    }

    /** Envois récents pour un marqueur ET une IP (formulaires) */
    public function compterExact(string $email, string $ip, ?int $fenetreMinutes = null): int {
        if (!$this->conn) {
            return 0;
        }
        $fenetre = max(1, $fenetreMinutes ?? self::FENETRE_MINUTES);
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS nb
             FROM tentative_connexion
             WHERE email = :email AND ip = :ip
             AND date_tentative > DATE_SUB(NOW(), INTERVAL {$fenetre} MINUTE)"
        );
        $stmt->execute([':email' => $email, ':ip' => $ip]);
        $row = $stmt->fetch() ?: [];
        return (int) ($row['nb'] ?? 0);
    }

    /** Anti-flood global sur un marqueur (toutes IP) */
    public function compterMarqueur(string $email, int $fenetreMinutes): int {
        if (!$this->conn) {
            return 0;
        }
        $fenetre = max(1, $fenetreMinutes);
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS nb
             FROM tentative_connexion
             WHERE email = :email
             AND date_tentative > DATE_SUB(NOW(), INTERVAL {$fenetre} MINUTE)"
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch() ?: [];
        return (int) ($row['nb'] ?? 0);
    }

    public function enregistrer(string $email, string $ip): void {
        if (!$this->conn) {
            return;
        }
        $stmt = $this->conn->prepare(
            'INSERT INTO tentative_connexion (email, ip) VALUES (:email, :ip)'
        );
        $stmt->execute([':email' => $email, ':ip' => $ip]);
        $this->conn->exec(
            'DELETE FROM tentative_connexion WHERE date_tentative < DATE_SUB(NOW(), INTERVAL 1 DAY)'
        );
    }

    public function purger(string $email): void {
        if (!$this->conn) {
            return;
        }
        $stmt = $this->conn->prepare('DELETE FROM tentative_connexion WHERE email = :email');
        $stmt->execute([':email' => $email]);
    }
}
