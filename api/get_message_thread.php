<?php
// api/get_message_thread.php
// Purpose: Fetch parent message + conversation replies between admin and a recipient.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$message_id = (int)($_GET['message_id'] ?? 0);
$recipient_id = (int)($_GET['recipient_id'] ?? 0);

if (!$message_id || !$recipient_id) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Missing parameters.']));
}

try {
    // Combine original broadcast message from admin_messages + subsequent message_replies
    $stmt = $pdo->prepare("
        (
            SELECT 
                0 AS id,
                body,
                'admin' AS sender_type,
                sent_at AS created_at
            FROM admin_messages
            WHERE id = ?
        )
        UNION ALL
        (
            SELECT 
                id,
                body,
                CASE WHEN is_from_admin = 1 THEN 'admin' ELSE 'user' END AS sender_type,
                created_at
            FROM message_replies
            WHERE message_id = ? AND recipient_id = ?
        )
        ORDER BY created_at ASC
    ");
    $stmt->execute([$message_id, $message_id, $recipient_id]);
    $thread = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'thread' => $thread
    ]);
} catch (PDOException $e) {
    error_log('get_message_thread error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
