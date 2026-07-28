<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller gérant les opérations CRUD sur les articles du hub.
 */
class ArticleController extends Controller
{
    /**
     * GET /api/articles
     * Récupère la liste de tous les articles publiés.
     */
    public function index(): JsonResponse
    {
        return response()->json(Article::all(['id', 'title', 'content', 'created_at']));
    }

    /**
     * POST /api/articles
     * Ajoute un nouvel article en base de données.
     * Accessible à tout utilisateur authentifié.
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des entrées
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'title.required' => 'Le titre est requis.',
            'content.required' => 'Le contenu est requis.',
        ]);

        // Nettoyage et enregistrement de l'article
        $article = Article::create([
            'title' => trim(htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8')),
            'content' => trim(htmlspecialchars($data['content'], ENT_QUOTES, 'UTF-8')),
        ]);

        return response()->json([
            'message' => 'Article ajouté avec succès !',
            'article' => $article,
        ], 201);
    }

    /**
     * PUT /api/articles/{id}
     * Mettre à jour un article existant.
     * Accessible uniquement aux administrateurs et modérateurs.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['error' => 'Article non trouvé'], 404);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'title.required' => 'Le titre est requis.',
            'content.required' => 'Le contenu est requis.',
        ]);

        $article->update([
            'title' => trim(htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8')),
            'content' => trim(htmlspecialchars($data['content'], ENT_QUOTES, 'UTF-8')),
        ]);

        return response()->json([
            'message' => 'Article mis à jour avec succès !',
            'article' => $article,
        ]);
    }

    /**
     * DELETE /api/articles/{id}
     * Supprime un article.
     * Accessible uniquement aux administrateurs et modérateurs.
     */
    public function destroy(int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['error' => 'Article non trouvé'], 404);
        }

        $article->delete();

        return response()->json(['message' => 'Article supprimé avec succès !']);
    }
}
