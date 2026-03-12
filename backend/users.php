<?php
// backend/users.php
session_start();
require_once 'models/UserModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];
$userModel = new UserModel();

$message = '';
$messageType = '';

// Suppression d'un utilisateur
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

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

// Récupérer tous les utilisateurs (objets User)
$users = $userModel->getAllUsers();

// Statistiques
$totalUsers = count($users);
$admins = 0;
$vendeurs = 0;
$fournisseurs = 0;

foreach ($users as $user) {
    if ($user->isAdmin()) $admins++;
    if ($user->isVendeur()) $vendeurs++;
    if ($user->isFournisseur()) $fournisseurs++;
}

include '../frontend/users.html';
?>