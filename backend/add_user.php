<?php
// backend/add_user.php
session_start();
require_once 'config/Database.php';
require_once 'models/UserModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Initialiser le modèle
$userModel = new UserModel();

$message = '';
$messageType = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'vendeur';
    $statut = $_POST['statut'] ?? 'actif';

    // Validation
    $errors = [];

    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire";
    }

    if (empty($email)) {
        $errors[] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est obligatoire";
    } elseif (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    }

    // Vérifier si l'email existe déjà
    if (empty($errors)) {
        $existingUser = $userModel->getUserByEmail($email);
        if ($existingUser) {
            $errors[] = "Cet email est déjà utilisé";
        }
    }

    if (empty($errors)) {
        // Hacher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($userModel->addUser($nom, $email, $hashedPassword, $role, $statut)) {
            $message = "Utilisateur ajouté avec succès !";
            $messageType = 'success';
        } else {
            $message = "Erreur lors de l'ajout de l'utilisateur";
            $messageType = 'error';
        }
    } else {
        $message = implode("<br>", $errors);
        $messageType = 'error';
    }
}

include '../frontend/add_user.html';
?>
