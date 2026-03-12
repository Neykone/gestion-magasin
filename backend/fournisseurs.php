<?php
// backend/fournisseurs.php
session_start();
require_once 'config/Database.php';
require_once 'models/FournisseurModel.php';
require_once 'models/ProductModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Initialiser les modèles
$fournisseurModel = new FournisseurModel();
$productModel = new ProductModel();

// Gestion des actions
$message = '';
$messageType = '';

// Suppression d'un fournisseur
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

    if ($fournisseurModel->deleteFournisseur($deleteId)) {
        $message = "Fournisseur supprimé avec succès";
        $messageType = 'success';
    } else {
        $message = "Impossible de supprimer ce fournisseur (utilisé par des produits)";
        $messageType = 'error';
    }
}

// Récupérer tous les fournisseurs
$fournisseurs = $fournisseurModel->getAllFournisseurs();

// Ajouter le nombre de produits pour chaque fournisseur
foreach ($fournisseurs as &$fournisseur) {
    $produits = $productModel->getProductsBySupplier($fournisseur['id']);
    $fournisseur['produits_fournis'] = count($produits);
}

// Statistiques
$totalFournisseurs = count($fournisseurs);
$fournisseursActifs = count(array_filter($fournisseurs, function ($f) {
    return ($f['statut'] ?? 'actif') === 'actif';
}));
$totalProduitsFournis = array_sum(array_column($fournisseurs, 'produits_fournis'));

include '../frontend/fournisseurs.html';
?>