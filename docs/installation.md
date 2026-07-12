# Installation Guide

This guide describes how to run Exon on your local environment using Docker.

## Prerequisites

- Git
- Docker
- Docker Compose

## Quick Start

1. Clone the repository
   ```bash
   git clone https://github.com/Exoncode-stream/Exon.git
   cd Exon
   ```

2. Start the application
   ```bash
   docker compose up --build
   ```

The application will be available at:
- Frontend: http://localhost:8081
- Backend API: http://localhost:8000/api/hub

## Default Accounts

The database is automatically seeded upon starting the containers.

**Administrator:**
- Username: `admin`
- Password: `admin`

You can use these credentials to log in and access the administration dashboard.
