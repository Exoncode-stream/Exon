# Exon — Hub Communautaire Full-Stack ("Terminal Noir")

**Exon** est une plateforme web de type hub communautaire centralisant du contenu multimédia (vidéos YouTube, articles en Markdown et liens sociaux/externes). Le projet se distingue par une charte graphique rétro-tech baptisée **« Terminal Noir »**, inspirée des interfaces en ligne de commande.

L'application repose sur une **architecture découplée** associant un Single Page Application (SPA) réactive en **React 19 (Vite)** et une API RESTful haute performance construite sous **Laravel 12 (SQLite)**.

---

## 🚀 Fonctionnalités Principales

* **Hub Multimédia Réactif** : Navigation fluide avec liens sous forme de puces (*pills*), grille de cartes vidéo YouTube et aperçus d'articles.
* **Barre d'Outils de Recherche et Filtrage** : Recherche textuelle *"grep"* en temps réel sur les titres/contenus et filtrage dynamique par catégories de vidéos.
* **Lecteur d'Articles Markdown** : Fenêtre modale native (`<dialog>`) intégrant le rendu Markdown (`react-markdown`) avec gestion des commentaires et des likes.
* **Système d'Interactions Polymorphique** : Modules de commentaires et de likes/upvotes rattachés indifféremment aux articles ou aux vidéos.
* **Authentification & Gestion de Profil** : Connexion/Inscription sécurisée, session par cookies `HttpOnly` et jetons Bearer, espace profil avec statistiques d'activité (nombre de commentaires et likes) et modification sécurisée du mot de passe.
* **Tableau de Bord d'Administration (CRUD & RBAC)** : Interface complète réservée au staff pour créer, éditer ou supprimer des liens, vidéos et articles, ainsi que pour modifier dynamiquement les rôles utilisateurs avec sécurité anti-verrouillage (*anti-lockout*).

---

## 🛠️ Stack Technique

