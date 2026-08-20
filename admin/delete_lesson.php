<?php
// admin/delete_lesson.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';
require_once PROJECT_ROOT . 'includes/createNotification.php';

header('Content-Type: application/json');
restrict_access('admin');

if (!user_is_admin()) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Δεν έχετε δικαίωμα διαγραφής προπόνησης.']);
    exit;
}

$data      = json_decode(file_get_contents('php://input'), true) ?? [];
$lesson_id = !empty($data['lesson_id']) ? (int)$data['lesson_id'] : 0;

if (!$lesson_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Μη έγκυρο lesson_id.']);
    exit;
}

try {
    $stmtAthletes = $pdo->prepare("SELECT athlete_id FROM lesson_athletes WHERE lesson_id = ?");
    $stmtAthletes->execute([$lesson_id]);
    $athleteIds = $stmtAthletes->fetchAll(PDO::FETCH_COLUMN);

    $pdo->prepare("DELETE FROM lesson_athletes WHERE lesson_id = ?")->execute([$lesson_id]);
    $pdo->prepare("DELETE FROM lessons WHERE id = ?")->execute([$lesson_id]);

    foreach ($athleteIds as $athleteId) {
        syncNegativeBalanceNotifications($pdo, (int)$athleteId);
    }

    echo json_encode(['status' => 'success', 'message' => 'Η προπόνηση διαγράφηκε.']);
} catch (PDOException $e) {
    error_log('delete_lesson error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
