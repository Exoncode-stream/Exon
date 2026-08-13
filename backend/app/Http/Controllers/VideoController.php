<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoRequest;
use App\Models\Video;
use Illuminate\Http\JsonResponse;

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
    public function store(VideoRequest $request): JsonResponse
    {
        $video = Video::create($request->validated());

        return response()->json([
            'message' => 'Vidéo ajoutée avec succès !',
            'video' => $video,
        ], 201);
    }

    /**
     * PUT /api/videos/{video}
     * Met à jour les informations d'une vidéo (titre, id youtube, catégorie).
     */
    public function update(VideoRequest $request, Video $video): JsonResponse
    {
        $video->update($request->validated());

        return response()->json([
            'message' => 'Vidéo mise à jour avec succès !',
            'video' => $video,
        ]);
    }

    /**
     * DELETE /api/videos/{video}
     * Supprime une vidéo. Requis : Rôle administrateur ou modérateur.
     */
    public function destroy(Video $video): JsonResponse
    {
        $video->delete();

        return response()->json(['message' => 'Vidéo supprimée avec succès']);
    }
}
