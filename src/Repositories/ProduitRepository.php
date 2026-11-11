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
            'SELECT p.*, c.libelle AS categorie_libelle, c.slug AS categorie_slug
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

    public function findAllAdmin(): array {
        if (!$this->conn) {
            return [];
        }
        $stmt = $this->conn->query(
            'SELECT p.*, c.libelle AS categorie_libelle
             FROM produit p
             JOIN categorie c ON c.categorie_id = p.categorie_id
             ORDER BY p.produit_id DESC'
        );
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

        $img = $this->conn->prepare(
            'INSERT INTO produit_image (produit_id, chemin, alt, ordre) VALUES (:pid, :chemin, :alt, 0)'
        );
        $img->execute([
            ':pid'    => $id,
            ':chemin' => 'produits/default.svg',
            ':alt'    => $data['nom'],
        ]);
        return $id;
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
