<?php
// Charger les variables d'environnement
require_once __DIR__ . '/../vendor/autoload.php';

// Charger le fichier .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Récupérer les variables d'environnement
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'preinscription_db';
$user = $_ENV['DB_USER'] ?? 'postgres';
$password = $_ENV['DB_PASS'] ?? '';
$port = $_ENV['DB_PORT'] ?? '5432';

// Validation des variables d'environnement
$required_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($required_vars as $var) {
    if (!isset($_ENV[$var]) || empty($_ENV[$var])) {
        die("Erreur de configuration : La variable d'environnement $var n'est pas définie ou est vide.");
    }
}

try {
    // Connexion avec PDO à PostgreSQL
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    
    // Affichage des erreurs PDO sous forme d'exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Encodage UTF-8
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
