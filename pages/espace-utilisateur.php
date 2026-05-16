<?php 
include_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['utilisateur'])) {
    header('Location: ' . $BASE_URL . '/pages/connexion.php');
    exit;
}

$utilisateur = $_SESSION['utilisateur'];
$succes = '';
$erreur = '';

// Modification des infos personnelles
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

// Annulation commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'annuler') {
    $commande_id = intval($_POST['commande_id']);
    $stmt = $pdo->prepare("UPDATE commande SET statut='annulee' WHERE commande_id=:id AND utilisateur_id=:user_id AND statut='en attente'");
    $stmt->execute([':id' => $commande_id, ':user_id' => $utilisateur['utilisateur_id']]);
    $succes = "Commande annulée avec succès.";
}

// Récupération des commandes
$stmt = $pdo->prepare("SELECT c.*, m.titre as menu_titre FROM commande c JOIN menu m ON c.menu_id = m.menu_id WHERE c.utilisateur_id = :id ORDER BY c.date_commande DESC");
$stmt->execute([':id' => $utilisateur['utilisateur_id']]);
$commandes = $stmt->fetchAll();
?>

<main class="container my-5">
    <h2 class="mb-4">👤 Mon espace</h2>

    <?php if ($succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Infos personnelles -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
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

        <!-- Mes commandes -->
        <div class="col-md-8">
            <h5 class="mb-3">📦 Mes commandes</h5>
            <?php if (empty($commandes)): ?>
                <p class="text-muted">Vous n'avez pas encore passé de commande.</p>
            <?php else: ?>
                <?php foreach ($commandes as $commande): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?= htmlspecialchars($commande['numero_commande']) ?></h6>
                                <span class="badge bg-<?= $commande['statut'] === 'annulee' ? 'danger' : ($commande['statut'] === 'terminee' ? 'success' : 'warning text-dark') ?>">
                                    <?= htmlspecialchars($commande['statut']) ?>
                                </span>
                            </div>
                            <hr>
                            <p class="mb-1"><strong>Menu :</strong> <?= htmlspecialchars($commande['menu_titre']) ?></p>
                            <p class="mb-1"><strong>Date prestation :</strong> <?= htmlspecialchars($commande['date_prestation']) ?></p>
                            <p class="mb-1"><strong>Adresse :</strong> <?= htmlspecialchars($commande['adresse_livraison']) ?></p>
                            <p class="mb-1"><strong>Nombre de personnes :</strong> <?= $commande['nombre_personne'] ?></p>
                            <p class="mb-2"><strong>Total :</strong> <?= number_format($commande['prix_total'], 2) ?> €</p>

                            <?php if ($commande['statut'] === 'en attente'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="annuler">
                                    <input type="hidden" name="commande_id" value="<?= $commande['commande_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer l\'annulation ?')">Annuler la commande</button>
                                </form>
                            <?php endif; ?>

                            <?php if (in_array($commande['statut'], ['accepte', 'en preparation', 'en cours de livraison', 'livre', 'terminee'])): ?>
                                <a href="<?= $BASE_URL ?>/pages/suivi-commande.php?id=<?= $commande['commande_id'] ?>" class="btn btn-sm btn-outline-warning">Suivre ma commande</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>