<?php
// models/FournisseurModel.php
require_once 'config/Database.php';
require_once 'models/entities/Fournisseur.php';

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
        $data = $stmt->fetchAll();

        $fournisseurs = [];
        foreach ($data as $row) {
            $fournisseurs[] = new Fournisseur($row);
        }
        return $fournisseurs;
    }

    /**
     * Récupérer un fournisseur par son ID
     */
    public function getFournisseurById($id) {
        $stmt = $this->db->prepare("SELECT * FROM fournisseurs WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Fournisseur($data);
        }
        return null;
    }

    /**
     * Ajouter un fournisseur
     */
    public function addFournisseur(Fournisseur $fournisseur) {
        $stmt = $this->db->prepare("
            INSERT INTO fournisseurs (nom, contact, email, telephone, adresse) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $fournisseur->getNom(),
            $fournisseur->getContact(),
            $fournisseur->getEmail(),
            $fournisseur->getTelephone(),
            $fournisseur->getAdresse()
        ]);
    }

    /**
     * Modifier un fournisseur
     */
    public function updateFournisseur(Fournisseur $fournisseur) {
        $stmt = $this->db->prepare("
            UPDATE fournisseurs 
            SET nom = ?, contact = ?, email = ?, telephone = ?, adresse = ?, statut = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $fournisseur->getNom(),
            $fournisseur->getContact(),
            $fournisseur->getEmail(),
            $fournisseur->getTelephone(),
            $fournisseur->getAdresse(),
            $fournisseur->getStatut(),
            $fournisseur->getId()
        ]);
    }

    /**
     * Supprimer un fournisseur
     */
    public function deleteFournisseur($id) {
        $checkStmt = $this->db->prepare("SELECT COUNT(*) as count FROM produits WHERE fournisseur_id = ?");
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM fournisseurs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Récupérer les fournisseurs actifs
     */
    public function getFournisseursActifs() {
        $stmt = $this->db->query("SELECT * FROM fournisseurs WHERE statut = 'actif' ORDER BY nom ASC");
        $data = $stmt->fetchAll();

        $fournisseurs = [];
        foreach ($data as $row) {
            $fournisseurs[] = new Fournisseur($row);
        }
        return $fournisseurs;
    }
}
?>