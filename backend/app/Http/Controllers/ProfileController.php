<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller gérant les données de profil et la sécurité du compte utilisateur.
 */
class ProfileController extends Controller
{
    /**
     * GET /api/profile
     * Récupère les informations complètes du profil de l'utilisateur authentifié.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->get('auth_user');

        $commentsCount = Comment::where('user_id', $user->id)->count();
        $likesCount = Like::where('user_id', $user->id)->count();

        return response()->json([
            'username' => $user->username,
            'role' => $user->role,
            'created_at' => $user->created_at ? $user->created_at->toIso8601String() : null,
            'stats' => [
                'comments_count' => $commentsCount,
                'likes_count' => $likesCount,
            ],
        ]);
    }

    /**
     * PUT /api/profile/password
     * Met à jour le mot de passe de l'utilisateur authentifié.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->get('auth_user');

        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|different:current_password',
            'new_password_confirmation' => 'required|same:new_password',
        ], [
            'current_password.required' => 'Le mot de passe actuel est requis.',
            'new_password.required' => 'Le nouveau mot de passe est requis.',
            'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'new_password.different' => 'Le nouveau mot de passe doit être différent du mot de passe actuel.',
            'new_password_confirmation.same' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['error' => 'Mot de passe actuel incorrect.'], 422);
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        return response()->json(['message' => 'Mot de passe mis à jour avec succès !']);
    }
}
