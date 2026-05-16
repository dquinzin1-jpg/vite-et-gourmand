<?php 
include_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['utilisateur']) || !in_array($_SESSION['utilisateur']['role'], ['employe', 'administrateur'])) {
    header('Location: ' . $BASE_URL . '/pages/connexion.php');
    exit;
}

$succes = '';
$erreur = '';

// Mise à jour statut commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_statut') {
    $commande_id = intval($_POST['commande_id']);
    $statut = $_POST['statut'];
    $stmt = $pdo->prepare("UPDATE commande SET statut=:statut WHERE commande_id=:id");
    $stmt->execute([':statut' => $statut, ':id' => $commande_id]);

    // Suivi commande
    $stmt = $pdo->prepare("INSERT INTO suivi_commande (commande_id, statut) VALUES (:id, :statut)");
    $stmt->execute([':id' => $commande_id, ':statut' => $statut]);
    $succes = "Statut mis à jour.";
}

// Validation/refus avis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'moderer_avis') {
    $avis_id = intval($_POST['avis_id']);
    $statut_avis = $_POST['statut_avis'];
    $stmt = $pdo->prepare("UPDATE avis SET statut=:statut WHERE avis_id=:id");
    $stmt->execute([':statut' => $statut_avis, ':id' => $avis_id]);
    $succes = "Avis modéré avec succès.";
}

// Filtre commandes
$filtre_statut = $_GET['statut'] ?? '';
$filtre_client = $_GET['client'] ?? '';

$sql = "SELECT c.*, m.titre as menu_titre, u.nom, u.prenom, u.email, u.telephone 
        FROM commande c 
        JOIN menu m ON c.menu_id = m.menu_id 
        JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id 
        WHERE 1=1";
$params = [];

if ($filtre_statut) {
    $sql .= " AND c.statut = :statut";
    $params[':statut'] = $filtre_statut;
}
if ($filtre_client) {
    $sql .= " AND (u.nom LIKE :client OR u.prenom LIKE :client OR u.email LIKE :client)";
    $params[':client'] = '%' . $filtre_client . '%';
}
$sql .= " ORDER BY c.date_commande DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$commandes = $stmt->fetchAll();

// Avis en attente
$avis = $pdo->query("SELECT a.*, u.nom, u.prenom FROM avis a JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id WHERE a.statut = 'en attente'")->fetchAll();
?>

<main class="container my-5">
    <h2 class="mb-4">🧑‍🍳 Espace Employé</h2>

    <?php if ($succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filtrer par statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <?php foreach (['en attente', 'accepte', 'en preparation', 'en cours de livraison', 'livre', 'en attente retour materiel', 'terminee', 'annulee'] as $s): ?>
                            <option value="<?= $s ?>" <?= $filtre_statut === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rechercher un client</label>
                    <input type="text" name="client" class="form-control" value="<?= htmlspecialchars($filtre_client) ?>" placeholder="Nom, prénom ou email">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-warning me-2">Filtrer</button>
                    <a href="<?= $BASE_URL ?>/pages/espace-employe.php" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Commandes -->
    <h5 class="mb-3">📦 Commandes (<?= count($commandes) ?>)</h5>
    <?php if (empty($commandes)): ?>
        <p class="text-muted">Aucune commande trouvée.</p>
    <?php else: ?>
        <?php foreach ($commandes as $commande): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6><?= htmlspecialchars($commande['numero_commande']) ?></h6>
                        <span class="badge bg-warning text-dark"><?= htmlspecialchars($commande['statut']) ?></span>
                    </div>
                    <p class="mb-1"><strong>Client :</strong> <?= htmlspecialchars($commande['prenom']) ?> <?= htmlspecialchars($commande['nom']) ?> — <?= htmlspecialchars($commande['email']) ?> — <?= htmlspecialchars($commande['telephone']) ?></p>
                    <p class="mb-1"><strong>Menu :</strong> <?= htmlspecialchars($commande['menu_titre']) ?></p>
                    <p class="mb-1"><strong>Date prestation :</strong> <?= htmlspecialchars($commande['date_prestation']) ?> à <?= htmlspecialchars($commande['heure_livraison']) ?></p>
                    <p class="mb-2"><strong>Total :</strong> <?= number_format($commande['prix_total'], 2) ?> €</p>

                    <form method="POST" class="d-flex gap-2 align-items-center">
                        <input type="hidden" name="action" value="update_statut">
                        <input type="hidden" name="commande_id" value="<?= $commande['commande_id'] ?>">
                        <select name="statut" class="form-select form-select-sm w-auto">
                            <?php foreach (['en attente', 'accepte', 'en preparation', 'en cours de livraison', 'livre', 'en attente retour materiel', 'terminee', 'annulee'] as $s): ?>
                                <option value="<?= $s ?>" <?= $commande['statut'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-warning">Mettre à jour</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Modération avis -->
    <h5 class="mt-5 mb-3">⭐ Avis en attente de modération (<?= count($avis) ?>)</h5>
    <?php if (empty($avis)): ?>
        <p class="text-muted">Aucun avis en attente.</p>
    <?php else: ?>
        <?php foreach ($avis as $a): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <p><strong><?= htmlspecialchars($a['prenom']) ?> <?= htmlspecialchars($a['nom']) ?></strong> — <?= str_repeat('⭐', $a['note']) ?></p>
                    <p><?= htmlspecialchars($a['description']) ?></p>
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="action" value="moderer_avis">
                        <input type="hidden" name="avis_id" value="<?= $a['avis_id'] ?>">
                        <button type="submit" name="statut_avis" value="valide" class="btn btn-sm btn-success">✅ Valider</button>
                        <button type="submit" name="statut_avis" value="refuse" class="btn btn-sm btn-danger">❌ Refuser</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>