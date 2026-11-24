<?php
// src/Repositories/ProduitRepository.php — accès SQL catalogue produits

require_once __DIR__ . '/../Services/SqlDatabase.php';

class ProduitRepository {
    private $conn;

    public function __construct() {
        $database = new SqlDatabase();
        $this->conn = $database->getConnection();
    }

    public function findById(int $id): ?array {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.libelle AS categorie_libelle, c.slug AS categorie_slug,
                    (SELECT chemin FROM produit_image pi WHERE pi.produit_id = p.produit_id ORDER BY ordre ASC LIMIT 1) AS image
             FROM produit p
             JOIN categorie c ON c.categorie_id = p.categorie_id
             WHERE p.produit_id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findMisEnAvant(int $limit = 6): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.libelle AS categorie_libelle,
                    (SELECT chemin FROM produit_image pi WHERE pi.produit_id = p.produit_id ORDER BY ordre ASC LIMIT 1) AS image
             FROM produit p
             JOIN categorie c ON c.categorie_id = p.categorie_id
             WHERE p.actif = 1 AND p.mis_en_avant = 1
             ORDER BY p.produit_id DESC
             LIMIT :lim'
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Produits marqués mis en avant (admin), hors éventuel produit en cours d’édition. */
    public function findMisEnAvantAdmin(?int $exclureId = null): array {
        if (!$this->conn) {
            return [];
        }
        $sql = 'SELECT produit_id, nom, actif, mis_en_avant
                FROM produit
                WHERE mis_en_avant = 1';
        $params = [];
        if ($exclureId !== null) {
            $sql .= ' AND produit_id <> :id';
            $params[':id'] = $exclureId;
        }
        $sql .= ' ORDER BY nom ASC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function retirerMisEnAvant(int $produitId): void {
        if (!$this->conn) {
            return;
        }
        $stmt = $this->conn->prepare(
            'UPDATE produit SET mis_en_avant = 0 WHERE produit_id = :id'
        );
        $stmt->execute([':id' => $produitId]);
    }

    public function findAllActifs(?int $categorieId = null): array {
        if (!$this->conn) {
            return [];
        }
        $sql = 'SELECT p.*, c.libelle AS categorie_libelle,
                       (SELECT chemin FROM produit_image pi WHERE pi.produit_id = p.produit_id ORDER BY ordre ASC LIMIT 1) AS image
                FROM produit p
                JOIN categorie c ON c.categorie_id = p.categorie_id
                WHERE p.actif = 1';
        $params = [];
        if ($categorieId !== null) {
            $sql .= ' AND p.categorie_id = :cat';
            $params[':cat'] = $categorieId;
        }
        $sql .= ' ORDER BY p.nom ASC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.libelle AS categorie_libelle, c.slug AS categorie_slug,
                    (SELECT chemin FROM produit_image pi WHERE pi.produit_id = p.produit_id ORDER BY ordre ASC LIMIT 1) AS image
             FROM produit p
             JOIN categorie c ON c.categorie_id = p.categorie_id
             WHERE p.slug = :slug AND p.actif = 1'
        );
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function search(string $q): array {
        if (!$this->conn) {
            return [];
        }
        $like = '%' . $q . '%';
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.libelle AS categorie_libelle,
                    (SELECT chemin FROM produit_image pi WHERE pi.produit_id = p.produit_id ORDER BY ordre ASC LIMIT 1) AS image
             FROM produit p
             JOIN categorie c ON c.categorie_id = p.categorie_id
             WHERE p.actif = 1 AND (p.nom LIKE :q OR p.description LIKE :q2 OR c.libelle LIKE :q3)
             ORDER BY p.nom ASC'
        );
        $stmt->execute([':q' => $like, ':q2' => $like, ':q3' => $like]);
        return $stmt->fetchAll();
    }

    /** Filtres catalogue (AJAX) : catégorie, recherche, prix min/max. */
    public function findFiltres(?int $categorieId, string $q = '', ?float $prixMin = null, ?float $prixMax = null): array {
        if (!$this->conn) {
            return [];
        }
        $sql = 'SELECT p.*, c.libelle AS categorie_libelle,
                       (SELECT chemin FROM produit_image pi WHERE pi.produit_id = p.produit_id ORDER BY ordre ASC LIMIT 1) AS image
                FROM produit p
                JOIN categorie c ON c.categorie_id = p.categorie_id
                WHERE p.actif = 1';
        $params = [];
        if ($categorieId !== null) {
            $sql .= ' AND p.categorie_id = :cat';
            $params[':cat'] = $categorieId;
        }
        $q = trim($q);
        if ($q !== '') {
            $like = '%' . $q . '%';
            $sql .= ' AND (p.nom LIKE :q OR p.description LIKE :q2 OR c.libelle LIKE :q3)';
            $params[':q'] = $like;
            $params[':q2'] = $like;
            $params[':q3'] = $like;
        }
        if ($prixMin !== null) {
            $sql .= ' AND p.prix >= :pmin';
            $params[':pmin'] = $prixMin;
        }
        if ($prixMax !== null) {
            $sql .= ' AND p.prix <= :pmax';
            $params[':pmax'] = $prixMax;
        }
        $sql .= ' ORDER BY p.nom ASC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function findAllAdmin(): array {
        return $this->findAdminFiltres();
    }

