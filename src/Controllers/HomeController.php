<?php
// src/Controllers/HomeController.php

require_once __DIR__ . '/../Models/ProduitModel.php';
require_once __DIR__ . '/../Models/CategorieModel.php';

class HomeController {

    public function index() {
        $titrePage = 'Accueil — Fashion Paris';
        $produitModel = new ProduitModel();
        $produitsVedette = $produitModel->getMisEnAvant(4);
        $categories = (new CategorieModel())->getAll();
        require __DIR__ . '/../Views/home.php';
    }
}
