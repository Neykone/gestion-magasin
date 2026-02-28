<?php
// admin.php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Récupérer les infos de l'utilisateur
$userName = $_SESSION['user']['name'];
$userRole = $_SESSION['user']['role'];

// Inclure le template HTML
include 'admin.html';
?>
