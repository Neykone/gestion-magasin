<?php
// backend/edit_product.php
session_start();
require_once 'config/Database.php';
require_once 'models/ProductModel.php';
require_once 'models/CategorieModel.php';
require_once 'models/FournisseurModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Initialiser les modèles
$productModel = new ProductModel();
$categorieModel = new CategorieModel();
$fournisseurModel = new FournisseurModel();

// Récupérer l'ID du produit à modifier
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: products.php');
    exit();
}

// Récupérer le produit
$product = $productModel->getProductById($id);

if (!$product) {
    header('Location: products.php');
    exit();
}

// Récupérer les listes pour les selects
$categories = $categorieModel->getAllCategories();
$fournisseurs = $fournisseurModel->getAllFournisseurs();

$message = '';
$messageType = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix_achat = floatval($_POST['prix_achat'] ?? 0);
    $prix_vente = floatval($_POST['prix_vente'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $seuil_alerte = intval($_POST['seuil_alerte'] ?? 5);
    $categorie_id = !empty($_POST['categorie_id']) ? intval($_POST['categorie_id']) : null;
    $fournisseur_id = !empty($_POST['fournisseur_id']) ? intval($_POST['fournisseur_id']) : null;

    if (empty($nom)) {
        $message = "Le nom du produit est obligatoire";
        $messageType = 'error';
    } elseif ($prix_vente <= 0) {
        $message = "Le prix de vente doit être supérieur à 0";
        $messageType = 'error';
    } else {
        if ($productModel->updateProduct($id, $nom, $description, $prix_achat, $prix_vente, $stock, $seuil_alerte, $categorie_id, $fournisseur_id)) {
            $message = "Produit modifié avec succès !";
            $messageType = 'success';
            // Recharger le produit
            $product = $productModel->getProductById($id);
        } else {
            $message = "Erreur lors de la modification";
            $messageType = 'error';
        }
    }
}

include '../frontend/edit_product.html';
?>
