<?php
// backend/export_ventes.php
session_start();
require_once 'config/Database.php';
require_once 'models/VenteModel.php';
require_once 'models/UserModel.php';
require_once 'fpdf/fpdf.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$venteModel = new VenteModel();
$userModel = new UserModel();

// Récupérer les paramètres de filtre
$filtre_statut = $_GET['statut'] ?? 'tous';
$filtre_periode = $_GET['periode'] ?? 'tout';

// Récupérer les ventes
$ventes = $venteModel->getVentesWithDetails();

// Appliquer les filtres si nécessaire
if ($filtre_statut !== 'tous') {
    $ventes = array_filter($ventes, function($v) use ($filtre_statut) {
        return $v->getStatut() === $filtre_statut;
    });
}

if ($filtre_periode !== 'tout') {
    $date_limite = date('Y-m-d', strtotime("-1 $filtre_periode"));
    $ventes = array_filter($ventes, function($v) use ($date_limite) {
        return date('Y-m-d', strtotime($v->getDateVente())) >= $date_limite;
    });
}

// Créer le PDF
class PDF extends FPDF {
    // En-tête
    function Header() {
        // Logo (optionnel)
        // $this->Image('logo.png',10,6,30);

        // Police Arial gras 15
        $this->SetFont('Arial', 'B', 15);

        // Titre
        $this->Cell(190, 10, 'Rapport des ventes', 0, 1, 'C');

        // Sous-titre
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(190, 6, 'Gestion Magasin - ' . date('d/m/Y H:i'), 0, 1, 'C');

        // Saut de ligne
        $this->Ln(10);

        // En-tête du tableau
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(102, 126, 234);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(20, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(35, 10, 'Date', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Client', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Vendeur', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Total', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Statut', 1, 1, 'C', true);
    }

    // Pied de page
    function Footer() {
        // Position à 1,5 cm du bas
        $this->SetY(-15);

        // Police Arial italique 8
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(0, 0, 0);

        // Numéro de page
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Initialiser le PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

$total_ventes = 0;
$montant_total = 0;

foreach ($ventes as $vente) {
    // Couleur selon le statut
    if ($vente->isPaye()) {
        $pdf->SetTextColor(40, 167, 69); // Vert
    } elseif ($vente->isAnnule()) {
        $pdf->SetTextColor(220, 53, 69); // Rouge
    } else {
        $pdf->SetTextColor(255, 193, 7); // Jaune
    }

    $pdf->Cell(20, 8, '#' . $vente->getId(), 1, 0, 'C');
    $pdf->SetTextColor(0, 0, 0); // Remettre en noir pour le reste

    $pdf->Cell(35, 8, date('d/m/Y', strtotime($vente->getDateVente())), 1, 0, 'C');
    $pdf->Cell(40, 8, substr($vente->getClientNom() ?? 'Client', 0, 15), 1, 0, 'L');
    $pdf->Cell(30, 8, substr($vente->getVendeurNom() ?? 'Inconnu', 0, 10), 1, 0, 'L');
    $pdf->Cell(30, 8, number_format($vente->getTotal(), 2) . ' DH', 1, 0, 'R');
    $pdf->Cell(25, 8, ucfirst(substr($vente->getStatut(), 0, 6)), 1, 1, 'C');

    $total_ventes++;
    $montant_total += $vente->getTotal();

    // Ajouter les détails des produits
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(20, 6, '', 0, 0);
    $pdf->Cell(155, 6, 'Produits:', 0, 1, 'L');

    foreach ($vente->getProduits() as $produit) {
        $pdf->Cell(20, 6, '', 0, 0);
        $pdf->Cell(155, 6, '  - ' . $produit['produit_nom'] . ' x' . $produit['quantite'] .
            ' (' . number_format($produit['prix_unitaire'], 2) . ' DH)', 0, 1, 'L');
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);
}

// Résumé
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'Résumé', 0, 1, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(190, 8, 'Total des ventes : ' . $total_ventes, 0, 1, 'L');
$pdf->Cell(190, 8, 'Montant total : ' . number_format($montant_total, 2) . ' DH', 0, 1, 'L');

// Générer le PDF
$pdf->Output('D', 'rapport_ventes_' . date('Y-m-d') . '.pdf');
?>
