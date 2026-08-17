<?php
// api/get_thread.php
// Purpose: Επιστρέφει όλο το reply thread για ένα (message_id, recipient_id).
//          Προσβάσιμο από: τον ίδιο τον recipient, ή τον admin.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$message_id   = (int)($_GET['message_id']   ?? 0);
$recipient_id = (int)($_GET['recipient_id'] ?? 0);

if (!$message_id || !$recipient_id) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Λείπουν παράμετροι.']));
}

$is_admin_user = ($_SESSION['user_role'] ?? '') === 'admin';

if (!$is_admin_user && $logged_in_id !== $recipient_id) {
    http_response_code(403);
    exit(json_encode(['status' => 'error', 'message' => 'Δεν έχετε πρόσβαση σε αυτό το thread.']));
}

try {
    // Fetch the original message
    $stmt_original = $pdo->prepare("
        SELECT 
            am.id, am.subject, am.body, am.sent_at AS created_at,
            COALESCE(u.first_name, u.username) AS sender_name
        FROM admin_messages am
        LEFT JOIN users u ON am.sent_by = u.id
        WHERE am.id = ?
    ");
    $stmt_original->execute([$message_id]);
    $original_message = $stmt_original->fetch();

    // Fetch all replies
    $stmt = $pdo->prepare("
        SELECT 
            mr.id, mr.body, mr.is_from_admin, mr.created_at,
            COALESCE(u.first_name, u.username) AS sender_name
        FROM message_replies mr
        JOIN users u ON mr.sender_id = u.id
        WHERE mr.message_id = ? AND mr.recipient_id = ?
        ORDER BY mr.created_at ASC
    ");
    $stmt->execute([$message_id, $recipient_id]);
    $replies = $stmt->fetchAll();

    echo json_encode([
        'status'          => 'success',
        'original_message' => $original_message,
        'replies'         => $replies,
    ]);
} catch (PDOException $e) {
    error_log('get_thread error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
