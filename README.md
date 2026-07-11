# Exon — Full-Stack Developer Hub

Exon est une plateforme web conçue pour regrouper des contenus liés au développement (vidéos YouTube, articles, liens utiles) avec une esthétique forte et assumée : le **"Terminal Noir"**.

Le projet est entièrement codé "from scratch", sans framework frontend ni backend lourd, afin de garantir des performances maximales et une compréhension totale du code.

## 🛠️ Stack Technique & Versions

### Frontend
- **HTML5** : Sémantique, propre, sans aucun attribut de style inline.
- **CSS3 (Vanilla)** : Système de design complet basé sur des variables CSS, Flexbox/Grid, et des micro-animations. Pas de Tailwind ou Bootstrap.
- **JavaScript (Vanilla - ES6+)** : Manipulation du DOM, requêtes `fetch`, gestion du `localStorage` (modules séparés par page).

### Backend
- **PHP (8.3)** : API RESTful (JSON).
- **SQLite (3)** : Base de données légère, stockée dans un seul fichier local (`backend/database.sqlite`).

### Infrastructure & Qualité
- **Docker & Docker Compose** : Environnements de production (Nginx/PHP-Apache) et de tests.
- **PHPUnit (11.0+)** : Tests unitaires automatisés pour valider chaque endpoint de l'API.
- **Guzzle (7.8+)** : Client HTTP utilisé pour exécuter les tests PHPUnit.

---

## 📂 Organisation du Projet

Le projet est structuré en deux parties distinctes :

```text
.
├── backend/                  # API REST & Logique Serveur
│   ├── tests/                # Tests unitaires PHPUnit (ApiTest.php)
│   ├── database.sqlite       # Base de données SQLite (Générée)
│   ├── index.php             # Endpoint public principal (Hub Data)
│   ├── init_db.php           # Script d'initialisation (Tables & Seed)
│   ├── login.php             # Authentification
│   ├── register.php          # Inscription
│   ├── verify-token.php      # Validation de session & Récupération du rôle
│   ├── list-users.php        # (Admin) Liste des utilisateurs
│   ├── update-role.php       # (Admin) Changement de rôle
│   ├── add-video.php         # (Admin) Ajout de contenu
│   ├── add-article.php       # (Admin) Ajout d'article
│   ├── delete-video.php      # (Admin/Mod) Suppression de contenu
│   ├── composer.json         # Dépendances (PHPUnit, Guzzle)
│   └── Dockerfile            # Image PHP 8.3 Apache avec SQLite
│
├── frontend/                 # Interface Utilisateur (UI/UX)
│   ├── scripts/              # Logique métier JavaScript (app.js, admin.js, etc.)
│   ├── styles/               # Feuilles de style (main.css)
│   ├── index.html            # Hub principal (Terminal, Vidéos, Articles)
│   ├── login.html            # Page de connexion
│   ├── register.html         # Page d'inscription
│   ├── profile.html          # Espace personnel (Rôle, Logout)
│   ├── admin.html            # Dashboard Admin (Gestion contenus & utilisateurs)
│   └── Dockerfile            # Image Nginx Alpine
│
└── docker-compose.yml        # Orchestration des conteneurs
```

---

## 🚀 Fonctionnalités Développées

### 1. Hub Principal (`index.html`)
- Affichage dynamique des informations (pseudo, description, liens) simulées avec un effet "Typewriter".
- Remontée des "Dernières Vidéos" intégrées (iFrames YouTube optimisées).
- Remontée des "Articles" consultables dans une modale native (glassmorphism).

### 2. Système d'Authentification (Auth)
- **Inscription (`register.php`)** : Création de compte sécurisée avec hachage des mots de passe (`password_hash`).
- **Connexion (`login.php`)** : Génération d'un token d'accès stocké en DB et dans le `localStorage` du navigateur.
- **Sécurité** : Protection systématique des requêtes API via le Header `Authorization: Bearer <token>`.

### 3. Gestion des Rôles (RBAC)
Le système gère 4 niveaux de privilèges :
- `viewer` (Défaut lors de l'inscription)
- `sub` (Abonné standard)
- `moderator` (Modérateur)
- `admin` (Administrateur principal)

### 4. Back-office & Dashboard (`admin.html`)
L'accès et l'affichage sont conditionnés par le rôle vérifié dynamiquement par le backend (`verify-token.php`).
- **Ajout de Contenu** : Formulaires pour ajouter des vidéos et des articles.
- **Suppression de Contenu** : Boutons "Delete" injectés dynamiquement sous les vidéos pour les rôles `admin` et `moderator` (`delete-video.php`).
- **Gestion des Utilisateurs** : Un tableau de bord réservé aux administrateurs (`list-users.php`) permettant de voir les dates de création et de modifier les rôles de n'importe quel compte en 1 clic via des menus déroulants customisés (`update-role.php`).

---

## 🧪 Tests & Intégration

Les tests sont cruciaux pour maintenir la stabilité des API lors de l'évolution du projet.
La commande `docker compose run --rm backend-test` exécute la suite complète :
- Vérification des réponses HTTP (200, 401, 403, 400, etc.).
- Tests de succès et d'échec sur les requêtes sensibles.
- Validation des permissions.
- **Tear Down** : La base de données est automatiquement nettoyée après l'exécution des tests grâce à un Hook PHPUnit qui supprime les entrées de test.

---

## 🎨 Philosophie de Design : "Terminal Noir"
L'interface évite les bibliothèques lourdes. Le CSS (`main.css`) centralise le design :
- **Typographie** : Combinaison de `JetBrains Mono` (Terminal) et `Inter` (Texte courant).
- **Couleurs** : Fonds sombres (Github/VSCode dark), Accentuation Vert Électrique (`#39ff88`), Rouge pour le danger (`#f85149`).
- **Effets** : Micro-animations CSS, transitions douces, glows (ombres portées colorées), verre poli (backdrop-filter) pour la modale.
- Zéro attribut inline, HTML ultra-sémantique.

---
*Ce projet démontre la capacité de créer un système complet de gestion d'utilisateurs et de rôles, accompagné d'une API sécurisée et testée de bout en bout, le tout avec un outillage logiciel moderne optimisé pour la performance locale avec Docker.*