### Frontend
* **React 19** & **Vite 6**
* **React Router DOM v7** (Routage SPA et `ProtectedRoute`)
* **React Markdown v10** (Rendu du contenu d'articles)
* **Vanilla CSS** (Design System personnalisé "Terminal Noir", animations CSS, CSS Grid/Flexbox)
* **Vitest** & **React Testing Library** (Tests unitaires et d'intégration frontend)

### Backend
* **Laravel 12** (PHP 8.2+)
* **Base de données SQLite 3**
* **Middlewares sur mesure** : `SecurityHeaders`, `TokenAuth`, `CheckRole` (RBAC)
* **PHPUnit 11** (Tests de fonctionnalités et d'API)

### Infrastructure & DevOps
* **Docker** & **Docker Compose**
* **Serveurs d'application** : Nginx (Frontend) & Apache (Backend)

---

## 📁 Structure du Projet

```text
exon/
├── backend/                  # API RESTful Laravel 12
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/  # Contrôleurs (Auth, Article, Video, Link, Comment, Like, Profile, User, Hub)
│   │   │   └── Middleware/   # Middlewares (SecurityHeaders, TokenAuth, CheckRole)
│   │   ├── Models/           # Modèles Eloquent (User, Article, Video, Link, Comment, Like)
│   │   └── Providers/        # Service Providers (Auto-migrations AppServiceProvider)
│   ├── config/               # Fichiers de configuration (CORS, cache, app, database)
│   ├── database/             # Migrations et DatabaseSeeder
│   ├── routes/
│   │   └── api.php           # Routes API REST de l'application
│   └── tests/                # Suite de tests Feature PHPUnit (9 classes de test)
│
├── frontend/                 # Client Single Page Application React 19
│   ├── src/
│   │   ├── components/       # Composants réutilisables (Navbar, VideoCard, ArticleCard, ArticleModal, Terminal, etc.)
│   │   ├── context/          # Context API (AuthContext pour la gestion de session)
│   │   ├── pages/            # Pages de l'application (Home, Login, Register, Profile, Admin, NotFound)
│   │   ├── services/         # Client API centralisé (customFetch avec credentials HttpOnly)
│   │   ├── styles/           # Design System Vanilla CSS (index.css)
│   │   └── tests/            # Suite de tests Vitest (AuthContext, Navigation, VideoCard, ArticleModal)
│   └── vite.config.js        # Configuration du bundler Vite et de Vitest
│
├── docs/                     # Guides de documentation (installation.md)
└── docker-compose.yml        # Orchestration des conteneurs Docker (Frontend, Backend, Test runner)
```

---

## ⚡ Installation et Lancement Rapide (Docker)

### Prérequis
* [Git](https://git-scm.com/)
* [Docker](https://www.docker.com/) & [Docker Compose](https://docs.docker.com/compose/)

### 1. Cloner le dépôt
```bash
git clone https://github.com/Exoncode-stream/Exon.git
cd Exon
```

### 2. Démarrer les conteneurs
```bash
docker compose up --build
```
*Au premier démarrage, les dépendances Composer sont installées, la base SQLite est créée et les migrations/seeders s'exécutent automatiquement.*

### 3. Accéder à l'application
* **Frontend SPA** : [http://localhost:8081](http://localhost:8081)
* **API Backend** : [http://localhost:8000/api/hub](http://localhost:8000/api/hub)

### 🔑 Compte Administrateur par défaut
* **Nom d'utilisateur** : `admin`
* **Mot de passe** : `admin`

---

## 🔒 Authentification & Sécurité (RBAC)

L'application définit 4 niveaux d'habilitation (**Role-Based Access Control**) :
1. **`viewer` (Visiteur anonyme ou compte basique)** : Consultation du hub, recherche, filtrage et lecture des articles.
2. **`sub` (Abonné connecté)** : Publication de commentaires, ajout de likes et gestion de son profil.
3. **`moderator` (Modérateur)** : Gestion CRUD complète des articles, vidéos et liens. Modération de tous les commentaires.
4. **`admin` (Administrateur)** : Accès d'administration total + gestion dynamique des rôles des utilisateurs enregistrés (avec protection anti-verrouillage du dernier administrateur).

### Dispositifs de Sécurité Implémentés
* **Cookies HttpOnly & Jetons SHA-256** : Les jetons Bearer ont une expiration de 7 jours. Seule leur empreinte SHA-256 est enregistrée en base de données. Ils sont transmis via des cookies sécurisés `HttpOnly` (`exon_token`) prémunissant l'application des attaques XSS.
* **En-têtes de Sécurité HTTP** : Injection automatique des en-têtes `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection` et `Referrer-Policy`.
* **Rate Limiting** : Protection des routes sensibles (`/api/login`, `/api/register`, `/api/profile/password`) contre le brute-force.

---

## 📡 Endpoints de l'API REST

| Méthode | Route | Description | Rôle / Accès Requis |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/login` | Authentification utilisateur & génération du cookie `exon_token` | Public |
| `POST` | `/api/register` | Inscription d'un nouveau compte (`role: viewer`) | Public |
| `GET` | `/api/hub` | Récupération agrégée des liens, vidéos et articles pour la Home | Public |
| `GET` | `/api/links` | Liste des liens sociaux/externes | Public |
| `GET` | `/api/videos` | Liste des vidéos enregistrées | Public |
| `GET` | `/api/articles` | Liste des articles publiés | Public |
| `GET` | `/api/{type}/{id}/comments` | Liste des commentaires (`type`: `articles` ou `videos`) | Public |
| `GET` | `/api/verify-token` | Vérification de la session et retour des infos utilisateur | Authentifié (`Bearer` / Cookie) |
| `POST` | `/api/logout` | Revocation du jeton et suppression du cookie de session | Authentifié |
| `GET` | `/api/profile` | Informations du profil et statistiques d'activité | Authentifié |
| `PUT` | `/api/profile/password` | Modification sécurisée du mot de passe | Authentifié |
| `POST` | `/api/{type}/{id}/comments` | Ajouter un commentaire sous un article ou une vidéo | Authentifié |
| `DELETE`| `/api/comments/{id}` | Supprimer un commentaire (Auteur ou Staff) | Authentifié |
| `POST` | `/api/{type}/{id}/like` | Alterne le like/upvote sur un contenu (Toggle) | Authentifié |
| `POST` | `/api/links` | Créer un lien externe | `admin` ou `moderator` |
| `PUT` | `/api/links/{id}` | Modifier un lien externe | `admin` ou `moderator` |
| `DELETE`| `/api/links/{id}` | Supprimer un lien externe | `admin` ou `moderator` |
| `POST` | `/api/videos` | Ajouter une vidéo | `admin` ou `moderator` |
| `PUT` | `/api/videos/{id}` | Modifier une vidéo | `admin` ou `moderator` |
| `DELETE`| `/api/videos/{id}` | Supprimer une vidéo | `admin` ou `moderator` |
| `POST` | `/api/articles` | Créer un article | `admin` ou `moderator` |
| `PUT` | `/api/articles/{id}` | Modifier un article | `admin` ou `moderator` |
| `DELETE`| `/api/articles/{id}` | Supprimer un article | `admin` ou `moderator` |
| `GET` | `/api/users` | Récupérer la liste des utilisateurs enregistrés | `admin` uniquement |
| `PUT` | `/api/users/{id}/role` | Modifier le rôle d'un utilisateur | `admin` uniquement |

---

## 🧪 Lancer les Tests Automatisés

### Tests Backend (PHPUnit)
Exécuter la suite de tests Feature côté backend (48 tests) :
```bash
# Hors conteneur
cd backend && ./vendor/bin/phpunit

# Via Docker Compose (Profil dédié aux tests)
docker compose --profile test up backend-test
```

### Tests Frontend (Vitest)
Exécuter la suite de tests composants et contextes côté frontend (8 tests) :
```bash
cd frontend && npm test
```

---

## 📄 Licence

Ce projet est sous licence **MIT**. Libre d'utilisation et de modification.