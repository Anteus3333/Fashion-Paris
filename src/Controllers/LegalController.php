<?php
// src/Controllers/LegalController.php

class LegalController {

    public function mentions() {
        $titrePage = 'Mentions légales — Fashion Paris';
        require __DIR__ . '/../Views/legal/mentions-legales.php';
    }

    public function cgv() {
        $titrePage = 'Conditions générales de vente — Fashion Paris';
        require __DIR__ . '/../Views/legal/cgv.php';
    }

    public function accessibilite() {
        $titrePage = 'Accessibilité — Fashion Paris';
        require __DIR__ . '/../Views/legal/accessibilite.php';
    }

    public function politiqueConfidentialite() {
        $titrePage = 'Politique de confidentialité — Fashion Paris';
        require __DIR__ . '/../Views/legal/politique-confidentialite.php';
    }
}
