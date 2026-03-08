<?php
// login.php - Version avec base de données
session_start();

// Inclure le modèle UserModel
require_once 'models/UserModel.php';

// Si déjà connecté, rediriger selon le rôle
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: admin.php');
    } elseif ($_SESSION['user']['role'] === 'vendeur') {
        header('Location: caisse.php');
    } elseif ($_SESSION['user']['role'] === 'fournisseur') {
        header('Location: fournisseur_dashboard.php');
    }
    exit();
}

$error = null;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Utiliser le modèle pour vérifier les identifiants
    $userModel = new UserModel();
    $user = $userModel->verifyPassword($email, $password);

    if ($user) {
        // Connexion réussie
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['nom'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        // Redirection selon le rôle
        if ($user['role'] === 'admin') {
            header('Location: admin.php');
        } elseif ($user['role'] === 'vendeur') {
            header('Location: caisse.php');
        } elseif ($user['role'] === 'fournisseur') {
            header('Location: fournisseur_dashboard.php');
        }
        exit();

    } else {
        $error = 'Email ou mot de passe incorrect';
    }
}

// Inclure la vue (le HTML ne change pas)
include 'login.html';
?>