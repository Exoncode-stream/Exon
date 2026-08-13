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
    public function handle(Request $request, Closure $next): Response
    {
        $rawToken = $request->bearerToken() ?? $request->cookie(User::TOKEN_COOKIE_NAME);

        // Si aucun token n'est présent dans le header ni dans le cookie
        if (!$rawToken) {
            return response()->json(['error' => 'Non autorisé - Token manquant'], 401);
        }

        // Recherche par empreinte SHA-256 (avec fallback)
        $user = User::findByToken($rawToken);

        // Si aucun utilisateur n'est associé au token fourni
        if (!$user) {
            return response()->json(['error' => 'Non autorisé - Token invalide'], 401);
        }

        // Vérification de l'expiration du token
        if ($user->token_expires_at && $user->token_expires_at->isPast()) {
            $user->invalidateToken();
            return response()->json(['error' => 'Non autorisé - Token expiré'], 401);
        }

        // Injection de l'utilisateur authentifié dans la requête
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
