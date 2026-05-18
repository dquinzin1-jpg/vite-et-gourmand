# Diagramme de classes / MCD enrichi — Vite & Gourmand

Modèle Conceptuel de Données représentant les **14 tables** de la base de données MySQL/MariaDB (hébergée sur AlwaysData), avec leurs attributs, types et cardinalités.

## Légende des cardinalités (notation Mermaid / Chen)

- `||--o{` : Un (et un seul) vers Zéro ou plusieurs
- `||--||` : Un vers Un
- `}o--||` : Zéro ou plusieurs vers Un

## Diagramme

```mermaid
erDiagram
    ROLE ||--o{ UTILISATEUR : "possède"
    UTILISATEUR ||--o{ COMMANDE : "passe"
    UTILISATEUR ||--o{ AVIS : "rédige"
    REGIME ||--o{ MENU : "définit"
    THEME ||--o{ MENU : "catégorise"
    MENU ||--o{ COMMANDE : "est commandé via"
    MENU ||--o{ IMAGE_MENU : "possède"
    MENU ||--o{ MENU_PLAT : "compose"
    PLAT ||--o{ MENU_PLAT : "est dans"
    PLAT ||--o{ PLAT_ALLERGENE : "contient"
    ALLERGENE ||--o{ PLAT_ALLERGENE : "présent dans"
    COMMANDE ||--o{ SUIVI_COMMANDE : "génère"
    COMMANDE ||--o{ AVIS : "fait l'objet de"

    ROLE {
        int role_id PK
        varchar libelle "client/employe/admin"
    }

    UTILISATEUR {
        int utilisateur_id PK
        varchar email UK "unique"
        varchar password "hashé bcrypt"
        varchar nom
        varchar prenom
        varchar telephone
        varchar adresse_postale
        tinyint statut "1=actif, 0=inactif"
        int role_id FK
    }

    REGIME {
        int regime_id PK
        varchar libelle "classique/vegetarien/halal..."
    }

    THEME {
        int theme_id PK
        varchar libelle "Noel/Paques/classique/evenement"
    }

    MENU {
        int menu_id PK
        varchar titre
        text description
        int nombre_personne_minimum
        double prix
        int quantite_restante
        text conditions
        int theme_id FK
        int regime_id FK
    }

    IMAGE_MENU {
        int image_id PK
        int menu_id FK
        varchar chemin
    }

    PLAT {
        int plat_id PK
        varchar nom
        double prix
        blob photo
        varchar type_plat "entree/plat/dessert"
    }

    ALLERGENE {
        int allergene_id PK
        varchar libelle "14 allergènes UE"
    }

    PLAT_ALLERGENE {
        int plat_id FK
        int allergene_id FK
    }

    MENU_PLAT {
        int menu_id FK
        int plat_id FK
    }

    COMMANDE {
        int commande_id PK
        varchar numero_commande
        date date_commande
        date date_prestation
        varchar heure_livraison
        varchar adresse_livraison
        int nombre_personne
        double prix_menu
        double prix_livraison
        double prix_total
        varchar statut "en_attente/accepte/.../terminee/annulee"
        tinyint pret_materiel
        tinyint retour_materiel
        varchar motif_annulation
        varchar mode_contact
        int utilisateur_id FK
        int menu_id FK
    }

    SUIVI_COMMANDE {
        int suivi_id PK
        int commande_id FK
        varchar statut
        datetime date_modification
    }

    AVIS {
        int avis_id PK
        int note "1 à 5"
        varchar description
        varchar statut "en_attente/valide/refuse"
        datetime date_avis
        int utilisateur_id FK
        int commande_id FK
    }

    HORAIRE {
        int horaire_id PK
        varchar jour
        varchar heure_ouverture
        varchar heure_fermeture
    }
```

## Notes techniques

### Conventions
- **PK** = Primary Key (clé primaire) — convention de nommage : `<nom_table>_id`
- **FK** = Foreign Key (clé étrangère)
- **UK** = Unique Key (contrainte d'unicité)

### Choix d'architecture
- La table **HORAIRE** est indépendante : elle stocke les horaires d'ouverture du traiteur (informations affichées en pied de page), sans rattachement à un utilisateur particulier.
- La relation `AVIS → COMMANDE` (et non `AVIS → MENU`) garantit qu'un avis est lié à une **commande réellement passée**. Cela empêche les faux avis : un utilisateur ne peut laisser un avis que sur un menu qu'il a commandé.
- Les tables **PLAT_ALLERGENE** et **MENU_PLAT** sont des **tables de liaison** (associations many-to-many) avec clés primaires composites.
- Le champ `password` de **UTILISATEUR** est stocké hashé avec `password_hash()` PHP (algorithme bcrypt par défaut).
- Le champ `email` de **UTILISATEUR** a une contrainte d'unicité pour éviter les doublons d'inscription.
- Le champ `statut` de **UTILISATEUR** (tinyint) permet la désactivation logique d'un compte sans suppression (soft delete).
- Le champ `quantite_restante` de **MENU** permet de gérer un stock limité par menu (utile pour les menus saisonniers/évènementiels).
- Le workflow de modération des avis : `en_attente` → `valide` ou `refuse` (gestion par les employés).
- Le workflow d'une commande : `en_attente` → `accepte` → `en_preparation` → `en_cours_de_livraison` → `livre` → `en_attente_retour_materiel` (si applicable) → `terminee` (ou `annulee` à n'importe quelle étape).

### Stack et types
- SGBD : **MariaDB 11.4.9** (compatible MySQL)
- Encodage : **utf8mb4** (support complet des emojis et caractères spéciaux)
- Moteur : **InnoDB** (support des transactions et clés étrangères)
- Accès depuis PHP : **PDO** (PHP Data Objects) avec requêtes préparées pour prévenir les injections SQL.