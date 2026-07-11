<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAuth
{
    /**
     * Authenticate the request via a Bearer token stored in the users table.
     * Replaces the manual token extraction logic from the legacy PHP files.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized - Token missing'], 401);
        }

        $user = User::where('token', $token)->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized - Invalid token'], 401);
        }

        // Store the authenticated user on the request for downstream use
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
