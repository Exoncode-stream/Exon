# Exon — Full-Stack Community Hub

Exon is a community hub web application centralizing multimedia content (YouTube videos, Markdown articles, and external social links).

The application is built on a decoupled architecture combining a reactive Single Page Application (SPA) in React 19 (Vite) and a high-performance RESTful API built with Laravel 12 (SQLite).

---

## Core Features

* **Reactive Multimedia Hub**: Smooth navigation with pill links, YouTube video cards grid, and article previews.
* **Search and Filter Toolbar**: Real-time text search across titles and content, combined with dynamic video category filtering.
* **Markdown Article Reader**: Native modal dialog (`<dialog>`) rendering Markdown content (`react-markdown`) with built-in comments and likes.
* **Polymorphic Interaction System**: Unified comments and likes/upvotes attached seamlessly to either articles or videos.
* **Authentication & Profile Management**: Secure login/register flows, HttpOnly cookie and Bearer token sessions, user profile space with activity statistics (comments and likes count), and secure password update.
* **Administration Dashboard (CRUD & RBAC)**: Complete staff dashboard to create, edit, or delete links, videos, and articles, as well as dynamically manage user roles with anti-lockout protection.

---

## Technical Stack

### Frontend
* **React 19** & **Vite 6**
* **React Router DOM v7** (SPA routing and `ProtectedRoute` guards)
* **React Markdown v10** (Article content rendering)
* **Vanilla CSS** (Modular design system under `src/styles/` with `base/`, `components/`, and `pages/` subdirectories)
* **Vitest** & **React Testing Library** (Frontend unit and integration testing)

### Backend
* **Laravel 12** (PHP 8.2+)
* **SQLite 3 Database**
* **FormRequests & Traits**: Dedicated validation (`app/Http/Requests`) and polymorphic relations (`app/Models/Traits/HasCommentsAndLikes.php`)
* **Custom Middlewares**: `SecurityHeaders`, `TokenAuth`, `CheckRole` (RBAC)
* **PHPUnit 11** (Feature and API testing)

### Infrastructure & DevOps
* **Docker** & **Docker Compose**
* **Application Servers**: Nginx (Frontend) & Apache (Backend)

---

## Project Structure

```text
exon/
├── backend/                  # RESTful API in Laravel 12
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/  # Controllers (Auth, Article, Video, Link, Comment, Like, Profile, User, Hub)
│   │   │   ├── Middleware/   # Middlewares (SecurityHeaders, TokenAuth, CheckRole)
│   │   │   └── Requests/     # FormRequest validation (ArticleRequest, CommentRequest, LinkRequest, VideoRequest)
│   │   ├── Models/           # Eloquent Models (User, Article, Video, Link, Comment, Like)
│   │   └── Services/         # Service resolution (PolymorphicResolver)
│   ├── config/               # Configuration files (CORS, cache, app, database)
│   ├── database/             # Migrations and DatabaseSeeder
│   ├── routes/
│   │   └── api.php           # REST API Routes
│   └── tests/                # PHPUnit Feature test suite (9 test classes)
│
├── frontend/                 # React 19 Single Page Application client
│   ├── src/
│   │   ├── components/       # Reusable components (Navbar, VideoCard, ArticleCard, ArticleModal, AuthForm, AdminCrudSection, etc.)
│   │   ├── context/          # Context API (AuthContext for session management)
│   │   ├── hooks/            # Custom hooks (useLike, useConfirmDelete)
│   │   ├── pages/            # Page views (Home, Login, Register, Profile, Admin, NotFound)
│   │   ├── services/         # Centralized API client (customFetch with HttpOnly credentials)
│   │   ├── styles/           # Modular CSS architecture (base/, components/, pages/, index.css)
│   │   ├── utils/            # Utilities (auth, formatters)
│   │   └── tests/            # Vitest test suite (AuthContext, Navigation, VideoCard, ArticleModal)
│   └── vite.config.js        # Vite bundler and Vitest configuration
│
└── docker-compose.yml        # Docker Compose orchestration (Frontend, Backend, Test runner)
```

---

## Quick Start (Docker)