    /**
     * Listing admin : recherche, catégorie, visibilité, vedette.
     * @param array{q?: string, categorie_id?: int|null, actif?: string, vedette?: string} $filtres
     */
    public function findAdminFiltres(array $filtres = []): array {
        if (!$this->conn) {
            return [];
        }
        $sql = 'SELECT p.*, c.libelle AS categorie_libelle
                FROM produit p
                JOIN categorie c ON c.categorie_id = p.categorie_id
                WHERE 1=1';
        $params = [];

        $q = trim((string) ($filtres['q'] ?? ''));
        if ($q !== '') {
            $like = '%' . $q . '%';
            $sql .= ' AND (p.nom LIKE :q OR p.sku LIKE :q2 OR p.description LIKE :q3 OR c.libelle LIKE :q4)';
            $params[':q'] = $like;
            $params[':q2'] = $like;
            $params[':q3'] = $like;
            $params[':q4'] = $like;
        }

        $categorieId = $filtres['categorie_id'] ?? null;
        if ($categorieId !== null && $categorieId !== '') {
            $sql .= ' AND p.categorie_id = :cat';
            $params[':cat'] = (int) $categorieId;
        }

        $actif = (string) ($filtres['actif'] ?? '');
        if ($actif === '1' || $actif === '0') {
            $sql .= ' AND p.actif = :actif';
            $params[':actif'] = (int) $actif;
        }

        $vedette = (string) ($filtres['vedette'] ?? '');
        if ($vedette === '1') {
            $sql .= ' AND p.mis_en_avant = 1';
        } elseif ($vedette === '0') {
            $sql .= ' AND p.mis_en_avant = 0';
        }

        $sql .= ' ORDER BY p.produit_id DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function create(array $data): int {
        $stmt = $this->conn->prepare("
            INSERT INTO produit (categorie_id, nom, slug, description, prix, stock, sku, actif, mis_en_avant)
            VALUES (:categorie_id, :nom, :slug, :description, :prix, :stock, :sku, :actif, :mis_en_avant)
        ");
        $stmt->execute([
            ':categorie_id' => $data['categorie_id'],
            ':nom'          => $data['nom'],
            ':slug'         => $data['slug'],
            ':description'  => $data['description'],
            ':prix'         => $data['prix'],
            ':stock'        => $data['stock'],
            ':sku'          => $data['sku'],
            ':actif'        => $data['actif'],
            ':mis_en_avant' => $data['mis_en_avant'],
        ]);
        $id = (int) $this->conn->lastInsertId();

        $chemin = trim((string) ($data['image'] ?? ''));
        if ($chemin === '') {
            $chemin = 'produits/default.svg';
        }
        $img = $this->conn->prepare(
            'INSERT INTO produit_image (produit_id, chemin, alt, ordre) VALUES (:pid, :chemin, :alt, 0)'
        );
        $img->execute([
            ':pid'    => $id,
            ':chemin' => $chemin,
            ':alt'    => $data['nom'],
        ]);
        return $id;
    }

    /** Remplace l’image principale (ordre 0) ; retourne l’ancien chemin si présent. */
    public function remplacerImagePrincipale(int $produitId, string $chemin, string $alt): ?string {
        if (!$this->conn) {
            return null;
        }
        $stmt = $this->conn->prepare(
            'SELECT chemin FROM produit_image WHERE produit_id = :pid ORDER BY ordre ASC LIMIT 1'
        );
        $stmt->execute([':pid' => $produitId]);
        $ancien = $stmt->fetchColumn();
        $ancien = $ancien !== false ? (string) $ancien : null;

        if ($ancien !== null) {
            $upd = $this->conn->prepare(
                'UPDATE produit_image SET chemin = :chemin, alt = :alt
                 WHERE image_id = (
                     SELECT image_id FROM (
                         SELECT image_id FROM produit_image
                         WHERE produit_id = :pid ORDER BY ordre ASC LIMIT 1
                     ) AS t
                 )'
            );
            $upd->execute([
                ':chemin' => $chemin,
                ':alt'    => $alt,
                ':pid'    => $produitId,
            ]);
        } else {
            $ins = $this->conn->prepare(
                'INSERT INTO produit_image (produit_id, chemin, alt, ordre) VALUES (:pid, :chemin, :alt, 0)'
            );
            $ins->execute([
                ':pid'    => $produitId,
                ':chemin' => $chemin,
                ':alt'    => $alt,
            ]);
        }

        return $ancien;
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->conn->prepare("
            UPDATE produit SET
                categorie_id = :categorie_id,
                nom = :nom,
                slug = :slug,
                description = :description,
                prix = :prix,
                stock = :stock,
                sku = :sku,
                actif = :actif,
                mis_en_avant = :mis_en_avant
            WHERE produit_id = :id
        ");
        $stmt->execute([
            ':categorie_id' => $data['categorie_id'],
            ':nom'          => $data['nom'],
            ':slug'         => $data['slug'],
            ':description'  => $data['description'],
            ':prix'         => $data['prix'],
            ':stock'        => $data['stock'],
            ':sku'          => $data['sku'],
            ':actif'        => $data['actif'],
            ':mis_en_avant' => $data['mis_en_avant'],
            ':id'           => $id,
        ]);
        return true;
    }

    public function slugExiste(string $slug, ?int $exclureId = null): bool {
        $sql = 'SELECT produit_id FROM produit WHERE slug = :slug';
        $params = [':slug' => $slug];
        if ($exclureId !== null) {
            $sql .= ' AND produit_id <> :id';
            $params[':id'] = $exclureId;
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetch();
    }
}
