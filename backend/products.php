<?php
// products.php - Gestion des produits
session_start();
require_once 'config/Database.php';
require_once 'models/UserModel.php';

// Vérifier si l'utilisateur est connecté ET est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Données simulées des produits
$products = [
    [
        'id' => 1,
        'nom' => 'iPhone 13',
        'description' => 'Smartphone Apple 128Go',
        'prix_achat' => 800.00,
        'prix_vente' => 999.99,
        'categorie' => 'Électronique',
        'fournisseur' => 'Apple Distribution',
        'stock' => 15,
        'seuil_alerte' => 5
    ],
    [
        'id' => 2,
        'nom' => 'Samsung Galaxy S21',
        'description' => 'Smartphone Samsung 128Go',
        'prix_achat' => 700.00,
        'prix_vente' => 899.99,
        'categorie' => 'Électronique',
        'fournisseur' => 'Samsung France',
        'stock' => 3,
        'seuil_alerte' => 5
    ],
    [
        'id' => 3,
        'nom' => 'Ordinateur Dell XPS 13',
        'description' => 'PC Portable 16Go RAM, 512Go SSD',
        'prix_achat' => 1100.00,
        'prix_vente' => 1499.99,
        'categorie' => 'Informatique',
        'fournisseur' => 'Dell Technologies',
        'stock' => 0,
        'seuil_alerte' => 3
    ],
    [
        'id' => 4,
        'nom' => 'Écran 24" LG',
        'description' => 'Écran Full HD IPS',
        'prix_achat' => 150.00,
        'prix_vente' => 229.99,
        'categorie' => 'Informatique',
        'fournisseur' => 'LG Electronics',
        'stock' => 8,
        'seuil_alerte' => 4
    ],
    [
        'id' => 5,
        'nom' => 'Clavier Mécanique',
        'description' => 'Clavier gaming RGB',
        'prix_achat' => 50.00,
        'prix_vente' => 89.99,
        'categorie' => 'Accessoires',
        'fournisseur' => 'Logitech',
        'stock' => 12,
        'seuil_alerte' => 5
    ],
    [
        'id' => 6,
        'nom' => 'Souris Sans Fil',
        'description' => 'Souris ergonomique',
        'prix_achat' => 25.00,
        'prix_vente' => 49.99,
        'categorie' => 'Accessoires',
        'fournisseur' => 'Logitech',
        'stock' => 2,
        'seuil_alerte' => 5
    ],
    [
        'id' => 7,
        'nom' => 'Casque Audio',
        'description' => 'Casque Bluetooth',
        'prix_achat' => 40.00,
        'prix_vente' => 79.99,
        'categorie' => 'Audio',
        'fournisseur' => 'Sony',
        'stock' => 6,
        'seuil_alerte' => 4
    ]
];

// Message pour les notifications
$message = '';
$messageType = '';

// Gestion des actions
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $message = "Produit supprimé (simulation)";
    $messageType = 'success';
}

if (isset($_GET['add_test'])) {
    $message = "Nouveau produit ajouté (simulation)";
    $messageType = 'success';
}

if (isset($_GET['edit_test'])) {
    $message = "Produit modifié (simulation)";
    $messageType = 'success';
}

// Inclure la vue
include '../frontend/products.html';
?>
