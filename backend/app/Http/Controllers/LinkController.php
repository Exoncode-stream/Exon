<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller gérant les liens externes affichés dans la barre de navigation du Hub.
 */
class LinkController extends Controller
{
    /**
     * GET /api/links
     * Récupère l'ensemble des liens externes enregistrés.
     */
    public function index(): JsonResponse
    {
        return response()->json(Link::all(['id', 'name', 'url']));
    }

    /**
     * POST /api/links
     * Ajoute un nouveau lien externe.
     * Requis : Rôle administrateur ou modérateur.
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
            'name' => trim($data['name']),
            'url' => trim($data['url']),
        ]);

        return response()->json([
            'message' => 'Lien ajouté avec succès !',
            'link' => $link,
        ], 201);
    }

    /**
     * PUT /api/links/{id}
     * Met à jour un lien existant.
     * Requis : Rôle administrateur ou modérateur.
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
            'name' => trim($data['name']),
            'url' => trim($data['url']),
        ]);

        return response()->json([
            'message' => 'Lien mis à jour avec succès !',
            'link' => $link,
        ]);
    }

    /**
     * DELETE /api/links/{id}
     * Supprime un lien externe.
     * Requis : Rôle administrateur ou modérateur.
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
