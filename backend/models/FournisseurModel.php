<?php
// models/FournisseurModel.php
require_once 'config/Database.php';

class FournisseurModel {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Récupérer tous les fournisseurs
     */
    public function getAllFournisseurs() {
        $stmt = $this->db->query("SELECT * FROM fournisseurs ORDER BY nom ASC");
        return $stmt->fetchAll();
    }

    /**
     * Récupérer un fournisseur par son ID
     */
    public function getFournisseurById($id) {
        $stmt = $this->db->prepare("SELECT * FROM fournisseurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Ajouter un fournisseur
     */
    public function addFournisseur($nom, $contact, $email, $telephone, $adresse) {
        $stmt = $this->db->prepare("INSERT INTO fournisseurs (nom, contact, email, telephone, adresse) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$nom, $contact, $email, $telephone, $adresse]);
    }

    /**
     * Modifier un fournisseur
     */
    public function updateFournisseur($id, $nom, $contact, $email, $telephone, $adresse, $statut) {
        $stmt = $this->db->prepare("UPDATE fournisseurs SET nom = ?, contact = ?, email = ?, telephone = ?, adresse = ?, statut = ? WHERE id = ?");
        return $stmt->execute([$nom, $contact, $email, $telephone, $adresse, $statut, $id]);
    }

    /**
     * Supprimer un fournisseur
     */
    public function deleteFournisseur($id) {
        // Vérifier si des produits utilisent ce fournisseur
        $checkStmt = $this->db->prepare("SELECT COUNT(*) as count FROM produits WHERE fournisseur_id = ?");
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false; // Fournisseur utilisé
        }

        $stmt = $this->db->prepare("DELETE FROM fournisseurs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Compter le nombre de fournisseurs
     */
    public function countFournisseurs() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM fournisseurs");
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Récupérer les fournisseurs actifs
     */
    public function getFournisseursActifs() {
        $stmt = $this->db->query("SELECT * FROM fournisseurs WHERE statut = 'actif' ORDER BY nom ASC");
        return $stmt->fetchAll();
    }
}
?>