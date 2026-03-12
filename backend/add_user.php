<?php
// backend/add_user.php
session_start();
require_once 'config/Database.php';
require_once 'models/UserModel.php';
require_once 'models/FournisseurModel.php';
require_once 'models/entities/User.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

$userModel = new UserModel();
$fournisseurModel = new FournisseurModel();

$fournisseurs = $fournisseurModel->getAllFournisseurs();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'vendeur';
    $statut = $_POST['statut'] ?? 'actif';
    $fournisseur_id = !empty($_POST['fournisseur_id']) ? intval($_POST['fournisseur_id']) : null;

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

    $existingUser = $userModel->getUserByEmail($email);
    if ($existingUser) {
        $errors[] = "Cet email est déjà utilisé";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $user = new User([
            'nom' => $nom,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'statut' => $statut,
            'fournisseur_id' => $fournisseur_id
        ]);

        if ($userModel->addUser($user)) {
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