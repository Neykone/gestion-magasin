<?php
// login.php - Logique de connexion
session_start();

$error = null;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Simulation de vérification (à remplacer par votre logique BDD)
    if ($email === 'admin@magasin.com' && $password === 'admin123') {
        $_SESSION['user'] = [
            'id' => 1,
            'name' => 'Admin',
            'email' => $email,
            'role' => 'admin'
        ];
        header('Location: admin.php');
        exit();
    } else {
        $error = 'Email ou mot de passe incorrect';
    }
}

// Inclure le template HTML
include 'login.html';
?>