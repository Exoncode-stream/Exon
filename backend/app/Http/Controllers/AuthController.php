<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Controller gérant l'authentification des utilisateurs (Connexion, Inscription, Déconnexion et Vérification du token).
 */
class AuthController extends Controller
{
    /**
     * POST /api/login
     * Authentifie un utilisateur avec ses identifiants et retourne un jeton d'accès Bearer.
     * 
     * Sécurité : Le jeton renvoyé est aléatoire (64 caractères), mais seule son empreinte SHA-256
     * est sauvegardée en base de données pour empêcher le vol de session en cas de fuite de DB.
     */
    public function login(Request $request): JsonResponse
    {
        // 1. Validation des champs requis
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Identifiant requis.',
            'password.required' => 'Mot de passe requis.',
        ]);

        // 2. Recherche de l'utilisateur par son pseudo
        $user = User::where('username', $data['username'])->first();

        // 3. Vérification du mot de passe avec le système de hachage Bcrypt/Argon de Laravel
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Identifiants invalides'], 401);
        }

        // 4. Génération d'un token aléatoire brut et stockage de son empreinte SHA-256 avec date d'expiration (7 jours)
        $plainToken = Str::random(64);
        $user->update([
            'token' => hash('sha256', $plainToken),
            'token_expires_at' => now()->addDays(7),
        ]);

        // 5. Création du cookie HttpOnly sécurisé (7 jours)
        $cookie = cookie(
            'exon_token',
            $plainToken,
            60 * 24 * 7,
            '/',
            null,
            $request->isSecure(),
            true, // httpOnly
            false,
            'lax'
        );

        // 6. Envoi du token et du cookie HttpOnly au client
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
     * 
     * Règles de validation :
     * - Nom d'utilisateur : 3 à 50 caractères (lettres, chiffres, tirets, underscores).
     * - Mot de passe : minimum 8 caractères.
     */
    public function register(Request $request): JsonResponse
    {
        // 1. Validation stricte des données reçues
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

        // 2. Empêcher la création de pseudos en double (doublons)
        if (User::where('username', $data['username'])->exists()) {
            return response()->json(['error' => 'Ce nom d\'utilisateur existe déjà.'], 409);
        }

        // 3. Création du compte (le mot de passe est automatiquement haché via le Cast du modèle User)
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
        // Récupération de l'utilisateur injecté par le middleware TokenAuth
        $user = $request->get('auth_user');
        if ($user) {
            $user->update([
                'token' => null,
                'token_expires_at' => null,
            ]);
        }

        $forgetCookie = cookie()->forget('exon_token');

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
