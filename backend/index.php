<?php

// 1. Cors Authorization
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 2. Content Type
header("Content-Type: application/json; charset=UTF-8");

// 3. Connection to database & Fetching data
try {
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch links
    $linksStmt = $db->query("SELECT name, url FROM links");
    $links = $linksStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch videos
    $videosStmt = $db->query("SELECT title, youtube_id, category FROM videos");
    $videos = $videosStmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        "pseudo" => "Exon",
        "description" => "Full-Stack student developer, learning code and sharing thsee on my socials",
        "links" => $links,
        "videos" => $videos,
    ];

    // 4. The answer
    echo json_encode($data);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Database connection/query failed: " . $e->getMessage()
    ]);
}

?>