<?php
// admin/update_lesson_statuses.php
// Auto-complete lessons when their scheduled datetime passes

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');
restrict_access(['admin', 'coach']);

if (!user_can_manage_classes()) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Δεν έχετε δικαίωμα διαχείρισης προπονήσεων.']);
    exit;
}

try {
    $now = (new DateTime())->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
        UPDATE lessons
        SET status = 'completed'
        WHERE status = 'scheduled'
          AND lesson_datetime < ?
    ");
    $stmt->execute([$now]);

    $updated = $stmt->rowCount();

    echo json_encode([
        'status'   => 'success',
        'updated'  => $updated,
        'message'  => $updated > 0
            ? "Ενημερώθηκαν $updated μαθήματα ως ολοκληρωμένα."
            : 'Κανένα μάθημα δεν χρειάζεται ενημέρωση.'
    ]);
} catch (PDOException $e) {
    error_log('update_lesson_statuses error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
