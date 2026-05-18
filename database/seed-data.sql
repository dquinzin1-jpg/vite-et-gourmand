-- ============================================================
-- FICHIER : seed-data.sql
-- BUT : Enrichir la BDD prod avec des données démo variées
-- BDD : vite_et_gourmand (dp-vite-et-gourmand_vite_et_gourmand)
-- USAGE : à exécuter via phpMyAdmin AlwaysData
-- AUTEUR : Dylan Quinzin, projet ECF TP DWWM Studi
-- DATE : Mai 2026
-- ============================================================


-- ============================================================
-- PARTIE 1 : MISE À JOUR DES MENUS EXISTANTS
-- Objectif : varier les thèmes et régimes pour respecter
-- l'énoncé Studi (variété thématique demandée)
-- ============================================================

-- Menu 1 = Prestige Mariage → thème évènement, classique
UPDATE menu SET theme_id = 4, regime_id = 1 WHERE menu_id = 1;

-- Menu 2 = Affaires Premium → thème classique, classique  
UPDATE menu SET theme_id = 3, regime_id = 1 WHERE menu_id = 2;

-- Menu 3 = Festif Anniversaire → thème évènement, classique
UPDATE menu SET theme_id = 4, regime_id = 1 WHERE menu_id = 3;

-- Menu 4 = Découverte → thème classique, classique
UPDATE menu SET theme_id = 3, regime_id = 1 WHERE menu_id = 4;


-- ============================================================
-- PARTIE 2 : AJOUT DE 4 NOUVEAUX MENUS VARIÉS
-- Objectif : couvrir tous les thèmes et plusieurs régimes
-- ============================================================

INSERT INTO menu (titre, description, nombre_personne_minimum, prix, quantite_restante, conditions, theme_id, regime_id) VALUES
('Menu Réveillon de Noel', 'Un menu festif et raffiné pour célébrer Noel en famille : foie gras maison, chapon farci aux marrons, bûche pâtissière. Une expérience traditionnelle revisitée.', 8, 75, 20, 'Réservation 3 semaines à l\'avance. Acompte de 50 % à la commande.', 1, 1);

INSERT INTO menu (titre, description, nombre_personne_minimum, prix, quantite_restante, conditions, theme_id, regime_id) VALUES
('Menu Pâques Gourmand', 'Célébrez Pâques avec un menu printanier : agneau pascal de 7 heures, légumes nouveaux et entremets chocolaté en forme d\'œuf.', 6, 62, 15, 'Disponible uniquement entre mars et avril. Réservation 2 semaines à l\'avance.', 2, 1);

INSERT INTO menu (titre, description, nombre_personne_minimum, prix, quantite_restante, conditions, theme_id, regime_id) VALUES
('Menu Végétarien Saveurs', 'Une carte 100 % végétarienne aux saveurs du monde : tartare de légumes, risotto crémeux aux champignons, tatin de tomates et pavlova fraises.', 4, 38, 30, 'Disponible toute l\'année. Possibilité d\'option vegan sur demande (-2 € par personne).', 3, 2);

INSERT INTO menu (titre, description, nombre_personne_minimum, prix, quantite_restante, conditions, theme_id, regime_id) VALUES
('Menu Halal Royal', 'Un menu halal d\'exception : briouates aux légumes, couscous royal aux 3 viandes (agneau, poulet, merguez) et pâtisseries orientales.', 10, 48, 25, 'Toutes les viandes sont certifiées halal. Réservation 1 semaine à l\'avance.', 4, 5);


-- ============================================================
-- PARTIE 3 : INSERTION DES PLATS (~20 plats variés)
-- Répartis en 3 types : entrée, plat, dessert
-- ============================================================

-- ============ ENTRÉES (6) ============
INSERT INTO plat (nom, prix, type_plat) VALUES ('Velouté de potimarron à la châtaigne', 8.50, 'entree');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Tartare de saumon mariné aux agrumes', 12, 'entree');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Foie gras maison et son chutney de figues', 18, 'entree');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Tartare de légumes croquants au pesto', 9, 'entree');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Briouates aux légumes et herbes fraîches', 7, 'entree');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Verrine de crabe et avocat', 11, 'entree');

-- ============ PLATS (8) ============
INSERT INTO plat (nom, prix, type_plat) VALUES ('Filet de bœuf Wellington', 28, 'plat');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Chapon farci aux marrons et foie gras', 32, 'plat');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Agneau pascal de 7 heures', 26, 'plat');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Saumon en croûte d\'herbes', 24, 'plat');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Risotto crémeux aux champignons des bois', 18, 'plat');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Couscous royal aux 3 viandes halal', 22, 'plat');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Tatin de tomates et chèvre frais', 16, 'plat');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Suprême de volaille forestier', 21, 'plat');

