<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkRequest;
use App\Models\Link;
use Illuminate\Http\JsonResponse;

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
     */
    public function store(LinkRequest $request): JsonResponse
    {
        $link = Link::create($request->validated());

        return response()->json([
            'message' => 'Lien ajouté avec succès !',
            'link' => $link,
        ], 201);
    }

    /**
     * PUT /api/links/{link}
     * Met à jour un lien existant.
     */
    public function update(LinkRequest $request, Link $link): JsonResponse
    {
        $link->update($request->validated());

        return response()->json([
            'message' => 'Lien mis à jour avec succès !',
            'link' => $link,
        ]);
    }

    /**
     * DELETE /api/links/{link}
     * Supprime un lien externe.
     */
    public function destroy(Link $link): JsonResponse
    {
        $link->delete();

        return response()->json(['message' => 'Lien supprimé avec succès !']);
    }
}
