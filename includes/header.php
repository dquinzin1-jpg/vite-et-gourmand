<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Détection automatique du préfixe d'URL selon l'environnement
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'alwaysdata.net') !== false) {
    $BASE_URL = '';
} else {
    $BASE_URL = '/vite-et-gourmand';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite &amp; Gourmand - Traiteur à Bordeaux</title>
    <meta name="description" content="Vite & Gourmand, traiteur à Bordeaux depuis 25 ans. Prestations sur mesure pour mariages, séminaires et événements privés.">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts : Playfair Display + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Notre CSS personnalisé -->
    <link rel="stylesheet" href="<?= $BASE_URL ?>/assets/css/style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $BASE_URL ?>/index.php">
                Vite <span>&amp;</span> Gourmand
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/pages/menus.php">Nos menus</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/pages/contact.php">Contact</a></li>
                    
                    <?php if (isset($_SESSION['utilisateur'])): ?>
                        <?php if ($_SESSION['utilisateur']['role'] === 'administrateur'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/pages/espace-admin.php">Espace admin</a></li>
                        <?php elseif ($_SESSION['utilisateur']['role'] === 'employe'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/pages/espace-employe.php">Espace employé</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/pages/espace-utilisateur.php">Mon espace</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link deconnexion" href="<?= $BASE_URL ?>/pages/deconnexion.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/pages/connexion.php">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/pages/inscription.php">S'inscrire</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
