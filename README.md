# Exon

Exon is a community hub platform that centralizes content (videos, articles, links) with a specific "Terminal Noir" design aesthetic. 

The project uses Laravel 12 for the backend API and React 19 (via Vite) for the frontend SPA.

## Project Structure

The project is divided into two distinct parts:

- `backend/`: A Laravel 12 REST API handling data, users, and roles.
- `frontend/`: A React SPA using React Router and a custom context-based authentication system.

## Documentation

For instructions on how to install and run the application, please refer to the installation guide:

- [Installation Guide](docs/installation.md)

## Features

- **Hub**: Display of dynamic links, embedded YouTube videos, and native article reading modals.
- **Authentication**: JWT-style authentication using Bearer tokens.
- **RBAC (Role-Based Access Control)**: Support for four user roles (`viewer`, `sub`, `moderator`, `admin`).
- **Admin Dashboard**: Content management (add videos, articles) and user management (change roles dynamically).

## API Endpoints

- `POST /api/login` - Authenticate a user
- `POST /api/register` - Register a new user
- `GET /api/hub` - Retrieve all public hub data
- `GET /api/verify-token` - Validate current session (Auth)
- `POST /api/videos` - Add a video (Auth)
- `DELETE /api/videos/{id}` - Delete a video (Admin/Moderator)
- `POST /api/articles` - Add an article (Auth)
- `GET /api/users` - List all users (Admin)
- `PUT /api/users/{id}/role` - Update user role (Admin)

## Tech Stack

### Frontend
- React 19
- Vite
- React Router DOM
- Vanilla CSS 

### Backend
- Laravel 12
- SQLite 3
- Docker & Docker Compose