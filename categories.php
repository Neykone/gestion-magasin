<?php
// categories.php - Gestion des catégories
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Données simulées des catégories
$categories = [
    [
        'id' => 1,
        'nom' => 'Électronique',
        'description' => 'Tous les appareils électroniques',
        'nb_produits' => 12,
        'statut' => 'actif'
    ],
    [
        'id' => 2,
        'nom' => 'Informatique',
        'description' => 'Ordinateurs, écrans, accessoires PC',
        'nb_produits' => 8,
        'statut' => 'actif'
    ],
    [
        'id' => 3,
        'nom' => 'Accessoires',
        'description' => 'Câbles, adaptateurs, supports',
        'nb_produits' => 15,
        'statut' => 'actif'
    ],
    [
        'id' => 4,
        'nom' => 'Audio',
        'description' => 'Casques, écouteurs, enceintes',
        'nb_produits' => 6,
        'statut' => 'actif'
    ],
    [
        'id' => 5,
        'nom' => 'Téléphonie',
        'description' => 'Smartphones, accessoires téléphone',
        'nb_produits' => 9,
        'statut' => 'inactif'
    ]
];

$message = '';
$messageType = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $message = "Catégorie supprimée (simulation)";
    $messageType = 'success';
}

include 'categories.html';
?>
