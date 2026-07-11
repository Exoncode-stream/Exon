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

        // Generate a new token and persist it
        $token = Str::random(64);
        $user->update(['token' => $token]);

        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'Login successful',
            'username' => $user->username,
            'role' => $user->role,
        ]);
    }

    /**
     * POST /api/register
     * Creates a new user account with 'viewer' role.
     * Replaces: register.php
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string|min:3',
            'password' => 'required|string|min:5',
        ], [
            'username.required' => 'Missing credentials',
            'password.required' => 'Missing credentials',
            'username.min' => 'Username must be at least 3 characters and password at least 5',
            'password.min' => 'Username must be at least 3 characters and password at least 5',
        ]);

        // Check for duplicate username
        if (User::where('username', $data['username'])->exists()) {
            return response()->json(['error' => 'Username already exists'], 409);
        }

        User::create([
            'username' => trim($data['username']),
            'password' => $data['password'], // Hashed automatically by the model cast
            'role' => 'viewer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. You can now login.',
        ], 201);
    }

    /**
     * GET /api/verify-token
     * Validates a bearer token and returns the associated user info.
     * Replaces: verify-token.php
     */
    public function verifyToken(Request $request): JsonResponse
    {
        $user = $request->get('auth_user');

        return response()->json([
            'message' => 'Token is valid',
            'valid' => true,
            'username' => $user->username,
            'role' => $user->role,
        ]);
    }
}
