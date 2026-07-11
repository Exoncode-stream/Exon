<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Link;
use App\Models\Video;
use Illuminate\Http\JsonResponse;

class HubController extends Controller
{
    /**
     * GET /api/hub
     * Returns all public hub data (links, videos, articles).
     * Replaces: index.php
     *
     * Note: linksHtml and videosHtml are generated server-side to maintain
     * backward compatibility with the existing frontend JavaScript.
     */
    public function index(): JsonResponse
    {
        $links = Link::all();
        $videos = Video::all();
        $articles = Article::all(['id', 'title', 'content']);

        // Generate links HTML (backward compat with frontend)
        $linksHtml = '';
        foreach ($links as $link) {
            $linksHtml .= '<a href="' . e($link->url) . '" class="btn-link" target="_blank">' . e($link->name) . '</a>';
        }

        // Generate videos HTML (backward compat with frontend)
        $videosHtml = '';
        foreach ($videos as $video) {
            $videoId = $video->youtube_id;

            // Extract YouTube video ID from full URL if needed
            if (str_contains($videoId, 'youtube.com') || str_contains($videoId, 'youtu.be')) {
                if (preg_match('/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/', $videoId, $matches)) {
                    if (isset($matches[2]) && strlen($matches[2]) === 11) {
                        $videoId = $matches[2];
                    }
                }
            }

            $videosHtml .= '<article class="video-card" data-id="' . $video->id . '">';
            $videosHtml .= '<iframe src="https://www.youtube.com/embed/' . e($videoId) . '" title="' . e($video->title) . '" width="100%" height="315" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';

            if (!empty($video->title)) {
                $videosHtml .= '<h3>' . e($video->title) . '</h3>';
            }
            if (!empty($video->category)) {
                $videosHtml .= '<span class="video-category">' . e($video->category) . '</span>';
            }
            $videosHtml .= '</article>';
        }

        return response()->json([
            'pseudo' => 'Exon',
            'description' => 'Full-Stack student developer, learning code and sharing these on my socials',
            'linksHtml' => $linksHtml,
            'videosHtml' => $videosHtml,
            'articles' => $articles,
        ]);
    }
}
