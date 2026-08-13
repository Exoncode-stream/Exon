<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller gérant l'authentification des utilisateurs (Connexion, Inscription, Déconnexion et Vérification du token).
 */
class AuthController extends Controller
{
    /**
     * POST /api/login
     * Authentifie un utilisateur avec ses identifiants et retourne un jeton d'accès Bearer.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Identifiant requis.',
            'password.required' => 'Mot de passe requis.',
        ]);

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Identifiants invalides'], 401);
        }

        $plainToken = $user->generateToken();

        $cookie = cookie(
            User::TOKEN_COOKIE_NAME,
            $plainToken,
            60 * 24 * 7,
            '/',
            null,
            $request->isSecure(),
            true, // httpOnly
            false,
            'lax'
        );

        return response()->json([
            'success' => true,
            'token' => $plainToken,
            'message' => 'Connexion réussie !',
            'username' => $user->username,
            'role' => $user->role,
        ])->withCookie($cookie);
    }

    /**
     * POST /api/register
     * Crée un nouveau compte utilisateur avec le rôle par défaut 'viewer'.
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

        if (User::where('username', $data['username'])->exists()) {
            return response()->json(['error' => 'Ce nom d\'utilisateur existe déjà.'], 409);
        }

        User::create([
            'username' => trim($data['username']),
            'password' => $data['password'],
            'role' => 'viewer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.',
        ], 201);
    }

    /**
     * POST /api/logout
     * Déconnecte l'utilisateur en effaçant son token de session côté serveur et en supprimant le cookie HttpOnly.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->get('auth_user');
        if ($user) {
            $user->invalidateToken();
        }

        $forgetCookie = cookie()->forget(User::TOKEN_COOKIE_NAME);

        return response()->json(['message' => 'Déconnexion réussie.'])->withCookie($forgetCookie);
    }

    /**
     * GET /api/verify-token
     * Vérifie la validité du token Bearer transmis et retourne les infos de profil.
     */
    public function verifyToken(Request $request): JsonResponse
    {
        $user = $request->get('auth_user');

        return response()->json([
            'message' => 'Token valide',
            'valid' => true,
            'username' => $user->username,
            'role' => $user->role,
        ]);
    }
}
