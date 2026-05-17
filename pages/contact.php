<?php 
include_once __DIR__ . '/../includes/header.php';

$succes = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $sujet = trim($_POST['sujet']);
    $message = trim($_POST['message']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse email invalide.";
    } elseif (empty($nom) || empty($message)) {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Ici on pourrait enregistrer en BDD ou envoyer un email
        $succes = "Votre message a bien été envoyé ! Nous vous répondrons dans les plus brefs délais.";
    }
}
?>

<!-- ==========================================================================
     HERO PAGE CONTACT
========================================================================== -->
<section class="hero-section" style="height: 40vh; min-height: 300px; background-image: linear-gradient(rgba(26, 22, 18, 0.6), rgba(26, 22, 18, 0.8)), url('<?= $BASE_URL ?>/assets/images/chef-cuisine.jpg');">
    <div class="hero-content">
        <h1 style="font-size: clamp(2rem, 4vw, 3.5rem);">Nous <span class="accent">contacter</span></h1>
        <div class="hero-separator"></div>
        <p class="hero-subtitle" style="font-size: 1.1rem;">
            Une question, un projet, un événement à organiser ? Notre équipe vous répond rapidement.
        </p>
    </div>
</section>

<!-- ==========================================================================
     SECTION CONTACT
========================================================================== -->
<section class="section-padding section-cream">
    <div class="container">
        <div class="row">
            <!-- Infos de contact à gauche -->
            <div class="col-lg-5 mb-4">
                <h2 style="color: var(--color-bordeaux); margin-bottom: 2rem;">Restons en contact</h2>
                <p style="color: var(--color-gray); line-height: 1.8; margin-bottom: 2rem;">
                    N'hésitez pas à nous contacter pour toute demande de devis ou simplement pour discuter de votre projet. Nous vous répondrons dans les 24h ouvrées.
                </p>
                
                <div class="contact-info-list">
                    <div class="contact-info-item mb-3" style="display: flex; align-items: start; gap: 1rem;">
                        <div style="background-color: var(--color-bordeaux); color: var(--color-gold); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.3rem;">📍</div>
                        <div>
                            <h5 style="color: var(--color-bordeaux); margin-bottom: 0.3rem; font-size: 1.1rem;">Adresse</h5>
                            <p style="color: var(--color-gray); margin: 0;">Bordeaux, Gironde<br>33000 France</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item mb-3" style="display: flex; align-items: start; gap: 1rem;">
                        <div style="background-color: var(--color-bordeaux); color: var(--color-gold); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.3rem;">📞</div>
                        <div>
                            <h5 style="color: var(--color-bordeaux); margin-bottom: 0.3rem; font-size: 1.1rem;">Téléphone</h5>
                            <p style="color: var(--color-gray); margin: 0;">05 56 XX XX XX</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item mb-3" style="display: flex; align-items: start; gap: 1rem;">
                        <div style="background-color: var(--color-bordeaux); color: var(--color-gold); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.3rem;">✉️</div>
                        <div>
                            <h5 style="color: var(--color-bordeaux); margin-bottom: 0.3rem; font-size: 1.1rem;">Email</h5>
                            <p style="color: var(--color-gray); margin: 0;">contact@vite-et-gourmand.fr</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item" style="display: flex; align-items: start; gap: 1rem;">
                        <div style="background-color: var(--color-bordeaux); color: var(--color-gold); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.3rem;">🕐</div>
                        <div>
                            <h5 style="color: var(--color-bordeaux); margin-bottom: 0.3rem; font-size: 1.1rem;">Horaires</h5>
                            <p style="color: var(--color-gray); margin: 0;">Lun-Ven : 9h - 18h<br>Samedi : 10h - 16h</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire à droite -->
            <div class="col-lg-7">
                <div class="form-card" style="max-width: 100%; margin: 0;">
                    <h2 style="text-align: left;">Envoyez-nous un message</h2>
                    <p class="form-subtitle" style="text-align: left;">Nous vous répondrons rapidement.</p>
                    
                    <?php if ($erreur): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($succes): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" placeholder="Votre nom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="votre@email.fr" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sujet</label>
                            <input type="text" name="sujet" class="form-control" placeholder="Objet de votre message">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Décrivez-nous votre projet..." required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn-primary-custom" style="width: 100%; border: none;">Envoyer le message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
