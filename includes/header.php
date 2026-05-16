<?php
session_start();
include_once __DIR__ . '/../config/db.php';

// Détection automatique du préfixe d'URL selon l'environnement
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'alwaysdata.net') !== false) {
    // En ligne (AlwaysData) - pas de préfixe
    $BASE_URL = '';
} else {
    // En local (XAMPP) - préfixe avec le dossier du projet
    $BASE_URL = '/vite-et-gourmand';
}

// Détermine le lien "Mon espace" selon le rôle de l'utilisateur connecté
$lien_espace = $BASE_URL . '/pages/espace-utilisateur.php';
if (isset($_SESSION['utilisateur']['role'])) {
    if ($_SESSION['utilisateur']['role'] === 'administrateur') {
        $lien_espace = $BASE_URL . '/pages/espace-admin.php';
    } elseif ($_SESSION['utilisateur']['role'] === 'employe') {
        $lien_espace = $BASE_URL . '/pages/espace-employe.php';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite & Gourmand</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= $BASE_URL ?>/index.php">🍽️ Vite & Gourmand</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/pages/menus.php">Nos menus</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/pages/contact.php">Contact</a></li>
                <?php if (isset($_SESSION['utilisateur'])): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $lien_espace ?>">Mon espace</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="<?= $BASE_URL ?>/pages/deconnexion.php">Déconnexion</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/pages/connexion.php">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>