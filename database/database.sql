-- ============================================================
-- BASE DE DONNÉES : vite_et_gourmand
-- Projet ECF TP DWWM - Studi
-- ============================================================

CREATE DATABASE IF NOT EXISTS vite_et_gourmand CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vite_et_gourmand;

-- ============================================================
-- TABLE : role
-- ============================================================
CREATE TABLE role (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- ============================================================
-- TABLE : utilisateur
-- ============================================================
CREATE TABLE utilisateur (
    utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    adresse_postale VARCHAR(255) NOT NULL,
    statut BOOLEAN NOT NULL DEFAULT TRUE,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES role(role_id)
);

-- ============================================================
-- TABLE : regime
-- ============================================================
CREATE TABLE regime (
    regime_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- ============================================================
-- TABLE : theme
-- ============================================================
CREATE TABLE theme (
    theme_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- ============================================================
-- TABLE : menu
-- ============================================================
CREATE TABLE menu (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    nombre_personne_minimum INT NOT NULL,
    prix DOUBLE NOT NULL,
    quantite_restante INT NOT NULL,
    conditions TEXT,
    theme_id INT NOT NULL,
    regime_id INT NOT NULL,
    FOREIGN KEY (theme_id) REFERENCES theme(theme_id),
    FOREIGN KEY (regime_id) REFERENCES regime(regime_id)
);

-- ============================================================
-- TABLE : allergene
-- ============================================================
CREATE TABLE allergene (
    allergene_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- ============================================================
-- TABLE : plat
-- ============================================================
CREATE TABLE plat (
    plat_id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prix DOUBLE NOT NULL,
    photo BLOB,
    type_plat VARCHAR(20) NOT NULL COMMENT 'entree, plat, dessert'
);

-- ============================================================
-- TABLE : plat_allergene (liaison plat <-> allergene)
-- ============================================================
CREATE TABLE plat_allergene (
    plat_id INT NOT NULL,
    allergene_id INT NOT NULL,
    PRIMARY KEY (plat_id, allergene_id),
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id),
    FOREIGN KEY (allergene_id) REFERENCES allergene(allergene_id)
);

-- ============================================================
-- TABLE : menu_plat (liaison menu <-> plat)
-- ============================================================
CREATE TABLE menu_plat (
    menu_id INT NOT NULL,
    plat_id INT NOT NULL,
    PRIMARY KEY (menu_id, plat_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id),
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id)
);

-- ============================================================
-- TABLE : image_menu (galerie d'images par menu)
-- ============================================================
CREATE TABLE image_menu (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id)
);

-- ============================================================
-- TABLE : commande
-- ============================================================
CREATE TABLE commande (
    commande_id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(50) NOT NULL UNIQUE,
    date_commande DATE NOT NULL,
    date_prestation DATE NOT NULL,
    heure_livraison VARCHAR(10) NOT NULL,
    adresse_livraison VARCHAR(255) NOT NULL,
    nombre_personne INT NOT NULL,
    prix_menu DOUBLE NOT NULL,
    prix_livraison DOUBLE NOT NULL DEFAULT 0,
    prix_total DOUBLE NOT NULL,
    statut VARCHAR(50) NOT NULL DEFAULT 'en attente' COMMENT 'en attente, accepte, en preparation, en cours de livraison, livre, en attente retour materiel, terminee, annulee',
    pret_materiel BOOLEAN NOT NULL DEFAULT FALSE,
    retour_materiel BOOLEAN NOT NULL DEFAULT FALSE,
    motif_annulation VARCHAR(255),
    mode_contact VARCHAR(50),
    utilisateur_id INT NOT NULL,
    menu_id INT NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id)
);

-- ============================================================
-- TABLE : suivi_commande (historique des statuts)
-- ============================================================
CREATE TABLE suivi_commande (
    suivi_id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    statut VARCHAR(50) NOT NULL,
    date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commande(commande_id)
);

-- ============================================================
-- TABLE : avis
-- ============================================================
CREATE TABLE avis (
    avis_id INT AUTO_INCREMENT PRIMARY KEY,
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    description VARCHAR(255),
    statut VARCHAR(20) NOT NULL DEFAULT 'en attente' COMMENT 'en attente, valide, refuse',
    date_avis DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    utilisateur_id INT NOT NULL,
    commande_id INT NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id),
    FOREIGN KEY (commande_id) REFERENCES commande(commande_id)
);

-- ============================================================
-- TABLE : horaire
-- ============================================================
CREATE TABLE horaire (
    horaire_id INT AUTO_INCREMENT PRIMARY KEY,
    jour VARCHAR(20) NOT NULL,
    heure_ouverture VARCHAR(10),
    heure_fermeture VARCHAR(10)
);

-- ============================================================
-- DONNÉES D'INITIALISATION
-- ============================================================

-- Rôles
INSERT INTO role (libelle) VALUES ('administrateur'), ('employe'), ('utilisateur');

-- Régimes
INSERT INTO regime (libelle) VALUES ('classique'), ('vegetarien'), ('vegan'), ('sans gluten'), ('halal');

-- Thèmes
INSERT INTO theme (libelle) VALUES ('Noel'), ('Pâques'), ('classique'), ('évènement');

-- Allergènes
INSERT INTO allergene (libelle) VALUES 
('Gluten'), ('Crustacés'), ('Oeufs'), ('Poissons'), 
('Arachides'), ('Soja'), ('Lait'), ('Fruits à coque'),
('Céleri'), ('Moutarde'), ('Sésame'), ('Sulfites'),
('Lupin'), ('Mollusques');

-- Horaires
INSERT INTO horaire (jour, heure_ouverture, heure_fermeture) VALUES
('Lundi', '09:00', '18:00'),
('Mardi', '09:00', '18:00'),
('Mercredi', '09:00', '18:00'),
('Jeudi', '09:00', '18:00'),
('Vendredi', '09:00', '18:00'),
('Samedi', '10:00', '16:00'),
('Dimanche', NULL, NULL);

-- Compte administrateur (mot de passe : Admin1234!)
INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, statut, role_id)
VALUES (
    'admin@vite-et-gourmand.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Admin',
    'Vite&Gourmand',
    '0600000000',
    '1 rue de Bordeaux, 33000 Bordeaux',
    TRUE,
    1
);