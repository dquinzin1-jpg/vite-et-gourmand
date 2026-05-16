<?php 
include_once __DIR__ . '/../includes/header.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT u.*, r.libelle as role FROM utilisateur u JOIN role r ON u.role_id = r.role_id WHERE u.email = :email AND u.statut = 1");
    $stmt->execute([':email' => $email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && password_verify($password, $utilisateur['password'])) {
        session_regenerate_id(true);
        $_SESSION['utilisateur'] = $utilisateur;
        header('Location: ' . $BASE_URL . '/index.php');
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h2 class="mb-4 text-center">Connexion</h2>
            <?php if ($erreur): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Adresse email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">Se connecter</button>
                        </div>
                        <p class="text-center mt-3">
                            Pas encore de compte ? <a href="<?= $BASE_URL ?>/pages/inscription.php">S'inscrire</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>