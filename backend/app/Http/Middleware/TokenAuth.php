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
        $rawToken = $request->bearerToken();

        if (!$rawToken) {
            return response()->json(['error' => 'Unauthorized - Token missing'], 401);
        }

        // Search by SHA-256 hash first, fallback to raw token for legacy tokens
        $hashedToken = hash('sha256', $rawToken);
        $user = User::where('token', $hashedToken)->first() 
             ?? User::where('token', $rawToken)->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized - Invalid token'], 401);
        }

        // Store the authenticated user on the request for downstream use
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
