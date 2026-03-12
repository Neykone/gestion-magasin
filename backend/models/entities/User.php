<?php
// models/entities/User.php

class User {
    private $id;
    private $nom;
    private $email;
    private $password;
    private $role;
    private $statut;
    private $dateCreation;
    private $fournisseurId;

    // Constantes pour les rôles
    const ROLE_ADMIN = 'admin';
    const ROLE_VENDEUR = 'vendeur';
    const ROLE_FOURNISSEUR = 'fournisseur';

    // Constantes pour les statuts
    const STATUT_ACTIF = 'actif';
    const STATUT_INACTIF = 'inactif';

    public function __construct($data = []) {
        $this->hydrate($data);
    }

    /**
     * Hydrate l'objet avec un tableau de données
     */
    public function hydrate($data) {
        foreach ($data as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Valide que l'utilisateur est un admin
     */
    public function isAdmin() {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Valide que l'utilisateur est un vendeur
     */
    public function isVendeur() {
        return $this->role === self::ROLE_VENDEUR;
    }

    /**
     * Valide que l'utilisateur est un fournisseur
     */
    public function isFournisseur() {
        return $this->role === self::ROLE_FOURNISSEUR;
    }

    /**
     * Valide que l'utilisateur est actif
     */
    public function isActif() {
        return $this->statut === self::STATUT_ACTIF;
    }

    /**
     * Vérifie le mot de passe (utilise password_verify)
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }

    /**
     * Hash un nouveau mot de passe
     */
    public function setHashedPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }
    public function getStatut() { return $this->statut; }
    public function getDateCreation() { return $this->dateCreation; }
    public function getFournisseurId() { return $this->fournisseurId; }

    // Setters
    public function setId($id) { $this->id = $id; return $this; }
    public function setNom($nom) { $this->nom = $nom; return $this; }
    public function setEmail($email) { $this->email = $email; return $this; }
    public function setPassword($password) { $this->password = $password; return $this; }
    public function setRole($role) { $this->role = $role; return $this; }
    public function setStatut($statut) { $this->statut = $statut; return $this; }
    public function setDateCreation($dateCreation) { $this->dateCreation = $dateCreation; return $this; }
    public function setFournisseurId($fournisseurId) { $this->fournisseurId = $fournisseurId; return $this; }
}
?>