<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Like;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller gérant le système de Like / Upvote polymorphique.
 */
class LikeController extends Controller
{
    /**
     * Helper pour déterminer le modèle cible (Article ou Video).
     */
    private function resolveLikeable(string $type, int $id)
    {
        if ($type === 'articles') {
            return Article::find($id);
        } elseif ($type === 'videos') {
            return Video::find($id);
        }
        return null;
    }

    /**
     * POST /api/{type}/{id}/like
     * Alterne (toggle) l'état de Like/Upvote pour un article ou une vidéo par l'utilisateur authentifié.
     */
    public function toggle(Request $request, string $type, int $id): JsonResponse
    {
        $likeable = $this->resolveLikeable($type, $id);

        if (!$likeable) {
            return response()->json(['error' => 'Contenu non trouvé'], 404);
        }

        $user = $request->get('auth_user');
        $likeableType = get_class($likeable);

        $existingLike = Like::where([
            'user_id' => $user->id,
            'likeable_type' => $likeableType,
            'likeable_id' => $likeable->id,
        ])->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $liked = false;
        } else {
            // Like
            Like::create([
                'user_id' => $user->id,
                'likeable_type' => $likeableType,
                'likeable_id' => $likeable->id,
            ]);
            $liked = true;
        }

        $likesCount = $likeable->likes()->count();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount,
            'message' => $liked ? 'Contenu aimé !' : 'Like retiré.',
        ]);
    }
}
