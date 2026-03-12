<?php
// backend/test_vente.php
require_once 'models/VenteModel.php';
require_once 'models/ProductModel.php';

echo "<h2>Test du modèle VenteModel</h2>";

try {
    $venteModel = new VenteModel();
    $productModel = new ProductModel();

    // Test 1 : Statistiques
    echo "<h3>1. Statistiques des ventes :</h3>";
    $stats = $venteModel->getStats();
    echo "<pre>";
    print_r($stats);
    echo "</pre>";

    // Test 2 : Liste des ventes
    echo "<h3>2. Liste des ventes :</h3>";
    $ventes = $venteModel->getAllVentes();
    echo "<p>Nombre de ventes : " . count($ventes) . "</p>";

    if (count($ventes) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Date</th><th>Client</th><th>Total</th><th>Statut</th></tr>";
        foreach ($ventes as $vente) {
            echo "<tr>";
            echo "<td>{$vente['id']}</td>";
            echo "<td>{$vente['date_vente']}</td>";
            echo "<td>{$vente['client_nom']}</td>";
            echo "<td>" . number_format($vente['total'], 2) . " DH</td>";
            echo "<td>{$vente['statut']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Aucune vente enregistrée</p>";
    }

    // Test 3 : Créer une vente de test (seulement s'il y a des produits)
    $products = $productModel->getAllProducts();
    if (count($products) > 0) {
        echo "<h3>3. Test création d'une vente :</h3>";

        $produitsVente = [
            ['id' => $products[0]['id'], 'quantite' => 1, 'prix' => $products[0]['prix_vente']]
        ];

        try {
            $venteId = $venteModel->createVente(
                1, // user_id (admin)
                "Client Test",
                $products[0]['prix_vente'],
                'carte',
                $produitsVente
            );
            echo "<p style='color: green;'>✅ Vente créée avec l'ID : $venteId</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur création : " . $e->getMessage() . "</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>
