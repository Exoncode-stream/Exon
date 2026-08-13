<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Services\PolymorphicResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller gérant le système de Like / Upvote polymorphique.
 */
class LikeController extends Controller
{
    /**
     * POST /api/{type}/{id}/like
     * Alterne (toggle) l'état de Like/Upvote pour un article ou une vidéo par l'utilisateur authentifié.
     */
    public function toggle(Request $request, string $type, int $id): JsonResponse
    {
        $likeable = PolymorphicResolver::resolve($type, $id);

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