### Prerequisites
* [Git](https://git-scm.com/)
* [Docker](https://www.docker.com/) & [Docker Compose](https://docs.docker.com/compose/)

### 1. Clone Repository
```bash
git clone https://github.com/Exoncode-stream/Exon.git
cd Exon
```

### 2. Start Containers
```bash
docker compose up --build
```
*On first boot, Composer dependencies are installed, the SQLite database is created, and migrations/seeders execute automatically.*

### 3. Access Application
* **Frontend SPA**: [http://localhost:8081](http://localhost:8081)
* **Backend API**: [http://localhost:8000/api/hub](http://localhost:8000/api/hub)

### Default Administrator Account
* **Username**: `admin`
* **Password**: `admin`

---

## Authentication & Security (RBAC)

The application enforces 4 permission levels (Role-Based Access Control):
1. **`viewer` (Anonymous visitor or basic account)**: Browse hub, search, filter, and read articles.
2. **`sub` (Registered subscriber)**: Post comments, add likes, and manage personal profile.
3. **`moderator` (Moderator)**: Full CRUD management of articles, videos, and links. Moderation of all comments.
4. **`admin` (Administrator)**: Complete administration access + dynamic user role management (with anti-lockout protection for the last admin).

### Implemented Security Controls
* **HttpOnly Cookies & SHA-256 Tokens**: Bearer tokens expire in 7 days. Only their SHA-256 hash is stored in the database. Tokens are transmitted via secure HttpOnly cookies (`exon_token`) protecting against XSS attacks.
* **HTTP Security Headers**: Automatic injection of `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection`, and `Referrer-Policy`.
* **Rate Limiting**: Protection of sensitive routes (`/api/login`, `/api/register`, `/api/profile/password`) against brute-force attacks.

---

## REST API Endpoints

| Method | Route | Description | Required Role / Access |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/login` | User authentication & `exon_token` cookie issuance | Public |
| `POST` | `/api/register` | New account registration (default role: `viewer`) | Public |
| `GET` | `/api/hub` | Aggregated links, videos, and articles for Home | Public |
| `GET` | `/api/links` | List of social and external links | Public |
| `GET` | `/api/videos` | List of published videos | Public |
| `GET` | `/api/articles` | List of published articles | Public |
| `GET` | `/api/{type}/{id}/comments` | List of comments (`type`: `articles` or `videos`) | Public |
| `GET` | `/api/verify-token` | Session verification and user info payload | Authenticated (`Bearer` / Cookie) |
| `POST` | `/api/logout` | Token revocation and cookie deletion | Authenticated |
| `GET` | `/api/profile` | Profile information and activity statistics | Authenticated |
| `PUT` | `/api/profile/password` | Secure password update | Authenticated |
| `POST` | `/api/{type}/{id}/comments` | Add comment under an article or video | Authenticated |
| `DELETE`| `/api/comments/{comment}` | Delete a comment (Author or Staff) | Authenticated |
| `POST` | `/api/{type}/{id}/like` | Toggle like/upvote on content | Authenticated |
| `POST` | `/api/links` | Create an external link | `admin` or `moderator` |
| `PUT` | `/api/links/{link}` | Update an external link | `admin` or `moderator` |
| `DELETE`| `/api/links/{link}` | Delete an external link | `admin` or `moderator` |
| `POST` | `/api/videos` | Add a video | `admin` or `moderator` |
| `PUT` | `/api/videos/{video}` | Update a video | `admin` or `moderator` |
| `DELETE`| `/api/videos/{video}` | Delete a video | `admin` or `moderator` |
| `POST` | `/api/articles` | Create an article | `admin` or `moderator` |
| `PUT` | `/api/articles/{article}` | Update an article | `admin` or `moderator` |
| `DELETE`| `/api/articles/{article}` | Delete an article | `admin` or `moderator` |
| `GET` | `/api/users` | Retrieve list of registered users | `admin` only |
| `PUT` | `/api/users/{id}/role` | Update user role | `admin` only |

---

## Running Automated Tests

### Backend Tests (PHPUnit)
Execute the backend feature test suite (48 tests):
```bash
# Outside container
cd backend && ./vendor/bin/phpunit

# Via Docker Compose (Dedicated test profile)
docker compose --profile test up backend-test
```

### Frontend Tests (Vitest)
Execute the frontend component and context test suite (8 tests):
```bash
cd frontend && npm run test
```
