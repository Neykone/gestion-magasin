<?php
// caisse.php - Interface de caisse pour les vendeurs
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'vendeur') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Données simulées des produits pour la caisse
$produits = [
    [
        'id' => 1,
        'nom' => 'iPhone 13',
        'prix' => 999.99,
        'stock' => 15,
        'code_barre' => '123456789'
    ],
    [
        'id' => 2,
        'nom' => 'Samsung Galaxy S21',
        'prix' => 899.99,
        'stock' => 3,
        'code_barre' => '987654321'
    ],
    [
        'id' => 3,
        'nom' => 'Ordinateur Dell XPS',
        'prix' => 1499.99,
        'stock' => 5,
        'code_barre' => '456789123'
    ],
    [
        'id' => 4,
        'nom' => 'Écran 24" LG',
        'prix' => 229.99,
        'stock' => 8,
        'code_barre' => '789123456'
    ],
    [
        'id' => 5,
        'nom' => 'Clavier Mécanique',
        'prix' => 89.99,
        'stock' => 12,
        'code_barre' => '321654987'
    ],
    [
        'id' => 6,
        'nom' => 'Souris Sans Fil',
        'prix' => 49.99,
        'stock' => 2,
        'code_barre' => '654987321'
    ]
];

$message = '';
$messageType = '';

include 'caisse.html';
?>
