<?php
// fournisseur_dashboard.php - Dashboard pour les fournisseurs
session_start();
require_once 'config/Database.php';
require_once 'models/UserModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fournisseur') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];
$userEmail = $_SESSION['user']['email'];

// Connexion à la BDD (à implémenter plus tard)
// Pour l'instant, données simulées
$produitsFournis = [
    [
        'id' => 1,
        'nom' => 'iPhone 13',
        'description' => 'Smartphone Apple 128Go',
        'prix_vente' => 999.99,
        'stock' => 15,
        'seuil_alerte' => 5,
        'categorie' => 'Électronique'
    ],
    [
        'id' => 4,
        'nom' => 'Écran 24" LG',
        'description' => 'Écran Full HD IPS',
        'prix_vente' => 229.99,
        'stock' => 8,
        'seuil_alerte' => 4,
        'categorie' => 'Informatique'
    ],
    [
        'id' => 7,
        'nom' => 'Casque Audio',
        'description' => 'Casque Bluetooth',
        'prix_vente' => 79.99,
        'stock' => 6,
        'seuil_alerte' => 4,
        'categorie' => 'Audio'
    ]
];

// Statistiques
$totalProduits = count($produitsFournis);
$stockTotal = array_sum(array_column($produitsFournis, 'stock'));
$produitsAlerte = count(array_filter($produitsFournis, function ($p) {
    return $p['stock'] <= $p['seuil_alerte'];
}));

include '../frontend/fournisseur_dashboard.html';
?>
