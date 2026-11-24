<?php
// src/Models/HoraireModel.php — couche métier horaires

require_once __DIR__ . '/../Repositories/HoraireRepository.php';

class HoraireModel {
    private HoraireRepository $repository;

    public function __construct() {
        $this->repository = new HoraireRepository();
    }

    public function getAll(): array {
        return $this->repository->getAll();
    }

    public function enregistrer(array $horaires): void {
        $this->repository->enregistrer($horaires);
    }

    public function getResumeFooter(): array {
        return $this->repository->getResumeFooter();
    }

    /** Prépare les 2 dalles admin (semaine / samedi) à partir des lignes BDD. */
    public function groupesEdition(array $horaires): array {
        $semaine = ['ouverture' => '10:00', 'fermeture' => '19:00'];
        $samedi = ['ouverture' => '10:00', 'fermeture' => '18:00'];

        foreach ($horaires as $h) {
            $jour = mb_strtolower(trim((string) ($h['jour'] ?? '')), 'UTF-8');
            $ouv = substr((string) ($h['heure_ouverture'] ?? '10:00'), 0, 5);
            $ferm = substr((string) ($h['heure_fermeture'] ?? '19:00'), 0, 5);
            if ($jour === 'samedi') {
                $samedi = ['ouverture' => $ouv, 'fermeture' => $ferm];
            } elseif (in_array($jour, ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'], true)) {
                $semaine = ['ouverture' => $ouv, 'fermeture' => $ferm];
            }
        }

        return ['semaine' => $semaine, 'samedi' => $samedi];
    }

    public function horairesValides(array $plage): bool {
        $ouv = (string) ($plage['ouverture'] ?? '');
        $ferm = (string) ($plage['fermeture'] ?? '');
        return (bool) preg_match('/^\d{2}:\d{2}$/', $ouv)
            && (bool) preg_match('/^\d{2}:\d{2}$/', $ferm);
    }

    /** Applique la plage semaine (lun–ven) + samedi sur toutes les lignes. */
    public function enregistrerGroupes(array $semaine, array $samedi): void {
        $lignes = [];
        foreach ($this->getAll() as $h) {
            $jour = mb_strtolower(trim((string) ($h['jour'] ?? '')), 'UTF-8');
            $plage = ($jour === 'samedi') ? $samedi : $semaine;
            if ($jour === 'dimanche') {
                continue;
            }
            $lignes[] = [
                'horaire_id'      => (int) $h['horaire_id'],
                'heure_ouverture' => $plage['ouverture'],
                'heure_fermeture' => $plage['fermeture'],
            ];
        }
        $this->enregistrer($lignes);
    }
}
