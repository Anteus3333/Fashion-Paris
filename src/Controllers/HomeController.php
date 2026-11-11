<?php
// src/Controllers/HomeController.php

require_once __DIR__ . '/../Models/ProduitModel.php';

class HomeController {

    public function index() {
        $titrePage = 'Accueil — Fashion Paris';
        $produitModel = new ProduitModel();
        $produitsVedette = $produitModel->getMisEnAvant(4);
        require __DIR__ . '/../Views/home.php';
    }
}
