<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * GET /api/articles
     * List all articles.
     */
    public function index(): JsonResponse
    {
        return response()->json(Article::all(['id', 'title', 'content', 'created_at']));
    }

    /**
     * POST /api/articles
     * Adds a new article. Requires authentication.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'title.required' => 'Le titre est requis.',
            'content.required' => 'Le contenu est requis.',
        ]);

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
     * Updates an existing article. Requires admin or moderator role.
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
     * Deletes an article. Requires admin or moderator role.
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
