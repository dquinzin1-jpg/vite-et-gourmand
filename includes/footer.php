</body>
<footer class="bg-dark text-white mt-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>🕐 Nos horaires</h5>
                <?php
                $stmt = $pdo->query("SELECT * FROM horaire");
                $horaires = $stmt->fetchAll();
                foreach ($horaires as $horaire): ?>
                    <p class="mb-0">
                        <strong><?= htmlspecialchars($horaire['jour']) ?></strong> :
                        <?= $horaire['heure_ouverture'] ? htmlspecialchars($horaire['heure_ouverture']) . ' - ' . htmlspecialchars($horaire['heure_fermeture']) : 'Fermé' ?>
                    </p>
                <?php endforeach; ?>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?= $BASE_URL ?>/pages/mentions-legales.php" class="text-white me-3">Mentions légales</a>
                <a href="<?= $BASE_URL ?>/pages/cgv.php" class="text-white">CGV</a>
                <p class="mt-2 mb-0">© <?= date('Y') ?> Vite & Gourmand - Tous droits réservés</p>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $BASE_URL ?>/assets/js/script.js"></script>
</html>