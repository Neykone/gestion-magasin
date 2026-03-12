<?php
// backend/edit_user.php
session_start();
require_once 'config/Database.php';
require_once 'models/UserModel.php';
require_once 'models/FournisseurModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

$userModel = new UserModel();
$fournisseurModel = new FournisseurModel();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: users.php');
    exit();
}

$user = $userModel->getUserById($id);

if (!$user) {
    header('Location: users.php');
    exit();
}

$fournisseurs = $fournisseurModel->getAllFournisseurs();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'vendeur';
    $statut = $_POST['statut'] ?? 'actif';
    $fournisseur_id = !empty($_POST['fournisseur_id']) ? intval($_POST['fournisseur_id']) : null;
    $newPassword = $_POST['new_password'] ?? '';

    $errors = [];

    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire";
    }

    if (empty($email)) {
        $errors[] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }

    $existingUser = $userModel->getUserByEmail($email);
    if ($existingUser && $existingUser->getId() != $id) {
        $errors[] = "Cet email est déjà utilisé par un autre utilisateur";
    }

    if (empty($errors)) {
        $user->setNom($nom)
            ->setEmail($email)
            ->setRole($role)
            ->setStatut($statut)
            ->setFournisseurId($fournisseur_id);

        if ($userModel->updateUser($user)) {
            if (!empty($newPassword) && strlen($newPassword) >= 6) {
                $userModel->changePassword($id, $newPassword);
                $message = "Utilisateur modifié avec succès (mot de passe changé) !";
            } else {
                $message = "Utilisateur modifié avec succès !";
            }
            $messageType = 'success';
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