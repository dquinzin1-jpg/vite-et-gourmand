<?php 
include_once __DIR__ . '/../includes/header.php';

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
// RÉCUPÉRATION DE TOUS LES MENUS (sans filtre - filtrage côté JS)
// ============================================================
$menus = [];
try {
    $sql = "SELECT m.*, t.libelle AS theme_libelle, r.libelle AS regime_libelle 
            FROM menu m 
            JOIN theme t ON m.theme_id = t.theme_id 
            JOIN regime r ON m.regime_id = r.regime_id 
            ORDER BY m.menu_id";
    $menus = $pdo->query($sql)->fetchAll();
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
     FORMULAIRE DE FILTRES (dynamiques, sans rechargement)
========================================================================== -->
<section class="section-padding section-cream" style="padding-bottom: 1rem;">
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3" style="color: var(--color-bordeaux);">🔍 Rechercher un menu</h5>
                <!-- Filtres JS pur : pas de submit, pas de rechargement -->
                <div class="row g-3">
                    <!-- Prix minimum -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label" for="filtre-prix-min">Prix minimum (€)</label>
                        <input type="number" id="filtre-prix-min" class="form-control filtre" min="0" step="0.01" placeholder="0">
                    </div>
                    <!-- Prix maximum -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label" for="filtre-prix-max">Prix maximum (€)</label>
                        <input type="number" id="filtre-prix-max" class="form-control filtre" min="0" step="0.01" placeholder="1000">
                    </div>
                    <!-- Thème -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label" for="filtre-theme">Thème</label>
                        <select id="filtre-theme" class="form-select filtre">
                            <option value="">Tous</option>
                            <?php foreach ($themes as $t): ?>
                                <option value="<?= $t['theme_id'] ?>"><?= htmlspecialchars($t['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Régime -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label" for="filtre-regime">Régime</label>
                        <select id="filtre-regime" class="form-select filtre">
                            <option value="">Tous</option>
                            <?php foreach ($regimes as $r): ?>
                                <option value="<?= $r['regime_id'] ?>"><?= htmlspecialchars($r['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Nombre de personnes -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label" for="filtre-nb-personnes">Nb personnes</label>
                        <input type="number" id="filtre-nb-personnes" class="form-control filtre" min="1" placeholder="ex: 20">
                    </div>
                    <!-- Bouton reset -->
                    <div class="col-md-4 col-lg-2 d-flex align-items-end">
                        <button type="button" id="btn-reset-filtres" class="btn btn-outline-secondary w-100" title="Réinitialiser tous les filtres">↻ Réinitialiser</button>
                    </div>
                </div>
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
            <p class="subtitle" id="texte-resultats">
                Chaque menu est pensé pour s'adapter à votre événement, vos goûts et votre budget. Cliquez pour découvrir le détail.
            </p>
        </div>

        <!-- Message "aucun résultat" (caché par défaut, affiché par JS) -->
        <div id="message-aucun-resultat" class="text-center mt-5" style="display: none;">
            <p class="text-muted">Aucun menu ne correspond à vos critères de recherche.</p>
            <button type="button" id="btn-reset-aucun-resultat" class="btn-primary-custom" style="border: none; cursor: pointer;">Voir tous les menus</button>
        </div>

        <?php if (empty($menus)): ?>
            <div class="text-center mt-5">
                <p class="text-muted">Aucun menu disponible pour le moment. Revenez bientôt !</p>
            </div>
        <?php else: ?>
            <div class="row mt-5" id="liste-menus">
                <?php foreach ($menus as $index => $menu): 
                    $photo = $photos_menus[$index % count($photos_menus)];
                ?>
                    <div class="col-lg-6 mb-4 carte-menu"
                         data-prix="<?= $menu['prix'] ?>"
                         data-theme="<?= $menu['theme_id'] ?>"
                         data-regime="<?= $menu['regime_id'] ?>"
                         data-min-personnes="<?= $menu['nombre_personne_minimum'] ?>">
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

<!-- ==========================================================================
     SCRIPT — FILTRES DYNAMIQUES SANS RECHARGEMENT
========================================================================== -->
<script>
(function() {
    'use strict';
    
    // Récupération des éléments DOM
    const inputPrixMin    = document.getElementById('filtre-prix-min');
    const inputPrixMax    = document.getElementById('filtre-prix-max');
    const selectTheme     = document.getElementById('filtre-theme');
    const selectRegime    = document.getElementById('filtre-regime');
    const inputNbPersonnes = document.getElementById('filtre-nb-personnes');
    const btnReset        = document.getElementById('btn-reset-filtres');
    const btnResetAucun   = document.getElementById('btn-reset-aucun-resultat');
    const cartes          = document.querySelectorAll('.carte-menu');
    const texteResultats  = document.getElementById('texte-resultats');
    const messageAucun    = document.getElementById('message-aucun-resultat');
    const listeMenus      = document.getElementById('liste-menus');
    
    // Texte par défaut (sans filtre actif)
    const TEXTE_DEFAUT = "Chaque menu est pensé pour s'adapter à votre événement, vos goûts et votre budget. Cliquez pour découvrir le détail.";
    
    /**
     * Filtre les cartes selon les valeurs des filtres actuels.
     * Affiche/cache chaque carte avec display: block/none.
     */
    function appliquerFiltres() {
        // Lecture des valeurs des filtres
        const prixMin     = inputPrixMin.value !== '' ? parseFloat(inputPrixMin.value) : null;
        const prixMax     = inputPrixMax.value !== '' ? parseFloat(inputPrixMax.value) : null;
        const themeId     = selectTheme.value;
        const regimeId    = selectRegime.value;
        const nbPersonnes = inputNbPersonnes.value !== '' ? parseInt(inputNbPersonnes.value, 10) : null;
        
        // Au moins un filtre est-il actif ?
        const filtresActifs = (prixMin !== null || prixMax !== null || themeId !== '' || regimeId !== '' || nbPersonnes !== null);
        
        // Parcours de chaque carte
        let nbVisibles = 0;
        cartes.forEach(function(carte) {
            const prixCarte    = parseFloat(carte.dataset.prix);
            const themeCarte   = carte.dataset.theme;
            const regimeCarte  = carte.dataset.regime;
            const minPersonnes = parseInt(carte.dataset.minPersonnes, 10);
            
            // Vérification de chaque critère (true = passe le filtre)
            let visible = true;
            
            if (prixMin !== null && prixCarte < prixMin) visible = false;
            if (prixMax !== null && prixCarte > prixMax) visible = false;
            if (themeId !== '' && themeCarte !== themeId) visible = false;
            if (regimeId !== '' && regimeCarte !== regimeId) visible = false;
            if (nbPersonnes !== null && minPersonnes > nbPersonnes) visible = false;
            
            // Affichage/masquage
            carte.style.display = visible ? '' : 'none';
            if (visible) nbVisibles++;
        });
        
        // Mise à jour du texte de résultats
        if (filtresActifs) {
            if (nbVisibles === 0) {
                texteResultats.textContent = '';
                messageAucun.style.display = 'block';
                if (listeMenus) listeMenus.style.display = 'none';
            } else {
                texteResultats.textContent = nbVisibles + ' menu' + (nbVisibles > 1 ? 's' : '') + ' correspondant' + (nbVisibles > 1 ? 's' : '') + ' à votre recherche.';
                messageAucun.style.display = 'none';
                if (listeMenus) listeMenus.style.display = '';
            }
        } else {
            texteResultats.textContent = TEXTE_DEFAUT;
            messageAucun.style.display = 'none';
            if (listeMenus) listeMenus.style.display = '';
        }
    }
    
    /**
     * Réinitialise tous les filtres et réaffiche toutes les cartes.
     */
    function reinitialiserFiltres() {
        inputPrixMin.value = '';
        inputPrixMax.value = '';
        selectTheme.value = '';
        selectRegime.value = '';
        inputNbPersonnes.value = '';
        appliquerFiltres();
    }
    
    // Écouteurs d'événements : filtrage instantané au changement
    [inputPrixMin, inputPrixMax, inputNbPersonnes].forEach(function(input) {
        input.addEventListener('input', appliquerFiltres);
    });
    [selectTheme, selectRegime].forEach(function(select) {
        select.addEventListener('change', appliquerFiltres);
    });
    
    // Boutons de réinitialisation
    if (btnReset) btnReset.addEventListener('click', reinitialiserFiltres);
    if (btnResetAucun) btnResetAucun.addEventListener('click', reinitialiserFiltres);
})();
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>