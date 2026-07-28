<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    /**
     * GET /api/links
     * Retrieve all links.
     */
    public function index(): JsonResponse
    {
        return response()->json(Link::all(['id', 'name', 'url']));
    }

    /**
     * POST /api/links
     * Create a new link. Requires authentication.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ], [
            'name.required' => 'Le nom du lien est requis.',
            'url.required' => 'L\'URL du lien est requise.',
            'url.url' => 'L\'URL fournie doit être une adresse valide (ex: https://example.com).',
        ]);

        $link = Link::create([
            'name' => trim(htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8')),
            'url' => trim($data['url']),
        ]);

        return response()->json([
            'message' => 'Lien ajouté avec succès !',
            'link' => $link,
        ], 201);
    }

    /**
     * PUT /api/links/{id}
     * Update an existing link. Requires admin or moderator role.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $link = Link::find($id);

        if (!$link) {
            return response()->json(['error' => 'Lien non trouvé'], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ], [
            'name.required' => 'Le nom du lien est requis.',
            'url.required' => 'L\'URL du lien est requise.',
            'url.url' => 'L\'URL fournie doit être une adresse valide.',
        ]);

        $link->update([
            'name' => trim(htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8')),
            'url' => trim($data['url']),
        ]);

        return response()->json([
            'message' => 'Lien mis à jour avec succès !',
            'link' => $link,
        ]);
    }

    /**
     * DELETE /api/links/{id}
     * Delete a link. Requires admin or moderator role.
     */
    public function destroy(int $id): JsonResponse
    {
        $link = Link::find($id);

        if (!$link) {
            return response()->json(['error' => 'Lien non trouvé'], 404);
        }

        $link->delete();

        return response()->json(['message' => 'Lien supprimé avec succès !']);
    }
}
