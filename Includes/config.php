<?php
/**
 * Configuration de la base de données
 * Ce fichier sert de fallback si le fichier .env n'est pas disponible
 */

// Configuration par défaut (à utiliser uniquement en développement)
$default_config = [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'preinscription_db',
    'DB_USER' => 'postgres',
    'DB_PASS' => '',
    'DB_PORT' => '5432'
];

// Fonction pour charger la configuration
function loadDatabaseConfig() {
    global $default_config;
    
    // Essayer de charger depuis .env
    if (file_exists(__DIR__ . '/../.env')) {
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->load();
            
            // Vérifier que toutes les variables nécessaires sont présentes
            $required_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
            foreach ($required_vars as $var) {
                if (!isset($_ENV[$var]) || empty($_ENV[$var])) {
                    throw new Exception("Variable $var manquante dans .env");
                }
            }
            
            return [
                'host' => $_ENV['DB_HOST'],
                'dbname' => $_ENV['DB_NAME'],
                'user' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASS'],
                'port' => $_ENV['DB_PORT'] ?? '5432'
            ];
        } catch (Exception $e) {
            error_log("Erreur lors du chargement du fichier .env: " . $e->getMessage());
        }
    }
    
    // Fallback vers la configuration par défaut
    return [
        'host' => $default_config['DB_HOST'],
        'dbname' => $default_config['DB_NAME'],
        'user' => $default_config['DB_USER'],
        'password' => $default_config['DB_PASS'],
        'port' => $default_config['DB_PORT']
    ];
}
?> 