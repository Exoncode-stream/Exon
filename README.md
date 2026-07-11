# Exon — Full-Stack Community Hub

Exon est une plateforme web communautaire conçue pour centraliser les actualités d'un créateur de contenu (vidéos YouTube, articles, liens utiles) avec une esthétique forte et assumée : le **"Terminal Noir"**.

Le projet utilise **Laravel 12** pour son backend API et un frontend **Vanilla** (HTML/CSS/JS) pour garantir des performances maximales et une compréhension totale du code.

## 🛠️ Stack Technique & Versions

### Frontend
- **HTML5** : Sémantique, propre, sans aucun attribut de style inline.
- **CSS3 (Vanilla)** : Système de design complet basé sur des variables CSS, Flexbox/Grid, et des micro-animations. Pas de Tailwind ou Bootstrap.
- **JavaScript (Vanilla - ES6+)** : Manipulation du DOM, requêtes `fetch`, gestion du `localStorage` (modules séparés par page).

### Backend
- **Laravel 12** (PHP 8.3) : API RESTful structurée avec Controllers, Middleware, Eloquent ORM et Form Validation.
- **SQLite 3** : Base de données légère, gérée via les Migrations Laravel (`database/database.sqlite`).

### Infrastructure & Qualité
- **Docker & Docker Compose** : Environnements de production (Nginx/PHP-Apache) et de tests.
- **PHPUnit 11+** : Tests unitaires (Feature Tests) utilisant `RefreshDatabase` pour des tests isolés en mémoire.
- **Laravel Artisan** : CLI pour les migrations, seeders, et la gestion de l'application.

---

## 📂 Organisation du Projet

```text
.
├── backend/                          # API REST Laravel
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── AuthController.php      # Login, Register, Verify Token
│   │   │   │   ├── HubController.php       # Données publiques du hub
│   │   │   │   ├── VideoController.php     # CRUD Vidéos
│   │   │   │   ├── ArticleController.php   # Création d'articles
│   │   │   │   └── UserController.php      # Gestion des utilisateurs
│   │   │   └── Middleware/
│   │   │       ├── TokenAuth.php           # Authentification par Bearer Token
│   │   │       └── CheckRole.php           # Vérification des rôles (RBAC)
│   │   ├── Models/                         # User, Video, Article, Link
│   │   └── Providers/
│   ├── config/                             # Configuration Laravel
│   ├── database/
│   │   ├── migrations/                     # Schéma de la BDD (4 tables)
│   │   └── seeders/                        # Données initiales
│   ├── routes/
│   │   └── api.php                         # Définition de toutes les routes API
│   ├── tests/
│   │   └── Feature/                        # Tests fonctionnels par domaine
│   │       ├── AuthTest.php
│   │       ├── HubTest.php
│   │       ├── VideoTest.php
│   │       ├── ArticleTest.php
│   │       └── UserManagementTest.php
│   ├── composer.json
│   ├── Dockerfile                          # Image PHP 8.3 Apache + Laravel
│   └── phpunit.xml
│
├── frontend/                               # Interface Utilisateur (UI/UX)
│   ├── scripts/                            # Logique métier JavaScript
│   │   ├── app.js                          # Hub principal
│   │   ├── login.js                        # Authentification
│   │   ├── register.js                     # Inscription
│   │   ├── profile.js                      # Profil utilisateur
│   │   └── admin.js                        # Dashboard admin
│   ├── styles/                             # Feuilles de style (main.css)
│   ├── index.html                          # Hub principal
│   ├── login.html                          # Page de connexion
│   ├── register.html                       # Page d'inscription
│   ├── profile.html                        # Espace personnel
│   ├── admin.html                          # Dashboard Admin
│   └── Dockerfile                          # Image Nginx Alpine
│
└── docker-compose.yml                      # Orchestration des conteneurs
```

---

## 🔗 API Routes

