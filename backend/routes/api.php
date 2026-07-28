<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LinkController;

/*
|--------------------------------------------------------------------------
| API Routes — Exon Backend
|--------------------------------------------------------------------------
|
| Route mapping:
|   login.php        → POST   /api/login
|   register.php     → POST   /api/register
|   verify-token.php → GET    /api/verify-token
|   index.php        → GET    /api/hub
|   links            → GET|POST|PUT|DELETE /api/links
|   videos           → POST|DELETE /api/videos
|   articles         → POST   /api/articles
|   users            → GET|PUT /api/users
|
*/

// --- Public Routes ---
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
Route::get('/hub', [HubController::class, 'index']);
Route::get('/links', [LinkController::class, 'index']);
Route::get('/videos', [VideoController::class, 'index']);
Route::get('/articles', [ArticleController::class, 'index']);

// --- Authenticated Routes (Token Required) ---
Route::middleware('token.auth')->group(function () {
    Route::get('/verify-token', [AuthController::class, 'verifyToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Link Management (admin or moderator)
    Route::post('/links', [LinkController::class, 'store'])
        ->middleware('role:admin,moderator');
    Route::put('/links/{id}', [LinkController::class, 'update'])
        ->middleware('role:admin,moderator');
    Route::delete('/links/{id}', [LinkController::class, 'destroy'])
        ->middleware('role:admin,moderator');

    // Content Creation (any authenticated user can add)
    Route::post('/videos', [VideoController::class, 'store']);
    Route::post('/articles', [ArticleController::class, 'store']);

    // Video Management (admin or moderator)
    Route::put('/videos/{id}', [VideoController::class, 'update'])
        ->middleware('role:admin,moderator');
    Route::delete('/videos/{id}', [VideoController::class, 'destroy'])
        ->middleware('role:admin,moderator');

    // Article Management (admin or moderator)
    Route::put('/articles/{id}', [ArticleController::class, 'update'])
        ->middleware('role:admin,moderator');
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])
        ->middleware('role:admin,moderator');

    // User Management (admin only)
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('role:admin');
    Route::put('/users/{id}/role', [UserController::class, 'updateRole'])
        ->middleware('role:admin');
});
