<?php
// api/get_message_threads_summary.php
// Purpose: Admin βλέπει τη λίστα recipients ενός μηνύματος + πόσες απαντήσεις
//          υπάρχουν ανά recipient (για να ξέρει ποια threads έχουν δραστηριότητα).

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$message_id = (int)($_GET['message_id'] ?? 0);
if (!$message_id) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Λείπει message_id.']));
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            amr.user_id,
            COALESCE(u.first_name, u.username) AS name,

            COUNT(mr.id)                                          AS reply_count,
            MAX(mr.created_at)                                    AS last_reply_at,
            SUM(CASE WHEN mr.is_from_admin = 0 THEN 1 ELSE 0 END) AS user_reply_count

        FROM admin_message_recipients amr
        JOIN users u ON amr.user_id = u.id
        LEFT JOIN message_replies mr 
               ON mr.message_id = amr.message_id AND mr.recipient_id = amr.user_id
        WHERE amr.message_id = ?
        GROUP BY amr.user_id, u.first_name, u.username
        ORDER BY
            CASE WHEN MAX(mr.created_at) IS NULL THEN 1 ELSE 0 END ASC,
            MAX(mr.created_at) DESC,
            name ASC
    ");
    $stmt->execute([$message_id]);
    $recipients = $stmt->fetchAll();

    echo json_encode([
        'status'     => 'success',
        'recipients' => $recipients,
    ]);
} catch (PDOException $e) {
    error_log('get_message_threads_summary error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
