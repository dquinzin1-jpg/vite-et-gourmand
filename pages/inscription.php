<?php 
include_once __DIR__ . '/../includes/header.php';

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse_postale']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse email invalide.";
    } else {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/';
        if (!preg_match($regex, $password)) {
            $erreur = "Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        } else {
            $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $erreur = "Cet email est déjà utilisé.";
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, statut, role_id) VALUES (:email, :password, :nom, :prenom, :telephone, :adresse, 1, 3)");
                $stmt->execute([
                    ':email' => $email,
                    ':password' => $hash,
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':telephone' => $telephone,
                    ':adresse' => $adresse
                ]);
                $succes = "Compte créé avec succès ! Vous pouvez vous connecter.";
            }
        }
    }
}
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4 text-center">Créer un compte</h2>
            <?php if ($erreur): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            <?php if ($succes): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($succes) ?>
                    <br><a href="<?= $BASE_URL ?>/pages/connexion.php" class="alert-link">Cliquez ici pour vous connecter</a>
                </div>
            <?php endif; ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Adresse postale</label>
                            <input type="text" name="adresse_postale" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                            <small class="text-muted">10 caractères min., avec majuscule, minuscule, chiffre et caractère spécial.</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">Créer mon compte</button>
                        </div>
                        <p class="text-center mt-3">
                            Déjà un compte ? <a href="<?= $BASE_URL ?>/pages/connexion.php">Se connecter</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>