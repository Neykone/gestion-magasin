<?php
// models/entities/Categorie.php

class Categorie {
    private $id;
    private $nom;
    private $description;
    private $statut;
    private $nbProduits; // Pour les statistiques

    public function __construct($data = []) {
        $this->hydrate($data);
    }

    public function hydrate($data) {
        foreach ($data as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function isActif() {
        return $this->statut === 'actif';
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getStatut() { return $this->statut; }
    public function getNbProduits() { return $this->nbProduits; }

    // Setters
    public function setId($id) { $this->id = $id; return $this; }
    public function setNom($nom) { $this->nom = $nom; return $this; }
    public function setDescription($description) { $this->description = $description; return $this; }
    public function setStatut($statut) { $this->statut = $statut; return $this; }
    public function setNbProduits($nbProduits) { $this->nbProduits = $nbProduits; return $this; }
}
?>