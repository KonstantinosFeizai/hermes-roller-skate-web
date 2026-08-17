<?php
// api/mark_message_read.php
// Purpose: Σημειώνει ένα μήνυμα ως διαβασμένο.

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

$data       = json_decode(file_get_contents('php://input'), true);
$message_id = (int)($data['message_id'] ?? 0);

if (!$message_id) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Λείπει message_id.']));
}

try {
    $stmt = $pdo->prepare("
        UPDATE admin_message_recipients
        SET is_read = 1, read_at = NOW()
        WHERE message_id = ? AND user_id = ? AND is_read = 0
    ");
    $stmt->execute([$message_id, $user_id]);

    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    error_log('mark_message_read error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
