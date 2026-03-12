<?php
// test_categorie_fournisseur.php
require_once 'models/CategorieModel.php';
require_once 'models/FournisseurModel.php';

echo "<h2>Test des modèles Catégorie et Fournisseur</h2>";

try {
    // Test Catégorie
    echo "<h3>1. Test CatégorieModel :</h3>";
    $categorieModel = new CategorieModel();
    $categories = $categorieModel->getAllCategories();

    echo "<p>Nombre de catégories : " . count($categories) . "</p>";
    echo "<ul>";
    foreach ($categories as $cat) {
        echo "<li>{$cat['id']} - {$cat['nom']} - {$cat['description']}</li>";
    }
    echo "</ul>";

    // Test Fournisseur
    echo "<h3>2. Test FournisseurModel :</h3>";
    $fournisseurModel = new FournisseurModel();
    $fournisseurs = $fournisseurModel->getAllFournisseurs();

    echo "<p>Nombre de fournisseurs : " . count($fournisseurs) . "</p>";
    echo "<ul>";
    foreach ($fournisseurs as $f) {
        echo "<li>{$f['id']} - {$f['nom']} - {$f['email']}</li>";
    }
    echo "</ul>";

    // Test statistiques
    echo "<h3>3. Statistiques :</h3>";
    echo "<p>Total catégories : " . $categorieModel->countCategories() . "</p>";
    echo "<p>Total fournisseurs : " . $fournisseurModel->countFournisseurs() . "</p>";
    echo "<p>Fournisseurs actifs : " . count($fournisseurModel->getFournisseursActifs()) . "</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>