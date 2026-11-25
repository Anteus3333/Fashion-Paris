<?php
// src/Controllers/AdminController.php — back-office

require_once __DIR__ . '/../Services/AuthGuard.php';
require_once __DIR__ . '/../Services/Csrf.php';
require_once __DIR__ . '/../Services/ImageUploadService.php';
require_once __DIR__ . '/../Models/ProduitModel.php';
require_once __DIR__ . '/../Models/CategorieModel.php';
require_once __DIR__ . '/../Models/CommandeModel.php';
require_once __DIR__ . '/../Models/UtilisateurModel.php';
require_once __DIR__ . '/../Models/MysqlStatsModel.php';
require_once __DIR__ . '/../Models/MongoStatsModel.php';

require_once __DIR__ . '/../Models/HoraireModel.php';
require_once __DIR__ . '/Traits/GestionCommandesTrait.php';

class AdminController {
    use GestionCommandesTrait;

    public function index() {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $titrePage = 'Administration — Fashion Paris';
        $statsModel = new MysqlStatsModel();
        $stats = $statsModel->resume();
        $commandesRecentes = (new CommandeModel())->getAll();
        $commandesRecentes = array_slice($commandesRecentes, 0, 5);
        $config = require __DIR__ . '/../../config/commandeconfig.php';
        require __DIR__ . '/../Views/admin/index.php';
    }

    public function produits() {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $titrePage = 'Produits — Admin';
        $categories = (new CategorieModel())->getAll();
        $filtres = [
            'q'            => trim((string) ($_GET['q'] ?? '')),
            'categorie_id' => ($_GET['categorie_id'] ?? '') !== '' ? (int) $_GET['categorie_id'] : null,
            'actif'        => (string) ($_GET['actif'] ?? ''),
            'vedette'      => (string) ($_GET['vedette'] ?? ''),
        ];
        if (!in_array($filtres['actif'], ['', '0', '1'], true)) {
            $filtres['actif'] = '';
        }
        if (!in_array($filtres['vedette'], ['', '0', '1'], true)) {
            $filtres['vedette'] = '';
        }
        $produits = (new ProduitModel())->filtrerAdmin($filtres);
        require __DIR__ . '/../Views/admin/produits.php';
    }

