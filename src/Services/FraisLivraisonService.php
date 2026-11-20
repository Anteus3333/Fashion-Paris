<?php
// src/Services/FraisLivraisonService.php — forfait livraison fixe
// Équivalent Fashion de PrixCommandeService (traiteur) : pas de km / DistanceService.

class FraisLivraisonService {
    private array $config;

    public function __construct(?array $config = null) {
        $this->config = $config ?? require __DIR__ . '/../../config/commandeconfig.php';
    }

    /**
     * Forfait fixe dès qu’il y a un article (panier non vide).
     *
     * @return array{
     *   sous_total: float,
     *   frais: float,
     *   total: float,
     *   franco: bool,
     *   seuil_franco: float|null,
     *   reste_franco: float,
     *   frais_forfait: float,
     *   delai_livraison: string
     * }
     */
    public function calculer(float $sousTotal): array {
        $sousTotal = max(0.0, $sousTotal);
        $forfait = (float) ($this->config['frais_livraison'] ?? 10.0);
        $delai = (string) ($this->config['delai_livraison'] ?? '7 jours ouvrés');

        $frais = $sousTotal > 0 ? $forfait : 0.0;

        return [
            'sous_total'       => round($sousTotal, 2),
            'frais'            => round($frais, 2),
            'total'            => round($sousTotal + $frais, 2),
            'franco'           => false,
            'seuil_franco'     => null,
            'reste_franco'     => 0.0,
            'frais_forfait'    => $forfait,
            'delai_livraison'  => $delai,
        ];
    }

    /** Montant des frais seuls (compat CommandeModel / panier). */
    public function montantFrais(float $sousTotal): float {
        return $this->calculer($sousTotal)['frais'];
    }
}
