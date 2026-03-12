<?php
// backend/fournisseur_dashboard.php
session_start();
require_once 'config/Database.php';
require_once 'models/UserModel.php';
require_once 'models/ProductModel.php';
require_once 'models/FournisseurModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fournisseur') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];
$userEmail = $_SESSION['user']['email'];
$userId = $_SESSION['user']['id'];

// Initialiser les modèles
$productModel = new ProductModel();
$fournisseurModel = new FournisseurModel();

// Trouver l'ID du fournisseur à partir de l'email
$fournisseurId = null;
$fournisseurs = $fournisseurModel->getAllFournisseurs(); // Retourne des tableaux pour l'instant

foreach ($fournisseurs as $f) {
    if ($f['email'] === $userEmail) {
        $fournisseurId = $f['id'];
        break;
    }
}

// Récupérer les produits de ce fournisseur (ce sont des objets Product maintenant !)
if ($fournisseurId) {
    $produitsFournis = $productModel->getProductsBySupplier($fournisseurId);
} else {
    $produitsFournis = [];
}

// Statistiques
$totalProduits = count($produitsFournis);
$stockTotal = 0;
$produitsAlerte = 0;

foreach ($produitsFournis as $produit) {
    // Maintenant $produit est un objet Product
    $stockTotal += $produit->getStock();
    if ($produit->isLowStock() || $produit->isOutOfStock()) {
        $produitsAlerte++;
    }
}

include '../frontend/fournisseur_dashboard.html';
?>