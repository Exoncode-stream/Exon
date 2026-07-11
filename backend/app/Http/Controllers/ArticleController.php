<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * POST /api/articles
     * Adds a new article. Requires authentication.
     * Replaces: add-article.php
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ], [
            'title.required' => 'All fields are required',
            'content.required' => 'All fields are required',
        ]);

        Article::create([
            'title' => trim(htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8')),
            'content' => trim(htmlspecialchars($data['content'], ENT_QUOTES, 'UTF-8')),
        ]);

        return response()->json(['message' => 'Article added successfully!'], 201);
    }
}
