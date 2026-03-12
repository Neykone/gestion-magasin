<?php
// backend/categories.php
session_start();
require_once 'models/CategorieModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];
$categorieModel = new CategorieModel();

$message = '';
$messageType = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

    if ($categorieModel->deleteCategorie($deleteId)) {
        $message = "Catégorie supprimée avec succès";
        $messageType = 'success';
    } else {
        $message = "Impossible de supprimer cette catégorie (utilisée par des produits)";
        $messageType = 'error';
    }
}

// Récupérer toutes les catégories avec le nombre de produits
$categories = $categorieModel->getAllCategoriesWithCount();

// Statistiques
$totalCategories = count($categories);
$categoriesActives = 0;
$totalProduitsCategories = 0;

foreach ($categories as $categorie) {
    if ($categorie->isActif()) {
        $categoriesActives++;
    }
    $totalProduitsCategories += $categorie->getNbProduits();
}

include '../frontend/categories.html';
?>