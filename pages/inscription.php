<?php 
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/mail.php';

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

                // Mail de bienvenue (simulé en dev — voir includes/mail.php)
                envoyerMail(
                    $email,
                    "Bienvenue chez Vite & Gourmand !",
                    "Bonjour $prenom $nom,\n\n"
                    . "Votre compte a bien été créé. Vous pouvez désormais vous "
                    . "connecter pour consulter nos menus et passer commande.\n\n"
                    . "À très bientôt,\nL'équipe Vite & Gourmand"
                );

                $succes = "Compte créé avec succès ! Vous pouvez vous connecter.";
            }
        }
    }
}
?>

<section class="form-section">
    <div class="container">
        <div class="form-card" style="max-width: 600px;">
            <h2>Créer un compte</h2>
            <p class="form-subtitle">Rejoignez la famille Vite &amp; Gourmand</p>
            
            <?php if ($erreur): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            
            <?php if ($succes): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($succes) ?>
                    <br><a href="<?= $BASE_URL ?>/pages/connexion.php" class="alert-link">Cliquez ici pour vous connecter →</a>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control" placeholder="Dupont" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" class="form-control" placeholder="Marie" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="marie.dupont@email.fr" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control" placeholder="06 12 34 56 78" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse postale</label>
                    <input type="text" name="adresse_postale" class="form-control" placeholder="123 rue de la République, 33000 Bordeaux" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••••" required>
                    <small class="text-muted">10 caractères min., avec majuscule, minuscule, chiffre et caractère spécial.</small>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn-primary-custom" style="width: 100%; border: none;">Créer mon compte</button>
                </div>
                <p class="form-link">
                    Déjà un compte ? <a href="<?= $BASE_URL ?>/pages/connexion.php">Se connecter</a>
                </p>
            </form>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>