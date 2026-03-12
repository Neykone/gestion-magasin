<?php
// backend/add_categorie.php
session_start();
require_once 'config/Database.php';
require_once 'models/CategorieModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

$categorieModel = new CategorieModel();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($nom)) {
        $message = "Le nom de la catégorie est obligatoire";
        $messageType = 'error';
    } else {
        if ($categorieModel->addCategorie($nom, $description)) {
            $message = "Catégorie ajoutée avec succès !";
            $messageType = 'success';
        } else {
            $message = "Erreur lors de l'ajout";
            $messageType = 'error';
        }
    }
}

include '../frontend/add_categorie.html';
?>
