<?php
// Lecture des variables d'environnement depuis le fichier .env
$env_path = __DIR__ . '/.env';
$env_vars = [];

if (file_exists($env_path)) {
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignore les commentaires
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
            continue;
        }
        list($key, $value) = explode('=', $line, 2);
        $env_vars[trim($key)] = trim($value);
    }
}

// Détection automatique de l'environnement
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'alwaysdata.net') !== false) {
    // CONFIGURATION EN LIGNE (AlwaysData)
    $host = $env_vars['DB_REMOTE_HOST'] ?? '';
    $dbname = $env_vars['DB_REMOTE_NAME'] ?? '';
    $username = $env_vars['DB_REMOTE_USER'] ?? '';
    $password = $env_vars['DB_REMOTE_PASSWORD'] ?? '';
} else {
    // CONFIGURATION LOCALE (XAMPP)
    $host = $env_vars['DB_LOCAL_HOST'] ?? 'localhost';
    $dbname = $env_vars['DB_LOCAL_NAME'] ?? 'vite_et_gourmand';
    $username = $env_vars['DB_LOCAL_USER'] ?? 'root';
    $password = $env_vars['DB_LOCAL_PASSWORD'] ?? '';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>