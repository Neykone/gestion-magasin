<?php
// backend/fournisseur_dashboard.php
session_start();
require_once 'config/Database.php';
require_once 'models/ProductModel.php';
require_once 'models/UserModel.php';
require_once 'models/FournisseurModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fournisseur') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];
$userId = $_SESSION['user']['id'];

// Initialiser les modèles
$userModel = new UserModel();
$productModel = new ProductModel();
$fournisseurModel = new FournisseurModel();

// Récupérer l'utilisateur avec son fournisseur_id
$user = $userModel->getUserById($userId);
$fournisseurId = $user->getFournisseurId();

$message = '';
$messageType = '';

// Récupérer les produits de ce fournisseur
if ($fournisseurId) {
    $produitsFournis = $productModel->getProductsBySupplier($fournisseurId);

    // Récupérer les informations du fournisseur
    $fournisseur = $fournisseurModel->getFournisseurById($fournisseurId);
    $fournisseurNom = $fournisseur ? $fournisseur->getNom() : 'Inconnu';
} else {
    $produitsFournis = [];
    $fournisseurNom = 'Non associé';
    $message = "Aucun fournisseur n'est associé à votre compte. Veuillez contacter l'administrateur.";
    $messageType = 'warning';
}

// Statistiques
$totalProduits = count($produitsFournis);
$stockTotal = 0;
$produitsAlerte = 0;

foreach ($produitsFournis as $produit) {
    $stockTotal += $produit->getStock();
    if ($produit->isLowStock() || $produit->isOutOfStock()) {
        $produitsAlerte++;
    }
}

// Données pour le graphique
$produitsData = [];
foreach ($produitsFournis as $produit) {
    $produitsData[] = [
        'nom' => $produit->getNom(),
        'stock' => $produit->getStock(),
        'seuil' => $produit->getSeuilAlerte(),
        'categorie' => $produit->getCategorieNom() ?? 'Non catégorisé',
        'prix' => $produit->getPrixVente()
    ];
}

include '../frontend/fournisseur_dashboard.html';
?>