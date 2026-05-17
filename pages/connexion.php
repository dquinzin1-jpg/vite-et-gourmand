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

<section class="form-section">
    <div class="container">
        <div class="form-card">
            <h2>Connexion</h2>
            <p class="form-subtitle">Heureux de vous retrouver !</p>
            
            <?php if ($erreur): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Adresse email</label>
                    <input type="email" name="email" class="form-control" placeholder="votre@email.fr" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••••" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn-primary-custom" style="width: 100%; border: none;">Se connecter</button>
                </div>
                <p class="form-link">
                    Pas encore de compte ? <a href="<?= $BASE_URL ?>/pages/inscription.php">Créez votre compte</a>
                </p>
            </form>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
