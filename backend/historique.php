<?php
// backend/historique.php
session_start();
require_once 'config/Database.php';
require_once 'models/VenteModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'vendeur') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];
$userId = $_SESSION['user']['id'];

$venteModel = new VenteModel();

// Récupérer uniquement les ventes du vendeur connecté
$ventes = $venteModel->getVentesByVendeur($userId);

// Statistiques personnelles du vendeur
$totalVentes = count($ventes);
$chiffreAffaires = 0;
$ventesReussies = 0;
$ventesAnnulees = 0;

foreach ($ventes as $vente) {
    if ($vente->isPaye()) {
        $chiffreAffaires += $vente->getTotal();
        $ventesReussies++;
    } elseif ($vente->isAnnule()) {
        $ventesAnnulees++;
    }
}

include '../frontend/historique.html';
?>