    public function produitForm(?int $id = null) {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $model = new ProduitModel();
        $categories = (new CategorieModel())->getAll();
        $produit = $id ? $model->getById($id) : null;
        if ($id && !$produit) {
            $_SESSION['flash_erreur'] = 'Produit introuvable.';
            header('Location: ' . BASE_URL . '/admin/produits');
            exit;
        }

        $titrePage = ($produit ? 'Modifier' : 'Ajouter') . ' un produit — Admin';
        $erreurs = [];
        $old = [
            'categorie_id'  => $produit['categorie_id'] ?? ($categories[0]['categorie_id'] ?? ''),
            'nom'           => $produit['nom'] ?? '',
            'slug'          => $produit['slug'] ?? '',
            'description'   => $produit['description'] ?? '',
            'prix'          => $produit['prix'] ?? '',
            'stock'         => $produit['stock'] ?? '0',
            'sku'           => $produit['sku'] ?? '',
            'actif'         => isset($produit) ? (string) (int) $produit['actif'] : '1',
            'mis_en_avant'  => isset($produit) ? (string) (int) $produit['mis_en_avant'] : '0',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach (['categorie_id', 'nom', 'description', 'prix', 'stock'] as $champ) {
                $old[$champ] = trim((string) ($_POST[$champ] ?? ''));
            }
            $old['actif'] = isset($_POST['actif']) ? '1' : '0';
            $old['mis_en_avant'] = isset($_POST['mis_en_avant']) ? '1' : '0';
            $remplaceId = (int) ($_POST['remplace_mis_en_avant'] ?? 0);
            // Slug / SKU hors formulaire : slug auto à la création, conservation à l’édition
            if ($old['slug'] === '') {
                $old['slug'] = ProduitModel::slugifier($old['nom']);
            }

            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée.';
            }
            if ($old['nom'] === '') {
                $erreurs[] = 'Le nom est obligatoire.';
            }
            if (!is_numeric($old['prix']) || (float) $old['prix'] < 0) {
                $erreurs[] = 'Prix invalide.';
            }
            if (!ctype_digit((string) $old['stock'])) {
                $erreurs[] = 'Stock invalide.';
            }
            if ($model->slugExiste($old['slug'], $id)) {
                $erreurs[] = 'Ce slug existe déjà.';
            }

            // Mise en avant : uniquement si actif, max 3 places (remplacement explicite)
            if ($old['actif'] !== '1') {
                $old['mis_en_avant'] = '0';
            }
            $dejaEnAvant = $id && !empty($produit['mis_en_avant']);
            $autresVedettes = $model->getMisEnAvantAdmin($id ?: null);
            $idsVedettes = array_map(static fn(array $p): int => (int) $p['produit_id'], $autresVedettes);
            if ($old['mis_en_avant'] === '1' && !$dejaEnAvant && count($autresVedettes) >= ProduitModel::MAX_MIS_EN_AVANT) {
                if ($remplaceId <= 0 || !in_array($remplaceId, $idsVedettes, true)) {
                    $erreurs[] = 'L’accueil n’affiche que ' . ProduitModel::MAX_MIS_EN_AVANT
                        . ' mises en avant : choisissez le produit à remplacer.';
                }
            } else {
                $remplaceId = 0;
            }

            $uploader = new ImageUploadService();
            $upload = $uploader->telechargerProduit($_FILES['image'] ?? null, false);
            if (!$upload['ok']) {
                $erreurs[] = $upload['erreur'] ?? 'Upload image impossible.';
            }

            if (empty($erreurs)) {
                $payload = [
                    'categorie_id'  => (int) $old['categorie_id'],
                    'nom'           => $old['nom'],
                    'slug'          => $old['slug'],
                    'description'   => $old['description'],
                    'prix'          => (float) $old['prix'],
                    'stock'         => (int) $old['stock'],
                    'sku'           => $old['sku'],
                    'actif'         => (int) $old['actif'],
                    'mis_en_avant'  => (int) $old['mis_en_avant'],
                ];
                if ($upload['relatif']) {
                    $payload['image'] = $upload['relatif'];
                }
                if ($remplaceId > 0) {
                    $model->retirerMisEnAvant($remplaceId);
                }
                if ($id) {
                    $model->update($id, $payload);
                    if ($upload['relatif']) {
                        $ancien = $model->remplacerImagePrincipale($id, $upload['relatif'], $old['nom']);
                        $uploader->supprimerSiUpload($ancien);
                    }
                    $savedId = $id;
                    $_SESSION['flash_succes'] = 'Produit mis à jour.';
                } else {
                    $savedId = $model->create($payload);
                    $_SESSION['flash_succes'] = 'Produit créé.';
                }
                // Garde-fou : au plus MAX vedettes au total (ce produit + MAX-1 autres)
                if ((int) $payload['mis_en_avant'] === 1 && $savedId) {
                    $autres = $model->getMisEnAvantAdmin((int) $savedId);
                    while (count($autres) > ProduitModel::MAX_MIS_EN_AVANT - 1) {
                        $model->retirerMisEnAvant((int) $autres[0]['produit_id']);
                        $autres = $model->getMisEnAvantAdmin((int) $savedId);
                    }
                }
                header('Location: ' . BASE_URL . '/admin/produits');
                exit;
            }

            if (!empty($upload['relatif'])) {
                $uploader->supprimerSiUpload($upload['relatif']);
            }
        }

