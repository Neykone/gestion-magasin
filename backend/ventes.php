<?php
// ventes.php - Gestion des ventes
session_start();
require_once 'config/Database.php';
require_once 'models/UserModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

// Données simulées des ventes
$ventes = [
    [
        'id' => 1001,
        'date' => '2026-03-01 14:30',
        'client' => 'Client A',
        'vendeur' => 'Jean Dupont',
        'produits' => [
            ['nom' => 'iPhone 13', 'quantite' => 1, 'prix' => 999.99],
            ['nom' => 'Coque iPhone', 'quantite' => 1, 'prix' => 29.99]
        ],
        'total' => 1029.98,
        'statut' => 'payé',
        'paiement' => 'carte'
    ],
    [
        'id' => 1002,
        'date' => '2026-03-01 11:15',
        'client' => 'Client B',
        'vendeur' => 'Marie Martin',
        'produits' => [
            ['nom' => 'Samsung Galaxy S21', 'quantite' => 1, 'prix' => 899.99],
            ['nom' => 'Écran 24" LG', 'quantite' => 2, 'prix' => 229.99]
        ],
        'total' => 1359.97,
        'statut' => 'payé',
        'paiement' => 'espèces'
    ],
    [
        'id' => 1003,
        'date' => '2026-02-28 17:45',
        'client' => 'Client C',
        'vendeur' => 'Jean Dupont',
        'produits' => [
            ['nom' => 'Clavier Mécanique', 'quantite' => 1, 'prix' => 89.99],
            ['nom' => 'Souris Sans Fil', 'quantite' => 1, 'prix' => 49.99]
        ],
        'total' => 139.98,
        'statut' => 'en attente',
        'paiement' => 'carte'
    ],
    [
        'id' => 1004,
        'date' => '2026-02-28 10:20',
        'client' => 'Client D',
        'vendeur' => 'Sophie Lefebvre',
        'produits' => [
            ['nom' => 'Ordinateur Dell XPS 13', 'quantite' => 1, 'prix' => 1499.99],
            ['nom' => 'Sacoche ordinateur', 'quantite' => 1, 'prix' => 49.99]
        ],
        'total' => 1549.98,
        'statut' => 'payé',
        'paiement' => 'virement'
    ],
    [
        'id' => 1005,
        'date' => '2026-02-27 16:30',
        'client' => 'Client E',
        'vendeur' => 'Marie Martin',
        'produits' => [
            ['nom' => 'Casque Audio', 'quantite' => 2, 'prix' => 79.99]
        ],
        'total' => 159.98,
        'statut' => 'annulé',
        'paiement' => 'carte'
    ],
    [
        'id' => 1006,
        'date' => '2026-02-27 09:15',
        'client' => 'Client F',
        'vendeur' => 'Jean Dupont',
        'produits' => [
            ['nom' => 'iPhone 13', 'quantite' => 2, 'prix' => 999.99],
            ['nom' => 'AirPods', 'quantite' => 2, 'prix' => 199.99]
        ],
        'total' => 2399.96,
        'statut' => 'payé',
        'paiement' => 'carte'
    ]
];

// Statistiques
$totalVentes = count($ventes);
$chiffreAffaires = array_sum(array_column($ventes, 'total'));
$ventesPayees = count(array_filter($ventes, function ($v) {
    return $v['statut'] === 'payé';
}));
$ventesAttente = count(array_filter($ventes, function ($v) {
    return $v['statut'] === 'en attente';
}));

$message = '';
$messageType = '';

include '../frontend/ventes.html';
?>
