<?php
// models/entities/Product.php

class Product {
    private $id;
    private $nom;
    private $description;
    private $prixAchat;
    private $prixVente;
    private $stock;
    private $seuilAlerte;
    private $categorieId;
    private $fournisseurId;
    private $statut;
    private $dateCreation;

    // Propriétés additionnelles (issues des jointures)
    private $categorieNom;
    private $fournisseurNom;

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

    /**
     * Vérifie si le produit est en stock faible
     */
    public function isLowStock() {
        return $this->stock <= $this->seuilAlerte && $this->stock > 0;
    }

    /**
     * Vérifie si le produit est en rupture
     */
    public function isOutOfStock() {
        return $this->stock == 0;
    }

    /**
     * Calcule la marge brute
     */
    public function getMarge() {
        return $this->prixVente - $this->prixAchat;
    }

    /**
     * Calcule le pourcentage de marge
     */
    public function getMargePourcentage() {
        return $this->prixAchat > 0 ? ($this->getMarge() / $this->prixAchat) * 100 : 0;
    }

    /**
     * Vérifie si le produit est disponible à la vente
     */
    public function isAvailable() {
        return $this->stock > 0 && $this->statut === 'actif';
    }

    /**
     * Réduit le stock d'une certaine quantité
     */
    public function decrementStock($quantite) {
        if ($this->stock >= $quantite) {
            $this->stock -= $quantite;
            return true;
        }
        return false;
    }

    /**
     * Augmente le stock d'une certaine quantité
     */
    public function incrementStock($quantite) {
        $this->stock += $quantite;
        return $this;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getPrixAchat() { return $this->prixAchat; }
    public function getPrixVente() { return $this->prixVente; }
    public function getStock() { return $this->stock; }
    public function getSeuilAlerte() { return $this->seuilAlerte; }
    public function getCategorieId() { return $this->categorieId; }
    public function getFournisseurId() { return $this->fournisseurId; }
    public function getStatut() { return $this->statut; }
    public function getDateCreation() { return $this->dateCreation; }
    public function getCategorieNom() { return $this->categorieNom; }
    public function getFournisseurNom() { return $this->fournisseurNom; }

    // Setters
    public function setId($id) { $this->id = $id; return $this; }
    public function setNom($nom) { $this->nom = $nom; return $this; }
    public function setDescription($description) { $this->description = $description; return $this; }
    public function setPrixAchat($prixAchat) { $this->prixAchat = $prixAchat; return $this; }
    public function setPrixVente($prixVente) { $this->prixVente = $prixVente; return $this; }
    public function setStock($stock) { $this->stock = $stock; return $this; }
    public function setSeuilAlerte($seuilAlerte) { $this->seuilAlerte = $seuilAlerte; return $this; }
    public function setCategorieId($categorieId) { $this->categorieId = $categorieId; return $this; }
    public function setFournisseurId($fournisseurId) { $this->fournisseurId = $fournisseurId; return $this; }
    public function setStatut($statut) { $this->statut = $statut; return $this; }
    public function setDateCreation($dateCreation) { $this->dateCreation = $dateCreation; return $this; }
    public function setCategorieNom($categorieNom) { $this->categorieNom = $categorieNom; return $this; }
    public function setFournisseurNom($fournisseurNom) { $this->fournisseurNom = $fournisseurNom; return $this; }
}
?>