<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * GET /api/users
     * Lists all users. Requires admin role.
     * Replaces: list-users.php
     */
    public function index(): JsonResponse
    {
        $users = User::select('id', 'username', 'role', 'created_at')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(['users' => $users]);
    }

    /**
     * PUT /api/users/{id}/role
     * Updates a user's role. Requires admin role.
     * Replaces: update-role.php
     */
    public function updateRole(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'role' => 'required|string',
        ], [
            'role.required' => 'user_id and role are required',
        ]);

        $allowedRoles = ['viewer', 'sub', 'moderator', 'admin'];
        $newRole = trim($data['role']);

        if (!in_array($newRole, $allowedRoles)) {
            return response()->json([
                'error' => 'Invalid role. Allowed: ' . implode(', ', $allowedRoles),
            ], 400);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['role' => $newRole]);

        return response()->json(['message' => 'Role updated successfully']);
    }
}
