<?php
// backend/caisse.php
session_start();
require_once 'config/Database.php';
require_once 'models/ProductModel.php';
require_once 'models/VenteModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'vendeur') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];
$userId = $_SESSION['user']['id'];

// Initialiser les modèles
$productModel = new ProductModel();
$venteModel = new VenteModel();

// Récupérer tous les produits pour la caisse
$products = $productModel->getAllProducts();  // ← C'est cette ligne qui est cruciale

// Vérification (à supprimer après)
if (empty($products)) {
    $products = []; // Évite l'erreur si aucun produit
}

// Message de notification
$message = '';
$messageType = '';

// Traitement du formulaire de vente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'encaisser') {

    $panier = json_decode($_POST['panier'], true);
    $total = floatval($_POST['total']);
    $paiement = $_POST['paiement'] ?? 'carte';
    $clientNom = $_POST['client_nom'] ?? 'Client';

    if (empty($panier)) {
        $message = "Panier vide !";
        $messageType = 'error';
    } else {
        try {
            $venteId = $venteModel->createVente(
                $userId,
                $clientNom,
                $total,
                $paiement,
                $panier
            );

            $message = "Vente #$venteId enregistrée avec succès !";
            $messageType = 'success';

            // Recharger les produits après la vente (pour mettre à jour les stocks)
            $products = $productModel->getAllProducts();

        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// DEBUG - À SUPPRIMER PLUS TARD
// echo "<!-- Nombre de produits : " . count($products) . " -->";

include '../frontend/caisse.html';
?>