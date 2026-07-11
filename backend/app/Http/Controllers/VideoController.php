<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * POST /api/videos
     * Adds a new video. Requires authentication.
     * Replaces: add-video.php
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string',
            'youtube_id' => 'required|string',
            'category' => 'required|string',
        ], [
            'title.required' => 'All fields are required',
            'youtube_id.required' => 'All fields are required',
            'category.required' => 'All fields are required',
        ]);

        Video::create([
            'title' => trim(htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8')),
            'youtube_id' => trim(htmlspecialchars($data['youtube_id'], ENT_QUOTES, 'UTF-8')),
            'category' => trim(htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8')),
        ]);

        return response()->json(['message' => 'Video added successfully!'], 201);
    }

    /**
     * DELETE /api/videos/{id}
     * Deletes a video. Requires admin or moderator role.
     * Replaces: delete-video.php
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