-- ============ DESSERTS (6) ============
INSERT INTO plat (nom, prix, type_plat) VALUES ('Bûche pâtissière chocolat-praliné', 9, 'dessert');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Entremets chocolat en forme d\'œuf', 8, 'dessert');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Pavlova aux fraises et fruits rouges', 7.50, 'dessert');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Pâtisseries orientales assorties', 8, 'dessert');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Crème brûlée à la vanille de Madagascar', 7, 'dessert');
INSERT INTO plat (nom, prix, type_plat) VALUES ('Salade de fruits exotiques', 6, 'dessert');


-- ============================================================
-- PARTIE 4 : LIAISONS PLAT-ALLERGÈNE
-- Pour chaque plat avec allergènes : associer ID plat + ID allergène
-- ============================================================

-- Entrées
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (1, 7);  -- Velouté potimarron : Lait
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (2, 4);  -- Tartare saumon : Poissons
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (2, 11); -- Tartare saumon : Sésame
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (3, 12); -- Foie gras : Sulfites
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (4, 8);  -- Tartare légumes : Fruits à coque
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (5, 1);  -- Briouates : Gluten
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (6, 2);  -- Verrine crabe : Crustacés
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (6, 3);  -- Verrine crabe : Oeufs

-- Plats
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (7, 1);  -- Wellington : Gluten
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (7, 3);  -- Wellington : Oeufs
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (7, 7);  -- Wellington : Lait
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (8, 8);  -- Chapon : Fruits à coque
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (8, 12); -- Chapon : Sulfites
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (10, 4); -- Saumon : Poissons
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (11, 7); -- Risotto : Lait
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (12, 1); -- Couscous : Gluten
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (13, 1); -- Tatin : Gluten
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (13, 7); -- Tatin : Lait
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (14, 7); -- Suprême : Lait

-- Desserts
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (15, 1); -- Bûche : Gluten
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (15, 3); -- Bûche : Oeufs
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (15, 7); -- Bûche : Lait
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (15, 6); -- Bûche : Soja
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (16, 1); -- Entremets : Gluten
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (16, 3); -- Entremets : Oeufs
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (16, 7); -- Entremets : Lait
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (17, 3); -- Pavlova : Oeufs
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (18, 1); -- Pâtisseries : Gluten
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (18, 8); -- Pâtisseries : Fruits à coque
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (19, 3); -- Crème brûlée : Oeufs
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (19, 7); -- Crème brûlée : Lait


-- ============================================================
-- PARTIE 5 : LIAISONS MENU-PLAT
-- Composition des menus avec entrée + plat + dessert
-- ============================================================

-- Menu 1 (Prestige Mariage) : Verrine de crabe + Wellington + Bûche
INSERT INTO menu_plat (menu_id, plat_id) VALUES (1, 6);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (1, 7);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (1, 15);

-- Menu 2 (Affaires Premium) : Velouté + Suprême de volaille + Crème brûlée
INSERT INTO menu_plat (menu_id, plat_id) VALUES (2, 1);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (2, 14);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (2, 19);

-- Menu 3 (Festif Anniversaire) : Tartare de saumon + Wellington + Bûche
INSERT INTO menu_plat (menu_id, plat_id) VALUES (3, 2);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (3, 7);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (3, 15);

-- Menu 4 (Découverte) : Velouté + Tatin + Salade de fruits
INSERT INTO menu_plat (menu_id, plat_id) VALUES (4, 1);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (4, 13);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (4, 20);

-- Menu 5 (Réveillon de Noel) : Foie gras + Chapon farci + Bûche
INSERT INTO menu_plat (menu_id, plat_id) VALUES (5, 3);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (5, 8);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (5, 15);

-- Menu 6 (Pâques Gourmand) : Velouté + Agneau + Entremets œuf
INSERT INTO menu_plat (menu_id, plat_id) VALUES (6, 1);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (6, 9);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (6, 16);

-- Menu 7 (Végétarien Saveurs) : Tartare légumes + Risotto + Pavlova
INSERT INTO menu_plat (menu_id, plat_id) VALUES (7, 4);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (7, 11);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (7, 17);

-- Menu 8 (Halal Royal) : Briouates + Couscous + Pâtisseries orientales
INSERT INTO menu_plat (menu_id, plat_id) VALUES (8, 5);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (8, 12);
INSERT INTO menu_plat (menu_id, plat_id) VALUES (8, 18);


-- ============================================================
-- PARTIE 6 : IMAGES DES MENUS (galeries)
-- ============================================================

