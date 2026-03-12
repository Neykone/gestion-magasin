<?php
// backend/hash_passwords.php
require_once 'config/Database.php';

$db = Database::getInstance();

// Récupérer tous les utilisateurs
$stmt = $db->query("SELECT id, email, password FROM users");
$users = $stmt->fetchAll();

foreach ($users as $user) {
    $plainPassword = $user['password'];

    // Vérifier si c'est déjà un hash (commence par $2y$)
    if (strpos($plainPassword, '$2y$') === 0) {
        echo "Utilisateur {$user['email']} : déjà hashé<br>";
        continue;
    }

    // Hacher le mot de passe
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Mettre à jour
    $updateStmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateStmt->execute([$hashedPassword, $user['id']]);

    echo "Utilisateur {$user['email']} : mot de passe hashé avec succès<br>";
}

echo "Terminé !";
?>
