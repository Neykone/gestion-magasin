<?php
// models/ProductModel.php
require_once 'config/Database.php';
require_once 'models/entities/Product.php';

class ProductModel {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Récupérer tous les produits (retourne des objets Product)
     */
    public function getAllProducts() {
        $sql = "SELECT p.*, 
                       c.nom as categorie_nom, 
                       f.nom as fournisseur_nom 
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
                ORDER BY p.id DESC";

        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll();

        $products = [];
        foreach ($data as $row) {
            $products[] = new Product($row);
        }
        return $products;
    }

    /**
     * Récupérer un produit par son ID (retourne un objet Product)
     */
    public function getProductById($id) {
        $sql = "SELECT p.*, 
                       c.nom as categorie_nom, 
                       f.nom as fournisseur_nom 
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
                WHERE p.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Product($data);
        }
        return null;
    }

    /**
     * Ajouter un nouveau produit (utilise un objet Product)
     */
    public function addProduct(Product $product) {
        $sql = "INSERT INTO produits (nom, description, prix_achat, prix_vente, stock, seuil_alerte, categorie_id, fournisseur_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $product->getNom(),
            $product->getDescription(),
            $product->getPrixAchat(),
            $product->getPrixVente(),
            $product->getStock(),
            $product->getSeuilAlerte(),
            $product->getCategorieId(),
            $product->getFournisseurId()
        ]);
    }

    /**
     * Modifier un produit (utilise un objet Product)
     */
    public function updateProduct(Product $product) {
        $sql = "UPDATE produits 
                SET nom = ?, description = ?, prix_achat = ?, prix_vente = ?, 
                    stock = ?, seuil_alerte = ?, categorie_id = ?, fournisseur_id = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $product->getNom(),
            $product->getDescription(),
            $product->getPrixAchat(),
            $product->getPrixVente(),
            $product->getStock(),
            $product->getSeuilAlerte(),
            $product->getCategorieId(),
            $product->getFournisseurId(),
            $product->getId()
        ]);
    }

    /**
     * Supprimer un produit
     */
    public function deleteProduct($id) {
        // Vérifier si le produit est utilisé dans des ventes
        $checkSql = "SELECT COUNT(*) as count FROM vente_details WHERE produit_id = ?";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false;
        }

        $sql = "DELETE FROM produits WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Mettre à jour le stock
     */
    public function updateStock($id, $quantite_vendue) {
        $sql = "UPDATE produits SET stock = stock - ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantite_vendue, $id]);
    }

    /**
     * Récupérer les produits avec stock faible
     */
    public function getLowStockProducts() {
        $sql = "SELECT p.*, 
                       c.nom as categorie_nom,
                       f.nom as fournisseur_nom
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
                WHERE p.stock <= p.seuil_alerte AND p.stock > 0
                ORDER BY p.stock ASC";

        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll();

        $products = [];
        foreach ($data as $row) {
            $products[] = new Product($row);
        }
        return $products;
    }

    /**
     * Récupérer les produits par fournisseur
     */
    public function getProductsBySupplier($fournisseurId) {
        $sql = "SELECT p.*, c.nom as categorie_nom 
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                WHERE p.fournisseur_id = ?
                ORDER BY p.nom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fournisseurId]);
        $data = $stmt->fetchAll();

        $products = [];
        foreach ($data as $row) {
            $products[] = new Product($row);
        }
        return $products;
    }

    /**
     * Compter le nombre total de produits
     */
    public function countProducts() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM produits");
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Calculer la valeur totale du stock
     */
    public function getTotalStockValue() {
        $stmt = $this->db->query("SELECT SUM(prix_achat * stock) as total FROM produits");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
?>