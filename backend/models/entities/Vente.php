<?php
// models/entities/Vente.php

class Vente {
    private $id;
    private $userId;
    private $clientNom;
    private $total;
    private $statut;
    private $paiement;
    private $dateVente;

    // Propriétés additionnelles
    private $vendeurNom;
    private $produits = []; // Tableau des produits vendus

    const STATUT_PAYE = 'payé';
    const STATUT_ATTENTE = 'en attente';
    const STATUT_ANNULE = 'annulé';

    const PAIEMENT_CARTE = 'carte';
    const PAIEMENT_ESPECES = 'espèces';
    const PAIEMENT_VIREMENT = 'virement';

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

    public function isPaye() {
        return $this->statut === self::STATUT_PAYE;
    }

    public function isAnnule() {
        return $this->statut === self::STATUT_ANNULE;
    }

    public function isEnAttente() {
        return $this->statut === self::STATUT_ATTENTE;
    }

    public function getIconePaiement() {
        switch ($this->paiement) {
            case self::PAIEMENT_CARTE:
                return 'fa-credit-card';
            case self::PAIEMENT_ESPECES:
                return 'fa-money-bill';
            case self::PAIEMENT_VIREMENT:
                return 'fa-university';
            default:
                return 'fa-credit-card';
        }
    }

    /**
     * Ajoute un produit à la vente
     */
    public function addProduit($produit) {
        $this->produits[] = $produit;
        return $this;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getClientNom() { return $this->clientNom; }
    public function getTotal() { return $this->total; }
    public function getStatut() { return $this->statut; }
    public function getPaiement() { return $this->paiement; }
    public function getDateVente() { return $this->dateVente; }
    public function getVendeurNom() { return $this->vendeurNom; }
    public function getProduits() { return $this->produits; }

    // Setters
    public function setId($id) { $this->id = $id; return $this; }
    public function setUserId($userId) { $this->userId = $userId; return $this; }
    public function setClientNom($clientNom) { $this->clientNom = $clientNom; return $this; }
    public function setTotal($total) { $this->total = $total; return $this; }
    public function setStatut($statut) { $this->statut = $statut; return $this; }
    public function setPaiement($paiement) { $this->paiement = $paiement; return $this; }
    public function setDateVente($dateVente) { $this->dateVente = $dateVente; return $this; }
    public function setVendeurNom($vendeurNom) { $this->vendeurNom = $vendeurNom; return $this; }
    public function setProduits($produits) { $this->produits = $produits; return $this; }
}
?>