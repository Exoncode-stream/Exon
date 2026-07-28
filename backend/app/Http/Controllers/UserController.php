<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller réservé aux administrateurs pour gérer la liste des utilisateurs et leurs rôles.
 */
class UserController extends Controller
{
    /**
     * GET /api/users
     * Retourne la liste de tous les utilisateurs enregistrés avec leurs rôles.
     * Requis : Rôle administrateur.
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
     * Modifie le rôle d'un utilisateur ('viewer', 'sub', 'moderator', 'admin').
     * 
     * Sécurité :
     * - Contrôle de la valeur du rôle transmis.
     * - Empêche le dernier administrateur de se rétrograder lui-même pour éviter tout blocage.
     * Requis : Rôle administrateur.
     */
    public function updateRole(Request $request, int $id): JsonResponse
    {
        // 1. Validation du champ rôle
        $data = $request->validate([
            'role' => 'required|string',
        ], [
            'role.required' => 'Le rôle est requis.',
        ]);

        // 2. Vérification des rôles autorisés dans l'application
        $allowedRoles = ['viewer', 'sub', 'moderator', 'admin'];
        $newRole = trim($data['role']);

        if (!in_array($newRole, $allowedRoles)) {
            return response()->json([
                'error' => 'Rôle invalide. Rôles autorisés : ' . implode(', ', $allowedRoles),
            ], 400);
        }

        // 3. Recherche de l'utilisateur ciblé
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        // 4. Sécurité anti-verrouillage : Empêcher le dernier admin de supprimer son propre rôle d'admin
        $authUser = $request->get('auth_user');
        if ($authUser && $authUser->id === $user->id && $newRole !== 'admin') {
            $otherAdminsCount = User::where('role', 'admin')->where('id', '!=', $user->id)->count();
            if ($otherAdminsCount === 0) {
                return response()->json([
                    'error' => 'Impossible de modifier votre propre rôle car vous êtes le dernier administrateur.',
                ], 400);
            }
        }

        // 5. Mise à jour du rôle
        $user->update(['role' => $newRole]);

        return response()->json(['message' => 'Rôle mis à jour avec succès !']);
    }
}
