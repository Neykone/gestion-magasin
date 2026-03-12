<?php
// users.php - Version avec base de données
session_start();
require_once 'models/UserModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];
$userModel = new UserModel();

// Gestion des actions
$message = '';
$messageType = '';

// Suppression d'un utilisateur
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

    // Empêcher la suppression de son propre compte
    if ($deleteId === $_SESSION['user']['id']) {
        $message = "Vous ne pouvez pas supprimer votre propre compte !";
        $messageType = 'error';
    } else {
        if ($userModel->deleteUser($deleteId)) {
            $message = "Utilisateur supprimé avec succès";
            $messageType = 'success';
        } else {
            $message = "Erreur lors de la suppression";
            $messageType = 'error';
        }
    }
}

// Ajout d'un utilisateur (simulation pour l'instant)
if (isset($_POST['add_user'])) {
    // À implémenter plus tard avec un formulaire
    $message = "Fonctionnalité d'ajout à venir";
    $messageType = 'info';
}

// Récupérer tous les utilisateurs depuis la BDD
$users = $userModel->getAllUsers();

// Statistiques
$totalUsers = count($users);
$admins = count(array_filter($users, function ($u) {
    return $u['role'] === 'admin';
}));
$vendeurs = count(array_filter($users, function ($u) {
    return $u['role'] === 'vendeur';
}));
$fournisseurs = count(array_filter($users, function ($u) {
    return $u['role'] === 'fournisseur';
}));

include '../frontend/users.html';
?>