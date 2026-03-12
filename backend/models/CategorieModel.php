<?php
// models/CategorieModel.php
require_once 'config/Database.php';

class CategorieModel {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Récupérer toutes les catégories
     */
    public function getAllCategories() {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY nom ASC");
        return $stmt->fetchAll();
    }

    /**
     * Récupérer une catégorie par son ID
     */
    public function getCategorieById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Ajouter une catégorie
     */
    public function addCategorie($nom, $description) {
        $stmt = $this->db->prepare("INSERT INTO categories (nom, description) VALUES (?, ?)");
        return $stmt->execute([$nom, $description]);
    }

    /**
     * Modifier une catégorie
     */
    public function updateCategorie($id, $nom, $description, $statut) {
        $stmt = $this->db->prepare("UPDATE categories SET nom = ?, description = ?, statut = ? WHERE id = ?");
        return $stmt->execute([$nom, $description, $statut, $id]);
    }

    /**
     * Supprimer une catégorie
     */
    public function deleteCategorie($id) {
        // Vérifier si des produits utilisent cette catégorie
        $checkStmt = $this->db->prepare("SELECT COUNT(*) as count FROM produits WHERE categorie_id = ?");
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false; // Catégorie utilisée
        }

        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Compter le nombre de catégories
     */
    public function countCategories() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM categories");
        $result = $stmt->fetch();
        return $result['total'];
    }
}
?>