<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Only POST requests are allowed"]);
    exit();
}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized - Token missing"]);
    exit();
}
$token = $matches[1];

$title = $input['title'] ?? '';
$youtube_id = $input['youtube_id'] ?? '';
$category = $input['category'] ?? '';

if (empty($title) || empty($youtube_id) || empty($category)) {
    http_response_code(400);
    echo json_encode(["error" => "All fields are required"]);
    exit();
}

try {
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare("SELECT * FROM users WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized - Invalid token"]);
        exit();
    }

    $stmt = $db->prepare("INSERT INTO videos (title, youtube_id, category) VALUES (:title, :youtube_id, :category)");
    
    $stmt->execute([
        ':title' => $title,
        ':youtube_id' => $youtube_id,
        ':category' => $category
    ]);

    http_response_code(201);
    echo json_encode(["message" => "Video added successfully!"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

?>
