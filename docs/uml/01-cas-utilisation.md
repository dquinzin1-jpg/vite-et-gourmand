# Diagramme de cas d'utilisation — Vite & Gourmand

Ce diagramme représente les interactions des 4 acteurs (Visiteur, Client, Employé, Admin) avec le système.

## Légende
- 👤 **Visiteur** : utilisateur non connecté
- 🛒 **Client** : utilisateur connecté avec rôle "client"
- 👨‍🍳 **Employé** : utilisateur connecté avec rôle "employe"
- 🛡️ **Admin** : utilisateur connecté avec rôle "admin"

> Les rôles héritent : Client hérite de Visiteur, Admin hérite de Employé.

## Diagramme

```mermaid
flowchart LR
    %% ===== ACTEURS =====
    V(("👤<br/>Visiteur"))
    C(("🛒<br/>Client"))
    E(("👨‍🍳<br/>Employé"))
    A(("🛡️<br/>Admin"))

    %% ===== CAS D'UTILISATION VISITEUR =====
    UC1["Consulter l'accueil"]
    UC2["Parcourir les menus"]
    UC3["Filtrer les menus<br/>(prix, thème, régime)"]
    UC4["Consulter la page contact"]
    UC5["Lire les avis validés"]
    UC6["S'inscrire"]
    UC7["Se connecter"]

    %% ===== CAS D'UTILISATION CLIENT =====
    UC8["Passer une commande"]
    UC9["Suivre ses commandes"]
    UC10["Laisser un avis"]
    UC11["Modifier son profil"]
    UC12["Se déconnecter"]

    %% ===== CAS D'UTILISATION EMPLOYÉ =====
    UC13["Modérer les avis<br/>(valider / refuser)"]
    UC14["Gérer les commandes<br/>(changer statut)"]
    UC15["Consulter le suivi<br/>des commandes"]

    %% ===== CAS D'UTILISATION ADMIN =====
    UC16["Gérer les utilisateurs<br/>(CRUD)"]
    UC17["Gérer les menus<br/>et les plats (CRUD)"]
    UC18["Consulter les statistiques"]

    %% ===== LIENS VISITEUR =====
    V --> UC1
    V --> UC2
    V --> UC3
    V --> UC4
    V --> UC5
    V --> UC6
    V --> UC7

    %% ===== LIENS CLIENT (hérite de Visiteur) =====
    C -.->|hérite| V
    C --> UC8
    C --> UC9
    C --> UC10
    C --> UC11
    C --> UC12

    %% ===== LIENS EMPLOYÉ =====
    E --> UC7
    E --> UC12
    E --> UC13
    E --> UC14
    E --> UC15

    %% ===== LIENS ADMIN (hérite de Employé) =====
    A -.->|hérite| E
    A --> UC16
    A --> UC17
    A --> UC18

    %% ===== STYLES =====
    classDef acteur fill:#722F37,stroke:#4A1F24,stroke-width:3px,color:#fff,font-weight:bold
    classDef usecase fill:#F5E6D3,stroke:#C9A961,stroke-width:2px,color:#2C1810
    classDef admin fill:#8B0000,stroke:#4A1F24,stroke-width:3px,color:#fff,font-weight:bold

    class V,C,E acteur
    class A admin
    class UC1,UC2,UC3,UC4,UC5,UC6,UC7,UC8,UC9,UC10,UC11,UC12,UC13,UC14,UC15,UC16,UC17,UC18 usecase
```

## Notes

- Les flèches pleines (`-->`) représentent les associations directes entre un acteur et un cas d'utilisation.
- Les flèches pointillées (`-.->`) avec le label "hérite" représentent la généralisation entre acteurs : un Client peut faire tout ce qu'un Visiteur peut faire + ses propres cas, idem pour Admin par rapport à Employé.
- Les couleurs reprennent la palette du site : bordeaux pour les acteurs standards, rouge foncé pour l'admin, crème/or pour les cas d'utilisation.