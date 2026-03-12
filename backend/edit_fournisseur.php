<?php
// backend/edit_fournisseur.php
session_start();
require_once 'models/FournisseurModel.php';
require_once 'models/ProductModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

$fournisseurModel = new FournisseurModel();
$productModel = new ProductModel();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: fournisseurs.php');
    exit();
}

$fournisseur = $fournisseurModel->getFournisseurById($id);

if (!$fournisseur) {
    header('Location: fournisseurs.php');
    exit();
}

// Récupérer les produits de ce fournisseur
$produits = $productModel->getProductsBySupplier($id);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $statut = $_POST['statut'] ?? 'actif';

    if (empty($nom)) {
        $message = "Le nom du fournisseur est obligatoire";
        $messageType = 'error';
    } else {
        $fournisseur->setNom($nom)
            ->setContact($contact)
            ->setEmail($email)
            ->setTelephone($telephone)
            ->setAdresse($adresse)
            ->setStatut($statut);

        if ($fournisseurModel->updateFournisseur($fournisseur)) {
            $message = "Fournisseur modifié avec succès !";
            $messageType = 'success';
        } else {
            $message = "Erreur lors de la modification";
            $messageType = 'error';
        }
    }
}

include '../frontend/edit_fournisseur.html';
?>