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

$categorieModel = new CategorieModel();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: categories.php');
    exit();
}

$categorie = $categorieModel->getCategorieById($id);

if (!$categorie) {
    header('Location: categories.php');
    exit();
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'actif';

    if (empty($nom)) {
        $message = "Le nom de la catégorie est obligatoire";
        $messageType = 'error';
    } else {
        $categorie->setNom($nom)
            ->setDescription($description)
            ->setStatut($statut);

        if ($categorieModel->updateCategorie($categorie)) {
            $message = "Catégorie modifiée avec succès !";
            $messageType = 'success';
        } else {
            $message = "Erreur lors de la modification";
            $messageType = 'error';
        }
    }
}

include '../frontend/edit_categorie.html';
?>