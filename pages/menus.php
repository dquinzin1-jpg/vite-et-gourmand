<?php 
include_once __DIR__ . '/../includes/header.php';

// ============================================================
// RÉCUPÉRATION DES FILTRES (depuis le formulaire GET)
// ============================================================
$filtre_prix_min      = isset($_GET['prix_min']) && $_GET['prix_min'] !== '' ? floatval($_GET['prix_min']) : null;
$filtre_prix_max      = isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? floatval($_GET['prix_max']) : null;
$filtre_theme_id      = isset($_GET['theme_id']) && $_GET['theme_id'] !== '' ? intval($_GET['theme_id']) : null;
$filtre_regime_id     = isset($_GET['regime_id']) && $_GET['regime_id'] !== '' ? intval($_GET['regime_id']) : null;
$filtre_nb_personnes  = isset($_GET['nb_personnes']) && $_GET['nb_personnes'] !== '' ? intval($_GET['nb_personnes']) : null;

// ============================================================
// RÉCUPÉRATION DES THÈMES ET RÉGIMES (pour les selects du formulaire)
// ============================================================
$themes = [];
$regimes = [];
try {
    $themes = $pdo->query("SELECT * FROM theme ORDER BY libelle")->fetchAll();
    $regimes = $pdo->query("SELECT * FROM regime ORDER BY libelle")->fetchAll();
} catch (Exception $e) {
    // Erreur silencieuse, on continue
}

// ============================================================
// CONSTRUCTION DE LA REQUÊTE SQL AVEC FILTRES
// ============================================================
$sql = "SELECT m.*, t.libelle AS theme_libelle, r.libelle AS regime_libelle 
        FROM menu m 
        JOIN theme t ON m.theme_id = t.theme_id 
        JOIN regime r ON m.regime_id = r.regime_id 
        WHERE 1=1";
$params = [];

if ($filtre_prix_min !== null) {
    $sql .= " AND m.prix >= :prix_min";
    $params[':prix_min'] = $filtre_prix_min;
}

if ($filtre_prix_max !== null) {
    $sql .= " AND m.prix <= :prix_max";
    $params[':prix_max'] = $filtre_prix_max;
}

if ($filtre_theme_id !== null) {
    $sql .= " AND m.theme_id = :theme_id";
    $params[':theme_id'] = $filtre_theme_id;
}

if ($filtre_regime_id !== null) {
    $sql .= " AND m.regime_id = :regime_id";
    $params[':regime_id'] = $filtre_regime_id;
}

if ($filtre_nb_personnes !== null) {
    $sql .= " AND m.nombre_personne_minimum <= :nb_personnes";
    $params[':nb_personnes'] = $filtre_nb_personnes;
}

$sql .= " ORDER BY m.menu_id";

// ============================================================
// EXÉCUTION DE LA REQUÊTE
// ============================================================
$menus = [];
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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

// Vérifier si au moins un filtre est actif (pour affichage)
$filtres_actifs = ($filtre_prix_min !== null || $filtre_prix_max !== null || $filtre_theme_id !== null || $filtre_regime_id !== null || $filtre_nb_personnes !== null);
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
     FORMULAIRE DE FILTRES
========================================================================== -->
<section class="section-padding section-cream" style="padding-bottom: 1rem;">
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3" style="color: var(--color-bordeaux);">🔍 Rechercher un menu</h5>
                <form method="GET" action="<?= $BASE_URL ?>/pages/menus.php">
                    <div class="row g-3">
                        <!-- Prix minimum -->
                        <div class="col-md-4 col-lg-2">
                            <label class="form-label">Prix minimum (€)</label>
                            <input type="number" name="prix_min" class="form-control" min="0" step="0.01" 
                                   value="<?= $filtre_prix_min !== null ? htmlspecialchars($filtre_prix_min) : '' ?>" 
                                   placeholder="0">
                        </div>
                        <!-- Prix maximum -->
                        <div class="col-md-4 col-lg-2">
                            <label class="form-label">Prix maximum (€)</label>
                            <input type="number" name="prix_max" class="form-control" min="0" step="0.01" 
                                   value="<?= $filtre_prix_max !== null ? htmlspecialchars($filtre_prix_max) : '' ?>" 
                                   placeholder="1000">
                        </div>
                        <!-- Thème -->
                        <div class="col-md-4 col-lg-2">
                            <label class="form-label">Thème</label>
                            <select name="theme_id" class="form-select">
                                <option value="">Tous</option>
                                <?php foreach ($themes as $t): ?>
                                    <option value="<?= $t['theme_id'] ?>" <?= $filtre_theme_id === intval($t['theme_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['libelle']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Régime -->
                        <div class="col-md-4 col-lg-2">
                            <label class="form-label">Régime</label>
                            <select name="regime_id" class="form-select">
                                <option value="">Tous</option>
                                <?php foreach ($regimes as $r): ?>
                                    <option value="<?= $r['regime_id'] ?>" <?= $filtre_regime_id === intval($r['regime_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['libelle']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Nombre de personnes -->
                        <div class="col-md-4 col-lg-2">
                            <label class="form-label">Nb personnes</label>
                            <input type="number" name="nb_personnes" class="form-control" min="1" 
                                   value="<?= $filtre_nb_personnes !== null ? htmlspecialchars($filtre_nb_personnes) : '' ?>" 
                                   placeholder="ex: 20">
                        </div>
                        <!-- Boutons -->
                        <div class="col-md-4 col-lg-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-warning flex-grow-1">Filtrer</button>
                            <a href="<?= $BASE_URL ?>/pages/menus.php" class="btn btn-outline-secondary" title="Réinitialiser">↻</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     LISTE DES MENUS
========================================================================== -->
<section class="section-padding section-cream" style="padding-top: 1rem;">
    <div class="container">
        <div class="section-title">
            <h2>Une carte d'exception</h2>
            <p class="subtitle">
                <?php if ($filtres_actifs): ?>
                    <?= count($menus) ?> menu<?= count($menus) > 1 ? 's' : '' ?> correspondant<?= count($menus) > 1 ? 's' : '' ?> à votre recherche.
                <?php else: ?>
                    Chaque menu est pensé pour s'adapter à votre événement, vos goûts et votre budget. Cliquez pour découvrir le détail.
                <?php endif; ?>
            </p>
        </div>

        <?php if (empty($menus)): ?>
            <div class="text-center mt-5">
                <?php if ($filtres_actifs): ?>
                    <p class="text-muted">Aucun menu ne correspond à vos critères de recherche.</p>
                    <a href="<?= $BASE_URL ?>/pages/menus.php" class="btn-primary-custom">Voir tous les menus</a>
                <?php else: ?>
                    <p class="text-muted">Aucun menu disponible pour le moment. Revenez bientôt !</p>
                <?php endif; ?>
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
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark"><?= htmlspecialchars($menu['theme_libelle']) ?></span>
                                    <span class="badge bg-success"><?= htmlspecialchars($menu['regime_libelle']) ?></span>
                                    <span class="badge bg-secondary">À partir de <?= $menu['nombre_personne_minimum'] ?> pers.</span>
                                </div>
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