| Méthode | Route | Description | Auth | Rôle |
|---|---|---|---|---|
| `POST` | `/api/login` | Authentification | ❌ | — |
| `POST` | `/api/register` | Inscription | ❌ | — |
| `GET` | `/api/hub` | Données publiques du hub | ❌ | — |
| `GET` | `/api/verify-token` | Validation de session | ✅ | — |
| `POST` | `/api/videos` | Ajout de vidéo | ✅ | — |
| `DELETE` | `/api/videos/{id}` | Suppression de vidéo | ✅ | admin, moderator |
| `POST` | `/api/articles` | Ajout d'article | ✅ | — |
| `GET` | `/api/users` | Liste des utilisateurs | ✅ | admin |
| `PUT` | `/api/users/{id}/role` | Mise à jour du rôle | ✅ | admin |

---

## 🚀 Fonctionnalités Développées

### 1. Hub Principal (`index.html`)
- Affichage dynamique des informations (pseudo, description, liens) simulées avec un effet "Typewriter".
- Remontée des "Dernières Vidéos" intégrées (iFrames YouTube optimisées).
- Remontée des "Articles" consultables dans une modale native (glassmorphism).

### 2. Système d'Authentification (Auth)
- **Inscription (`AuthController@register`)** : Création de compte sécurisée avec hachage automatique des mots de passe via Eloquent Casting.
- **Connexion (`AuthController@login`)** : Génération d'un token d'accès stocké en DB et dans le `localStorage` du navigateur.
- **Sécurité** : Protection systématique des requêtes API via le Middleware `TokenAuth` et le Header `Authorization: Bearer <token>`.

### 3. Gestion des Rôles (RBAC)
Le système gère 4 niveaux de privilèges via le Middleware `CheckRole` :
- `viewer` (Défaut lors de l'inscription)
- `sub` (Abonné standard)
- `moderator` (Modérateur)
- `admin` (Administrateur principal)

### 4. Back-office & Dashboard (`admin.html`)
L'accès et l'affichage sont conditionnés par le rôle vérifié dynamiquement par le backend.
- **Ajout de Contenu** : Formulaires pour ajouter des vidéos et des articles.
- **Suppression de Contenu** : Boutons "Delete" injectés dynamiquement sous les vidéos pour les rôles `admin` et `moderator`.
- **Gestion des Utilisateurs** : Un tableau de bord réservé aux administrateurs permettant de voir les dates de création et de modifier les rôles de n'importe quel compte en 1 clic via des menus déroulants customisés.

---

## 🧪 Tests & Intégration

Les tests sont cruciaux pour maintenir la stabilité des API lors de l'évolution du projet.
La commande `docker compose run --rm backend-test` exécute la suite complète :

```bash
# Lancer les tests
docker compose run --rm backend-test

# Résultat attendu
# PHPUnit 11.x
# ...............................................  30 / 30 (100%)
# OK (30 tests, 45+ assertions)
```

Les tests couvrent :
- **AuthTest** : Login (succès/échec/validation), Register (succès/doublon/validation), Verify Token.
- **HubTest** : Réponse publique, structure JSON.
- **VideoTest** : Ajout, suppression, permissions (admin/moderator/viewer).
- **ArticleTest** : Ajout avec authentification.
- **UserManagementTest** : Liste des utilisateurs, mise à jour des rôles, contrôle d'accès.

Chaque test utilise le trait `RefreshDatabase` avec SQLite en mémoire (`:memory:`) pour une exécution rapide et isolée.

---

## 🎨 Philosophie de Design : "Terminal Noir"
L'interface évite les bibliothèques lourdes. Le CSS (`main.css`) centralise le design :
- **Typographie** : Combinaison de `JetBrains Mono` (Terminal) et `Inter` (Texte courant).
- **Couleurs** : Fonds sombres (Github/VSCode dark), Accentuation Vert Électrique (`#39ff88`), Rouge pour le danger (`#f85149`).
- **Effets** : Micro-animations CSS, transitions douces, glows (ombres portées colorées), verre poli (backdrop-filter) pour la modale.
- Zéro attribut inline, HTML ultra-sémantique.

---

## 🐳 Démarrage Rapide

```bash
# Cloner le projet
git clone https://github.com/Exoncode-stream/Exon.git && cd Exon

# Lancer l'application (migrations + seed automatiques)
docker compose up --build

# Accéder au site
# Frontend : http://localhost:8081
# Backend API : http://localhost:8000/api/hub

# Lancer les tests
docker compose run --rm backend-test
```