<?php
// backend/edit_categorie.php
session_start();
require_once 'config/Database.php';
require_once 'models/CategorieModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Initialiser le modèle
$categorieModel = new CategorieModel();

// Récupérer l'ID de la catégorie à modifier
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: categories.php');
    exit();
}

// Récupérer la catégorie
$categorie = $categorieModel->getCategorieById($id);

if (!$categorie) {
    header('Location: categories.php');
    exit();
}

$message = '';
$messageType = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'actif';

    if (empty($nom)) {
        $message = "Le nom de la catégorie est obligatoire";
        $messageType = 'error';
    } else {
        if ($categorieModel->updateCategorie($id, $nom, $description, $statut)) {
            $message = "Catégorie modifiée avec succès !";
            $messageType = 'success';
            // Recharger la catégorie
            $categorie = $categorieModel->getCategorieById($id);
        } else {
            $message = "Erreur lors de la modification";
            $messageType = 'error';
        }
    }
}

include '../frontend/edit_categorie.html';
?>
