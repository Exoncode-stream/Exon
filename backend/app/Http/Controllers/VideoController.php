<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * GET /api/videos
     * List all videos.
     */
    public function index(): JsonResponse
    {
        return response()->json(Video::all(['id', 'title', 'youtube_id', 'category', 'created_at']));
    }

    /**
     * POST /api/videos
     * Adds a new video. Requires authentication.
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
            'message' => 'Video added successfully!',
            'video' => $video,
        ], 201);
    }

    /**
     * PUT /api/videos/{id}
     * Updates an existing video. Requires admin or moderator role.
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
     * Deletes a video. Requires admin or moderator role.
     */
    public function destroy(int $id): JsonResponse
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        $video->delete();

        return response()->json(['message' => 'Video deleted successfully']);
    }
}
