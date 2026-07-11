<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes — Exon Backend
|--------------------------------------------------------------------------
|
| Route mapping from legacy PHP files:
|   login.php        → POST   /api/login
|   register.php     → POST   /api/register
|   verify-token.php → GET    /api/verify-token
|   index.php        → GET    /api/hub
|   add-video.php    → POST   /api/videos
|   delete-video.php → DELETE /api/videos/{id}
|   add-article.php  → POST   /api/articles
|   list-users.php   → GET    /api/users
|   update-role.php  → PUT    /api/users/{id}/role
|
*/

// --- Public Routes ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/hub', [HubController::class, 'index']);

// --- Authenticated Routes (Token Required) ---
Route::middleware('token.auth')->group(function () {
    Route::get('/verify-token', [AuthController::class, 'verifyToken']);

    // Content Management (any authenticated user can add)
    Route::post('/videos', [VideoController::class, 'store']);
    Route::post('/articles', [ArticleController::class, 'store']);

    // Content Deletion (admin or moderator only)
    Route::delete('/videos/{id}', [VideoController::class, 'destroy'])
        ->middleware('role:admin,moderator');

    // User Management (admin only)
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('role:admin');
    Route::put('/users/{id}/role', [UserController::class, 'updateRole'])
        ->middleware('role:admin');
});
