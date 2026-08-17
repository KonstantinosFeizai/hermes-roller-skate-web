<?php
// api/get_admin_messages.php
// Purpose: Επιστρέφει τα απεσταλμένα μηνύματα του admin (για το dashboard).

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

try {
    $stmt = $pdo->query("
        SELECT 
            am.id, am.subject, am.body, am.filters,
            am.send_email, am.recipient_count, am.sent_at,
            COALESCE(u.first_name, u.username) AS sent_by_name
        FROM admin_messages am
        JOIN users u ON am.sent_by = u.id
        ORDER BY am.sent_at DESC
        LIMIT 50
    ");
    $messages = $stmt->fetchAll();

    // Decode filters JSON για κάθε μήνυμα
    foreach ($messages as &$msg) {
        $msg['filters'] = json_decode($msg['filters'], true) ?? [];
    }
    unset($msg);

    echo json_encode([
        'status'   => 'success',
        'messages' => $messages,
    ]);

} catch (PDOException $e) {
    error_log('get_admin_messages error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
