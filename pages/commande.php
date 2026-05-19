<?php 
include_once '../includes/header.php';
require_once __DIR__ . '/../config/mongodb.php';

if (!isset($_SESSION['utilisateur'])) {
    header('Location: /pages/connexion.php');
    exit;
}

$utilisateur = $_SESSION['utilisateur'];
$menu_id = isset($_GET['menu_id']) ? intval($_GET['menu_id']) : null;
$succes = '';
$erreur = '';

// Récupération des menus
$menus = $pdo->query("SELECT * FROM menu WHERE quantite_restante > 0")->fetchAll();

// Récupération du menu sélectionné
$menu = null;
if ($menu_id) {
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = :id");
    $stmt->execute([':id' => $menu_id]);
    $menu = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id_post = intval($_POST['menu_id']);
    $nombre_personne = intval($_POST['nombre_personne']);
    $adresse_livraison = trim($_POST['adresse_livraison']);
    $date_prestation = $_POST['date_prestation'];
    $heure_livraison = $_POST['heure_livraison'];

    $stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = :id");
    $stmt->execute([':id' => $menu_id_post]);
    $menu_choisi = $stmt->fetch();

    if ($nombre_personne < $menu_choisi['nombre_personne_minimum']) {
        $erreur = "Le nombre minimum de personnes pour ce menu est de " . $menu_choisi['nombre_personne_minimum'] . ".";
    } else {
        // Calcul du prix
        $prix_menu = $menu_choisi['prix'];
        $difference = $nombre_personne - $menu_choisi['nombre_personne_minimum'];
        if ($difference >= 5) {
            $prix_menu = $prix_menu * 0.90;
        }
        $prix_menu_total = ($prix_menu / $menu_choisi['nombre_personne_minimum']) * $nombre_personne;

        // Calcul livraison
        $prix_livraison = 0;
        if (stripos($adresse_livraison, 'bordeaux') === false) {
            $prix_livraison = 5;
        }

        $prix_total = $prix_menu_total + $prix_livraison;
        $numero_commande = 'CMD-' . strtoupper(uniqid());

        // ===== TRANSACTION : création atomique de la commande =====
        try {
            $pdo->beginTransaction();

            // Re-vérification du stock avec verrou (anti-survente / race condition)
            $stmt_stock = $pdo->prepare("SELECT quantite_restante FROM menu WHERE menu_id = :id FOR UPDATE");
            $stmt_stock->execute([':id' => $menu_id_post]);
            $stock_actuel = $stmt_stock->fetchColumn();

            if ($stock_actuel <= 0) {
                $pdo->rollBack();
                $erreur = "Désolé, ce menu n'est plus disponible.";
            } else {
                // 1. INSERT commande
                $stmt = $pdo->prepare("INSERT INTO commande 
                    (numero_commande, date_commande, date_prestation, heure_livraison, adresse_livraison, nombre_personne, prix_menu, prix_livraison, prix_total, statut, utilisateur_id, menu_id)
                    VALUES (:numero, NOW(), :date_prestation, :heure, :adresse, :nb_pers, :prix_menu, :prix_liv, :prix_total, 'en attente', :user_id, :menu_id)");
                $stmt->execute([
                    ':numero' => $numero_commande,
                    ':date_prestation' => $date_prestation,
                    ':heure' => $heure_livraison,
                    ':adresse' => $adresse_livraison,
                    ':nb_pers' => $nombre_personne,
                    ':prix_menu' => $prix_menu_total,
                    ':prix_liv' => $prix_livraison,
                    ':prix_total' => $prix_total,
                    ':user_id' => $utilisateur['utilisateur_id'],
                    ':menu_id' => $menu_id_post
                ]);

                $commande_id = $pdo->lastInsertId();

                // 2. INSERT premier suivi (historique de la commande)
                $stmt_suivi = $pdo->prepare("INSERT INTO suivi_commande (commande_id, statut, date_modification) 
                    VALUES (:cmd_id, 'en attente', NOW())");
                $stmt_suivi->execute([':cmd_id' => $commande_id]);

                // 3. UPDATE stock (décrémente quantite_restante)
                $stmt_stock_update = $pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante - 1 WHERE menu_id = :id");
                $stmt_stock_update->execute([':id' => $menu_id_post]);

                // Validation de la transaction MariaDB
                $pdo->commit();

                // ===== STRATÉGIE B : insertion événement dans MongoDB =====
                // MongoDB sert de base analytique pour les stats admin (graphique commandes/menu).
                // Insertion APRÈS le commit MariaDB : si MongoDB échoue, la commande reste valide.
                try {
                    $manager = getMongoManager();
                    $bulk = new MongoDB\Driver\BulkWrite;
                    $bulk->insert([
                        'commande_id'     => (int) $commande_id,
                        'numero_commande' => $numero_commande,
                        'menu_id'         => $menu_id_post,
                        'menu_titre'      => $menu_choisi['titre'],
                        'utilisateur_id'  => (int) $utilisateur['utilisateur_id'],
                        'nombre_personne' => $nombre_personne,
                        'prix_total'      => (float) $prix_total,
                        'date_commande'   => new MongoDB\BSON\UTCDateTime(),
                        'statut'          => 'en attente',
                    ]);
                    $namespace = MONGODB_DATABASE . '.' . MONGODB_COLLECTION_COMMANDES;
                    $manager->executeBulkWrite($namespace, $bulk);
                } catch (Exception $e) {
                    // Échec MongoDB non bloquant : la commande MariaDB est déjà validée.
                    // En production, on loggerait l'erreur pour suivi et resync ultérieur.
                    // error_log('MongoDB sync failed for commande ' . $numero_commande . ': ' . $e->getMessage());
                }

                $succes = "✅ Commande $numero_commande confirmée ! Total : " . number_format($prix_total, 2) . " €";
            }
        } catch (PDOException $e) {
            // Annulation de toutes les opérations en cas d'erreur
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $erreur = "Une erreur est survenue lors de la création de la commande. Veuillez réessayer.";
            // En production, logger l'erreur côté serveur :
            // error_log('Erreur commande : ' . $e->getMessage());
        }
    }
}
?>

<main class="container my-5">
    <h2 class="mb-4 text-center">Passer une commande</h2>

    <?php if ($succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">

                <!-- Infos client -->
                <h5 class="mb-3">👤 Vos informations</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($utilisateur['nom']) ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($utilisateur['email']) ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" value="<?= htmlspecialchars($utilisateur['telephone']) ?>" readonly>
                    </div>
                </div>

                <!-- Infos prestation -->
                <h5 class="mb-3 mt-3">📍 Informations de livraison</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Adresse de livraison</label>
                        <input type="text" name="adresse_livraison" class="form-control" required>
                        <small class="text-muted">Hors Bordeaux : +5€ de frais de livraison</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Date de la prestation</label>
                        <input type="date" name="date_prestation" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Heure souhaitée</label>
                        <input type="time" name="heure_livraison" class="form-control" required>
                    </div>
                </div>

                <!-- Choix du menu -->
                <h5 class="mb-3 mt-3">🍽️ Menu choisi</h5>
                <div class="mb-3">
                    <label class="form-label">Sélectionner un menu</label>
                    <select name="menu_id" id="menu-select" class="form-select" required onchange="updateMinPersonnes(this)">
                        <option value="">-- Choisir un menu --</option>
                        <?php foreach ($menus as $m): ?>
                            <option value="<?= $m['menu_id'] ?>"
                                data-min="<?= $m['nombre_personne_minimum'] ?>"
                                data-prix="<?= $m['prix'] ?>"
                                <?= ($menu && $menu['menu_id'] == $m['menu_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['titre']) ?> — <?= number_format($m['prix'], 2) ?> € / <?= $m['nombre_personne_minimum'] ?> pers. min.
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Nombre de personnes -->
                <div class="mb-3">
                    <label class="form-label">Nombre de personnes</label>
                    <input type="number" name="nombre_personne" id="nb-personnes" class="form-control" min="1" required onchange="calculerPrix()">
                    <small class="text-muted" id="info-min"></small>
                </div>

                <!-- Récapitulatif prix -->
                <div class="alert alert-light border mt-3" id="recap-prix" style="display:none;">
                    <h6>💰 Récapitulatif du prix</h6>
                    <p class="mb-1">Prix menu : <strong id="prix-menu-affiche">-</strong></p>
                    <p class="mb-1">Frais de livraison : <strong id="prix-livraison-affiche">-</strong></p>
                    <p class="mb-0">Total : <strong id="prix-total-affiche" class="text-warning fs-5">-</strong></p>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-warning btn-lg">Confirmer la commande</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function updateMinPersonnes(select) {
    const option = select.options[select.selectedIndex];
    const min = option.dataset.min;
    if (min) {
        document.getElementById('nb-personnes').min = min;
        document.getElementById('info-min').textContent = 'Minimum : ' + min + ' personnes. Réduction 10% à partir de ' + (parseInt(min) + 5) + ' personnes.';
    }
    calculerPrix();
}

function calculerPrix() {
    const select = document.getElementById('menu-select');
    const option = select.options[select.selectedIndex];
    const nb = parseInt(document.getElementById('nb-personnes').value);
    const min = parseInt(option.dataset.min);
    const prixBase = parseFloat(option.dataset.prix);

    if (!nb || !min || !prixBase) return;

    let prixMenu = (prixBase / min) * nb;
    if (nb >= min + 5) prixMenu = prixMenu * 0.90;

    const adresse = document.querySelector('[name="adresse_livraison"]').value.toLowerCase();
    const livraison = adresse && !adresse.includes('bordeaux') ? 5 : 0;

    document.getElementById('recap-prix').style.display = 'block';
    document.getElementById('prix-menu-affiche').textContent = prixMenu.toFixed(2) + ' €';
    document.getElementById('prix-livraison-affiche').textContent = livraison.toFixed(2) + ' €';
    document.getElementById('prix-total-affiche').textContent = (prixMenu + livraison).toFixed(2) + ' €';
}
</script>

<?php include_once '../includes/footer.php'; ?>