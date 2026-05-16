<?php 
include_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'administrateur') {
    header('Location: /vite-et-gourmand/pages/connexion.php');
    exit;
}

$succes = '';
$erreur = '';

// Création compte employé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'creer_employe') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);

    $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        $erreur = "Cet email est déjà utilisé.";
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, statut, role_id) VALUES (:email, :password, :nom, :prenom, '0000000000', 'Non renseigné', 1, 2)");
        $stmt->execute([':email' => $email, ':password' => $hash, ':nom' => $nom, ':prenom' => $prenom]);
        $succes = "Compte employé créé pour $prenom $nom.";
    }
}

// Activation/désactivation compte employé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_employe') {
    $user_id = intval($_POST['user_id']);
    $statut = intval($_POST['statut']);
    $stmt = $pdo->prepare("UPDATE utilisateur SET statut = :statut WHERE utilisateur_id = :id AND role_id = 2");
    $stmt->execute([':statut' => $statut, ':id' => $user_id]);
    $succes = "Compte mis à jour.";
}

// Récupération des employés
$employes = $pdo->query("SELECT * FROM utilisateur WHERE role_id = 2")->fetchAll();

// Statistiques commandes par menu
$stats = $pdo->query("SELECT m.titre, COUNT(c.commande_id) as nb_commandes, SUM(c.prix_total) as chiffre_affaires FROM commande c JOIN menu m ON c.menu_id = m.menu_id WHERE c.statut != 'annulee' GROUP BY m.menu_id")->fetchAll();
?>

<main class="container my-5">
    <h2 class="mb-4">⚙️ Espace Administrateur</h2>

    <?php if ($succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Créer un employé -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>➕ Créer un compte employé</h5>
                    <form method="POST">
                        <input type="hidden" name="action" value="creer_employe">
                        <div class="mb-2">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Créer le compte</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des employés -->
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>👥 Comptes employés</h5>
                    <?php if (empty($employes)): ?>
                        <p class="text-muted">Aucun employé enregistré.</p>
                    <?php else: ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employes as $emp): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($emp['prenom']) ?> <?= htmlspecialchars($emp['nom']) ?></td>
                                        <td><?= htmlspecialchars($emp['email']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $emp['statut'] ? 'success' : 'danger' ?>">
                                                <?= $emp['statut'] ? 'Actif' : 'Inactif' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="toggle_employe">
                                                <input type="hidden" name="user_id" value="<?= $emp['utilisateur_id'] ?>">
                                                <input type="hidden" name="statut" value="<?= $emp['statut'] ? 0 : 1 ?>">
                                                <button type="submit" class="btn btn-sm btn-<?= $emp['statut'] ? 'danger' : 'success' ?>">
                                                    <?= $emp['statut'] ? 'Désactiver' : 'Activer' ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5>📊 Statistiques par menu</h5>
            <canvas id="graphique-commandes" height="100"></canvas>
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Nb commandes</th>
                        <th>Chiffre d'affaires</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['titre']) ?></td>
                            <td><?= $s['nb_commandes'] ?></td>
                            <td><?= number_format($s['chiffre_affaires'], 2) ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = <?= json_encode(array_column($stats, 'titre')) ?>;
const data = <?= json_encode(array_column($stats, 'nb_commandes')) ?>;

new Chart(document.getElementById('graphique-commandes'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Nombre de commandes',
            data: data,
            backgroundColor: 'rgba(255, 193, 7, 0.7)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>