<?php
// backend/ventes.php
session_start();
require_once 'config/Database.php';
require_once 'models/VenteModel.php';
require_once 'models/UserModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Initialiser les modèles
$venteModel = new VenteModel();
$userModel = new UserModel();

// Gestion des actions
$message = '';
$messageType = '';

// Annulation d'une vente
if (isset($_GET['annuler']) && is_numeric($_GET['annuler'])) {
    $venteId = (int)$_GET['annuler'];

    try {
        if ($venteModel->annulerVente($venteId)) {
            $message = "Vente #$venteId annulée avec succès";
            $messageType = 'success';
        } else {
            $message = "Erreur lors de l'annulation";
            $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
        $messageType = 'error';
    }
}

// Récupérer toutes les ventes avec détails
$ventes = $venteModel->getVentesWithDetails();

// Statistiques
$stats = $venteModel->getStats();
$totalVentes = $stats['nb_ventes'] ?? 0;
$chiffreAffaires = $stats['ca_total'] ?? 0;
$ventesAujourdhui = $stats['ventes_aujourdhui'] ?? 0;
$caAujourdhui = $stats['ca_aujourdhui'] ?? 0;

// Compter les ventes par statut
$ventesPayees = 0;
$ventesAttente = 0;
$ventesAnnulees = 0;

foreach ($stats['par_statut'] as $statut) {
    if ($statut['statut'] === 'payé') $ventesPayees = $statut['count'];
    if ($statut['statut'] === 'en attente') $ventesAttente = $statut['count'];
    if ($statut['statut'] === 'annulé') $ventesAnnulees = $statut['count'];
}

$jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
$valeurs = [5200, 6800, 4300, 7900, 10200, 8500, 7200];
$maxValeur = max($valeurs);

echo "<!-- DEBUG: valeurs = " . print_r($valeurs, true) . " -->";
echo "<!-- DEBUG: maxValeur = $maxValeur -->";

// Inclure la vue
include '../frontend/ventes.html';
?>