<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

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
     */
    public function store(ArticleRequest $request): JsonResponse
    {
        $article = Article::create($request->validated());

        return response()->json([
            'message' => 'Article ajouté avec succès !',
            'article' => $article,
        ], 201);
    }

    /**
     * PUT /api/articles/{article}
     * Mettre à jour un article existant.
     */
    public function update(ArticleRequest $request, Article $article): JsonResponse
    {
        $article->update($request->validated());

        return response()->json([
            'message' => 'Article mis à jour avec succès !',
            'article' => $article,
        ]);
    }

    /**
     * DELETE /api/articles/{article}
     * Supprime un article.
     */
    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json(['message' => 'Article supprimé avec succès !']);
    }
}
