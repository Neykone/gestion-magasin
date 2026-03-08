<?php
// test_user.php
require_once 'models/UserModel.php';

echo "<h2>Test du modèle UserModel</h2>";

try {
    $userModel = new UserModel();

    // Test 1 : Récupérer tous les utilisateurs
    echo "<h3>1. Tous les utilisateurs :</h3>";
    $users = $userModel->getAllUsers();
    echo "<ul>";
    foreach ($users as $user) {
        echo "<li>{$user['id']} - {$user['nom']} - {$user['email']} - {$user['role']}</li>";
    }
    echo "</ul>";

    // Test 2 : Récupérer un utilisateur par email
    echo "<h3>2. Recherche par email (admin@magasin.com) :</h3>";
    $user = $userModel->getUserByEmail('admin@magasin.com');
    if ($user) {
        echo "<p>✅ Trouvé : {$user['nom']} ({$user['role']})</p>";
    } else {
        echo "<p>❌ Non trouvé</p>";
    }

    // Test 3 : Vérifier le mot de passe
    echo "<h3>3. Vérification mot de passe :</h3>";
    $result = $userModel->verifyPassword('admin@magasin.com', 'admin123');
    if ($result) {
        echo "<p style='color: green;'>✅ Mot de passe correct pour admin</p>";
    } else {
        echo "<p style='color: red;'>❌ Mot de passe incorrect</p>";
    }

    $result = $userModel->verifyPassword('vendeur@magasin.com', 'vendeur123');
    if ($result) {
        echo "<p style='color: green;'>✅ Mot de passe correct pour vendeur</p>";
    } else {
        echo "<p style='color: red;'>❌ Mot de passe incorrect</p>";
    }

    // Test 4 : Compter les utilisateurs
    echo "<h3>4. Statistiques :</h3>";
    $count = $userModel->countUsers();
    echo "<p>Total utilisateurs : <strong>$count</strong></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>
