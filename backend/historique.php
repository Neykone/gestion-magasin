<?php
// historique.php - Historique des ventes pour le vendeur
session_start();
require_once 'config/Database.php';
require_once 'models/UserModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'vendeur') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];
$userId = $_SESSION['user']['id'];

// Données simulées de l'historique des ventes du vendeur connecté
$historiqueVentes = [
    [
        'id' => 1001,
        'date' => '2026-03-01 14:30',
        'client' => 'Client A',
        'produits' => [
            ['nom' => 'iPhone 13', 'quantite' => 1, 'prix' => 999.99],
            ['nom' => 'Coque iPhone', 'quantite' => 1, 'prix' => 29.99]
        ],
        'total' => 1029.98,
        'statut' => 'payé',
        'paiement' => 'carte'
    ],
    [
        'id' => 1003,
        'date' => '2026-02-28 17:45',
        'client' => 'Client C',
        'produits' => [
            ['nom' => 'Clavier Mécanique', 'quantite' => 1, 'prix' => 89.99],
            ['nom' => 'Souris Sans Fil', 'quantite' => 1, 'prix' => 49.99]
        ],
        'total' => 139.98,
        'statut' => 'payé',
        'paiement' => 'carte'
    ],
    [
        'id' => 1007,
        'date' => '2026-02-28 11:20',
        'client' => 'Client G',
        'produits' => [
            ['nom' => 'Casque Audio', 'quantite' => 1, 'prix' => 79.99],
            ['nom' => 'Adaptateur USB', 'quantite' => 2, 'prix' => 15.99]
        ],
        'total' => 111.97,
        'statut' => 'payé',
        'paiement' => 'espèces'
    ],
    [
        'id' => 1010,
        'date' => '2026-02-27 09:45',
        'client' => 'Client J',
        'produits' => [
            ['nom' => 'Écran 24" LG', 'quantite' => 1, 'prix' => 229.99],
            ['nom' => 'Câble HDMI', 'quantite' => 1, 'prix' => 19.99]
        ],
        'total' => 249.98,
        'statut' => 'payé',
        'paiement' => 'carte'
    ],
    [
        'id' => 1012,
        'date' => '2026-02-26 16:30',
        'client' => 'Client L',
        'produits' => [
            ['nom' => 'Samsung Galaxy S21', 'quantite' => 1, 'prix' => 899.99]
        ],
        'total' => 899.99,
        'statut' => 'annulé',
        'paiement' => 'carte'
    ]
];

// Statistiques
$totalVentes = count($historiqueVentes);
$chiffreAffaires = array_sum(array_column($historiqueVentes, 'total'));
$ventesReussies = count(array_filter($historiqueVentes, function ($v) {
    return $v['statut'] === 'payé';
}));
$ventesAnnulees = count(array_filter($historiqueVentes, function ($v) {
    return $v['statut'] === 'annulé';
}));

// Message de notification
$message = '';
$messageType = '';

include '../frontend/historique.html';
?>
