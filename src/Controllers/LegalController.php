<?php
// src/Controllers/LegalController.php

class LegalController {

    public function mentions() {
        $titrePage = 'Mentions légales — Fashion Paris';
        require __DIR__ . '/../Views/legal/mentions-legales.php';
    }

    public function accessibilite() {
        $titrePage = 'Accessibilité — Fashion Paris';
        require __DIR__ . '/../Views/legal/accessibilite.php';
    }
}
