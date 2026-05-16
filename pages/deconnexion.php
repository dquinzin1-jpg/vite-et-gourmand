<?php
session_start();

// Détection automatique du préfixe d'URL
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'alwaysdata.net') !== false) {
    $BASE_URL = '';
} else {
    $BASE_URL = '/vite-et-gourmand';
}

// On vide toutes les variables de session
$_SESSION = [];

// On détruit le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// On détruit la session
session_destroy();

// Redirection vers la page d'accueil
header('Location: ' . $BASE_URL . '/index.php');
exit;
?>