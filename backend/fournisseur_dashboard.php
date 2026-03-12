<?php
// backend/fournisseur_dashboard.php - Dashboard pour les fournisseurs
session_start();
require_once 'config/Database.php';
require_once 'models/UserModel.php';
require_once 'models/ProductModel.php';  // Ajout du modèle produit

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fournisseur') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];
$userEmail = $_SESSION['user']['email'];
$userId = $_SESSION['user']['id'];

// Initialiser le modèle produit
$productModel = new ProductModel();

// Récupérer l'ID du fournisseur à partir de l'email ou du nom
// Supposons que dans la table fournisseurs, l'email correspond à l'email de l'utilisateur
$fournisseurId = null;

// Méthode 1 : Si tu as une relation entre users et fournisseurs
// Il faudrait une colonne fournisseur_id dans users, mais on ne l'a pas

// Méthode 2 : Chercher le fournisseur par email
require_once 'models/FournisseurModel.php';
$fournisseurModel = new FournisseurModel();
$fournisseurs = $fournisseurModel->getAllFournisseurs();

foreach ($fournisseurs as $f) {
    if ($f['email'] === $userEmail) {
        $fournisseurId = $f['id'];
        break;
    }
}

// Si on ne trouve pas par email, chercher par nom (approximation)
if (!$fournisseurId) {
    foreach ($fournisseurs as $f) {
        if (stripos($f['nom'], explode(' ', $userName)[0]) !== false) {
            $fournisseurId = $f['id'];
            break;
        }
    }
}

// Récupérer les produits de ce fournisseur
if ($fournisseurId) {
    $produitsFournis = $productModel->getProductsBySupplier($fournisseurId);
} else {
    // Fallback: tous les produits (à éviter)
    $produitsFournis = $productModel->getAllProducts();
}

// Statistiques
$totalProduits = count($produitsFournis);
$stockTotal = 0;
$produitsAlerte = 0;

foreach ($produitsFournis as $produit) {
    $stockTotal += $produit['stock'];
    if ($produit['stock'] <= $produit['seuil_alerte']) {
        $produitsAlerte++;
    }
}

include '../frontend/fournisseur_dashboard.html';
?>