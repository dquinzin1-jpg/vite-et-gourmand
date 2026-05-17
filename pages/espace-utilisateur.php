<?php 
include_once __DIR__ . '/../includes/header.php';

// Redirection si non connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ' . $BASE_URL . '/pages/connexion.php');
    exit;
}

$utilisateur = $_SESSION['utilisateur'];
$succes = '';
$erreur = '';

// ============================================================
// TRAITEMENT : Modification des infos personnelles
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifier_profil') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse_postale']);

    $stmt = $pdo->prepare("UPDATE utilisateur SET nom=:nom, prenom=:prenom, telephone=:tel, adresse_postale=:adresse WHERE utilisateur_id=:id");
    $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':tel' => $telephone, ':adresse' => $adresse, ':id' => $utilisateur['utilisateur_id']]);

    $_SESSION['utilisateur']['nom'] = $nom;
    $_SESSION['utilisateur']['prenom'] = $prenom;
    $_SESSION['utilisateur']['telephone'] = $telephone;
    $_SESSION['utilisateur']['adresse_postale'] = $adresse;
    $utilisateur = $_SESSION['utilisateur'];
    $succes = "Vos informations ont été mises à jour.";
}

// ============================================================
// TRAITEMENT : Annulation d'une commande
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'annuler') {
    $commande_id = intval($_POST['commande_id']);
    $stmt = $pdo->prepare("UPDATE commande SET statut='annulee' WHERE commande_id=:id AND utilisateur_id=:user_id AND statut='en attente'");
    $stmt->execute([':id' => $commande_id, ':user_id' => $utilisateur['utilisateur_id']]);
    $succes = "Commande annulée avec succès.";
}

// ============================================================
// TRAITEMENT : Soumission d'un avis sur une commande terminée
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'laisser_avis') {
    $commande_id = intval($_POST['commande_id']);
    $note = intval($_POST['note']);
    $description = trim($_POST['description']);

    // Validations
    if ($note < 1 || $note > 5) {
        $erreur = "La note doit être comprise entre 1 et 5.";
    } elseif (strlen($description) > 255) {
        $erreur = "Le commentaire ne doit pas dépasser 255 caractères.";
    } elseif (empty($description)) {
        $erreur = "Le commentaire ne peut pas être vide.";
    } else {
        // Vérifier que la commande appartient bien à l'utilisateur ET qu'elle est terminée
        $stmt = $pdo->prepare("SELECT statut FROM commande WHERE commande_id = :id AND utilisateur_id = :user_id");
        $stmt->execute([':id' => $commande_id, ':user_id' => $utilisateur['utilisateur_id']]);
        $cmd = $stmt->fetch();

        if (!$cmd || $cmd['statut'] !== 'terminee') {
            $erreur = "Vous ne pouvez laisser un avis que sur une commande terminée.";
        } else {
            // Vérifier qu'aucun avis n'existe déjà pour cette commande
            $stmt = $pdo->prepare("SELECT avis_id FROM avis WHERE commande_id = :id");
            $stmt->execute([':id' => $commande_id]);
            if ($stmt->fetch()) {
                $erreur = "Un avis a déjà été laissé pour cette commande.";
            } else {
                // Insertion de l'avis (statut "en attente" pour modération employé)
                $stmt = $pdo->prepare("INSERT INTO avis (note, description, statut, utilisateur_id, commande_id) VALUES (:note, :desc, 'en attente', :user_id, :cmd_id)");
                $stmt->execute([
                    ':note' => $note,
                    ':desc' => $description,
                    ':user_id' => $utilisateur['utilisateur_id'],
                    ':cmd_id' => $commande_id
                ]);
                $succes = "Merci pour votre avis ! Il sera publié après modération par notre équipe.";
            }
        }
    }
}

// ============================================================
// RÉCUPÉRATION DES DONNÉES
// ============================================================

// Récupération des commandes de l'utilisateur
$stmt = $pdo->prepare("SELECT c.*, m.titre as menu_titre FROM commande c JOIN menu m ON c.menu_id = m.menu_id WHERE c.utilisateur_id = :id ORDER BY c.date_commande DESC");
$stmt->execute([':id' => $utilisateur['utilisateur_id']]);
$commandes = $stmt->fetchAll();

// Récupération des avis laissés par l'utilisateur (indexés par commande_id)
$stmt = $pdo->prepare("SELECT * FROM avis WHERE utilisateur_id = :id");
$stmt->execute([':id' => $utilisateur['utilisateur_id']]);
$avis_brut = $stmt->fetchAll();

$avis_par_commande = [];
foreach ($avis_brut as $a) {
    $avis_par_commande[$a['commande_id']] = $a;
}
?>

