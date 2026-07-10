<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

header("Content-Type: application/json; charset=UTF-8");

try {
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $linksStmt = $db->query("SELECT name, url FROM links");
    $links = $linksStmt->fetchAll(PDO::FETCH_ASSOC);

    $videosStmt = $db->query("SELECT title, youtube_id, category FROM videos");
    $videos = $videosStmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        "pseudo" => "Exon",
        "description" => "Full-Stack student developer, learning code and sharing thsee on my socials",
        "links" => $links,
        "videos" => $videos,
    ];

    echo json_encode($data);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Database connection/query failed: " . $e->getMessage()
    ]);
}

?>