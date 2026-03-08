<?php
// test_db.php - Fichier de test temporaire
require_once 'config/Database.php';

echo "<h2>Test de connexion à la base de données</h2>";

try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✅ Connexion réussie !</p>";

    // Test simple : compter les utilisateurs
    $stmt = $db->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch();
    echo "<p>Nombre d'utilisateurs dans la base : <strong>" . $result['total'] . "</strong></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>
