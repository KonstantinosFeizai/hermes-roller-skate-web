<?php
// admin/save_lesson.php  — CREATE or UPDATE a lesson
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';
require_once PROJECT_ROOT . 'includes/createNotification.php';

header('Content-Type: application/json');
restrict_access('admin');

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$lesson_id         = !empty($data['lesson_id'])   ? (int)$data['lesson_id']  : null;
$title             = trim($data['title']           ?? '');
$lesson_type       = trim($data['lesson_type']     ?? 'rollers');
$location_id       = !empty($data['location_id'])  ? (int)$data['location_id'] : null;
$lesson_datetime   = trim($data['lesson_datetime'] ?? '');
$weather_condition = trim($data['weather_condition'] ?? '');
$temperature       = $data['temperature'] !== '' && $data['temperature'] !== null ? (float)$data['temperature'] : null;
$notes             = trim($data['notes']           ?? '');
$status            = trim($data['status']          ?? 'scheduled');

$allowed_types    = ['rollers', 'iceskate', 'hockey', 'ski', 'fitness'];
$allowed_statuses = ['scheduled', 'completed', 'cancelled'];
if (!in_array($lesson_type, $allowed_types))    $lesson_type = 'rollers';
if (!in_array($status, $allowed_statuses))      $status = 'scheduled';

if (empty($lesson_datetime)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Η ημερομηνία και ώρα είναι υποχρεωτικές.']);
    exit;
}

try {
    if (!$lesson_id) {
        $stmt = $pdo->prepare("
            INSERT INTO lessons (title, lesson_type, location_id, lesson_datetime,
                                 weather_condition, temperature, notes, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $title,
            $lesson_type,
            $location_id,
            $lesson_datetime,
            $weather_condition ?: null,
            $temperature,
            $notes ?: null,
            $status
        ]);
        $newId = $pdo->lastInsertId();
        echo json_encode(['status' => 'success', 'message' => 'Η προπόνηση δημιουργήθηκε!', 'lesson_id' => $newId]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE lessons SET
                title             = ?, lesson_type       = ?, location_id       = ?,
                lesson_datetime   = ?, weather_condition = ?, temperature       = ?,
                notes             = ?, status            = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $title,
            $lesson_type,
            $location_id,
            $lesson_datetime,
            $weather_condition ?: null,
            $temperature,
            $notes ?: null,
            $status,
            $lesson_id
        ]);

        // Ακύρωση → μηδενισμός παρουσιών ώστε να μην αφαιρεθούν μαθήματα
        if ($status === 'cancelled') {
            $stmtAthletes = $pdo->prepare("SELECT athlete_id FROM lesson_athletes WHERE lesson_id = ?");
            $stmtAthletes->execute([$lesson_id]);
            $athleteIds = $stmtAthletes->fetchAll(PDO::FETCH_COLUMN);

            $pdo->prepare("UPDATE lesson_athletes SET attended = 0 WHERE lesson_id = ?")
                ->execute([$lesson_id]);

            foreach ($athleteIds as $athleteId) {
                syncNegativeBalanceNotifications($pdo, (int)$athleteId);
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Η προπόνηση ενημερώθηκε!']);
    }
} catch (PDOException $e) {
    error_log('save_lesson error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
