<?php
// backend/login.php
session_start();
require_once 'models/UserModel.php';

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
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $userModel = new UserModel();
    $user = $userModel->verifyPassword($email, $password);

    if ($user) {
        // Maintenant $user est un objet User
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'name' => $user->getNom(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ];

        if ($user->isAdmin()) {
            header('Location: admin.php');
        } elseif ($user->isVendeur()) {
            header('Location: caisse.php');
        } elseif ($user->isFournisseur()) {
            header('Location: fournisseur_dashboard.php');
        }
        exit();
    } else {
        $error = 'Email ou mot de passe incorrect';
    }
}

include '../frontend/login.html';
?>