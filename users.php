<?php
// users.php - Gestion des utilisateurs
session_start();

// Vérifier si l'utilisateur est connecté ET est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Données simulées des utilisateurs (en attendant la BDD)
$users = [
    [
        'id' => 1,
        'nom' => 'Admin Principal',
        'email' => 'admin@magasin.com',
        'role' => 'admin',
        'statut' => 'actif'
    ],
    [
        'id' => 2,
        'nom' => 'Jean Dupont',
        'email' => 'jean@magasin.com',
        'role' => 'vendeur',
        'statut' => 'actif'
    ],
    [
        'id' => 3,
        'nom' => 'Marie Martin',
        'email' => 'marie@magasin.com',
        'role' => 'vendeur',
        'statut' => 'actif'
    ],
    [
        'id' => 4,
        'nom' => 'Pierre Durand',
        'email' => 'pierre@fournisseur.com',
        'role' => 'fournisseur',
        'statut' => 'inactif'
    ],
    [
        'id' => 5,
        'nom' => 'Sophie Lefebvre',
        'email' => 'sophie@magasin.com',
        'role' => 'vendeur',
        'statut' => 'actif'
    ],
    [
        'id' => 6,
        'nom' => 'Lucas Martin',
        'email' => 'lucas@magasin.com',
        'role' => 'vendeur',
        'statut' => 'actif'
    ],
    [
        'id' => 7,
        'nom' => 'Claire Bernard',
        'email' => 'claire@fournisseur.com',
        'role' => 'fournisseur',
        'statut' => 'actif'
    ]
];

// Message pour les notifications (ajout/modif/suppr)
$message = '';
$messageType = '';

// Simuler la suppression
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];

    // Chercher l'utilisateur à supprimer
    $userToDelete = null;
    foreach ($users as $user) {
        if ($user['id'] == $deleteId) {
            $userToDelete = $user;
            break;
        }
    }

    if ($userToDelete) {
        // Ne pas permettre la suppression de son propre compte
        if ($userToDelete['id'] == $_SESSION['user']['id']) {
            $message = "Vous ne pouvez pas supprimer votre propre compte !";
            $messageType = 'error';
        } else {
            $message = "L'utilisateur " . $userToDelete['nom'] . " a été supprimé (simulation)";
            $messageType = 'success';
            // Dans la vraie vie : requête DELETE
        }
    }
}

// Simuler l'ajout (pour tester)
if (isset($_GET['add_test'])) {
    $message = "Nouvel utilisateur ajouté (simulation)";
    $messageType = 'success';
}

// Simuler la modification (pour tester)
if (isset($_GET['edit_test'])) {
    $message = "Utilisateur modifié (simulation)";
    $messageType = 'success';
}

// Inclure la vue
include 'users.html';
?>