        $vedettesAutres = $model->getMisEnAvantAdmin($id ?: null);
        $placesVedettesPleines = count($vedettesAutres) >= ProduitModel::MAX_MIS_EN_AVANT;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $remplaceId = 0;
        }
        require __DIR__ . '/../Views/admin/produit-form.php';
    }

    public function clients() {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $titrePage = 'Clients — Admin';
        $clients = (new UtilisateurModel())->getClients();
        require __DIR__ . '/../Views/admin/clients.php';
    }

    public function stats() {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $mongo = new MongoStatsModel();
        $mysql = new MysqlStatsModel();
        $mongoOk = $mongo->estDisponible();
        $nbDocs = 0;
        $series = ['labels' => [], 'commandes' => [], 'ca' => []];
        // Source effective des graphiques (mongodb prioritaire, mysql en secours)
        $statsSource = 'mysql';
        $statsErreur = null;

        if ($mongoOk && $_SERVER['REQUEST_METHOD'] === 'POST' && Csrf::verifier() && isset($_POST['sync'])) {
            try {
                $n = $mongo->syncDepuisMysql();
                $_SESSION['flash_succes'] = "Sync effectuée : {$n} document(s).";
            } catch (Throwable $e) {
                $_SESSION['flash_erreur'] = 'Sync échouée : ' . $e->getMessage();
            }
            header('Location: ' . BASE_URL . '/admin/stats');
            exit;
        }

        if ($mongoOk) {
            try {
                $nbDocs = $mongo->compter();
                if ($nbDocs > 0) {
                    $series = $mongo->seriesMensuelles(12);
                    $statsSource = 'mongodb';
                } else {
                    // Mongo joignable mais collection vide → MySQL pour ne pas laisser l’admin sans graphe
                    $series = $mysql->seriesMensuelles(12);
                    $statsSource = 'mysql';
                    $statsErreur = 'MongoDB synchronisé à 0 document — affichage MySQL en secours. Lancez une synchronisation.';
                }
            } catch (Throwable $e) {
                $statsErreur = 'Mongo : ' . $e->getMessage() . ' — affichage MySQL en secours.';
                $series = $mysql->seriesMensuelles(12);
                $statsSource = 'mysql';
            }
        } else {
            $statsErreur = 'MongoDB non configuré ou indisponible — affichage MySQL en secours.';
            $series = $mysql->seriesMensuelles(12);
            $statsSource = 'mysql';
        }

        $titrePage = 'Stats — Admin';
        require __DIR__ . '/../Views/admin/stats.php';
    }

    public function horaires(): void {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $model = new HoraireModel();
        $horaires = $model->getAll();
        $erreurs = [];
        $groupes = $model->groupesEdition($horaires);
        $semaine = $groupes['semaine'];
        $samedi = $groupes['samedi'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée.';
            } else {
                $semaine = [
                    'ouverture' => trim((string) ($_POST['ouverture_semaine'] ?? '')),
                    'fermeture' => trim((string) ($_POST['fermeture_semaine'] ?? '')),
                ];
                $samedi = [
                    'ouverture' => trim((string) ($_POST['ouverture_samedi'] ?? '')),
                    'fermeture' => trim((string) ($_POST['fermeture_samedi'] ?? '')),
                ];
                if (!$model->horairesValides($semaine) || !$model->horairesValides($samedi)) {
                    $erreurs[] = 'Horaires invalides (format HH:MM).';
                } else {
                    $model->enregistrerGroupes($semaine, $samedi);
                    $_SESSION['flash_succes'] = 'Horaires mis à jour (pied de page et page Contact).';
                    header('Location: ' . BASE_URL . '/admin/horaires');
                    exit;
                }
            }
        }

        $titrePage = 'Horaires — Admin';
        require __DIR__ . '/../Views/admin/horaires.php';
    }

    public function personnels(): void {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $personnels = (new UtilisateurModel())->getPersonnels();
        $titrePage = 'Gestion du personnel — Admin';
        require __DIR__ . '/../Views/admin/personnels.php';
    }

    public function personnelCreer(): void {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $model = new UtilisateurModel();
        $erreurs = [];
        $erreursPassword = [];
        $erreurConfirmation = null;
        $old = $this->champsPersonnelVides();
        $modeEdition = false;
        $personnelId = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = $this->lireChampsPersonnel();
            $password = (string) ($_POST['password'] ?? '');
            $confirmation = (string) ($_POST['confirmation'] ?? '');

            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée.';
            }
            $erreurs = array_merge($erreurs, $this->validerChampsPersonnel($old));
            $erreursPassword = $this->validerMotDePasse($password);
            if ($password !== $confirmation) {
                $erreurConfirmation = 'La confirmation ne correspond pas au mot de passe.';
            }
            if (empty($erreurs) && empty($erreursPassword) && $erreurConfirmation === null && $model->findByEmail($old['email'])) {
                $erreurs[] = 'Un compte existe déjà avec cette adresse mail.';
            }
            if (empty($erreurs) && empty($erreursPassword) && $erreurConfirmation === null) {
                $model->createPersonnel($old + ['password' => $password]);
                $_SESSION['flash_succes'] = 'Compte personnel créé.';
                header('Location: ' . BASE_URL . '/admin/personnel');
                exit;
            }
        }

        $titrePage = 'Nouveau compte personnel — Admin';
        require __DIR__ . '/../Views/admin/personnel-form.php';
    }

    public function personnelModifier(int $id): void {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        $model = new UtilisateurModel();
        if (!$model->estPersonnel($id)) {
            $_SESSION['flash_erreur'] = 'Compte personnel introuvable.';
            header('Location: ' . BASE_URL . '/admin/personnel');
            exit;
        }

        $pers = $model->findById($id);
        $erreurs = [];
        $erreursPassword = [];
        $erreurConfirmation = null;
        $old = [
            'prenom'          => $pers['prenom'] ?? '',
            'nom'             => $pers['nom'] ?? '',
            'email'           => $pers['email'] ?? '',
            'telephone'       => $pers['telephone'] ?? '',
            'adresse_postale' => $pers['adresse_postale'] ?? '',
            'code_postal'     => $pers['code_postal'] ?? '',
            'ville'           => $pers['ville'] ?? '',
        ];
        $modeEdition = true;
        $personnelId = $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = $this->lireChampsPersonnel();
            $password = (string) ($_POST['password'] ?? '');
            $confirmation = (string) ($_POST['confirmation'] ?? '');

            if (!Csrf::verifier()) {
                $erreurs[] = 'Session expirée.';
            }
            $erreurs = array_merge($erreurs, $this->validerChampsPersonnel($old));
            if ($password !== '' || $confirmation !== '') {
                $erreursPassword = $this->validerMotDePasse($password);
                if ($password !== $confirmation) {
                    $erreurConfirmation = 'La confirmation ne correspond pas au mot de passe.';
                }
            }
            if (empty($erreurs) && empty($erreursPassword) && $erreurConfirmation === null) {
                $existant = $model->findByEmail($old['email']);
                if ($existant && (int) $existant['utilisateur_id'] !== $id) {
                    $erreurs[] = 'Un compte existe déjà avec cette adresse mail.';
                }
            }
            if (empty($erreurs) && empty($erreursPassword) && $erreurConfirmation === null) {
                $payload = $old;
                if ($password !== '') {
                    $payload['password'] = $password;
                }
                $model->updatePersonnel($id, $payload);
                $_SESSION['flash_succes'] = 'Compte personnel mis à jour.';
                header('Location: ' . BASE_URL . '/admin/personnel');
                exit;
            }
        }

        $titrePage = 'Modifier le compte personnel — Admin';
        require __DIR__ . '/../Views/admin/personnel-form.php';
    }

    public function personnelSupprimer(int $id): void {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verifier()) {
            $_SESSION['flash_erreur'] = 'Action non autorisée.';
            header('Location: ' . BASE_URL . '/admin/personnel');
            exit;
        }
        $model = new UtilisateurModel();
        if (!$model->estPersonnel($id)) {
            $_SESSION['flash_erreur'] = 'Compte personnel introuvable.';
        } elseif ($model->supprimerPersonnel($id)) {
            $_SESSION['flash_succes'] = 'Compte personnel supprimé.';
        } else {
            $_SESSION['flash_erreur'] = 'Impossible de supprimer (données liées ou introuvable).';
        }
        header('Location: ' . BASE_URL . '/admin/personnel');
        exit;
    }

    public function personnelDesactiver(int $id): void {
        $this->actionPersonnelActif($id, false);
    }

    public function personnelActiver(int $id): void {
        $this->actionPersonnelActif($id, true);
    }

    private function actionPersonnelActif(int $id, bool $actif): void {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verifier()) {
            $_SESSION['flash_erreur'] = 'Action non autorisée.';
            header('Location: ' . BASE_URL . '/admin/personnel');
            exit;
        }
        $model = new UtilisateurModel();
        if (!$model->estPersonnel($id)) {
            $_SESSION['flash_erreur'] = 'Compte personnel introuvable.';
        } elseif ($model->setActifPersonnel($id, $actif)) {
            $_SESSION['flash_succes'] = $actif ? 'Compte réactivé.' : 'Compte désactivé.';
        } else {
            $_SESSION['flash_erreur'] = 'Action impossible.';
        }
        header('Location: ' . BASE_URL . '/admin/personnel');
        exit;
    }

    private function champsPersonnelVides(): array {
        return [
            'prenom' => '', 'nom' => '', 'email' => '', 'telephone' => '',
            'adresse_postale' => '', 'code_postal' => '', 'ville' => '',
        ];
    }

    private function lireChampsPersonnel(): array {
        $old = $this->champsPersonnelVides();
        foreach (array_keys($old) as $champ) {
            $old[$champ] = trim((string) ($_POST[$champ] ?? ''));
        }
        return $old;
    }

    private function validerChampsPersonnel(array $old): array {
        $erreurs = [];
        if ($old['prenom'] === '') {
            $erreurs[] = 'Le prénom est obligatoire.';
        }
        if ($old['nom'] === '') {
            $erreurs[] = 'Le nom est obligatoire.';
        }
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = "L'adresse mail n'est pas valide.";
        }
        if ($old['adresse_postale'] === '') {
            $erreurs[] = "L'adresse postale est obligatoire.";
        }
        if ($old['code_postal'] === '') {
            $erreurs[] = 'Le code postal est obligatoire.';
        } elseif (!preg_match('/^\d{5}$/', $old['code_postal'])) {
            $erreurs[] = 'Le code postal doit contenir 5 chiffres.';
        }
        if ($old['ville'] === '') {
            $erreurs[] = 'La ville est obligatoire.';
        }
        return $erreurs;
    }

    /** @return string[] */
    private function validerMotDePasse(string $password): array {
        $erreurs = [];
        if (strlen($password) < 10) {
            $erreurs[] = 'Le mot de passe doit contenir au moins 10 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $erreurs[] = 'Le mot de passe doit contenir au moins une majuscule.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $erreurs[] = 'Le mot de passe doit contenir au moins une minuscule.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $erreurs[] = 'Le mot de passe doit contenir au moins un chiffre.';
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $erreurs[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
        }
        return $erreurs;
    }

    protected function gestionBasePath(): string {
        return '/admin';
    }

    protected function gestionCommandesListPath(): string {
        return '/commandes';
    }

    protected function gestionTitreEspace(): string {
        return 'Admin';
    }

    protected function exigerGestionAcces(): void {
        AuthGuard::exigerRole(AuthGuard::rolesAdmin(), '/admin');
    }
}

