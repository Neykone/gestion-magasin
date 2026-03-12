<?php
// fournisseurs.php - Gestion des fournisseurs
session_start();
require_once 'config/Database.php';
require_once 'models/UserModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Données simulées des fournisseurs
$fournisseurs = [
    [
        'id' => 1,
        'nom' => 'Apple Distribution',
        'contact' => 'Jean Apple',
        'email' => 'contact@apple.fr',
        'telephone' => '01 23 45 67 89',
        'adresse' => '12 Avenue de l\'Innovation, 75001 Paris',
        'produits_fournis' => 5,
        'statut' => 'actif'
    ],
    [
        'id' => 2,
        'nom' => 'Samsung France',
        'contact' => 'Marie Samsung',
        'email' => 'commercial@samsung.fr',
        'telephone' => '01 98 76 54 32',
        'adresse' => '34 Rue de la Technologie, 69001 Lyon',
        'produits_fournis' => 4,
        'statut' => 'actif'
    ],
    [
        'id' => 3,
        'nom' => 'Dell Technologies',
        'contact' => 'Pierre Dell',
        'email' => 'ventes@dell.fr',
        'telephone' => '04 56 78 90 12',
        'adresse' => '56 Boulevard Informatique, 33000 Bordeaux',
        'produits_fournis' => 3,
        'statut' => 'actif'
    ],
    [
        'id' => 4,
        'nom' => 'LG Electronics',
        'contact' => 'Sophie LG',
        'email' => 'contact@lg.fr',
        'telephone' => '05 67 89 01 23',
        'adresse' => '78 Rue des Écrans, 31000 Toulouse',
        'produits_fournis' => 2,
        'statut' => 'inactif'
    ],
    [
        'id' => 5,
        'nom' => 'Logitech',
        'contact' => 'Thomas Logitech',
        'email' => 'support@logitech.fr',
        'telephone' => '03 45 67 89 01',
        'adresse' => '90 Avenue des Accessoires, 59000 Lille',
        'produits_fournis' => 3,
        'statut' => 'actif'
    ],
    [
        'id' => 6,
        'nom' => 'Sony France',
        'contact' => 'Emma Sony',
        'email' => 'contact@sony.fr',
        'telephone' => '02 34 56 78 90',
        'adresse' => '23 Rue du Son, 44000 Nantes',
        'produits_fournis' => 1,
        'statut' => 'actif'
    ]
];

$message = '';
$messageType = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $message = "Fournisseur supprimé (simulation)";
    $messageType = 'success';
}

include '../frontend/fournisseurs.html';
?>
