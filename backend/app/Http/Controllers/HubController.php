<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Link;
use App\Models\Video;
use Illuminate\Http\JsonResponse;

/**
 * Controller fournissant les données publiques regroupées pour la page d'accueil du Hub.
 */
class HubController extends Controller
{
    /**
     * GET /api/hub
     * Retourne l'ensemble des données nécessaires pour la page d'accueil du Hub :
     * - Informations de profil du Hub (pseudo, description)
     * - Liste des liens externes
     * - Liste des vidéos
     * - Liste des articles
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'pseudo' => 'Exon',
            'description' => 'Full-Stack student developer, learning code and sharing these on my socials',
            'links' => Link::all(['id', 'name as label', 'url']),
            'videos' => Video::all(['id', 'title', 'youtube_id', 'category']),
            'articles' => Article::all(['id', 'title', 'content']),
        ]);
    }
}
