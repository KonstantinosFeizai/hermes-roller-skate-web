<?php
// admin/toggle_attendance.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');
restrict_access('admin');

$data       = json_decode(file_get_contents('php://input'), true) ?? [];
$lesson_id  = !empty($data['lesson_id'])  ? (int)$data['lesson_id']  : 0;
$athlete_id = !empty($data['athlete_id']) ? (int)$data['athlete_id'] : 0;
$attended   = isset($data['attended'])    ? ($data['attended'] ? 1 : 0) : 0;

if (!$lesson_id || !$athlete_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Μη έγκυρα δεδομένα.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE lesson_athletes SET attended = ?
        WHERE lesson_id = ? AND athlete_id = ?
    ");
    $stmt->execute([$attended, $lesson_id, $athlete_id]);
    echo json_encode(['status' => 'success', 'attended' => $attended]);
} catch (PDOException $e) {
    error_log('toggle_attendance error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
