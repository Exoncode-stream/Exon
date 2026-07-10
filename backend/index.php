<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

header("Content-Type: application/json; charset=UTF-8");

try {
    $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $linksStmt = $db->query("SELECT name, url FROM links");
    $links = $linksStmt->fetchAll(PDO::FETCH_ASSOC);

    $videosStmt = $db->query("SELECT id, title, youtube_id, category FROM videos");
    $videos = $videosStmt->fetchAll(PDO::FETCH_ASSOC);

    $articlesStmt = $db->query("SELECT id, title, content FROM articles");
    $articles = $articlesStmt->fetchAll(PDO::FETCH_ASSOC);

    $linksHtml = "";
    foreach ($links as $link) {
        $linksHtml .= '<a href="' . htmlspecialchars($link['url']) . '" class="btn-link" target="_blank">' . htmlspecialchars($link['name']) . '</a>';
    }

    $videosHtml = "";
    foreach ($videos as $video) {
        $videoId = $video['youtube_id'];
        if (strpos($videoId, "youtube.com") !== false || strpos($videoId, "youtu.be") !== false) {
            preg_match('/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/', $videoId, $matches);
            if (isset($matches[2]) && strlen($matches[2]) === 11) {
                $videoId = $matches[2];
            }
        }

        $videosHtml .= '<article class="video-card" data-id="' . $video['id'] . '">';
        $videosHtml .= '<iframe src="https://www.youtube.com/embed/' . htmlspecialchars($videoId) . '" title="' . htmlspecialchars($video['title']) . '" width="100%" height="315" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';

        if (!empty($video['title'])) {
            $videosHtml .= '<h3>' . htmlspecialchars($video['title']) . '</h3>';
        }
        if (!empty($video['category'])) {
            $videosHtml .= '<span class="video-category">' . htmlspecialchars($video['category']) . '</span>';
        }
        $videosHtml .= '</article>';
    }

    $data = [
        "pseudo" => "Exon",
        "description" => "Full-Stack student developer, learning code and sharing thsee on my socials",
        "linksHtml" => $linksHtml,
        "videosHtml" => $videosHtml,
        "articles" => $articles,
    ];

    echo json_encode($data);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Database connection/query failed: " . $e->getMessage()
    ]);
}

?>