<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware vérifiant la présence et la validité du token Bearer transmis dans l'en-tête Authorization.
 */
class TokenAuth
{
    /**
     * Traitement de la requête entrante.
     * 
     * Étapes :
     * 1. Extraire le token 'Bearer <token>' de l'en-tête HTTP.
     * 2. Calculer le hash SHA-256 du token et rechercher l'utilisateur correspondant en base de données.
     * 3. Injecter l'utilisateur trouvé ($request->merge(['auth_user' => $user])) pour les middlewares et controllers suivants.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rawToken = $request->bearerToken() ?? $request->cookie('exon_token');

        // Si aucun token n'est présent dans le header ni dans le cookie
        if (!$rawToken) {
            return response()->json(['error' => 'Non autorisé - Token manquant'], 401);
        }

        // Recherche par empreinte SHA-256 (avec fallback pour les jetons historiques)
        $hashedToken = hash('sha256', $rawToken);
        $user = User::where('token', $hashedToken)->first() 
             ?? User::where('token', $rawToken)->first();

        // Si aucun utilisateur n'est associé au token fourni
        if (!$user) {
            return response()->json(['error' => 'Non autorisé - Token invalide'], 401);
        }

        // Vérification de l'expiration du token
        if ($user->token_expires_at && $user->token_expires_at->isPast()) {
            $user->update([
                'token' => null,
                'token_expires_at' => null,
            ]);
            return response()->json(['error' => 'Non autorisé - Token expiré'], 401);
        }

        // Injection de l'utilisateur authentifié dans la requête
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
