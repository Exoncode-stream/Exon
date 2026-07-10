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

$authHeader = '';
if (function_exists('apache_request_headers')) {
    $apacheHeaders = apache_request_headers();
    $authHeader = $apacheHeaders['Authorization'] ?? '';
}
if (empty($authHeader) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
}

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized - Token missing"]);
    exit();
}
$token = $matches[1];

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$userId = isset($input['user_id']) ? (int)$input['user_id'] : 0;
$newRole = isset($input['role']) ? trim($input['role']) : '';

$allowedRoles = ['viewer', 'sub', 'moderator', 'admin'];

if (empty($userId) || empty($newRole)) {
    http_response_code(400);
    echo json_encode(["error" => "user_id and role are required"]);
    exit();
}

if (!in_array($newRole, $allowedRoles)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid role. Allowed: " . implode(', ', $allowedRoles)]);
    exit();
}

try {
    $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare("SELECT id, role FROM users WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized - Invalid token"]);
        exit();
    }

    if ($admin['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(["error" => "Forbidden - Admin access required"]);
        exit();
    }

    $stmt = $db->prepare("SELECT id FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
        exit();
    }

    $stmt = $db->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute(['role' => $newRole, 'id' => $userId]);

    http_response_code(200);
    echo json_encode(["message" => "Role updated successfully"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
