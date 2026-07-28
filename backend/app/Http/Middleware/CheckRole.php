<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware vérifiant les autorisations basées sur les rôles utilisateur (RBAC).
 * Utilisation dans les routes : ->middleware('role:admin,moderator')
 */
class CheckRole
{
    /**
     * Traite la requête et vérifie si le rôle de l'utilisateur fait partie des rôles autorisés.
     * 
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles Liste des rôles autorisés (ex: 'admin', 'moderator')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Récupération de l'utilisateur authentifié préalablement par TokenAuth
        $user = $request->get('auth_user');

        // Si l'utilisateur n'est pas présent ou n'a pas un des rôles requis -> 403 Interdit
        if (!$user || !in_array($user->role, $roles)) {
            return response()->json(['error' => 'Accès interdit - Permissions insuffisantes'], 403);
        }

        return $next($request);
    }
}
