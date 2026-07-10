<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Only GET requests are allowed"]);
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

try {
    $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare("SELECT id, role FROM users WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized - Invalid token"]);
        exit();
    }

    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(["error" => "Forbidden - Admin access required"]);
        exit();
    }

    $columns = [];
    $pragma = $db->query("PRAGMA table_info(users)");
    while ($col = $pragma->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $col['name'];
    }

    $hasCreatedAt = in_array('created_at', $columns);

    if ($hasCreatedAt) {
        $stmt = $db->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
    } else {
        $stmt = $db->query("SELECT id, username, role FROM users ORDER BY id ASC");
    }
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode(["users" => $users]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
