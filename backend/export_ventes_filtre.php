<?php
// backend/export_ventes_filtre.php
session_start();
require_once 'config/Database.php';
require_once 'models/VenteModel.php';
require_once 'fpdf186/fpdf.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Récupérer les dates de filtre
$date_debut = $_GET['date_debut'] ?? date('Y-m-01');
$date_fin = $_GET['date_fin'] ?? date('Y-m-d');

$venteModel = new VenteModel();
$db = Database::getInstance();

// Récupérer les ventes avec filtre de dates
$sql = "SELECT v.*, u.nom as vendeur_nom 
        FROM ventes v
        LEFT JOIN users u ON v.user_id = u.id
        WHERE DATE(v.date_vente) BETWEEN ? AND ?
        ORDER BY v.date_vente DESC";

$stmt = $db->prepare($sql);
$stmt->execute([$date_debut, $date_fin]);
$ventes_data = $stmt->fetchAll();

// Créer le PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(190, 10, 'Rapport des ventes', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(190, 6, 'Période du ' . $_GET['date_debut'] . ' au ' . $_GET['date_fin'], 0, 1, 'C');
        $this->Ln(10);

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

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

$total_ventes = 0;
$montant_total = 0;

foreach ($ventes_data as $row) {
    // Récupérer les détails des produits pour cette vente
    $sql_details = "SELECT vd.*, p.nom as produit_nom 
                    FROM vente_details vd
                    LEFT JOIN produits p ON vd.produit_id = p.id
                    WHERE vd.vente_id = ?";
    $stmt_details = $db->prepare($sql_details);
    $stmt_details->execute([$row['id']]);
    $produits = $stmt_details->fetchAll();

    // Couleur selon le statut
    if ($row['statut'] === 'payé') {
        $pdf->SetTextColor(40, 167, 69);
    } elseif ($row['statut'] === 'annulé') {
        $pdf->SetTextColor(220, 53, 69);
    } else {
        $pdf->SetTextColor(255, 193, 7);
    }

    $pdf->Cell(20, 8, '#' . $row['id'], 1, 0, 'C');
    $pdf->SetTextColor(0, 0, 0);

    $pdf->Cell(35, 8, date('d/m/Y', strtotime($row['date_vente'])), 1, 0, 'C');
    $pdf->Cell(40, 8, substr($row['client_nom'] ?? 'Client', 0, 15), 1, 0, 'L');
    $pdf->Cell(30, 8, substr($row['vendeur_nom'] ?? 'Inconnu', 0, 10), 1, 0, 'L');
    $pdf->Cell(30, 8, number_format($row['total'], 2) . ' DH', 1, 0, 'R');
    $pdf->Cell(25, 8, ucfirst(substr($row['statut'], 0, 6)), 1, 1, 'C');

    $total_ventes++;
    $montant_total += $row['total'];

    // Détails des produits
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(20, 6, '', 0, 0);
    $pdf->Cell(155, 6, 'Produits:', 0, 1, 'L');

    foreach ($produits as $produit) {
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
$pdf->Cell(190, 10, 'Résumé de la période', 0, 1, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(190, 8, 'Total des ventes : ' . $total_ventes, 0, 1, 'L');
$pdf->Cell(190, 8, 'Montant total : ' . number_format($montant_total, 2) . ' DH', 0, 1, 'L');
$pdf->Cell(190, 8, 'Moyenne par vente : ' . number_format($montant_total / max($total_ventes, 1), 2) . ' DH', 0, 1, 'L');

$pdf->Output('D', 'rapport_ventes_' . $date_debut . '_au_' . $date_fin . '.pdf');
?>
