<?php
// api/get_inbox.php
// Purpose: Επιστρέφει τα εισερχόμενα μηνύματα του logged-in χρήστη (με υποστήριξη σελιδοποίησης).

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// ── GET PAGINATION PARAMETERS ──
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 10; // Load 10 messages per request

try {
    // 1. Get the actual total unread count for the badge (independent of the limit)
    $unreadStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM admin_message_recipients 
        WHERE user_id = ? AND is_read = 0
    ");
    $unreadStmt->execute([$user_id]);
    $unread = (int)$unreadStmt->fetchColumn();

    // 2. Fetch the paginated batch of messages using LIMIT and OFFSET
    $stmt = $pdo->prepare("
        SELECT 
            am.id              AS message_id,
            am.subject,
            am.body,
            am.sent_at,
            amr.is_read,
            amr.read_at,
            COALESCE(u.first_name, u.username) AS sender_name
        FROM admin_message_recipients amr
        JOIN admin_messages am ON amr.message_id = am.id
        JOIN users u ON am.sent_by = u.id
        WHERE amr.user_id = ?
        ORDER BY am.sent_at DESC
        LIMIT $limit OFFSET $offset
    ");
    $stmt->execute([$user_id]);
    $messages = $stmt->fetchAll();

    // 3. Check if there are potentially more messages left to load
    $has_more = count($messages) === $limit;

    echo json_encode([
        'status'   => 'success',
        'messages' => $messages,
        'unread'   => $unread,
        'has_more' => $has_more // Tells JavaScript whether to display the button
    ]);
} catch (PDOException $e) {
    error_log('get_inbox error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
