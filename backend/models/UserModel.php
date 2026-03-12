<?php
// models/UserModel.php
require_once 'config/Database.php';

class UserModel {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Récupérer un utilisateur par son email
     */
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Récupérer un utilisateur par son ID
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Récupérer tous les utilisateurs
     */
    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    /**
     * Vérifier le mot de passe (pour la connexion)
     */
    public function verifyPassword($email, $password) {
        $user = $this->getUserByEmail($email);

        if ($user) {
            // Pour l'instant, on compare en clair (à changer plus tard avec password_verify)
            if ($password === $user['password']) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Ajouter un nouvel utilisateur
     */
    public function addUser($nom, $email, $password, $role) {
        $stmt = $this->db->prepare("
            INSERT INTO users (nom, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$nom, $email, $password, $role]);
    }

    /**
     * Modifier un utilisateur
     */
    public function updateUser($id, $nom, $email, $role, $statut) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET nom = ?, email = ?, role = ?, statut = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$nom, $email, $role, $statut, $id]);
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Changer le mot de passe (avec hash)
     */
    public function changePassword($id, $newPassword) {
        // Plus tard on ajoutera password_hash()
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$newPassword, $id]);
    }

    /**
     * Compter le nombre d'utilisateurs
     */
    public function countUsers() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Récupérer les utilisateurs par rôle
     */
    public function getUsersByRole($role) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }
}
?>