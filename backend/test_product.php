<?php
// test_product.php
require_once 'models/ProductModel.php';

echo "<h2>Test du modèle ProductModel</h2>";

try {
    $productModel = new ProductModel();

    // Test 1 : Récupérer tous les produits
    echo "<h3>1. Tous les produits :</h3>";
    $products = $productModel->getAllProducts();
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nom</th><th>Catégorie</th><th>Fournisseur</th><th>Prix</th><th>Stock</th></tr>";
    foreach ($products as $product) {
        echo "<tr>";
        echo "<td>{$product['id']}</td>";
        echo "<td>{$product['nom']}</td>";
        echo "<td>{$product['categorie_nom']}</td>";
        echo "<td>{$product['fournisseur_nom']}</td>";
        echo "<td>" . number_format($product['prix_vente'], 2) . " DH</td>";
        echo "<td>{$product['stock']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Test 2 : Produits en stock faible
    echo "<h3>2. Produits avec stock faible :</h3>";
    $lowStock = $productModel->getLowStockProducts();
    if (count($lowStock) > 0) {
        echo "<ul>";
        foreach ($lowStock as $p) {
            echo "<li>{$p['nom']} - Stock: {$p['stock']} (Seuil: {$p['seuil_alerte']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Aucun produit en stock faible</p>";
    }

    // Test 3 : Produits en rupture
    echo "<h3>3. Produits en rupture :</h3>";
    $outOfStock = $productModel->getOutOfStockProducts();
    if (count($outOfStock) > 0) {
        echo "<ul>";
        foreach ($outOfStock as $p) {
            echo "<li>{$p['nom']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Aucun produit en rupture</p>";
    }

    // Test 4 : Statistiques
    echo "<h3>4. Statistiques :</h3>";
    $count = $productModel->countProducts();
    $value = $productModel->getTotalStockValue();
    echo "<p>Total produits : <strong>$count</strong></p>";
    echo "<p>Valeur du stock : <strong>" . number_format($value, 2) . " DH</strong></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>
