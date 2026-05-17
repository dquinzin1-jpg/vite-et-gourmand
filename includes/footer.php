</body>
<footer class="footer-custom">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>Vite &amp; Gourmand</h5>
                <p>Traiteur d'exception à Bordeaux depuis 25 ans. Nous sublimons vos événements avec une cuisine raffinée et un service sur mesure.</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <h5>🕐 Nos horaires</h5>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM horaire");
                    $horaires = $stmt->fetchAll();
                    foreach ($horaires as $horaire): ?>
                        <p class="mb-1">
                            <strong><?= htmlspecialchars($horaire['jour']) ?></strong> :
                            <?= $horaire['heure_ouverture'] ? htmlspecialchars($horaire['heure_ouverture']) . ' - ' . htmlspecialchars($horaire['heure_fermeture']) : 'Fermé' ?>
                        </p>
                    <?php endforeach;
                } catch (Exception $e) {
                    echo '<p>Horaires non disponibles</p>';
                }
                ?>
            </div>
            
            <div class="col-md-4 mb-4">
                <h5>📍 Nous contacter</h5>
                <p>📌 Bordeaux, Gironde<br>
                📞 05 56 XX XX XX<br>
                ✉️ contact@vite-et-gourmand.fr</p>
                <p class="mt-3">
                    <a href="<?= $BASE_URL ?>/pages/contact.php">Formulaire de contact →</a>
                </p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="legal-links mb-2">
                <a href="<?= $BASE_URL ?>/pages/mentions-legales.php">Mentions légales</a>
                |
                <a href="<?= $BASE_URL ?>/pages/cgv.php">CGV</a>
            </div>
            <p class="mb-0">© <?= date('Y') ?> Vite &amp; Gourmand - Tous droits réservés</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $BASE_URL ?>/assets/js/script.js"></script>

<!-- Animation au scroll -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
});
</script>
</body>
</html>
