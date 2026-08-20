<?php
// admin/get_newsletter_logs.php
// Purpose: Return sent newsletter campaign history (Admin only).

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';

restrict_access(['admin']);

try {
    $stmt = $pdo->query("
        SELECT id, subject, message, sent_count, failed_count, sent_at 
        FROM newsletter_logs 
        ORDER BY sent_at DESC
    ");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'logs' => $logs]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Σφάλμα ανάκτησης ιστορικού.']);
}
