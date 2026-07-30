<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes — Exon Backend
|--------------------------------------------------------------------------
*/

// --- Public Routes ---
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
Route::get('/hub', [HubController::class, 'index']);
Route::get('/links', [LinkController::class, 'index']);
Route::get('/videos', [VideoController::class, 'index']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/{type}/{id}/comments', [CommentController::class, 'index'])->where('type', 'articles|videos');

// --- Authenticated Routes (Token Required) ---
Route::middleware('token.auth')->group(function () {
    Route::get('/verify-token', [AuthController::class, 'verifyToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->middleware('throttle:5,1');

    // Comments & Likes (all authenticated users)
    Route::post('/{type}/{id}/comments', [CommentController::class, 'store'])->where('type', 'articles|videos');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::post('/{type}/{id}/like', [LikeController::class, 'toggle'])->where('type', 'articles|videos');

    // Link Management (admin or moderator)
    Route::post('/links', [LinkController::class, 'store'])
        ->middleware('role:admin,moderator');
    Route::put('/links/{id}', [LinkController::class, 'update'])
        ->middleware('role:admin,moderator');
    Route::delete('/links/{id}', [LinkController::class, 'destroy'])
        ->middleware('role:admin,moderator');

    // Video Management (admin or moderator)
    Route::post('/videos', [VideoController::class, 'store'])
        ->middleware('role:admin,moderator');
    Route::put('/videos/{id}', [VideoController::class, 'update'])
        ->middleware('role:admin,moderator');
    Route::delete('/videos/{id}', [VideoController::class, 'destroy'])
        ->middleware('role:admin,moderator');

    // Article Management (admin or moderator)
    Route::post('/articles', [ArticleController::class, 'store'])
        ->middleware('role:admin,moderator');
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
