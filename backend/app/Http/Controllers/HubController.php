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
     * - Liste des vidéos (avec compteurs de likes et commentaires)
     * - Liste des articles (avec compteurs de likes et commentaires)
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'pseudo' => 'Exon',
            'description' => 'Full-Stack student developer, learning code and sharing these on my socials',
            'links' => Link::all(['id', 'name as label', 'url']),
            'videos' => Video::withCount(['likes', 'comments'])->get(['id', 'title', 'youtube_id', 'category']),
            'articles' => Article::withCount(['likes', 'comments'])->get(['id', 'title', 'content']),
        ]);
    }
}
