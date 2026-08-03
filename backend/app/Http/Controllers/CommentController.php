<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller gérant la liste, l'ajout et la suppression des commentaires.
 */
class CommentController extends Controller
{
    /**
     * Helper pour déterminer le modèle cible (Article ou Video).
     */
    private function resolveCommentable(string $type, int $id)
    {
        if ($type === 'articles') {
            return Article::find($id);
        } elseif ($type === 'videos') {
            return Video::find($id);
        }
        return null;
    }

    /**
     * GET /api/{type}/{id}/comments
     * Récupère la liste des commentaires pour un article ou une vidéo.
     */
    public function index(string $type, int $id): JsonResponse
    {
        $commentable = $this->resolveCommentable($type, $id);

        if (!$commentable) {
            return response()->json(['error' => 'Contenu non trouvé'], 404);
        }

        $comments = $commentable->comments()
            ->with('user:id,username,role')
            ->latest()
            ->get();

        return response()->json(['comments' => $comments]);
    }

    /**
     * POST /api/{type}/{id}/comments
     * Ajoute un commentaire sous un article ou une vidéo. Requis : Authentification.
     */
    public function store(Request $request, string $type, int $id): JsonResponse
    {
        $commentable = $this->resolveCommentable($type, $id);

        if (!$commentable) {
            return response()->json(['error' => 'Contenu non trouvé'], 404);
        }

        $user = $request->get('auth_user');

        $data = $request->validate([
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => 'Le commentaire ne peut pas être vide.',
            'content.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ]);

        $comment = $commentable->comments()->create([
            'user_id' => $user->id,
            'content' => trim($data['content']),
        ]);

        $comment->load('user:id,username,role');

        return response()->json([
            'message' => 'Commentaire ajouté avec succès !',
            'comment' => $comment,
        ], 201);
    }

    /**
     * DELETE /api/comments/{id}
     * Supprime un commentaire.
     * Requis : Être l'auteur du commentaire OU être modérateur / administrateur.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['error' => 'Commentaire non trouvé'], 404);
        }

        $user = $request->get('auth_user');

        // Vérification des droits : Auteur du commentaire OU admin/moderator
        $isAuthor = $user->id === $comment->user_id;
        $isStaff = in_array($user->role, ['admin', 'moderator']);

        if (!$isAuthor && !$isStaff) {
            return response()->json(['error' => 'Accès interdit - Vous ne pouvez pas supprimer ce commentaire'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Commentaire supprimé avec succès !']);
    }
}