<section class="dashboard-section">
    <div class="container">
        <h2>👤 Bonjour <?= htmlspecialchars($utilisateur['prenom']) ?> !</h2>
        <p style="color: var(--color-gray); margin-bottom: 2rem;">Gérez votre compte et suivez vos commandes</p>

        <?php if ($succes): ?>
            <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
        <?php endif; ?>

        <?php if ($erreur): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- ============================================================ -->
            <!-- COLONNE GAUCHE : Infos personnelles                          -->
            <!-- ============================================================ -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Mes informations</h5>
                        <form method="POST">
                            <input type="hidden" name="action" value="modifier_profil">
                            <div class="mb-2">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($utilisateur['telephone']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Adresse postale</label>
                                <input type="text" name="adresse_postale" class="form-control" value="<?= htmlspecialchars($utilisateur['adresse_postale']) ?>" required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- COLONNE DROITE : Mes commandes                               -->
            <!-- ============================================================ -->
            <div class="col-md-8">
                <h5 class="mb-3" style="color: var(--color-bordeaux);">📦 Mes commandes</h5>

                <?php if (empty($commandes)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <p class="text-muted mb-3">Vous n'avez pas encore passé de commande.</p>
                            <a href="<?= $BASE_URL ?>/pages/menus.php" class="btn-primary-custom">Découvrir nos menus</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($commandes as $commande): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <!-- En-tête commande -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0" style="color: var(--color-bordeaux);"><?= htmlspecialchars($commande['numero_commande']) ?></h6>
                                    <span class="badge bg-<?= $commande['statut'] === 'annulee' ? 'danger' : ($commande['statut'] === 'terminee' ? 'success' : 'warning text-dark') ?>">
                                        <?= htmlspecialchars($commande['statut']) ?>
                                    </span>
                                </div>
                                <hr>

                                <!-- Détails commande -->
                                <p class="mb-1"><strong>Menu :</strong> <?= htmlspecialchars($commande['menu_titre']) ?></p>
                                <p class="mb-1"><strong>Date prestation :</strong> <?= htmlspecialchars($commande['date_prestation']) ?></p>
                                <p class="mb-1"><strong>Adresse :</strong> <?= htmlspecialchars($commande['adresse_livraison']) ?></p>
                                <p class="mb-1"><strong>Nombre de personnes :</strong> <?= $commande['nombre_personne'] ?></p>
                                <p class="mb-2"><strong>Total :</strong> <?= number_format($commande['prix_total'], 2) ?> €</p>

                                <!-- Bouton annulation (seulement si "en attente") -->
                                <?php if ($commande['statut'] === 'en attente'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="annuler">
                                        <input type="hidden" name="commande_id" value="<?= $commande['commande_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer l\'annulation ?')">Annuler la commande</button>
                                    </form>
                                <?php endif; ?>

                                <!-- Bouton suivi (si en cours) -->
                                <?php if (in_array($commande['statut'], ['accepte', 'en preparation', 'en cours de livraison', 'livre', 'terminee'])): ?>
                                    <a href="<?= $BASE_URL ?>/pages/suivi-commande.php?id=<?= $commande['commande_id'] ?>" class="btn btn-sm btn-outline-warning">Suivre ma commande</a>
                                <?php endif; ?>

                                <!-- ============================================ -->
                                <!-- BLOC AVIS (uniquement si statut = "terminee") -->
                                <!-- ============================================ -->
                                <?php if ($commande['statut'] === 'terminee'): ?>
                                    <hr class="mt-3">
                                    <?php if (isset($avis_par_commande[$commande['commande_id']])): ?>
                                        <!-- L'utilisateur a déjà laissé un avis -->
                                        <?php $mon_avis = $avis_par_commande[$commande['commande_id']]; ?>
                                        <div class="mt-2">
                                            <h6 style="color: var(--color-bordeaux);">⭐ Votre avis</h6>
                                            <p class="mb-1"><?= str_repeat('⭐', $mon_avis['note']) ?></p>
                                            <p class="mb-1"><em>« <?= htmlspecialchars($mon_avis['description']) ?> »</em></p>
                                            <p class="mb-0">
                                                <small>
                                                    Statut :
                                                    <?php if ($mon_avis['statut'] === 'valide'): ?>
                                                        <span class="badge bg-success">✅ Publié sur le site</span>
                                                    <?php elseif ($mon_avis['statut'] === 'refuse'): ?>
                                                        <span class="badge bg-danger">❌ Refusé par la modération</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">⏳ En attente de modération</span>
                                                    <?php endif; ?>
                                                </small>
                                            </p>
                                        </div>
                                    <?php else: ?>
                                        <!-- L'utilisateur n'a pas encore laissé d'avis -->
                                        <div class="mt-2">
                                            <h6 style="color: var(--color-bordeaux);">⭐ Laissez votre avis</h6>
                                            <p class="text-muted small mb-2">Partagez votre expérience avec les futurs clients.</p>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="laisser_avis">
                                                <input type="hidden" name="commande_id" value="<?= $commande['commande_id'] ?>">
                                                <div class="mb-2">
                                                    <label class="form-label">Note (1 à 5 étoiles)</label>
                                                    <select name="note" class="form-select form-select-sm" required>
                                                        <option value="">Choisir une note...</option>
                                                        <option value="5">⭐⭐⭐⭐⭐ — Excellent</option>
                                                        <option value="4">⭐⭐⭐⭐ — Très bien</option>
                                                        <option value="3">⭐⭐⭐ — Bien</option>
                                                        <option value="2">⭐⭐ — Décevant</option>
                                                        <option value="1">⭐ — Mauvais</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Votre commentaire</label>
                                                    <textarea name="description" class="form-control form-control-sm" rows="2" maxlength="255" placeholder="Partagez votre expérience..." required></textarea>
                                                    <small class="text-muted">Maximum 255 caractères</small>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-warning">Publier mon avis</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>