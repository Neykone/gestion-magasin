<?php
// models/CategorieModel.php
require_once 'config/Database.php';
require_once 'models/entities/Categorie.php';

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
        $data = $stmt->fetchAll();

        $categories = [];
        foreach ($data as $row) {
            $categories[] = new Categorie($row);
        }
        return $categories;
    }

    /**
     * Récupérer une catégorie par son ID
     */
    public function getCategorieById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Categorie($data);
        }
        return null;
    }

    /**
     * Récupérer toutes les catégories avec le nombre de produits
     */
    public function getAllCategoriesWithCount() {
        $sql = "SELECT c.*, COUNT(p.id) as nb_produits 
                FROM categories c
                LEFT JOIN produits p ON c.id = p.categorie_id
                GROUP BY c.id
                ORDER BY c.nom ASC";

        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll();

        $categories = [];
        foreach ($data as $row) {
            $categories[] = new Categorie($row);
        }
        return $categories;
    }

    /**
     * Ajouter une catégorie
     */
    public function addCategorie(Categorie $categorie) {
        $stmt = $this->db->prepare("INSERT INTO categories (nom, description) VALUES (?, ?)");
        return $stmt->execute([
            $categorie->getNom(),
            $categorie->getDescription()
        ]);
    }

    /**
     * Modifier une catégorie
     */
    public function updateCategorie(Categorie $categorie) {
        $stmt = $this->db->prepare("UPDATE categories SET nom = ?, description = ?, statut = ? WHERE id = ?");
        return $stmt->execute([
            $categorie->getNom(),
            $categorie->getDescription(),
            $categorie->getStatut(),
            $categorie->getId()
        ]);
    }

    /**
     * Supprimer une catégorie
     */
    public function deleteCategorie($id) {
        $checkStmt = $this->db->prepare("SELECT COUNT(*) as count FROM produits WHERE categorie_id = ?");
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>