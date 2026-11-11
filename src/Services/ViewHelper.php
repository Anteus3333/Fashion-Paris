<?php
// src/Services/ViewHelper.php — helpers d'affichage réutilisables

class ViewHelper {

    public static function formatPrenom(?string $prenom): string {
        $prenom = trim((string) $prenom);
        if ($prenom === '') {
            return '';
        }
        return mb_convert_case(mb_strtolower($prenom, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    public static function formatNom(?string $nom): string {
        $nom = trim((string) $nom);
        if ($nom === '') {
            return '';
        }
        return mb_strtoupper($nom, 'UTF-8');
    }

    public static function formatNomComplet(?string $prenom, ?string $nom, string $fallback = ''): string {
        $complet = trim(self::formatPrenom($prenom) . ' ' . self::formatNom($nom));
        return $complet !== '' ? $complet : $fallback;
    }

    public static function prix(float|string|null $montant): string {
        return number_format((float) $montant, 2, ',', ' ') . ' €';
    }

    public static function btnNav(string $url, string $label, string $classes = 'btn', array $attrs = []): string {
        $action = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        $labelEsc = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        $classesEsc = htmlspecialchars($classes, ENT_QUOTES, 'UTF-8');
        $extra = '';
        foreach ($attrs as $name => $value) {
            $extra .= ' ' . htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8')
                . '="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '"';
        }
        return '<button type="button" class="' . $classesEsc . '" data-navigate="' . $action . '"' . $extra . '>'
            . $labelEsc
            . '</button>';
    }
}
