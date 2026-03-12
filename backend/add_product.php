<?php
// backend/add_product.php
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

$categorieModel = new CategorieModel();
$fournisseurModel = new FournisseurModel();
$productModel = new ProductModel();

$categories = $categorieModel->getAllCategories();
$fournisseurs = $fournisseurModel->getAllFournisseurs();

$message = '';
$messageType = '';

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
        $product = new Product([
            'nom' => $nom,
            'description' => $description,
            'prix_achat' => $prix_achat,
            'prix_vente' => $prix_vente,
            'stock' => $stock,
            'seuil_alerte' => $seuil_alerte,
            'categorie_id' => $categorie_id,
            'fournisseur_id' => $fournisseur_id
        ]);

        if ($productModel->addProduct($product)) {
            $message = "Produit ajouté avec succès !";
            $messageType = 'success';
        } else {
            $message = "Erreur lors de l'ajout du produit";
            $messageType = 'error';
        }
    }
}

include '../frontend/add_product.html';
?>