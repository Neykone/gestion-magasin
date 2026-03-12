<?php
// backend/products.php
session_start();
require_once 'config/Database.php';
require_once 'models/ProductModel.php';
require_once 'models/CategorieModel.php';
require_once 'models/FournisseurModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Initialiser les modèles
$productModel = new ProductModel();
$categorieModel = new CategorieModel();
$fournisseurModel = new FournisseurModel();

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
        $message = "Impossible de supprimer ce produit (lié à des ventes)";
        $messageType = 'error';
    }
}

// Récupérer tous les produits (objets Product)
$products = $productModel->getAllProducts();

// Récupérer les catégories et fournisseurs pour les filtres
$categories = $categorieModel->getAllCategories(); // À adapter plus tard
$fournisseurs = $fournisseurModel->getAllFournisseurs(); // À adapter plus tard

// Statistiques
$totalProduits = count($products);
$stockTotal = 0;
$valeurStock = 0;
$produitsAlerte = 0;

foreach ($products as $product) {
    $stockTotal += $product->getStock();
    $valeurStock += $product->getPrixAchat() * $product->getStock();
    if ($product->isLowStock()) {
        $produitsAlerte++;
    }
}

include '../frontend/products.html';
?>