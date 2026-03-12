<?php
// models/ProductModel.php
require_once 'config/Database.php';

class ProductModel {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Récupérer tous les produits
     */
    public function getAllProducts() {
        $sql = "SELECT p.*, 
                   c.nom as categorie_nom, 
                   f.nom as fournisseur_nom 
            FROM produits p
            LEFT JOIN categories c ON p.categorie_id = c.id
            LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
            ORDER BY p.nom ASC";

        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        return $result ?: []; // Retourne un tableau vide si pas de résultats
    }

    /**
     * Récupérer un produit par son ID
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
        return $stmt->fetch();
    }

    /**
     * Ajouter un nouveau produit
     */
    public function addProduct($nom, $description, $prix_achat, $prix_vente, $stock, $seuil_alerte, $categorie_id, $fournisseur_id) {
        $sql = "INSERT INTO produits (nom, description, prix_achat, prix_vente, stock, seuil_alerte, categorie_id, fournisseur_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $description, $prix_achat, $prix_vente, $stock, $seuil_alerte, $categorie_id, $fournisseur_id]);
    }

    /**
     * Modifier un produit
     */
    public function updateProduct($id, $nom, $description, $prix_achat, $prix_vente, $stock, $seuil_alerte, $categorie_id, $fournisseur_id) {
        $sql = "UPDATE produits 
                SET nom = ?, description = ?, prix_achat = ?, prix_vente = ?, 
                    stock = ?, seuil_alerte = ?, categorie_id = ?, fournisseur_id = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $description, $prix_achat, $prix_vente, $stock, $seuil_alerte, $categorie_id, $fournisseur_id, $id]);
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
            // Le produit est dans des ventes, on ne peut pas le supprimer
            return false;
        }

        $sql = "DELETE FROM produits WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Mettre à jour le stock après une vente
     */
    public function updateStock($id, $quantite_vendue) {
        $sql = "UPDATE produits SET stock = stock - ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantite_vendue, $id]);
    }

    /**
     * Récupérer les produits avec stock faible (en dessous du seuil d'alerte)
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
        return $stmt->fetchAll();
    }

    /**
     * Récupérer les produits en rupture de stock
     */
    public function getOutOfStockProducts() {
        $sql = "SELECT p.*, 
                       c.nom as categorie_nom,
                       f.nom as fournisseur_nom
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
                WHERE p.stock = 0
                ORDER BY p.id DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Rechercher des produits
     */
    public function searchProducts($keyword) {
        $sql = "SELECT p.*, 
                       c.nom as categorie_nom,
                       f.nom as fournisseur_nom
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
                WHERE p.nom LIKE ? OR p.description LIKE ?
                ORDER BY p.nom ASC";

        $searchTerm = "%$keyword%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    /**
     * Filtrer les produits par catégorie
     */
    public function getProductsByCategory($categorie_id) {
        $sql = "SELECT p.*, 
                       c.nom as categorie_nom,
                       f.nom as fournisseur_nom
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
                WHERE p.categorie_id = ?
                ORDER BY p.nom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categorie_id]);
        return $stmt->fetchAll();
    }

    /**
     * Filtrer les produits par fournisseur
     */
    public function getProductsBySupplier($fournisseur_id) {
        $sql = "SELECT p.*, 
                       c.nom as categorie_nom,
                       f.nom as fournisseur_nom
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
                WHERE p.fournisseur_id = ?
                ORDER BY p.nom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fournisseur_id]);
        return $stmt->fetchAll();
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
        return isset($result['total']) ? $result['total'] : 0;
    }

    /**
     * Récupérer les produits pour un fournisseur spécifique
     */
    public function getProductsByFournisseur($fournisseur_id) {
        $sql = "SELECT p.*, c.nom as categorie_nom 
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                WHERE p.fournisseur_id = ?
                ORDER BY p.nom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fournisseur_id]);
        return $stmt->fetchAll();
    }
}
?>