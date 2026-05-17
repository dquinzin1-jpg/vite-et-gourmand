<?php 
include_once __DIR__ . '/includes/header.php';

// Récupération des avis validés (s'il y en a)
$avis_valides = [];
try {
    $stmt = $pdo->query("SELECT a.*, u.prenom, u.nom FROM avis a JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id WHERE a.statut = 'valide' ORDER BY a.date_avis DESC LIMIT 3");
    $avis_valides = $stmt->fetchAll();
} catch (Exception $e) {
    // Si la requête échoue, on continue sans avis
}
?>

<!-- ==========================================================================
     HERO SECTION
========================================================================== -->
<section class="hero-section">
    <div class="hero-content">
        <h1>Bienvenue chez <span class="accent">Vite &amp; Gourmand</span></h1>
        <div class="hero-separator"></div>
        <p class="hero-subtitle">
            Depuis 25 ans à Bordeaux, Julie et José vous proposent des prestations culinaires sur mesure pour sublimer tous vos événements.
        </p>
        <a href="<?= $BASE_URL ?>/pages/menus.php" class="btn-gold">Découvrir nos menus</a>
    </div>
</section>

<!-- ==========================================================================
     SECTION POINTS FORTS
========================================================================== -->
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Notre engagement</h2>
            <p class="subtitle">Une équipe passionnée à votre service pour faire de chaque événement un moment d'exception</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="icon">🏆</div>
                <h3>25 ans d'expérience</h3>
                <p>Une expertise reconnue dans toute la région bordelaise, fruit d'années de passion et de savoir-faire culinaire.</p>
            </div>
            
            <div class="feature-card">
                <div class="icon">🍽️</div>
                <h3>Menus personnalisés</h3>
                <p>Des menus adaptés à tous les goûts et toutes les occasions, élaborés avec des produits frais et de saison.</p>
            </div>
            
            <div class="feature-card">
                <div class="icon">🚚</div>
                <h3>Livraison à domicile</h3>
                <p>Nous nous déplaçons chez vous partout en Gironde, pour vous offrir une prestation clé en main.</p>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     SECTION À PROPOS
========================================================================== -->
<section class="about-section">
    <div class="container">
        <div class="about-grid">
            <div class="about-image">
                <img src="<?= $BASE_URL ?>/assets/images/chef-cuisine.jpg" alt="Notre chef en cuisine">
            </div>
            
            <div class="about-content">
                <h2>Notre savoir-faire</h2>
                <p class="lead">
                    "La gastronomie est un art qui se transmet et se partage. Chaque plat est une histoire que nous écrivons avec passion."
                </p>
                <p>
                    Julie et José ont fondé Vite &amp; Gourmand il y a 25 ans avec une vision claire : démocratiser la haute gastronomie en l'apportant directement chez vous. Aujourd'hui, notre équipe de chefs passionnés perpétue cette tradition d'excellence.
                </p>
                <p>
                    Du choix méticuleux des producteurs locaux à la présentation soignée de chaque plat, nous mettons un point d'honneur à offrir une expérience culinaire mémorable.
                </p>
                
                <div class="about-stats">
                    <div class="stat-item">
                        <span class="stat-number">25</span>
                        <span class="stat-label">Années d'expérience</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Événements par an</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">Satisfaction client</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     SECTION PRESTATIONS
========================================================================== -->
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Nos prestations</h2>
            <p class="subtitle">Que ce soit pour un mariage, un séminaire d'entreprise ou un anniversaire, nous adaptons nos menus à votre événement</p>
        </div>
        
        <div class="prestations-grid">
            <a href="<?= $BASE_URL ?>/pages/menus.php" class="prestation-card">
                <img src="<?= $BASE_URL ?>/assets/images/menu-mariage.jpg" alt="Menu mariage">
                <div class="prestation-overlay">
                    <span class="prestation-tag">Cérémonies</span>
                    <h3>Mariages</h3>
                    <p>Cocktails, buffets et menus gastronomiques pour célébrer votre union en toute élégance.</p>
                </div>
            </a>
            
            <a href="<?= $BASE_URL ?>/pages/menus.php" class="prestation-card">
                <img src="<?= $BASE_URL ?>/assets/images/menu-entreprise.jpg" alt="Menu entreprise">
                <div class="prestation-overlay">
                    <span class="prestation-tag">Professionnel</span>
                    <h3>Entreprises</h3>
                    <p>Séminaires, cocktails d'affaires, repas d'équipe : des prestations adaptées au monde professionnel.</p>
                </div>
            </a>
            
            <a href="<?= $BASE_URL ?>/pages/menus.php" class="prestation-card">
                <img src="<?= $BASE_URL ?>/assets/images/menu-anniversaire.jpg" alt="Menu anniversaire">
                <div class="prestation-overlay">
                    <span class="prestation-tag">Célébrations</span>
                    <h3>Anniversaires</h3>
                    <p>Gâteaux personnalisés, sweet tables et buffets festifs pour marquer vos moments inoubliables.</p>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- ==========================================================================
     SECTION TÉMOIGNAGES
========================================================================== -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-title">
            <h2>L'avis de nos clients</h2>
            <p class="subtitle">Ils nous ont fait confiance pour leurs événements et nous les remercions pour leurs retours.</p>
        </div>
        
        <div class="row mt-5">
            <?php if (empty($avis_valides)): ?>
                <!-- Témoignages d'exemple si aucun avis en BDD -->
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">★★★★★</div>
                        <p class="testimonial-text">"Une équipe à l'écoute, des plats raffinés et une présentation impeccable. Notre mariage a été sublimé !"</p>
                        <p class="testimonial-author">— Sophie &amp; Marc</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">★★★★★</div>
                        <p class="testimonial-text">"Service impeccable pour notre séminaire d'entreprise. Tous nos collaborateurs ont été conquis par les saveurs."</p>
                        <p class="testimonial-author">— Laurent D., DRH</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">★★★★★</div>
                        <p class="testimonial-text">"Pour les 60 ans de mon père, c'était parfait. Les invités en parlent encore plusieurs mois après !"</p>
                        <p class="testimonial-author">— Marie L.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($avis_valides as $avis): ?>
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-stars"><?= str_repeat('★', $avis['note']) ?></div>
                            <p class="testimonial-text">"<?= htmlspecialchars($avis['description']) ?>"</p>
                            <p class="testimonial-author">— <?= htmlspecialchars($avis['prenom']) ?> <?= htmlspecialchars(substr($avis['nom'], 0, 1)) ?>.</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ==========================================================================
     SECTION CTA FINALE
========================================================================== -->
<section class="section-padding section-cream">
    <div class="container text-center">
        <h2>Prêt à organiser votre événement ?</h2>
        <p class="lead mt-3 mb-4" style="color: var(--color-gray); max-width: 700px; margin-left: auto; margin-right: auto;">
            Contactez-nous pour discuter de votre projet et recevoir un devis personnalisé. Notre équipe se fera un plaisir de vous accompagner.
        </p>
        <a href="<?= $BASE_URL ?>/pages/contact.php" class="btn-primary-custom">Nous contacter</a>
    </div>
</section>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
