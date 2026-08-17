<?php
// profile_update_handler.php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'code' => 'UNAUTHORIZED']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$new_username = trim($data['username'] ?? '');
$new_email = trim($data['email'] ?? '');
$user_id = $_SESSION['user_id'];

try {
    // 1. Basic validation
    if (empty($new_username) || empty($new_email)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'code' => 'REQUIRED_FIELDS_MISSING']);
        exit;
    }

    // 2. Validate Email Format
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'code' => 'INVALID_EMAIL_FORMAT']);
        exit;
    }

    // 3. Check if EMAIL is already used by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$new_email, $user_id]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'code' => 'EMAIL_EXISTS']);
        exit;
    }

    // 4. Check if USERNAME is already used by another user
    $stmtUser = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmtUser->execute([$new_username, $user_id]);
    if ($stmtUser->fetch()) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'code' => 'USERNAME_EXISTS']);
        exit;
    }

    // 5. Update user record in DB
    $updateStmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $updateStmt->execute([$new_username, $new_email, $user_id]);

    // 6. Update session variables
    $_SESSION['username'] = $new_username;
    if (isset($_SESSION['email'])) {
        $_SESSION['email'] = $new_email;
    }

    echo json_encode(['status' => 'success', 'code' => 'SUCCESS']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'code' => 'DB_ERROR']);
}
