<?php
// config/Database.php
// Classe de connexion à la base de données (pattern Singleton)

// Pas de namespace pour l'instant (plus simple pour commencer)
// On va garder un code simple sans namespace

class Database {
    // Instance unique de la connexion
    private static $instance = null;

    // L'objet PDO de connexion
    private $pdo;

    // Constructeur privé (empêche l'instanciation directe)
    private function __construct() {
        try {
            // Configuration de la connexion
            $host = '127.0.0.1';        // ou 'localhost'
            $port = '3307';              // Ton port MySQL
            $dbname = 'gestion_magasin';
            $username = 'root';
            $password = '';               // Mot de passe vide

            // Création de la connexion PDO (avec \ devant PDO pour le namespace global)
            $this->pdo = new \PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (\PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    // Méthode pour obtenir l'instance unique
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }

    // Empêche le clonage
    private function __clone() {}

    // __wakeup public (correction de l'avertissement)
    public function __wakeup() {}
}
?>