<?php
// backend/add_fournisseur.php
session_start();
require_once 'config/Database.php';
require_once 'models/FournisseurModel.php';
require_once 'models/entities/Fournisseur.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user']['name'];

$fournisseurModel = new FournisseurModel();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $adresse = $_POST['adresse'] ?? '';

    if (empty($nom)) {
        $message = "Le nom du fournisseur est obligatoire";
        $messageType = 'error';
    } else {
        $fournisseur = new Fournisseur([
            'nom' => $nom,
            'contact' => $contact,
            'email' => $email,
            'telephone' => $telephone,
            'adresse' => $adresse
        ]);

        if ($fournisseurModel->addFournisseur($fournisseur)) {
            $message = "Fournisseur ajouté avec succès !";
            $messageType = 'success';
        } else {
            $message = "Erreur lors de l'ajout";
            $messageType = 'error';
        }
    }
}

include '../frontend/add_fournisseur.html';
?>