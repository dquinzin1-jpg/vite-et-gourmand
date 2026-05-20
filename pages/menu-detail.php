<?php 
include_once '../includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: /pages/menus.php');
    exit;
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT m.*, t.libelle as theme, r.libelle as regime 
                        FROM menu m 
                        JOIN theme t ON m.theme_id = t.theme_id 
                        JOIN regime r ON m.regime_id = r.regime_id 
                        WHERE m.menu_id = :id");
$stmt->execute([':id' => $id]);
$menu = $stmt->fetch();

if (!$menu) {
    header('Location: /pages/menus.php');
    exit;
}

$plats = $pdo->prepare("SELECT p.*, GROUP_CONCAT(a.libelle SEPARATOR ', ') as allergenes 
                         FROM plat p 
                         JOIN menu_plat mp ON p.plat_id = mp.plat_id 
                         LEFT JOIN plat_allergene pa ON p.plat_id = pa.plat_id 
                         LEFT JOIN allergene a ON pa.allergene_id = a.allergene_id 
                         WHERE mp.menu_id = :id 
                         GROUP BY p.plat_id");
$plats->execute([':id' => $id]);
$plats = $plats->fetchAll();

$images = $pdo->prepare("SELECT * FROM image_menu WHERE menu_id = :id");
$images->execute([':id' => $id]);
$images = $images->fetchAll();
?>

<main class="container my-5">
    <a href="<?= $BASE_URL ?>/pages/menus.php" class="btn btn-outline-secondary mb-4">← Retour aux menus</a>

    <div class="row">
        <div class="col-md-8">
            <h1><?= htmlspecialchars($menu['titre']) ?></h1>
            <span class="badge bg-warning text-dark"><?= htmlspecialchars($menu['theme']) ?></span>
            <span class="badge bg-success ms-2"><?= htmlspecialchars($menu['regime']) ?></span>

            <p class="mt-3"><?= nl2br(htmlspecialchars($menu['description'])) ?></p>

            <!-- Conditions importantes -->
            <?php if ($menu['conditions']): ?>
            <div class="alert alert-warning mt-3">
                <strong>⚠️ Conditions importantes :</strong><br>
                <?= nl2br(htmlspecialchars($menu['conditions'])) ?>
            </div>
            <?php endif; ?>

            <!-- Plats -->
            <h4 class="mt-4">🍽️ Composition du menu</h4>
            <?php
            $types = ['entree' => 'Entrées', 'plat' => 'Plats', 'dessert' => 'Desserts'];
            foreach ($types as $type => $label):
                $plats_type = array_filter($plats, fn($p) => $p['type_plat'] === $type);
                if ($plats_type): ?>
                    <h6 class="mt-3"><?= $label ?></h6>
                    <ul class="list-group mb-3">
                        <?php foreach ($plats_type as $plat): ?>
                            <li class="list-group-item">
                                <strong><?= htmlspecialchars($plat['nom']) ?></strong>
                                <?php if ($plat['allergenes']): ?>
                                    <br><small class="text-muted">⚠️ Allergènes : <?= htmlspecialchars($plat['allergenes']) ?></small>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
            <?php endif; endforeach; ?>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>📋 Informations</h5>
                    <p><strong>👥 Minimum :</strong> <?= $menu['nombre_personne_minimum'] ?> personnes</p>
                    <p><strong>💰 Prix :</strong> <?= number_format($menu['prix'], 2) ?> € / <?= $menu['nombre_personne_minimum'] ?> pers.</p>
                    <p><strong>📦 Stock :</strong> <?= $menu['quantite_restante'] ?> disponible(s)</p>

                    <?php if (isset($_SESSION['utilisateur'])): ?>
                        <a href="<?= $BASE_URL ?>/pages/commande.php?menu_id=<?= $menu['menu_id'] ?>" class="btn btn-warning w-100 mt-2">Commander ce menu</a>
                    <?php else: ?>
                        <div class="alert alert-info mt-3">
                            <a href="<?= $BASE_URL ?>/pages/connexion.php">Connectez-vous</a> ou 
                            <a href="<?= $BASE_URL ?>/pages/inscription.php">créez un compte</a> pour commander.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>