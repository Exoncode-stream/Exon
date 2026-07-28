<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * Authenticates a user and returns a bearer token.
     * Replaces: login.php
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Missing credentials',
            'password.required' => 'Missing credentials',
        ]);

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate a new token and persist its SHA-256 hash
        $plainToken = Str::random(64);
        $user->update(['token' => hash('sha256', $plainToken)]);

        return response()->json([
            'success' => true,
            'token' => $plainToken,
            'message' => 'Login successful',
            'username' => $user->username,
            'role' => $user->role,
        ]);
    }

    /**
     * POST /api/register
     * Creates a new user account with 'viewer' role.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_\-]+$/',
            'password' => 'required|string|min:8|max:100',
        ], [
            'username.required' => 'Le nom d\'utilisateur est requis.',
            'username.min' => 'Le nom d\'utilisateur doit contenir au moins 3 caractères.',
            'username.max' => 'Le nom d\'utilisateur ne peut pas dépasser 50 caractères.',
            'username.regex' => 'Le nom d\'utilisateur ne doit contenir que des lettres, chiffres, tirets et underscores.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.max' => 'Le mot de passe ne peut pas dépasser 100 caractères.',
        ]);

        // Check for duplicate username
        if (User::where('username', $data['username'])->exists()) {
            return response()->json(['error' => 'Ce nom d\'utilisateur existe déjà.'], 409);
        }

        User::create([
            'username' => trim($data['username']),
            'password' => $data['password'], // Hashed automatically by the model cast
            'role' => 'viewer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.',
        ], 201);
    }

    /**
     * POST /api/logout
     * Invalidates the current user token.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->get('auth_user');
        if ($user) {
            $user->update(['token' => null]);
        }

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    /**
     * GET /api/verify-token
     * Validates a bearer token and returns the associated user info.
     */
    public function verifyToken(Request $request): JsonResponse
    {
        $user = $request->get('auth_user');

        return response()->json([
            'message' => 'Token valid',
            'valid' => true,
            'username' => $user->username,
            'role' => $user->role,
        ]);
    }
}
