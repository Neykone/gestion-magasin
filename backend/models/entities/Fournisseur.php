<?php
// models/entities/Fournisseur.php

class Fournisseur {
    private $id;
    private $nom;
    private $contact;
    private $email;
    private $telephone;
    private $adresse;
    private $statut;
    private $produitsFournis = []; // Tableau d'objets Product
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

    public function addProduit(Product $produit) {
        $this->produitsFournis[] = $produit;
        $this->nbProduits = count($this->produitsFournis);
        return $this;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getContact() { return $this->contact; }
    public function getEmail() { return $this->email; }
    public function getTelephone() { return $this->telephone; }
    public function getAdresse() { return $this->adresse; }
    public function getStatut() { return $this->statut; }
    public function getProduitsFournis() { return $this->produitsFournis; }
    public function getNbProduits() { return $this->nbProduits ?: count($this->produitsFournis); }

    // Setters
    public function setId($id) { $this->id = $id; return $this; }
    public function setNom($nom) { $this->nom = $nom; return $this; }
    public function setContact($contact) { $this->contact = $contact; return $this; }
    public function setEmail($email) { $this->email = $email; return $this; }
    public function setTelephone($telephone) { $this->telephone = $telephone; return $this; }
    public function setAdresse($adresse) { $this->adresse = $adresse; return $this; }
    public function setStatut($statut) { $this->statut = $statut; return $this; }
    public function setNbProduits($nbProduits) { $this->nbProduits = $nbProduits; return $this; }
}
?>