<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Link;
use App\Models\Video;
use Illuminate\Http\JsonResponse;

class HubController extends Controller
{
    /**
     * Retrieve all public data for the hub.
     * 
     * Returns a collection of links, videos, and articles
     * to be consumed by the frontend application.
     *
     * @return \Illuminate\Http\JsonResponse
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
