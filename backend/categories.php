<?php
// backend/categories.php
session_start();
require_once 'config/Database.php';
require_once 'models/CategorieModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Initialiser le modèle
$categorieModel = new CategorieModel();

// Gestion des actions
$message = '';
$messageType = '';

// Suppression d'une catégorie
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

// Récupérer toutes les catégories
$categories = $categorieModel->getAllCategories();

// Statistiques
$totalCategories = count($categories);
$categoriesActives = count(array_filter($categories, fn($c) => ($c['statut'] ?? 'actif') === 'actif'));

include '../frontend/categories.html';
?>