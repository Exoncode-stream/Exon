<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller gérant les vidéos YouTube présentées sur la plateforme.
 */
class VideoController extends Controller
{
    /**
     * GET /api/videos
     * Retourne la liste de toutes les vidéos enregistrées.
     */
    public function index(): JsonResponse
    {
        return response()->json(Video::all(['id', 'title', 'youtube_id', 'category', 'created_at']));
    }

    /**
     * POST /api/videos
     * Ajoute une nouvelle vidéo. Accessible à tout utilisateur authentifié.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'youtube_id' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ], [
            'title.required' => 'Le titre est requis.',
            'youtube_id.required' => 'L\'identifiant ou l\'URL YouTube est requis.',
            'category.required' => 'La catégorie est requise.',
        ]);

        $video = Video::create([
            'title' => trim(htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8')),
            'youtube_id' => trim(htmlspecialchars($data['youtube_id'], ENT_QUOTES, 'UTF-8')),
            'category' => trim(htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8')),
        ]);

        return response()->json([
            'message' => 'Vidéo ajoutée avec succès !',
            'video' => $video,
        ], 201);
    }

    /**
     * PUT /api/videos/{id}
     * Met à jour les informations d'une vidéo (titre, id youtube, catégorie).
     * Requis : Rôle administrateur ou modérateur.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['error' => 'Vidéo non trouvée'], 404);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'youtube_id' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ], [
            'title.required' => 'Le titre est requis.',
            'youtube_id.required' => 'L\'identifiant YouTube est requis.',
            'category.required' => 'La catégorie est requise.',
        ]);

        $video->update([
            'title' => trim(htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8')),
            'youtube_id' => trim(htmlspecialchars($data['youtube_id'], ENT_QUOTES, 'UTF-8')),
            'category' => trim(htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8')),
        ]);

        return response()->json([
            'message' => 'Vidéo mise à jour avec succès !',
            'video' => $video,
        ]);
    }

    /**
     * DELETE /api/videos/{id}
     * Supprime une vidéo. Requis : Rôle administrateur ou modérateur.
     */
    public function destroy(int $id): JsonResponse
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['error' => 'Vidéo non trouvée'], 404);
        }

        $video->delete();

        return response()->json(['message' => 'Vidéo supprimée avec succès']);
    }
}
