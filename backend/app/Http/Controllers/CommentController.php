<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Services\PolymorphicResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller gérant la liste, l'ajout et la suppression des commentaires.
 */
class CommentController extends Controller
{
    /**
     * GET /api/{type}/{id}/comments
     * Récupère la liste des commentaires pour un article ou une vidéo.
     */
    public function index(string $type, int $id): JsonResponse
    {
        $commentable = PolymorphicResolver::resolve($type, $id);

        if (!$commentable) {
            return response()->json(['error' => 'Contenu non trouvé'], 404);
        }

        $comments = $commentable->comments()
            ->with('user:id,username,role')
            ->get();

        return response()->json(['comments' => $comments]);
    }

    /**
     * POST /api/{type}/{id}/comments
     * Ajoute un commentaire sous un article ou une vidéo. Requis : Authentification.
     */
    public function store(CommentRequest $request, string $type, int $id): JsonResponse
    {
        $commentable = PolymorphicResolver::resolve($type, $id);

        if (!$commentable) {
            return response()->json(['error' => 'Contenu non trouvé'], 404);
        }

        $user = $request->get('auth_user');

        $comment = $commentable->comments()->create([
            'user_id' => $user->id,
            'content' => $request->validated('content'),
        ]);

        $comment->load('user:id,username,role');

        return response()->json([
            'message' => 'Commentaire ajouté avec succès !',
            'comment' => $comment,
        ], 201);
    }

    /**
     * DELETE /api/comments/{comment}
     * Supprime un commentaire.
     * Requis : Être l'auteur du commentaire OU être modérateur / administrateur.
     */
    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->get('auth_user');

        // Vérification des droits : Auteur du commentaire OU admin/moderator
        $isAuthor = $user->id === $comment->user_id;
        $isStaff = $user->isStaff();

        if (!$isAuthor && !$isStaff) {
            return response()->json(['error' => 'Accès interdit - Vous ne pouvez pas supprimer ce commentaire'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Commentaire supprimé avec succès !']);
    }
}
