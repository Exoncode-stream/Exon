<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Check if the authenticated user has one of the required roles.
     * Usage in routes: ->middleware('role:admin,moderator')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->get('auth_user');

        if (!$user || !in_array($user->role, $roles)) {
            return response()->json(['error' => 'Forbidden - Insufficient permissions'], 403);
        }

        return $next($request);
    }
}
