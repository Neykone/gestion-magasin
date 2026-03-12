<?php
// backend/admin.php (extrait corrigé)
session_start();
require_once 'models/VenteModel.php';
require_once 'models/ProductModel.php';
require_once 'models/UserModel.php';
require_once 'config/Database.php'; // Ajout important !

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Initialiser les modèles
$venteModel = new VenteModel();
$productModel = new ProductModel();
$userModel = new UserModel();

// Statistiques des ventes
$stats = $venteModel->getStats();
$ca_jour = $stats['ca_aujourdhui'] ?? 0;
$ca_mois = 0; // À calculer
$nb_commandes = $stats['nb_ventes'] ?? 0;

// Calcul du CA du mois - CORRECTION ICI
$db = Database::getInstance(); // Au lieu de $venteModel->getDb()
$debutMois = date('Y-m-01');
$finMois = date('Y-m-t');
$sqlMois = "SELECT SUM(total) as ca FROM ventes WHERE statut = 'payé' AND date_vente BETWEEN ? AND ?";
$stmtMois = $db->prepare($sqlMois);
$stmtMois->execute([$debutMois, $finMois]);
$ca_mois = $stmtMois->fetch()['ca'] ?? 0;

// Produits en alerte
$lowStockProducts = $productModel->getLowStockProducts();
$outOfStockProducts = $productModel->getOutOfStockProducts();
$produits_alerte = count($lowStockProducts) + count($outOfStockProducts);

// Dernières ventes (5 dernières)
$sqlVentes = "SELECT v.*, u.nom as vendeur_nom 
              FROM ventes v
              LEFT JOIN users u ON v.user_id = u.id
              ORDER BY v.date_vente DESC LIMIT 5";
$recentSales = $db->query($sqlVentes)->fetchAll();

// Statistiques générales
$nb_produits = $productModel->countProducts();
$nb_utilisateurs = $userModel->countUsers();

// Données pour le graphique (7 derniers jours)
$jours = [];
$valeurs = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $jours[] = date('D', strtotime($date)); // Lun, Mar, etc.

    $sqlJour = "SELECT SUM(total) as total FROM ventes 
                WHERE statut = 'payé' AND DATE(date_vente) = ?";
    $stmtJour = $db->prepare($sqlJour);
    $stmtJour->execute([$date]);
    $total = $stmtJour->fetch()['total'] ?? 0;
    $valeurs[] = $total;
}

$maxValeur = max($valeurs) ?: 1;

include '../frontend/admin.html';
?>