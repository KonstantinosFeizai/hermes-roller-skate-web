<?php
// admin/get_lesson_details.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');
restrict_access('admin');

$data      = json_decode(file_get_contents('php://input'), true) ?? [];
$lesson_id = !empty($data['lesson_id']) ? (int)$data['lesson_id'] : 0;

if (!$lesson_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Μη έγκυρο lesson_id.']);
    exit;
}

try {
    $stmtL = $pdo->prepare("
        SELECT l.*, loc.name AS location_name
        FROM lessons l
        LEFT JOIN locations loc ON l.location_id = loc.id
        WHERE l.id = ?
    ");
    $stmtL->execute([$lesson_id]);
    $lesson = $stmtL->fetch(PDO::FETCH_ASSOC);

    if (!$lesson) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Η προπόνηση δεν βρέθηκε.']);
        exit;
    }

    $stmtA = $pdo->prepare("
        SELECT a.id, a.first_name, a.last_name, a.phone,
               loc.name AS location_name,
               la.attended
        FROM lesson_athletes la
        JOIN athletes a ON la.athlete_id = a.id
        LEFT JOIN locations loc ON a.location_id = loc.id
        WHERE la.lesson_id = ?
        ORDER BY a.last_name ASC, a.first_name ASC
    ");
    $stmtA->execute([$lesson_id]);
    $athletes = $stmtA->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status'  => 'success',
        'lesson'  => $lesson,
        'athletes' => $athletes,
    ]);
} catch (PDOException $e) {
    error_log('get_lesson_details error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
