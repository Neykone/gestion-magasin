<?php
// login.php - Version simplifiée avec données simulées
session_start();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Simulation de vérification
    if ($email === 'admin@magasin.com' && $password === 'admin123') {
        $_SESSION['user'] = [
            'id' => 1,
            'name' => 'Admin',
            'email' => $email,
            'role' => 'admin'
        ];
        header('Location: admin.php');
        exit();

    } elseif ($email === 'vendeur@magasin.com' && $password === 'vendeur123') {
        $_SESSION['user'] = [
            'id' => 2,
            'name' => 'Vendeur',
            'email' => $email,
            'role' => 'vendeur'
        ];
        header('Location: caisse.php');
        exit();

    } elseif ($email === 'fournisseur@magasin.com' && $password === 'fournisseur123') {
        $_SESSION['user'] = [
            'id' => 3,
            'name' => 'Pierre Durand',
            'email' => $email,
            'role' => 'fournisseur'
        ];
        header('Location: fournisseur_dashboard.php');  // ← Redirection fournisseur
        exit();

    } else {
        $error = 'Email ou mot de passe incorrect';
    }
}

include 'login.html';
?>