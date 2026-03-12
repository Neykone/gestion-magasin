<?php
// models/UserModel.php
require_once 'config/Database.php';
require_once 'models/entities/User.php';

class UserModel {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Récupérer un utilisateur par son email (retourne un objet User)
     */
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        if ($data) {
            return new User($data);
        }
        return null;
    }

    /**
     * Récupérer un utilisateur par son ID (retourne un objet User)
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if ($data) {
            return new User($data);
        }
        return null;
    }

    /**
     * Récupérer tous les utilisateurs (retourne un tableau d'objets User)
     */
    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        $data = $stmt->fetchAll();

        $users = [];
        foreach ($data as $row) {
            $users[] = new User($row);
        }
        return $users;
    }

    /**
     * Vérifier le mot de passe (utilise la méthode de l'entité)
     */
    public function verifyPassword($email, $password) {
        $user = $this->getUserByEmail($email);

        if ($user && $user->verifyPassword($password)) {
            return $user;
        }
        return false;
    }

    /**
     * Ajouter un nouvel utilisateur (utilise un objet User)
     */
    /**
     * Ajouter un nouvel utilisateur (utilise un objet User)
     */
    public function addUser(User $user) {
        $sql = "INSERT INTO users (nom, email, password, role, statut";

        $params = [
            $user->getNom(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getRole(),
            $user->getStatut()
        ];

        // Ajouter fournisseur_id si le rôle est fournisseur
        if ($user->getRole() === 'fournisseur') {
            $sql .= ", fournisseur_id";
            $params[] = $user->getFournisseurId();
        }

        $sql .= ") VALUES (" . implode(',', array_fill(0, count($params), '?')) . ")";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Modifier un utilisateur (utilise un objet User)
     */
    public function updateUser(User $user) {
        $sql = "UPDATE users 
            SET nom = ?, email = ?, role = ?, statut = ?";

        $params = [
            $user->getNom(),
            $user->getEmail(),
            $user->getRole(),
            $user->getStatut()
        ];

        // Ajouter fournisseur_id si le rôle est fournisseur
        if ($user->getRole() === 'fournisseur') {
            $sql .= ", fournisseur_id = ?";
            $params[] = $user->getFournisseurId();
        }

        $sql .= " WHERE id = ?";
        $params[] = $user->getId();

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Changer le mot de passe (utilise la méthode de l'entité)
     */
    public function changePassword($id, $newPassword) {
        $user = $this->getUserById($id);
        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
            return $stmt->execute([$hashedPassword, $id]);
        }
        return false;
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
        $data = $stmt->fetchAll();

        $users = [];
        foreach ($data as $row) {
            $users[] = new User($row);
        }
        return $users;
    }

    public function getUserByFournisseurId($fournisseurId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE fournisseur_id = ?");
        $stmt->execute([$fournisseurId]);
        $data = $stmt->fetch();

        if ($data) {
            return new User($data);
        }
        return null;
    }
}
?>