INSERT INTO image_menu (menu_id, chemin) VALUES (5, 'assets/images/hero-accueil.jpg');
INSERT INTO image_menu (menu_id, chemin) VALUES (6, 'assets/images/menu-mariage.jpg');
INSERT INTO image_menu (menu_id, chemin) VALUES (7, 'assets/images/menu-entreprise.jpg');
INSERT INTO image_menu (menu_id, chemin) VALUES (8, 'assets/images/menu-anniversaire.jpg');


-- ============================================================
-- PARTIE 7 : COMMANDES TERMINÉES FICTIVES
-- Pour pouvoir créer des avis validés sur l'accueil
-- ATTENTION : on suppose qu'il existe au moins 1 utilisateur 
-- avec rôle "utilisateur" (role_id = 3) en BDD
-- ============================================================

-- Récupération automatique d'un utilisateur existant pour les commandes
-- (les commandes seront associées au premier utilisateur trouvé avec role_id = 3)
SET @user_id = (SELECT utilisateur_id FROM utilisateur WHERE role_id = 3 ORDER BY utilisateur_id ASC LIMIT 1);

-- Commande terminée 1 : Mariage de Sophie
INSERT INTO commande (numero_commande, date_commande, date_prestation, heure_livraison, adresse_livraison, nombre_personne, prix_menu, prix_livraison, prix_total, statut, pret_materiel, retour_materiel, mode_contact, utilisateur_id, menu_id)
VALUES ('CMD-DEMO001', '2026-04-15', '2026-05-10', '18:00', '15 rue des Vignes, 33000 Bordeaux', 60, 5340, 50, 5390, 'terminee', TRUE, TRUE, 'email', @user_id, 1);

-- Commande terminée 2 : Séminaire d'entreprise
INSERT INTO commande (numero_commande, date_commande, date_prestation, heure_livraison, adresse_livraison, nombre_personne, prix_menu, prix_livraison, prix_total, statut, pret_materiel, retour_materiel, mode_contact, utilisateur_id, menu_id)
VALUES ('CMD-DEMO002', '2026-04-20', '2026-05-05', '12:00', '8 avenue de la Liberté, 33200 Mérignac', 30, 1350, 30, 1380, 'terminee', FALSE, FALSE, 'telephone', @user_id, 2);

-- Commande terminée 3 : Anniversaire 60 ans
INSERT INTO commande (numero_commande, date_commande, date_prestation, heure_livraison, adresse_livraison, nombre_personne, prix_menu, prix_livraison, prix_total, statut, pret_materiel, retour_materiel, mode_contact, utilisateur_id, menu_id)
VALUES ('CMD-DEMO003', '2026-04-25', '2026-05-12', '19:30', '22 cours Victor Hugo, 33000 Bordeaux', 40, 2200, 40, 2240, 'terminee', TRUE, TRUE, 'email', @user_id, 3);


-- ============================================================
-- PARTIE 8 : SUIVI DES COMMANDES (historique des statuts)
-- ============================================================

INSERT INTO suivi_commande (commande_id, statut, date_modification)
SELECT commande_id, 'terminee', NOW() FROM commande WHERE numero_commande IN ('CMD-DEMO001', 'CMD-DEMO002', 'CMD-DEMO003');


-- ============================================================
-- PARTIE 9 : AVIS VALIDÉS (3 avis pour l'affichage accueil)
-- Ces avis seront affichés sur la page d'accueil grâce au statut "valide"
-- ============================================================

-- Récupération des IDs de commandes
SET @cmd1 = (SELECT commande_id FROM commande WHERE numero_commande = 'CMD-DEMO001');
SET @cmd2 = (SELECT commande_id FROM commande WHERE numero_commande = 'CMD-DEMO002');
SET @cmd3 = (SELECT commande_id FROM commande WHERE numero_commande = 'CMD-DEMO003');

INSERT INTO avis (note, description, statut, date_avis, utilisateur_id, commande_id)
VALUES (5, 'Une équipe à l\'écoute, des plats raffinés et une présentation impeccable. Notre mariage a été sublimé par la qualité du service.', 'valide', NOW(), @user_id, @cmd1);

INSERT INTO avis (note, description, statut, date_avis, utilisateur_id, commande_id)
VALUES (5, 'Service impeccable pour notre séminaire d\'entreprise. Tous nos collaborateurs ont été conquis par les saveurs et la présentation.', 'valide', NOW(), @user_id, @cmd2);

INSERT INTO avis (note, description, statut, date_avis, utilisateur_id, commande_id)
VALUES (5, 'Pour les 60 ans de mon père, c\'était parfait. Les invités en parlent encore plusieurs mois après. Merci à toute l\'équipe !', 'valide', NOW(), @user_id, @cmd3);


-- ============================================================
-- FIN DU SEED
-- ============================================================