<?php
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';

restrict_access(['admin']);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userId = (int)($data['user_id'] ?? 0);

if (!$userId) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid user ID.']);
    exit;
}

// Prevent admin from deactivating themselves
if ($userId === (int)$_SESSION['user_id']) {
    http_response_code(403);
    echo json_encode(['message' => 'Cannot change your own account status.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$userId]);

    $stmt2 = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
    $stmt2->execute([$userId]);
    $newStatus = (int)$stmt2->fetchColumn();

    echo json_encode(['success' => true, 'is_active' => $newStatus]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error.']);
}
