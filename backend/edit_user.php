<?php
// backend/edit_user.php
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

// Récupérer l'ID de l'utilisateur à modifier
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: users.php');
    exit();
}

// Récupérer l'utilisateur
$user = $userModel->getUserById($id);

if (!$user) {
    header('Location: users.php');
    exit();
}

$message = '';
$messageType = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'vendeur';
    $statut = $_POST['statut'] ?? 'actif';
    $newPassword = $_POST['new_password'] ?? '';

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

    // Vérifier si l'email existe déjà (pour un autre utilisateur)
    $existingUser = $userModel->getUserByEmail($email);
    if ($existingUser && $existingUser['id'] != $id) {
        $errors[] = "Cet email est déjà utilisé par un autre utilisateur";
    }

    if (empty($errors)) {
        // Mise à jour des informations de base
        if ($userModel->updateUser($id, $nom, $email, $role, $statut)) {

            // Si un nouveau mot de passe est fourni, le mettre à jour
            if (!empty($newPassword)) {
                if (strlen($newPassword) >= 6) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $userModel->changePassword($id, $hashedPassword);
                    $message = "Utilisateur modifié avec succès (mot de passe changé) !";
                } else {
                    $message = "Utilisateur modifié mais le mot de passe doit faire 6 caractères minimum";
                }
            } else {
                $message = "Utilisateur modifié avec succès !";
            }
            $messageType = 'success';

            // Recharger l'utilisateur
            $user = $userModel->getUserById($id);
        } else {
            $message = "Erreur lors de la modification";
            $messageType = 'error';
        }
    } else {
        $message = implode("<br>", $errors);
        $messageType = 'error';
    }
}

include '../frontend/edit_user.html';
?>
