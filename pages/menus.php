<?php 
include_once __DIR__ . '/../includes/header.php';

// Récupération de tous les menus
$menus = [];
try {
    $stmt = $pdo->query("SELECT * FROM menu ORDER BY menu_id");
    $menus = $stmt->fetchAll();
} catch (Exception $e) {
    // Si la requête échoue, on continue avec un tableau vide
}

// Photos associées aux menus (par défaut, on alterne)
$photos_menus = [
    'menu-mariage.jpg',
    'menu-entreprise.jpg',
    'menu-anniversaire.jpg',
    'hero-accueil.jpg'
];
?>

<!-- ==========================================================================
     HERO PAGE MENUS
========================================================================== -->
<section class="hero-section" style="height: 50vh; min-height: 350px; background-image: linear-gradient(rgba(26, 22, 18, 0.6), rgba(26, 22, 18, 0.8)), url('<?= $BASE_URL ?>/assets/images/menu-entreprise.jpg');">
    <div class="hero-content">
        <h1 style="font-size: clamp(2rem, 4vw, 3.5rem);">Nos <span class="accent">menus</span></h1>
        <div class="hero-separator"></div>
        <p class="hero-subtitle" style="font-size: 1.1rem;">
            Découvrez nos créations culinaires, élaborées avec passion par notre équipe de chefs.
        </p>
    </div>
</section>

<!-- ==========================================================================
     LISTE DES MENUS
========================================================================== -->
<section class="section-padding section-cream">
    <div class="container">
        <div class="section-title">
            <h2>Une carte d'exception</h2>
            <p class="subtitle">Chaque menu est pensé pour s'adapter à votre événement, vos goûts et votre budget. Cliquez pour découvrir le détail.</p>
        </div>

        <?php if (empty($menus)): ?>
            <div class="text-center mt-5">
                <p class="text-muted">Aucun menu disponible pour le moment. Revenez bientôt !</p>
            </div>
        <?php else: ?>
            <div class="row mt-5">
                <?php foreach ($menus as $index => $menu): 
                    $photo = $photos_menus[$index % count($photos_menus)];
                ?>
                    <div class="col-lg-6 mb-4">
                        <div class="menu-detail-card">
                            <div class="menu-detail-image">
                                <img src="<?= $BASE_URL ?>/assets/images/<?= $photo ?>" alt="<?= htmlspecialchars($menu['titre']) ?>">
                                <div class="menu-detail-price">
                                    <?= number_format($menu['prix'], 2) ?> €
                                </div>
                            </div>
                            <div class="menu-detail-content">
                                <h3><?= htmlspecialchars($menu['titre']) ?></h3>
                                <p><?= htmlspecialchars($menu['description']) ?></p>
                                <a href="<?= $BASE_URL ?>/pages/menu-detail.php?id=<?= $menu['menu_id'] ?>" class="btn-primary-custom">Voir le détail</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ==========================================================================
     SECTION CTA
========================================================================== -->
<section class="section-padding section-dark" style="background-image: linear-gradient(rgba(26, 22, 18, 0.92), rgba(26, 22, 18, 0.92)), url('<?= $BASE_URL ?>/assets/images/wine-accent.jpg'); background-size: cover; background-position: center;">
    <div class="container text-center">
        <h2>Une demande particulière ?</h2>
        <p class="lead mt-3 mb-4" style="color: var(--color-cream); max-width: 700px; margin: 0 auto 2rem;">
            Nous adaptons nos menus à vos contraintes : allergies, régime végétarien, exigences culturelles. Contactez-nous pour un menu sur mesure.
        </p>
        <a href="<?= $BASE_URL ?>/pages/contact.php" class="btn-gold">Demander un devis</a>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
