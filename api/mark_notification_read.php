<?php
// api/mark_notification_read.php
// Purpose: Σημειώνει μια ειδοποίηση ως διαβασμένη.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data)) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Invalid JSON.']));
}

$notification_id = isset($data['notification_id']) ? (int)$data['notification_id'] : (isset($data['message_id']) ? (int)$data['message_id'] : 0);

if (!$notification_id) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Λείπει notification_id.']));
}

try {
    $stmt = $pdo->prepare("
        UPDATE notifications
        SET is_read = 1, read_at = NOW()
        WHERE id = ? AND user_id = ? AND is_read = 0
    ");
    $stmt->execute([$notification_id, $user_id]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    error_log('mark_message_read error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
