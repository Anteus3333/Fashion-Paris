<?php
// src/Repositories/HoraireRepository.php — horaires boutique / SAV

require_once __DIR__ . '/../Services/SqlDatabase.php';

class HoraireRepository {
    private $conn;

    public function __construct() {
        $this->conn = (new SqlDatabase())->getConnection();
    }

    public function getAll(): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->query("
            SELECT horaire_id, jour, heure_ouverture, heure_fermeture
            FROM horaire
            ORDER BY horaire_id ASC
        ");
        return $stmt->fetchAll() ?: [];
    }

    public function enregistrer(array $horaires): void {
        if (!$this->conn) {
            return;
        }
        $stmt = $this->conn->prepare("
            UPDATE horaire SET heure_ouverture = :ouverture, heure_fermeture = :fermeture
            WHERE horaire_id = :id
        ");
        foreach ($horaires as $row) {
            $stmt->execute([
                ':ouverture' => $row['heure_ouverture'],
                ':fermeture' => $row['heure_fermeture'],
                ':id'        => (int) $row['horaire_id'],
            ]);
        }
    }

    /**
     * Résumé footer : regroupe les jours consécutifs aux mêmes horaires.
     * @return list<string>
     */
    public function getResumeFooter(): array {
        $rows = $this->getAll();
        if (empty($rows)) {
            return ['Du Lundi au Samedi · 10:00 - 19:00', 'Dimanche · 12:00 - 18:00'];
        }

        $groupes = [];
        foreach ($rows as $h) {
            $ouverture = $this->formaterHeure($h['heure_ouverture'] ?? '');
            $fermeture = $this->formaterHeure($h['heure_fermeture'] ?? '');
            $plage = $ouverture . ' - ' . $fermeture;
            $jour = trim((string) ($h['jour'] ?? ''));

            $dernier = $groupes[count($groupes) - 1] ?? null;
            if ($dernier !== null && $dernier['plage'] === $plage) {
                $groupes[count($groupes) - 1]['fin'] = $jour;
                continue;
            }

            $groupes[] = [
                'debut' => $jour,
                'fin'   => $jour,
                'plage' => $plage,
            ];
        }

        $lignes = [];
        foreach ($groupes as $g) {
            if ($g['debut'] === $g['fin']) {
                $lignes[] = $g['debut'] . ' · ' . $g['plage'];
            } else {
                $lignes[] = 'Du ' . $g['debut'] . ' au ' . $g['fin'] . ' · ' . $g['plage'];
            }
        }
        return $lignes;
    }

    private function formaterHeure(string $heure): string {
        $heure = trim($heure);
        return $heure === '' ? '' : substr($heure, 0, 5);
    }
}
