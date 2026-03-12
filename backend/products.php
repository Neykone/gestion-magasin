<?php
// backend/products.php
session_start();
require_once 'config/Database.php';
require_once 'models/ProductModel.php';
require_once 'models/CategorieModel.php';  // À créer
require_once 'models/FournisseurModel.php'; // À créer

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Initialiser les modèles
$productModel = new ProductModel();
$categorieModel = new CategorieModel();   // À créer
$fournisseurModel = new FournisseurModel(); // À créer

// Gestion des actions
$message = '';
$messageType = '';

// Suppression d'un produit
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

    if ($productModel->deleteProduct($deleteId)) {
        $message = "Produit supprimé avec succès";
        $messageType = 'success';
    } else {
        $message = "Impossible de supprimer ce produit (peut-être lié à des ventes)";
        $messageType = 'error';
    }
}

// Récupérer tous les produits
$products = $productModel->getAllProducts();

// Récupérer les catégories et fournisseurs pour les filtres (optionnel)
$categories = $categorieModel->getAllCategories();
$fournisseurs = $fournisseurModel->getAllFournisseurs();

// Statistiques
$totalProduits = count($products);
$stockTotal = 0;
$valeurStock = 0;
$produitsAlerte = 0;

foreach ($products as $product) {
    $stockTotal += $product['stock'];
    $valeurStock += $product['prix_achat'] * $product['stock'];
    if ($product['stock'] <= $product['seuil_alerte']) {
        $produitsAlerte++;
    }
}
$pageTitle = "Gestion des Produits";

// Inclure la vue
include '../frontend/products.html';
?>