<?php
// src/Services/FraisLivraisonService.php — frais e-commerce (franco / forfait)
// Équivalent Fashion de PrixCommandeService (traiteur) : pas de km / DistanceService.

class FraisLivraisonService {
    private array $config;

    public function __construct(?array $config = null) {
        $this->config = $config ?? require __DIR__ . '/../../config/commandeconfig.php';
    }

    /**
     * Calcule le détail tarifaire livraison à partir du sous-total panier.
     * - Frais forfaitaires sous le seuil franco
     * - Livraison offerte si sous-total >= seuil_franco (ou panier vide)
     *
     * @return array{
     *   sous_total: float,
     *   frais: float,
     *   total: float,
     *   franco: bool,
     *   seuil_franco: float,
     *   reste_franco: float,
     *   frais_forfait: float
     * }
     */
    public function calculer(float $sousTotal): array {
        $sousTotal = max(0.0, $sousTotal);
        $seuil = (float) ($this->config['seuil_franco'] ?? 150);
        $forfait = (float) ($this->config['frais_livraison'] ?? 5.90);

        $frais = ($sousTotal <= 0 || $sousTotal >= $seuil) ? 0.0 : $forfait;
        $franco = $frais <= 0 && $sousTotal > 0;

        return [
            'sous_total'    => round($sousTotal, 2),
            'frais'         => round($frais, 2),
            'total'         => round($sousTotal + $frais, 2),
            'franco'        => $franco,
            'seuil_franco'  => $seuil,
            'reste_franco'  => $frais > 0 ? round(max(0, $seuil - $sousTotal), 2) : 0.0,
            'frais_forfait' => $forfait,
        ];
    }

    /** Montant des frais seuls (compat CommandeModel / panier). */
    public function montantFrais(float $sousTotal): float {
        return $this->calculer($sousTotal)['frais'];
    }
}
